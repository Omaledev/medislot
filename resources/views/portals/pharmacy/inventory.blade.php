@extends('layouts.portal', ['title' => 'Medication Inventory – MediSlot HMS'])

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
<a href="{{ route('pharmacy.dispensing-history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing History</span>
</a>
<a href="{{ route('pharmacy.inventory') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Medication Inventory</span>
</a>
<a href="{{ route('pharmacy.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('pharmacy.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('topbar_search')
<form action="{{ route('pharmacy.inventory') }}" method="GET" class="relative hidden sm:block">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search medications, generic or code" class="w-64 pl-3.5 pr-8 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
    <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Medication Inventory</h1>
            <p class="text-xs text-slate-500 mt-1">Stock tracking, batch management, and automated reorder alerts.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pharmacy.inventory.create') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                + Add Medication
            </a>
        </div>
    </div>

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Total Inventory SKUs</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $totalCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Formulary medicines</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Low Stock Alerts</div>
            <div class="text-3xl font-bold text-amber-600 mt-2">{{ $lowStockCount }}</div>
            <div class="text-xs text-slate-400 mt-1">At or below reorder level</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Out of Stock</div>
            <div class="text-3xl font-bold text-red-600 mt-2">{{ $outOfStockCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Zero units remaining</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Expiring Soon (90 Days)</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $expiringCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Requires rotation or return</div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex items-center space-x-2 overflow-x-auto text-xs font-medium">
        <a href="{{ route('pharmacy.inventory', ['filter' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'all' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">All ({{ $totalCount }})</a>
        <a href="{{ route('pharmacy.inventory', ['filter' => 'low_stock']) }}" class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'low_stock' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Low Stock ({{ $lowStockCount }})</a>
        <a href="{{ route('pharmacy.inventory', ['filter' => 'out_of_stock']) }}" class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'out_of_stock' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Out of Stock ({{ $outOfStockCount }})</a>
        <a href="{{ route('pharmacy.inventory', ['filter' => 'expiring_soon']) }}" class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'expiring_soon' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Expiring Soon ({{ $expiringCount }})</a>
        <a href="{{ route('pharmacy.inventory', ['filter' => 'expired']) }}" class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'expired' ? 'bg-brand-700 text-white border-brand-700' : 'bg-white text-slate-700 border-slate-200' }}">Expired ({{ $expiredCount }})</a>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Medication Name</th>
                        <th class="px-6 py-3">Strength & Form</th>
                        <th class="px-6 py-3">Stock On Hand</th>
                        <th class="px-6 py-3">Reorder Threshold</th>
                        <th class="px-6 py-3">Expiry Date</th>
                        <th class="px-6 py-3">Batch</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($medications as $med)
                        @php
                            $statusColor = match($med->status) {
                                'available' => 'bg-emerald-50 text-emerald-700',
                                'low_stock' => 'bg-amber-50 text-amber-700',
                                'out_of_stock', 'expired' => 'bg-red-50 text-red-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $med->code }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-900">{{ $med->name }}</span>
                                @if($med->generic_name)
                                    <span class="block text-[10px] text-slate-400">({{ $med->generic_name }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $med->strength }} {{ $med->form }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $med->quantity }} <span class="text-xs font-normal text-slate-400">{{ $med->unit_of_measurement }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $med->reorder_level }} {{ $med->unit_of_measurement }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $med->expiry_date?->format('M Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono text-slate-600">{{ $med->batch_number ?? 'BN-DEFAULT' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $med->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('pharmacy.inventory.show', $med) }}" class="inline-flex items-center px-3 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No medications in this inventory view.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
