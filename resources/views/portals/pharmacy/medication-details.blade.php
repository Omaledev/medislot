@extends('layouts.portal', ['title' => $medication->name . ' – Medication Details – MediSlot Pharmacy'])

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

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                {{ $medication->name }} {{ $medication->strength }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $medication->code }} · {{ $medication->form }} · Formulary Medicine
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-semibold {{ $medication->quantity <= 0 ? 'text-red-700' : ($medication->quantity <= $medication->reorder_level ? 'text-amber-700' : 'text-emerald-700') }}">
            <span class="size-2 rounded-full {{ $medication->quantity <= 0 ? 'bg-red-500' : ($medication->quantity <= $medication->reorder_level ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
            {{ ucfirst(str_replace('_', ' ', $medication->status)) }}
        </span>
    </div>

    <div class="grid items-start gap-6 xl:grid-cols-2">
        <!-- Left: Details & Movements -->
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Medication details</h2>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="grid gap-2 py-2.5 sm:grid-cols-[140px_1fr]">
                        <dt class="font-medium text-slate-500">Generic name</dt>
                        <dd class="text-slate-900 font-medium">{{ $medication->generic_name ?? '—' }}</dd>
                    </div>
                    <div class="grid gap-2 py-2.5 sm:grid-cols-[140px_1fr]">
                        <dt class="font-medium text-slate-500">Available quantity</dt>
                        <dd class="text-slate-900 font-bold text-base">{{ $medication->quantity }} {{ $medication->unit_of_measurement }}</dd>
                    </div>
                    <div class="grid gap-2 py-2.5 sm:grid-cols-[140px_1fr]">
                        <dt class="font-medium text-slate-500">Reorder level</dt>
                        <dd class="text-slate-900 font-medium">{{ $medication->reorder_level }}</dd>
                    </div>
                    <div class="grid gap-2 py-2.5 sm:grid-cols-[140px_1fr]">
                        <dt class="font-medium text-slate-500">Expiry date</dt>
                        <dd class="text-slate-900 font-medium">{{ $medication->expiry_date ? $medication->expiry_date->format('M Y') : 'Not recorded' }}</dd>
                    </div>
                    <div class="grid gap-2 py-2.5 sm:grid-cols-[140px_1fr]">
                        <dt class="font-medium text-slate-500">Batch number</dt>
                        <dd class="text-slate-900 font-medium">{{ $medication->batch_number ?? 'Not recorded' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Recent activity</h2>
                <div class="text-xs text-slate-500 space-y-2">
                    <div class="flex justify-between py-1.5 border-b border-slate-50">
                        <span class="font-medium text-slate-800">{{ $medication->updated_at->format('d M H:i') }}</span>
                        <span class="text-slate-600">Current stock: {{ $medication->quantity }}</span>
                        <span class="text-slate-500">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Receive Stock & Stock Adjustment Forms -->
        <div class="space-y-5">
            <!-- Record Stock Received -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Record stock received</h2>
                <form method="POST" action="{{ route('pharmacy.inventory.receive-stock', $medication) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity received</label>
                        <input type="number" min="1" name="quantity_received" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Batch number (optional)</label>
                        <input type="text" name="batch_number" value="{{ $medication->batch_number }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry date (optional)</label>
                        <input type="date" name="expiry_date" value="{{ optional($medication->expiry_date)->format('Y-m-d') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                    </div>
                    <button type="submit" class="w-full inline-flex min-h-10 items-center justify-center rounded-lg bg-teal-700 text-xs font-semibold text-white shadow-sm hover:bg-teal-800 transition">
                        Record stock received
                    </button>
                </form>
            </div>

            <!-- Adjust Stock -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Adjust stock count</h2>
                <form method="POST" action="{{ route('pharmacy.inventory.adjust-stock', $medication) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Change amount (use minus to reduce, e.g. -5)</label>
                        <input type="number" name="adjustment_amount" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Reason (required)</label>
                        <select name="reason" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            <option value="Damaged">Damaged</option>
                            <option value="Expired">Expired</option>
                            <option value="Stock count correction">Stock count correction</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Save adjustment
                    </button>
                </form>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('pharmacy.inventory') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    &larr; Back to inventory
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
