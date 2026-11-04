@extends('layouts.portal', ['title' => 'Consultation History – Consultant MediSlot'])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>My Patients</span>
</a>
<a href="{{ route('consultant.history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Consultation History</h1>
            <p class="text-xs text-slate-500 mt-1">Archived log of finalized consultations and clinical management notes.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Visit Ref</th>
                        <th class="px-6 py-3">Chief Complaint</th>
                        <th class="px-6 py-3">Final Diagnosis</th>
                        <th class="px-6 py-3">Completed On</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($visits as $v)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $v->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $v->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $v->visit_number }}</td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate">{{ $v->reason_for_visit }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800 max-w-xs truncate">{{ $v->consultationNote?->assessment_diagnosis ?? 'Clinically documented' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $v->completed_at?->format('M d, Y · H:i') ?? $v->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('consultant.workspace', ['visit' => $v, 'tab' => 'notes']) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold shadow-sm transition">
                                    View Note
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No finalized consultations in your history yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($visits->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $visits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
