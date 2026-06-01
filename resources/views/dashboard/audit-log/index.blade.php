@extends('layouts.dashboard')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'All user and system activity — click any row to see full details')

@section('content')
<livewire:audit-log.audit-log-table />
@endsection
