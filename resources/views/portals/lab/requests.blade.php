@extends('layouts.portal', ['title' => 'Laboratory Test Requests – MediSlot HMS'])

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

@section('topbar_search')
<form action="{{ route('lab.requests') }}" method="GET" class="relative hidden sm:block">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tests or patients" class="w-64 pl-3.5 pr-8 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
    <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Laboratory Requests</h1>
            <p class="text-xs text-slate-500 mt-1">Full registry of requested tests across all clinical departments.</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('lab.requests') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-3 items-center text-xs">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by patient name, ID, test..." class="px-3 py-2 rounded-xl border border-slate-200 focus:border-brand-700 outline-none flex-1 min-w-[200px]">

        <select name="priority" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:border-brand-700 outline-none">
            <option value="">All Priorities</option>
            <option value="Urgent" {{ request('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="Routine" {{ request('priority') === 'Routine' ? 'selected' : '' }}>Routine</option>
        </select>

        <select name="category" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:border-brand-700 outline-none">
            <option value="">All Categories</option>
            <option value="Hematology" {{ request('category') === 'Hematology' ? 'selected' : '' }}>Hematology</option>
            <option value="Chemical Pathology" {{ request('category') === 'Chemical Pathology' ? 'selected' : '' }}>Chemical Pathology</option>
            <option value="Microbiology" {{ request('category') === 'Microbiology' ? 'selected' : '' }}>Microbiology</option>
            <option value="Parasitology" {{ request('category') === 'Parasitology' ? 'selected' : '' }}>Parasitology</option>
            <option value="Serology" {{ request('category') === 'Serology' ? 'selected' : '' }}>Serology</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl font-semibold shadow-sm hover:bg-slate-900 transition">Filter</button>
        @if(request()->hasAny(['q', 'priority', 'category']))
            <a href="{{ route('lab.requests') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Req ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Investigation</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Result</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $r->request_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $r->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $r->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $r->test_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $r->category }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $r->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $r->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $r->status === 'submitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-teal-50 text-teal-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($r->result)
                                    <span class="font-bold text-slate-900">{{ $r->result->result_value }} {{ $r->result->unit }}</span>
                                @else
                                    <span class="text-slate-400 italic">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('lab.requests.show', $r) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                    {{ $r->result ? 'View / Edit Result' : 'Enter Result' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No test requests found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
