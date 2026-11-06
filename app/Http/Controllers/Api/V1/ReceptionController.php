<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BedCategoryResource;
use App\Http\Resources\V1\PatientResource;
use App\Http\Resources\V1\VisitResource;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $registeredToday = $hospital->patients()->whereDate('created_at', today())->count();
        $waitingVisits = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'waiting')->count();
        $inConsultation = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'in_consultation')->count();
        $completedVisits = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'completed')->count();

        $todayQueue = $hospital->visits()
            ->whereDate('arrived_at', today())
            ->with(['patient', 'department', 'clinician'])
            ->latest('arrived_at')
            ->take(10)
            ->get();

        $beds = $hospital->bedCategories;

        return response()->json([
            'stats' => [
                'registered_today' => $registeredToday,
                'waiting' => $waitingVisits,
                'in_consultation' => $inConsultation,
                'completed_visits' => $completedVisits,
            ],
            'queue' => VisitResource::collection($todayQueue),
            'beds' => BedCategoryResource::collection($beds),
        ]);
    }

    public function patients(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $query = $hospital->patients();

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('patient_id', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $patients = $query->latest()->paginate(15);

        return response()->json([
            'data' => PatientResource::collection($patients),
            'meta' => [
                'total' => $patients->total(),
            ],
        ]);
    }

    public function storePatient(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'age' => ['nullable', 'integer'],
            'gender' => ['required', 'in:Female,Male,Other'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'chronic_conditions' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
        ]);

        // Generate next patient code
        $nextNum = Patient::where('hospital_id', $hospital->id)->count() + 1;
        $patientId = 'MED-'.str_pad((string) $nextNum, 6, '0', STR_PAD_LEFT);

        $patient = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $patientId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'chronic_conditions' => $validated['chronic_conditions'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
            'registered_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Patient registered successfully',
            'data' => new PatientResource($patient),
        ], 201);
    }

    public function showPatient(Request $request, Patient $patient): JsonResponse
    {
        $patient->load(['visits.clinician', 'visits.department', 'consultationNotes', 'labRequests', 'prescriptions']);

        return response()->json([
            'data' => new PatientResource($patient),
            'visits' => VisitResource::collection($patient->visits),
        ]);
    }

    public function updatePatient(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string'],
            'last_name' => ['sometimes', 'required', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'age' => ['nullable', 'integer'],
            'gender' => ['sometimes', 'required', 'in:Female,Male,Other'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'chronic_conditions' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return response()->json([
            'message' => 'Patient updated successfully',
            'data' => new PatientResource($patient),
        ]);
    }

    public function updateVisit(Request $request, Visit $visit): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => ['sometimes', 'exists:departments,id'],
            'clinician_id' => ['nullable', 'exists:users,id'],
            'visit_type' => ['sometimes', 'in:Walk-in,Appointment,Emergency'],
            'reason_for_visit' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'in:Routine,Urgent,Emergency'],
            'status' => ['sometimes', 'in:waiting,in_consultation,completed,cancelled'],
        ]);

        $visit->update($validated);

        return response()->json([
            'message' => 'Visit updated successfully',
            'data' => new VisitResource($visit->load(['patient', 'department', 'clinician'])),
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Reception settings updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function visits(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $query = $hospital->visits()->with(['patient', 'department', 'clinician']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->boolean('today', true)) {
            $query->whereDate('arrived_at', today());
        }

        $visits = $query->latest('arrived_at')->get();

        return response()->json([
            'data' => VisitResource::collection($visits),
            'meta' => [
                'total' => $visits->count(),
                'waiting' => $visits->where('status', 'waiting')->count(),
                'in_consultation' => $visits->where('status', 'in_consultation')->count(),
                'completed' => $visits->where('status', 'completed')->count(),
                'cancelled' => $visits->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function storeVisit(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'clinician_id' => ['nullable', 'exists:users,id'],
            'visit_type' => ['required', 'in:Walk-in,Appointment,Emergency'],
            'reason_for_visit' => ['required', 'string'],
            'priority' => ['required', 'in:Routine,Urgent,Emergency'],
        ]);

        $nextNum = Visit::where('hospital_id', $hospital->id)->count() + 1;
        $visitNumber = 'V-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);

        $visit = Visit::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $validated['patient_id'],
            'department_id' => $validated['department_id'],
            'clinician_id' => $validated['clinician_id'] ?? null,
            'visit_number' => $visitNumber,
            'visit_type' => $validated['visit_type'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'priority' => $validated['priority'],
            'status' => 'waiting',
            'arrived_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Visit started successfully',
            'data' => new VisitResource($visit->load(['patient', 'department', 'clinician'])),
        ], 201);
    }

    public function cancelVisit(Request $request, Visit $visit): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $visit->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Visit cancelled',
            'data' => new VisitResource($visit),
        ]);
    }
}
