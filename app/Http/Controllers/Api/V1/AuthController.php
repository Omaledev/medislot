<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StaffResource;
use App\Mail\RegistrationOtpMail;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        if ($user->status === 'deactivated') {
            return response()->json([
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        $token = $user->createToken('medislot-api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new StaffResource($user),
            'hospital' => [
                'id' => $user->hospital_id,
                'name' => $user->hospital?->name,
                'slug' => $user->hospital?->slug,
            ],
        ]);
    }

    public function departmentLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('department_username', $validated['department_username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'department_username' => ['Invalid department username or password.'],
            ]);
        }

        if ($user->status === 'deactivated') {
            return response()->json([
                'message' => 'This department account is deactivated.',
            ], 403);
        }

        $token = $user->createToken('medislot-dept-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new StaffResource($user),
            'department' => [
                'id' => $user->department_id,
                'name' => $user->department?->name,
                'role' => $user->role,
            ],
            'hospital' => [
                'id' => $user->hospital_id,
                'name' => $user->hospital?->name,
                'slug' => $user->hospital?->slug,
            ],
        ]);
    }

    public function sendRegistrationOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'hospital_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
        ]);

        $email = strtolower($validated['admin_email']);
        $otp = (string) random_int(100000, 999999);

        Cache::put("reg_otp_{$email}", [
            'code' => $otp,
            'hospital_name' => $validated['hospital_name'],
            'admin_name' => $validated['admin_name'],
            'sent_at' => now(),
            'expires_at' => now()->addMinutes(15),
        ], now()->addMinutes(15));

        try {
            Mail::to($email)->send(new RegistrationOtpMail(
                otp: $otp,
                adminName: $validated['admin_name'],
                hospitalName: $validated['hospital_name'],
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Verification code sent successfully.',
            'email' => $email,
            'expires_in_minutes' => 15,
        ]);
    }

    public function verifyRegistrationOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $email = strtolower($validated['admin_email']);
        $otp = preg_replace('/\D/', '', $validated['otp']);

        $cached = Cache::get("reg_otp_{$email}");

        if (! $cached || now()->isAfter($cached['expires_at'])) {
            return response()->json([
                'message' => 'Verification code has expired or was not requested. Please request a new code.',
            ], 422);
        }

        if ($cached['code'] !== $otp) {
            return response()->json([
                'message' => 'Invalid verification code. Please check your inbox and try again.',
            ], 422);
        }

        Cache::put("reg_verified_{$email}", true, now()->addMinutes(30));

        return response()->json([
            'message' => 'Email verified successfully. You may now complete hospital registration.',
            'email' => $email,
            'verified' => true,
        ]);
    }

    public function resendRegistrationOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_email' => ['required', 'email'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'admin_name' => ['nullable', 'string', 'max:255'],
        ]);

        $email = strtolower($validated['admin_email']);
        $cached = Cache::get("reg_otp_{$email}");

        if ($cached && ! empty($cached['sent_at'])) {
            $sentAt = Carbon::parse($cached['sent_at']);
            if ($sentAt->copy()->addSeconds(30)->isFuture()) {
                $secondsRemaining = max(1, (int) now()->diffInSeconds($sentAt->copy()->addSeconds(30)));

                return response()->json([
                    'message' => "Please wait {$secondsRemaining} seconds before requesting a new code.",
                    'seconds_remaining' => $secondsRemaining,
                ], 429);
            }
        }

        $hospitalName = $validated['hospital_name'] ?? $cached['hospital_name'] ?? 'Your Hospital';
        $adminName = $validated['admin_name'] ?? $cached['admin_name'] ?? 'Administrator';

        $otp = (string) random_int(100000, 999999);

        Cache::put("reg_otp_{$email}", [
            'code' => $otp,
            'hospital_name' => $hospitalName,
            'admin_name' => $adminName,
            'sent_at' => now(),
            'expires_at' => now()->addMinutes(15),
        ], now()->addMinutes(15));

        try {
            Mail::to($email)->send(new RegistrationOtpMail(
                otp: $otp,
                adminName: $adminName,
                hospitalName: $hospitalName,
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'A new 6-digit verification code has been sent.',
            'email' => $email,
            'expires_in_minutes' => 15,
        ]);
    }

    public function registerHospital(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hospital_name' => ['required', 'string', 'max:255'],
            'hospital_type' => ['required', 'string'],
            'hospital_email' => ['required', 'email'],
            'hospital_phone' => ['required', 'string'],
            'country' => ['nullable', 'string'],
            'state' => ['required', 'string'],
            'lga' => ['required', 'string'],
            'hospital_address' => ['required', 'string'],
            'registration_number' => ['nullable', 'string'],
            'website' => ['nullable', 'string'],

            'admin_first_name' => ['required', 'string'],
            'admin_last_name' => ['required', 'string'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'admin_phone' => ['required', 'string'],
            'admin_password' => ['required', 'string', 'min:8'],
            'otp' => ['nullable', 'string'],
        ]);

        $email = strtolower($validated['admin_email']);
        if ($request->filled('otp')) {
            $otp = preg_replace('/\D/', '', (string) $request->input('otp'));
            $cached = Cache::get("reg_otp_{$email}");
            $isPreVerified = Cache::get("reg_verified_{$email}") === true;

            if (! $isPreVerified && (! $cached || $cached['code'] !== $otp || now()->isAfter($cached['expires_at']))) {
                return response()->json([
                    'message' => 'Invalid or expired OTP verification code.',
                ], 422);
            }
        }

        $slug = Str::slug($validated['hospital_name']);
        if (Hospital::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        $hospital = Hospital::create([
            'name' => $validated['hospital_name'],
            'slug' => $slug,
            'type' => $validated['hospital_type'],
            'email' => $validated['hospital_email'],
            'phone' => $validated['hospital_phone'],
            'country' => $validated['country'] ?? 'Nigeria',
            'state' => $validated['state'],
            'lga' => $validated['lga'],
            'address' => $validated['hospital_address'],
            'registration_number' => $validated['registration_number'] ?? null,
            'website' => $validated['website'] ?? null,
            'emergency_status' => 'accepting',
            'location_for_directions' => $validated['hospital_address'],
            'status' => 'active',
            'last_reported_at' => now(),
        ]);

        $admin = User::create([
            'hospital_id' => $hospital->id,
            'name' => "{$validated['admin_first_name']} {$validated['admin_last_name']}",
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'hospital_admin',
            'department_username' => 'admin/'.$hospital->slug,
            'phone' => $validated['admin_phone'],
            'title' => 'Hospital Admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create standard departments
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

        Cache::forget("reg_otp_{$email}");
        Cache::forget("reg_verified_{$email}");

        $token = $admin->createToken('medislot-admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Hospital registered successfully',
            'token' => $token,
            'user' => new StaffResource($admin),
            'hospital' => [
                'id' => $hospital->id,
                'name' => $hospital->name,
                'slug' => $hospital->slug,
            ],
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new StaffResource($request->user()),
            'hospital' => [
                'id' => $request->user()->hospital_id,
                'name' => $request->user()->hospital?->name,
                'slug' => $request->user()->hospital?->slug,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'If an account exists for this email, a reset token has been issued.',
            ]);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset token generated successfully.',
            'email' => $validated['email'],
            'token' => $token,
            'reset_url' => route('password.reset', ['token' => $token, 'email' => $validated['email']]),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
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
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired password reset token.'],
            ]);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            throw ValidationException::withMessages([
                'token' => ['Password reset token has expired. Please request a new one.'],
            ]);
        }

        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully. You can now log in with your new password.',
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match our records.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }
}
