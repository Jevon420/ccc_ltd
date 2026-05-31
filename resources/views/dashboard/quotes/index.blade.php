@extends('layouts.dashboard')

@section('title', 'Quotes')
@section('page-title', 'Quotes')
@section('page-subtitle', 'Manage client quotes and their approval status')

@section('content')
<livewire:quotes.quote-list />
@endsection
