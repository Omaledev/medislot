@extends('layouts.base')

@section('body')
<header x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                @if(request()->routeIs('finder.results') || request()->routeIs('finder.hospital'))
                    <a href="{{ route('finder') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-teal-700 transition pr-2 border-r border-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Search</span>
                    </a>
                @endif
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <x-logo />
                </a>
            </div>

            <!-- Desktop Nav Items -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-teal-700 font-semibold' : 'hover:text-teal-700' }} transition">Home</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-teal-700 font-semibold' : 'hover:text-teal-700' }} transition">About</a>
                <a href="{{ route('app') }}" class="{{ request()->routeIs('app') ? 'text-teal-700 font-semibold' : 'hover:text-teal-700' }} transition">App</a>
                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'text-teal-700 font-semibold' : 'hover:text-teal-700' }} transition">Contact Us</a>
                <a href="/api/documentation" class="hover:text-teal-700 transition">API Docs</a>
            </nav>

            <!-- Desktop Actions -->
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('finder') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-teal-800 hover:bg-teal-900 transition">
                    Find a Bed
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-slate-700 hover:text-teal-700 transition">
                    Hospital Sign In
                </a>
            </div>

            <!-- Mobile Controls -->
            <div class="flex md:hidden items-center space-x-2">
                <a href="{{ route('finder') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-teal-800 hover:bg-teal-900 transition">
                    Find a Bed
                </a>
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-teal-700 hover:bg-teal-50 focus:outline-none focus:ring-2 focus:ring-teal-500 transition"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-label="Toggle navigation menu"
                >
                    <!-- Hamburger Icon -->
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <!-- Close Icon -->
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.away="mobileMenuOpen = false"
        class="md:hidden border-b border-slate-200 bg-white px-4 pt-3 pb-6 shadow-lg"
    >
        <div class="space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-700 hover:bg-slate-50 hover:text-teal-700' }}">Home</a>
            <a href="{{ route('about') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('about') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-700 hover:bg-slate-50 hover:text-teal-700' }}">About</a>
            <a href="{{ route('app') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('app') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-700 hover:bg-slate-50 hover:text-teal-700' }}">App</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('contact') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-700 hover:bg-slate-50 hover:text-teal-700' }}">Contact Us</a>
            <a href="/api/documentation" class="block px-3 py-2.5 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-teal-700">API Documentation</a>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
            <a href="{{ route('finder') }}" class="flex w-full items-center justify-center px-4 py-2.5 rounded-lg font-semibold text-white bg-teal-800 hover:bg-teal-900 shadow-sm transition">
                Find a Bed
            </a>
            <a href="{{ route('register') }}" class="flex w-full items-center justify-center px-4 py-2.5 rounded-lg font-semibold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition">
                Register Hospital
            </a>
            <div class="grid grid-cols-2 gap-2 pt-2">
                <a href="{{ route('login') }}" class="flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Hospital Sign In
                </a>
                <a href="{{ route('department.login') }}" class="flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Department Sign In
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
        <div class="flex flex-wrap space-x-6 text-sm">
            <a href="{{ route('about') }}" class="text-slate-600 hover:text-teal-700">About</a>
            <a href="{{ route('app') }}" class="text-slate-600 hover:text-teal-700">Explore the app</a>
            <a href="{{ route('contact') }}" class="text-slate-600 hover:text-teal-700">Contact</a>
            <a href="/api/documentation" class="text-slate-600 hover:text-teal-700">API Docs</a>
            <a href="{{ route('department.login') }}" class="text-slate-600 hover:text-teal-700">Department Sign In</a>
            <a href="{{ route('register') }}" class="text-slate-600 hover:text-teal-700">Register Hospital</a>
            <a href="{{ route('login') }}" class="text-slate-600 hover:text-teal-700">Hospital Sign In</a>
        </div>
    </div>
</footer>
@endsection
