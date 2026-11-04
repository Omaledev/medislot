@extends('layouts.auth', ['title' => 'Account Ready – MediSlot'])

@section('content')
<div class="space-y-6 text-center">
    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full mx-auto flex items-center justify-center">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
    </div>

    <div class="space-y-2">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Account Ready
        </h2>
        <p class="text-sm text-slate-500 max-w-sm mx-auto">
            Your hospital and administrator account have been created successfully. Welcome to MediSlot!
        </p>
    </div>

    <div class="pt-4">
        <a
            href="{{ route('admin.dashboard') }}"
            class="inline-block w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
        >
            Go to Admin Dashboard
        </a>
    </div>
</div>
@endsection
