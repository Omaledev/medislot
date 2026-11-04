@extends('layouts.auth', ['title' => 'Change Department Password – MediSlot'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Change Password
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Update credentials for {{ auth()->user()->department_username ?? auth()->user()->name }}.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('department.change-password.post') }}" class="space-y-4">
        @csrf

        <div>
            <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">Current password</label>
            <input
                id="current_password"
                type="password"
                name="current_password"
                required
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
            Update Password
        </button>
    </form>
</div>
@endsection
