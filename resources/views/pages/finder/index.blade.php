@extends('layouts.base', ['title' => 'Find care. Know where to go. – MediSlot'])

@section('body')
<div class="min-h-screen bg-emerald-50/40 flex flex-col justify-between py-8 px-4 sm:px-6">
    <div class="max-w-md w-full mx-auto space-y-6">
        <!-- Logo -->
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}">
                <x-logo size="md" />
            </a>
            <a href="{{ route('home') }}" class="text-xs font-semibold text-brand-700 hover:underline">
                Back to Home
            </a>
        </div>

        <!-- Location Box -->
        <div class="bg-white rounded-2xl border border-emerald-100 p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="block text-[11px] text-slate-400 font-medium uppercase tracking-wider">Your location</span>
                <span class="text-sm font-semibold text-slate-800">Jos North, Plateau, Nigeria</span>
            </div>
            <button class="text-xs font-semibold text-brand-700 hover:text-brand-800 transition">
                Change
            </button>
        </div>

        <!-- Header -->
        <div class="space-y-1.5">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 leading-tight">
                Find care. Know where to go.
            </h1>
            <p class="text-sm text-slate-500">
                See which nearby hospitals report beds and services.
            </p>
        </div>

        <!-- Search Form -->
        <form action="{{ route('finder.results') }}" method="GET">
            <div class="relative">
                <input
                    type="text"
                    name="q"
                    placeholder="Search hospital, service or specialty"
                    class="w-full bg-white rounded-2xl border border-slate-200 px-4 py-3.5 pl-11 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
                >
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>

        <!-- Care Categories -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold text-slate-800 tracking-wide">What care do you need?</h3>

            <a href="{{ route('finder.results', ['care_type' => 'Accident / Trauma']) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-brand-700 shadow-sm transition group">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-700 transition">Accident / Trauma</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Injuries, fractures, road accidents</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <a href="{{ route('finder.results', ['care_type' => 'Maternity / Delivery']) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-brand-700 shadow-sm transition group">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-700 transition">Maternity / Delivery</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Labour and delivery care</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <a href="{{ route('finder.results', ['care_type' => 'General Emergency']) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-brand-700 shadow-sm transition group">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-700 transition">General Emergency</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Sudden illness or severe symptoms</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <a href="{{ route('finder.results', ['care_type' => 'Child Emergency']) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-brand-700 shadow-sm transition group">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-700 transition">Child Emergency</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Care for infants and children</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <a href="{{ route('finder.results', ['care_type' => 'Other Care']) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-brand-700 shadow-sm transition group">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-700 transition">Other Care</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Services, clinics and specialists</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Emergency disclaimer at bottom -->
    <div class="max-w-md w-full mx-auto mt-8 text-center text-xs text-slate-500 leading-relaxed">
        Availability is reported by hospitals and may change. In a life-threatening emergency, do not delay seeking care while using this app.
    </div>
</div>
@endsection
