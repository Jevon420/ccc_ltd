@extends('layouts.dashboard')

@section('title', 'Equipment')
@section('page-title', 'Equipment')
@section('page-subtitle', 'Track company vehicles, machinery, tools, and PPE')

@section('content')
<livewire:equipment.equipment-list />
@endsection
