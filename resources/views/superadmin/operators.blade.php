@extends('layouts.superadmin', ['title' => 'Platform Operators – MediSlot Super Admin'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Platform Super Admin Operators
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Manage founding team members with full administrative privileges across all MediSlot tenant systems.
            </p>
        </div>
    </div>

    <!-- 2-Column Split: Operators List & Add Operator Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Operators List -->
        <div class="lg:col-span-7 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Active Platform Operators ({{ $operators->count() }})
                    </h2>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($operators as $op)
                        <div class="p-4 sm:p-5 flex items-center justify-between text-xs hover:bg-slate-50/60 transition">
                            <div class="flex items-center gap-3">
                                <span class="grid size-9 place-items-center rounded-xl bg-teal-800 text-white font-bold text-xs">
                                    {{ $op->initials }}
                                </span>
                                <div>
                                    <strong class="block text-sm font-bold text-slate-900">{{ $op->name }}</strong>
                                    <span class="text-slate-500">{{ $op->email }}</span>
                                    @if($op->phone)
                                        <span class="text-slate-400 block text-[11px]">{{ $op->phone }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-[10px] font-bold text-teal-800 border border-teal-200">
                                    Superadmin
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-1">
                                    Added {{ $op->created_at?->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-400">
                            No operators found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add Operator Form -->
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Add Platform Operator</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Grant full Superadmin access to an engineering or operations team member.
                    </p>
                </div>

                @if($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('superadmin.operators.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="e.g. Alex Johnson"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="alex@medislot.ng"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number (Optional)</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+234 800 000 0000"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            placeholder="Minimum 8 characters"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700"
                        />
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-teal-800 hover:bg-teal-900 py-2.5 text-xs font-semibold text-white shadow-xs transition">
                        Create Superadmin Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
