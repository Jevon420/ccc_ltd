@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

@section('content')

{{-- Role Badge --}}
<div class="mb-6 flex items-center justify-between">
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
        <div class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></div>
        {{ auth()->user()->getRoleNames()->first() }}
    </span>

    <button onclick="window.cccOps?.startTour('dashboard_intro')"
            class="text-xs text-gray-400 hover:text-blue-700 flex items-center gap-1 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Take a Tour
    </button>
</div>

{{-- Livewire Stats + Activity Component (auto-refreshes every 60s) --}}
<livewire:dashboard.stats-overview />

{{-- Quick Links & Info (below the Livewire component) --}}
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Quick Actions</h2>
        <div class="space-y-2">
            @can('settings.view')
            <a href="{{ route('dashboard.settings.index') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Company Settings</p>
                    <p class="text-xs text-gray-400">Update company info & features</p>
                </div>
            </a>
            @endcan

            @can('audit_logs.view')
            <a href="{{ route('dashboard.audit-log') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Audit Log</p>
                    <p class="text-xs text-gray-400">View all system activity</p>
                </div>
            </a>
            @endcan

            @can('system_health.view')
            <a href="{{ route('dashboard.system-health') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">System Health</p>
                    <p class="text-xs text-gray-400">Monitor app & infrastructure</p>
                </div>
            </a>
            @endcan

            <a href="{{ route('dashboard.tours') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Guided Tours</p>
                    <p class="text-xs text-gray-400">Replay onboarding walkthroughs</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Phase 2 Roadmap --}}
    <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl p-5 text-white">
        <div class="text-2xl mb-3">🚀</div>
        <p class="text-sm font-bold mb-2">Phase 2 — Coming Next</p>
        <ul class="text-xs text-blue-200 space-y-1.5">
            @foreach(['Clients', 'Service Types', 'Job Requests', 'Jobs & Work Orders', 'Quotes', 'Invoices', 'Payments (WiPay)', 'Equipment', 'Job Photos'] as $item)
            <li class="flex items-center gap-2">
                <div class="w-1 h-1 bg-blue-400 rounded-full flex-shrink-0"></div>
                {{ $item }}
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Current User --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Signed In As</h2>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-11 h-11 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                {{ auth()->user()->initials }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    {{ auth()->user()->getRoleNames()->first() }}
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-xs font-semibold text-red-600 border border-red-200 py-2 rounded-lg hover:bg-red-50 transition-colors">
                Sign Out
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
{{-- Pass tour data to driver.js --}}
@php
$tours = \App\Models\Tour::with('steps')->active()->get();
$toursJson = json_encode($tours->map(fn($t) => [
    'id'    => $t->id,
    'name'  => $t->name,
    'steps' => $t->steps->map(fn($s) => [
        'element' => $s->element,
        'popover' => [
            'title'       => $s->title,
            'description' => $s->description,
            'side'        => $s->placement,
        ],
    ])->values(),
])->values());
@endphp
<script>
window.cccTours = {!! $toursJson !!};
</script>
@endpush
