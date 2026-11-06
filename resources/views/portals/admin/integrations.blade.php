@extends('layouts.portal', ['title' => 'EHR & Bed Allocations Integration – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.departments') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Departments</span>
</a>
<a href="{{ route('admin.staff') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Staff</span>
</a>
<a href="{{ route('admin.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Beds</span>
</a>
<a href="{{ route('admin.admissions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Admissions</span>
</a>
<a href="{{ route('admin.services') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Services</span>
</a>
<a href="{{ route('admin.integrations') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>EHR Integrations</span>
</a>
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header Banner -->
    <div class="bg-brand-700 text-white rounded-2xl p-6 sm:p-8 shadow-sm">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-800 text-brand-100 border border-brand-600 mb-3">
                <svg class="w-3.5 h-3.5 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                MediSlot Connect · Headless Allocation Engine
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">EHR & External Hospital Integration</h1>
            <p class="mt-2 text-sm sm:text-base text-brand-100 leading-relaxed">
                Connect your existing hospital software (Epic, Cerner, OpenMRS, Bahmni, or custom in-house EHR). When your nurses admit or discharge patients in your system, MediSlot synchronizes bed counts automatically with zero manual double-entry.
            </p>
        </div>
    </div>

    @if (session('plainTextApiKey'))
        <div class="p-5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-950 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-emerald-200/80 rounded-lg text-emerald-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-emerald-900 text-base">New API Key Generated!</h3>
                    <p class="text-xs sm:text-sm text-emerald-800 mt-1">Please copy your API key now. For your security, this key will <strong>never be shown again</strong>.</p>
                    <div class="mt-3 flex items-center gap-2">
                        <code class="px-3 py-2 bg-white rounded-lg border border-emerald-300 text-sm font-mono font-bold text-emerald-900 select-all flex-1">{{ session('plainTextApiKey') }}</code>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- API Keys Management -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Hospital Integration Keys</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Use these secret keys to authenticate your hospital EHR webhooks.</p>
                    </div>
                </div>

                <!-- Generate Key Form -->
                <form method="POST" action="{{ route('admin.integrations.keys.store') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="name" required placeholder="e.g. On-Premise EHR Webhook, OpenMRS Bridge" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-sm font-semibold transition shrink-0">
                        Generate New Key
                    </button>
                </form>

                <!-- Keys List -->
                <div class="mt-6 divide-y divide-slate-100">
                    @forelse ($apiKeys as $key)
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-sm text-slate-900">{{ $key->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-100 text-slate-700">{{ $key->key_prefix }}...</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                                    <span>Created: {{ $key->created_at->format('M d, Y') }}</span>
                                    <span>·</span>
                                    <span>Last Used: {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Never' }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.integrations.keys.revoke', $key) }}" onsubmit="return confirm('Revoke this API key? External systems using it will be disconnected.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                    Revoke
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 text-sm">
                            No API keys generated yet. Click "Generate New Key" above to integrate your system.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Ward Code Mappings -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">EHR Ward Code Mapping</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Map your internal hospital ward codes (e.g. ICU_01, WARD_3B) to MediSlot categories.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.integrations.wards.update') }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                                <tr>
                                    <th class="py-2.5 px-3">MediSlot Bed Category</th>
                                    <th class="py-2.5 px-3">Total / Available</th>
                                    <th class="py-2.5 px-3">Your EHR Ward Code</th>
                                    <th class="py-2.5 px-3">Last Synced</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($beds as $bed)
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">
                                            {{ $bed->name }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-600">
                                            {{ $bed->total_beds }} total · <span class="font-bold text-emerald-600">{{ $bed->available_beds }} avail</span>
                                        </td>
                                        <td class="py-2 px-3">
                                            <input type="text" name="mappings[{{ $bed->id }}]" value="{{ old('mappings.'.$bed->id, $bed->external_code) }}" placeholder="e.g. {{ strtoupper(str_replace(' ', '_', $bed->name)) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-mono w-48 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none">
                                        </td>
                                        <td class="py-3 px-3 text-xs text-slate-500">
                                            {{ $bed->last_synced_at ? $bed->last_synced_at->diffForHumans() : 'Not synced' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-sm font-semibold transition">
                            Save Ward Code Mappings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documentation & Quick Reference -->
        <div class="space-y-6">
            <div class="bg-slate-900 text-slate-200 rounded-xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 text-teal-400 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    API Quick Reference
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-slate-400 font-semibold mb-1">1. Smart Bed Allocation (Find best bed)</div>
                        <pre class="bg-slate-950 p-2.5 rounded-lg overflow-x-auto text-teal-300 font-mono">POST /api/v1/integrations/beds/allocate
Header: X-Hospital-Key: YOUR_KEY
{
  "acuity": "critical",
  "gender": "female"
}</pre>
                    </div>

                    <div>
                        <div class="text-slate-400 font-semibold mb-1">2. Ingest Admission (Patient admitted)</div>
                        <pre class="bg-slate-950 p-2.5 rounded-lg overflow-x-auto text-emerald-300 font-mono">POST /api/v1/integrations/adt/admit
Header: X-Hospital-Key: YOUR_KEY
{
  "ward_code": "ICU_01",
  "external_admission_id": "ADM-904",
  "patient_name": "Ada Lovelace"
}</pre>
                    </div>

                    <div>
                        <div class="text-slate-400 font-semibold mb-1">3. Ingest Discharge (Bed vacated)</div>
                        <pre class="bg-slate-950 p-2.5 rounded-lg overflow-x-auto text-amber-300 font-mono">POST /api/v1/integrations/adt/discharge
Header: X-Hospital-Key: YOUR_KEY
{
  "external_admission_id": "ADM-904"
}</pre>
                    </div>

                    <div>
                        <div class="text-slate-400 font-semibold mb-1">4. Periodic Snapshot Sync (Legacy/Cron)</div>
                        <pre class="bg-slate-950 p-2.5 rounded-lg overflow-x-auto text-sky-300 font-mono">PUT /api/v1/integrations/beds/sync
Header: X-Hospital-Key: YOUR_KEY
{
  "wards": [
    {"ward_code": "ICU_01", "total_beds": 10, "occupied_beds": 8}
  ]
}</pre>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-800">
                    <a href="{{ url('/api/v1/documentation') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-teal-300 hover:text-teal-200 font-semibold">
                        View Full Interactive Swagger Documentation &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
