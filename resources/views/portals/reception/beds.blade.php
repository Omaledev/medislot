@extends('layouts.portal', ['title' => 'Bed Availability – MediSlot Reception'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Bed availability</h1>
            <p class="text-xs text-slate-500 mt-1">Real-time occupancy status across all hospital wards and specialized units.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reception.visits.create') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Admit / Start visit
            </a>
        </div>
    </div>

    @php
        $totalBeds = $beds->sum('total_beds');
        $occupiedBeds = $beds->sum('occupied_beds');
        $availableBeds = $beds->sum('available_beds');
        $overallOccupancy = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;
    @endphp

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Total Bed Capacity</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $totalBeds }}</div>
            <div class="text-xs text-slate-400 mt-1">Across {{ $beds->count() }} wards</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Occupied Beds</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $occupiedBeds }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $overallOccupancy }}% overall occupancy</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Available Beds</div>
            <div class="text-3xl font-bold text-emerald-700 mt-2">{{ $availableBeds }}</div>
            <div class="text-xs text-slate-400 mt-1">Ready for patient admission</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">ICU / Critical Status</div>
            @php
                $icu = $beds->firstWhere('name', 'Intensive Care Unit (ICU)');
            @endphp
            <div class="text-3xl font-bold {{ ($icu?->available_beds ?? 0) <= 2 ? 'text-amber-600' : 'text-slate-900' }} mt-2">
                {{ $icu?->available_beds ?? 0 }} <span class="text-sm font-normal text-slate-400">/ {{ $icu?->total_beds ?? 0 }}</span>
            </div>
            <div class="text-xs text-slate-400 mt-1">{{ $icu?->name ?? 'Critical care unit' }}</div>
        </div>
    </div>

    <!-- Ward Breakdown Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Ward Occupancy Breakdown</h2>
            <span class="text-xs text-slate-400">Updated real-time</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Ward / Category</th>
                        <th class="px-6 py-3">Total Beds</th>
                        <th class="px-6 py-3">Occupied</th>
                        <th class="px-6 py-3">Available</th>
                        <th class="px-6 py-3">Occupancy Rate</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($beds as $bed)
                        @php
                            $rate = $bed->total_beds > 0 ? round(($bed->occupied_beds / $bed->total_beds) * 100) : 0;
                            $statusColor = $bed->available_beds == 0 ? 'bg-red-50 text-red-700' : ($bed->available_beds <= 2 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700');
                            $statusText = $bed->available_beds == 0 ? 'Full' : ($bed->available_beds <= 2 ? 'Limited' : 'Available');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $bed->name }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $bed->total_beds }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $bed->occupied_beds }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $bed->available_beds }}</td>
                            <td class="px-6 py-4 w-48">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full {{ $rate >= 90 ? 'bg-red-500' : ($rate >= 70 ? 'bg-amber-500' : 'bg-brand-600') }}" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-700 w-8 text-right">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No bed categories registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
