@extends('layouts.dashboard')

@section('title', $job->reference)
@section('page-title', $job->reference)
@section('page-subtitle', $job->title)

@section('content')
<livewire:jobs.job-show :job="$job" />
@endsection
