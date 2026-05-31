@extends('layouts.dashboard')

@section('title', 'Invoices')
@section('page-title', 'Invoices')
@section('page-subtitle', 'Track and manage all client invoices')

@section('content')
<livewire:invoices.invoice-list />
@endsection
