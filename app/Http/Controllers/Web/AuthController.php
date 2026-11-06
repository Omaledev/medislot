<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function sendOtp(Request $request): RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        $this->generateAndSendOtp($step1, $step2);

        return redirect()->route('register.verify-email');
    }

    public function showVerifyEmail(): View|RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        $otpData = session('register.otp');
        if (! $otpData || now()->isAfter($otpData['expires_at'])) {
            $this->generateAndSendOtp($step1, $step2);
        }

        return view('auth.verify-email', compact('step1', 'step2'));
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        $otpData = session('register.otp');
        if ($otpData && ! empty($otpData['sent_at'])) {
            $sentAt = Carbon::parse($otpData['sent_at']);
            if ($sentAt->copy()->addSeconds(30)->isFuture()) {
                $secondsRemaining = max(1, (int) now()->diffInSeconds($sentAt->copy()->addSeconds(30)));

                return back()->with('info', "Please wait {$secondsRemaining} seconds before requesting a new code.");
            }
        }

        $this->generateAndSendOtp($step1, $step2);

        return back()->with('success', 'A new 6-digit verification code has been sent to '.$step2['email']);
    }

    private function generateAndSendOtp(array $step1, array $step2): string
    {
        $otp = (string) random_int(100000, 999999);

        session([
            'register.otp' => [
                'code' => $otp,
                'expires_at' => now()->addMinutes(15),
                'sent_at' => now(),
            ],
        ]);

        try {
            Mail::to($step2['email'])->send(new RegistrationOtpMail(
                otp: $otp,
                adminName: "{$step2['first_name']} {$step2['last_name']}",
                hospitalName: $step1['hospital_name'],
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return $otp;
    }

    public function completeRegistration(Request $request): RedirectResponse
    {
        $step1 = session('register.step1');
        $step2 = session('register.step2');

        if (! $step1 || ! $step2) {
            return redirect()->route('register');
        }

        // Support both single "otp" input and 6-box "otp_digit[]" array
        $otpInput = $request->input('otp');
        if (empty($otpInput) && is_array($request->input('otp_digit'))) {
            $otpInput = implode('', array_filter($request->input('otp_digit'), fn ($v) => $v !== null && $v !== ''));
        }
        $otpInput = preg_replace('/\D/', '', (string) $otpInput);

        if (strlen($otpInput) !== 6) {
            return back()->withErrors(['otp' => 'Please enter the complete 6-digit verification code.'])->withInput();
        }

        $otpData = session('register.otp');
        if (! $otpData) {
            return back()->withErrors(['otp' => 'No active verification code found. Please request a new code.'])->withInput();
        }

        if (now()->isAfter($otpData['expires_at'])) {
            return back()->withErrors(['otp' => 'Verification code has expired. Please request a new code.'])->withInput();
        }

        if ($otpInput !== (string) $otpData['code']) {
            return back()->withErrors(['otp' => 'Invalid verification code. Please check your inbox and try again.'])->withInput();
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

        ActivityLog::create([
            'hospital_id' => $hospital->id,
            'user_id' => $admin->id,
            'action' => 'hospital_registered',
            'description' => "Hospital '{$hospital->name}' successfully registered on MediSlot platform with primary admin {$admin->name}.",
        ]);

        session()->forget(['register.step1', 'register.step2', 'register.otp']);

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
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'hospital_admin' => redirect()->route('admin.dashboard'),
            'reception' => redirect()->route('reception.dashboard'),
            'consultant' => redirect()->route('consultant.dashboard'),
            'lab_scientist' => redirect()->route('lab.dashboard'),
            'pharmacist' => redirect()->route('pharmacy.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
