@extends('layouts.base')

@section('body')
<div class="min-h-full flex flex-col lg:flex-row">
    <!-- Left Hero Brand Panel -->
    <div class="lg:w-5/12 bg-brand-700 text-white p-8 lg:p-16 flex flex-col justify-between flex-shrink-0">
        <div>
            <a href="{{ route('home') }}">
                <x-logo :light="true" size="lg" />
            </a>
        </div>

        <div class="mt-12 lg:mt-0">
            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-white mb-4 leading-tight">
                Better Hospital Management Starts Here.
            </h1>
            <p class="text-brand-100 text-base lg:text-lg leading-relaxed max-w-md">
                Manage patient visits, laboratory, pharmacy and bed availability securely, from one place.
            </p>
        </div>

        <div class="text-xs text-brand-200 mt-8 hidden lg:block">
            &copy; {{ date('Y') }} MediSlot. Connected healthcare systems.
        </div>
    </div>

    <!-- Right Form Content -->
    <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-16 bg-white overflow-y-auto">
        <div class="w-full max-w-xl mx-auto">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
@endsection
