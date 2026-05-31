@extends('layouts.dashboard')

@section('title', 'Clients')
@section('page-title', 'Client Management')
@section('page-subtitle', 'Manage companies and individual clients')

@section('content')
<livewire:clients.client-list />
@endsection
