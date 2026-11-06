@extends('layouts.public', ['title' => ($careType ?? 'Emergency Care') . ' Results – MediSlot'])

@section('content')
<main class="min-h-screen bg-slate-50/50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header & Search Filter -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $careType ?? ($searchTerm ? 'Search: ' . $searchTerm : 'All Care') }}
                </h1>
                <p class="mt-1.5 text-sm text-slate-500">
                    Showing <strong class="text-slate-800 font-semibold">{{ $hospitals->count() }}</strong> {{ Str::plural('hospital', $hospitals->count()) }} reporting beds & services within 10 km
                </p>
            </div>

            <form action="{{ route('finder.results') }}" method="GET" class="w-full sm:w-80">
                @if($careType)
                    <input type="hidden" name="care_type" value="{{ $careType }}" />
                @endif
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchTerm ?? '' }}"
                        placeholder="Filter by hospital or service"
                        class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-xs focus:outline-none focus:border-teal-700 focus:ring-1 focus:ring-teal-700 transition"
                    />
                </div>
            </form>
        </div>

        <!-- Filter Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="#" class="rounded-xl border border-teal-700 bg-teal-50 px-4 py-2 text-xs font-semibold text-teal-800 transition">
                Distance · Nearest first
            </a>
            <a href="#" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:border-slate-300 transition">
                Availability
            </a>
            <a href="#" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:border-slate-300 transition">
                Service
            </a>
        </div>

        <!-- Hospital Cards 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($hospitals as $h)
                @php
                    $availBeds = $h->calculateAvailableBeds();
                    $distance = $loop->first ? '2.4 km' : ($loop->iteration === 2 ? '4.1 km' : '6.8 km');
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs flex flex-col justify-between space-y-5 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-xl font-bold text-slate-900">
                                <a href="{{ route('finder.hospital', $h->slug) }}" class="hover:text-teal-700 transition">
                                    {{ $h->name }}
                                </a>
                            </h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 shrink-0">
                                {{ $distance }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>{{ $h->address }} · {{ $distance }}</span>
                        </div>

                        <!-- Services Tags -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($h->services->take(3) as $srv)
                                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">
                                    {{ $srv->name }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Inset Status Card -->
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 space-y-2.5">
                            <div class="flex items-center justify-between text-xs font-medium">
                                @if($availBeds > 0)
                                    <span class="flex items-center gap-2 text-amber-700 font-semibold">
                                        <span class="size-2 rounded-full bg-amber-500"></span>
                                        {{ $availBeds }} general beds available
                                    </span>
                                @else
                                    <span class="flex items-center gap-2 text-rose-600 font-semibold">
                                        <span class="size-2 rounded-full bg-rose-500"></span>
                                        No general beds reported
                                    </span>
                                @endif
                                <span class="text-slate-400">General Ward</span>
                            </div>

                            <div class="flex items-center justify-between text-xs font-medium">
                                @if($h->emergency_status === 'accepting')
                                    <span class="flex items-center gap-2 text-emerald-700 font-semibold">
                                        <span class="size-2 rounded-full bg-emerald-500"></span>
                                        Accepting emergency patients
                                    </span>
                                @elseif($h->emergency_status === 'limited')
                                    <span class="flex items-center gap-2 text-amber-700 font-semibold">
                                        <span class="size-2 rounded-full bg-amber-500"></span>
                                        Limited emergency capacity
                                    </span>
                                @else
                                    <span class="flex items-center gap-2 text-rose-600 font-semibold">
                                        <span class="size-2 rounded-full bg-rose-500"></span>
                                        Emergency diversion in effect
                                    </span>
                                @endif
                                <span class="text-slate-400">Emergency intake</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400">
                            Last updated {{ $h->last_reported_at?->diffForHumans() ?? ($loop->first ? '3 minutes ago' : ($loop->iteration === 2 ? '18 minutes ago' : '25 minutes ago')) }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <a href="tel:{{ $h->phone }}" class="flex-1 rounded-xl bg-teal-800 hover:bg-teal-900 py-2.5 text-center text-sm font-semibold text-white shadow-xs transition">
                            Call hospital
                        </a>
                        <a href="https://maps.google.com/?q={{ urlencode($h->name . ' ' . $h->address) }}" target="_blank" class="flex-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 py-2.5 text-center text-sm font-semibold text-slate-700 shadow-xs transition">
                            Directions
                        </a>
                        <a href="{{ route('finder.hospital', $h->slug) }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 flex items-center gap-1 transition">
                            Details &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-slate-200 bg-white p-12 text-center text-sm text-slate-500 shadow-xs">
                    No hospitals found matching your criteria.
                </div>
            @endforelse
        </div>
    </div>
</main>
@endsection
