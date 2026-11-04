@extends('layouts.portal', ['title' => 'Patient Workspace – Dr. ' . $user->name])

@section('portal_subtitle', 'Consultant')

@section('sidebar_nav')
<a href="{{ route('consultant.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('consultant.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>My Patients</span>
</a>
<a href="{{ route('consultant.history') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Consultation History</span>
</a>
<a href="{{ route('consultant.lab-requests') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Laboratory Requests</span>
</a>
<a href="{{ route('consultant.prescriptions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Prescriptions</span>
</a>
<a href="{{ route('consultant.messages') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Messages</span>
</a>
<a href="{{ route('consultant.profile') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Profile & Settings</span>
</a>
@endsection

@section('content')
<div class="space-y-6" x-data="{ orderLabModal: false, newRxModal: false }">
    <!-- Back & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('consultant.dashboard') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Queue
        </a>

        <div class="flex items-center space-x-2">
            <button @click="orderLabModal = true" class="px-3.5 py-1.5 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 text-xs font-semibold transition">
                + Order Lab Test
            </button>
            <button @click="newRxModal = true" class="px-3.5 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-100 text-xs font-semibold transition">
                + Prescribe Medication
            </button>
        </div>
    </div>

    <!-- Patient Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-xl flex-shrink-0">
                    {{ substr($visit->patient?->first_name ?? 'P', 0, 1) }}{{ substr($visit->patient?->last_name ?? 'T', 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-xl font-bold text-slate-900">{{ $visit->patient?->full_name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700">
                            {{ $visit->patient?->patient_id }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $visit->priority === 'Emergency' ? 'bg-red-50 text-red-700' : ($visit->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                            {{ $visit->priority }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-2">
                        <span><strong>Gender:</strong> {{ $visit->patient?->gender }}</span>
                        <span>·</span>
                        <span><strong>Age:</strong> {{ $visit->patient?->age ?? 'N/A' }}</span>
                        <span>·</span>
                        <span><strong>Blood Group:</strong> {{ $visit->patient?->blood_group ?? 'O+' }}</span>
                        <span>·</span>
                        <span><strong>Phone:</strong> {{ $visit->patient?->phone ?? 'None' }}</span>
                        <span>·</span>
                        <span><strong>Visit No:</strong> {{ $visit->visit_number }}</span>
                    </div>
                </div>
            </div>

            <!-- Allergy Banner -->
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center space-x-3 text-red-800 text-xs">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <span class="font-bold">Allergies:</span>
                    <span>{{ $visit->patient?->allergies ?? 'Penicillin, NSAIDs (reported)' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Sub-tabs Navigation -->
    <div class="border-b border-slate-200">
        <nav class="flex space-x-8 -mb-px text-xs font-semibold">
            <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'overview']) }}" class="py-3 px-1 border-b-2 transition {{ $activeTab === 'overview' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Overview & Vitals
            </a>
            <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'history']) }}" class="py-3 px-1 border-b-2 transition {{ $activeTab === 'history' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Medical History
            </a>
            <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'notes']) }}" class="py-3 px-1 border-b-2 transition {{ $activeTab === 'notes' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Consultation Notes
            </a>
            <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'laboratory']) }}" class="py-3 px-1 border-b-2 transition {{ $activeTab === 'laboratory' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Laboratory ({{ $visit->labRequests->count() }})
            </a>
            <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'prescriptions']) }}" class="py-3 px-1 border-b-2 transition {{ $activeTab === 'prescriptions' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Prescriptions ({{ $visit->prescriptions->count() }})
            </a>
        </nav>
    </div>

    <!-- TAB 1: OVERVIEW & VITALS -->
    @if($activeTab === 'overview')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vitals & Triage -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Current Visit Vitals</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Blood Pressure</span>
                        <span class="font-bold text-slate-800">{{ $visit->patient?->blood_pressure ?? '120/80 mmHg' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Heart Rate</span>
                        <span class="font-bold text-slate-800">{{ $visit->patient?->heart_rate ?? '76 bpm' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Temperature</span>
                        <span class="font-bold text-slate-800">{{ $visit->patient?->temperature ?? '36.8 °C' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Respiratory Rate</span>
                        <span class="font-bold text-slate-800">18 breaths/min</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">SpO2 (Oxygen)</span>
                        <span class="font-bold text-emerald-700">98%</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Weight & Height</span>
                        <span class="font-bold text-slate-800">68 kg · 165 cm</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Triage & Reason</h3>
                <div class="text-xs text-slate-600 space-y-2">
                    <p><strong>Chief Complaint:</strong> {{ $visit->reason_for_visit }}</p>
                    <p><strong>Visit Type:</strong> {{ $visit->visit_type }}</p>
                    <p><strong>Arrived At:</strong> {{ $visit->arrived_at?->format('h:i A (d M Y)') ?? 'Today' }}</p>
                    <p><strong>Department:</strong> {{ $visit->department?->name }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Summary & Actions -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Current Consultation Progress</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $visit->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-brand-50 text-brand-700' }}">
                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                    </span>
                </div>

                @if($visit->consultationNote)
                    <div class="space-y-4 text-xs">
                        <div class="bg-slate-50 rounded-xl p-4">
                            <h4 class="font-semibold text-slate-900 mb-1">Assessment & Diagnosis</h4>
                            <p class="text-slate-700">{{ $visit->consultationNote->assessment_diagnosis ?? 'Pending documentation' }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <h4 class="font-semibold text-slate-900 mb-1">Treatment Plan</h4>
                            <p class="text-slate-700">{{ $visit->consultationNote->treatment_plan ?? 'Pending documentation' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500 mb-4">No consultation notes have been recorded yet for this visit.</p>
                    <a href="{{ route('consultant.workspace', ['visit' => $visit, 'tab' => 'notes']) }}" class="inline-flex items-center px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                        Start Clinical Note
                    </a>
                @endif
            </div>

            <!-- Recent Lab Orders & Prescriptions Quick Preview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Lab Tests ({{ $visit->labRequests->count() }})</h4>
                    <div class="space-y-2">
                        @forelse($visit->labRequests as $lr)
                            <div class="text-xs border-b border-slate-100 pb-2 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $lr->test_name }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $lr->request_number }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $lr->status === 'submitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $lr->status)) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No laboratory tests requested yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Prescriptions ({{ $visit->prescriptions->count() }})</h4>
                    <div class="space-y-2">
                        @forelse($visit->prescriptions as $rx)
                            <div class="text-xs border-b border-slate-100 pb-2 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $rx->rx_number }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $rx->items->count() }} item(s)</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $rx->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $rx->status)) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No prescriptions created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 2: MEDICAL HISTORY -->
    @if($activeTab === 'history')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4">Patient Longitudinal Record</h3>
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Past Medical History & Chronic Conditions</h4>
                    <p class="text-xs text-slate-700">{{ $visit->patient?->medical_history ?? 'No prior chronic conditions recorded in primary health registry.' }}</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Previous Hospital Encounters</h4>
                    <div class="space-y-3">
                        @forelse($visit->patient?->visits ?? [] as $prevVisit)
                            <div class="border-b border-slate-200 pb-2 text-xs flex justify-between items-start">
                                <div>
                                    <span class="font-bold text-slate-900">{{ $prevVisit->visit_number }}</span> · <span class="text-slate-500">{{ $prevVisit->arrived_at?->format('M d, Y') }}</span>
                                    <p class="text-slate-600 mt-0.5">{{ $prevVisit->reason_for_visit }} (Dr. {{ $prevVisit->clinician?->name ?? 'Consultant' }})</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200 text-slate-700">{{ ucfirst($prevVisit->status) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">First documented visit at this hospital facility.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 3: CONSULTATION NOTES -->
    @if($activeTab === 'notes')
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Clinical Documentation (SOAP)</h3>
                <p class="text-xs text-slate-500">Record structured clinical observations, diagnosis, and management directives.</p>
            </div>
            @if($visit->consultationNote?->status === 'finalized')
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Finalized on {{ $visit->consultationNote->finalized_at?->format('M d, Y H:i') }}
                </span>
            @endif
        </div>

        <form action="{{ route('consultant.workspace.notes', $visit) }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Chief Complaint</label>
                    <textarea name="chief_complaint" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none">{{ old('chief_complaint', $visit->consultationNote?->chief_complaint ?? $visit->reason_for_visit) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">History of Present Illness (HPI)</label>
                    <textarea name="history_of_present_illness" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none">{{ old('history_of_present_illness', $visit->consultationNote?->history_of_present_illness) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Relevant Medical History</label>
                    <textarea name="relevant_medical_history" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none">{{ old('relevant_medical_history', $visit->consultationNote?->relevant_medical_history) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Examination Findings (Physical / Systems)</label>
                    <textarea name="examination_findings" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none">{{ old('examination_findings', $visit->consultationNote?->examination_findings) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assessment / Primary Diagnosis</label>
                    <textarea name="assessment_diagnosis" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none" placeholder="e.g. Acute bacterial sinusitis, uncomplicated hypertension">{{ old('assessment_diagnosis', $visit->consultationNote?->assessment_diagnosis) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Treatment Plan & Medication Strategy</label>
                    <textarea name="treatment_plan" rows="3" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none">{{ old('treatment_plan', $visit->consultationNote?->treatment_plan) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Follow-up Instructions & Patient Counseling</label>
                    <textarea name="follow_up_instructions" rows="2" class="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-brand-700 outline-none" placeholder="Review in clinic after 5 days or if symptoms worsen">{{ old('follow_up_instructions', $visit->consultationNote?->follow_up_instructions) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="submit" name="action_type" value="draft" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold shadow-sm transition">
                    Save Draft Notes
                </button>
                <button type="submit" name="action_type" value="complete" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    Finalize & Complete Consultation
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- TAB 4: LABORATORY -->
    @if($activeTab === 'laboratory')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Laboratory Investigations for this Visit</h3>
            <button @click="orderLabModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                + Order New Test
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Req Number</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Test Name</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Result / Value</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($visit->labRequests as $req)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $req->request_number }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $req->category }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $req->test_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $req->priority === 'Urgent' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $req->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $req->status === 'submitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-teal-50 text-teal-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($req->result)
                                        <span class="font-bold text-slate-900">{{ $req->result->result_value }} {{ $req->result->unit }}</span>
                                        @if($req->result->interpretation)
                                            <span class="block text-[10px] text-slate-500">({{ $req->result->interpretation }})</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Awaiting lab run</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $req->created_at->format('M d, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">No laboratory tests requested yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 5: PRESCRIPTIONS -->
    @if($activeTab === 'prescriptions')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Prescriptions for this Visit</h3>
            <button @click="newRxModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                + Prescribe Medication
            </button>
        </div>

        <div class="space-y-4">
            @forelse($visit->prescriptions as $rx)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                        <div>
                            <span class="font-bold text-slate-900 text-sm">{{ $rx->rx_number }}</span>
                            <span class="text-xs text-slate-400 ml-2">Ordered {{ $rx->created_at->format('M d, Y · H:i') }}</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $rx->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700' : ($rx->status === 'clarification_needed' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $rx->status)) }}
                        </span>
                    </div>

                    @if($rx->clarification_note)
                        <div class="my-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                            <strong>Pharmacist Note:</strong> {{ $rx->clarification_note }}
                        </div>
                    @endif

                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach($rx->items as $item)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2">
                                <div>
                                    <span class="font-bold text-slate-900 text-sm">{{ $item->medication_name }}</span>
                                    <span class="text-slate-500 ml-1">{{ $item->strength }} · {{ $item->dosage }}</span>
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        Route: {{ $item->route }} · Frequency: {{ $item->frequency }} · Duration: {{ $item->duration }}
                                    </div>
                                    @if($item->instructions)
                                        <div class="text-slate-600 italic text-[11px] mt-0.5">{{ $item->instructions }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-slate-800">Qty: {{ $item->quantity_prescribed }}</span>
                                    <div class="text-[10px] text-slate-400">Dispensed: {{ $item->quantity_dispensed }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                    No medications prescribed yet for this consultation.
                </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- MODAL: ORDER LAB TEST -->
    <div x-show="orderLabModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="orderLabModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Order Laboratory Test</h3>
                <button @click="orderLabModal = false" class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>

            <form action="{{ route('consultant.workspace.lab-requests', $visit) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Laboratory Category</label>
                    <select name="category" required class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                        <option value="Hematology">Hematology (FBC, ESR, Genotype)</option>
                        <option value="Chemical Pathology">Chemical Pathology (E/U/Cr, LFT, Lipid)</option>
                        <option value="Microbiology">Microbiology (Urinalysis, Culture, Stool)</option>
                        <option value="Parasitology">Parasitology (Malaria parasite, Blood film)</option>
                        <option value="Serology">Serology / Immunology (Widal, Hepatitis, HIV)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Test Name</label>
                    <input type="text" name="test_name" required placeholder="e.g. Full Blood Count (FBC)" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Priority</label>
                    <select name="priority" required class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                        <option value="Routine">Routine</option>
                        <option value="Urgent">Urgent / STAT</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Clinical Reason / Suspected Diagnosis</label>
                    <textarea name="clinical_reason" rows="2" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none" placeholder="Suspected sepsis / evaluate fever"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Instructions for Lab Technician</label>
                    <input type="text" name="instructions" placeholder="Fasting sample required" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="orderLabModal = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-semibold shadow-sm">Submit Lab Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: PRESCRIBE MEDICATION -->
    <div x-show="newRxModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="newRxModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Prescribe Medication</h3>
                <button @click="newRxModal = false" class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>

            <form action="{{ route('consultant.workspace.prescriptions', $visit) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Medication Name</label>
                        <input type="text" name="medication_name" required placeholder="e.g. Amoxicillin / Clavulanic acid" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Strength</label>
                        <input type="text" name="strength" placeholder="625 mg" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Dosage</label>
                        <input type="text" name="dosage" required placeholder="1 tablet" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Route</label>
                        <select name="route" class="w-full rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-brand-700 outline-none">
                            <option value="Oral">Oral</option>
                            <option value="Intravenous (IV)">Intravenous (IV)</option>
                            <option value="Intramuscular (IM)">Intramuscular (IM)</option>
                            <option value="Subcutaneous">Subcutaneous</option>
                            <option value="Topical">Topical</option>
                            <option value="Inhalation">Inhalation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Frequency</label>
                        <input type="text" name="frequency" required placeholder="TDS (Three times daily)" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Duration</label>
                        <input type="text" name="duration" required placeholder="7 days" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Total Quantity</label>
                        <input type="number" name="quantity_prescribed" required min="1" value="21" class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>

                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Patient Instructions</label>
                        <input type="text" name="instructions" placeholder="Take after meals. Complete full antibiotic course." class="w-full rounded-xl border border-slate-200 p-2.5 focus:border-brand-700 outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="submit" name="submit_type" value="draft" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium">Save as Draft</button>
                    <button type="submit" name="submit_type" value="send" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-semibold shadow-sm">Send to Pharmacy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
