@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Reset Password</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your email address and we'll send you a password reset link.</p>

    @if(session('status'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
        <p class="text-sm text-green-700 font-medium">{{ session('status') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">
            Send Reset Link
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-blue-700 hover:underline">← Back to Login</a>
    </div>
</div>
@endsection
