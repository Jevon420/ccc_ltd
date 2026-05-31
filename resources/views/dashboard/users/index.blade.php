@extends('layouts.dashboard')

@section('title', 'Users')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage staff accounts and role assignments')

@section('content')
<livewire:users.user-list />
@endsection
