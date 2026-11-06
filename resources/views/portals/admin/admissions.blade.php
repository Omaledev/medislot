@extends('layouts.portal', ['title' => 'Admissions – MediSlot HMS'])

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
<a href="{{ route('admin.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Beds</span>
</a>
<a href="{{ route('admin.admissions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
<div class="space-y-6" x-data="{
    tab: 'admitted',
    admitModal: false,
    selectedDischarge: null
}">
    <!-- Top Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Admissions</h1>
            <p class="text-xs text-slate-500 mt-1">Admitting or discharging updates bed occupancy automatically</p>
        </div>
        <button @click="admitModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Admit patient
        </button>
    </div>

    <!-- Filter tabs -->
    <div class="flex space-x-2 text-xs font-semibold">
        <button
            @click="tab = 'admitted'"
            :class="tab === 'admitted' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600'"
            class="px-4 py-2 rounded-xl border shadow-sm transition"
        >
            Currently admitted · {{ $admissions->whereIn('status', ['admitted', 'critical_care'])->count() }}
        </button>
        <button
            @click="tab = 'discharged'"
            :class="tab === 'discharged' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600'"
            class="px-4 py-2 rounded-xl border shadow-sm transition"
        >
            Discharged · {{ $admissions->where('status', 'discharged')->count() }}
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Admissions Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                            <th class="py-2.5">Patient ID</th>
                            <th class="py-2.5">Patient</th>
                            <th class="py-2.5">Ward</th>
                            <th class="py-2.5">Bed category</th>
                            <th class="py-2.5">Admitted</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($admissions as $adm)
                            <tr x-show="tab === 'all' || (tab === 'admitted' && ('{{ $adm->status }}' === 'admitted' || '{{ $adm->status }}' === 'critical_care')) || (tab === 'discharged' && '{{ $adm->status }}' === 'discharged')">
                                <td class="py-3.5 font-bold text-slate-900">{{ $adm->admission_number }}</td>
                                <td class="py-3.5 text-slate-800 font-medium">{{ $adm->patient?->full_name }}</td>
                                <td class="py-3.5 text-slate-600">{{ $adm->bedCategory?->name }}</td>
                                <td class="py-3.5 text-slate-600">{{ $adm->bedCategory?->name }}</td>
                                <td class="py-3.5 text-slate-500">{{ $adm->admitted_at->format('j M, H:i') }}</td>
                                <td class="py-3.5">
                                    @if($adm->status === 'admitted')
                                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Admitted</span>
                                        </span>
                                    @elseif($adm->status === 'critical_care')
                                        <span class="inline-flex items-center space-x-1.5 text-amber-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Critical care</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 text-slate-400 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Discharged</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-right space-x-2">
                                    @if($adm->status !== 'discharged')
                                        <button
                                            @click="selectedDischarge = { id: {{ $adm->id }}, name: '{{ $adm->patient?->full_name }}', ward: '{{ $adm->bedCategory?->name }}' }"
                                            class="text-xs font-semibold text-brand-700 hover:underline"
                                        >
                                            View · Discharge
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">Discharged</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Discharge Confirmation Card -->
        <div class="lg:col-span-4">
            <template x-if="selectedDischarge">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm">Discharge <span x-text="selectedDischarge?.name"></span>?</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        This marks the patient as discharged and frees 1 bed in <span x-text="selectedDischarge?.ward" class="font-semibold text-slate-700"></span>. The public finder will show the new count.
                    </p>

                    <form :action="'/admin/admissions/' + selectedDischarge?.id + '/discharge'" method="POST" class="pt-2 flex items-center justify-between">
                        @csrf
                        @method('PUT')
                        <button type="button" @click="selectedDischarge = null" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm">
                            Discharge patient
                        </button>
                    </form>
                </div>
            </template>

            <template x-if="!selectedDischarge">
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400 space-y-2">
                    <p>Select a patient from the list to view details or process a discharge.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Admit Patient Modal -->
    <div x-show="admitModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="admitModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Admit Patient to Ward</h3>
                <button @click="admitModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.admissions.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Patient</label>
                    <select name="patient_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                        @foreach($patients as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->full_name }} ({{ $pt->patient_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Ward / Bed Category</label>
                    <select name="bed_category_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                        @foreach($beds as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->available_beds }} available)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Admission notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Reason for admission..." class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="admitModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Admit patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
