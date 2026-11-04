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

                <!-- User profile badge -->
                <div class="flex items-center space-x-2 text-sm">
                    <span class="font-medium text-slate-800">{{ auth()->user()->name }}</span>
                    <span class="text-slate-400">·</span>
                    <span class="text-slate-500 text-xs bg-slate-100 px-2 py-0.5 rounded font-medium">{{ auth()->user()->title ?? ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
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
