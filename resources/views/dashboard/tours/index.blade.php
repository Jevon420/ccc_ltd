@extends('layouts.dashboard')

@section('title', 'Guided Tours')
@section('page-title', 'Guided Tours')
@section('page-subtitle', 'Onboarding and feature walkthroughs')

@section('content')

@php
$tours = \App\Models\Tour::with('steps')->active()->orderBy('sort_order')->get();
$userProgress = auth()->user()->tourProgress()->with('tour')->get()->keyBy('tour_id');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($tours as $tour)
    @php
        $progress = $userProgress[$tour->id] ?? null;
        $status   = $progress?->status ?? 'not_started';
        $statusLabel = match($status) {
            'completed' => ['label' => 'Completed', 'color' => 'bg-green-100 text-green-700'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'bg-blue-100 text-blue-700'],
            'skipped'   => ['label' => 'Skipped', 'color' => 'bg-gray-100 text-gray-600'],
            default     => ['label' => 'Not Started', 'color' => 'bg-amber-100 text-amber-700'],
        };
    @endphp
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="text-2xl">🗺️</div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusLabel['color'] }}">
                {{ $statusLabel['label'] }}
            </span>
        </div>
        <h3 class="text-sm font-bold text-gray-900 mb-1">{{ $tour->title }}</h3>
        <p class="text-xs text-gray-500 mb-3">{{ $tour->description }}</p>
        <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
            <span>{{ $tour->steps->count() }} steps</span>
            @if($tour->route_name)
            <span class="font-mono text-gray-400">{{ $tour->route_name }}</span>
            @endif
        </div>
        <button onclick="window.location.href='{{ $tour->route_name ? route($tour->route_name) : '#' }}'"
                class="w-full text-xs font-semibold text-blue-700 border border-blue-200 py-2 rounded-lg hover:bg-blue-50 transition-colors">
            {{ $status === 'completed' ? 'Replay Tour' : 'Start Tour' }}
        </button>
    </div>
    @empty
    <div class="col-span-3 py-16 text-center">
        <div class="text-5xl mb-4">🗺️</div>
        <p class="text-sm font-semibold text-gray-700">No tours available</p>
        <p class="text-xs text-gray-400 mt-1">Guided tours will appear here as they are created.</p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
// Tour data for driver.js — populated when a tour is launched
window.cccTours = @json($tours->map(fn($t) => [
    'id'    => $t->id,
    'name'  => $t->name,
    'steps' => $t->steps->map(fn($s) => [
        'element'     => $s->element,
        'popover'     => [
            'title'       => $s->title,
            'description' => $s->description,
            'side'        => $s->placement,
        ],
    ])->values(),
])->values());
</script>
@endpush
