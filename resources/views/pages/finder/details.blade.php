@extends('layouts.base', ['title' => $hospital->name . ' – MediSlot Hospital Finder'])

@section('body')
<div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6">
    <div class="max-w-md w-full mx-auto space-y-5">
        <!-- Logo -->
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}">
                <x-logo size="md" />
            </a>
            <a href="{{ route('finder.results') }}" class="text-xs font-semibold text-brand-700 hover:underline flex items-center space-x-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>All hospitals</span>
            </a>
        </div>

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 leading-snug">
                {{ $hospital->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                {{ $hospital->address }} · 2.4 km · {{ $hospital->phone }}
            </p>
        </div>

        <!-- Top Actions -->
        <div class="grid grid-cols-2 gap-3">
            <a href="tel:{{ $hospital->phone }}" class="flex items-center justify-center py-2.5 px-4 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                Call hospital
            </a>
            <a href="https://maps.google.com/?q={{ urlencode($hospital->name . ' ' . $hospital->address) }}" target="_blank" class="flex items-center justify-center py-2.5 px-4 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition">
                Directions
            </a>
        </div>

        <!-- Emergency Readiness -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2">
            <span class="text-xs text-slate-500 font-medium">Emergency readiness · General Emergency</span>
            <div class="flex items-center space-x-2 text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Reports accepting emergency patients</span>
            </div>
            <p class="text-[11px] text-slate-400">
                Reported by hospital · Updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}. Admission is not guaranteed.
            </p>
        </div>

        <!-- Bed Availability -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900">Bed availability</h3>

            <div class="space-y-2">
                <div class="grid grid-cols-3 text-[11px] font-semibold text-slate-400 pb-1 border-b border-slate-100">
                    <span>Category</span>
                    <span class="text-center">Free</span>
                    <span class="text-right">Status</span>
                </div>

                @forelse($hospital->bedCategories as $bed)
                    <div class="grid grid-cols-3 text-xs items-center py-1.5 border-b border-slate-50">
                        <span class="font-bold text-slate-800">{{ $bed->name }}</span>
                        <span class="text-center font-medium text-slate-600">{{ $bed->available_beds }}</span>
                        <div class="text-right">
                            @if($bed->status === 'available')
                                <span class="inline-flex items-center space-x-1 text-emerald-700 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Available</span>
                                </span>
                            @elseif($bed->status === 'limited')
                                <span class="inline-flex items-center space-x-1 text-amber-700 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Limited</span>
                                </span>
                            @else
                                <span class="inline-flex items-center space-x-1 text-red-600 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span>Full</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2">No bed information reported.</p>
                @endforelse
            </div>

            <p class="text-[11px] text-slate-400 pt-1">
                Last updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}
            </p>
        </div>

        <!-- Services -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900">Services</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($hospital->services as $srv)
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg">
                        {{ $srv->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Specialist Availability -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-1.5">
            <h3 class="text-sm font-bold text-slate-900">Specialist availability</h3>
            <div class="pt-1">
                <div class="text-xs font-semibold text-slate-800">Obstetrician</div>
                <div class="flex items-center space-x-1.5 text-xs text-amber-700 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Available from 2:00 PM</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 pt-1">
                Source: hospital · Updated 12 minutes ago
            </p>
        </div>

        <!-- Disclaimer -->
        <div class="text-center text-[11px] text-slate-400 leading-relaxed pt-2">
            Availability is reported by the hospital and may change. Call before travelling when possible.
        </div>
    </div>
</div>
@endsection
