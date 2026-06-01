<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Input --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-5">Email Details</h2>

        @if($errorMessage)
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errorMessage }}</div>
        @endif

        <form wire:submit="generate" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Recipient Name / Company</label>
                <input wire:model="recipient" type="text" placeholder="e.g. John Smith / ABC Corp"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                <input wire:model="subject" type="text" placeholder="e.g. Your Quote Reference QUO-0012"
                    class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-400 @enderror border-gray-300">
                @error('subject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tone</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['professional' => '💼 Professional', 'friendly' => '😊 Friendly', 'firm' => '⚖️ Firm', 'apologetic' => '🙏 Apologetic'] as $value => $label)
                    <button type="button" wire:click="$set('tone', '{{ $value }}')"
                        class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $tone === $value ? 'bg-blue-700 text-white border-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Points to Cover <span class="text-red-500">*</span></label>
                <textarea wire:model="context" rows="6"
                    placeholder="Enter bullet points or notes for the email:&#10;- Quote approved, ready to schedule&#10;- Site visit on Tuesday 10am&#10;- Bring signed copy of contract"
                    class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('context') border-red-400 @enderror border-gray-300"></textarea>
                @error('context') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60">
                <svg wire:loading wire:target="generate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <span wire:loading.remove wire:target="generate">✉️ Draft Email</span>
                <span wire:loading wire:target="generate">Queuing…</span>
            </button>
        </form>
    </div>

    {{-- Output --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-900">AI Draft</h2>
            @if($draft)
            <div class="flex gap-2">
                <button x-data @click="navigator.clipboard.writeText($refs.draftText.value); $wire.copyDraft()"
                    class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                    📋 Copy
                </button>
                <button wire:click="reset"
                    class="text-xs font-semibold text-gray-500 hover:text-gray-700 px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                    Clear
                </button>
            </div>
            @endif
        </div>

        @if($generating)
        <div wire:poll.3000ms="checkDraft" class="flex-1 flex items-center justify-center text-center text-gray-400">
            <div>
                <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <p class="text-sm font-medium">Drafting your email…</p>
                <p class="text-xs mt-1">This takes a few seconds</p>
            </div>
        </div>
        @elseif($draft)
        <div class="flex-1">
            <textarea x-ref="draftText" wire:model="draft" rows="16"
                class="w-full text-sm text-gray-800 border border-gray-200 rounded-xl px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 leading-relaxed font-mono"></textarea>
            <p class="text-xs text-gray-400 mt-2">Review and edit before sending. AI drafts require human approval.</p>
        </div>
        @else
        <div class="flex-1 flex items-center justify-center text-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
            <div>
                <div class="text-4xl mb-3">✉️</div>
                <p class="text-sm font-medium">Fill in the details and click<br><strong>Draft Email</strong></p>
            </div>
        </div>
        @endif
    </div>
</div>
