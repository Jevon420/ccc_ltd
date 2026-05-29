@extends('layouts.dashboard')

@section('title', 'AI Tools')
@section('page-title', 'AI Tools')
@section('page-subtitle', 'AI-assisted drafting and summaries — powered by Prism PHP')

@section('content')

{{-- Safety Notice --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-4">
    <div class="text-2xl flex-shrink-0">⚠️</div>
    <div>
        <p class="text-sm font-bold text-amber-900 mb-1">AI Safety Policy</p>
        <p class="text-xs text-amber-800 leading-relaxed">
            AI features in CCC Ops are <strong>assistive only</strong>. AI may draft, suggest, and summarise — but it must
            <strong>never</strong> autonomously approve invoices, mark payments as paid, delete records, assign roles,
            or modify any critical data without explicit human confirmation.
        </p>
    </div>
</div>

@php $aiEnabled = \App\Models\Setting::get('ai_features_enabled', false); @endphp

@if(!$aiEnabled)
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
    <div class="text-5xl mb-4">🤖</div>
    <h2 class="text-lg font-bold text-gray-900 mb-2">AI Features are Disabled</h2>
    <p class="text-sm text-gray-500 mb-5 max-w-md mx-auto">
        Enable AI features in Settings to unlock AI-assisted quote drafting, report summaries, and email drafting.
        An Anthropic or OpenAI API key is required.
    </p>
    @can('settings.edit')
    <a href="{{ route('dashboard.settings.index') }}"
       class="inline-flex items-center gap-2 bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
        Go to Settings
    </a>
    @endcan
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    @php
    $tools = [
        ['icon' => '📝', 'title' => 'Quote Drafter',     'desc' => 'Generate a professional quote description from job details.', 'badge' => 'Phase 2'],
        ['icon' => '📊', 'title' => 'Report Summariser',  'desc' => 'Summarise long job reports into concise executive briefs.',   'badge' => 'Phase 2'],
        ['icon' => '✉️',  'title' => 'Email Drafter',     'desc' => 'Draft professional client emails from simple bullet points.', 'badge' => 'Phase 2'],
    ];
    @endphp
    @foreach($tools as $tool)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="text-3xl mb-3">{{ $tool['icon'] }}</div>
        <div class="flex items-start justify-between mb-2">
            <h3 class="text-sm font-bold text-gray-900">{{ $tool['title'] }}</h3>
            <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full">{{ $tool['badge'] }}</span>
        </div>
        <p class="text-xs text-gray-500 mb-4">{{ $tool['desc'] }}</p>
        <button disabled class="w-full text-xs font-semibold text-gray-400 border border-gray-200 py-2 rounded-lg cursor-not-allowed">
            Coming in Phase 2
        </button>
    </div>
    @endforeach
</div>
@endif

@endsection
