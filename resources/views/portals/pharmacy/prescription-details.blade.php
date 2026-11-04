@extends('layouts.portal', ['title' => 'Prescription ' . $prescription->rx_number . ' – MediSlot HMS'])

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
<div class="max-w-4xl space-y-6" x-data="{ clarifyModal: false }">
    <!-- Back Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pharmacy.prescriptions') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Prescriptions
        </a>

        <!-- Clarification Trigger -->
        @if($prescription->status !== 'dispensed')
            <button @click="clarifyModal = true" class="px-3.5 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 text-xs font-semibold transition">
                Request Doctor Clarification
            </button>
        @endif
    </div>

    <!-- Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div>
                <span class="font-mono font-bold text-slate-900 text-lg">{{ $prescription->rx_number }}</span>
                <span class="text-xs text-slate-400 ml-2">Prescribed {{ $prescription->created_at->format('M d, Y · H:i') }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $prescription->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700' : ($prescription->status === 'clarification_needed' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                {{ ucfirst(str_replace('_', ' ', $prescription->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Patient</span>
                <span class="font-bold text-slate-900">{{ $prescription->patient?->full_name }}</span>
                <span class="text-slate-500 block">{{ $prescription->patient?->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Demographics</span>
                <span class="font-semibold text-slate-800">{{ $prescription->patient?->gender }}, {{ $prescription->patient?->age ?? 'N/A' }}</span>
                <span class="text-slate-500 block">Phone: {{ $prescription->patient?->phone ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Prescribing Clinician</span>
                <span class="font-bold text-slate-900">Dr. {{ $prescription->prescriber?->name }}</span>
                <span class="text-slate-500 block">{{ $prescription->visit?->department?->name ?? 'Outpatient' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Known Allergies</span>
                <span class="font-bold text-red-600">{{ $prescription->patient?->allergies ?? 'Penicillin (reported)' }}</span>
            </div>
        </div>

        @if($prescription->clarification_note)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                <strong>Clarification Requested:</strong> {{ $prescription->clarification_note }}
            </div>
        @endif
    </div>

    <!-- Prescribed Medications List -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Prescribed Items & Regimen</h2>
            <span class="text-xs text-slate-400">{{ $prescription->items->count() }} item(s)</span>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($prescription->items as $item)
                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-base font-bold text-slate-900">{{ $item->medication_name }}</span>
                            @if($item->strength)
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">{{ $item->strength }}</span>
                            @endif
                        </div>
                        <div class="text-slate-600">
                            <strong>Dosage:</strong> {{ $item->dosage }} ·
                            <strong>Route:</strong> {{ $item->route ?? 'Oral' }} ·
                            <strong>Frequency:</strong> {{ $item->frequency }} ·
                            <strong>Duration:</strong> {{ $item->duration }}
                        </div>
                        @if($item->instructions)
                            <div class="text-slate-500 italic mt-1 bg-slate-50 p-2 rounded-lg">
                                Instructions: {{ $item->instructions }}
                            </div>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-bold text-slate-900">Qty Prescribed: {{ $item->quantity_prescribed }}</div>
                        <div class="text-xs text-slate-400">Qty Dispensed: {{ $item->quantity_dispensed }}</div>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dispensing Workflow Buttons Footer -->
        <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end space-x-3">
            @if($prescription->status === 'new')
                <form action="{{ route('pharmacy.prescriptions.accept', $prescription) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        Accept Prescription
                    </button>
                </form>
            @elseif($prescription->status === 'accepted')
                <form action="{{ route('pharmacy.prescriptions.begin', $prescription) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        Begin Dispensing
                    </button>
                </form>
            @elseif($prescription->status === 'processing')
                <form action="{{ route('pharmacy.prescriptions.dispense', $prescription) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        Complete & Dispense to Patient
                    </button>
                </form>
            @elseif($prescription->status === 'dispensed')
                <div class="text-xs text-emerald-800 font-semibold flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>Dispensed on {{ $prescription->dispensed_at?->format('M d, Y · H:i') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: CLARIFICATION REQUEST -->
    <div x-show="clarifyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="clarifyModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Request Doctor Clarification</h3>
                <button @click="clarifyModal = false" class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>

            <form action="{{ route('pharmacy.prescriptions.clarify', $prescription) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Inquiry / Clinical Flag Note</label>
                    <textarea name="clarification_note" rows="4" required class="w-full rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none" placeholder="e.g. Patient allergy flagged for penicillin derivative. Please confirm alternative antibiotic or confirm override."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="clarifyModal = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold shadow-sm">Submit to Prescriber</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
