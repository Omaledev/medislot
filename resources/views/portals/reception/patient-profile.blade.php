@extends('layouts.portal', ['title' => $patient->full_name . ' – Patient Profile – MediSlot'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
<a href="{{ route('reception.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ $patient->full_name }}</h1>
                <span class="px-2.5 py-1 bg-emerald-50 text-brand-700 font-bold rounded-lg text-xs">{{ $patient->patient_id }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $patient->age ? $patient->age . ' yrs' : ($patient->date_of_birth?->format('d M Y') ?? '—') }}, {{ $patient->gender }} · {{ $patient->phone ?? 'No phone' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reception.patients.edit', $patient) }}" class="px-4 py-2.5 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition">
                Edit patient
            </a>
            <a href="{{ route('reception.visits.create', ['patient_id' => $patient->id]) }}" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Start new visit
            </a>
        </div>
    </div>

    <!-- 3 Column Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Patient Details -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3 text-xs">
            <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Patient details</h3>

            <div class="space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Date of birth</span>
                    <span class="font-medium text-slate-800">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Gender</span>
                    <span class="font-medium text-slate-800">{{ $patient->gender }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Phone</span>
                    <span class="font-medium text-slate-800">{{ $patient->phone ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Address</span>
                    <span class="font-medium text-slate-800 text-right">{{ $patient->address ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Emergency contact</span>
                    <span class="font-medium text-slate-800 text-right">{{ $patient->emergency_contact ?? '—' }}</span>
                </div>
            </div>
        </div>

        <!-- Clinical Background -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3 text-xs">
            <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Clinical background</h3>

            <div class="space-y-2.5">
                <div>
                    <span class="text-slate-400 block mb-0.5">Allergies</span>
                    <span class="font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded">{{ $patient->allergies ?? 'None known' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Known Conditions</span>
                    <span class="font-medium text-slate-800">{{ $patient->chronic_conditions ?? 'None recorded' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Current Medication</span>
                    <span class="font-medium text-slate-800">{{ $patient->current_medications ?? 'None' }}</span>
                </div>
                <div class="pt-2 text-[11px] text-slate-400">
                    Recorded at this hospital only.
                </div>
            </div>
        </div>

        <!-- Visits History -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3 text-xs">
            <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Recent visits</h3>

            <div class="space-y-3">
                @forelse($patient->visits as $v)
                    <div class="border-b border-slate-50 pb-2.5 last:border-none last:pb-0">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-800">{{ $v->visit_number }} · {{ $v->visit_type }}</span>
                            @if($v->status === 'in_consultation')
                                <span class="text-[11px] text-blue-600 font-semibold">In consultation</span>
                            @elseif($v->status === 'waiting')
                                <span class="text-[11px] text-amber-700 font-semibold">Waiting</span>
                            @elseif($v->status === 'completed')
                                <span class="text-[11px] text-emerald-700 font-semibold">Completed</span>
                            @else
                                <span class="text-[11px] text-slate-400 font-semibold">Cancelled</span>
                            @endif
                        </div>
                        <p class="text-slate-500 mt-1">{{ $v->reason_for_visit }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $v->department?->name }} · {{ $v->clinician?->name ?? 'Unassigned' }}</p>
                    </div>
                @empty
                    <p class="text-slate-400 text-xs">No previous visits recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
