@extends('layouts.public')

@section('title', 'Our Projects')
@section('seo-title', 'Our Projects')
@section('seo-description', 'Browse completed land clearance, rural development, debris removal, and metal trading projects by Constructive Cleaning Company LTD across Trinidad & Tobago.')

@section('content')

<section class="bg-gradient-to-br from-slate-900 to-blue-950 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Our Projects</h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto">A track record of completed work across Trinidad & Tobago and beyond.</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filter tabs placeholder --}}
        <div class="flex flex-wrap gap-2 mb-10 justify-center">
            @foreach(['All Projects', 'Land Clearance', 'Rural Development', 'Metal Trading', 'Advisory'] as $filter)
            <button class="px-4 py-2 text-sm font-medium rounded-full border border-gray-300 text-gray-600 hover:bg-blue-700 hover:text-white hover:border-blue-700 transition-colors {{ $filter === 'All Projects' ? 'bg-blue-700 text-white border-blue-700' : '' }}">
                {{ $filter }}
            </button>
            @endforeach
        </div>

        {{-- Project Gallery Grid (placeholder cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['title' => 'Site Clearance — San Fernando', 'category' => 'Land Clearance', 'year' => '2024', 'emoji' => '🧹'],
                ['title' => 'Rural Road Development — Sangre Grande', 'category' => 'Rural Development', 'year' => '2024', 'emoji' => '🌾'],
                ['title' => 'Scrap Metal Export — Port of Spain', 'category' => 'Metal Trading', 'year' => '2023', 'emoji' => '⚙️'],
                ['title' => 'Land Maintenance Contract — Chaguanas', 'category' => 'Land Clearance', 'year' => '2023', 'emoji' => '🌿'],
                ['title' => 'Development Advisory — South Trinidad', 'category' => 'Advisory', 'year' => '2023', 'emoji' => '🏗️'],
                ['title' => 'Post-Storm Debris Removal — Tobago', 'category' => 'Land Clearance', 'year' => '2022', 'emoji' => '🧹'],
            ] as $project)
            <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200 hover:shadow-md transition-shadow group">
                <div class="bg-gradient-to-br from-blue-800 to-slate-900 h-40 flex items-center justify-center">
                    <span class="text-6xl opacity-60 group-hover:opacity-90 transition-opacity">{{ $project['emoji'] }}</span>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">{{ $project['category'] }}</span>
                        <span class="text-xs text-gray-400">{{ $project['year'] }}</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm">{{ $project['title'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Project details and photos available upon request.</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-gray-500">Project gallery will be fully populated in Phase 2 with real photos and detailed case studies.</p>
        </div>
    </div>
</section>

<section class="py-12 bg-blue-700 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-2">Want your project here?</h2>
        <p class="text-blue-200 mb-6 text-sm">Let's work together on your next project.</p>
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors">
            Start a Project
        </a>
    </div>
</section>

@endsection
