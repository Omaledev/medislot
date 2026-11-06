@extends('layouts.portal', ['portal_subtitle' => 'Reception Desk'])

@section('title', 'Reception Settings – MediSlot')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
    Dashboard
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
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
<a href="{{ route('reception.settings') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-teal-50 text-teal-800 transition">
    <svg class="w-5 h-5 mr-3 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    Settings
</a>
@endsection

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                Reception settings
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage your receptionist profile and default visit options.
            </p>
        </div>
        <button type="submit" form="reception-settings-form" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
            Save changes
        </button>
    </div>

    <form id="reception-settings-form" method="POST" action="{{ route('reception.settings.update') }}">
        @csrf
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">Reception profile</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Full name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Email address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Phone number</label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '0803 000 0121') }}" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Department</label>
                    <input type="text" disabled value="Reception Desk" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Default department for new visits</label>
                    <select name="default_department" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
