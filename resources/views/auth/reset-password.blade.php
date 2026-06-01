@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Set New Password</h2>
    <p class="text-sm text-gray-500 mb-6">Choose a strong password for your account.</p>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <input type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
            <input type="password" name="password" required
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>

        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">
            Reset Password
        </button>
    </form>
</div>
@endsection
