@extends('layouts.portal', ['title' => 'Add Medication – MediSlot HMS'])

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
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('pharmacy.inventory') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Inventory
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h1 class="text-xl font-bold tracking-tight text-slate-900 mb-1">Add New Medication to Inventory</h1>
        <p class="text-xs text-slate-500 mb-6">Register a new drug into the hospital formulary with initial stock count and reorder parameters.</p>

        <form action="{{ route('pharmacy.inventory.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Brand / Commercial Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Augmentin" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Generic Name</label>
                    <input type="text" name="generic_name" placeholder="e.g. Amoxicillin + Clavulanic Acid" value="{{ old('generic_name') }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Strength</label>
                    <input type="text" name="strength" placeholder="e.g. 625mg or 250mg/5ml" value="{{ old('strength') }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Dosage Form *</label>
                    <select name="form" required class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Syrup / Suspension">Syrup / Suspension</option>
                        <option value="Injection / Infusion">Injection / Infusion</option>
                        <option value="Eye / Ear Drop">Eye / Ear Drop</option>
                        <option value="Inhaler">Inhaler</option>
                        <option value="Ointment / Cream">Ointment / Cream</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit of Measurement *</label>
                    <select name="unit_of_measurement" required class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                        <option value="tablets">tablets</option>
                        <option value="capsules">capsules</option>
                        <option value="bottles">bottles</option>
                        <option value="vials">vials</option>
                        <option value="ampoules">ampoules</option>
                        <option value="tubes">tubes</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Initial Quantity in Stock *</label>
                    <input type="number" name="quantity" required min="0" value="{{ old('quantity', 100) }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reorder Alert Threshold *</label>
                    <input type="number" name="reorder_level" required min="0" value="{{ old('reorder_level', 20) }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Batch / Lot Number</label>
                    <input type="text" name="batch_number" placeholder="e.g. B240801" value="{{ old('batch_number') }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('pharmacy.inventory') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-bold shadow-sm transition">
                    Save to Formulary
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
