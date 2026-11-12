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

        @if($hospital->isEmergencyDiverting())
            <!-- Emergency Diversion Alert Banner -->
            <div class="rounded-3xl border-2 border-rose-300 bg-rose-50/90 p-6 sm:p-7 shadow-sm space-y-4">
                <div class="flex items-start gap-4">
                    <span class="grid size-12 place-items-center rounded-2xl bg-rose-100 text-rose-700 shrink-0">
                        <svg class="size-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div class="space-y-1.5 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-xl font-extrabold text-rose-900">
                                EMERGENCY INTAKE FULL · DIVERSION IN EFFECT
                            </h2>
                            <span class="rounded-full bg-rose-200/80 px-3 py-0.5 text-xs font-bold text-rose-800">
                                0 Emergency Bays
                            </span>
                        </div>
                        <p class="text-sm text-rose-800 leading-relaxed">
                            This hospital has <strong>no open emergency resuscitation bays</strong>. Do not travel here for acute general or trauma emergencies.
                        </p>
                        <div class="text-xs text-rose-900 bg-white/80 rounded-xl p-3 mt-2 border border-rose-200/90 leading-relaxed">
                            <strong>Why can't I use open beds in Maternity or Pediatrics?</strong><br>
                            Hospital beds are clinical ecosystems, not just furniture. Maternity beds are strictly reserved for labor, delivery, and postpartum mothers under infection control and midwifery regulations. They cannot receive adult trauma or acute medical resuscitations.
                        </div>
                    </div>
                </div>

                @if(!empty($nearestAlternative))
                    <div class="rounded-2xl bg-white p-4 sm:p-5 border border-emerald-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                        <div class="space-y-1">
                            <span class="text-xs uppercase font-extrabold text-emerald-800 tracking-wider">Nearest Alternative Emergency Facility</span>
                            <h3 class="text-base font-bold text-slate-900">{{ $nearestAlternative->name }}</h3>
                            <p class="text-xs text-slate-500">
                                {{ $nearestAlternative->address }} · <strong class="text-emerald-700">{{ $nearestAlternative->calculateEmergencyAvailableBeds() }} Emergency Beds Available</strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="tel:{{ $nearestAlternative->phone }}" class="rounded-xl bg-teal-800 hover:bg-teal-900 text-white text-xs font-semibold px-4 py-2.5 transition">
                                Call Hospital
                            </a>
                            <a href="{{ route('finder.hospital', ['hospital' => $nearestAlternative->slug, 'care_type' => $careType]) }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold px-4 py-2.5 transition">
                                View Details &rarr;
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- 2-Column Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Left Column: Emergency Readiness & Specialist -->
            <div class="space-y-6">
                <!-- Emergency Readiness -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-3">
                    <h2 class="text-lg font-bold text-slate-900">Emergency Readiness</h2>
                    @if($hospital->isEmergencyDiverting())
                        <div class="flex items-center space-x-2 text-sm font-semibold text-rose-700">
                            <span class="size-2.5 rounded-full bg-rose-500"></span>
                            <span>Emergency diversion in effect (0 beds open)</span>
                        </div>
                    @elseif($hospital->emergency_status === 'accepting')
                        <div class="flex items-center space-x-2 text-sm font-semibold text-emerald-700">
                            <span class="size-2.5 rounded-full bg-emerald-500"></span>
                            <span>Reports accepting emergency patients ({{ $hospital->calculateEmergencyAvailableBeds() }} bays available)</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 text-sm font-semibold text-amber-700">
                            <span class="size-2.5 rounded-full bg-amber-500"></span>
                            <span>Limited emergency intake</span>
                        </div>
                    @endif
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
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Bed Availability by Ward</h2>
                            <p class="text-xs text-slate-400">Classified by clinical admission capability</p>
                        </div>
                        <span class="text-xs text-slate-400">
                            Updated {{ $hospital->last_reported_at?->diffForHumans() ?? 'recently' }}
                        </span>
                    </div>

                    @php
                        $emergencyBedsList = $hospital->bedCategories->filter(fn ($b) => $b->is_emergency_intake);
                        $restrictedBedsList = $hospital->bedCategories->filter(fn ($b) => $b->isRestricted() && ! $b->is_emergency_intake);
                        $generalBedsList = $hospital->bedCategories->filter(fn ($b) => ! $b->isRestricted() && ! $b->is_emergency_intake);
                    @endphp

                    <!-- 1. Emergency & Resuscitation Bays -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-rose-500"></span>
                                Emergency & Acute Intake Bays
                            </span>
                            <span class="text-[11px] text-slate-400">Direct Emergency Intake</span>
                        </div>

                        @forelse($emergencyBedsList as $bed)
                            <div class="flex items-center justify-between py-2 border-b border-slate-50 text-sm">
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $bed->name }}</span>
                                    <span class="text-[11px] text-slate-500">Acute trauma & resuscitation</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-extrabold {{ $bed->available_beds > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $bed->available_beds }} available
                                    </span>
                                    <span class="block text-[11px] text-slate-400">of {{ $bed->total_beds }} bays</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-1">No emergency intake bays reported.</p>
                        @endforelse
                    </div>

                    <!-- 2. Specialized & Restricted Wards -->
                    @if($restrictedBedsList->isNotEmpty())
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="size-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Specialized / Restricted Wards
                                </span>
                                <span class="text-[11px] text-amber-700 font-medium">Ineligible for general emergency</span>
                            </div>

                            @foreach($restrictedBedsList as $bed)
                                <div class="rounded-xl border border-amber-200/70 bg-amber-50/30 p-3 space-y-1">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-bold text-slate-900">{{ $bed->name }}</span>
                                        <span class="font-semibold text-slate-700">{{ $bed->available_beds }} available</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-800">
                                        <span>🔒 {{ $bed->restriction_label ?? 'Restricted Admission' }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-tight">
                                        {{ $bed->restriction_warning }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- 3. General Inpatient Wards -->
                    @if($generalBedsList->isNotEmpty())
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    General Inpatient Units
                                </span>
                                <span class="text-[11px] text-slate-400">Step-down / Admitted Patients</span>
                            </div>

                            @foreach($generalBedsList as $bed)
                                <div class="flex items-center justify-between py-2 border-b border-slate-50 text-sm">
                                    <div>
                                        <span class="font-bold text-slate-800 block">{{ $bed->name }}</span>
                                        <span class="text-[11px] text-slate-400">Post-assessment admission only</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-slate-700">{{ $bed->available_beds }} available</span>
                                        <span class="block text-[11px] text-slate-400">of {{ $bed->total_beds }} beds</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
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
