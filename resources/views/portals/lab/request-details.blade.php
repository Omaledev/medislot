@extends('layouts.portal', ['title' => 'Request ' . $laboratoryRequest->request_number . ' – MediSlot Laboratory'])

@section('portal_subtitle', 'Laboratory')

@section('sidebar_nav')
<a href="{{ route('lab.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('lab.requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Test Requests</span>
</a>
<a href="{{ route('lab.samples') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">
                Request {{ $laboratoryRequest->request_number }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Diagnostic Result Entry · {{ $laboratoryRequest->test_name }} · {{ $laboratoryRequest->category }} · requested {{ $laboratoryRequest->created_at->format('d M, H:i') }} by {{ $laboratoryRequest->requester?->name ?? 'Consultant' }}
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-semibold {{ $laboratoryRequest->priority === 'Urgent' ? 'text-red-700' : 'text-slate-700' }}">
            <span class="size-2 rounded-full {{ $laboratoryRequest->priority === 'Urgent' ? 'bg-red-600' : 'bg-slate-500' }}"></span>
            {{ $laboratoryRequest->priority }}
        </span>
    </div>

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(300px,0.9fr)]">
        <!-- Left Side: Patient & Consultant details -->
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Patient</h2>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Name</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->patient?->full_name }}</dd>
                    </div>
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Patient ID</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->patient?->patient_id }}</dd>
                    </div>
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Age / Gender</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->patient?->age ? $laboratoryRequest->patient->age . ' yrs' : ($laboratoryRequest->patient?->date_of_birth?->format('d M Y') ?? '—') }} · {{ $laboratoryRequest->patient?->gender }}</dd>
                    </div>
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Visit</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->visit?->visit_number ?? 'Walk-in' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Request from consultant</h2>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Clinical reason</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->clinical_reason ?? 'None specified' }}</dd>
                    </div>
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Instructions</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->instructions ?? 'Standard protocol' }}</dd>
                    </div>
                    <div class="grid gap-2 py-2 sm:grid-cols-[120px_1fr]">
                        <dt class="font-medium text-slate-800">Consultant</dt>
                        <dd class="text-slate-600">{{ $laboratoryRequest->requester?->name ?? 'Dr. Assigned' }}</dd>
                    </div>
                </dl>
                <p class="mt-3 text-xs text-slate-400">Only the ordering consultant can modify clinical indication or test orders.</p>
            </div>
        </div>

        <!-- Right Side: Stages & Actions -->
        <div class="space-y-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-3">Progress stages</h2>
                @php
                    $stages = [
                        ['label' => 'Request accepted', 'key' => ['accepted', 'awaiting_sample', 'sample_collected', 'processing', 'result_entry', 'submitted']],
                        ['label' => 'Sample collected', 'key' => ['sample_collected', 'processing', 'result_entry', 'submitted']],
                        ['label' => 'Processing', 'key' => ['processing', 'result_entry', 'submitted']],
                        ['label' => 'Result entry', 'key' => ['result_entry', 'submitted']],
                        ['label' => 'Submitted to consultant', 'key' => ['submitted']],
                    ];
                @endphp
                <ol class="space-y-3">
                    @foreach($stages as $stage)
                        @php
                            $completed = in_array($laboratoryRequest->status, $stage['key']);
                        @endphp
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="flex items-center gap-2">
                                <span class="grid size-4 place-items-center rounded-full border {{ $completed ? 'border-teal-700 bg-teal-700' : 'border-slate-200 bg-slate-200' }}">
                                    @if($completed) <span class="size-1.5 rounded-full bg-white"></span> @endif
                                </span>
                                <span class="{{ $completed ? 'font-semibold text-slate-900' : 'text-slate-500' }}">{{ $stage['label'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                @if($laboratoryRequest->status === 'new')
                    <form action="{{ route('lab.requests.advance', $laboratoryRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                            Accept request
                        </button>
                    </form>
                @elseif($laboratoryRequest->status === 'awaiting_sample' || $laboratoryRequest->status === 'accepted')
                    <form action="{{ route('lab.requests.advance', $laboratoryRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                            Mark sample collected
                        </button>
                    </form>
                @elseif($laboratoryRequest->status === 'sample_collected')
                    <form action="{{ route('lab.requests.advance', $laboratoryRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                            Start processing
                        </button>
                    </form>
                @elseif(in_array($laboratoryRequest->status, ['processing', 'result_entry']))
                    <a href="{{ route('lab.requests.enter-result', $laboratoryRequest) }}" class="w-full inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-teal-800">
                        Enter result
                    </a>
                @elseif($laboratoryRequest->status === 'submitted')
                    <a href="{{ route('lab.reports.show', $laboratoryRequest) }}" class="w-full inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 px-5 text-sm font-semibold text-teal-800 hover:bg-teal-50">
                        View submitted report
                    </a>
                @endif

                <form method="POST" action="{{ route('lab.messages.send') }}" class="space-y-2 border-t border-slate-100 pt-3">
                    @csrf
                    <input type="hidden" name="messageable_type" value="LaboratoryRequest" />
                    <input type="hidden" name="messageable_id" value="{{ $laboratoryRequest->id }}" />
                    <label class="block text-sm font-medium text-slate-800">Message consultant</label>
                    <textarea name="message" required rows="2" placeholder="Message {{ $laboratoryRequest->requester?->name ?? 'consultant' }}..." class="block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"></textarea>
                    <button type="submit" class="w-full inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Send message to consultant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
