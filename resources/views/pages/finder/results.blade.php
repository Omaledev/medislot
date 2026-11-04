@extends('layouts.base', ['title' => 'Emergency Care Results – MediSlot'])

@section('body')
<div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6">
    <div class="max-w-md w-full mx-auto space-y-6">
        <!-- Logo and back -->
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}">
                <x-logo size="md" />
            </a>
            <a href="{{ route('finder') }}" class="text-xs font-semibold text-brand-700 hover:underline flex items-center space-x-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Change search</span>
            </a>
        </div>

        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                {{ $careType ?? 'General Emergency' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $hospitals->count() }} {{ Str::plural('hospital', $hospitals->count()) }} found in your area
            </p>
        </div>

        <!-- Filter pills -->
        <div class="flex space-x-2 text-xs font-medium">
            <button class="px-3 py-2 bg-white rounded-xl border border-slate-200 text-slate-700 hover:border-slate-300 shadow-sm transition">
                Distance
            </button>
            <button class="px-3 py-2 bg-white rounded-xl border border-slate-200 text-slate-700 hover:border-slate-300 shadow-sm transition">
                Availability
            </button>
            <button class="px-3 py-2 bg-white rounded-xl border border-slate-200 text-slate-700 hover:border-slate-300 shadow-sm transition">
                Service
            </button>
        </div>

        <!-- Hospital Cards -->
        <div class="space-y-4">
            @forelse($hospitals as $h)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3.5">
                    <div>
                        <a href="{{ route('finder.hospital', $h->slug) }}" class="text-base font-bold text-slate-900 hover:text-brand-700 transition">
                            {{ $h->name }}
                        </a>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $h->address }} · {{ $loop->first ? '2.4 km' : '4.1 km' }}
                        </p>
                        <p class="text-xs text-slate-600 mt-1">
                            {{ $h->services->pluck('name')->take(3)->implode(' · ') }}
                        </p>
                    </div>

                    <div class="space-y-1.5 text-xs font-medium">
                        @php
                            $availBeds = $h->calculateAvailableBeds();
                        @endphp
                        @if($availBeds > 0)
                            <div class="flex items-center space-x-2 text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span>
                                <span>{{ $availBeds }} general beds available</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 text-red-600">
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <span>No general beds reported</span>
                            </div>
                        @endif

                        @if($h->emergency_status === 'accepting')
                            <div class="flex items-center space-x-2 text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span>Accepting emergency patients</span>
                            </div>
                        @elseif($h->emergency_status === 'limited')
                            <div class="flex items-center space-x-2 text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span>
                                <span>Limited emergency capacity</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 text-red-600">
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <span>Emergency diversion in effect</span>
                            </div>
                        @endif
                    </div>

                    <p class="text-[11px] text-slate-400">
                        Last updated {{ $h->last_reported_at?->diffForHumans() ?? 'recently' }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <a href="tel:{{ $h->phone }}" class="flex items-center justify-center py-2.5 px-4 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Call hospital
                        </a>
                        <a href="https://maps.google.com/?q={{ urlencode($h->name . ' ' . $h->address) }}" target="_blank" class="flex items-center justify-center py-2.5 px-4 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Directions
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-sm text-slate-500">
                    No hospitals found matching your criteria.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
