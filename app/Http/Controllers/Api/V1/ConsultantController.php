<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ConsultationNoteResource;
use App\Http\Resources\V1\LaboratoryRequestResource;
use App\Http\Resources\V1\PatientResource;
use App\Http\Resources\V1\PrescriptionResource;
use App\Http\Resources\V1\VisitResource;
use App\Models\ConsultationNote;
use App\Models\LaboratoryRequest;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function dashboard(Request $request): JsonResponse
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

        $pendingLabCount = LaboratoryRequest::where('hospital_id', $hospital->id)
            ->where('requester_id', $user->id)
            ->whereIn('status', ['new', 'awaiting_sample', 'sample_collected', 'processing'])
            ->count();

        $pendingRxCount = Prescription::where('hospital_id', $hospital->id)
            ->where('prescriber_id', $user->id)
            ->whereIn('status', ['new', 'accepted', 'processing', 'clarification_needed'])
            ->count();

        $patients = (clone $assignedQuery)
            ->with(['patient', 'department'])
            ->latest('arrived_at')
            ->get();

        return response()->json([
            'stats' => [
                'assigned_patients' => $assignedCount,
                'waiting' => $waitingCount,
                'in_consultation' => $inConsultationCount,
                'completed' => $completedCount,
                'pending_lab_results' => $pendingLabCount,
                'pending_prescriptions' => $pendingRxCount,
            ],
            'queue' => VisitResource::collection($patients),
        ]);
    }

    public function workspace(Request $request, Visit $visit): JsonResponse
    {
        $visit->load([
            'patient.visits',
            'clinician',
            'department',
            'consultationNote',
            'labRequests.result',
            'prescriptions.items.medication',
        ]);

        return response()->json([
            'visit' => new VisitResource($visit),
            'patient' => new PatientResource($visit->patient),
            'consultation_note' => $visit->consultationNote ? new ConsultationNoteResource($visit->consultationNote) : null,
            'lab_requests' => LaboratoryRequestResource::collection($visit->labRequests),
            'prescriptions' => PrescriptionResource::collection($visit->prescriptions),
        ]);
    }

    public function saveNotes(Request $request, Visit $visit): JsonResponse
    {
        $validated = $request->validate([
            'chief_complaint' => ['nullable', 'string'],
            'history_of_present_illness' => ['nullable', 'string'],
            'relevant_medical_history' => ['nullable', 'string'],
            'examination_findings' => ['nullable', 'string'],
            'assessment_diagnosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'follow_up_instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,finalized'],
        ]);

        $note = ConsultationNote::updateOrCreate(
            [
                'visit_id' => $visit->id,
            ],
            [
                'patient_id' => $visit->patient_id,
                'clinician_id' => $request->user()->id,
                ...$validated,
                'finalized_at' => $validated['status'] === 'finalized' ? now() : null,
            ]
        );

        if ($validated['status'] === 'finalized') {
            $visit->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } else {
            $visit->update([
                'status' => 'in_consultation',
                'clinician_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'message' => 'Consultation note saved successfully',
            'data' => new ConsultationNoteResource($note),
        ]);
    }

    public function storePrescription(Request $request, Visit $visit): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_name' => ['required', 'string'],
            'items.*.strength' => ['nullable', 'string'],
            'items.*.dosage' => ['required', 'string'],
            'items.*.route' => ['nullable', 'string'],
            'items.*.frequency' => ['required', 'string'],
            'items.*.duration' => ['required', 'string'],
            'items.*.quantity_prescribed' => ['required', 'integer', 'min:1'],
            'items.*.instructions' => ['nullable', 'string'],
            'is_draft' => ['nullable', 'boolean'],
        ]);

        $nextNum = Prescription::where('hospital_id', $hospital->id)->count() + 1;
        $rxNumber = 'RX-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);

        $rx = Prescription::create([
            'hospital_id' => $hospital->id,
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'prescriber_id' => $request->user()->id,
            'rx_number' => $rxNumber,
            'notes' => $validated['notes'] ?? null,
            'status' => ($validated['is_draft'] ?? false) ? 'draft' : 'new',
        ]);

        foreach ($validated['items'] as $item) {
            PrescriptionItem::create([
                'prescription_id' => $rx->id,
                'medication_name' => $item['medication_name'],
                'strength' => $item['strength'] ?? null,
                'dosage' => $item['dosage'],
                'route' => $item['route'] ?? 'Oral',
                'frequency' => $item['frequency'],
                'duration' => $item['duration'],
                'quantity_prescribed' => $item['quantity_prescribed'],
                'instructions' => $item['instructions'] ?? null,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Prescription created successfully',
            'data' => new PrescriptionResource($rx->load(['items', 'patient', 'prescriber'])),
        ], 201);
    }

    public function storeLabRequest(Request $request, Visit $visit): JsonResponse
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

        $labReq = LaboratoryRequest::create([
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

        return response()->json([
            'message' => 'Laboratory test requested',
            'data' => new LaboratoryRequestResource($labReq->load(['patient', 'requester'])),
        ], 201);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'title' => $user->title,
            ],
        ]);
    }
}
