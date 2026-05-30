@extends('layouts.dashboard')

@section('title', 'New Quote')
@section('page-title', 'New Quote')
@section('page-subtitle', 'Use AI to draft a professional quote description from job details')

@section('content')

{{-- Safety Notice --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
    <div class="text-xl flex-shrink-0">⚠️</div>
    <p class="text-xs text-amber-800 leading-relaxed">
        <strong>AI Safety:</strong> The AI draft is a suggestion only. Review and edit before sending to the client.
        Pricing must be set manually — AI will never add amounts to quotes.
    </p>
</div>

<livewire:quotes.quote-drafter />

@endsection
