@extends('layouts.base', ['title' => $title ?? 'MediSlot Platform Command Center'])

@section('body')
<div class="min-h-screen flex bg-slate-100 text-slate-800" x-data="{ mobileMenuOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:w-64 xl:w-72 bg-slate-900 text-white flex-col justify-between shrink-0 h-screen sticky top-0 border-r border-slate-800 z-30 select-none">
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Sidebar Brand Header -->
            <div class="px-6 py-5 border-b border-slate-800/80 flex items-center justify-between">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center space-x-3">
                    <x-logo :light="true" size="md" />
                </a>
                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-300 bg-teal-950/90 border border-teal-800/80 px-2 py-0.5 rounded-full">
                    Super Admin
                </span>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-6">
                <!-- Group 1: Operations -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Platform Operations
                    </span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('superadmin.dashboard') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <span>Overview</span>
                            @if(request()->routeIs('superadmin.dashboard'))
                                <span class="size-1.5 rounded-full bg-teal-300"></span>
                            @endif
                        </a>

                        <a href="{{ route('superadmin.hospitals.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.hospitals.*') ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <span>Hospital Directory</span>
                            @if(request()->routeIs('superadmin.hospitals.*'))
                                <span class="size-1.5 rounded-full bg-teal-300"></span>
                            @endif
                        </a>

                        <a href="{{ route('superadmin.activity') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.activity') ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <span>Global Activity Stream</span>
                            @if(request()->routeIs('superadmin.activity'))
                                <span class="size-1.5 rounded-full bg-teal-300"></span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Group 2: Platform Services -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Integrations & Access
                    </span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('superadmin.integrations') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.integrations') ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <span>EHR & API Gateways</span>
                            @if(request()->routeIs('superadmin.integrations'))
                                <span class="size-1.5 rounded-full bg-teal-300"></span>
                            @endif
                        </a>

                        <a href="{{ route('superadmin.operators') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.operators') ? 'bg-teal-700 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <span>Platform Operators</span>
                            @if(request()->routeIs('superadmin.operators'))
                                <span class="size-1.5 rounded-full bg-teal-300"></span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Group 3: External & Public Portals -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Public Network
                    </span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('finder') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <span>Bed & Care Finder</span>
                            <span class="text-[10px] text-teal-400 font-normal">Live</span>
                        </a>

                        <a href="{{ route('home') }}" target="_blank"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <span>Public Homepage</span>
                            <span class="text-[10px] text-slate-500 font-normal">&nearr;</span>
                        </a>
                    </div>
                </div>

                <!-- Sidebar "Good Things": System Status Card -->
                <div class="pt-2">
                    <div class="rounded-2xl bg-slate-800/80 border border-slate-700/60 p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">System Status</span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-emerald-400">
                                <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Operational
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-300 leading-snug">
                            All tenant routing, multi-domain resolution, and HL7/ADT gateways are synchronized.
                        </div>
                        <div class="pt-1 border-t border-slate-700/50 flex items-center justify-between text-[10px] text-slate-400">
                            <span>Engine v1.0.4</span>
                            <span>PHP 8.5</span>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Operator Footer -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="size-8 rounded-full bg-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ auth()->user()->initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-teal-400 truncate">Super Admin Operator</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-slate-400 hover:text-rose-400 transition px-2 py-1" title="Sign Out">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Slideover Navigation (Alpine.js) -->
    <div x-show="mobileMenuOpen"
         x-cloak
         class="fixed inset-0 z-50 md:hidden flex"
         role="dialog"
         aria-modal="true">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs"></div>

        <!-- Off-canvas menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative mr-16 flex w-full max-w-xs flex-1 bg-slate-900 text-white flex-col justify-between py-6 px-4">

            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <x-logo :light="true" size="md" />
                    <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white p-1 text-sm font-bold">
                        ✕
                    </button>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('superadmin.dashboard') ? 'bg-teal-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Overview</a>
                    <a href="{{ route('superadmin.hospitals.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('superadmin.hospitals.*') ? 'bg-teal-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Hospital Directory</a>
                    <a href="{{ route('superadmin.activity') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('superadmin.activity') ? 'bg-teal-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Global Activity</a>
                    <a href="{{ route('superadmin.integrations') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('superadmin.integrations') ? 'bg-teal-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}">EHR & APIs</a>
                    <a href="{{ route('superadmin.operators') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('superadmin.operators') ? 'bg-teal-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Operators</a>
                </div>

                <div class="pt-4 border-t border-slate-800 space-y-1">
                    <a href="{{ route('finder') }}" target="_blank" class="block px-3 py-2 text-xs text-slate-300 hover:text-white">Bed & Care Finder &rarr;</a>
                    <a href="{{ route('home') }}" target="_blank" class="block px-3 py-2 text-xs text-slate-300 hover:text-white">Public Site &rarr;</a>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-400 hover:bg-slate-800 rounded-lg transition">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top App Bar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-8 shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-4">
                <!-- Mobile Menu Trigger -->
                <button @click="mobileMenuOpen = true" type="button" class="md:hidden p-2 text-slate-600 hover:text-slate-900 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <div>
                    <span class="text-sm font-bold text-slate-900">MediSlot Platform Operations</span>
                    <span class="hidden sm:inline-block text-xs text-slate-400 ml-2">· {{ date('l, j F Y') }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Platform Live
                </span>

                <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    Public Portal
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-medium text-rose-800 space-y-1">
                    <p class="font-bold">Please check the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Subtle Footer -->
        <footer class="px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-400">
            MediSlot Platform Operations Center &copy; {{ date('Y') }} &middot; Confidential Founder Console
        </footer>
    </div>
</div>
@endsection
