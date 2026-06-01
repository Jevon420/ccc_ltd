@extends('layouts.dashboard')

@section('title', $client->name)
@section('page-title', $client->name)
@section('page-subtitle', ucfirst($client->type).' · '.($client->city ?? 'Trinidad & Tobago'))

@section('content')
<livewire:clients.client-show :client="$client" />
@endsection
