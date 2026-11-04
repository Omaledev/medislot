@extends('layouts.portal', ['title' => 'Register Patient – MediSlot Reception'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Register patient</h1>
        <p class="text-xs text-slate-500 mt-1">Search first to avoid creating a duplicate record</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Registration Form -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('reception.patients.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">First name (required)</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" placeholder="Amina" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Last name (required)</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}" placeholder="Yusuf" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date of birth or age (required)</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Gender (required)</label>
                        <select name="gender" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Phone number (recommended)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0803 000 0124" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Address (optional)</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="14 Marina Road, Jos" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency contact (optional)</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="e.g. Next of Kin · 0802 000 0000" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('reception.patients') }}" class="px-5 py-2.5 border border-slate-300 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm transition">
                        Register patient
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Duplicate Search Helper -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">Already registered?</h3>
                <form action="{{ route('reception.patients') }}" method="GET">
                    <input type="text" name="q" placeholder="Search by ID, name or phone" class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs focus:border-brand-700 outline-none">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
