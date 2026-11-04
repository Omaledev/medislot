<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Department;
use App\Models\HospitalService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminWebController extends Controller
{
    public function dashboard(Request $request): View
    {
        $hospital = $request->user()->hospital;

        $currentlyAdmitted = Admission::where('hospital_id', $hospital->id)
            ->whereIn('status', ['admitted', 'critical_care'])
            ->count();
        $totalBeds = (int) $hospital->bedCategories()->sum('total_beds');
        $availableBeds = (int) $hospital->bedCategories()->sum('available_beds');
        $visitsToday = $hospital->visits()->whereDate('arrived_at', today())->count();
        $waitingVisits = $hospital->visits()->where('status', 'waiting')->count();
        $registeredPatients = $hospital->patients()->count();

        $beds = $hospital->bedCategories;
        $recentActivity = ActivityLog::where('hospital_id', $hospital->id)->latest()->take(5)->get();

        return view('portals.admin.dashboard', compact(
            'hospital',
            'currentlyAdmitted',
            'totalBeds',
            'availableBeds',
            'visitsToday',
            'waitingVisits',
            'registeredPatients',
            'beds',
            'recentActivity'
        ));
    }

    public function departments(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $departments = $hospital->departments()->with(['lead', 'staff'])->get();
        $staffMembers = $hospital->staff()->where('status', 'active')->get();

        return view('portals.admin.departments', compact('hospital', 'departments', 'staffMembers'));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', 'in:clinical,diagnostic,support,administrative'],
            'description' => ['nullable', 'string'],
            'lead_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $dept = Department::create([
            'hospital_id' => $hospital->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'lead_user_id' => $validated['lead_user_id'] ?? null,
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $request->user()->id,
            'action' => 'Department created',
            'description' => "{$dept->name} was created",
        ]);

        return back()->with('success', 'Department created successfully');
    }

    public function toggleDepartment(Department $department): RedirectResponse
    {
        $department->update([
            'status' => $department->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Department status updated.');
    }

    public function staff(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $query = $hospital->staff()->with('department');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        $staff = $query->get();
        $departments = $hospital->departments;

        return view('portals.admin.staff', compact('hospital', 'staff', 'departments'));
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:hospital_admin,reception,consultant,lab_scientist,pharmacist'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $deptUsername = Str::slug($validated['name']).'/'.$hospital->slug;

        $user = User::create([
            'hospital_id' => $hospital->id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'),
            'role' => $validated['role'],
            'department_username' => $deptUsername,
            'title' => $validated['title'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $request->user()->id,
            'action' => 'Staff account created',
            'description' => "{$user->name} was added as {$user->role}",
        ]);

        return back()->with('success', "Staff member {$user->name} created successfully");
    }

    public function beds(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $beds = $hospital->bedCategories;

        return view('portals.admin.beds', compact('hospital', 'beds'));
    }

    public function updateBedCapacity(Request $request, BedCategory $bedCategory): RedirectResponse
    {
        $validated = $request->validate([
            'total_beds' => ['required', 'integer', 'min:0'],
            'out_of_service_beds' => ['required', 'integer', 'min:0'],
        ]);

        $bedCategory->total_beds = $validated['total_beds'];
        $bedCategory->out_of_service_beds = $validated['out_of_service_beds'];
        $bedCategory->recalculateAvailability();

        ActivityLog::create([
            'hospital_id' => $request->user()->hospital_id,
            'user_id' => $request->user()->id,
            'action' => 'Bed capacity updated',
            'description' => "{$bedCategory->name} capacity updated",
        ]);

        return back()->with('success', 'Bed capacity updated successfully');
    }

    public function storeBedCategory(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'total_beds' => ['required', 'integer', 'min:1'],
        ]);

        $bed = BedCategory::create([
            'hospital_id' => $hospital->id,
            'name' => $validated['name'],
            'total_beds' => $validated['total_beds'],
            'occupied_beds' => 0,
            'out_of_service_beds' => 0,
            'available_beds' => $validated['total_beds'],
            'status' => 'available',
            'last_updated_at' => now(),
        ]);

        return back()->with('success', "Bed category {$bed->name} created");
    }

    public function admissions(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $admissions = Admission::where('hospital_id', $hospital->id)
            ->with(['patient', 'bedCategory'])
            ->latest('admitted_at')
            ->get();
        $patients = $hospital->patients;
        $beds = $hospital->bedCategories()->where('available_beds', '>', 0)->get();

        return view('portals.admin.admissions', compact('hospital', 'admissions', 'patients', 'beds'));
    }

    public function admitPatient(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'bed_category_id' => ['required', 'exists:bed_categories,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $bed = BedCategory::findOrFail($validated['bed_category_id']);
        if ($bed->available_beds <= 0) {
            return back()->withErrors(['bed_category_id' => 'No beds available in selected category.']);
        }

        $nextNum = Admission::where('hospital_id', $hospital->id)->count() + 1;
        $admissionNumber = 'MS-'.str_pad((string) $nextNum, 5, '0', STR_PAD_LEFT);

        $adm = Admission::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $validated['patient_id'],
            'bed_category_id' => $bed->id,
            'admission_number' => $admissionNumber,
            'admitted_at' => now(),
            'status' => 'admitted',
            'notes' => $validated['notes'] ?? null,
            'admitted_by' => $request->user()->id,
        ]);

        $bed->increment('occupied_beds');
        $bed->recalculateAvailability();

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $request->user()->id,
            'action' => 'Admission created',
            'description' => "{$bed->name} · {$adm->patient?->full_name} admitted ({$adm->admission_number})",
        ]);

        return back()->with('success', 'Patient admitted successfully');
    }

    public function dischargeAdmission(Request $request, Admission $admission): RedirectResponse
    {
        $admission->update([
            'status' => 'discharged',
            'discharged_at' => now(),
            'discharged_by' => $request->user()->id,
        ]);

        if ($admission->bedCategory && $admission->bedCategory->occupied_beds > 0) {
            $admission->bedCategory->decrement('occupied_beds');
            $admission->bedCategory->recalculateAvailability();
        }

        ActivityLog::create([
            'hospital_id' => $request->user()->hospital_id,
            'user_id' => $request->user()->id,
            'action' => 'Patient discharged',
            'description' => "{$admission->patient?->full_name} discharged from {$admission->bedCategory?->name}",
        ]);

        return back()->with('success', 'Patient discharged successfully');
    }

    public function services(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $services = $hospital->services()->with('department')->get();
        $departments = $hospital->departments;

        return view('portals.admin.services', compact('hospital', 'services', 'departments'));
    }

    public function storeService(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'status' => ['required', 'in:available,limited,unavailable,unknown'],
            'is_emergency_capable' => ['nullable', 'boolean'],
            'is_public_visible' => ['nullable', 'boolean'],
        ]);

        HospitalService::create([
            'hospital_id' => $hospital->id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'status' => $validated['status'],
            'is_emergency_capable' => $request->boolean('is_emergency_capable'),
            'is_public_visible' => $request->boolean('is_public_visible', true),
            'last_updated_at' => now(),
        ]);

        return back()->with('success', 'Service added successfully');
    }

    public function updateService(Request $request, HospitalService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:available,limited,unavailable,unknown'],
            'is_emergency_capable' => ['nullable', 'boolean'],
            'is_public_visible' => ['nullable', 'boolean'],
        ]);

        $service->update([
            'status' => $validated['status'],
            'is_emergency_capable' => $request->boolean('is_emergency_capable'),
            'is_public_visible' => $request->boolean('is_public_visible'),
            'last_updated_at' => now(),
        ]);

        return back()->with('success', 'Service updated successfully');
    }

    public function settings(Request $request): View
    {
        $hospital = $request->user()->hospital;

        return view('portals.admin.settings', compact('hospital'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'location_for_directions' => ['nullable', 'string'],
            'emergency_status' => ['required', 'in:accepting,limited,not_accepting'],
            'warn_unupdated_hours' => ['required', 'integer'],
            'mark_stale_hours' => ['required', 'integer'],
        ]);

        $hospital->update($validated);

        return back()->with('success', 'Hospital settings saved successfully');
    }

    public function activity(Request $request): View
    {
        $hospital = $request->user()->hospital;
        $logs = ActivityLog::where('hospital_id', $hospital->id)->latest()->paginate(25);

        return view('portals.admin.activity', compact('hospital', 'logs'));
    }
}
