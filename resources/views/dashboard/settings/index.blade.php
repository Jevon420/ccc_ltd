@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Company configuration, feature flags and AI')

@section('content')

<div x-data="{ tab: 'general' }" class="space-y-5">

    {{-- Tab Bar --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button @click="tab = 'general'"
                :class="tab === 'general' ? 'border-b-2 border-blue-700 text-blue-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3.5 text-sm transition-colors">
                ⚙️ General
            </button>
            @can('settings.edit')
            <button @click="tab = 'ai'"
                :class="tab === 'ai' ? 'border-b-2 border-blue-700 text-blue-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3.5 text-sm transition-colors">
                🤖 AI Configuration
            </button>
            @endcan
        </div>
    </div>

    <div x-show="tab === 'general'">
        <livewire:settings.settings-form />
    </div>

    @can('settings.edit')
    <div x-show="tab === 'ai'">
        <livewire:settings.ai-settings />
    </div>
    @endcan

</div>

@endsection
