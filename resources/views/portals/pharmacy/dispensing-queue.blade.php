@extends('layouts.portal', ['title' => 'Dispensing Queue – MediSlot HMS'])

@section('portal_subtitle', 'Pharmacy')

@section('sidebar_nav')
<a href="{{ route('pharmacy.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('pharmacy.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('pharmacy.dispensing') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dispensing Queue</h1>
            <p class="text-xs text-slate-500 mt-1">Real-time prescription staging and drug fulfillment queue.</p>
        </div>
    </div>

    <!-- Staging Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 text-xs font-semibold pb-1">
        <a href="{{ route('pharmacy.dispensing', ['tab' => 'ready']) }}" class="py-2.5 px-4 rounded-xl transition {{ $tab === 'ready' ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Ready to Dispense ({{ $readyCount }})
        </a>
        <a href="{{ route('pharmacy.dispensing', ['tab' => 'processing']) }}" class="py-2.5 px-4 rounded-xl transition {{ $tab === 'processing' ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Processing & Counting ({{ $processingCount }})
        </a>
        <a href="{{ route('pharmacy.dispensing', ['tab' => 'partially_dispensed']) }}" class="py-2.5 px-4 rounded-xl transition {{ $tab === 'partially_dispensed' ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Partially Dispensed ({{ $partiallyCount }})
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Rx ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Ordered At</th>
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
                                <div class="font-medium text-slate-800">{{ $rx->items->count() }} item(s)</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">
                                    {{ $rx->items->pluck('medication_name')->implode(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $rx->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $rx->created_at->format('M d, H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="inline-flex items-center px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                    Fulfill
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No prescriptions in this queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
