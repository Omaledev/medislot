@extends('layouts.portal', ['title' => 'Laboratory Requests – Consultant MediSlot'])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>My Patients</span>
</a>
<a href="{{ route('consultant.history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Consultation History</span>
</a>
<a href="{{ route('consultant.lab-requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Laboratory Requests</h1>
            <p class="text-xs text-slate-500 mt-1">Status of pathology, hematology, and diagnostic investigations you have ordered.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Req No</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Test Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Result</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $lr)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $lr->request_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $lr->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $lr->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $lr->test_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $lr->category }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lr->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $lr->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lr->status === 'submitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-teal-50 text-teal-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $lr->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($lr->result)
                                    <span class="font-bold text-slate-900">{{ $lr->result->result_value }} {{ $lr->result->unit }}</span>
                                    @if($lr->result->interpretation)
                                        <span class="block text-[10px] text-slate-500 font-medium">({{ $lr->result->interpretation }})</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Pending analysis</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($lr->visit_id)
                                    <a href="{{ route('consultant.workspace', ['visit' => $lr->visit_id, 'tab' => 'laboratory']) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold shadow-sm transition">
                                        Workspace
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No laboratory requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
