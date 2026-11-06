@extends('layouts.portal', ['title' => 'Hospital Services – MediSlot HMS'])

@section('portal_subtitle', 'Hospital Admin')

@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.departments') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
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
<a href="{{ route('admin.services') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-100 text-brand-700 transition">
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
<div class="space-y-6" x-data="{ addModal: false, activeEdit: null }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Hospital services</h1>
            <p class="text-xs text-slate-500 mt-1">Only services marked visible appear on the public finder</p>
        </div>
        <button @click="addModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Add service
        </button>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100 pb-2">
                        <th class="py-2.5">Service</th>
                        <th class="py-2.5">Department</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5">Emergency capable</th>
                        <th class="py-2.5">Public</th>
                        <th class="py-2.5">Updated</th>
                        <th class="py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($services as $srv)
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900">{{ $srv->name }}</td>
                            <td class="py-3.5 text-slate-600">{{ $srv->department?->name ?? '—' }}</td>
                            <td class="py-3.5">
                                @if($srv->status === 'available')
                                    <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Available</span>
                                    </span>
                                @elseif($srv->status === 'limited')
                                    <span class="inline-flex items-center space-x-1.5 text-amber-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Limited</span>
                                    </span>
                                @elseif($srv->status === 'unavailable')
                                    <span class="inline-flex items-center space-x-1.5 text-red-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        <span>Unavailable</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 text-slate-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Unknown</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-slate-700">{{ $srv->is_emergency_capable ? 'Yes' : 'No' }}</td>
                            <td class="py-3.5 text-slate-700">{{ $srv->is_public_visible ? 'Visible' : 'Hidden' }}</td>
                            <td class="py-3.5 text-slate-400">{{ $srv->last_updated_at?->diffForHumans(null, true) ?? 'Never' }} ago</td>
                            <td class="py-3.5 text-right">
                                <button
                                    @click="activeEdit = { id: {{ $srv->id }}, name: '{{ $srv->name }}', status: '{{ $srv->status }}', emergency: {{ $srv->is_emergency_capable ? 'true' : 'false' }}, visible: {{ $srv->is_public_visible ? 'true' : 'false' }} }"
                                    class="text-brand-700 font-semibold hover:underline"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div x-show="activeEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="activeEdit = null" class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Edit <span x-text="activeEdit?.name"></span></h3>
                <button @click="activeEdit = null" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/services/' + activeEdit?.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" :value="activeEdit?.status" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                        <option value="available">Available</option>
                        <option value="limited">Limited</option>
                        <option value="unavailable">Unavailable</option>
                        <option value="unknown">Unknown</option>
                    </select>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="flex items-center space-x-2 text-slate-700">
                        <input type="checkbox" name="is_emergency_capable" value="1" :checked="activeEdit?.emergency" class="rounded border-slate-300 text-brand-700">
                        <span>Emergency capable</span>
                    </label>

                    <label class="flex items-center space-x-2 text-slate-700">
                        <input type="checkbox" name="is_public_visible" value="1" :checked="activeEdit?.visible" class="rounded border-slate-300 text-brand-700">
                        <span>Visible on public finder</span>
                    </label>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="activeEdit = null" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Add Hospital Service</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Service name</label>
                    <input type="text" name="name" required placeholder="e.g. Neonatal Intensive Care" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-brand-700 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                        <option value="">None</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white focus:border-brand-700 outline-none">
                        <option value="available">Available</option>
                        <option value="limited">Limited</option>
                        <option value="unavailable">Unavailable</option>
                        <option value="unknown">Unknown</option>
                    </select>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="flex items-center space-x-2 text-slate-700">
                        <input type="checkbox" name="is_emergency_capable" value="1" class="rounded border-slate-300 text-brand-700">
                        <span>Emergency capable</span>
                    </label>

                    <label class="flex items-center space-x-2 text-slate-700">
                        <input type="checkbox" name="is_public_visible" value="1" checked class="rounded border-slate-300 text-brand-700">
                        <span>Visible on public finder</span>
                    </label>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg font-semibold shadow-sm">Add service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
