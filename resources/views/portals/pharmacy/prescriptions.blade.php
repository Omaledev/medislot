@extends('layouts.portal', ['title' => 'Prescriptions Queue – MediSlot HMS'])

@section('portal_subtitle', 'Pharmacy')

@section('sidebar_nav')
<a href="{{ route('pharmacy.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('pharmacy.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('pharmacy.dispensing') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing Queue</span>
</a>
<a href="{{ route('pharmacy.dispensing-history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing History</span>
</a>
<a href="{{ route('pharmacy.inventory') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Medication Inventory</span>
</a>
<a href="{{ route('pharmacy.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('pharmacy.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Prescription Orders</h1>
            <p class="text-xs text-slate-500 mt-1">Full registry of clinician orders received by the hospital pharmacy.</p>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex items-center space-x-2 overflow-x-auto text-xs font-medium">
        <a href="{{ route('pharmacy.prescriptions') }}" class="px-3.5 py-1.5 rounded-xl border {{ !request('status') ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">All</a>
        <a href="{{ route('pharmacy.prescriptions', ['status' => 'new']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'new' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">New</a>
        <a href="{{ route('pharmacy.prescriptions', ['status' => 'accepted']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'accepted' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Accepted</a>
        <a href="{{ route('pharmacy.prescriptions', ['status' => 'processing']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'processing' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Processing</a>
        <a href="{{ route('pharmacy.prescriptions', ['status' => 'clarification_needed']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'clarification_needed' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Clarification</a>
        <a href="{{ route('pharmacy.prescriptions', ['status' => 'dispensed']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'dispensed' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Dispensed</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Rx ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Clinician</th>
                        <th class="px-6 py-3">Items Prescribed</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $rx->rx_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $rx->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $rx->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">Dr. {{ $rx->prescriber?->name }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $rx->items->count() }} item(s)</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">
                                    {{ $rx->items->pluck('medication_name')->implode(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $rx->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700' : ($rx->status === 'clarification_needed' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $rx->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $rx->created_at->format('M d, H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No prescriptions match the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
