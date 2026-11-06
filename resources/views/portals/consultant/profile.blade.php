@extends('layouts.portal', ['title' => 'Doctor Profile – Consultant MediSlot'])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>My Patients</span>
</a>
<a href="{{ route('consultant.history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Consultation History</span>
</a>
<a href="{{ route('consultant.lab-requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Laboratory Requests</span>
</a>
<a href="{{ route('consultant.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('consultant.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('consultant.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">My profile</h1>
            <p class="mt-1 text-sm text-slate-500">Manage your consultant contact and professional details.</p>
        </div>
        <button type="submit" form="consultant-profile-form" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800 transition">
            Save changes
        </button>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">Professional information</h2>
        <form id="consultant-profile-form" method="POST" action="{{ route('consultant.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Professional title</label>
                    <input type="text" name="title" value="{{ old('title', $user->title ?? 'Consultant Physician') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Phone number</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '0803 000 0001') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Department</label>
                    <input type="text" disabled value="{{ $user->department?->name ?? 'Internal Medicine' }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Hospital</label>
                    <input type="text" disabled value="{{ $hospital->name }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500" />
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
