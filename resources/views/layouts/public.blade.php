@extends('layouts.base')

@section('body')
<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('home') }}">
                <x-logo />
            </a>

            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-brand-700 transition">Home</a>
                <a href="{{ route('finder') }}" class="hover:text-brand-700 transition">Find Care</a>
                <a href="#about" class="hover:text-brand-700 transition">About</a>
                <a href="/api/documentation" class="hover:text-brand-700 transition">API Docs</a>
                <a href="{{ route('login') }}" class="hover:text-brand-700 transition">HMS Sign In</a>
            </nav>

            <div class="flex items-center space-x-3">
                <a href="{{ route('finder') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-700 hover:bg-brand-800 transition">
                    Find a Hospital
                </a>
            </div>
        </div>
    </div>
</header>

<main class="flex-grow">
    @yield('content')
</main>

<footer class="bg-white border-t border-slate-200 py-10 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
        <div class="flex items-center space-x-3">
            <x-logo size="sm" />
            <span>&copy; {{ date('Y') }} MediSlot. Hospital finder and healthcare management.</span>
        </div>
        <div class="flex space-x-6 text-sm">
            <a href="/api/documentation" class="text-brand-700 hover:underline">Swagger API Docs</a>
            <a href="{{ route('department.login') }}" class="text-brand-700 hover:underline">Department Sign In</a>
            <a href="{{ route('register') }}" class="text-brand-700 hover:underline">Register Hospital</a>
            <a href="{{ route('login') }}" class="text-brand-700 hover:underline">Admin Login</a>
        </div>
    </div>
</footer>
@endsection
