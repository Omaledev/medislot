<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdmissionResource;
use App\Http\Resources\V1\BedCategoryResource;
use App\Http\Resources\V1\DepartmentResource;
use App\Http\Resources\V1\HospitalServiceResource;
use App\Http\Resources\V1\StaffResource;
use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Department;
use App\Models\HospitalService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $currentlyAdmitted = Admission::where('hospital_id', $hospital->id)
            ->whereIn('status', ['admitted', 'critical_care'])
            ->count();

        $totalBeds = (int) $hospital->bedCategories()->sum('total_beds');
        $availableBeds = (int) $hospital->bedCategories()->sum('available_beds');
        $wardsCount = $hospital->bedCategories()->count();

        $todayVisits = $hospital->visits()->whereDate('arrived_at', today())->count();
        $waitingVisits = $hospital->visits()->where('status', 'waiting')->count();

        $registeredPatients = $hospital->patients()->count();
        $departmentsCount = $hospital->departments()->count();
        $staffCount = $hospital->staff()->count();

        $bedAvailability = BedCategoryResource::collection($hospital->bedCategories);

        $needsAttention = [];
        // Stale bed check
        $staleBeds = $hospital->bedCategories()->where('updated_at', '<', now()->subHours(1))->get();
        foreach ($staleBeds as $sb) {
            $needsAttention[] = [
                'type' => 'not_updated',
                'title' => 'Not updated',
                'description' => "{$sb->name} availability has not been updated in over 1 hour.",
            ];
        }

        // Full beds check
        $fullBeds = $hospital->bedCategories()->where('available_beds', 0)->get();
        foreach ($fullBeds as $fb) {
            $needsAttention[] = [
                'type' => 'full',
                'title' => 'Full',
                'description' => "{$fb->name} is marked at capacity on the public finder.",
            ];
        }

        $recentActivity = ActivityLog::where('hospital_id', $hospital->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($log) => [
                'time' => $log->created_at->format('H:i'),
                'action' => $log->action,
                'description' => $log->description,
            ]);

        return response()->json([
            'stats' => [
                'currently_admitted' => $currentlyAdmitted,
                'total_beds' => $totalBeds,
                'available_beds' => $availableBeds,
                'wards_count' => $wardsCount,
                'visits_today' => $todayVisits,
                'waiting_now' => $waitingVisits,
                'registered_patients' => $registeredPatients,
                'departments_count' => $departmentsCount,
                'staff_count' => $staffCount,
            ],
            'bed_availability' => $bedAvailability,
            'needs_attention' => $needsAttention,
            'recent_activity' => $recentActivity,
        ]);
    }

    public function departments(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $departments = $hospital->departments()->with(['lead', 'staff'])->get();

        return response()->json([
            'data' => DepartmentResource::collection($departments),
            'meta' => [
                'total' => $departments->count(),
                'active' => $departments->where('status', 'active')->count(),
            ],
        ]);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
            'description' => "Department {$dept->name} was created",
        ]);

        return response()->json([
            'message' => 'Department created successfully',
            'data' => new DepartmentResource($dept),
        ], 201);
    }

    public function toggleDepartmentStatus(Request $request, Department $department): JsonResponse
    {
        $newStatus = $department->status === 'active' ? 'inactive' : 'active';
        $department->update(['status' => $newStatus]);

        return response()->json([
            'message' => "Department marked as {$newStatus}",
            'data' => new DepartmentResource($department),
        ]);
    }

    public function staff(Request $request): JsonResponse
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

        return response()->json([
            'data' => StaffResource::collection($staff),
            'meta' => [
                'total' => $staff->count(),
            ],
        ]);
    }

    public function storeStaff(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:hospital_admin,reception,consultant,lab_scientist,pharmacist'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'title' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
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
            'description' => "Account for {$user->name} ({$user->role}) was created",
        ]);

        return response()->json([
            'message' => 'Staff created successfully',
            'data' => new StaffResource($user),
        ], 201);
    }

    public function beds(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $beds = $hospital->bedCategories;

        return response()->json([
            'data' => BedCategoryResource::collection($beds),
        ]);
    }

    public function updateBedCapacity(Request $request, BedCategory $bedCategory): JsonResponse
    {
        $validated = $request->validate([
            'total_beds' => ['required', 'integer', 'min:0'],
            'out_of_service_beds' => ['required', 'integer', 'min:0'],
            'care_category' => ['nullable', 'string', 'in:emergency,maternity,pediatric,icu,general,specialized'],
            'is_emergency_intake' => ['nullable', 'boolean'],
            'restriction_scope' => ['nullable', 'string', 'max:100'],
            'restriction_label' => ['nullable', 'string', 'max:255'],
        ]);

        $bedCategory->total_beds = $validated['total_beds'];
        $bedCategory->out_of_service_beds = $validated['out_of_service_beds'];
        if ($request->has('care_category')) {
            $bedCategory->care_category = $validated['care_category'] ?? 'general';
        }
        if ($request->has('is_emergency_intake')) {
            $bedCategory->is_emergency_intake = (bool) $validated['is_emergency_intake'];
        }
        if ($request->has('restriction_scope')) {
            $bedCategory->restriction_scope = $validated['restriction_scope'];
        }
        if ($request->has('restriction_label')) {
            $bedCategory->restriction_label = $validated['restriction_label'];
        }
        $bedCategory->recalculateAvailability();

        ActivityLog::create([
            'hospital_id' => $request->user()->hospital_id,
            'user_id' => $request->user()->id,
            'action' => 'Bed capacity updated',
            'description' => "{$bedCategory->name} capacity updated to {$bedCategory->total_beds}",
        ]);

        return response()->json([
            'message' => 'Bed capacity updated successfully',
            'data' => new BedCategoryResource($bedCategory),
        ]);
    }

    public function admissions(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $admissions = Admission::where('hospital_id', $hospital->id)
            ->with(['patient', 'bedCategory'])
            ->latest('admitted_at')
            ->get();

        return response()->json([
            'data' => AdmissionResource::collection($admissions),
            'meta' => [
                'currently_admitted' => $admissions->whereIn('status', ['admitted', 'critical_care'])->count(),
                'discharged_today' => $admissions->where('status', 'discharged')->where('discharged_at', '>=', today())->count(),
            ],
        ]);
    }

    public function dischargeAdmission(Request $request, Admission $admission): JsonResponse
    {
        $admission->status = 'discharged';
        $admission->discharged_at = now();
        $admission->discharged_by = $request->user()->id;
        $admission->save();

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

        return response()->json([
            'message' => 'Patient discharged successfully',
            'data' => new AdmissionResource($admission),
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $services = $hospital->services()->with('department')->get();

        return response()->json([
            'data' => HospitalServiceResource::collection($services),
        ]);
    }

    public function updateService(Request $request, HospitalService $service): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:available,limited,unavailable,unknown'],
            'is_emergency_capable' => ['required', 'boolean'],
            'is_public_visible' => ['required', 'boolean'],
        ]);

        $service->update([
            ...$validated,
            'last_updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => new HospitalServiceResource($service),
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;

        return response()->json([
            'hospital' => $hospital,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
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

        return response()->json([
            'message' => 'Settings saved successfully',
            'hospital' => $hospital,
        ]);
    }

    public function activity(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        $logs = ActivityLog::where('hospital_id', $hospital->id)
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }
}
