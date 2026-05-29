@props(['route', 'label', 'collapsed' => false, 'permission' => null])

@php
    $active = request()->routeIs($route) || request()->routeIs($route.'.*');
    $shouldShow = $permission ? auth()->user()?->can($permission) : true;
@endphp

@if($shouldShow)
<a href="{{ route($route) }}"
   title="{{ $label }}"
   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
          {{ $active
            ? 'bg-blue-600 text-white shadow-sm'
            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {{ $icon }}
    </svg>
    @if(!$collapsed)
    <span class="truncate">{{ $label }}</span>
    @endif
</a>
@endif
