<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LaboratoryRequestResource;
use App\Models\LaboratoryRequest;
use App\Models\LaboratoryResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $newRequests = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'new')->count();
        $awaitingSamples = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'awaiting_sample')->count();
        $processing = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'processing')->count();
        $resultsPending = LaboratoryRequest::where('hospital_id', $hospital->id)->where('status', 'result_entry')->count();
        $completedToday = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('status', 'submitted')
            ->whereDate('updated_at', today())
            ->count();

        $requests = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->whereIn('status', ['new', 'awaiting_sample'])
            ->with(['patient', 'requester'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'stats' => [
                'new_requests' => $newRequests,
                'awaiting_samples' => $awaitingSamples,
                'processing' => $processing,
                'results_pending' => $resultsPending,
                'completed_today' => $completedToday,
            ],
            'requests' => LaboratoryRequestResource::collection($requests),
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $query = LaboratoryRequest::where('hospital_id', $hospital->id)->with(['patient', 'requester', 'result']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('request_number', 'like', "%{$q}%")
                    ->orWhere('test_name', 'like', "%{$q}%")
                    ->orWhereHas('patient', function ($pq) use ($q) {
                        $pq->where('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                            ->orWhere('patient_id', 'like', "%{$q}%");
                    });
            });
        }

        $requests = $query->latest()->get();

        return response()->json([
            'data' => LaboratoryRequestResource::collection($requests),
        ]);
    }

    public function show(LaboratoryRequest $laboratoryRequest): JsonResponse
    {
        $laboratoryRequest->load(['patient', 'requester', 'result', 'visit']);

        return response()->json([
            'data' => new LaboratoryRequestResource($laboratoryRequest),
        ]);
    }

    public function updateSampleStatus(Request $request, LaboratoryRequest $laboratoryRequest): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:accepted,awaiting_sample,sample_collected,processing,result_entry,submitted'],
            'sample_type' => ['nullable', 'string'],
        ]);

        $updates = [
            'status' => $validated['status'],
        ];

        if ($validated['sample_type'] ?? false) {
            $updates['sample_type'] = $validated['sample_type'];
        }

        if ($validated['status'] === 'sample_collected') {
            $updates['sample_collected_at'] = now();
            $updates['sample_collected_by'] = $request->user()->id;
        }

        $laboratoryRequest->update($updates);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => new LaboratoryRequestResource($laboratoryRequest),
        ]);
    }

    public function enterResult(Request $request, LaboratoryRequest $laboratoryRequest): JsonResponse
    {
        $validated = $request->validate([
            'result_value' => ['required', 'string'],
            'unit' => ['nullable', 'string'],
            'reference_range' => ['nullable', 'string'],
            'interpretation' => ['nullable', 'string'],
            'lab_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,finalized'],
        ]);

        $result = LaboratoryResult::updateOrCreate(
            ['laboratory_request_id' => $laboratoryRequest->id],
            [
                'entered_by' => $request->user()->id,
                'result_value' => $validated['result_value'],
                'unit' => $validated['unit'] ?? null,
                'reference_range' => $validated['reference_range'] ?? null,
                'interpretation' => $validated['interpretation'] ?? null,
                'lab_notes' => $validated['lab_notes'] ?? null,
                'status' => $validated['status'],
                'finalized_at' => $validated['status'] === 'finalized' ? now() : null,
            ]
        );

        if ($validated['status'] === 'finalized') {
            $laboratoryRequest->update(['status' => 'submitted']);
        } else {
            $laboratoryRequest->update(['status' => 'result_entry']);
        }

        return response()->json([
            'message' => 'Result saved successfully',
            'data' => new LaboratoryRequestResource($laboratoryRequest->load('result')),
        ]);
    }

    public function reports(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $reports = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('status', 'submitted')
            ->with(['patient', 'requester', 'result'])
            ->latest('updated_at')
            ->get();

        return response()->json([
            'data' => LaboratoryRequestResource::collection($reports),
        ]);
    }
}
