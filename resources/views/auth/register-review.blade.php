@extends('layouts.auth', ['title' => 'Review Registration – MediSlot'])

@section('content')
<div class="space-y-6">
    <!-- Stepper Header -->
    <div class="flex items-center space-x-3 text-xs font-semibold">
        <div class="flex items-center space-x-1.5 text-emerald-600">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
            <span>Hospital Information</span>
        </div>
        <div class="w-8 h-0.5 bg-emerald-600"></div>
        <div class="flex items-center space-x-1.5 text-emerald-600">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
            <span>Account Administrator</span>
        </div>
        <div class="w-8 h-0.5 bg-brand-700"></div>
        <div class="flex items-center space-x-1.5 text-brand-700">
            <span class="w-6 h-6 rounded-full bg-brand-700 text-white flex items-center justify-center text-xs">3</span>
            <span>Review</span>
        </div>
    </div>

    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Review Your Registration
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Check your details before creating the account.
        </p>
    </div>

    <div class="space-y-4">
        <!-- Hospital Information Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="font-bold text-slate-900 text-sm">Hospital information</h3>
                <a href="{{ route('register') }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>
            </div>
            <div class="grid grid-cols-2 gap-y-2 text-xs">
                <span class="text-slate-500">Name</span>
                <span class="font-medium text-slate-800">{{ $step1['hospital_name'] ?? 'Hospital' }}</span>

                <span class="text-slate-500">Type</span>
                <span class="font-medium text-slate-800">{{ $step1['hospital_type'] ?? 'General Hospital' }}</span>

                <span class="text-slate-500">Email</span>
                <span class="font-medium text-slate-800">{{ $step1['hospital_email'] ?? $step1['email'] ?? '—' }}</span>

                <span class="text-slate-500">Phone</span>
                <span class="font-medium text-slate-800">{{ $step1['hospital_phone'] ?? $step1['phone'] ?? '—' }}</span>

                <span class="text-slate-500">Location</span>
                <span class="font-medium text-slate-800">{{ $step1['lga'] ?? 'LGA' }}, {{ $step1['state'] ?? 'State' }}, {{ $step1['country'] ?? 'Nigeria' }}</span>

                <span class="text-slate-500">Address</span>
                <span class="font-medium text-slate-800">{{ $step1['hospital_address'] ?? $step1['address'] ?? '—' }}</span>
            </div>
        </div>

        <!-- Account Administrator Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="font-bold text-slate-900 text-sm">Account administrator</h3>
                <a href="{{ route('register.administrator') }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>
            </div>
            <div class="grid grid-cols-2 gap-y-2 text-xs">
                <span class="text-slate-500">Name</span>
                <span class="font-medium text-slate-800">{{ $step2['first_name'] }} {{ $step2['last_name'] }}</span>

                <span class="text-slate-500">Email</span>
                <span class="font-medium text-slate-800">{{ $step2['email'] }}</span>

                <span class="text-slate-500">Phone</span>
                <span class="font-medium text-slate-800">{{ $step2['phone'] }}</span>

                <span class="text-slate-500">Password</span>
                <span class="font-medium text-slate-400">Hidden for your security</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register.complete') }}" class="pt-4 flex items-center justify-between">
        @csrf
        <a
            href="{{ route('register.administrator') }}"
            class="px-5 py-2.5 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-sm transition"
        >
            Back
        </a>
        <button
            type="submit"
            class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm transition shadow-sm"
        >
            Create Hospital Account
        </button>
    </form>
</div>
@endsection
