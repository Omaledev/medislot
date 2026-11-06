@extends('layouts.base')

@section('body')
<div class="min-h-screen flex flex-col md:flex-row bg-[#F8FAFC]">
    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <!-- Sidebar Header -->
            <div class="px-6 py-6 border-b border-slate-100">
                <a href="{{ route('home') }}">
                    <x-logo size="md" />
                </a>
                <div class="text-xs text-slate-400 mt-1 capitalize font-medium">
                    @yield('portal_subtitle', str_replace('_', ' ', auth()->user()->role ?? 'Portal'))
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1">
                @yield('sidebar_nav')
            </nav>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top App Bar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 z-10">
            <div class="flex items-center space-x-4">
                <span class="font-semibold text-slate-900 text-base">{{ auth()->user()->hospital?->name ?? 'Riverside General Hospital' }}</span>
                <span class="text-xs text-slate-400 hidden sm:inline">· {{ date('D, j M Y') }}</span>
            </div>

            <div class="flex items-center space-x-4">
                @yield('topbar_search')

                <!-- Notification Bell -->
                <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition" title="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-700 rounded-full"></span>
                </button>

                <!-- User profile dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex items-center space-x-3 p-1 rounded-xl hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-brand-700/20"
                        :class="{ 'bg-slate-100': open }"
                    >
                        <!-- Profile picture / Avatar -->
                        <div class="w-9 h-9 rounded-full bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-sm ring-2 ring-brand-100 select-none shrink-0 overflow-hidden">
                            <img
                                src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->name }}"
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.parentElement.innerHTML='{{ auth()->user()->initials }}';"
                            />
                        </div>

                        <!-- Name and role -->
                        <div class="text-left hidden sm:block">
                            <div class="font-semibold text-slate-800 text-sm leading-tight flex items-center gap-1.5">
                                <span>{{ auth()->user()->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium leading-tight mt-0.5">
                                {{ auth()->user()->title ?? ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </div>
                        </div>

                        <!-- Down chevron -->
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Panel -->
                    <div
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        x-cloak
                        class="absolute right-0 mt-2 w-60 rounded-2xl bg-white shadow-xl border border-slate-200 py-1.5 z-50 divide-y divide-slate-100 text-xs"
                    >
                        <!-- Header with email and role badge -->
                        <div class="px-4 py-3">
                            <p class="font-bold text-slate-900 text-sm truncate">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500 truncate mt-0.5 font-mono text-[11px]">{{ auth()->user()->email }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded text-[10px] font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </div>

                        <!-- Menu Options -->
                        <div class="py-1">
                            <a
                                href="{{ auth()->user()->getProfileUrl() }}"
                                class="flex items-center gap-2.5 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition"
                            >
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>My Profile</span>
                            </a>

                            @if (auth()->user()->role === 'hospital_admin')
                                <a
                                    href="{{ route('admin.settings') }}"
                                    class="flex items-center gap-2.5 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition"
                                >
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Hospital Settings</span>
                                </a>

                                <a
                                    href="{{ route('admin.integrations') }}"
                                    class="flex items-center gap-2.5 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-700 transition"
                                >
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span>EHR Integrations</span>
                                </a>
                            @endif
                        </div>

                        <!-- Sign Out -->
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2 font-semibold text-rose-600 hover:bg-rose-50 transition"
                                >
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Alerts -->
        @if(session('success'))
            <div class="m-6 mb-0 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="m-6 mb-0 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>
@endsection
