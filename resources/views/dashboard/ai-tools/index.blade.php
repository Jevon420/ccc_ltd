@extends('layouts.dashboard')

@section('title', 'AI Tools')
@section('page-title', 'AI Tools')
@section('page-subtitle', 'AI-assisted drafting and summaries — powered by OpenAI via Laravel AI SDK')

@section('content')

{{-- Safety Notice --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-4">
    <div class="text-2xl flex-shrink-0">⚠️</div>
    <div>
        <p class="text-sm font-bold text-amber-900 mb-1">AI Safety Policy</p>
        <p class="text-xs text-amber-800 leading-relaxed">
            AI features in CCC Ops are <strong>assistive only</strong>. AI may draft, suggest, and summarise —
            but it must <strong>never</strong> autonomously approve quotes, mark payments, delete records, or
            assign roles without explicit human confirmation.
        </p>
    </div>
</div>

@php $aiEnabled = (bool) \App\Models\Setting::get('ai_features_enabled', false); @endphp

@if(!$aiEnabled)
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
    <div class="text-5xl mb-4">🤖</div>
    <h2 class="text-lg font-bold text-gray-900 mb-2">AI Features are Disabled</h2>
    <p class="text-sm text-gray-500 mb-5 max-w-md mx-auto">
        Enable AI features in Settings → AI Configuration to unlock quote drafting, email drafting, and report summaries.
    </p>
    @can('settings.edit')
    <a href="{{ route('dashboard.settings.index') }}"
        class="inline-flex items-center gap-2 bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
        Go to Settings → AI Configuration
    </a>
    @endcan
</div>
@else

{{-- Tool Tabs --}}
<div x-data="{ tool: 'quote' }" class="space-y-5">

    {{-- Tool Picker --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            @php
            $tools = [
                ['key' => 'quote',  'icon' => '📝', 'label' => 'Quote Drafter',     'setting' => 'ai_quote_drafting'],
                ['key' => 'email',  'icon' => '✉️',  'label' => 'Email Drafter',     'setting' => 'ai_email_drafting'],
                ['key' => 'report', 'icon' => '📊', 'label' => 'Report Summariser',  'setting' => 'ai_report_summaries'],
            ];
            @endphp

            @foreach($tools as $t)
            @if((bool) \App\Models\Setting::get($t['setting'], true))
            <button @click="tool = '{{ $t['key'] }}'"
                :class="tool === '{{ $t['key'] }}' ? 'border-b-2 border-blue-700 text-blue-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3.5 text-sm whitespace-nowrap transition-colors">
                {{ $t['icon'] }} {{ $t['label'] }}
            </button>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Quote Drafter --}}
    @if((bool) \App\Models\Setting::get('ai_quote_drafting', true))
    <div x-show="tool === 'quote'">
        <livewire:quotes.quote-drafter />
    </div>
    @endif

    {{-- Email Drafter --}}
    @if((bool) \App\Models\Setting::get('ai_email_drafting', true))
    <div x-show="tool === 'email'">
        <livewire:ai-tools.email-drafter />
    </div>
    @endif

    {{-- Report Summariser --}}
    @if((bool) \App\Models\Setting::get('ai_report_summaries', true))
    <div x-show="tool === 'report'">
        <livewire:ai-tools.report-summariser />
    </div>
    @endif

</div>
@endif

@endsection
