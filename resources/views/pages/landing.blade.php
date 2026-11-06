@extends('layouts.public', ['title' => 'MediSlot – Find the Right Care. Without the Guesswork.'])

@section('content')
<main>
    <!-- Hero Section -->
    <section class="bg-[#effbf9]">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8 lg:py-16">
            <div class="max-w-xl">
                <p class="mb-3 text-sm font-semibold text-teal-800">Hospital finder and hospital management</p>
                <h1 class="text-4xl font-bold leading-[1.08] tracking-tight text-slate-900 sm:text-5xl">Find the Right Care. Without the Guesswork.</h1>
                <p class="mt-5 max-w-lg text-base leading-relaxed text-slate-600">Discover hospitals, explore available healthcare services, and know where to go when you need care. MediSlot connects people with the healthcare information they need.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('finder') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                        Find a Hospital
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 px-5 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                        Register Hospital
                    </a>
                </div>
                <p class="mt-4 text-xs text-slate-500">Availability is reported by hospitals and may change.</p>
            </div>
            <div>
                <img
                    src="{{ asset('HomeIMG.png') }}"
                    alt="A doctor consulting a patient over video using MediSlot on a laptop"
                    class="w-full rounded-2xl shadow-[10px_10px_0_0_#c7f7ee] object-cover"
                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'grid min-h-64 place-items-center rounded-2xl border border-slate-200 bg-slate-200 p-6 text-center shadow-[10px_10px_0_0_#c7f7ee]\'><p class=\'text-sm font-semibold text-slate-700\'>MediSlot Connected Care</p></div>';"
                />
            </div>
        </div>
    </section>

    <!-- Information Section -->
    <section class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8">
        <div>
            <div class="mb-8 max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Healthcare Information That Helps You Make Better Decisions.</h2>
                <p class="mt-3 text-base leading-relaxed text-slate-600">MediSlot is built to make healthcare access easier by connecting people with hospitals and helping healthcare facilities manage their services more efficiently.</p>
            </div>
            <ul class="space-y-5">
                <li class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Find hospitals</strong>
                        <span class="mt-1 block text-sm leading-relaxed text-slate-600">Discover hospitals and healthcare facilities based on your location.</span>
                    </span>
                </li>
                <li class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Know what is available</strong>
                        <span class="mt-1 block text-sm leading-relaxed text-slate-600">View hospital services and availability information to help you decide where to seek care.</span>
                    </span>
                </li>
                <li class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Better hospital operations</strong>
                        <span class="mt-1 block text-sm leading-relaxed text-slate-600">MediSlot helps hospitals manage patient visits, laboratory requests, prescriptions, and pharmacy operations.</span>
                    </span>
                </li>
            </ul>
            <a href="{{ route('about') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-teal-700 hover:underline">
                Learn about MediSlot <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-teal-50/50 p-6 shadow-[10px_10px_0_0_#c7f7ee]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        MediSlot Care Network
                    </span>
                    <span class="text-xs bg-emerald-50 text-emerald-800 font-medium px-2.5 py-1 rounded-full">Live Updates</span>
                </div>
                <div class="space-y-3">
                    <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Riverside General Hospital</div>
                            <div class="text-xs text-slate-500">14 Marina Road · Lagos</div>
                        </div>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-100/60 px-2.5 py-1 rounded">Accepting Emergency</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-800">St. Anne Specialist Hospital</div>
                            <div class="text-xs text-slate-500">7 Hill Street · Victoria Island</div>
                        </div>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-100/60 px-2.5 py-1 rounded">Limited Beds</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Preview Section -->
    <section class="bg-slate-100">
        <div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-20 lg:px-8">
            <div class="mb-8 max-w-2xl mx-auto text-center">
                <p class="mb-2 text-sm font-semibold text-teal-700">Discover MediSlot</p>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Healthcare, Connected.</h2>
                <p class="mt-3 text-base leading-relaxed text-slate-600">From finding a hospital to connecting healthcare teams, MediSlot is designed to make healthcare information more accessible.</p>
            </div>

            <!-- Product Preview Component -->
            <div class="mx-auto grid max-w-4xl items-stretch justify-center gap-6 md:grid-cols-[minmax(0,1.8fr)_240px]">
                <div class="h-full overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm flex flex-col justify-between md:h-[290px]">
                    <div class="grid h-full grid-cols-[100px_1fr] sm:grid-cols-[130px_1fr]">
                        <aside class="border-r border-slate-200 p-4 text-xs">
                            <strong class="block pb-3 font-bold text-slate-900">MediSlot</strong>
                            <p class="mb-1 rounded px-2 py-1.5 bg-teal-50 text-teal-800 font-semibold">Beds</p>
                            <p class="mb-1 rounded px-2 py-1.5 text-slate-500">Admissions</p>
                            <p class="mb-1 rounded px-2 py-1.5 text-slate-500">Services</p>
                        </aside>
                        <div class="bg-slate-50/50 p-4 sm:p-5 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900">Bed management</h3>
                                <p class="mt-0.5 text-xs text-slate-500">Last updated 3 minutes ago</p>
                                <div class="mt-3.5 rounded-xl border border-slate-200 bg-white p-3 text-xs shadow-xs">
                                    <div class="mb-2 grid grid-cols-3 font-semibold text-slate-500">
                                        <span>Ward</span><span>Free</span><span>Status</span>
                                    </div>
                                    <div class="grid grid-cols-3 border-t border-slate-100 py-2 text-slate-700">
                                        <span class="font-medium">General Ward</span><span>4</span><span class="text-amber-700 font-semibold">Limited</span>
                                    </div>
                                    <div class="grid grid-cols-3 border-t border-slate-100 py-2 text-slate-700">
                                        <span class="font-medium">Maternity</span><span>3</span><span class="text-emerald-700 font-semibold">Available</span>
                                    </div>
                                    <div class="grid grid-cols-3 border-t border-slate-100 py-2 text-slate-700">
                                        <span class="font-medium">ICU</span><span>1</span><span class="text-amber-700 font-semibold">Limited</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phone Mockup -->
                <div class="mx-auto w-[230px] sm:w-[240px] overflow-hidden rounded-[2.5rem] border-[7px] border-slate-900 bg-white p-3 text-left shadow-md flex flex-col justify-start md:h-[290px]">
                    <div class="mx-auto mb-2 h-1.5 w-12 rounded-full bg-slate-900 shrink-0"></div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-800 shrink-0">
                        <svg class="w-3.5 h-3.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        MediSlot
                    </div>
                    <div class="mt-1.5 flex items-center gap-1.5 rounded-md border border-slate-200 px-2 py-1 text-[9px] text-slate-400 shrink-0">
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Search hospital or service
                    </div>
                    <div class="mt-2 rounded-lg border border-slate-200 p-2 text-[9px] shrink-0 bg-white">
                        <p class="font-bold text-slate-900">Riverside General Hospital</p>
                        <p class="mt-0.5 text-[8px] text-slate-500">14 Marina Road · 2.4 km</p>
                        <p class="mt-1 text-[8.5px] font-semibold text-amber-700">4 general beds available</p>
                        <p class="mt-0.5 text-[8.5px] font-semibold text-emerald-700">Accepting emergency patients</p>
                        <div class="mt-2 flex gap-1.5">
                            <span class="flex-1 rounded bg-teal-800 py-1 text-center text-white font-semibold text-[8px]">Call</span>
                            <span class="flex-1 rounded border border-slate-200 py-1 text-center text-slate-700 font-semibold text-[8px]">Directions</span>
                        </div>
                    </div>
                    <div class="mt-2 rounded-lg border border-slate-200 p-2 text-[9px] shrink-0 bg-white">
                        <p class="font-bold text-slate-900">St. Anne Specialist Hospital</p>
                        <p class="mt-0.5 text-[8px] text-slate-500">7 Hill Street · 4.1 km</p>
                    </div>
                </div>
            </div>
            </div>

            <div class="mx-auto mt-8 grid max-w-4xl gap-5 text-left sm:grid-cols-3">
                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-teal-700 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Find hospitals near you</strong>
                        <span class="mt-1 block text-sm text-slate-600">Search by location and care category.</span>
                    </span>
                </div>
                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-teal-700 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Explore hospital services</strong>
                        <span class="mt-1 block text-sm text-slate-600">See which services a hospital reports.</span>
                    </span>
                </div>
                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-teal-700 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Access hospital information</strong>
                        <span class="mt-1 block text-sm text-slate-600">Contact details, beds, and directions.</span>
                    </span>
                </div>
            </div>

            <a href="{{ route('finder') }}" class="mt-8 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                Explore the App
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
            <p class="mt-3 text-xs text-slate-500">MediSlot is a web application for now. A mobile app is planned.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="mx-auto grid max-w-7xl items-start gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2 lg:px-8">
        <div class="pt-2">
            <div class="mb-8 max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Let Us Make Healthcare More Accessible.</h2>
                <p class="mt-3 text-base leading-relaxed text-slate-600">Have a question, want to partner with MediSlot, or are interested in bringing MediSlot to your hospital? We would love to hear from you.</p>
            </div>
            <div class="space-y-4 text-sm">
                <p><strong class="block text-slate-900">General inquiries</strong><span class="text-slate-500">hello@medislot.ng</span></p>
                <p><strong class="block text-slate-900">Hospital partnerships</strong><span class="text-slate-500">partnerships@medislot.ng</span></p>
                <a href="{{ route('contact') }}" class="mt-2 inline-flex items-center gap-2 font-semibold text-teal-700 hover:underline">
                    Contact our team <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>

        <div>
            @if(session('success'))
                <div role="status" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">
                        Full name
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="fullName" required />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Email address
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="email" type="email" required />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Subject
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="subject" required />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Message
                        <textarea name="message" required rows="4" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"></textarea>
                    </label>
                </div>
                <button type="submit" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                    Send message
                </button>
            </form>
        </div>
    </section>
</main>
@endsection
