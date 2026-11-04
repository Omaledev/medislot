@extends('layouts.portal', ['title' => 'Laboratory Profile – MediSlot HMS'])

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
<a href="{{ route('lab.reports') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Results & Reports</span>
</a>
<a href="{{ route('lab.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('lab.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Laboratory Department Profile</h1>
        <p class="text-xs text-slate-500 mt-1">Diagnostic personnel credentials, laboratory section assignments, and login credentials.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
        <div class="flex items-center space-x-4 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-teal-700 text-white flex items-center justify-center font-bold text-2xl shadow-sm">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500">{{ $user->title ?? 'Medical Laboratory Scientist' }} · {{ $hospital->name }}</p>
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                    Active Technician
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-50">
                <span class="text-slate-500 block mb-1">Email Address</span>
                <span class="font-semibold text-slate-800">{{ $user->email }}</span>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <span class="text-slate-500 block mb-1">Assigned Department</span>
                <span class="font-semibold text-slate-800">{{ $user->department?->name ?? 'Diagnostic Laboratory' }}</span>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <span class="text-slate-500 block mb-1">Department Login Handle</span>
                <span class="font-semibold font-mono text-slate-800">{{ $user->department_username ?? 'laboratory/riverside' }}</span>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <span class="text-slate-500 block mb-1">Hospital License No</span>
                <span class="font-semibold text-slate-800">{{ $hospital->license_number ?? 'MDC-99214-RG' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
