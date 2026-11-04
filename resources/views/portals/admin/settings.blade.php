@extends('layouts.portal', ['title' => 'Hospital Settings – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Hospital settings</h1>
            <p class="text-xs text-slate-500 mt-1">Controls your profile and what the public sees on MediSlot Finder</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Save changes
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hospital Profile -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
            <h3 class="font-bold text-slate-900 text-sm">Hospital profile</h3>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Hospital name</label>
                <input type="text" name="name" value="{{ old('name', $hospital->name) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address', $hospital->address) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Phone number</label>
                <input type="text" name="phone" value="{{ old('phone', $hospital->phone) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Admin email</label>
                <input type="email" name="email" value="{{ old('email', $hospital->email) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>
        </div>

        <!-- Public Information -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
            <h3 class="font-bold text-slate-900 text-sm">Public information</h3>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Public phone (shown to patients)</label>
                <input type="text" name="phone" value="{{ old('phone', $hospital->phone) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Location for directions</label>
                <input type="text" name="location_for_directions" value="{{ old('location_for_directions', $hospital->location_for_directions ?? $hospital->address) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Emergency acceptance</label>
                <select name="emergency_status" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                    <option value="accepting" {{ $hospital->emergency_status === 'accepting' ? 'selected' : '' }}>Accepting emergency patients</option>
                    <option value="limited" {{ $hospital->emergency_status === 'limited' ? 'selected' : '' }}>Limited capacity</option>
                    <option value="not_accepting" {{ $hospital->emergency_status === 'not_accepting' ? 'selected' : '' }}>Not accepting emergency patients</option>
                </select>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 leading-relaxed">
                Patients see this phone number, address and the status above. Private patient records are never shown.
            </p>
        </div>

        <!-- Availability Reminders -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
            <h3 class="font-bold text-slate-900 text-sm">Availability reminders</h3>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Warn me if beds are not updated for</label>
                <select name="warn_unupdated_hours" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                    <option value="1" {{ $hospital->warn_unupdated_hours == 1 ? 'selected' : '' }}>1 hour</option>
                    <option value="2" {{ $hospital->warn_unupdated_hours == 2 ? 'selected' : '' }}>2 hours</option>
                    <option value="4" {{ $hospital->warn_unupdated_hours == 4 ? 'selected' : '' }}>4 hours</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Mark availability as stale after</label>
                <select name="mark_stale_hours" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                    <option value="2" {{ $hospital->mark_stale_hours == 2 ? 'selected' : '' }}>2 hours</option>
                    <option value="3" {{ $hospital->mark_stale_hours == 3 ? 'selected' : '' }}>3 hours</option>
                    <option value="6" {{ $hospital->mark_stale_hours == 6 ? 'selected' : '' }}>6 hours</option>
                </select>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 leading-relaxed">
                Stale availability shows as "Unknown" on the public finder, with its last update time.
            </p>
        </div>
    </div>
</form>
@endsection
