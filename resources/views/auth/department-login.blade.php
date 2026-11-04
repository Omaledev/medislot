@extends('layouts.base', ['title' => 'Department Sign In – MediSlot'])

@section('body')
<div class="min-h-screen bg-[#ECFDF5] flex flex-col justify-between py-12 px-4 sm:px-6">
    <div class="flex justify-center">
        <a href="{{ route('home') }}">
            <x-logo size="md" />
        </a>
    </div>

    <div class="max-w-md w-full mx-auto" x-data="{ showPass: false }">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-10 shadow-sm space-y-6">
            <!-- Icon & Header -->
            <div class="text-center space-y-2">
                <div class="w-12 h-12 bg-emerald-50 text-brand-700 rounded-xl mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                    Department Sign In
                </h2>
                <p class="text-xs text-slate-500">
                    Access your department workspace securely.
                </p>
            </div>

            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('department.login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="department_username" class="block text-xs font-semibold text-slate-700 mb-1">
                        Department username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </span>
                        <input
                            id="department_username"
                            type="text"
                            name="department_username"
                            value="{{ old('department_username', 'reception/riverside') }}"
                            required
                            placeholder="e.g. reception/riverside"
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                        >
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Try: reception/riverside, consultant/riverside, laboratory/riverside, or pharmacy/riverside</p>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input
                            id="password"
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            value="password"
                            required
                            placeholder="Enter your department password"
                            class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                        >
                        <button
                            type="button"
                            @click="showPass = !showPass"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                        >
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
                >
                    Sign In to Department
                </button>
            </form>

            <div class="pt-2 text-center space-y-1.5 text-xs text-slate-500">
                <p>Forgot your password? Contact your Hospital Admin.</p>
                <p>Are you a Hospital Admin? <a href="{{ route('login') }}" class="text-brand-700 font-semibold hover:underline">Sign in here</a>.</p>
            </div>
        </div>
    </div>

    <!-- Bottom tagline -->
    <div class="text-center text-xs text-slate-600 space-y-1">
        <p class="font-bold text-slate-800">Your department. Your workspace.</p>
        <p class="text-slate-500">Manage your hospital department securely with MediSlot.</p>
    </div>
</div>
@endsection
