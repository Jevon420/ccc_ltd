<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Sign In') — {{ $companyName }}</title>
    <link rel="icon" href="{{ asset('images/ccc_ops_logo.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('images/ccc_ops_logo.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Logo / Brand --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
            <img src="{{ asset('images/ccc_ops_logo.png') }}"
                     alt="{{ $companyName }}"
                     class="h-16 w-16 object-contain rounded-2xl shadow-lg mx-auto"
                     width="64" height="64" />
        </a>
        <h1 class="mt-4 text-2xl font-bold text-white">{{ $companyName }}</h1>
        <p class="text-sm text-blue-300 mt-1">Operations Portal</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @yield('content')
    </div>

    <p class="text-center text-xs text-blue-300/60 mt-6">
        &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
    </p>
</div>

@stack('scripts')
</body>
</html>
