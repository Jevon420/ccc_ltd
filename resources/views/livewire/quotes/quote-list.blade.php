<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search reference, title, client…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="accepted">Accepted</option>
                <option value="declined">Declined</option>
                <option value="expired">Expired</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            @can('quotes.delete')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Deleted' : 'Show Deleted' }}
            </button>
            @endcan

            @can('quotes.create')
            <a href="{{ route('dashboard.quotes.create') }}"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Quote
            </a>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Reference</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Service</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Amount</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Valid Until</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($quotes as $quote)
                    <tr class="hover:bg-gray-50 transition-colors {{ $quote->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('dashboard.quotes.show', $quote) }}"
                                class="font-mono text-xs font-bold text-blue-700 hover:text-blue-900">
                                {{ $quote->reference }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $quote->displayClient }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $quote->serviceType?->name ?? $quote->service_type ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-semibold text-gray-900">
                            {{ $quote->amount ? 'TTD '.number_format($quote->amount, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $quote->statusColour }}">
                                {{ ucfirst($quote->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">
                            {{ $quote->valid_until?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($quote->trashed())
                                    @can('quotes.delete')
                                    <button wire:click="restore({{ $quote->id }})"
                                        class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50">
                                        Restore
                                    </button>
                                    @endcan
                                @else
                                    <a href="{{ route('dashboard.quotes.show', $quote) }}"
                                        class="text-xs font-semibold text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-100">
                                        View
                                    </a>
                                    @can('quotes.delete')
                                    <button wire:click="confirmDelete({{ $quote->id }})"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">
                                        Delete
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-medium">No quotes found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotes->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $quotes->links() }}</div>
        @endif
    </div>

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Quote?</h3>
            <p class="text-sm text-gray-500 mb-6">This quote will be deleted. You can restore it later from the deleted view.</p>
            <div class="flex gap-3">
                <button wire:click="deleteQuote" wire:loading.attr="disabled"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteQuote">Deleting…</span>
                    <span wire:loading.remove wire:target="deleteQuote">Yes, Delete</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
