@extends('layouts.public')

@section('title', 'Home')
@section('seo-title', 'Home')
@section('seo-description', 'Constructive Cleaning Company LTD — professional land management, debris removal, rural development, development advisory, and licensed international metal trading across Trinidad & Tobago. Get a free quote today.')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-blue-600/20 border border-blue-500/30 text-blue-300 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                Licensed &amp; Certified Operations
            </div>

            <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
                Building a Cleaner,<br/>
                <span class="text-blue-400">Better Tomorrow</span>
            </h1>

            <p class="text-xl text-slate-300 mb-4 leading-relaxed">
                {{ $companyName }} — delivering professional land management, debris removal, development advisory, and international metal trading across the Caribbean and beyond.
            </p>

            <p class="text-sm text-blue-300 italic mb-8 font-medium">
                "{{ $companyMotto }}"
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('services') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors shadow-lg">
                    Explore Services
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 border border-white/30 hover:border-white/60 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                    Request a Quote
                </a>
            </div>
        </div>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 60L1440 60L1440 0C1200 40 960 60 720 40C480 20 240 0 0 20L0 60Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- Slogan Banner --}}
<section class="bg-blue-700 text-white py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm md:text-base font-semibold tracking-wide">
            {{ $companySlogan }}
        </p>
    </div>
</section>

{{-- Stats Bar --}}
<section class="bg-white border-b border-gray-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([
                ['value' => '15+', 'label' => 'Years of Experience'],
                ['value' => '200+', 'label' => 'Projects Completed'],
                ['value' => '5', 'label' => 'Service Divisions'],
                ['value' => 'T&T', 'label' => 'Caribbean Based'],
            ] as $stat)
            <div>
                <div class="text-3xl font-bold text-blue-700">{{ $stat['value'] }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Services Preview --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Our Services</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">From land clearance to international metal trading — we bring efficiency and expertise to every project.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $services = [
                ['icon' => '🏗️', 'title' => 'Development Advisory', 'desc' => 'Expert guidance on construction, land development, and project feasibility assessment.', 'color' => 'blue'],
                ['icon' => '🌾', 'title' => 'Rural Development', 'desc' => 'Community-focused rural infrastructure and agricultural land improvement projects.', 'color' => 'green'],
                ['icon' => '🧹', 'title' => 'Debris Cleaning/Removal', 'desc' => 'Professional debris clearance for residential, commercial, and disaster-recovery sites.', 'color' => 'amber'],
                ['icon' => '🌿', 'title' => 'Land Maintenance', 'desc' => 'Ongoing land care, vegetation control, drainage, and site upkeep services.', 'color' => 'emerald'],
                ['icon' => '⚙️', 'title' => 'International Metal Trading', 'desc' => 'Licensed international trading of ferrous and non-ferrous metals across global markets.', 'color' => 'slate'],
            ];
            @endphp

            @foreach($services as $service)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow group">
                <div class="text-4xl mb-4">{{ $service['icon'] }}</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $service['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $service['desc'] }}</p>
                <a href="{{ route('services') }}" class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-blue-700 hover:text-blue-800">
                    Learn more <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            @endforeach

            {{-- CTA card --}}
            <div class="bg-blue-700 rounded-2xl p-6 text-white flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold mb-2">Ready to Get Started?</h3>
                    <p class="text-sm text-blue-200">Contact us today for a free consultation and quote.</p>
                </div>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-semibold text-sm px-4 py-2.5 rounded-lg hover:bg-blue-50 transition-colors">
                    Request a Quote
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- About CTA --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-slate-900 to-blue-900 rounded-3xl p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-3xl font-bold text-white mb-3">Who We Are</h2>
                <p class="text-slate-300 max-w-xl leading-relaxed">
                    Constructive Cleaning Company LTD has been at the forefront of land management and development support across Trinidad & Tobago. We combine local expertise with international standards to deliver outstanding results.
                </p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('about') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                    Our Story
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Contact CTA --}}
<section class="py-12 bg-blue-700 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-2">Ready to work with us?</h2>
        <p class="text-blue-200 mb-6 text-sm">Get in touch and let's discuss your project.</p>
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors">
            Contact Us Today
        </a>
    </div>
</section>

@endsection
