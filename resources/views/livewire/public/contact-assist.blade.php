<div>
    {{-- AI Assist Trigger Button --}}
    @if(!$isOpen && !$refinedDescription)
    <button wire:click="open" type="button"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 hover:text-blue-900 border border-blue-200 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
        ✨ AI: Help me describe my project
    </button>
    @endif

    {{-- Expanded Input --}}
    @if($isOpen && !$refinedDescription)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-3">
        <div class="flex items-start gap-2">
            <span class="text-lg">✨</span>
            <div>
                <p class="text-xs font-bold text-blue-900">AI Project Assist</p>
                <p class="text-xs text-blue-700">Describe your project in your own words — the AI will help turn it into a clear project brief.</p>
            </div>
        </div>
        <textarea wire:model="roughDescription" rows="3"
            placeholder="e.g. I need someone to clear the bushes on my land in Chaguanas, it's about 2 acres, been overgrown for months..."
            maxlength="1000"
            class="w-full text-sm border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none bg-white">
        </textarea>
        @error('roughDescription') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
        <div class="flex gap-2">
            <button wire:click="refine" wire:loading.attr="disabled" type="button"
                class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-2 rounded-lg transition-colors disabled:opacity-60">
                <span wire:loading wire:target="refine">Refining…</span>
                <span wire:loading.remove wire:target="refine">Refine with AI</span>
            </button>
            <button wire:click="$set('isOpen', false)" type="button"
                class="px-3 py-2 text-xs font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
        </div>
        <p class="text-[10px] text-blue-500">1 use per 30 minutes · Your description is not stored.</p>
    </div>
    @endif

    {{-- Refined Result --}}
    @if($refinedDescription)
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 space-y-3">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-xs font-bold text-green-900">AI-refined description</p>
        </div>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $refinedDescription }}</p>
        <div class="flex gap-2">
            <button wire:click="useDescription" type="button"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 rounded-lg transition-colors">
                Use this description
            </button>
            <button wire:click="$set('refinedDescription', '')" type="button"
                class="px-3 py-2 text-xs font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Discard
            </button>
        </div>
    </div>
    @endif
</div>
