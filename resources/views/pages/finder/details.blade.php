@extends('layouts.public', ['title' => $hospital->name . ' – MediSlot Hospital Finder'])

@section('content')
<main class="min-h-screen bg-slate-50/50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Hospital Header Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs sm:flex sm:items-center sm:justify-between sm:gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2">
                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800">
                        Verified Facility
                    </span>
                    <span class="text-xs text-slate-400">2.4 km away</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $hospital->name }}
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $hospital->address }} · {{ $hospital->phone }}
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="mt-6 sm:mt-0 flex flex-wrap items-center gap-3 shrink-0">
                <a href="tel:{{ $hospital->phone }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-teal-800 hover:bg-teal-900 px-6 text-sm font-semibold text-white shadow-xs transition">
                    Call hospital
                </a>
                <a href="https://maps.google.com/?q={{ urlencode($hospital->name . ' ' . $hospital->address) }}" target="_blank" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-6 text-sm font-semibold text-slate-700 shadow-xs transition">
                    Directions
                </a>
            </div>
        </div>

        <!-- 2-Column Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Left Column: Emergency Readiness & Specialist -->
            <div class="space-y-6">
                <!-- Emergency Readiness -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-3">
                    <h2 class="text-lg font-bold text-slate-900">Emergency Readiness</h2>
                    <div class="flex items-center space-x-2 text-sm font-semibold text-emerald-700">
                        <span class="size-2.5 rounded-full bg-emerald-500"></span>
                        <span>Reports accepting emergency patients</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Reported by hospital staff · Updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}. Admission is not guaranteed without clinical assessment.
                    </p>
                </div>

                <!-- Specialist Availability -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-3">
                    <h2 class="text-lg font-bold text-slate-900">Specialist Availability</h2>
                    <div class="pt-1">
                        <div class="text-sm font-semibold text-slate-800">Obstetrician & Gynaecologist</div>
                        <div class="flex items-center space-x-2 text-xs text-amber-700 font-medium mt-1">
                            <span class="size-2 rounded-full bg-amber-500"></span>
                            <span>Available on-call from 2:00 PM</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 pt-1">
                        Source: hospital duty roster · Updated 12 minutes ago
                    </p>
                </div>

                <!-- Contact & Directions Overview -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-3">
                    <h2 class="text-lg font-bold text-slate-900">Facility Information</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p><strong class="text-slate-800 font-medium">Direct Hotline:</strong> {{ $hospital->phone }}</p>
                        <p><strong class="text-slate-800 font-medium">Full Address:</strong> {{ $hospital->address }}, {{ $hospital->state }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Bed Categories & Services -->
            <div class="space-y-6">
                <!-- Bed Availability Table -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">Bed Availability</h2>
                        <span class="text-xs text-slate-400">
                            Updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}
                        </span>
                    </div>

                    <div class="space-y-2 pt-1">
                        <div class="grid grid-cols-3 text-xs font-semibold text-slate-400 pb-2 border-b border-slate-100 uppercase tracking-wider">
                            <span>Category</span>
                            <span class="text-center">Available Beds</span>
                            <span class="text-right">Status</span>
                        </div>

                        @forelse($hospital->bedCategories as $bed)
                            <div class="grid grid-cols-3 text-sm items-center py-2.5 border-b border-slate-50">
                                <span class="font-bold text-slate-800">{{ $bed->name }}</span>
                                <span class="text-center font-semibold text-slate-700">{{ $bed->available_beds }}</span>
                                <div class="text-right">
                                    @if($bed->status === 'available')
                                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-semibold text-xs">
                                            <span class="size-2 rounded-full bg-emerald-500"></span>
                                            <span>Available</span>
                                        </span>
                                    @elseif($bed->status === 'limited')
                                        <span class="inline-flex items-center space-x-1.5 text-amber-700 font-semibold text-xs">
                                            <span class="size-2 rounded-full bg-amber-500"></span>
                                            <span>Limited</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 text-rose-600 font-semibold text-xs">
                                            <span class="size-2 rounded-full bg-rose-500"></span>
                                            <span>Full</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-3">No bed categories currently reported.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Services List -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900">Clinical Services</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($hospital->services as $srv)
                            <span class="rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-medium text-slate-700">
                                {{ $srv->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Disclaimer -->
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 text-center text-xs text-slate-500 leading-relaxed">
            Availability is reported directly by hospitals and may change rapidly. Always call before travelling when possible. In a life-threatening emergency, call emergency services immediately.
        </div>
    </div>
</main>
@endsection
