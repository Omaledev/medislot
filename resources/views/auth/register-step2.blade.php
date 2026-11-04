@extends('layouts.auth', ['title' => 'Register Your Hospital – Administrator – MediSlot'])

@section('content')
<div class="space-y-6">
    <!-- Stepper Header -->
    <div class="flex items-center space-x-3 text-xs font-semibold">
        <div class="flex items-center space-x-1.5 text-emerald-600">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
            <span>Hospital Information</span>
        </div>
        <div class="w-8 h-0.5 bg-emerald-600"></div>
        <div class="flex items-center space-x-1.5 text-brand-700">
            <span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center text-xs">2</span>
            <span>Account Administrator</span>
        </div>
        <div class="w-8 h-0.5 bg-slate-200"></div>
        <div class="flex items-center space-x-1.5 text-slate-400">
            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs">3</span>
            <span>Review</span>
        </div>
    </div>

    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Who Will Manage This Hospital?
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Create the account for the person responsible for managing this hospital on MediSlot.
        </p>
    </div>

    <form method="POST" action="{{ route('register.step2') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">First name</label>
                <input
                    type="text"
                    name="first_name"
                    value="{{ old('first_name', $step2['first_name'] ?? 'Chinedu') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Last name</label>
                <input
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $step2['last_name'] ?? 'Obinna') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $step2['email'] ?? 'chinedu@apex.example') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone number</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $step2['phone'] ?? '0803 555 1234') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    value="Password123"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    value="Password123"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div class="flex items-center space-x-4 text-xs text-emerald-600 font-medium pt-1">
            <span>✓ 8 or more characters</span>
            <span>✓ Upper and lower case</span>
            <span>✓ A number</span>
        </div>

        <div class="pt-2">
            <label class="flex items-start space-x-2 text-xs text-slate-600 cursor-pointer">
                <input type="checkbox" required checked class="mt-0.5 rounded border-slate-300 text-brand-700 focus:ring-brand-700">
                <span>I confirm that I am authorized to register and manage this hospital on MediSlot.</span>
            </label>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <a
                href="{{ route('register') }}"
                class="px-5 py-2.5 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-sm transition"
            >
                Back
            </a>
            <button
                type="submit"
                class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm transition shadow-sm"
            >
                Continue
            </button>
        </div>
    </form>
</div>
@endsection
