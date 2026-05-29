@extends('layouts.public')

@section('title', 'Contact Us')
@section('seo-title', 'Contact Us — Request a Free Quote')
@section('seo-description', 'Contact Constructive Cleaning Company LTD to request a free quote for land management, debris removal, rural development, or metal trading services in Trinidad & Tobago.')

@section('content')

<section class="bg-gradient-to-br from-slate-900 to-blue-950 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Get In Touch</h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto">Request a quote, ask about our services, or just say hello.</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- Contact Info --}}
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-500 uppercase">Email</div>
                                <a href="mailto:{{ \App\Models\Setting::get('company_email', 'info@constructivecleaningco.com') }}"
                                   class="text-sm text-gray-900 hover:text-blue-700">
                                    {{ \App\Models\Setting::get('company_email', 'info@constructivecleaningco.com') }}
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-500 uppercase">Phone</div>
                                <span class="text-sm text-gray-900">{{ \App\Models\Setting::get('company_phone', '+1 (868) 000-0000') }}</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-500 uppercase">Location</div>
                                <span class="text-sm text-gray-900">{{ \App\Models\Setting::get('company_address', 'Trinidad & Tobago') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                    <p class="text-sm font-semibold text-blue-900 mb-1">Business Hours</p>
                    <p class="text-xs text-blue-700">Monday – Friday: 8:00 AM – 5:00 PM</p>
                    <p class="text-xs text-blue-700">Saturday: 9:00 AM – 1:00 PM</p>
                    <p class="text-xs text-blue-500 mt-1">Emergency contacts available</p>
                </div>
            </div>

            {{-- Contact / Quote Form --}}
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Request a Quote</h2>

                @if(session('quote_sent'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-700 font-medium">✅ Your request has been submitted. We'll be in touch shortly!</p>
                </div>
                @endif

                <form method="POST" action="#" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" required placeholder="John Smith"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" required placeholder="john@company.com"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+1 (868) 000-0000"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Needed *</label>
                        <select name="service" required
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Select a service...</option>
                            <option>Development Advisory</option>
                            <option>Rural Development</option>
                            <option>Debris Cleaning/Removal</option>
                            <option>Land Maintenance</option>
                            <option>International Metal Trading</option>
                            <option>Multiple Services</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Project Details *</label>
                        <textarea name="message" rows="5" required
                                  placeholder="Describe your project, location, timeline, and any specific requirements..."
                                  class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 resize-none"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit"
                                class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-lg transition-colors">
                            Send Quote Request
                        </button>
                        <p class="text-xs text-gray-400 mt-2">We'll respond within 1 business day. Full online quote flow coming in Phase 2.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
