@extends('layouts.portal', ['title' => 'Laboratory Result Entry – MediSlot HMS'])

@section('portal_subtitle', 'Laboratory')

@section('sidebar_nav')
<a href="{{ route('lab.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('lab.requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Test Requests</span>
</a>
<a href="{{ route('lab.samples') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Sample Processing</span>
</a>
<a href="{{ route('lab.reports') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<div class="max-w-4xl space-y-6">
    <!-- Back Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lab.requests') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Requests
        </a>

        <!-- Fast Status Advancer -->
        <form action="{{ route('lab.requests.advance', $laboratoryRequest) }}" method="POST">
            @csrf
            <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 text-xs font-semibold transition">
                Advance Specimen Stage &rarr;
            </button>
        </form>
    </div>

    <!-- Request Details Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div>
                <span class="font-mono font-bold text-slate-900 text-lg">{{ $laboratoryRequest->request_number }}</span>
                <span class="text-xs text-slate-400 ml-2">Ordered {{ $laboratoryRequest->created_at->format('M d, Y · H:i') }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $laboratoryRequest->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $laboratoryRequest->priority }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800">
                    {{ ucfirst(str_replace('_', ' ', $laboratoryRequest->status)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Patient Name</span>
                <span class="font-bold text-slate-900">{{ $laboratoryRequest->patient?->full_name }}</span>
                <span class="text-slate-500 block">{{ $laboratoryRequest->patient?->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Gender / Age</span>
                <span class="font-semibold text-slate-800">{{ $laboratoryRequest->patient?->gender }}, {{ $laboratoryRequest->patient?->age ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Requested Investigation</span>
                <span class="font-bold text-slate-900">{{ $laboratoryRequest->test_name }}</span>
                <span class="text-slate-500 block">{{ $laboratoryRequest->category }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Requesting Clinician</span>
                <span class="font-semibold text-slate-800">Dr. {{ $laboratoryRequest->requester?->name }}</span>
            </div>
        </div>

        @if($laboratoryRequest->clinical_reason || $laboratoryRequest->instructions)
            <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                @if($laboratoryRequest->clinical_reason)
                    <p class="text-slate-700"><strong>Clinical Notes:</strong> {{ $laboratoryRequest->clinical_reason }}</p>
                @endif
                @if($laboratoryRequest->instructions)
                    <p class="text-slate-700"><strong>Special Instructions:</strong> {{ $laboratoryRequest->instructions }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Enter Result Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-1">Diagnostic Result Entry</h2>
        <p class="text-xs text-slate-500 mb-6">Record quantified analysis values, reference ranges, and laboratory interpretation.</p>

        <form action="{{ route('lab.requests.results', $laboratoryRequest) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Result Value *</label>
                    <input type="text" name="result_value" required value="{{ old('result_value', $laboratoryRequest->result?->result_value) }}" placeholder="e.g. 13.8 or Positive" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit of Measurement</label>
                    <input type="text" name="unit" value="{{ old('unit', $laboratoryRequest->result?->unit) }}" placeholder="e.g. g/dL, mg/dL, /uL" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reference Range</label>
                    <input type="text" name="reference_range" value="{{ old('reference_range', $laboratoryRequest->result?->reference_range) }}" placeholder="e.g. 12.0 - 15.5 g/dL" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Interpretation</label>
                    <select name="interpretation" class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                        <option value="Normal" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Normal') ? 'selected' : '' }}>Normal</option>
                        <option value="Borderline" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Borderline') ? 'selected' : '' }}>Borderline</option>
                        <option value="Elevated / High" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Elevated / High') ? 'selected' : '' }}>Elevated / High</option>
                        <option value="Low / Decreased" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Low / Decreased') ? 'selected' : '' }}>Low / Decreased</option>
                        <option value="Positive / Reactive" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Positive / Reactive') ? 'selected' : '' }}>Positive / Reactive</option>
                        <option value="Negative / Non-reactive" {{ (old('interpretation', $laboratoryRequest->result?->interpretation) == 'Negative / Non-reactive') ? 'selected' : '' }}>Negative / Non-reactive</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Technician Remarks & Observations</label>
                    <input type="text" name="lab_notes" value="{{ old('lab_notes', $laboratoryRequest->result?->lab_notes) }}" placeholder="Sample was clear, no hemolysis observed" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <button type="submit" name="action_type" value="draft" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl font-bold shadow-sm transition">
                    Save Result Draft
                </button>
                <button type="submit" name="action_type" value="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-bold shadow-sm transition">
                    Authorize & Submit to Doctor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
