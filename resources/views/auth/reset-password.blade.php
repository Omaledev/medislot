@extends('layouts.auth', ['title' => 'Set New Password – MediSlot'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Set New Password
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Please enter your account email and choose a strong new password.
        </p>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                required
                value="{{ old('email', request('email')) }}"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none text-sm transition"
            >
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">New password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                placeholder="At least 8 characters"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none text-sm transition"
            >
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm new password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                placeholder="Re-type new password"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none text-sm transition"
            >
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
        >
            Reset Password
        </button>
    </form>

    <div class="text-center pt-2">
        <a href="{{ route('login') }}" class="text-xs font-semibold text-brand-700 hover:underline">
            Back to Sign In
        </a>
    </div>
</div>
@endsection
