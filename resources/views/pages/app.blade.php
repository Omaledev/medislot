@extends('layouts.public')

@section('title', 'The MediSlot App - MediSlot')

@section('content')
<main>
    <section class="bg-[#effbf9]">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8">
            <div>
                <p class="mb-2 text-sm font-semibold text-teal-800">The MediSlot app</p>
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Find care. See what hospitals offer.</h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-600">Explore hospital information, reported services, and availability. MediSlot is a web application for now; a mobile app is planned.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('finder') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                        Find a Hospital
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 px-5 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                        Get in touch
                    </a>
                </div>
                <p class="mt-3 text-xs text-slate-500">Availability is reported by hospitals and may change. Always contact the hospital to confirm.</p>
            </div>

            <!-- Hospital Finder Preview Card -->
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-xl sm:p-6">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    MediSlot
                    <span class="ml-auto rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">Hospital finder</span>
                </div>
                <div class="mt-5 flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 px-3 text-sm text-slate-500">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Search hospital or service
                </div>
                <article class="mt-3 rounded-xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-slate-900">Riverside General Hospital</h2>
                            <p class="mt-1 text-sm text-slate-500">14 Marina Road · Lagos Island</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800">Reported</span>
                    </div>
                    <p class="mt-3 text-sm text-amber-800 font-medium">4 general beds available</p>
                    <p class="mt-1 text-sm text-emerald-700 font-medium">Accepting emergency patients</p>
                    <p class="mt-2 text-xs text-slate-500">Updated 3 minutes ago</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('finder') }}" class="inline-flex min-h-9 flex-1 items-center justify-center gap-2 rounded-lg bg-teal-700 text-sm font-semibold text-white">
                            View Facility
                        </a>
                        <a href="{{ route('finder') }}" class="inline-flex min-h-9 flex-1 items-center justify-center rounded-lg border border-slate-200 text-sm font-medium text-slate-700">
                            Directions
                        </a>
                    </div>
                </article>
                <article class="mt-3 rounded-xl border border-slate-200 p-4">
                    <h2 class="font-semibold text-slate-900">St. Anne Specialist Hospital</h2>
                    <p class="mt-1 text-sm text-slate-500">7 Hill Street · Victoria Island</p>
                    <p class="mt-2 text-xs text-slate-500">Hospital information preview</p>
                </article>
                <p class="mt-4 text-center text-xs text-slate-500">Illustrative preview · live hospital search is powered by verified facility data.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div class="mb-8 max-w-2xl">
            <p class="mb-2 text-sm font-semibold text-teal-700">What you can do</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Useful information, in one place.</h2>
            <p class="mt-3 text-base leading-relaxed text-slate-600">MediSlot connects people looking for care with hospitals that want to share trusted, timely information.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Discover nearby hospitals</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Browse facilities and get useful location and direction details.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Explore services</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">See the services and emergency information hospitals choose to share.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Check reported availability</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Review bed availability and its last updated time before you travel.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Manage hospital workflows</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Authorized teams can coordinate visits, patient records, labs, pharmacy, and beds.</p>
            </article>
        </div>
    </section>

    <section class="bg-slate-100">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-4 py-14 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">A hospital administrator?</h2>
                <p class="mt-2 text-sm text-slate-600">Create an account to set up your hospital on MediSlot.</p>
            </div>
            <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-teal-700 bg-white px-5 text-sm font-semibold text-teal-800 hover:bg-teal-50 transition">
                Register your hospital
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </section>
</main>
@endsection
