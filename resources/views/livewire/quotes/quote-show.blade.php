<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $quote->reference }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $quote->statusColour }}">
                        {{ ucfirst($quote->status) }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $quote->title ?? "Quote for {$quote->displayClient}" }}</h1>
                <p class="text-sm text-gray-500">{{ $quote->displayClient }}
                    @if($quote->serviceType) · {{ $quote->serviceType->name }}
                    @elseif($quote->service_type) · {{ $quote->service_type }}
                    @endif
                </p>
            </div>

            {{-- Action buttons by status --}}
            <div class="flex flex-wrap gap-2">
                @if($quote->status === 'draft')
                    @can('quotes.send')
                    <button wire:click="markSent"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Mark as Sent
                    </button>
                    @endcan
                @endif

                @if($quote->status === 'sent')
                    @can('quotes.approve')
                    <button wire:click="approve"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Accept
                    </button>
                    @endcan
                    @can('quotes.reject')
                    <button wire:click="decline"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Decline
                    </button>
                    @endcan
                @endif

                @if(in_array($quote->status, ['sent', 'accepted', 'declined', 'expired']))
                    @can('quotes.edit')
                    <button wire:click="revertToDraft"
                        class="text-sm font-semibold px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        Revert to Draft
                    </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Main details --}}
        <div class="md:col-span-2 space-y-5">

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-gray-900">Pricing</h2>
                    @can('quotes.edit')
                    @if(!$editingAmount)
                    <button wire:click="$set('editingAmount', true)"
                        class="text-xs font-semibold text-blue-700 hover:text-blue-900">Edit</button>
                    @endif
                    @endcan
                </div>

                @if($editingAmount)
                <form wire:submit="saveAmount" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (TTD)</label>
                            <input wire:model="amount" type="number" step="0.01" min="0" placeholder="0.00"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Valid Until</label>
                            <input wire:model="validUntil" type="date"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('validUntil') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                            Save
                        </button>
                        <button type="button" wire:click="$set('editingAmount', false)"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
                @else
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Quote Amount</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $quote->amount ? 'TTD '.number_format($quote->amount, 2) : 'Not set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Valid Until</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $quote->valid_until?->format('d M Y') ?? 'Not set' }}
                        </p>
                        @if($quote->valid_until && $quote->valid_until->isPast() && $quote->status !== 'accepted')
                        <p class="text-xs text-red-500 mt-1">⚠ Expired</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Job Description --}}
            @if($quote->job_details || $quote->ai_draft)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">Quote Description</h2>
                @if($quote->ai_draft)
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $quote->ai_draft }}</div>
                @endif
                @if($quote->job_details && $quote->job_details !== $quote->ai_draft)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Original Job Details</p>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $quote->job_details }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Notes --}}
            @if($quote->notes)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <p class="text-xs font-semibold text-amber-800 mb-2">Notes</p>
                <p class="text-sm text-amber-900 leading-relaxed">{{ $quote->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Client --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Client</h3>
                <p class="font-semibold text-gray-900">{{ $quote->displayClient }}</p>
                @if($quote->client?->contact_person)
                <p class="text-xs text-gray-500 mt-1">{{ $quote->client->contact_person }}</p>
                @endif
                @if($quote->client_email ?? $quote->client?->email)
                <a href="mailto:{{ $quote->client_email ?? $quote->client->email }}"
                    class="text-xs text-blue-600 hover:underline block mt-1">
                    {{ $quote->client_email ?? $quote->client->email }}
                </a>
                @endif
                @if($quote->client_phone ?? $quote->client?->phone)
                <p class="text-xs text-gray-500 mt-1">{{ $quote->client_phone ?? $quote->client->phone }}</p>
                @endif
            </div>

            {{-- Linked Job --}}
            @if($quote->job)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Linked Job</h3>
                <a href="{{ route('dashboard.jobs.show', $quote->job) }}"
                    class="font-mono text-sm font-bold text-blue-700 hover:underline">
                    {{ $quote->job->reference }}
                </a>
                <p class="text-xs text-gray-500 mt-1">{{ $quote->job->title }}</p>
            </div>
            @endif

            {{-- Record --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Record</h3>
                <p class="text-xs text-gray-600">Created {{ $quote->created_at->format('d M Y') }}</p>
                <p class="text-xs text-gray-600">Updated {{ $quote->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    {{-- Back --}}
    <a href="{{ route('dashboard.quotes.index') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Quotes
    </a>
</div>
