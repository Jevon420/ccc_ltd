<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Input --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-2">Report Text</h2>
        <p class="text-xs text-gray-500 mb-5">Paste a job report below. AI will produce a concise executive summary for management review.</p>

        @if($errorMessage)
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errorMessage }}</div>
        @endif

        <form wire:submit="summarise" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Report Content <span class="text-red-500">*</span></label>
                <textarea wire:model="reportText" rows="14"
                    placeholder="Paste the full job report here…"
                    class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('reportText') border-red-400 @enderror border-gray-300"></textarea>
                @error('reportText') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Minimum 50 characters required.</p>
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60">
                <svg wire:loading wire:target="summarise" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <span wire:loading.remove wire:target="summarise">📊 Summarise Report</span>
                <span wire:loading wire:target="summarise">Queuing…</span>
            </button>
        </form>
    </div>

    {{-- Output --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-900">Executive Summary</h2>
            @if($summary)
            <button x-data @click="navigator.clipboard.writeText($refs.summaryText.innerText)"
                class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                📋 Copy
            </button>
            @endif
        </div>

        @if($generating)
        <div wire:poll.3000ms="checkSummary" class="flex-1 flex items-center justify-center text-center text-gray-400">
            <div>
                <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                <p class="text-sm font-medium">Summarising report…</p>
                <p class="text-xs mt-1">This takes a few seconds</p>
            </div>
        </div>
        @elseif($summary)
        <div class="flex-1 bg-gray-50 rounded-xl border border-gray-200 p-5">
            <div x-ref="summaryText" class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $summary }}</div>
            <p class="text-xs text-gray-400 mt-4 pt-4 border-t border-gray-200">AI-generated summary. Review before sharing with management.</p>
        </div>
        @else
        <div class="flex-1 flex items-center justify-center text-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
            <div>
                <div class="text-4xl mb-3">📊</div>
                <p class="text-sm font-medium">Paste a report and click<br><strong>Summarise Report</strong></p>
            </div>
        </div>
        @endif
    </div>
</div>
