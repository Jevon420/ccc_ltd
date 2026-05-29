@extends('layouts.public')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center max-w-md px-4">
        <div class="text-8xl mb-6">🔍</div>
        <h1 class="text-6xl font-bold text-blue-700 mb-3">404</h1>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">Page Not Found</h2>
        <p class="text-gray-500 mb-8">The page you're looking for doesn't exist or has been moved.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 bg-blue-700 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
                Back to Home
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</div>
@endsection
