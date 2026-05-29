<div x-data="{ activeTab: 'company' }">

    {{-- Save Banner --}}
    @if($saved)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('saved', false) }, 3000)"
         class="mb-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 flex items-center gap-3">
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm text-green-700 font-medium">Settings saved successfully.</p>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 w-fit">
        <button type="button" @click="activeTab = 'company'"
                :class="activeTab === 'company' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
            Company Info
        </button>
        <button type="button" @click="activeTab = 'features'"
                :class="activeTab === 'features' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
            Features
        </button>
    </div>

    {{-- Company Info Tab --}}
    <div id="settings-company" x-show="activeTab === 'company'">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Company Information</h2>
                <p class="text-xs text-gray-500 mt-0.5">These values are used across the system and the public website.</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Name</label>
                    <input type="text" wire:model="company_name"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 @error('company_name') border-red-400 @enderror">
                    @error('company_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Email</label>
                    <input type="email" wire:model="company_email"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 @error('company_email') border-red-400 @enderror">
                    @error('company_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Phone</label>
                    <input type="text" wire:model="company_phone"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Address</label>
                    <input type="text" wire:model="company_address"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Slogan</label>
                    <input type="text" wire:model="company_slogan"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Motto</label>
                    <input type="text" wire:model="company_motto"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>
        </div>
    </div>

    {{-- Features Tab --}}
    <div id="settings-features" x-show="activeTab === 'features'">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Feature Flags</h2>
                <p class="text-xs text-gray-500 mt-0.5">Changes take effect immediately on save.</p>
            </div>
            <div class="p-6 space-y-5">
                @foreach([
                    ['prop' => 'public_site_enabled',      'label' => 'Public Website',         'desc' => 'Enable the public-facing company website.'],
                    ['prop' => 'quote_requests_enabled',   'label' => 'Quote Requests',          'desc' => 'Allow visitors to submit quote requests from the public site.'],
                    ['prop' => 'ai_features_enabled',      'label' => 'AI Features',             'desc' => 'Enable AI-assisted drafting. Requires API key in .env.'],
                    ['prop' => 'onboarding_tours_enabled', 'label' => 'Guided Onboarding Tours', 'desc' => 'Show guided tours to new users after first login.'],
                ] as $flag)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $flag['label'] }}</p>
                        <p class="text-xs text-gray-500">{{ $flag['desc'] }}</p>
                    </div>
                    <button type="button"
                            wire:click="$toggle('{{ $flag['prop'] }}')"
                            :class="{{ $flag['prop'] }} ? 'bg-blue-600' : 'bg-gray-200'"
                            x-bind:class="$wire.{{ $flag['prop'] }} ? 'bg-blue-600' : 'bg-gray-200'"
                            class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors duration-200 focus:outline-none ml-4 flex-shrink-0">
                        <span x-bind:class="$wire.{{ $flag['prop'] }} ? 'translate-x-5' : 'translate-x-0.5'"
                              class="inline-block w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="mt-6 flex items-center gap-3">
        <button wire:click="save" wire:loading.attr="disabled"
                class="bg-blue-700 hover:bg-blue-800 disabled:opacity-60 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 flex items-center gap-2">
            <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="save">Save Settings</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
    </div>
</div>
