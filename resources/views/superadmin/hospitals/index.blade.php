@extends('layouts.superadmin', ['title' => 'Registered Hospitals – MediSlot Super Admin'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Hospital Directory
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Oversee all healthcare facilities onboarded on the MediSlot platform.
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 shadow-xs">
            <span class="size-2 rounded-full bg-emerald-500"></span>
            <span>Total Facilities: {{ $hospitals->total() }}</span>
        </span>
    </div>

    <!-- Search & Filter Controls -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
        <form action="{{ route('superadmin.hospitals.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search by hospital name, state, address, email or registration number..."
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700 focus:ring-1 focus:ring-teal-700"
                />
            </div>

            <div class="flex items-center gap-2">
                <select name="status" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-teal-700">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="rounded-xl bg-teal-800 hover:bg-teal-900 px-5 py-2.5 text-xs font-semibold text-white transition">
                    Filter
                </button>

                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('superadmin.hospitals.index') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Hospitals Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 font-semibold text-slate-600 uppercase tracking-wider text-[11px]">
                        <th class="p-4 pl-6">Hospital</th>
                        <th class="p-4">Location</th>
                        <th class="p-4 text-center">Staff</th>
                        <th class="p-4 text-center">Wards</th>
                        <th class="p-4 text-center">Admissions</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Registered</th>
                        <th class="p-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($hospitals as $h)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4 pl-6">
                                <strong class="block text-sm font-bold text-slate-900">{{ $h->name }}</strong>
                                <span class="text-slate-400 font-mono text-[11px]">{{ $h->slug }}</span>
                                @if($h->custom_domain)
                                    <span class="block text-[11px] text-teal-700">🌐 {{ $h->custom_domain }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-600">
                                <span class="block font-medium text-slate-800">{{ $h->state }}, Nigeria</span>
                                <span class="text-slate-400 truncate max-w-[200px] block">{{ $h->address }}</span>
                            </td>
                            <td class="p-4 text-center font-bold text-slate-800">
                                {{ $h->users_count }}
                            </td>
                            <td class="p-4 text-center font-bold text-slate-800">
                                {{ $h->bed_categories_count }}
                            </td>
                            <td class="p-4 text-center font-bold text-slate-800">
                                {{ $h->admissions_count }}
                            </td>
                            <td class="p-4">
                                @if($h->status === 'active')
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @elseif($h->status === 'suspended')
                                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700 border border-rose-200">
                                        Suspended
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700 border border-amber-200">
                                        {{ ucfirst($h->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 whitespace-nowrap">
                                {{ $h->created_at?->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <a href="{{ route('superadmin.hospitals.show', $h->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-teal-50 hover:text-teal-800 px-3 py-1.5 font-semibold text-slate-700 transition">
                                    <span>Manage</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                No hospitals matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hospitals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $hospitals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
