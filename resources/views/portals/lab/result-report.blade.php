@extends('layouts.portal', ['title' => 'Report · ' . $laboratoryRequest->request_number . ' – MediSlot Laboratory'])

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
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                Report · {{ $laboratoryRequest->request_number }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $laboratoryRequest->test_name }} · {{ $laboratoryRequest->patient?->full_name }} ({{ $laboratoryRequest->patient?->patient_id }}) · Ordered by {{ $laboratoryRequest->requester?->name }}
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
            <span class="size-2 rounded-full bg-emerald-500"></span>
            Submitted Report
        </span>
    </div>

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.8fr)]">
        <!-- Result Report Card -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-semibold text-slate-900">Submitted laboratory result</h2>
                <p class="text-xs text-slate-400 mt-0.5">This report is read-only after final submission.</p>
            </div>

            <dl class="divide-y divide-slate-100 text-sm">
                <div class="grid gap-2 py-3 sm:grid-cols-[140px_1fr]">
                    <dt class="font-medium text-slate-800">Result Value</dt>
                    <dd class="text-slate-900 font-bold text-base">{{ $laboratoryRequest->result?->result_value ?? '—' }} {{ $laboratoryRequest->result?->unit }}</dd>
                </div>
                <div class="grid gap-2 py-3 sm:grid-cols-[140px_1fr]">
                    <dt class="font-medium text-slate-800">Reference Range</dt>
                    <dd class="text-slate-600 font-mono text-xs">{{ $laboratoryRequest->result?->reference_range ?? 'Not specified' }}</dd>
                </div>
                <div class="grid gap-2 py-3 sm:grid-cols-[140px_1fr]">
                    <dt class="font-medium text-slate-800">Interpretation</dt>
                    <dd class="text-slate-700">{{ $laboratoryRequest->result?->interpretation ?? 'Normal / No remarks' }}</dd>
                </div>
                <div class="grid gap-2 py-3 sm:grid-cols-[140px_1fr]">
                    <dt class="font-medium text-slate-800">Laboratory Notes</dt>
                    <dd class="text-slate-700">{{ $laboratoryRequest->result?->lab_notes ?? 'None' }}</dd>
                </div>
                <div class="grid gap-2 py-3 sm:grid-cols-[140px_1fr]">
                    <dt class="font-medium text-slate-800">Certified By</dt>
                    <dd class="text-slate-700">{{ $laboratoryRequest->result?->enteredBy?->name ?? auth()->user()->name }} · {{ optional($laboratoryRequest->result?->finalized_at)->format('d M Y, H:i') ?? $laboratoryRequest->updated_at->format('d M Y, H:i') }}</dd>
                </div>
            </dl>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Hospital Laboratory Services · Official Record</span>
                <button type="button" onclick="window.print()" class="text-teal-700 hover:underline font-medium">Print Report</button>
            </div>
        </div>

        <!-- Right Side: Amendments & Navigation -->
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                <h3 class="font-semibold text-slate-900 border-b border-slate-100 pb-2">Record an amendment</h3>
                <p class="text-xs text-slate-500">The original result remains recorded for audit compliance. Enter amendment reasons below.</p>

                <form method="POST" action="{{ route('lab.requests.amend', $laboratoryRequest) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Reason for amendment</label>
                        <textarea name="amendment_reason" required rows="3" placeholder="e.g. Specimen re-tested, dilution adjusted..." class="w-full rounded-lg border border-slate-200 p-2.5 text-xs focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Corrected Value (optional)</label>
                        <input type="text" name="corrected_value" placeholder="New value if applicable" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                    </div>
                    <button type="submit" class="w-full inline-flex min-h-10 items-center justify-center rounded-lg bg-teal-700 text-xs font-semibold text-white shadow-sm hover:bg-teal-800">
                        Submit amendment record
                    </button>
                </form>

                <div class="pt-3 border-t border-slate-100 text-center">
                    <a href="{{ route('lab.reports') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                        Back to all results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
