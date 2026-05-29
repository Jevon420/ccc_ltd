@extends('layouts.public')

@section('title', 'Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center max-w-md px-4">
        <div class="text-8xl mb-6">⚠️</div>
        <h1 class="text-6xl font-bold text-red-600 mb-3">500</h1>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">Server Error</h2>
        <p class="text-gray-500 mb-8">Something went wrong on our end. Please try again in a moment.</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 bg-blue-700 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
            Back to Home
        </a>
    </div>
</div>
@endsection
