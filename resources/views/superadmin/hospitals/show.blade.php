@extends('layouts.superadmin', ['title' => $hospital->name . ' – Facility Inspection'])

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('superadmin.hospitals.index') }}" class="hover:text-teal-700 hover:underline">Hospitals</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $hospital->name }}</span>
    </div>

    <!-- Facility Header & Status Changer -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $hospital->name }}
                </h1>
                @if($hospital->status === 'active')
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">Active</span>
                @elseif($hospital->status === 'suspended')
                    <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200">Suspended</span>
                @else
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">{{ ucfirst($hospital->status) }}</span>
                @endif
            </div>
            <p class="text-xs text-slate-500">
                Registered {{ $hospital->created_at?->format('F d, Y · h:i A') }} ({{ $hospital->created_at?->diffForHumans() }})
            </p>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>Handle: <strong class="text-slate-700 font-mono">{{ $hospital->slug }}</strong></span>
                @if($hospital->custom_domain)
                    <span>·</span>
                    <span class="text-teal-700 font-medium">Domain: <code>{{ $hospital->custom_domain }}</code></span>
                @endif
            </div>
        </div>

        <!-- Quick Status Change Form -->
        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 shrink-0">
            <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Hospital Status</span>
            <form action="{{ route('superadmin.hospitals.status', $hospital->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="status" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-teal-700">
                    <option value="active" {{ $hospital->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending_verification" {{ $hospital->status === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="inactive" {{ $hospital->status === 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                </select>
                <button type="submit" class="rounded-xl bg-teal-800 hover:bg-teal-900 px-4 py-2 text-xs font-semibold text-white transition">
                    Update Status
                </button>
            </form>
        </div>
    </div>

    <!-- Metric Stats Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
            <span class="block text-xs font-medium text-slate-400">Total Beds</span>
            <span class="block text-2xl font-bold text-slate-900 mt-1">{{ $hospitalBedsTotal }}</span>
            <span class="block text-[11px] text-teal-700 mt-0.5">{{ $hospitalBedsAvail }} available free</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
            <span class="block text-xs font-medium text-slate-400">Admissions</span>
            <span class="block text-2xl font-bold text-slate-900 mt-1">{{ $hospitalAdmissionsCount }}</span>
            <span class="block text-[11px] text-slate-400 mt-0.5">Recorded patient stays</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
            <span class="block text-xs font-medium text-slate-400">Registered Patients</span>
            <span class="block text-2xl font-bold text-slate-900 mt-1">{{ $hospitalPatientsCount }}</span>
            <span class="block text-[11px] text-slate-400 mt-0.5">On facility record</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
            <span class="block text-xs font-medium text-slate-400">Staff Accounts</span>
            <span class="block text-2xl font-bold text-slate-900 mt-1">{{ $hospital->users->count() }}</span>
            <span class="block text-[11px] text-slate-400 mt-0.5">{{ $hospital->departments->count() }} departments</span>
        </div>
    </div>

    <!-- 2-Column Details Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Facility Information & Staff Roster -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Facility Information Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Facility Metadata</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="font-medium text-slate-400">Email Address</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $hospital->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-400">Direct Phone</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $hospital->phone }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-400">Registration Number</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $hospital->registration_number ?? 'Not reported' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-400">Facility Type</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $hospital->type }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-slate-400">Physical Address</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $hospital->address }}, {{ $hospital->lga }}, {{ $hospital->state }}, {{ $hospital->country }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Staff Accounts -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                    <span>Staff Users Roster</span>
                    <span class="text-xs text-slate-400 font-normal">{{ $hospital->users->count() }} accounts</span>
                </h3>
                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    @forelse($hospital->users as $u)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="grid size-7 place-items-center rounded-lg bg-teal-50 text-teal-800 font-bold text-[10px]">
                                    {{ $u->initials }}
                                </span>
                                <div>
                                    <strong class="block text-slate-900">{{ $u->name }}</strong>
                                    <span class="text-slate-400">{{ $u->email }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                    {{ $u->role }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $u->department_username }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4">No staff members created yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Ward & Bed Breakdown -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ward & Bed Allocation</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($hospital->bedCategories as $bed)
                        <div class="py-2.5 flex items-center justify-between">
                            <strong class="text-slate-800">{{ $bed->name }}</strong>
                            <div class="flex items-center gap-4">
                                <span><strong class="text-teal-700">{{ $bed->available_beds }}</strong> free / {{ $bed->total_beds }} total</span>
                                <span class="inline-block rounded-full px-2 py-0.5 text-[10px] font-bold {{ $bed->status === 'available' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($bed->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3">No bed categories set up yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: EHR API Keys & Recent Hospital Activity -->
        <div class="lg:col-span-5 space-y-6">
            <!-- External EHR API Keys -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                    <span>EHR Integration Keys</span>
                    <span class="text-xs text-slate-400 font-normal">{{ $hospital->apiKeys->count() }} keys</span>
                </h3>

                <div class="space-y-3">
                    @forelse($hospital->apiKeys as $key)
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <strong class="text-slate-800">{{ $key->name }}</strong>
                                <span class="rounded bg-teal-100/60 px-1.5 py-0.2 text-[10px] font-mono text-teal-800">{{ $key->prefix }}...</span>
                            </div>
                            <p class="text-[10px] text-slate-400">
                                Last used: {{ $key->last_used_at?->diffForHumans() ?? 'Never' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No EHR API keys generated by this hospital yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity for this Hospital -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Facility Audit Trail</h3>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse($recentLogs as $log)
                        <div class="py-3 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">{{ $log->action }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 leading-snug">{{ $log->description }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4">No activity logged for this facility yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
