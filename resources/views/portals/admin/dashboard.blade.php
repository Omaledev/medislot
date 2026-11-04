@extends('layouts.portal', ['title' => 'Admin Dashboard – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">{{ date('l, j F Y') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.activity') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                View activity log
            </a>
            <a href="{{ route('admin.beds') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Manage beds
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Currently admitted</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $currentlyAdmitted }}</div>
            <div class="text-xs text-slate-400 mt-1">of {{ $totalBeds }} total beds</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Available beds</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $availableBeds }}</div>
            <div class="text-xs text-slate-400 mt-1">across {{ $beds->count() }} wards</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Visits today</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ $visitsToday }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $waitingVisits }} waiting now</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Registered patients</div>
            <div class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($registeredPatients) }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $hospital->departments()->count() }} departments · {{ $hospital->staff()->count() }} staff accounts</div>
        </div>
    </div>

    <!-- Main 2-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Bed Availability Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Bed availability</h3>
                <span class="text-xs text-slate-400">Last updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                            <th class="py-2.5">Ward</th>
                            <th class="py-2.5">Total</th>
                            <th class="py-2.5">Occupied</th>
                            <th class="py-2.5">Available</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($beds as $b)
                            <tr>
                                <td class="py-3 font-bold text-slate-800">{{ $b->name }}</td>
                                <td class="py-3 text-slate-600">{{ $b->total_beds }}</td>
                                <td class="py-3 text-slate-600">{{ $b->occupied_beds }}</td>
                                <td class="py-3 font-semibold text-slate-800">{{ $b->available_beds }}</td>
                                <td class="py-3">
                                    @if($b->status === 'available')
                                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Available</span>
                                        </span>
                                    @elseif($b->status === 'limited')
                                        <span class="inline-flex items-center space-x-1.5 text-amber-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Limited</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 text-red-600 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <span>Full</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-400">{{ $b->updated_at->diffForHumans(null, true) }} ago</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Needs Attention & Recent Activity -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Needs Attention -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">Needs attention</h3>

                <div class="space-y-3 pt-1">
                    <div class="flex items-start space-x-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mt-1 flex-shrink-0"></span>
                        <div>
                            <span class="font-bold text-slate-800">Not updated</span>
                            <p class="text-slate-500 mt-0.5">Paediatric availability has not been updated in over 1 hour.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-red-500 mt-1 flex-shrink-0"></span>
                        <div>
                            <span class="font-bold text-slate-800">Full</span>
                            <p class="text-slate-500 mt-0.5">Surgery theatre is marked unavailable on the public finder.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">Recent activity</h3>

                <div class="space-y-3 pt-1 text-xs">
                    @forelse($recentActivity as $act)
                        <div class="flex items-start justify-between">
                            <span class="text-slate-400 font-medium w-12">{{ $act->created_at->format('H:i') }}</span>
                            <div class="flex-1 ml-2 text-slate-700">
                                <span class="font-medium">{{ $act->action }}</span>
                                <span class="text-slate-400">· {{ $act->description }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs">No recent activity recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
