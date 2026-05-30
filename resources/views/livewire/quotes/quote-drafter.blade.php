<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Left: Input form --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-5">Job Details</h2>

        @if($saved)
        <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Quote saved as draft.
        </div>
        @endif

        <form wire:submit="generate" class="space-y-4">

            {{-- Client Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Client Name <span class="text-red-500">*</span></label>
                <input wire:model="client_name" type="text" placeholder="e.g. ABC Corporation Ltd"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_name') border-red-400 @enderror">
                @error('client_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Contact --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input wire:model="client_email" type="email" placeholder="client@email.com"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('client_email') border-red-400 @enderror">
                    @error('client_email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                    <input wire:model="client_phone" type="text" placeholder="+1 868 000-0000"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Service Type --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Service Type <span class="text-red-500">*</span></label>
                <select wire:model="service_type"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('service_type') border-red-400 @enderror">
                    <option value="">Select a service…</option>
                    <option>Commercial Cleaning</option>
                    <option>Residential Cleaning</option>
                    <option>Post-Construction Clean</option>
                    <option>Deep Cleaning</option>
                    <option>Carpet Cleaning</option>
                    <option>Window Cleaning</option>
                    <option>Janitorial Services</option>
                    <option>One-off Clean</option>
                    <option>Other</option>
                </select>
                @error('service_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Location / Address</label>
                <input wire:model="location" type="text" placeholder="e.g. Port of Spain, Trinidad"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Job Details --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Job Details <span class="text-red-500">*</span></label>
                <textarea wire:model="job_details" rows="5" placeholder="Describe the job — size of space, specific areas, special requirements, frequency, etc."
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('job_details') border-red-400 @enderror"></textarea>
                @error('job_details') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60">
                <span wire:loading wire:target="generate">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                </span>
                <span wire:loading.remove wire:target="generate">✨</span>
                <span wire:loading wire:target="generate">Drafting…</span>
                <span wire:loading.remove wire:target="generate">Generate AI Draft</span>
            </button>
        </form>
    </div>

    {{-- Right: AI Output --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-900">AI Draft</h2>
            @if($ai_draft)
            <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Ready</span>
            @else
            <span class="text-xs bg-gray-100 text-gray-500 font-medium px-2 py-0.5 rounded-full">Awaiting input</span>
            @endif
        </div>

        @if($errorMessage)
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            {{ $errorMessage }}
        </div>
        @endif

        @if($generating)
        <div class="flex-1 flex items-center justify-center text-gray-400">
            <div class="text-center">
                <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <p class="text-sm font-medium">Generating your draft…</p>
            </div>
        </div>
        @elseif($ai_draft)
        <div class="flex-1">
            <textarea wire:model="ai_draft" rows="12"
                class="w-full text-sm text-gray-800 border border-gray-200 rounded-xl px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 leading-relaxed">{{ $ai_draft }}</textarea>
            <p class="text-xs text-gray-400 mt-2">You may edit the draft before saving.</p>
        </div>
        <div class="mt-4 flex gap-3">
            <button wire:click="saveQuote" wire:loading.attr="disabled"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60">
                Save as Draft Quote
            </button>
            <button wire:click="generate"
                class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Regenerate
            </button>
        </div>
        @else
        <div class="flex-1 flex items-center justify-center text-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
            <div>
                <div class="text-4xl mb-3">✨</div>
                <p class="text-sm font-medium">Fill in the job details and click<br><strong>Generate AI Draft</strong></p>
            </div>
        </div>
        @endif
    </div>

</div>
