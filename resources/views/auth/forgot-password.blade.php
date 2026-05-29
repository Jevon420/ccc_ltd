@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Reset Password</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your email to receive a reset link.</p>

    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
        Password reset emails are configured in Phase 2. Please contact your administrator for now.
    </p>

    <a href="{{ route('login') }}" class="block w-full text-center bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
        ← Back to Login
    </a>
</div>
@endsection
