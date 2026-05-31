<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard') — CCC Ops</title>
    <link rel="icon" href="{{ asset('images/ccc_ops_logo.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('images/ccc_ops_logo.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 text-gray-900" x-data="{ sidebarOpen: false, sidebarCollapsed: false }" data-route="{{ Route::currentRouteName() }}">

{{-- ============================== SIDEBAR ============================== --}}
{{-- Mobile overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 z-30 bg-black/50 md:hidden" x-transition:enter="transition ease-in-out duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

<aside id="app-sidebar"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'w-64': !sidebarCollapsed, 'w-16': sidebarCollapsed}"
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-slate-900 transition-all duration-300 ease-in-out md:translate-x-0">

    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
            <img src="{{ asset('images/ccc_ops_logo.png') }}"
                 alt="CCC Ops"
                 class="w-8 h-8 object-contain rounded-lg flex-shrink-0"
                 width="32" height="32" />
            <div x-show="!sidebarCollapsed" class="min-w-0">
                <div class="text-white text-xs font-bold leading-tight truncate">CCC Ops</div>
                <div class="text-slate-400 text-[10px] leading-tight truncate">Operations Portal</div>
            </div>
        </a>
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:flex text-slate-400 hover:text-white p-1 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 6l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">

        @php
            $navItems = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'permission' => 'dashboard.view'],
                // Phase 2 items — uncomment as modules are built
                // ['route' => 'dashboard.clients.index', 'label' => 'Clients', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'permission' => 'clients.view'],
            ];
        @endphp

        {{-- Dashboard --}}
        <x-sidebar-link route="dashboard" label="Dashboard" permission="dashboard.view" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </x-slot:icon>
        </x-sidebar-link>

        {{-- Settings --}}
        @can('settings.view')
        <x-sidebar-link route="dashboard.settings.index" label="Settings" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endcan

        {{-- Audit Log --}}
        @can('audit_logs.view')
        <x-sidebar-link route="dashboard.audit-log" label="Audit Log" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endcan

        {{-- System Health --}}
        @can('system_health.view')
        <x-sidebar-link route="dashboard.system-health" label="System Health" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endcan

        {{-- AI Tools --}}
        @can('ai_tools.view')
        <x-sidebar-link route="dashboard.ai-tools" label="AI Tools" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endcan

        {{-- Guided Tours --}}
        <x-sidebar-link route="dashboard.tours" label="Guided Tours" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </x-slot:icon>
        </x-sidebar-link>

        {{-- Phase 2 Modules --}}
        <div x-show="!sidebarCollapsed" class="px-3 pt-4 pb-1">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Operations</p>
        </div>

        @can('users.view')
        <x-sidebar-link route="dashboard.users.index" label="Users" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endcan

        @can('clients.view')
        @if(Route::has('dashboard.clients.index'))
        <x-sidebar-link route="dashboard.clients.index" label="Clients" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan

        @can('jobs.view')
        @if(Route::has('dashboard.jobs.index'))
        <x-sidebar-link route="dashboard.jobs.index" label="Jobs" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan

        @can('quotes.view')
        @if(Route::has('dashboard.quotes.index'))
        <x-sidebar-link route="dashboard.quotes.index" label="Quotes" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan

        @can('invoices.view')
        @if(Route::has('dashboard.invoices.index'))
        <x-sidebar-link route="dashboard.invoices.index" label="Invoices" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan

        @can('payments.view')
        @if(Route::has('dashboard.payments.index'))
        <x-sidebar-link route="dashboard.payments.index" label="Payments" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan

        @can('equipment.view')
        @if(Route::has('dashboard.equipment.index'))
        <x-sidebar-link route="dashboard.equipment.index" label="Equipment" :collapsed="$sidebarCollapsed ?? false">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </x-slot:icon>
        </x-sidebar-link>
        @endif
        @endcan
    </nav>

    {{-- Sidebar Footer --}}
    <div class="border-t border-slate-700 p-3 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ auth()->user()->initials }}
            </div>
            <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ============================== MAIN CONTENT ============================== --}}
<div :class="{'md:ml-64': !sidebarCollapsed, 'md:ml-16': sidebarCollapsed}" class="flex flex-col min-h-screen transition-all duration-300">

    {{-- Top Navigation --}}
    <header class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm h-16 flex items-center px-4 md:px-6 gap-4">

        {{-- Mobile menu toggle --}}
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page Title --}}
        <div class="flex-1">
            <h1 class="text-sm font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-gray-500">@yield('page-subtitle', $companyName)</p>
        </div>

        {{-- Search placeholder --}}
        <div class="hidden md:flex items-center bg-gray-100 rounded-lg px-3 py-2 gap-2 w-64">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-xs text-gray-400">Search... (coming soon)</span>
        </div>

        {{-- Notifications --}}
        <button class="relative text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100" title="Notifications (Phase 2)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        {{-- Profile Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 p-1 rounded-lg hover:bg-gray-100">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ auth()->user()->initials }}
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ auth()->user()->getRoleNames()->first() }}
                    </span>
                </div>
                <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </a>
                @can('settings.view')
                <a href="{{ route('dashboard.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    Settings
                </a>
                @endcan
                <div class="border-t border-gray-100 mt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="m-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
            <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="m-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
            <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Page Content --}}
    <main class="flex-1 p-4 md:p-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 px-6 py-3">
        <p class="text-xs text-gray-400 text-center">
            CCC Ops — {{ $companyName }} &bull; {{ config('app.env') }} &bull; v{{ app()->version() }}
        </p>
    </footer>
</div>

<x-toast />
@livewireScripts
<script>
    // Bridge Livewire toast events to Alpine toast window event
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (data) => {
            window.dispatchEvent(new CustomEvent('toast', { detail: data[0] ?? data }));
        });
    });
</script>
<script>
    // Driver.js guided tour will be initialized here
    // window.cccTourData will be set by the controller
</script>
@stack('scripts')
</body>
</html>
