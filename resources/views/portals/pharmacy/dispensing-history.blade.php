@extends('layouts.portal', ['title' => 'Dispensing History – MediSlot HMS'])

@section('portal_subtitle', 'Pharmacy')

@section('sidebar_nav')
<a href="{{ route('pharmacy.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('pharmacy.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('pharmacy.dispensing') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing Queue</span>
</a>
<a href="{{ route('pharmacy.dispensing-history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dispensing History</h1>
            <p class="text-xs text-slate-500 mt-1">Audit log of dispensed prescriptions and medications handed to patients.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Rx ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Medications Dispensed</th>
                        <th class="px-6 py-3">Prescribing Doctor</th>
                        <th class="px-6 py-3">Dispensed By</th>
                        <th class="px-6 py-3">Dispensed At</th>
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
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    @foreach($rx->items as $it)
                                        <div class="font-medium text-slate-800">{{ $it->medication_name }} ({{ $it->quantity_dispensed }} pcs)</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">Dr. {{ $rx->prescriber?->name }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $rx->dispensedByUser?->name ?? 'Pharmacist' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $rx->dispensed_at?->format('M d, Y · H:i') ?? $rx->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold shadow-sm transition">
                                    Receipt / Record
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No dispensing history logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($prescriptions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
