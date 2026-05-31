@extends('layouts.dashboard')

@section('title', 'Payments')
@section('page-title', 'Payments')
@section('page-subtitle', 'Record and track all payments received')

@section('content')
<livewire:payments.payment-list />
@endsection
