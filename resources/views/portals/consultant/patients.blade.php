@extends('layouts.portal', ['title' => 'My Patients – Consultant MediSlot'])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">My Patients</h1>
            <p class="text-xs text-slate-500 mt-1">Directory of patients under your clinical care and active consultation rosters.</p>
        </div>
    </div>

    <!-- Patients List -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Assigned Patient Queue ({{ $visits->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Patient Name</th>
                        <th class="px-6 py-3">Patient ID</th>
                        <th class="px-6 py-3">Gender / Age</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Reason for Visit</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $visit->patient?->full_name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">{{ $visit->patient?->patient_id }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $visit->patient?->gender }}, {{ $visit->patient?->age ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $visit->department?->name ?? 'General Clinic' }}</td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate">{{ $visit->reason_for_visit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $visit->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($visit->status === 'in_consultation' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('consultant.workspace', $visit) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                    Open Workspace
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No assigned patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
