@extends('layouts.portal', ['title' => 'Admin Profile – MediSlot HMS'])

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
<a href="{{ route('admin.integrations') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>EHR Integrations</span>
</a>
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Hospital Settings</span>
</a>
<a href="{{ route('admin.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>My Profile</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div
    class="max-w-4xl space-y-6"
    x-data="{
        imageUrl: null,
        removeAvatar: false,
        previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                this.imageUrl = URL.createObjectURL(file);
                this.removeAvatar = false;
            }
        },
        clearAvatar() {
            this.imageUrl = null;
            this.removeAvatar = true;
            this.$refs.avatarInput.value = '';
        }
    }"
>
    <!-- Header with title and save button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Administrator Profile</h1>
            <p class="mt-1 text-sm text-slate-500">Manage your profile photo, personal information, and login credentials.</p>
        </div>
        <button type="submit" form="admin-profile-form" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800 transition shrink-0">
            Save Profile Changes
        </button>
    </div>

    <form id="admin-profile-form" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Hidden input for file and removal flag -->
        <input
            type="file"
            name="avatar"
            x-ref="avatarInput"
            @change="previewImage($event)"
            accept="image/png,image/jpeg,image/jpg,image/webp"
            class="hidden"
        />
        <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'" />

        <!-- Profile Header & Avatar Upload Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Avatar Preview with Clickable Overlay -->
                <div class="relative group cursor-pointer shrink-0" @click="$refs.avatarInput.click()" title="Click to upload picture">
                    <div class="w-24 h-24 rounded-full bg-brand-700 text-white font-bold text-2xl flex items-center justify-center shadow-md ring-4 ring-brand-100 select-none overflow-hidden">
                        <template x-if="imageUrl">
                            <img :src="imageUrl" alt="Preview" class="w-full h-full object-cover" />
                        </template>
                        <template x-if="!imageUrl && !removeAvatar">
                            <img
                                src="{{ $user->avatar_url }}"
                                alt="{{ $user->name }}"
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.parentElement.innerHTML='{{ $user->initials }}';"
                            />
                        </template>
                        <template x-if="removeAvatar">
                            <span class="font-bold text-2xl text-white">{{ $user->initials }}</span>
                        </template>
                    </div>

                    <!-- Camera icon overlay on hover -->
                    <div class="absolute inset-0 rounded-full bg-slate-900/50 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-[10px] font-semibold mt-0.5">Change</span>
                    </div>
                </div>

                <!-- Avatar Actions & Details -->
                <div class="text-center sm:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                            Hospital Administrator
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $hospital->name }} · {{ $user->email }}</p>

                    <!-- Upload & Remove buttons -->
                    <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                        <button
                            type="button"
                            @click="$refs.avatarInput.click()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 text-xs font-semibold transition"
                        >
                            <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Choose New Picture
                        </button>

                        <!-- Direct Save button when a photo has been chosen -->
                        <button
                            type="submit"
                            x-show="imageUrl"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold shadow-sm transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Photo Now
                        </button>

                        <button
                            type="button"
                            x-show="imageUrl || ({{ $user->avatar ? 'true' : 'false' }} && !removeAvatar)"
                            @click="clearAvatar()"
                            class="inline-flex items-center px-3 py-2 rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 text-xs font-semibold transition"
                        >
                            Remove Photo
                        </button>

                        <!-- Save button when remove was clicked -->
                        <button
                            type="submit"
                            x-show="removeAvatar"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition"
                        >
                            Save & Remove Photo
                        </button>

                        <span class="text-xs text-slate-400">JPG, PNG, or WebP up to 2MB</span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500">
                        <span>Account created: <strong>{{ $user->created_at->format('M d, Y') }}</strong></span>
                        <span>·</span>
                        <span>Role: <strong class="text-slate-700">hospital_admin</strong></span>
                        <span>·</span>
                        <span>Login handle: <code class="px-2 py-0.5 bg-slate-100 rounded text-slate-700 font-mono">{{ $user->department_username }}</code></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Personal & Professional Details</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Professional Title</label>
                    <input type="text" name="title" value="{{ old('title', $user->title ?? 'Hospital Administrator') }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>
            </div>
        </div>

        <!-- Security & Password Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Change Password</h3>
            <p class="text-xs text-slate-500">Leave blank if you do not wish to change your password.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" autocomplete="new-password" placeholder="At least 8 characters" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Re-type new password" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>
            </div>
        </div>

        <!-- Bottom Action Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-6 text-sm font-semibold text-white shadow-sm hover:bg-teal-800 transition">
                Save Profile Changes
            </button>
        </div>
    </form>
</div>
@endsection
