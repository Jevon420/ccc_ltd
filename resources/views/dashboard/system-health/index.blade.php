@extends('layouts.dashboard')

@section('title', 'System Health')
@section('page-title', 'System Health')
@section('page-subtitle', 'Application status and infrastructure overview')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $statusCards = [
        ['label' => 'Database',      'value' => $health['db_status'],    'ok' => $health['db_status'] === 'connected', 'icon' => '🗄️'],
        ['label' => 'PHP Version',   'value' => $health['php_version'],  'ok' => true, 'icon' => '🐘'],
        ['label' => 'Laravel',       'value' => $health['app_version'],  'ok' => true, 'icon' => '🔴'],
        ['label' => 'Failed Jobs',   'value' => $health['failed_jobs'],  'ok' => $health['failed_jobs'] === 0, 'icon' => '⚙️'],
    ];
    @endphp

    @foreach($statusCards as $card)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl">{{ $card['icon'] }}</span>
            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full
                {{ $card['ok'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                <div class="w-1.5 h-1.5 rounded-full {{ $card['ok'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                {{ $card['ok'] ? 'OK' : 'Issue' }}
            </span>
        </div>
        <div class="text-sm font-bold text-gray-900">{{ $card['value'] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- App Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Application Details</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @php
            $details = [
                ['label' => 'Environment',    'value' => $health['app_env']],
                ['label' => 'Debug Mode',     'value' => $health['app_debug'] ? '⚠️ Enabled' : '✅ Disabled'],
                ['label' => 'Queue Driver',   'value' => $health['queue_driver']],
                ['label' => 'Cache Driver',   'value' => $health['cache_driver']],
                ['label' => 'Default Disk',   'value' => $health['storage_disk']],
                ['label' => 'App URL',        'value' => config('app.url')],
            ];
            @endphp
            @foreach($details as $detail)
            <div class="flex items-center justify-between px-6 py-3">
                <span class="text-xs text-gray-500">{{ $detail['label'] }}</span>
                <span class="text-xs font-medium text-gray-900">{{ $detail['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Monitoring Placeholders --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-900">Laravel Nightwatch</h2>
                <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full">Configuration Needed</span>
            </div>
            <p class="text-xs text-gray-500 mb-3">
                Laravel Nightwatch is installed. Connect your Nightwatch account to enable full monitoring.
            </p>
            <a href="https://nightwatch.laravel.com" target="_blank"
               class="text-xs text-blue-700 font-medium hover:underline">
                Configure Nightwatch →
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-900">Failed Jobs</h2>
                <span class="text-xs font-bold {{ $health['failed_jobs'] > 0 ? 'text-red-700' : 'text-green-700' }}">
                    {{ $health['failed_jobs'] }} failed
                </span>
            </div>
            <p class="text-xs text-gray-500">Queue driver: <strong>{{ $health['queue_driver'] }}</strong></p>
            @if($health['failed_jobs'] > 0)
            <p class="text-xs text-red-600 mt-2 font-medium">⚠️ Check <code>php artisan queue:failed</code> to investigate.</p>
            @else
            <p class="text-xs text-green-600 mt-2">✅ No failed jobs. Queue is healthy.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-2">Storage</h2>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Default disk</span>
                    <span class="font-medium text-gray-900">{{ config('filesystems.default') }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Cloud (Wasabi)</span>
                    <span class="font-medium {{ config('filesystems.disks.wasabi.key') ? 'text-green-700' : 'text-amber-700' }}">
                        {{ config('filesystems.disks.wasabi.key') ? '✅ Configured' : '⚠️ Missing key' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
