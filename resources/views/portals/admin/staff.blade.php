@extends('layouts.portal', ['title' => 'Staff Accounts – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.departments') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Departments</span>
</a>
<a href="{{ route('admin.staff') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Staff</span>
</a>
<a href="{{ route('admin.beds') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Beds</span>
</a>
<a href="{{ route('admin.admissions') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Admissions</span>
</a>
<a href="{{ route('admin.services') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Services</span>
</a>
<a href="{{ route('admin.integrations') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>EHR Integrations</span>
</a>
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Staff</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $staff->count() }} accounts · each person has their own login</p>
        </div>
        <button @click="addModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Add staff
        </button>
    </div>

    <!-- Search and Filters -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search by name or email"
                class="w-full bg-white rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"
            >
        </div>

        <select name="role" onchange="this.form.submit()" class="bg-white rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:border-brand-700 outline-none">
            <option value="">All roles</option>
            <option value="hospital_admin" {{ request('role') === 'hospital_admin' ? 'selected' : '' }}>Hospital Admin</option>
            <option value="reception" {{ request('role') === 'reception' ? 'selected' : '' }}>Reception</option>
            <option value="consultant" {{ request('role') === 'consultant' ? 'selected' : '' }}>Consultant</option>
            <option value="lab_scientist" {{ request('role') === 'lab_scientist' ? 'selected' : '' }}>Laboratory</option>
            <option value="pharmacist" {{ request('role') === 'pharmacist' ? 'selected' : '' }}>Pharmacy</option>
        </select>

        <select name="department_id" onchange="this.form.submit()" class="bg-white rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:border-brand-700 outline-none">
            <option value="">All departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </form>

    <!-- Staff Table -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                        <th class="py-2.5">Name</th>
                        <th class="py-2.5">Email</th>
                        <th class="py-2.5">Role</th>
                        <th class="py-2.5">Department</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5">Created</th>
                        <th class="py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($staff as $s)
                        <tr>
                            <td class="py-3 font-bold text-slate-900">{{ $s->name }}</td>
                            <td class="py-3 text-slate-600">{{ $s->email }}</td>
                            <td class="py-3 text-slate-700 capitalize">{{ str_replace('_', ' ', $s->role) }}</td>
                            <td class="py-3 text-slate-600">{{ $s->department?->name ?? '—' }}</td>
                            <td class="py-3">
                                @if($s->status === 'active')
                                    <span class="inline-flex items-center space-x-1 text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Active</span>
                                    </span>
                                @elseif($s->status === 'invited')
                                    <span class="inline-flex items-center space-x-1 text-blue-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Invited</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 text-slate-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Deactivated</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-400">{{ $s->created_at->format('d M Y') }}</td>
                            <td class="py-3 text-right">
                                <span class="text-brand-700 font-semibold cursor-pointer hover:underline">Edit</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-[11px] text-slate-400 mt-4">New staff receive an email invitation to set their own password.</p>
    </div>

    <!-- Add Staff Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Add New Staff Member</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Full name</label>
                    <input type="text" name="name" required placeholder="Dr. Jane Doe" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" required placeholder="jane@hospital.example" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="0803 000 0000" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Role</label>
                        <select name="role" required class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="consultant">Consultant</option>
                            <option value="reception">Reception</option>
                            <option value="lab_scientist">Laboratory</option>
                            <option value="pharmacist">Pharmacy</option>
                            <option value="hospital_admin">Hospital Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Department</label>
                        <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                            <option value="">None / Admin</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Professional Title</label>
                    <input type="text" name="title" placeholder="Consultant Cardiologist / Lead Scientist" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Save staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
