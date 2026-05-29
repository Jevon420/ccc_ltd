@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Company configuration and feature flags')

@section('content')

<livewire:settings.settings-form />

@endsection
