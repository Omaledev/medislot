@extends('layouts.portal', ['title' => 'Pharmacy Communications – MediSlot HMS'])

@section('portal_subtitle', 'Pharmacy')

@section('sidebar_nav')
<a href="{{ route('pharmacy.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('pharmacy.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('pharmacy.dispensing') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing Queue</span>
</a>
<a href="{{ route('pharmacy.dispensing-history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dispensing History</span>
</a>
<a href="{{ route('pharmacy.inventory') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Medication Inventory</span>
</a>
<a href="{{ route('pharmacy.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Messages</span>
</a>
<a href="{{ route('pharmacy.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Pharmacy Messages</h1>
        <p class="text-xs text-slate-500 mt-1">Direct inquiries with doctors regarding drug doses, dosage forms, and allergy alerts.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm grid grid-cols-1 md:grid-cols-3 min-h-[500px]">
        <!-- Thread List -->
        <div class="border-r border-slate-100 divide-y divide-slate-100">
            <div class="p-4 bg-slate-50/50 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Clarification Inquiries ({{ $threads->count() }})</span>
            </div>
            @forelse($threads as $t)
                <div class="p-4 hover:bg-slate-50 transition cursor-pointer {{ $loop->first ? 'bg-slate-50/80 border-l-4 border-brand-700' : '' }}">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-slate-900">{{ $t->sender?->name }}</span>
                        <span class="text-[10px] text-slate-400">{{ $t->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-xs font-medium text-slate-700 truncate">
                        Re: {{ class_basename($t->messageable_type) }} #{{ $t->messageable?->rx_number ?? $t->messageable?->request_number }}
                    </div>
                    <div class="text-[11px] text-slate-500 truncate mt-1">
                        {{ $t->message }}
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    No active inquiries.
                </div>
            @endforelse
        </div>

        <!-- Dialogue Pane -->
        <div class="md:col-span-2 flex flex-col justify-between">
            @if($activeThread)
                <div class="p-4 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900">
                            {{ class_basename($activeThread->messageable_type) }} #{{ $activeThread->messageable?->rx_number ?? $activeThread->messageable?->request_number }}
                            · Patient: {{ $activeThread->messageable?->patient?->full_name }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            From: {{ $activeThread->sender?->name }} ({{ ucfirst($activeThread->sender?->role) }})
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto flex-1">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ substr($activeThread->sender?->name ?? 'P', 0, 1) }}
                        </div>
                        <div class="max-w-md bg-slate-100 rounded-2xl rounded-tl-none p-3.5 text-xs text-slate-800">
                            <p class="font-semibold text-slate-900 mb-1">{{ $activeThread->sender?->name }}</p>
                            <p>{{ $activeThread->message }}</p>
                            <span class="block text-[10px] text-slate-400 mt-2">{{ $activeThread->created_at->format('M d, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100">
                    <form action="{{ route('pharmacy.messages.send') }}" method="POST" class="flex items-center space-x-3">
                        @csrf
                        <input type="hidden" name="messageable_type" value="{{ $activeThread->messageable_type }}">
                        <input type="hidden" name="messageable_id" value="{{ $activeThread->messageable_id }}">
                        <input type="text" name="message" required placeholder="Type reply to doctor..." class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-xs focus:border-brand-700 outline-none">
                        <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Send Reply
                        </button>
                    </form>
                </div>
            @else
                <div class="p-12 text-center text-xs text-slate-400 m-auto">
                    Select a clarification inquiry on the left to review details and respond.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
