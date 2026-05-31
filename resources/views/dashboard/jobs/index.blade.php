@extends('layouts.dashboard')

@section('title', 'Jobs')
@section('page-title', 'Jobs')
@section('page-subtitle', 'Manage all operational jobs and their assignments')

@section('content')
<livewire:jobs.job-list />
@endsection
