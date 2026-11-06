@extends('layouts.portal', ['title' => 'Bed Management – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.departments') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Departments</span>
</a>
<a href="{{ route('admin.staff') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Staff</span>
</a>
<a href="{{ route('admin.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Beds</span>
</a>
<a href="{{ route('admin.admissions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Admissions</span>
</a>
<a href="{{ route('admin.services') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Services</span>
</a>
<a href="{{ route('admin.integrations') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>EHR Integrations</span>
</a>
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div class="space-y-6" x-data="{ addCategoryModal: false, activeEdit: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Bed management</h1>
            <p class="text-xs text-slate-500 mt-1">Published to the public finder · Last updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Available = Total – Occupied – Out of service. Occupancy changes automatically when patients are admitted or discharged.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.admissions') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                View admissions
            </a>
            <button @click="addCategoryModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Add bed category
            </button>
        </div>
    </div>

    <!-- Wards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($beds as $bed)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-base">{{ $bed->name }}</h3>
                    <div>
                        @if($bed->status === 'available')
                            <span class="inline-flex items-center space-x-1.5 text-xs text-emerald-700 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Available</span>
                            </span>
                        @elseif($bed->status === 'limited')
                            <span class="inline-flex items-center space-x-1.5 text-xs text-amber-700 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Limited</span>
                            </span>
                        @else
                            <span class="inline-flex items-center space-x-1.5 text-xs text-red-600 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span>Full</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Segmented Capacity Bar -->
                @php
                    $tot = max(1, $bed->total_beds);
                    $pctOccupied = ($bed->occupied_beds / $tot) * 100;
                    $pctOutOfService = ($bed->out_of_service_beds / $tot) * 100;
                    $pctAvailable = ($bed->available_beds / $tot) * 100;
                @endphp
                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden flex">
                    <div style="width: {{ $pctOccupied }}%" class="bg-slate-700 h-full" title="Occupied: {{ $bed->occupied_beds }}"></div>
                    <div style="width: {{ $pctOutOfService }}%" class="bg-slate-400 h-full" title="Out of service: {{ $bed->out_of_service_beds }}"></div>
                    <div style="width: {{ $pctAvailable }}%" class="bg-brand-500 h-full" title="Available: {{ $bed->available_beds }}"></div>
                </div>

                <!-- Counts -->
                <div class="grid grid-cols-4 gap-2 pt-1 text-center">
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Total</span>
                        <span class="text-xl font-bold text-slate-900">{{ $bed->total_beds }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Occupied</span>
                        <span class="text-xl font-bold text-slate-700">{{ $bed->occupied_beds }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Out of service</span>
                        <span class="text-xl font-bold text-slate-500">{{ $bed->out_of_service_beds }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Available</span>
                        <span class="text-xl font-bold text-brand-700">{{ $bed->available_beds }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-2 flex items-center space-x-3 text-xs">
                    <button
                        @click="activeEdit = { id: {{ $bed->id }}, name: '{{ $bed->name }}', total: {{ $bed->total_beds }}, out: {{ $bed->out_of_service_beds }} }"
                        class="px-3 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg transition"
                    >
                        Edit capacity
                    </button>
                    <button
                        @click="activeEdit = { id: {{ $bed->id }}, name: '{{ $bed->name }}', total: {{ $bed->total_beds }}, out: {{ $bed->out_of_service_beds }} }"
                        class="px-3 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg transition"
                    >
                        Mark out of service
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bar Key -->
    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-4 pt-2">
        <span class="font-semibold text-slate-700">Bar key:</span>
        <span class="flex items-center space-x-1.5">
            <span class="w-3 h-3 rounded-sm bg-slate-700 inline-block"></span>
            <span>Dark slate = occupied</span>
        </span>
        <span class="flex items-center space-x-1.5">
            <span class="w-3 h-3 rounded-sm bg-slate-400 inline-block"></span>
            <span>Mid slate = out of service</span>
        </span>
        <span class="flex items-center space-x-1.5">
            <span class="w-3 h-3 rounded-sm bg-brand-500 inline-block"></span>
            <span>Teal = available</span>
        </span>
    </div>

    <!-- Edit Capacity Modal -->
    <div x-show="activeEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="activeEdit = null" class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Edit <span x-text="activeEdit?.name"></span> Capacity</h3>
                <button @click="activeEdit = null" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/beds/' + activeEdit?.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Total Beds</label>
                    <input type="number" name="total_beds" :value="activeEdit?.total" required min="0" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Out of Service Beds</label>
                    <input type="number" name="out_of_service_beds" :value="activeEdit?.out" required min="0" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="activeEdit = null" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Bed Category Modal -->
    <div x-show="addCategoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addCategoryModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Add Bed Category</h3>
                <button @click="addCategoryModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.beds.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Ward / Category name</label>
                    <input type="text" name="name" required placeholder="e.g. Neonatal ICU" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Total Bed Capacity</label>
                    <input type="number" name="total_beds" required min="1" value="10" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="addCategoryModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
