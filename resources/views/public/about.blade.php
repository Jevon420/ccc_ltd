@extends('layouts.public')

@section('title', 'About Us')
@section('seo-title', 'About Us')
@section('seo-description', 'Learn about Constructive Cleaning Company LTD — our mission, values, and the team behind Trinidad & Tobago\'s trusted land management and cleaning company. Licensed, insured, and community-focused.')

@section('content')

<section class="bg-gradient-to-br from-slate-900 to-blue-950 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">About Us</h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto">{{ $companyName }} — Built on trust, expertise, and a commitment to excellence.</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Constructive Cleaning Company LTD was founded with a clear vision — to provide professional, reliable, and innovative land management and cleaning solutions across Trinidad & Tobago and the wider Caribbean region.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    From debris clearance on residential properties to large-scale rural development and international metal trading, we bring the same level of professionalism and dedication to every engagement.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-700 pl-4 py-3 rounded-r-lg">
                    <p class="text-blue-900 font-semibold italic text-sm">{{ $companySlogan }}</p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    @foreach(['Licensed & Insured', 'Caribbean Based', 'Internationally Certified', 'Community Focused'] as $badge)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm text-gray-700">{{ $badge }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-gradient-to-br from-blue-700 to-slate-900 rounded-3xl p-10 text-white">
                <p class="text-2xl font-bold italic mb-4">"{{ $companyMotto }}"</p>
                <p class="text-blue-200 text-sm leading-relaxed">
                    These three words define how we approach every project. We deliver sufficient resources, effective solutions, and a track record of success that speaks for itself.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-10">What Sets Us Apart</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach([
                ['emoji' => '🛡️', 'title' => 'Licensed & Compliant', 'desc' => 'Fully licensed for all operations including international metal trading.'],
                ['emoji' => '⚡', 'title' => 'Fast & Efficient', 'desc' => 'We mobilise quickly and complete projects on schedule.'],
                ['emoji' => '🤝', 'title' => 'Community First', 'desc' => 'We invest in local talent and sustainable practices.'],
            ] as $item)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="text-4xl mb-4">{{ $item['emoji'] }}</div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $item['title'] }}</h3>
                <p class="text-sm text-gray-500">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-12 bg-blue-700 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-2">Want to work with us?</h2>
        <p class="text-blue-200 mb-6 text-sm">Request a quote or reach out to our team today.</p>
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors">
            Get in Touch
        </a>
    </div>
</section>

@endsection
