@extends('layouts.auth', ['title' => 'Verify Your Email – MediSlot'])

@section('content')
<div class="space-y-6">
    <!-- Stepper Header -->
    <div class="flex items-center space-x-2 sm:space-x-3 text-xs font-semibold overflow-x-auto pb-1">
        <div class="flex items-center space-x-1.5 text-emerald-600 shrink-0">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
            <span>Hospital</span>
        </div>
        <div class="w-4 sm:w-6 h-0.5 bg-emerald-600 shrink-0"></div>
        <div class="flex items-center space-x-1.5 text-emerald-600 shrink-0">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
            <span>Administrator</span>
        </div>
        <div class="w-4 sm:w-6 h-0.5 bg-emerald-600 shrink-0"></div>
        <div class="flex items-center space-x-1.5 text-emerald-600 shrink-0">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
            <span>Review</span>
        </div>
        <div class="w-4 sm:w-6 h-0.5 bg-brand-700 shrink-0"></div>
        <div class="flex items-center space-x-1.5 text-brand-700 shrink-0">
            <span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center text-xs font-bold">4</span>
            <span>Verify</span>
        </div>
    </div>

    <div>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Verify Your Email
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            We sent a 6-digit verification code to your administrator email address.
        </p>
    </div>

    <!-- Active Email Recipient Badge -->
    <div class="flex items-center justify-between gap-3 rounded-xl bg-teal-50 border border-teal-100 px-4 py-3 text-xs sm:text-sm">
        <div class="min-w-0">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-teal-800">Administrator Email</span>
            <span class="truncate font-semibold text-slate-900">{{ session('register.step2.email') ?? 'admin@hospital.example' }}</span>
        </div>
        <a href="{{ route('register.administrator') }}" class="shrink-0 font-semibold text-teal-700 hover:text-teal-900 hover:underline text-xs">
            Change email
        </a>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
            {{ session('info') }}
        </div>
    @endif

    @if($errors->has('otp'))
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
            {{ $errors->first('otp') }}
        </div>
    @endif

    <!-- OTP Input Form -->
    <form method="POST" action="{{ route('register.complete') }}" id="otp-form" class="space-y-6">
        @csrf
        <input type="hidden" name="otp" id="hidden-otp" value="" />

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 text-center">
                Enter 6-Digit Code
            </label>

            <!-- 6 Digit Input Boxes -->
            <div class="flex gap-2 sm:gap-3 justify-center" id="otp-container">
                @for($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="1"
                        autocomplete="off"
                        data-index="{{ $i }}"
                        class="otp-digit-input h-13 w-11 sm:h-14 sm:w-12 text-center text-xl sm:text-2xl font-extrabold text-slate-900 rounded-xl border border-slate-300 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100 transition shadow-xs"
                    />
                @endfor
            </div>

            <!-- Live 15-Minute Expiry Countdown -->
            @php
                $expiresTimestamp = session('register.otp.expires_at')
                    ? \Carbon\Carbon::parse(session('register.otp.expires_at'))->timestamp
                    : now()->addMinutes(15)->timestamp;
            @endphp
            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-slate-500">
                <span class="size-2 rounded-full bg-emerald-500" id="timer-dot"></span>
                <span>Code expires in:</span>
                <span id="expiry-timer" class="font-mono font-extrabold text-teal-800 text-sm">15:00</span>
            </div>
        </div>

        <button
            type="submit"
            id="verify-button"
            class="w-full inline-flex min-h-11 items-center justify-center rounded-xl bg-teal-800 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-700/50"
        >
            Verify & Create Hospital Account
        </button>
    </form>

    <!-- Resend Code Form -->
    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-500">Didn't receive the email?</span>
        <form method="POST" action="{{ route('register.resend-otp') }}" id="resend-form" class="inline">
            @csrf
            <button
                type="submit"
                id="resend-btn"
                class="font-semibold text-teal-700 hover:text-teal-900 hover:underline disabled:text-slate-400 disabled:no-underline transition"
            >
                Resend code
            </button>
        </form>
    </div>

    <!-- Back to Review Link -->
    <div class="pt-2 text-center">
        <a href="{{ route('register.review') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 transition">
            &larr; Back to review details
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = Array.from(document.querySelectorAll('.otp-digit-input'));
    const hiddenOtp = document.getElementById('hidden-otp');
    const form = document.getElementById('otp-form');

    if (inputs.length > 0) {
        inputs[0].focus();
    }

    function syncHiddenOtp() {
        const otpValue = inputs.map(i => i.value.trim()).join('');
        hiddenOtp.value = otpValue;
        return otpValue;
    }

    inputs.forEach((input, idx) => {
        // Handle direct key typing
        input.addEventListener('input', function (e) {
            const val = this.value.replace(/\D/g, '');
            this.value = val ? val[val.length - 1] : '';

            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }

            syncHiddenOtp();
        });

        // Handle navigation and Backspace
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!this.value && idx > 0) {
                    inputs[idx - 1].focus();
                    inputs[idx - 1].value = '';
                    e.preventDefault();
                } else {
                    this.value = '';
                }
                syncHiddenOtp();
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                inputs[idx - 1].focus();
            } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });

        // Handle Paste (e.g. user pastes 6-digit code)
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pasted.replace(/\D/g, '').slice(0, 6);

            if (digits.length > 0) {
                digits.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });

                syncHiddenOtp();

                const focusIndex = Math.min(digits.length, inputs.length - 1);
                inputs[focusIndex].focus();
            }
        });
    });

    form.addEventListener('submit', function (e) {
        const otp = syncHiddenOtp();
        if (otp.length !== 6) {
            e.preventDefault();
            alert('Please enter all 6 digits of your verification code.');
            const firstEmpty = inputs.find(i => !i.value.trim());
            if (firstEmpty) firstEmpty.focus();
        }
    });

    // 15-Minute Live OTP Expiry Countdown
    const expiresTimestamp = {{ $expiresTimestamp }};
    const timerEl = document.getElementById('expiry-timer');
    const timerDot = document.getElementById('timer-dot');

    function updateExpiryTimer() {
        const nowSec = Math.floor(Date.now() / 1000);
        const remaining = expiresTimestamp - nowSec;

        if (remaining <= 0) {
            if (timerEl) {
                timerEl.textContent = '00:00 (Expired)';
                timerEl.className = 'font-mono font-extrabold text-rose-600 text-sm';
            }
            if (timerDot) {
                timerDot.className = 'size-2 rounded-full bg-rose-500';
            }
            return;
        }

        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        const formatted = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        if (timerEl) {
            timerEl.textContent = formatted;
            if (remaining < 120) {
                timerEl.className = 'font-mono font-extrabold text-amber-600 text-sm';
                if (timerDot) timerDot.className = 'size-2 rounded-full bg-amber-500 animate-pulse';
            } else {
                timerEl.className = 'font-mono font-extrabold text-teal-800 text-sm';
                if (timerDot) timerDot.className = 'size-2 rounded-full bg-emerald-500';
            }
        }
    }

    updateExpiryTimer();
    setInterval(updateExpiryTimer, 1000);

    // 30-second cooldown timer for resend
    const resendBtn = document.getElementById('resend-btn');
    if (resendBtn && sessionStorage.getItem('otp_cooldown_end')) {
        const remaining = Math.ceil((parseInt(sessionStorage.getItem('otp_cooldown_end'), 10) - Date.now()) / 1000);
        if (remaining > 0) {
            startCooldown(remaining);
        }
    }

    document.getElementById('resend-form')?.addEventListener('submit', function () {
        sessionStorage.setItem('otp_cooldown_end', (Date.now() + 30000).toString());
    });

    function startCooldown(seconds) {
        resendBtn.disabled = true;
        const originalText = 'Resend code';
        resendBtn.textContent = `Resend code (${seconds}s)`;

        const timer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendBtn.textContent = originalText;
                sessionStorage.removeItem('otp_cooldown_end');
            } else {
                resendBtn.textContent = `Resend code (${seconds}s)`;
            }
        }, 1000);
    }
});
</script>
@endsection
