@extends('layouts.dashboard')

@section('title', $invoice->reference)
@section('page-title', $invoice->reference)
@section('page-subtitle', $invoice->title)

@section('content')
<livewire:invoices.invoice-show :invoice="$invoice" />
@endsection
