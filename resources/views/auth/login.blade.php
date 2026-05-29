@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
    <p class="text-sm text-gray-500 mb-6">Sign in to your operations account</p>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
        @foreach($errors->all() as $error)
            <p class="text-sm text-red-600">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900
                          focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600
                          @error('email') border-red-400 focus:ring-red-400 @enderror"
                   placeholder="you@company.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" name="password" id="password" required autocomplete="current-password"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 pr-10
                              focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600
                              @error('password') border-red-400 focus:ring-red-400 @enderror"
                       placeholder="••••••••">
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-blue-700 hover:text-blue-800 font-medium">
                Forgot password?
            </a>
        </div>

        <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm py-2.5 px-4 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to website
        </a>
    </div>
</div>
@endsection
