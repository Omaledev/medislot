<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryRequest;
use App\Models\LaboratoryResult;
use App\Models\Prescription;
use App\Models\RequestMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabWebController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $newRequestsCount = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'new')->count();
        $awaitingSamplesCount = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'awaiting_sample')->count();
        $processingCount = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'processing')->count();
        $resultsPendingCount = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'result_entry')->count();
        $completedTodayCount = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('status', 'submitted')
            ->whereDate('updated_at', today())
            ->count();

        $requests = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->whereIn('status', ['new', 'awaiting_sample'])
            ->with(['patient', 'requester'])
            ->latest()
            ->take(8)
            ->get();

        return view('portals.lab.dashboard', compact(
            'hospital',
            'user',
            'newRequestsCount',
            'awaitingSamplesCount',
            'processingCount',
            'resultsPendingCount',
            'completedTodayCount',
            'requests'
        ));
    }

    public function requests(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $query = LaboratoryRequest::where('hospital_id', $hospital->id)->with(['patient', 'requester', 'result']);

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('request_number', 'like', "%{$q}%")
                    ->orWhere('test_name', 'like', "%{$q}%")
                    ->orWhereHas('patient', function ($pq) use ($q) {
                        $pq->where('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        $requests = $query->latest()->get();

        return view('portals.lab.requests', compact('hospital', 'user', 'requests'));
    }

    public function showRequest(LaboratoryRequest $laboratoryRequest): View
    {
        $laboratoryRequest->load(['patient', 'requester', 'result', 'visit']);
        $hospital = $laboratoryRequest->hospital;
        $user = auth()->user();

        return view('portals.lab.request-details', compact('hospital', 'user', 'laboratoryRequest'));
    }

    public function enterResultForm(LaboratoryRequest $laboratoryRequest): View
    {
        $laboratoryRequest->load(['patient', 'requester', 'result', 'visit']);
        $hospital = $laboratoryRequest->hospital;
        $user = auth()->user();

        return view('portals.lab.enter-result', compact('hospital', 'user', 'laboratoryRequest'));
    }

    public function showReport(LaboratoryRequest $laboratoryRequest): View
    {
        $laboratoryRequest->load(['patient', 'requester', 'result.enteredBy', 'visit']);
        $hospital = $laboratoryRequest->hospital;
        $user = auth()->user();

        return view('portals.lab.result-report', compact('hospital', 'user', 'laboratoryRequest'));
    }

    public function amendReport(Request $request, LaboratoryRequest $laboratoryRequest): RedirectResponse
    {
        $validated = $request->validate([
            'amendment_reason' => ['required', 'string'],
            'corrected_value' => ['nullable', 'string'],
        ]);

        if ($laboratoryRequest->result) {
            $notes = ($laboratoryRequest->result->lab_notes ? $laboratoryRequest->result->lab_notes."\n" : '')
                .'[Amendment on '.now()->format('Y-m-d H:i').' by '.$request->user()->name.']: '.$validated['amendment_reason'];

            $updates = ['lab_notes' => $notes];
            if (! empty($validated['corrected_value'])) {
                $updates['result_value'] = $validated['corrected_value'];
            }
            $laboratoryRequest->result->update($updates);
        }

        return back()->with('success', 'Amendment successfully recorded on laboratory report.');
    }

    public function saveResult(Request $request, LaboratoryRequest $laboratoryRequest): RedirectResponse
    {
        $validated = $request->validate([
            'result_value' => ['required', 'string'],
            'unit' => ['nullable', 'string'],
            'reference_range' => ['nullable', 'string'],
            'interpretation' => ['nullable', 'string'],
            'lab_notes' => ['nullable', 'string'],
            'action_type' => ['required', 'in:draft,submit'],
        ]);

        $isFinal = $validated['action_type'] === 'submit';

        LaboratoryResult::updateOrCreate(
            ['laboratory_request_id' => $laboratoryRequest->id],
            [
                'entered_by' => $request->user()->id,
                'result_value' => $validated['result_value'],
                'unit' => $validated['unit'] ?? null,
                'reference_range' => $validated['reference_range'] ?? null,
                'interpretation' => $validated['interpretation'] ?? null,
                'lab_notes' => $validated['lab_notes'] ?? null,
                'status' => $isFinal ? 'finalized' : 'draft',
                'finalized_at' => $isFinal ? now() : null,
            ]
        );

        $laboratoryRequest->update([
            'status' => $isFinal ? 'submitted' : 'result_entry',
        ]);

        return redirect()->route('lab.requests')->with('success', $isFinal ? "Result for {$laboratoryRequest->request_number} finalized and sent to doctor." : 'Result draft saved.');
    }

    public function samples(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $samples = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->whereIn('status', ['accepted', 'awaiting_sample', 'sample_collected', 'processing', 'result_entry'])
            ->with(['patient', 'requester'])
            ->latest()
            ->get();

        return view('portals.lab.samples', compact('hospital', 'user', 'samples'));
    }

    public function advanceSampleStatus(Request $request, LaboratoryRequest $laboratoryRequest): RedirectResponse
    {
        $statusFlow = [
            'new' => 'awaiting_sample',
            'accepted' => 'awaiting_sample',
            'awaiting_sample' => 'sample_collected',
            'sample_collected' => 'processing',
            'processing' => 'result_entry',
            'result_entry' => 'submitted',
        ];

        $next = $statusFlow[$laboratoryRequest->status] ?? $laboratoryRequest->status;

        $updates = ['status' => $next];
        if ($next === 'sample_collected') {
            $updates['sample_collected_at'] = now();
            $updates['sample_collected_by'] = $request->user()->id;
            if (! $laboratoryRequest->sample_type) {
                $updates['sample_type'] = 'Blood';
            }
        }

        $laboratoryRequest->update($updates);

        return back()->with('success', "Request {$laboratoryRequest->request_number} advanced to ".str_replace('_', ' ', $next));
    }

    public function reports(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $reports = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('status', 'submitted')
            ->with(['patient', 'requester', 'result'])
            ->latest('updated_at')
            ->get();

        return view('portals.lab.reports', compact('hospital', 'user', 'reports'));
    }

    public function messages(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $threads = RequestMessage::where('hospital_id', $hospital->id)
            ->with(['sender', 'messageable.patient'])
            ->latest()
            ->get();

        $activeThread = $threads->first();

        return view('portals.lab.messages', compact('hospital', 'user', 'threads', 'activeThread'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'messageable_type' => ['required', 'string'],
            'messageable_id' => ['required', 'integer'],
            'message' => ['required', 'string'],
        ]);

        $modelClass = str_contains($validated['messageable_type'], 'Prescription') ? Prescription::class : LaboratoryRequest::class;

        RequestMessage::create([
            'hospital_id' => $hospital->id,
            'sender_id' => $request->user()->id,
            'messageable_type' => $modelClass,
            'messageable_id' => $validated['messageable_id'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Message sent.');
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        return view('portals.lab.profile', compact('hospital', 'user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
