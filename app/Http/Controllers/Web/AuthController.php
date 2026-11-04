<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'deactivated') {
                Auth::logout();

                return back()->withErrors(['email' => 'Your hospital account is currently inactive.']);
            }

            return $this->redirectForRole($user);
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function showDepartmentLogin(): View
    {
        return view('auth.department-login');
    }

    public function departmentLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('department_username', $validated['department_username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'department_username' => 'Invalid department username or password.',
            ])->onlyInput('department_username');
        }

        if ($user->status === 'deactivated') {
            return back()->withErrors([
                'department_username' => 'This department account is deactivated.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectForRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegisterStep1(): View
    {
        $step1 = session('register.step1', []);

        return view('auth.register-step1', compact('step1'));
    }

    public function processRegisterStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_name' => ['required', 'string', 'max:255'],
            'hospital_type' => ['required', 'string'],
            'hospital_email' => ['required', 'email'],
            'hospital_phone' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'lga' => ['required', 'string'],
            'hospital_address' => ['required', 'string'],
            'registration_number' => ['nullable', 'string'],
            'website' => ['nullable', 'string'],
        ]);

        session(['register.step1' => $validated]);

        return redirect()->route('register.administrator');
    }

    public function showRegisterStep2(): View|RedirectResponse
    {
        if (! session()->has('register.step1')) {
            return redirect()->route('register');
        }

        $step2 = session('register.step2', []);

        return view('auth.register-step2', compact('step2'));
    }

    public function processRegisterStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session(['register.step2' => $validated]);

        return redirect()->route('register.review');
    }

    public function showRegisterReview(): View|RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        return view('auth.register-review', compact('step1', 'step2'));
    }

    public function completeRegistration(Request $request): RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        $slug = Str::slug($step1['hospital_name']);
        if (Hospital::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        $hospital = Hospital::create([
            'name' => $step1['hospital_name'],
            'slug' => $slug,
            'type' => $step1['hospital_type'],
            'email' => $step1['hospital_email'],
            'phone' => $step1['hospital_phone'],
            'country' => $step1['country'] ?? 'Nigeria',
            'state' => $step1['state'],
            'lga' => $step1['lga'],
            'address' => $step1['hospital_address'],
            'registration_number' => $step1['registration_number'] ?? null,
            'website' => $step1['website'] ?? null,
            'emergency_status' => 'accepting',
            'location_for_directions' => $step1['hospital_address'],
            'status' => 'active',
            'last_reported_at' => now(),
        ]);

        $admin = User::create([
            'hospital_id' => $hospital->id,
            'name' => "{$step2['first_name']} {$step2['last_name']}",
            'email' => $step2['email'],
            'password' => Hash::make($step2['password']),
            'role' => 'hospital_admin',
            'department_username' => 'admin/'.$hospital->slug,
            'phone' => $step2['phone'],
            'title' => 'Hospital Admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Default departments
        $defaultDepts = [
            ['name' => 'General Medicine', 'type' => 'clinical', 'slug' => 'general-medicine'],
            ['name' => 'Reception', 'type' => 'administrative', 'slug' => 'reception'],
            ['name' => 'Laboratory', 'type' => 'diagnostic', 'slug' => 'laboratory'],
            ['name' => 'Pharmacy', 'type' => 'support', 'slug' => 'pharmacy'],
        ];

        foreach ($defaultDepts as $d) {
            Department::create([
                'hospital_id' => $hospital->id,
                'name' => $d['name'],
                'slug' => $d['slug'],
                'type' => $d['type'],
                'status' => 'active',
            ]);
        }

        session()->forget(['register.step1', 'register.step2']);

        Auth::login($admin);

        return redirect()->route('register.ready');
    }

    public function showAccountReady(): View
    {
        return view('auth.account-ready');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function processForgotPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $validated['email'])->first();

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $validated['email']]);

        session([
            'reset_link' => $resetLink,
            'reset_email' => $validated['email'],
        ]);

        return redirect()->route('forgot-password.sent');
    }

    public function showResetSent(): View
    {
        return view('auth.reset-sent');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function processResetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $record || $record->token !== $validated['token']) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()->with('error', 'Password reset token has expired. Please request a new one.');
        }

        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please sign in with your new password.');
    }

    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    public function processChangePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->redirectForRole($user)->with('success', 'Password updated successfully.');
    }

    protected function redirectForRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'hospital_admin' => redirect()->route('admin.dashboard'),
            'reception' => redirect()->route('reception.dashboard'),
            'consultant' => redirect()->route('consultant.dashboard'),
            'lab_scientist' => redirect()->route('lab.dashboard'),
            'pharmacist' => redirect()->route('pharmacy.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
