@extends('layouts.portal', ['title' => 'Patients – MediSlot Reception'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Patients</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $patients->total() }} registered patients in hospital registry</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reception.visits.create') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-sm transition">
                Start visit
            </a>
            <a href="{{ route('reception.patients.create') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Register patient
            </a>
        </div>
    </div>

    <!-- Search input -->
    <form method="GET" class="relative max-w-md">
        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search by ID, name, or phone number"
            class="w-full bg-white rounded-xl border border-slate-200 px-4 py-2.5 pl-10 text-xs focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none shadow-sm"
        >
        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                        <th class="py-2.5">Patient ID</th>
                        <th class="py-2.5">Full Name</th>
                        <th class="py-2.5">Gender</th>
                        <th class="py-2.5">Age / DOB</th>
                        <th class="py-2.5">Phone</th>
                        <th class="py-2.5">Registered</th>
                        <th class="py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $pt)
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900">{{ $pt->patient_id }}</td>
                            <td class="py-3.5 text-slate-800 font-medium">{{ $pt->full_name }}</td>
                            <td class="py-3.5 text-slate-600">{{ $pt->gender }}</td>
                            <td class="py-3.5 text-slate-600">
                                {{ $pt->age ? $pt->age . ' yrs' : ($pt->date_of_birth?->format('d M Y') ?? '—') }}
                            </td>
                            <td class="py-3.5 text-slate-600">{{ $pt->phone ?? '—' }}</td>
                            <td class="py-3.5 text-slate-400">{{ $pt->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 text-right space-x-2">
                                <a href="{{ route('reception.visits.create', ['patient_id' => $pt->id]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                    Start visit
                                </a>
                                <span class="text-slate-300">·</span>
                                <a href="{{ route('reception.patients.show', $pt) }}" class="text-xs font-semibold text-brand-700 hover:underline">
                                    View profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection
