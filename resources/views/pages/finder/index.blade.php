@extends('layouts.public', ['title' => 'Find care. Know where to go. – MediSlot'])

@section('content')
<main class="min-h-screen bg-slate-50/50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-12">
        <!-- Hero Box with Search & Location -->
        <div class="rounded-3xl border border-slate-200 bg-[#effbf9]/50 p-6 sm:p-10 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-100/70 px-3 py-1 text-xs font-semibold text-teal-800">
                        <svg class="w-3.5 h-3.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0-9l4-4m-4 4l-4 4"></path></svg>
                        Hospital Bed & Service Finder
                    </span>
                    <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                        Find care. Know where to go.
                    </h1>
                    <p class="mt-3 text-base text-slate-600 max-w-2xl leading-relaxed">
                        See which nearby hospitals report beds and services before you travel.
                    </p>
                </div>

                <!-- Location Box -->
                <div class="w-full lg:w-72 bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-xl bg-teal-50 text-teal-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </span>
                        <div>
                            <span class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider">Your location</span>
                            <strong class="block text-sm font-bold text-slate-800">Current location</strong>
                        </div>
                    </div>
                    <button type="button" class="text-sm font-semibold text-teal-800 hover:text-teal-900 hover:underline">
                        Change
                    </button>
                </div>
            </div>

            <!-- Search Form -->
            <form action="{{ route('finder.results') }}" method="GET" class="mt-8">
                <div class="flex items-center rounded-2xl border border-slate-200 bg-white p-2 shadow-sm focus-within:border-teal-700 focus-within:ring-2 focus-within:ring-teal-100 transition">
                    <svg class="ml-3 w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input
                        type="text"
                        name="q"
                        placeholder="Search hospital, service or specialty"
                        class="w-full bg-transparent px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                    />
                    <button type="submit" class="rounded-xl bg-teal-800 hover:bg-teal-900 px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition">
                        Search
                    </button>
                </div>
            </form>

            <!-- Popular tags -->
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span class="font-medium">Popular:</span>
                <a href="{{ route('finder.results', ['care_type' => 'General Emergency']) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:border-teal-700 hover:text-teal-700 transition">
                    General Emergency
                </a>
                <a href="{{ route('finder.results', ['care_type' => 'Maternity / Delivery']) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:border-teal-700 hover:text-teal-700 transition">
                    Maternity
                </a>
                <a href="{{ route('finder.results', ['q' => 'ICU']) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:border-teal-700 hover:text-teal-700 transition">
                    ICU Beds
                </a>
                <a href="{{ route('finder.results', ['q' => 'Riverside']) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1 font-medium text-slate-700 hover:border-teal-700 hover:text-teal-700 transition">
                    Riverside General
                </a>
            </div>
        </div>

        <!-- Care Categories Section -->
        <div>
            <div class="mb-6">
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    What care do you need?
                </h2>
                <p class="mt-1.5 text-sm text-slate-500">
                    Select a category to view reported capacity, bed allocation, and nearby hospitals.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Accident / Trauma -->
                <a href="{{ route('finder.results', ['care_type' => 'Accident / Trauma']) }}" class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-600 hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 16v3a2 2 0 01-2 2H7a2 2 0 01-2-2v-3m14 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v7m14 0H5m4-11V3h6v2M9 11h6m-3-3v6"></path></svg>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                            Accident / Trauma
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Injuries, fractures, road accidents
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-800">
                        <span>View reported hospitals</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- Maternity / Delivery -->
                <a href="{{ route('finder.results', ['care_type' => 'Maternity / Delivery']) }}" class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-600 hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                            Maternity / Delivery
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Labour and delivery care
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-800">
                        <span>View reported hospitals</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- General Emergency -->
                <a href="{{ route('finder.results', ['care_type' => 'General Emergency']) }}" class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-600 hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                            General Emergency
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Sudden illness or severe symptoms
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-800">
                        <span>View reported hospitals</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- Child Emergency -->
                <a href="{{ route('finder.results', ['care_type' => 'Child Emergency']) }}" class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-600 hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                            Child Emergency
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Care for infants and children
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-800">
                        <span>View reported hospitals</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- Other Care -->
                <a href="{{ route('finder.results', ['care_type' => 'Other Care']) }}" class="group flex flex-col justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-600 hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-teal-700 transition">
                            Other Care
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Services, clinics and specialists
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-teal-800">
                        <span>View reported hospitals</span>
                        <span>&rarr;</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- 3-Column Features Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex items-start gap-4">
                <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </span>
                <div>
                    <h4 class="font-bold text-slate-900">Bed Allocation Updates</h4>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Real-time counts of available beds across General, Maternity, ICU, and Paediatric wards.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <h4 class="font-bold text-slate-900">Emergency Readiness</h4>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Check if emergency wards are accepting patients before beginning your commute.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                </span>
                <div>
                    <h4 class="font-bold text-slate-900">Direct Navigation</h4>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        Direct phone access and instant map directions to hospital emergency entrances.
                    </p>
                </div>
            </div>
        </div>

        <!-- Emergency Notice -->
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 text-xs text-slate-600 leading-relaxed">
            <strong class="text-slate-800 font-semibold">Emergency Notice:</strong> Availability is reported by hospitals and may change rapidly. In a life-threatening emergency, call emergency services immediately and do not delay seeking care while using this application.
        </div>
    </div>
</main>
@endsection
