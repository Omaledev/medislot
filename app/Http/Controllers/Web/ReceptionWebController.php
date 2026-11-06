<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionWebController extends Controller
{
    public function dashboard(Request $request): View
    {
        $hospital = $request->user()->hospital;

        $registeredToday = $hospital->patients()->whereDate('created_at', today())->count();
        $waitingCount = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'waiting')->count();
        $inConsultationCount = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'in_consultation')->count();
        $completedCount = $hospital->visits()->whereDate('arrived_at', today())->where('status', 'completed')->count();

        $queue = $hospital->visits()
            ->whereDate('arrived_at', today())
            ->with(['patient', 'department', 'clinician'])
            ->latest('arrived_at')
            ->take(8)
            ->get();

        $beds = $hospital->bedCategories;

        return view('portals.reception.dashboard', compact(
            'hospital',
            'registeredToday',
            'waitingCount',
            'inConsultationCount',
            'completedCount',
            'queue',
            'beds'
        ));
    }

    public function patients(Request $request): View
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

        return view('portals.reception.patients', compact('hospital', 'patients'));
    }

    public function createPatient(Request $request): View
    {
        $hospital = $request->user()->hospital;

        return view('portals.reception.create-patient', compact('hospital'));
    }

    public function storePatient(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:Female,Male,Other'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
        ]);

        $nextNum = Patient::where('hospital_id', $hospital->id)->count() + 1;
        $patientId = 'MED-'.str_pad((string) $nextNum, 6, '0', STR_PAD_LEFT);

        $patient = Patient::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $patientId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'registered_by' => $request->user()->id,
        ]);

        return redirect()->route('reception.patients.show', $patient)->with('success', "Patient {$patient->full_name} registered ({$patient->patient_id})");
    }

    public function showPatient(Patient $patient): View
    {
        $patient->load(['visits.clinician', 'visits.department', 'consultationNotes', 'labRequests', 'prescriptions']);

        return view('portals.reception.patient-profile', compact('patient'));
    }

    public function editPatient(Patient $patient): View
    {
        $hospital = auth()->user()->hospital;

        return view('portals.reception.edit-patient', compact('hospital', 'patient'));
    }

    public function updatePatient(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:Female,Male,Other'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return redirect()->route('reception.patients.show', $patient)->with('success', 'Patient details updated.');
    }

    public function settings(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $departments = $hospital->departments()->where('status', 'active')->get();

        return view('portals.reception.settings', compact('hospital', 'departments'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Reception settings saved.');
    }

    public function queue(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $query = $hospital->visits()
            ->whereDate('arrived_at', today())
            ->with(['patient', 'department', 'clinician']);

        $activeTab = $request->query('tab', 'all');
        if ($activeTab === 'waiting') {
            $query->where('status', 'waiting');
        } elseif ($activeTab === 'in_consultation') {
            $query->where('status', 'in_consultation');
        } elseif ($activeTab === 'completed') {
            $query->where('status', 'completed');
        } elseif ($activeTab === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $allVisits = $hospital->visits()->whereDate('arrived_at', today())->get();
        $visits = $query->latest('arrived_at')->get();

        return view('portals.reception.queue', [
            'hospital' => $hospital,
            'visits' => $visits,
            'activeTab' => $activeTab,
            'totalCount' => $allVisits->count(),
            'waitingCount' => $allVisits->where('status', 'waiting')->count(),
            'inConsultationCount' => $allVisits->where('status', 'in_consultation')->count(),
            'completedCount' => $allVisits->where('status', 'completed')->count(),
            'cancelledCount' => $allVisits->where('status', 'cancelled')->count(),
        ]);
    }

    public function createVisit(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $patients = $hospital->patients;
        $departments = $hospital->departments()->where('status', 'active')->get();
        $clinicians = $hospital->staff()->where('role', 'consultant')->where('status', 'active')->get();
        $selectedPatient = $request->filled('patient_id') ? Patient::find($request->patient_id) : $patients->first();

        return view('portals.reception.start-visit', compact('hospital', 'patients', 'departments', 'clinicians', 'selectedPatient'));
    }

    public function storeVisit(Request $request): RedirectResponse
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

        Visit::create([
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

        return redirect()->route('reception.queue')->with('success', 'Visit created and added to queue.');
    }

    public function cancelVisit(Request $request, Visit $visit): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $visit->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Visit cancelled.');
    }

    public function beds(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $beds = $hospital->bedCategories;

        return view('portals.reception.beds', compact('hospital', 'beds'));
    }
}
