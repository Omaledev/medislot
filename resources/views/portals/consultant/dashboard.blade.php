@extends('layouts.portal', ['title' => 'Consultant Dashboard – MediSlot HMS'])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>My Patients</span>
</a>
<a href="{{ route('consultant.history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Consultation History</span>
</a>
<a href="{{ route('consultant.lab-requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Laboratory Requests</span>
</a>
<a href="{{ route('consultant.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('consultant.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('consultant.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Consultant workspace</h1>
            <p class="text-xs text-slate-500 mt-1">Welcome back, Dr. {{ $user->name }}. You have {{ $waitingCount }} patient{{ $waitingCount == 1 ? '' : 's' }} waiting in queue.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('consultant.patients') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                Assigned Patients ({{ $assignedCount }})
            </a>
            <a href="{{ route('consultant.messages') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Open Messages
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Assigned Today</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $assignedCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Patients in your clinic</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Waiting Queue</div>
            <div class="text-3xl font-bold text-amber-600 mt-2">{{ $waitingCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Ready for consultation</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">In Consultation</div>
            <div class="text-3xl font-bold text-brand-700 mt-2">{{ $inConsultationCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Currently open sessions</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Completed Today</div>
            <div class="text-3xl font-bold text-emerald-700 mt-2">{{ $completedCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Finished visits</div>
        </div>
    </div>

    <!-- Quick Alerts Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('consultant.lab-requests') }}" class="bg-teal-50/60 border border-teal-200 rounded-2xl p-4 flex items-center justify-between hover:bg-teal-50 transition">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Laboratory Requests</h3>
                    <p class="text-xs text-slate-600">{{ $pendingLabResults }} pending investigation results</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-teal-800 bg-white px-3 py-1 rounded-full border border-teal-200">View</span>
        </a>

        <a href="{{ route('consultant.prescriptions') }}" class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 flex items-center justify-between hover:bg-blue-50 transition">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Active Prescriptions</h3>
                    <p class="text-xs text-slate-600">{{ $pendingPrescriptions }} prescriptions currently in pharmacy queue</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-blue-800 bg-white px-3 py-1 rounded-full border border-blue-200">View</span>
        </a>
    </div>

    <!-- Patient Workload Queue Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Patient Workload Queue</h2>
                <p class="text-xs text-slate-500">Patients booked or waiting for consultation today</p>
            </div>
            <!-- Tabs Filter -->
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl">
                <a href="{{ route('consultant.dashboard', ['tab' => 'all']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ $tab === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">All</a>
                <a href="{{ route('consultant.dashboard', ['tab' => 'waiting']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ $tab === 'waiting' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Waiting ({{ $waitingCount }})</a>
                <a href="{{ route('consultant.dashboard', ['tab' => 'in_consultation']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ $tab === 'in_consultation' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">In Consultation</a>
                <a href="{{ route('consultant.dashboard', ['tab' => 'completed']) }}" class="px-3 py-1 rounded-lg text-xs font-medium {{ $tab === 'completed' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Completed</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Visit No</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $visit)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $visit->patient?->full_name }}</div>
                                <div class="text-slate-400 text-xs">{{ $visit->patient?->patient_id }} · {{ $visit->patient?->gender }}, {{ $visit->patient?->age ?? 'Age N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $visit->priority === 'Emergency' ? 'bg-red-50 text-red-700' : ($visit->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $visit->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-800">{{ $visit->department?->name ?? 'General Medicine' }}</td>
                            <td class="px-6 py-4 max-w-xs truncate text-slate-600">{{ $visit->reason_for_visit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $visit->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($visit->status === 'in_consultation' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('consultant.workspace', $visit) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                    {{ $visit->status === 'completed' ? 'View Details' : 'Open Workspace' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No patients in this queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
