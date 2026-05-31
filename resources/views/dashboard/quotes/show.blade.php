@extends('layouts.dashboard')

@section('title', $quote->reference)
@section('page-title', $quote->reference)
@section('page-subtitle', $quote->title ?? 'Quote Details')

@section('content')
<livewire:quotes.quote-show :quote="$quote" />
@endsection
