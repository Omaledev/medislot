<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConsultationNote;
use App\Models\LaboratoryRequest;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\RequestMessage;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultantWebController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $assignedQuery = $hospital->visits()
            ->whereDate('arrived_at', today())
            ->where(function ($q) use ($user) {
                $q->where('clinician_id', $user->id)
                    ->orWhereNull('clinician_id');
            });

        $assignedCount = (clone $assignedQuery)->count();
        $waitingCount = (clone $assignedQuery)->where('status', 'waiting')->count();
        $inConsultationCount = (clone $assignedQuery)->where('status', 'in_consultation')->count();
        $completedCount = (clone $assignedQuery)->where('status', 'completed')->count();

        $pendingLabResults = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('requester_id', $user->id)
            ->whereIn('status', ['new', 'awaiting_sample', 'sample_collected', 'processing'])
            ->count();

        $pendingPrescriptions = Prescription::where('hospital_id', $hospital->id)
            ->where('prescriber_id', $user->id)
            ->whereIn('status', ['new', 'accepted', 'processing', 'clarification_needed'])
            ->count();

        $tab = $request->query('tab', 'all');
        $tabQuery = clone $assignedQuery;
        if ($tab === 'waiting') {
            $tabQuery->where('status', 'waiting');
        } elseif ($tab === 'in_consultation') {
            $tabQuery->where('status', 'in_consultation');
        } elseif ($tab === 'completed') {
            $tabQuery->where('status', 'completed');
        }

        $patients = $tabQuery->with(['patient', 'department'])->latest('arrived_at')->get();

        return view('portals.consultant.dashboard', compact(
            'hospital',
            'user',
            'assignedCount',
            'waitingCount',
            'inConsultationCount',
            'completedCount',
            'pendingLabResults',
            'pendingPrescriptions',
            'patients',
            'tab'
        ));
    }

    public function patients(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $visits = $hospital->visits()
            ->where(function ($q) use ($user) {
                $q->where('clinician_id', $user->id)
                    ->orWhereNull('clinician_id');
            })
            ->with(['patient', 'department'])
            ->latest('arrived_at')
            ->get();

        return view('portals.consultant.patients', compact('hospital', 'user', 'visits'));
    }

    public function workspace(Request $request, Visit $visit): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $visit->load([
            'patient.visits.clinician',
            'patient.consultationNotes.clinician',
            'patient.prescriptions.items.medication',
            'patient.labRequests.result',
            'clinician',
            'department',
            'consultationNote',
            'labRequests.result',
            'prescriptions.items.medication',
        ]);

        $activeTab = $request->query('tab', 'overview');

        return view('portals.consultant.workspace', compact(
            'hospital',
            'user',
            'visit',
            'activeTab'
        ));
    }

    public function saveNotes(Request $request, Visit $visit): RedirectResponse
    {
        $validated = $request->validate([
            'chief_complaint' => ['nullable', 'string'],
            'history_of_present_illness' => ['nullable', 'string'],
            'relevant_medical_history' => ['nullable', 'string'],
            'examination_findings' => ['nullable', 'string'],
            'assessment_diagnosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'follow_up_instructions' => ['nullable', 'string'],
            'action_type' => ['required', 'in:draft,save,complete'],
        ]);

        $status = $validated['action_type'] === 'complete' ? 'finalized' : 'draft';

        ConsultationNote::updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'patient_id' => $visit->patient_id,
                'clinician_id' => $request->user()->id,
                'chief_complaint' => $validated['chief_complaint'],
                'history_of_present_illness' => $validated['history_of_present_illness'],
                'relevant_medical_history' => $validated['relevant_medical_history'],
                'examination_findings' => $validated['examination_findings'],
                'assessment_diagnosis' => $validated['assessment_diagnosis'],
                'treatment_plan' => $validated['treatment_plan'],
                'follow_up_instructions' => $validated['follow_up_instructions'],
                'status' => $status,
                'finalized_at' => $status === 'finalized' ? now() : null,
            ]
        );

        if ($status === 'finalized') {
            $visit->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return redirect()->route('consultant.dashboard')->with('success', "Consultation completed for {$visit->patient?->full_name}.");
        } else {
            $visit->update([
                'status' => 'in_consultation',
                'clinician_id' => $request->user()->id,
            ]);

            return redirect()->route('consultant.workspace', ['visit' => $visit, 'tab' => 'notes'])->with('success', 'Consultation notes updated.');
        }
    }

    public function createPrescription(Request $request, Visit $visit): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'medication_name' => ['required', 'string'],
            'strength' => ['nullable', 'string'],
            'dosage' => ['required', 'string'],
            'route' => ['nullable', 'string'],
            'frequency' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'quantity_prescribed' => ['required', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
            'submit_type' => ['required', 'in:draft,send'],
        ]);

        $nextNum = Prescription::where('hospital_id', $hospital->id)->count() + 1;
        $rxNumber = 'RX-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);

        $rx = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'prescriber_id' => $request->user()->id,
            'rx_number' => $rxNumber,
            'status' => $validated['submit_type'] === 'draft' ? 'draft' : 'new',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx->id,
            'medication_name' => $validated['medication_name'],
            'strength' => $validated['strength'] ?? null,
            'dosage' => $validated['dosage'],
            'route' => $validated['route'] ?? 'Oral',
            'frequency' => $validated['frequency'],
            'duration' => $validated['duration'],
            'quantity_prescribed' => $validated['quantity_prescribed'],
            'instructions' => $validated['instructions'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('consultant.workspace', ['visit' => $visit, 'tab' => 'prescriptions'])
            ->with('success', 'Prescription '.($validated['submit_type'] === 'send' ? 'sent to pharmacy' : 'saved as draft'));
    }

    public function createLabRequest(Request $request, Visit $visit): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'category' => ['required', 'string'],
            'test_name' => ['required', 'string'],
            'clinical_reason' => ['nullable', 'string'],
            'priority' => ['required', 'in:Routine,Urgent'],
            'instructions' => ['nullable', 'string'],
        ]);

        $nextNum = LaboratoryRequest::where('hospital_id', $hospital->id)->count() + 1;
        $requestNumber = 'LR-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);

        LaboratoryRequest::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'requester_id' => $request->user()->id,
            'request_number' => $requestNumber,
            'category' => $validated['category'],
            'test_name' => $validated['test_name'],
            'clinical_reason' => $validated['clinical_reason'] ?? null,
            'priority' => $validated['priority'],
            'instructions' => $validated['instructions'] ?? null,
            'status' => 'new',
        ]);

        return redirect()->route('consultant.workspace', ['visit' => $visit, 'tab' => 'laboratory'])
            ->with('success', "Lab request {$requestNumber} submitted to laboratory.");
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $visits = $hospital->visits()
            ->where('clinician_id', $user->id)
            ->where('status', 'completed')
            ->with(['patient', 'consultationNote'])
            ->latest('completed_at')
            ->paginate(15);

        return view('portals.consultant.history', compact('hospital', 'user', 'visits'));
    }

    public function labRequests(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $requests = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('requester_id', $user->id)
            ->with(['patient', 'result'])
            ->latest()
            ->paginate(15);

        return view('portals.consultant.lab-requests', compact('hospital', 'user', 'requests'));
    }

    public function prescriptions(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        $prescriptions = Prescription::where('hospital_id', $hospital->id)
            ->where('prescriber_id', $user->id)
            ->with(['patient', 'items.medication'])
            ->latest()
            ->paginate(15);

        return view('portals.consultant.prescriptions', compact('hospital', 'user', 'prescriptions'));
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

        return view('portals.consultant.messages', compact('hospital', 'user', 'threads', 'activeThread'));
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

        return back()->with('success', 'Reply sent');
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        $hospital = $user->hospital;

        return view('portals.consultant.profile', compact('hospital', 'user'));
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
