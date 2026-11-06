@extends('layouts.portal', ['title' => 'Laboratory Results & Reports – MediSlot HMS'])

@section('portal_subtitle', 'Laboratory')

@section('sidebar_nav')
<a href="{{ route('lab.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('lab.requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Test Requests</span>
</a>
<a href="{{ route('lab.samples') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Sample Processing</span>
</a>
<a href="{{ route('lab.reports') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Results & Reports</span>
</a>
<a href="{{ route('lab.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('lab.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Results & Diagnostic Reports</h1>
            <p class="text-xs text-slate-500 mt-1">Authorized test results delivered back to referring consultants.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Req ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Test Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Result / Value</th>
                        <th class="px-6 py-3">Interpretation</th>
                        <th class="px-6 py-3">Authorized On</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $rep)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $rep->request_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $rep->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $rep->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $rep->test_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $rep->category }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $rep->result?->result_value }} {{ $rep->result?->unit }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800">
                                    {{ $rep->result?->interpretation ?? 'Validated' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $rep->result?->finalized_at?->format('M d, Y · H:i') ?? $rep->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('lab.reports.show', $rep) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold shadow-sm transition">
                                    View Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No submitted reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
