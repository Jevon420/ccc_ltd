<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    {{-- SEO Component — pages set @section('seo-title'), ('seo-description'), etc. --}}
    @php
        $seoTitle       = trim($__env->yieldContent('seo-title') ?: $__env->yieldContent('title', ''));
        $seoDescription = trim($__env->yieldContent('seo-description', ''));
        $seoImage       = trim($__env->yieldContent('seo-image', ''));
        $seoCanonical   = trim($__env->yieldContent('seo-canonical', ''));
    @endphp
    <x-seo
        :title="$seoTitle"
        :description="$seoDescription ?: null"
        :image="$seoImage ?: null"
        :canonical="$seoCanonical ?: null"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link rel="preload" as="image" href="{{ asset('images/ccc_ops_logo.png') }}" />

    @stack('head')
</head>
<body class="antialiased bg-white text-gray-900">

{{-- ================================================================
     SITE-ENTRY PRELOADER
     Shows only on first visit per browser session (sessionStorage gated).
     Never shows between page navigations.
     ================================================================ --}}
<div id="ccc-preloader"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900"
     aria-hidden="true">
    <div class="flex flex-col items-center gap-6">
        {{-- Logo --}}
        <img src="{{ asset('images/ccc_ops_logo.png') }}"
             alt="{{ $companyName }}"
             class="w-24 h-24 object-contain rounded-2xl shadow-2xl animate-[pulse_1.5s_ease-in-out_infinite]"
             width="96" height="96" />

        {{-- Company name --}}
        <div class="text-center">
            <p class="text-white font-bold text-lg tracking-wide">Constructive Cleaning</p>
            <p class="text-blue-400 text-sm font-medium">Company LTD</p>
        </div>

        {{-- Progress bar --}}
        <div class="w-48 h-1 bg-slate-700 rounded-full overflow-hidden">
            <div id="ccc-preloader-bar"
                 class="h-full bg-blue-500 rounded-full transition-all duration-700 ease-out w-0"></div>
        </div>

        <p class="text-slate-500 text-xs">Loading…</p>
    </div>
</div>

<script>
    // Show preloader only on first visit (sessionStorage flag)
    (function () {
        var preloader = document.getElementById('ccc-preloader');
        var bar       = document.getElementById('ccc-preloader-bar');

        if (!preloader) return;

        // If already visited this session, hide immediately and bail
        if (sessionStorage.getItem('ccc_preloader_shown')) {
            preloader.style.display = 'none';
            return;
        }

        // Animate the progress bar
        var progress = 0;
        var tick = setInterval(function () {
            progress = Math.min(progress + Math.random() * 18, 90);
            bar.style.width = progress + '%';
        }, 120);

        function hidePreloader() {
            clearInterval(tick);
            bar.style.width = '100%';
            setTimeout(function () {
                preloader.style.transition = 'opacity 0.5s ease';
                preloader.style.opacity   = '0';
                setTimeout(function () {
                    preloader.style.display = 'none';
                    sessionStorage.setItem('ccc_preloader_shown', '1');
                }, 500);
            }, 300);
        }

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
            // Safety timeout — never block the user
            setTimeout(hidePreloader, 4000);
        }
    })();
</script>

{{-- ================================================================
     NAVIGATION
     ================================================================ --}}
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm"
     x-data="{ mobileOpen: false }"
     role="navigation"
     aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Logo --}}
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 group"
               aria-label="{{ $companyName }} — Home">
                <img src="{{ asset('images/ccc_ops_logo.png') }}"
                     alt="{{ $companyName }} logo"
                     class="h-10 w-10 object-contain rounded-lg"
                     width="40" height="40"
                     loading="eager" />
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-tight">Constructive Cleaning</div>
                    <div class="text-xs text-blue-700 leading-tight font-semibold">Company LTD</div>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-6" role="menubar">
                @foreach([
                    ['route' => 'home',     'label' => 'Home'],
                    ['route' => 'about',    'label' => 'About'],
                    ['route' => 'services', 'label' => 'Services'],
                    ['route' => 'projects', 'label' => 'Projects'],
                    ['route' => 'contact',  'label' => 'Contact'],
                ] as $item)
                <a href="{{ route($item['route']) }}"
                   role="menuitem"
                   class="text-sm font-medium transition-colors {{ request()->routeIs($item['route']) ? 'text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-700' }}">
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>

            {{-- CTA --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('contact') }}"
                   class="text-sm font-medium text-blue-700 border border-blue-700 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                    Get a Quote
                </a>
                @auth
                <a href="{{ route('dashboard') }}"
                   class="text-sm font-medium text-white bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                    Dashboard
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-white bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                    Staff Login
                </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-md text-gray-500 hover:text-gray-700"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-menu"
                    aria-label="Toggle menu">
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen"  class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu"
         x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-3 space-y-1">
            @foreach([
                ['route' => 'home',     'label' => 'Home'],
                ['route' => 'about',    'label' => 'About'],
                ['route' => 'services', 'label' => 'Services'],
                ['route' => 'projects', 'label' => 'Projects'],
                ['route' => 'contact',  'label' => 'Contact'],
            ] as $item)
            <a href="{{ route($item['route']) }}"
               @click="mobileOpen = false"
               class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
            <div class="pt-2 border-t border-gray-200 space-y-1">
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-700 hover:bg-blue-50">Get a Quote</a>
                @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white bg-blue-700">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white bg-blue-700">Staff Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Main Content --}}
<main id="main-content" role="main">
    @yield('content')
</main>

{{-- ================================================================
     FOOTER
     ================================================================ --}}
<footer class="bg-gray-900 text-gray-300" role="contentinfo" aria-label="Site footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">

            {{-- Brand --}}
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/ccc_ops_logo.png') }}"
                         alt="{{ $companyName }} logo"
                         class="h-12 w-12 object-contain rounded-xl"
                         width="48" height="48" loading="lazy" />
                    <div>
                        <div class="text-white font-bold leading-tight">Constructive Cleaning</div>
                        <div class="text-blue-400 text-xs font-medium">Company LTD</div>
                    </div>
                </a>
                <p class="text-sm text-gray-400 italic mb-2 leading-relaxed">{{ $companySlogan }}</p>
                <p class="text-sm text-gray-500">{{ $companyMotto }}</p>
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-white font-semibold text-sm mb-3">Services</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    @foreach(['Development Advisory','Rural Development','Debris Cleaning/Removal','Land Maintenance','International Metal Trading'] as $svc)
                    <li>
                        <a href="{{ route('services') }}" class="hover:text-white transition-colors">{{ $svc }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h3 class="text-white font-semibold text-sm mb-3">Company</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}"    class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('projects') }}" class="text-gray-400 hover:text-white transition-colors">Our Projects</a></li>
                    <li><a href="{{ route('contact') }}"  class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    <li><a href="{{ route('contact') }}"  class="text-gray-400 hover:text-white transition-colors">Request a Quote</a></li>
                    <li><a href="{{ route('login') }}"    class="text-gray-400 hover:text-white transition-colors">Staff Login</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-600">
                &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
            </p>
            <div class="flex gap-4 text-xs text-gray-600">
                <a href="{{ route('sitemap') }}" class="hover:text-gray-400 transition-colors">Sitemap</a>
                <span>Trinidad &amp; Tobago</span>
            </div>
        </div>
    </div>
</footer>

<x-toast />

{{-- Public AI Chatbot — shown only when enabled in admin settings --}}
@php $chatbotEnabled = (bool) \App\Models\Setting::get('ai_public_enabled', false) && (bool) \App\Models\Setting::get('ai_chatbot_enabled', false); @endphp
@if($chatbotEnabled)
<livewire:public.chatbot />
@endif

@livewireScripts
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (data) => {
            window.dispatchEvent(new CustomEvent('toast', { detail: data[0] ?? data }));
        });
    });
</script>
@stack('scripts')
</body>
</html>
