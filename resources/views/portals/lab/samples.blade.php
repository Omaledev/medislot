@extends('layouts.portal', ['title' => 'Sample Processing – MediSlot HMS'])

@section('portal_subtitle', 'Laboratory')

@section('sidebar_nav')
<a href="{{ route('lab.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('lab.requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Test Requests</span>
</a>
<a href="{{ route('lab.samples') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Sample Processing</span>
</a>
<a href="{{ route('lab.reports') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Results & Reports</span>
</a>
<a href="{{ route('lab.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('lab.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Specimen Processing Pipeline</h1>
            <p class="text-xs text-slate-500 mt-1">Track physical sample collection, phlebotomy verification, and bench analyzer runs.</p>
        </div>
    </div>

    <!-- Pipeline Stage Kanban Columns -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Stage 1: Awaiting Specimen -->
        <div class="bg-slate-100/70 rounded-2xl p-4 flex flex-col space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Awaiting Collection</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white text-slate-700">
                    {{ $samples->whereIn('status', ['new', 'accepted', 'awaiting_sample'])->count() }}
                </span>
            </div>
            <div class="space-y-3 overflow-y-auto max-h-[600px]">
                @forelse($samples->whereIn('status', ['new', 'accepted', 'awaiting_sample']) as $s)
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-2 text-xs">
                        <div class="flex justify-between items-start">
                            <span class="font-mono font-bold text-slate-900">{{ $s->request_number }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $s->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">{{ $s->priority }}</span>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $s->test_name }}</div>
                        <div class="text-slate-500">{{ $s->patient?->full_name }} ({{ $s->patient?->patient_id }})</div>
                        <form action="{{ route('lab.requests.advance', $s) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full py-1.5 bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 rounded-lg text-xs font-semibold transition">
                                Mark Sample Collected &rarr;
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">None awaiting sample</p>
                @endforelse
            </div>
        </div>

        <!-- Stage 2: Sample Collected -->
        <div class="bg-slate-100/70 rounded-2xl p-4 flex flex-col space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sample Collected</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white text-slate-700">
                    {{ $samples->where('status', 'sample_collected')->count() }}
                </span>
            </div>
            <div class="space-y-3 overflow-y-auto max-h-[600px]">
                @forelse($samples->where('status', 'sample_collected') as $s)
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-2 text-xs">
                        <div class="flex justify-between items-start">
                            <span class="font-mono font-bold text-slate-900">{{ $s->request_number }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 text-teal-800">{{ $s->sample_type ?? 'Blood' }}</span>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $s->test_name }}</div>
                        <div class="text-slate-500">{{ $s->patient?->full_name }}</div>
                        <form action="{{ route('lab.requests.advance', $s) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full py-1.5 bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-100 rounded-lg text-xs font-semibold transition">
                                Begin Processing &rarr;
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">None in this stage</p>
                @endforelse
            </div>
        </div>

        <!-- Stage 3: In Processing -->
        <div class="bg-slate-100/70 rounded-2xl p-4 flex flex-col space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">In Processing</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white text-slate-700">
                    {{ $samples->where('status', 'processing')->count() }}
                </span>
            </div>
            <div class="space-y-3 overflow-y-auto max-h-[600px]">
                @forelse($samples->where('status', 'processing') as $s)
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-2 text-xs">
                        <div class="flex justify-between items-start">
                            <span class="font-mono font-bold text-slate-900">{{ $s->request_number }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700">Analyzing</span>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $s->test_name }}</div>
                        <div class="text-slate-500">{{ $s->patient?->full_name }}</div>
                        <form action="{{ route('lab.requests.advance', $s) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" class="w-full py-1.5 bg-brand-50 border border-brand-200 text-brand-800 hover:bg-brand-100 rounded-lg text-xs font-semibold transition">
                                Ready for Entry &rarr;
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">None currently processing</p>
                @endforelse
            </div>
        </div>

        <!-- Stage 4: Ready for Entry -->
        <div class="bg-slate-100/70 rounded-2xl p-4 flex flex-col space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ready for Entry</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white text-slate-700">
                    {{ $samples->where('status', 'result_entry')->count() }}
                </span>
            </div>
            <div class="space-y-3 overflow-y-auto max-h-[600px]">
                @forelse($samples->where('status', 'result_entry') as $s)
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-2 text-xs">
                        <div class="flex justify-between items-start">
                            <span class="font-mono font-bold text-slate-900">{{ $s->request_number }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Done</span>
                        </div>
                        <div class="font-semibold text-slate-800">{{ $s->test_name }}</div>
                        <div class="text-slate-500">{{ $s->patient?->full_name }}</div>
                        <a href="{{ route('lab.requests.show', $s) }}" class="block text-center w-full py-1.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                            Enter & Authorize
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">None pending entry</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
