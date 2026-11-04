@extends('layouts.portal', ['title' => 'Reception Desk – MediSlot HMS'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('topbar_search')
<form action="{{ route('reception.patients') }}" method="GET" class="relative hidden sm:block">
    <input type="text" name="q" placeholder="Search patients" class="w-64 pl-3.5 pr-8 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
    <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Reception desk</h1>
            <p class="text-xs text-slate-500 mt-1">Good morning, {{ auth()->user()->name }}. Here is today at {{ $hospital->name }}.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reception.patients') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                Find patient
            </a>
            <a href="{{ route('reception.visits.create') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                Start visit
            </a>
            <a href="{{ route('reception.patients.create') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Register patient
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Registered today</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $registeredToday }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Waiting</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $waitingCount }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">In consultation</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $inConsultationCount }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Completed visits</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $completedCount }}</div>
        </div>
    </div>

    <!-- 2 Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Today's Patient Queue -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Today's patient queue</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                            <th class="py-2.5">Patient ID</th>
                            <th class="py-2.5">Patient</th>
                            <th class="py-2.5">Department</th>
                            <th class="py-2.5">Arrival</th>
                            <th class="py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($queue as $v)
                            <tr>
                                <td class="py-3.5 font-bold text-slate-900">{{ $v->patient?->patient_id }}</td>
                                <td class="py-3.5 text-slate-800 font-medium">
                                    <a href="{{ route('reception.patients.show', $v->patient) }}" class="hover:text-brand-700">
                                        {{ $v->patient?->full_name }}
                                    </a>
                                </td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No patients in queue currently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                <a href="{{ route('reception.queue') }}" class="text-xs font-semibold text-brand-700 hover:underline">
                    Open full queue &rarr;
                </a>
            </div>
        </div>

        <!-- Bed Availability Preview -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Bed availability</h3>
                <span class="text-[11px] text-slate-400">View only · updated 3 min ago</span>
            </div>

            <div class="space-y-3 text-xs">
                @foreach($beds as $b)
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="font-bold text-slate-800">{{ $b->name }}</span>
                        @if($b->status === 'available')
                            <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ $b->available_beds }} free</span>
                            </span>
                        @elseif($b->status === 'limited')
                            <span class="inline-flex items-center space-x-1.5 text-amber-700 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>{{ $b->available_beds }} free</span>
                            </span>
                        @else
                            <span class="inline-flex items-center space-x-1.5 text-red-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span>0 free</span>
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                <a href="{{ route('reception.beds') }}" class="text-xs font-semibold text-brand-700 hover:underline">
                    View ward breakdowns &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
