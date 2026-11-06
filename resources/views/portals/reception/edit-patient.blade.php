@extends('layouts.portal', ['portal_subtitle' => 'Reception Desk'])

@section('title', 'Edit Patient - ' . $patient->full_name)

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Dashboard
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-teal-50 text-teal-800 transition">
    <svg class="w-5 h-5 mr-3 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
    Patients
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    Visits & Queue
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
    New Patient
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
    Bed Availability
</a>
<a href="{{ route('reception.settings') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    Settings
</a>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                Edit patient details
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $patient->patient_id }} · {{ $patient->full_name }}
            </p>
        </div>
        <a href="{{ route('reception.patients.show', $patient) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Cancel
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('reception.patients.update', $patient) }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700">First name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Last name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Date of birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Gender</label>
                <select name="gender" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="Female" {{ old('gender', $patient->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Male" {{ old('gender', $patient->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Other" {{ old('gender', $patient->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Phone number</label>
                <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Emergency contact</label>
                <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Residential address</label>
                <textarea name="address" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('address', $patient->address) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Allergies</label>
                <input type="text" name="allergies" value="{{ old('allergies', $patient->allergies) }}" placeholder="e.g. Penicillin, Peanuts" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
            </div>

            <div class="flex justify-end gap-3 sm:col-span-2 pt-4 border-t border-slate-100">
                <a href="{{ route('reception.patients.show', $patient) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-6 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                    Save details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
