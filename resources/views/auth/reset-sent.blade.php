@extends('layouts.auth', ['title' => 'Reset Link Sent – MediSlot'])

@section('content')
<div class="space-y-6 text-center">
    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full mx-auto flex items-center justify-center">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
    </div>

    <div class="space-y-2">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Reset Link Sent
        </h2>
        <p class="text-sm text-slate-500 max-w-sm mx-auto">
            If an account matches {{ session('reset_email', 'that email address') }}, you will receive password reset instructions within a few minutes.
        </p>
    </div>

    @if(session('reset_link'))
        <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs text-left space-y-2">
            <span class="font-bold block uppercase tracking-wider text-[11px] text-teal-800">Quick Testing / Direct Reset Link:</span>
            <a href="{{ session('reset_link') }}" class="font-semibold text-brand-700 underline break-all block">
                {{ session('reset_link') }}
            </a>
            <span class="text-[11px] text-slate-500 block">Click the link above to proceed directly to the new password screen.</span>
        </div>
    @endif

    <div class="pt-4 flex flex-col space-y-2">
        @if(session('reset_link'))
            <a
                href="{{ session('reset_link') }}"
                class="inline-block w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
            >
                Proceed to Reset Password &rarr;
            </a>
        @endif
        <a
            href="{{ route('login') }}"
            class="inline-block w-full py-3 px-4 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-sm transition"
        >
            Return to Sign In
        </a>
    </div>
</div>
@endsection
