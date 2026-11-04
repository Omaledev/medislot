@extends('layouts.portal', ['title' => 'Laboratory Dashboard – MediSlot HMS'])

@section('portal_subtitle', 'Laboratory')

@section('sidebar_nav')
<a href="{{ route('lab.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('lab.requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Laboratory Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">Diagnostic intake, specimen tracking, and pathology results verification.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('lab.samples') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                Sample Pipeline
            </a>
            <a href="{{ route('lab.requests') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                View All Requests
            </a>
        </div>
    </div>

    <!-- 5 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">New Requests</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $newRequestsCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Pending lab intake</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Awaiting Specimen</div>
            <div class="text-3xl font-bold text-amber-600 mt-2">{{ $awaitingSamplesCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Phlebotomy / collection</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">In Processing</div>
            <div class="text-3xl font-bold text-teal-700 mt-2">{{ $processingCount }}</div>
            <div class="text-xs text-slate-400 mt-1">In analyzers / incubator</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Pending Entry</div>
            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $resultsPendingCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Awaiting technician entry</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Completed Today</div>
            <div class="text-3xl font-bold text-emerald-700 mt-2">{{ $completedTodayCount }}</div>
            <div class="text-xs text-slate-400 mt-1">Validated & released</div>
        </div>
    </div>

    <!-- Recent Urgent & New Requests -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Incoming Test Queue</h2>
            <a href="{{ route('lab.requests') }}" class="text-xs font-semibold text-brand-700 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Req ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Investigation</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Doctor</th>
                        <th class="px-6 py-3">Current Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $req->request_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $req->patient?->full_name }}
                                <div class="text-[11px] text-slate-400 font-normal">{{ $req->patient?->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-800">{{ $req->test_name }}</span>
                                <span class="block text-[10px] text-slate-500">{{ $req->category }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $req->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $req->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">Dr. {{ $req->requester?->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800">
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('lab.requests.show', $req) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                    Process / Enter Result
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No pending test requests in queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
