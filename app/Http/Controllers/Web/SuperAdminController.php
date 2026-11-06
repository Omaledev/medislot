<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\BedCategory;
use App\Models\Hospital;
use App\Models\HospitalApiKey;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $totalHospitals = Hospital::count();
        $activeHospitals = Hospital::where('status', 'active')->count();
        $pendingHospitals = Hospital::where('status', '!=', 'active')->count();

        $totalBeds = BedCategory::sum('total_beds');
        $availableBeds = BedCategory::sum('available_beds');

        $totalPatients = Patient::count();
        $totalAdmissions = Admission::count();
        $activeAdmissions = Admission::where('status', 'admitted')->count();
        $totalApiKeys = HospitalApiKey::count();

        $recentHospitals = Hospital::withCount(['users', 'bedCategories'])
            ->latest()
            ->take(6)
            ->get();

        $recentActivities = ActivityLog::with(['hospital', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalHospitals',
            'activeHospitals',
            'pendingHospitals',
            'totalBeds',
            'availableBeds',
            'totalPatients',
            'totalAdmissions',
            'activeAdmissions',
            'totalApiKeys',
            'recentHospitals',
            'recentActivities'
        ));
    }

    public function hospitals(Request $request): View
    {
        $query = Hospital::withCount(['users', 'bedCategories', 'admissions']);

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        $hospitals = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.hospitals.index', compact('hospitals'));
    }

    public function showHospital(Hospital $hospital): View
    {
        $hospital->load([
            'users' => fn ($q) => $q->latest(),
            'departments',
            'bedCategories',
            'apiKeys',
        ]);

        $hospitalBedsTotal = $hospital->bedCategories->sum('total_beds');
        $hospitalBedsAvail = $hospital->bedCategories->sum('available_beds');
        $hospitalAdmissionsCount = Admission::where('hospital_id', $hospital->id)->count();
        $hospitalPatientsCount = Patient::where('hospital_id', $hospital->id)->count();

        $recentLogs = ActivityLog::where('hospital_id', $hospital->id)
            ->with('user')
            ->latest()
            ->take(15)
            ->get();

        return view('superadmin.hospitals.show', compact(
            'hospital',
            'hospitalBedsTotal',
            'hospitalBedsAvail',
            'hospitalAdmissionsCount',
            'hospitalPatientsCount',
            'recentLogs'
        ));
    }

    public function updateHospitalStatus(Request $request, Hospital $hospital): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,pending_verification,inactive'],
        ]);

        $oldStatus = $hospital->status;
        $hospital->update(['status' => $validated['status']]);

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => auth()->id(),
            'action' => 'hospital_status_changed',
            'description' => "Hospital status changed from '{$oldStatus}' to '{$validated['status']}' by Platform Superadmin (".auth()->user()->name.').',
        ]);

        return back()->with('success', "Hospital '{$hospital->name}' status updated to {$validated['status']}.");
    }

    public function activity(Request $request): View
    {
        $query = ActivityLog::with(['hospital', 'user']);

        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', $request->query('hospital_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->query('action').'%');
        }

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where('description', 'like', "%{$search}%");
        }

        $activities = $query->latest()->paginate(25)->withQueryString();
        $hospitalsList = Hospital::orderBy('name')->get(['id', 'name']);

        return view('superadmin.activity', compact('activities', 'hospitalsList'));
    }

    public function integrations(): View
    {
        $apiKeys = HospitalApiKey::with('hospital')
            ->latest()
            ->paginate(20);

        $integrationLogs = ActivityLog::with(['hospital', 'user'])
            ->where(function ($q) {
                $q->where('action', 'like', '%integration%')
                    ->orWhere('action', 'like', '%adt_%')
                    ->orWhere('action', 'like', '%api_key%')
                    ->orWhere('action', 'like', '%sync%');
            })
            ->latest()
            ->take(20)
            ->get();

        $activeHospitalsWithKeys = HospitalApiKey::distinct('hospital_id')->count('hospital_id');

        return view('superadmin.integrations', compact(
            'apiKeys',
            'integrationLogs',
            'activeHospitalsWithKeys'
        ));
    }

    public function operators(): View
    {
        $operators = User::where('role', 'superadmin')->latest()->get();

        return view('superadmin.operators', compact('operators'));
    }

    public function storeOperator(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $operator = User::create([
            'hospital_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'superadmin',
            'phone' => $validated['phone'] ?? null,
            'title' => 'Platform Superadmin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        ActivityLog::create([
            'hospital_id' => null,
            'user_id' => auth()->id(),
            'action' => 'superadmin_operator_created',
            'description' => "New Platform Superadmin operator '{$operator->name}' ({$operator->email}) created by ".auth()->user()->name.'.',
        ]);

        return back()->with('success', "Superadmin operator '{$operator->name}' created successfully.");
    }
}
