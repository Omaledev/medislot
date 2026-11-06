@extends('layouts.auth', ['title' => 'Sign In – MediSlot'])

@section('content')
<div class="space-y-6" x-data="{ showPass: false }">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            {{ isset($tenantHospital) ? $tenantHospital->name : 'Welcome Back' }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            {{ isset($tenantHospital) ? 'Sign in to access your hospital staff portal.' : 'Sign in to manage your hospital on MediSlot.' }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', 'hannah@riverside.example') }}"
                required
                autocomplete="email"
                autofocus
                placeholder="name@hospital.example"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
            >
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
            </div>
            <div class="relative">
                <input
                    id="password"
                    :type="showPass ? 'text' : 'password'"
                    name="password"
                    value="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 pr-10 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
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

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-700 focus:ring-brand-700">
                <span>Remember me</span>
            </label>
            <a href="{{ route('forgot-password') }}" class="font-medium text-brand-700 hover:underline">
                Forgot password?
            </a>
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
        >
            Sign In
        </button>
    </form>

    <div class="pt-4 border-t border-slate-100 space-y-2 text-xs text-center text-slate-500">
        <div>
            New to MediSlot? <a href="{{ route('register') }}" class="text-brand-700 font-semibold hover:underline">Register your hospital</a>.
        </div>
        <div>
            Are you a department or clinical staff? <a href="{{ route('department.login') }}" class="text-brand-700 font-semibold hover:underline">Department Sign In</a>.
        </div>
    </div>
</div>
@endsection
