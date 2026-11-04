@extends('layouts.public', ['title' => 'MediSlot – Hospital Finder and Hospital Management System'])

@section('content')
<div class="space-y-24 py-12">
    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-brand-700 text-sm font-semibold tracking-wide">
                    Hospital finder and hospital management
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 leading-[1.15]">
                    Find the Right Care.<br>Without the Guesswork.
                </h1>
                <p class="text-lg text-slate-600 leading-relaxed max-w-xl">
                    Discover hospitals, explore available healthcare services, and know where to go when you need care. MediSlot connects people with the healthcare information they need.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('finder') }}" class="px-6 py-3 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold shadow-sm transition">
                        Find a Hospital
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold transition">
                        Explore MediSlot HMS
                    </a>
                </div>
                <p class="text-xs text-slate-400">
                    Availability is reported by hospitals and may change.
                </p>
            </div>

            <div class="lg:col-span-5">
                <div class="relative bg-emerald-100/60 rounded-3xl p-4 sm:p-6 shadow-sm">
                    <div class="bg-slate-100 border border-slate-200/80 rounded-2xl h-96 flex flex-col items-center justify-center text-center p-8">
                        <div class="w-14 h-14 bg-white rounded-full shadow-sm flex items-center justify-center text-brand-700 mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="font-semibold text-slate-700 text-sm mb-1">Healthcare Excellence</h4>
                        <p class="text-xs text-slate-500 max-w-xs">Connecting Nigerian hospitals, consultants, laboratories, and pharmacies into one unified platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature 1 -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="about">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 leading-snug">
                    Healthcare Information That Helps You Make Better Decisions.
                </h2>
                <p class="text-base text-slate-600 leading-relaxed">
                    MediSlot is built to make healthcare access easier by connecting people with hospitals and helping healthcare facilities manage their services more efficiently.
                </p>

                <div class="space-y-6 pt-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-brand-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-base">Find Hospitals</h3>
                            <p class="text-sm text-slate-500 mt-1">Discover hospitals and healthcare facilities based on your location and care category.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-brand-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-base">Know What is Available</h3>
                            <p class="text-sm text-slate-500 mt-1">View hospital services, emergency status, and bed availability information in real time.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-brand-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-base">Better Hospital Operations</h3>
                            <p class="text-sm text-slate-500 mt-1">MediSlot helps hospitals manage patient visits, laboratory requests, prescriptions, and pharmacy operations through one connected system.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="bg-emerald-100/50 rounded-3xl p-6 sm:p-8">
                    <div class="bg-slate-100 border border-slate-200 rounded-2xl h-96 flex flex-col items-center justify-center text-center p-8">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-700 mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h4 class="font-semibold text-slate-800 text-sm">Empowering Clinicians & Patients</h4>
                        <p class="text-xs text-slate-500 max-w-xs mt-1">Transparent capacity and coordinated clinical records save lives.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Healthcare Connected -->
    <section class="bg-slate-100/60 py-20 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                Healthcare, Connected.
            </h2>
            <p class="text-base text-slate-600 max-w-2xl mx-auto">
                From finding a hospital to connecting healthcare teams, MediSlot is designed to make healthcare information more accessible.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-10 max-w-4xl mx-auto text-left">
                <!-- Bed Management Preview Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100 mb-4">
                        <div class="text-sm font-bold text-slate-800">Bed management</div>
                        <span class="text-xs text-slate-400">Last updated 3 minutes ago</span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                            <span class="font-medium text-slate-700">General Ward</span>
                            <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">4 free · Limited</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                            <span class="font-medium text-slate-700">Maternity</span>
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">3 free · Available</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="font-medium text-slate-700">ICU</span>
                            <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">1 free · Limited</span>
                        </div>
                    </div>
                </div>

                <!-- Finder Mobile Card Preview -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-3">
                        <x-logo size="sm" />
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/60 space-y-2">
                        <h4 class="font-bold text-slate-900 text-sm">Riverside General Hospital</h4>
                        <p class="text-xs text-slate-500">14 Marina Road · 2.4 km</p>
                        <div class="text-xs space-y-1 pt-1 font-medium">
                            <div class="text-amber-700 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>4 general beds available</span>
                            </div>
                            <div class="text-emerald-700 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Accepting emergency patients</span>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2">
                            <span class="text-center text-xs py-1.5 px-3 bg-brand-700 text-white font-medium rounded-lg">Call</span>
                            <span class="text-center text-xs py-1.5 px-3 border border-slate-300 text-slate-700 font-medium rounded-lg">Directions</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-10">
                <a href="{{ route('finder') }}" class="inline-flex px-6 py-3 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-semibold transition">
                    Explore the App
                </a>
                <p class="text-xs text-slate-400 mt-2">MediSlot is a web application accessible from any device.</p>
            </div>
        </div>
    </section>

    <!-- Contact & Hospital Partners -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" id="contact">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-5 space-y-6">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 leading-snug">
                    Let us Make Healthcare More Accessible.
                </h2>
                <p class="text-base text-slate-600 leading-relaxed">
                    Have a question, want to partner with MediSlot, or interested in bringing MediSlot to your hospital? We would love to hear from you.
                </p>
                <div class="space-y-4 pt-2 text-sm text-slate-600">
                    <div>
                        <div class="font-semibold text-slate-800">General inquiries</div>
                        <div class="text-brand-700">contact@medislot.example</div>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-800">Hospital partnerships</div>
                        <div class="text-brand-700">partners@medislot.example</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <form class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-4 shadow-sm" onsubmit="event.preventDefault(); alert('Thank you for reaching out! We will contact you shortly.')">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full name</label>
                        <input type="text" placeholder="Amina Yusuf" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
                        <input type="email" placeholder="amina@example.com" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject</label>
                        <input type="text" placeholder="Hospital partnership" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Message</label>
                        <textarea rows="4" placeholder="Tell us how we can help..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-brand-700 hover:bg-brand-800 text-white font-semibold rounded-lg transition text-sm">
                        Send message
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
