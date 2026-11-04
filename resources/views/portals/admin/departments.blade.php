@extends('layouts.portal', ['title' => 'Departments – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.departments') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
    <span>Departments</span>
</a>
<a href="{{ route('admin.staff') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Settings</span>
</a>
<a href="{{ route('admin.activity') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Activity log</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Departments</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $departments->count() }} departments · {{ $departments->where('status', 'active')->count() }} active</p>
        </div>
        <a href="#add-department" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Add department
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Departments Table -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                            <th class="py-2.5">Department</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Lead</th>
                            <th class="py-2.5">Staff</th>
                            <th class="py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($departments as $dept)
                            <tr>
                                <td class="py-3.5 font-bold text-slate-900">{{ $dept->name }}</td>
                                <td class="py-3.5">
                                    @if($dept->status === 'active')
                                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Active</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 text-slate-400 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Inactive</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-slate-600">{{ $dept->lead?->name ?? 'Not assigned' }}</td>
                                <td class="py-3.5 text-slate-600">{{ $dept->staff->count() }}</td>
                                <td class="py-3.5 text-right space-x-2">
                                    <form method="POST" action="{{ route('admin.departments.toggle', $dept) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="font-medium text-xs {{ $dept->status === 'active' ? 'text-red-600 hover:text-red-800' : 'text-emerald-700 hover:text-emerald-900' }}">
                                            {{ $dept->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Department Form -->
        <div id="add-department" class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Add department</h3>

            <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Department name</label>
                    <input type="text" name="name" required placeholder="e.g. Paediatrics" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Department type</label>
                    <select name="type" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                        <option value="clinical">Clinical</option>
                        <option value="diagnostic">Diagnostic</option>
                        <option value="support">Support</option>
                        <option value="administrative">Administrative</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Description (optional)</label>
                    <textarea name="description" rows="3" placeholder="Brief overview of department functions..." class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Department lead (optional)</label>
                    <select name="lead_user_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                        <option value="">Not assigned</option>
                        @foreach($staffMembers as $sm)
                            <option value="{{ $sm->id }}">{{ $sm->name }} ({{ $sm->title ?? $sm->role }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 focus:ring-1 focus:ring-brand-700 outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="reset" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Create department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
