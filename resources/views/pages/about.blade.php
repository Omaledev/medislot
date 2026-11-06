@extends('layouts.public')

@section('title', 'About - MediSlot')

@section('content')
<main>
    <section class="bg-[#effbf9]">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8">
            <div>
                <p class="mb-2 text-sm font-semibold text-teal-800">About MediSlot</p>
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Healthcare information, connected.</h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-600">We are building a simpler way for people to find hospitals and for healthcare facilities to share useful service information and manage daily operations.</p>
            </div>
            <div class="grid min-h-64 place-items-center rounded-2xl border border-slate-200 bg-slate-200 p-6 text-center shadow-[10px_10px_0_0_#c7f7ee] sm:min-h-72">
                <div class="max-w-xs">
                    <svg class="mx-auto mb-2 text-slate-600 w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-sm font-semibold text-slate-700">Healthcare teams, working together</p>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">A licensed photograph showing healthcare professionals collaborating in a modern Nigerian hospital.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
        <div>
            <p class="mb-2 text-sm font-semibold text-teal-700">Our purpose</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Better access starts with better information.</h2>
            <p class="mt-3 text-base leading-relaxed text-slate-600">Finding the right facility can be difficult when service and availability information is hard to discover. MediSlot aims to make that information easier to access.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Make care easier to navigate</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Help people discover healthcare facilities and understand the services they report.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Keep information clear</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Show when availability was last updated so people can make informed decisions.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Support better operations</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Connect hospital teams with tools for visits, laboratory requests, prescriptions, and more.</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="grid size-11 place-items-center rounded-xl bg-teal-50 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">Built for people and care teams</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Public hospital information and practical hospital management tools work together in one platform.</p>
            </article>
        </div>
    </section>

    <section class="bg-slate-100">
        <div class="mx-auto flex max-w-7xl flex-col items-start gap-6 px-4 py-14 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Want to make healthcare more accessible?</h2>
                <p class="mt-2 text-sm text-slate-600">Learn about the app or talk to us about a hospital partnership.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('finder') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                    Explore the App
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
</main>
@endsection
