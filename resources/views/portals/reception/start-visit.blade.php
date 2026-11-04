@extends('layouts.portal', ['title' => 'Start New Visit – MediSlot Reception'])

@section('portal_subtitle', 'Reception')

@section('sidebar_nav')
<a href="{{ route('reception.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('reception.patients') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Patients</span>
</a>
<a href="{{ route('reception.queue') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Visits & Queue</span>
</a>
<a href="{{ route('reception.patients.create') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>New Patient</span>
</a>
<a href="{{ route('reception.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Bed Availability</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Start new visit</h1>
        <p class="text-xs text-slate-500 mt-1">The patient joins the department queue once the visit is created</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Visit Form -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('reception.visits.store') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Patient Selector Box -->
                <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-slate-400 font-medium block">Patient</span>
                        <select name="patient_id" class="bg-transparent font-bold text-slate-900 text-sm outline-none cursor-pointer">
                            @foreach($patients as $pt)
                                <option value="{{ $pt->id }}" {{ ($selectedPatient?->id == $pt->id) ? 'selected' : '' }}>
                                    {{ $pt->patient_id }} · {{ $pt->full_name }} ({{ $pt->gender }}, {{ $pt->age ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('reception.patients') }}" class="text-xs font-semibold text-brand-700 hover:underline">Change</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Visit type</label>
                        <select name="visit_type" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="Walk-in">Walk-in</option>
                            <option value="Appointment">Appointment</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reason for visit</label>
                    <textarea name="reason_for_visit" required rows="3" placeholder="e.g. Fever and headache for two days" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-brand-700 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Assigned clinician (optional)</label>
                        <select name="clinician_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="">Not assigned</option>
                            @foreach($clinicians as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->department?->name ?? 'Doctor' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Priority</label>
                        <select name="priority" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="Routine">Routine</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('reception.queue') }}" class="px-5 py-2.5 border border-slate-300 text-slate-600 rounded-lg font-semibold hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm transition">
                        Create visit
                    </button>
                </div>
            </form>
        </div>

        <!-- Emergency Alert Helper -->
        <div class="lg:col-span-4">
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 space-y-2">
                <h4 class="font-bold text-blue-900 text-sm">If the patient looks seriously unwell</h4>
                <p class="text-xs text-blue-800 leading-relaxed">
                    Alert clinical staff immediately. Reception does not judge medical urgency.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
