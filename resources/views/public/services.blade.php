@extends('layouts.public')

@section('title', 'Services')
@section('seo-title', 'Our Services')
@section('seo-description', 'CCC offers Development Advisory, Rural Development, Debris Cleaning & Removal, Land Maintenance, and Licensed International Metal Trading across Trinidad & Tobago. Contact us for a free quote.')

@section('content')

<section class="bg-gradient-to-br from-slate-900 to-blue-950 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Our Services</h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto">Five specialist divisions. One trusted company.</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
        $services = [
            [
                'icon' => '🏗️',
                'title' => 'Development Advisory',
                'color' => 'blue',
                'summary' => 'Strategic advisory services for construction and land development projects.',
                'details' => [
                    'Feasibility studies and site assessments',
                    'Project planning and timeline management',
                    'Regulatory compliance guidance',
                    'Stakeholder and community engagement',
                    'Environmental impact advisory',
                ],
            ],
            [
                'icon' => '🌾',
                'title' => 'Rural Development',
                'color' => 'green',
                'summary' => 'Transforming rural communities with sustainable development solutions.',
                'details' => [
                    'Rural infrastructure planning',
                    'Agricultural land preparation',
                    'Water and drainage system support',
                    'Community access road development',
                    'Sustainable land use consulting',
                ],
            ],
            [
                'icon' => '🧹',
                'title' => 'Debris Cleaning/Removal',
                'color' => 'amber',
                'summary' => 'Professional debris clearance for any scale of project.',
                'details' => [
                    'Post-construction debris removal',
                    'Disaster and storm cleanup',
                    'Residential lot clearance',
                    'Commercial site preparation',
                    'Vegetation and waste disposal',
                ],
            ],
            [
                'icon' => '🌿',
                'title' => 'Land Maintenance',
                'color' => 'emerald',
                'summary' => 'Keeping your land in prime condition year-round.',
                'details' => [
                    'Regular grass cutting and vegetation control',
                    'Drainage maintenance and clearing',
                    'Erosion control measures',
                    'Tree trimming and removal',
                    'Long-term maintenance contracts',
                ],
            ],
            [
                'icon' => '⚙️',
                'title' => 'Licensed International Metal Trading',
                'color' => 'slate',
                'summary' => 'Certified international trading of ferrous and non-ferrous metals.',
                'details' => [
                    'Export and import of scrap metals',
                    'Regulatory and customs compliance',
                    'Bulk metal commodity trading',
                    'Secure storage and logistics',
                    'Market price advisory',
                ],
            ],
        ];
        @endphp

        <div class="space-y-10">
            @foreach($services as $i => $service)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center {{ $i % 2 !== 0 ? 'md:flex-row-reverse' : '' }} bg-gray-50 rounded-3xl p-8 border border-gray-200">
                <div class="{{ $i % 2 !== 0 ? 'md:order-2' : '' }}">
                    <div class="text-5xl mb-4">{{ $service['icon'] }}</div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $service['title'] }}</h2>
                    <p class="text-gray-600 mb-5">{{ $service['summary'] }}</p>
                    <ul class="space-y-2">
                        @foreach($service['details'] as $detail)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $detail }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 mt-6 text-sm font-semibold text-blue-700 hover:text-blue-800">
                        Get a quote for this service
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                <div class="{{ $i % 2 !== 0 ? 'md:order-1' : '' }} bg-gradient-to-br from-blue-700 to-slate-900 rounded-2xl h-48 flex items-center justify-center">
                    <span class="text-8xl opacity-50">{{ $service['icon'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-12 bg-blue-700 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-2">Need a custom solution?</h2>
        <p class="text-blue-200 mb-6 text-sm">Contact our team and we will design a service package that fits your needs.</p>
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors">
            Request a Quote
        </a>
    </div>
</section>

@endsection
