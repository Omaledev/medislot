@extends('layouts.portal', ['title' => 'Dispense ' . $prescription->rx_number . ' – MediSlot Pharmacy'])

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
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                Dispense {{ $prescription->rx_number }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $prescription->patient?->full_name }} · {{ $prescription->patient?->patient_id }} · {{ $prescription->prescriber?->name ?? 'Consultant' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pharmacy.prescriptions.show', $prescription) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Prescription Details
            </a>
            <button form="dispensing-form" type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-teal-700 px-5 text-xs font-semibold text-white shadow-sm hover:bg-teal-800">
                Complete dispensing
            </button>
        </div>
    </div>

    <form id="dispensing-form" method="POST" action="{{ route('pharmacy.dispensing.process', $prescription) }}" class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(280px,0.9fr)]">
        @csrf
        <!-- Left: Prescribed Medications Dispensing Inputs -->
        <div class="space-y-4">
            @foreach($prescription->items as $item)
                @php
                    $med = $item->medication;
                    $remaining = $item->quantity_prescribed - ($item->quantity_dispensed ?? 0);
                    $stock = $med?->quantity ?? 0;
                @endphp
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2">
                        <h2 class="font-semibold text-slate-900 text-sm">
                            {{ $med?->name ?? $item->drug_name }} {{ $med?->strength }} · {{ $med?->form ?? 'Unit' }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-medium {{ $stock > 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            <span class="size-2 rounded-full {{ $stock > 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $stock > 0 ? "In stock: {$stock}" : 'Out of stock' }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500">
                        Prescribed: <strong class="text-slate-800">{{ $remaining }}</strong> · Instructions: {{ $item->instructions ?? 'Take as directed' }}
                    </p>

                    <div class="grid gap-3 sm:grid-cols-2 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity to dispense</label>
                            <input type="number" min="0" max="{{ min($remaining, $stock) }}" name="quantities[{{ $item->id }}]" value="{{ min($remaining, $stock) }}" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dispensing notes / reason if partial</label>
                            <input type="text" name="notes[{{ $item->id }}]" placeholder="e.g. Batch verified" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Side: Guidelines info -->
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-teal-50/50 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-900 text-sm mb-2">When you record dispensing</h3>
                <p class="text-xs leading-relaxed text-slate-600">
                    Stock is automatically deducted from inventory. The prescription status updates to dispensed or partially dispensed, and the consultant is notified in real time.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-200 text-xs text-slate-500">
                    Dispensing pharmacist: <strong class="text-slate-700">{{ auth()->user()->name }}</strong>
                </div>
            </div>

            <!-- Clarification prompt -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-slate-900 text-sm mb-2">Need clarification from doctor?</h3>
                <p class="text-xs text-slate-500 mb-3">If the dosage or medicine requires confirmation before dispensing.</p>
                <a href="{{ route('pharmacy.prescriptions.show', $prescription) }}" class="w-full inline-flex min-h-10 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                    Request clarification
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
