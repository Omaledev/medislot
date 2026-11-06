@extends('layouts.superadmin', ['title' => 'EHR & External Integrations Monitor'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                EHR & External Hospital Integrations
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Monitor inbound ADT webhooks, HL7 event streams, and active API keys issued to hospital software.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs">
                Active Tenants with Keys: <strong class="text-teal-700">{{ $activeHospitalsWithKeys }}</strong>
            </span>
            <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs">
                Total Keys: <strong class="text-teal-700">{{ $apiKeys->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- Webhook Endpoints Reference Card -->
    <div class="rounded-2xl border border-teal-200 bg-[#effbf9]/40 p-5 shadow-xs">
        <h3 class="text-xs font-bold text-teal-900 uppercase tracking-wider mb-2">
            Live MediSlot Connect Webhook Receivers
        </h3>
        <p class="text-xs text-slate-600 mb-3">
            External hospitals point their Electronic Health Record systems (Epic, Cerner, Bahmni, OpenMRS) to these endpoints:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-mono text-[11px]">
            <div class="p-2.5 rounded-lg bg-white border border-teal-100 text-slate-800">
                <span class="text-emerald-700 font-bold">POST</span> /api/v1/integrations/adt/admit
            </div>
            <div class="p-2.5 rounded-lg bg-white border border-teal-100 text-slate-800">
                <span class="text-emerald-700 font-bold">POST</span> /api/v1/integrations/adt/discharge
            </div>
            <div class="p-2.5 rounded-lg bg-white border border-teal-100 text-slate-800">
                <span class="text-emerald-700 font-bold">POST</span> /api/v1/integrations/beds/sync
            </div>
        </div>
    </div>

    <!-- Active API Keys Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Issued Hospital API Keys
            </h2>
            <span class="text-xs text-slate-400">Headers: <code>X-Hospital-Key</code> or Bearer</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 font-semibold text-slate-600 uppercase tracking-wider text-[11px]">
                        <th class="p-4 pl-6">Hospital</th>
                        <th class="p-4">Key Name</th>
                        <th class="p-4">Prefix</th>
                        <th class="p-4">Last Used</th>
                        <th class="p-4">Created Date</th>
                        <th class="p-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($apiKeys as $k)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4 pl-6 font-bold text-slate-900">
                                <a href="{{ route('superadmin.hospitals.show', $k->hospital_id) }}" class="hover:text-teal-700">
                                    {{ $k->hospital?->name ?? 'Unknown Hospital' }}
                                </a>
                            </td>
                            <td class="p-4 font-medium text-slate-800">
                                {{ $k->name }}
                            </td>
                            <td class="p-4 font-mono text-[11px] text-teal-800">
                                <span class="bg-teal-50 px-2 py-0.5 rounded border border-teal-100">{{ $k->prefix }}...</span>
                            </td>
                            <td class="p-4 text-slate-500">
                                {{ $k->last_used_at?->diffForHumans() ?? 'Never used yet' }}
                            </td>
                            <td class="p-4 text-slate-400">
                                {{ $k->created_at?->format('M d, Y') }}
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <a href="{{ route('superadmin.hospitals.show', $k->hospital_id) }}" class="text-teal-700 hover:text-teal-800 font-semibold hover:underline">
                                    Inspect Tenant &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                No API keys generated yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($apiKeys->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $apiKeys->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Integration Activity -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs p-6 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
            Recent EHR Integration Events
        </h2>

        <div class="divide-y divide-slate-100">
            @forelse($integrationLogs as $log)
                <div class="py-3 flex items-start justify-between text-xs gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900">{{ $log->hospital?->name }}</span>
                            <span class="font-mono text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">{{ $log->action }}</span>
                        </div>
                        <p class="text-slate-600">{{ $log->description }}</p>
                    </div>
                    <span class="text-[10px] text-slate-400 shrink-0">{{ $log->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4">No recent EHR webhook logs recorded.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
