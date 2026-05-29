@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Create Account</h2>
    <p class="text-sm text-gray-500 mb-6">New staff accounts are created by an administrator.</p>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <p class="text-sm text-amber-800">
            Self-registration is not enabled. Please contact your system administrator to have an account created for you.
        </p>
    </div>

    <a href="{{ route('login') }}"
       class="block w-full text-center bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-blue-800 transition-colors">
        ← Back to Login
    </a>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to website
        </a>
    </div>
</div>
@endsection
