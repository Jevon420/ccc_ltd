<div>
    {{-- Success state --}}
    @if($saved)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-3">✅</div>
        <h3 class="text-lg font-bold text-green-900 mb-2">AI Draft Ready!</h3>
        <p class="text-sm text-green-700 mb-5">Your quote has been saved as a draft with the AI-generated description.</p>
        <div class="flex justify-center gap-3">
            <a href="{{ route('dashboard.quotes.index') }}"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                View All Quotes
            </a>
            <button wire:click="$set('saved', false)"
                class="text-sm font-semibold text-gray-700 border border-gray-300 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                Draft Another
            </button>
        </div>
    </div>

    {{-- Generating state (polling) --}}
    @elseif($generating)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
        <svg class="animate-spin w-12 h-12 mx-auto mb-4 text-blue-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        <h3 class="text-base font-bold text-gray-900 mb-2">Generating AI Draft…</h3>
        <p class="text-sm text-gray-500 mb-1">Your quote draft has been saved and is being generated in the background.</p>
        <p class="text-xs text-gray-400">This usually takes 5–15 seconds. You can keep working — we'll notify you when it's ready.</p>
        <div class="mt-6">
            <a href="{{ route('dashboard.quotes.index') }}"
                class="text-sm font-semibold text-blue-700 hover:underline">
                Go to Quotes list →
            </a>
        </div>
    </div>

    {{-- Input form --}}
    @else
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-gray-900 mb-5">Job Details</h2>

            @if($errorMessage)
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errorMessage }}</div>
            @endif

            <form wire:submit="generate" class="space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Existing Client</label>
                    <select wire:model.live="clientId"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select client or fill manually…</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-semibold text-gray-500 mb-3">Or enter manually:</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Client Name <span class="text-red-500">*</span></label>
                    <input wire:model="client_name" type="text" placeholder="e.g. ABC Corporation Ltd"
                        class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_name') border-red-400 @enderror border-gray-300">
                    @error('client_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input wire:model="client_email" type="email" placeholder="client@email.com"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                        <input wire:model="client_phone" type="text" placeholder="+1 868 000-0000"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Service Type <span class="text-red-500">*</span></label>
                    <select wire:model="service_type"
                        class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('service_type') border-red-400 @enderror border-gray-300">
                        <option value="">Select a service…</option>
                        @foreach($serviceTypes as $type)
                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('service_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Location / Address</label>
                    <input wire:model="location" type="text" placeholder="e.g. Port of Spain, Trinidad"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Job Details <span class="text-red-500">*</span></label>
                    <textarea wire:model="job_details" rows="6"
                        placeholder="Describe the job — size of space, specific areas, special requirements, frequency, etc."
                        class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('job_details') border-red-400 @enderror border-gray-300"></textarea>
                    @error('job_details') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <svg wire:loading wire:target="generate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span wire:loading.remove wire:target="generate">✨ Generate AI Draft</span>
                    <span wire:loading wire:target="generate">Queuing…</span>
                </button>
            </form>
        </div>

        {{-- Info panel --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                <h3 class="text-sm font-bold text-blue-900 mb-2">⚡ How it works</h3>
                <ol class="space-y-2 text-xs text-blue-800 list-decimal list-inside">
                    <li>Fill in the job details and click Generate</li>
                    <li>A draft quote is saved instantly</li>
                    <li>The AI draft is generated in the background</li>
                    <li>You can continue working — the draft appears in the Quotes list when ready</li>
                </ol>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <p class="text-xs font-bold text-amber-900 mb-1">⚠️ AI Safety</p>
                <p class="text-xs text-amber-800 leading-relaxed">
                    AI drafts are suggestions only. Review and edit before sending to clients.
                    Pricing must always be set manually.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
