@extends('layouts.public')

@section('title', 'Contact MediSlot - MediSlot')

@section('content')
<main class="bg-slate-50 min-h-[calc(100vh-140px)]">
    <div class="mx-auto grid max-w-7xl items-start gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
        <div>
            <div class="mb-8 max-w-2xl">
                <p class="mb-2 text-sm font-semibold text-teal-700">Contact MediSlot</p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Let us make healthcare more accessible.</h1>
                <p class="mt-3 text-base leading-relaxed text-slate-600">Have a question, want to partner with MediSlot, or interested in bringing MediSlot to your hospital? Send us a message.</p>
            </div>

            <div class="space-y-5">
                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">General inquiries</strong>
                        <span class="mt-1 block text-sm text-slate-600">hello@medislot.ng</span>
                    </span>
                </div>

                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Hospital partnerships</strong>
                        <span class="mt-1 block text-sm text-slate-600">partnerships@medislot.ng</span>
                    </span>
                </div>

                <div class="flex gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <span>
                        <strong class="text-sm text-slate-900">Response time</strong>
                        <span class="mt-1 block text-sm text-slate-600">We respond to inquiries within 1-2 business days.</span>
                    </span>
                </div>
            </div>
        </div>

        <div>
            @if(session('success'))
                <div role="status" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">
                        Full name
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="fullName" value="{{ old('fullName') }}" type="text" required autocomplete="name" />
                    </label>

                    <label class="block text-sm font-medium text-slate-700">
                        Email address
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="email" value="{{ old('email') }}" type="email" required autocomplete="email" />
                    </label>

                    <label class="block text-sm font-medium text-slate-700">
                        Subject
                        <input class="mt-1.5 block min-h-11 w-full rounded-lg border border-slate-200 px-3 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100" name="subject" value="{{ old('subject') }}" type="text" required />
                    </label>

                    <label class="block text-sm font-medium text-slate-700">
                        Message
                        <textarea name="message" required rows="5" class="mt-1.5 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('message') }}</textarea>
                    </label>
                </div>

                <button type="submit" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Send message
                </button>
            </form>
        </div>
    </div>
</main>
@endsection
