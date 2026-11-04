@extends('layouts.auth', ['title' => 'Forgot Password – MediSlot'])

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">
            Forgot Password?
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Enter your email address and we will send you a link to reset your password.
        </p>
    </div>

    <form method="POST" action="{{ route('forgot-password') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                required
                placeholder="name@hospital.example"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
            >
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg text-sm shadow-sm transition"
        >
            Send Reset Link
        </button>
    </form>

    <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
        Remember your password? <a href="{{ route('login') }}" class="text-brand-700 font-semibold hover:underline">Sign in</a>.
    </div>
</div>
@endsection
