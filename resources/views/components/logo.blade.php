@props(['light' => false, 'size' => 'md'])

@php
    $iconSizes = [
        'sm' => 'w-6 h-6 text-xs',
        'md' => 'w-7 h-7 text-sm',
        'lg' => 'w-9 h-9 text-base',
    ];
    $textSizes = [
        'sm' => 'text-lg',
        'md' => 'text-xl',
        'lg' => 'text-2xl',
    ];
@endphp

<div class="flex items-center space-x-2.5 font-bold tracking-tight select-none">
    <div class="{{ $iconSizes[$size] ?? $iconSizes['md'] }} rounded-full flex items-center justify-center font-bold {{ $light ? 'bg-white text-brand-700' : 'bg-brand-700 text-white shadow-sm' }}">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
    </div>
    <span class="{{ $textSizes[$size] ?? $textSizes['md'] }} {{ $light ? 'text-white' : 'text-slate-900' }}">MediSlot</span>
</div>
