@extends('layouts.superadmin', ['title' => 'Global Platform Activity Stream'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Global Platform Activity
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Real-time audit stream of all clinical, administrative, and integration events across all tenant facilities.
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 shadow-xs">
            <span class="size-2 rounded-full bg-teal-500 animate-pulse"></span>
            <span>Total Events: {{ $activities->total() }}</span>
        </span>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
        <form action="{{ route('superadmin.activity') }}" method="GET" class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search activity description..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700"
                />
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select name="hospital_id" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-teal-700">
                    <option value="">All Hospitals</option>
                    @foreach($hospitalsList as $h)
                        <option value="{{ $h->id }}" {{ request('hospital_id') == $h->id ? 'selected' : '' }}>
                            {{ $h->name }}
                        </option>
                    @endforeach
                </select>

                <input
                    type="text"
                    name="action"
                    value="{{ request('action') }}"
                    placeholder="Action code (e.g. adt_, admit)"
                    class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-700 w-44"
                />

                <button type="submit" class="rounded-xl bg-teal-800 hover:bg-teal-900 px-5 py-2.5 text-xs font-semibold text-white transition">
                    Filter
                </button>

                @if(request()->hasAny(['q', 'hospital_id', 'action']))
                    <a href="{{ route('superadmin.activity') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Stream Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($activities as $act)
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 hover:bg-slate-50/60 transition">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($act->hospital)
                                <a href="{{ route('superadmin.hospitals.show', $act->hospital->id) }}" class="inline-flex items-center gap-1 font-bold text-xs text-slate-900 hover:text-teal-700">
                                    <span class="size-1.5 rounded-full bg-teal-500"></span>
                                    <span>{{ $act->hospital->name }}</span>
                                </a>
                            @else
                                <span class="font-bold text-xs text-slate-900 flex items-center gap-1">
                                    <span class="size-1.5 rounded-full bg-slate-400"></span>
                                    <span>Platform System</span>
                                </span>
                            @endif

                            <span class="font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded text-slate-700">
                                {{ $act->action }}
                            </span>

                            @if($act->user)
                                <span class="text-xs text-slate-400">by <strong class="text-slate-600 font-medium">{{ $act->user->name }}</strong></span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed">
                            {{ $act->description }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-[11px] font-medium text-slate-500 block">
                            {{ $act->created_at?->format('M d, Y · h:i A') }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">
                            {{ $act->created_at?->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-sm text-slate-400">
                    No activity logs recorded matching your filter.
                </div>
            @endforelse
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
