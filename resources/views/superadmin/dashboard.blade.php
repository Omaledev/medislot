@extends('layouts.superadmin', ['title' => 'Platform Overview – MediSlot Super Admin'])

@section('content')
<div class="space-y-8">
    <!-- Top Welcome & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Platform Command Center
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Real-time network oversight across all tenant hospitals, bed allocations, and clinical events.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('superadmin.hospitals.index') }}" class="inline-flex items-center rounded-xl bg-teal-800 hover:bg-teal-900 px-4 py-2 text-xs font-semibold text-white shadow-xs transition">
                Hospital Directory ({{ $totalHospitals }})
            </a>
            <a href="{{ route('superadmin.activity') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 shadow-xs transition">
                Live Audit Stream
            </a>
        </div>
    </div>

    <!-- Platform KPI Metric Cards (Clean Typography, No Cluttered Icons) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Hospitals -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Hospitals</span>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalHospitals }}</span>
                <span class="block text-[11px] text-slate-500 mt-1">
                    <strong class="text-emerald-700 font-semibold">{{ $activeHospitals }} active</strong> · {{ $pendingHospitals }} pending
                </span>
            </div>
        </div>

        <!-- Card 2: Managed Network Beds -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Network Beds</span>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalBeds }}</span>
                <span class="block text-[11px] text-slate-500 mt-1">
                    <strong class="text-teal-700 font-semibold">{{ $availableBeds }} free</strong> · {{ $totalBeds - $availableBeds }} occupied
                </span>
            </div>
        </div>

        <!-- Card 3: Registered Patients -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Registered Patients</span>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalPatients }}</span>
                <span class="block text-[11px] text-slate-400 mt-1">Across all hospital tenants</span>
            </div>
        </div>

        <!-- Card 4: Active Admissions -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Admissions & Visits</span>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $activeAdmissions }}</span>
                <span class="block text-[11px] text-slate-400 mt-1">
                    {{ $totalAdmissions }} total all-time
                </span>
            </div>
        </div>

        <!-- Card 5: External EHR Keys -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">EHR API Webhooks</span>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalApiKeys }}</span>
                <span class="block text-[11px] text-slate-400 mt-1">Active integration keys</span>
            </div>
        </div>
    </div>

    <!-- 2-Column Split: Recent Hospitals & Live Platform Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Recently Registered Hospitals -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>Recent Hospital Registrations</span>
                    <span class="rounded-full bg-slate-200 text-slate-700 text-xs px-2 py-0.5 font-semibold">
                        {{ $recentHospitals->count() }}
                    </span>
                </h2>
                <a href="{{ route('superadmin.hospitals.index') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-800 hover:underline">
                    View directory &rarr;
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="divide-y divide-slate-100">
                    @forelse($recentHospitals as $h)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-slate-50/70 transition">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <strong class="text-sm sm:text-base font-bold text-slate-900">{{ $h->name }}</strong>
                                    @if($h->status === 'active')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">Active</span>
                                    @elseif($h->status === 'suspended')
                                        <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200">Suspended</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200">{{ ucfirst($h->status) }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ $h->address }}, {{ $h->state }} · Joined {{ $h->created_at?->diffForHumans() ?? 'recently' }}
                                </p>
                                <div class="flex items-center gap-3 text-xs text-slate-400 pt-1">
                                    <span>Handle: <code class="text-slate-700 font-mono text-[11px]">{{ $h->slug }}</code></span>
                                    <span>·</span>
                                    <span>Staff: <strong class="text-slate-700">{{ $h->users_count }}</strong></span>
                                    <span>·</span>
                                    <span>Wards: <strong class="text-slate-700">{{ $h->bed_categories_count }}</strong></span>
                                </div>
                            </div>

                            <a href="{{ route('superadmin.hospitals.show', $h->id) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white hover:bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-700 transition shrink-0">
                                Inspect Facility &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-500">
                            No hospitals registered yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Live Platform Activity Stream -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="size-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Live Platform Activity</span>
                </h2>
                <a href="{{ route('superadmin.activity') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-800 hover:underline">
                    Full audit feed &rarr;
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs p-5 space-y-4">
                <div class="divide-y divide-slate-100">
                    @forelse($recentActivities as $act)
                        <div class="py-3.5 first:pt-0 last:pb-0 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 truncate max-w-[180px]">
                                    {{ $act->hospital?->name ?? 'Platform System' }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $act->created_at?->diffForHumans() ?? 'just now' }}</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-snug">
                                {{ $act->description }}
                            </p>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400 pt-0.5">
                                <span class="font-mono text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded">{{ $act->action }}</span>
                                @if($act->user)
                                    <span>by {{ $act->user->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-slate-400">
                            No recorded platform activity yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
