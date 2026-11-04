@extends('layouts.auth', ['title' => 'Register Your Hospital – Step 1 – MediSlot'])

@section('content')
<div class="space-y-6">
    <!-- Stepper Header -->
    <div class="flex items-center space-x-3 text-xs font-semibold">
        <div class="flex items-center space-x-1.5 text-brand-700">
            <span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center text-xs">1</span>
            <span>Hospital Information</span>
        </div>
        <div class="w-8 h-0.5 bg-slate-200"></div>
        <div class="flex items-center space-x-1.5 text-slate-400">
            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs">2</span>
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
            Register Your Hospital
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Set up your hospital on MediSlot and manage your operations from one place.
        </p>
        <p class="text-xs text-slate-400 mt-2">All fields are required unless marked optional.</p>
    </div>

    <form method="POST" action="{{ route('register.step1') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Hospital name</label>
            <input
                type="text"
                name="hospital_name"
                value="{{ old('hospital_name', $step1['hospital_name'] ?? 'Apex Specialist Hospital') }}"
                required
                placeholder="e.g. Riverside General Hospital"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
            >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hospital type</label>
                <select
                    name="hospital_type"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm bg-white focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
                    <option value="General Hospital" {{ ($step1['hospital_type'] ?? '') === 'General Hospital' ? 'selected' : '' }}>General Hospital</option>
                    <option value="Specialist Hospital" {{ ($step1['hospital_type'] ?? '') === 'Specialist Hospital' ? 'selected' : '' }}>Specialist Hospital</option>
                    <option value="Private Medical Centre" {{ ($step1['hospital_type'] ?? '') === 'Private Medical Centre' ? 'selected' : '' }}>Private Medical Centre</option>
                    <option value="Diagnostic & Lab Centre" {{ ($step1['hospital_type'] ?? '') === 'Diagnostic & Lab Centre' ? 'selected' : '' }}>Diagnostic & Lab Centre</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hospital email address</label>
                <input
                    type="email"
                    name="hospital_email"
                    value="{{ old('hospital_email', $step1['hospital_email'] ?? 'info@apex.example') }}"
                    required
                    placeholder="info@hospital.example"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hospital phone number</label>
                <input
                    type="text"
                    name="hospital_phone"
                    value="{{ old('hospital_phone', $step1['hospital_phone'] ?? '0800 123 4567') }}"
                    required
                    placeholder="0800 000 0000"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Country</label>
                <input
                    type="text"
                    name="country"
                    value="{{ old('country', $step1['country'] ?? 'Nigeria') }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none bg-slate-50"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">State</label>
                <input
                    type="text"
                    name="state"
                    value="{{ old('state', $step1['state'] ?? 'Plateau') }}"
                    required
                    placeholder="e.g. Plateau"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Local Government Area</label>
                <input
                    type="text"
                    name="lga"
                    value="{{ old('lga', $step1['lga'] ?? 'Jos North') }}"
                    required
                    placeholder="e.g. Jos North"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Hospital address</label>
            <input
                type="text"
                name="hospital_address"
                value="{{ old('hospital_address', $step1['hospital_address'] ?? '24 Rayfield Road, Jos') }}"
                required
                placeholder="Street address"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
            >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Registration number (optional)</label>
                <input
                    type="text"
                    name="registration_number"
                    value="{{ old('registration_number', $step1['registration_number'] ?? 'RC-552109') }}"
                    placeholder="e.g. RC-000000"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Website (optional)</label>
                <input
                    type="url"
                    name="website"
                    value="{{ old('website', $step1['website'] ?? 'https://apex.example') }}"
                    placeholder="https://..."
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                You can complete your hospital profile and upload your logo after registration.
            </p>
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
