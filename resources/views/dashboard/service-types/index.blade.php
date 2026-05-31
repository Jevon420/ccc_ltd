@extends('layouts.dashboard')

@section('title', 'Service Types')
@section('page-title', 'Service Types')
@section('page-subtitle', 'Manage the service catalogue used across jobs and quotes')

@section('content')
<livewire:service-types.service-type-list />
@endsection
