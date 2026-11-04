@extends('layouts.portal', ['title' => 'Patient Queue – MediSlot Reception'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('content')
<div class="space-y-6" x-data="{ selectedCancel: null }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Today's queue</h1>
            <p class="text-xs text-slate-500 mt-1">{{ date('D, j M Y') }} · {{ $totalCount }} visits</p>
        </div>
        <a href="{{ route('reception.visits.create') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Register walk-in
        </a>
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('reception.queue', ['tab' => 'all']) }}" class="px-4 py-2 rounded-xl border transition shadow-sm {{ $activeTab === 'all' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600' }}">
            All · {{ $totalCount }}
        </a>
        <a href="{{ route('reception.queue', ['tab' => 'waiting']) }}" class="px-4 py-2 rounded-xl border transition shadow-sm {{ $activeTab === 'waiting' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600' }}">
            Waiting · {{ $waitingCount }}
        </a>
        <a href="{{ route('reception.queue', ['tab' => 'in_consultation']) }}" class="px-4 py-2 rounded-xl border transition shadow-sm {{ $activeTab === 'in_consultation' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600' }}">
            In consultation · {{ $inConsultationCount }}
        </a>
        <a href="{{ route('reception.queue', ['tab' => 'completed']) }}" class="px-4 py-2 rounded-xl border transition shadow-sm {{ $activeTab === 'completed' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600' }}">
            Completed · {{ $completedCount }}
        </a>
        <a href="{{ route('reception.queue', ['tab' => 'cancelled']) }}" class="px-4 py-2 rounded-xl border transition shadow-sm {{ $activeTab === 'cancelled' ? 'bg-white border-brand-700 text-brand-700' : 'bg-white border-slate-200 text-slate-600' }}">
            Cancelled · {{ $cancelledCount }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Queue Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                            <th class="py-2.5">Patient ID</th>
                            <th class="py-2.5">Patient</th>
                            <th class="py-2.5">Department</th>
                            <th class="py-2.5">Arrival</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Clinician</th>
                            <th class="py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($visits as $v)
                            <tr>
                                <td class="py-3.5 font-bold text-slate-900">{{ $v->patient?->patient_id }}</td>
                                <td class="py-3.5 text-slate-800 font-medium">{{ $v->patient?->full_name }}</td>
                                <td class="py-3.5 text-slate-600">{{ $v->department?->name ?? 'General OPD' }}</td>
                                <td class="py-3.5 text-slate-500">{{ $v->arrived_at->format('H:i') }}</td>
                                <td class="py-3.5">
                                    @if($v->status === 'waiting')
                                        <span class="inline-flex items-center space-x-1.5 text-amber-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Waiting</span>
                                        </span>
                                    @elseif($v->status === 'in_consultation')
                                        <span class="inline-flex items-center space-x-1.5 text-blue-600 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>In consultation</span>
                                        </span>
                                    @elseif($v->status === 'completed')
                                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Completed</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 text-slate-400 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Cancelled</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-slate-600">{{ $v->clinician?->name ?? 'Not assigned' }}</td>
                                <td class="py-3.5 text-right">
                                    @if($v->status === 'waiting')
                                        <button
                                            @click="selectedCancel = { id: {{ $v->id }}, name: '{{ $v->patient?->full_name }}' }"
                                            class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline"
                                        >
                                            Cancel visit
                                        </button>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No visits in this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-[11px] text-slate-400 mt-4">Only clinicians mark a consultation as started or completed.</p>
        </div>

        <!-- Right Side: Cancel Visit Card -->
        <div class="lg:col-span-4">
            <template x-if="selectedCancel">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm">Cancel visit for <span x-text="selectedCancel?.name"></span>?</h3>

                    <form :action="'/reception/visits/' + selectedCancel?.id + '/cancel'" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Reason (required)</label>
                            <textarea name="reason" required rows="3" placeholder="e.g. Patient left before being seen" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-red-500 outline-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <button type="button" @click="selectedCancel = null" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg font-medium hover:bg-slate-50">
                                Keep visit
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm">
                                Cancel visit
                            </button>
                        </div>
                    </form>
                </div>
            </template>

            <template x-if="!selectedCancel">
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400 space-y-1">
                    <p class="font-medium text-slate-600">Active Queue Desk</p>
                    <p>Click "Cancel visit" on any waiting patient if they leave before consultation.</p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
