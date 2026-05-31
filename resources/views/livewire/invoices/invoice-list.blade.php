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
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            @can('invoices.delete')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Deleted' : 'Show Deleted' }}
            </button>
            @endcan

            @can('invoices.create')
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Invoice
            </button>
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
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Total</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Balance Due</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Due Date</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition-colors {{ $invoice->trashed() ? 'opacity-60' : '' }} {{ $invoice->isOverdue ? 'bg-red-50/30' : '' }}">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('dashboard.invoices.show', $invoice) }}"
                                class="font-mono text-xs font-bold text-blue-700 hover:text-blue-900">
                                {{ $invoice->reference }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $invoice->client?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-semibold text-gray-900">TTD {{ number_format($invoice->total, 2) }}</td>
                        <td class="px-5 py-3.5">
                            @if($invoice->balanceDue > 0)
                            <span class="font-semibold text-red-700">TTD {{ number_format($invoice->balanceDue, 2) }}</span>
                            @else
                            <span class="text-green-600 font-semibold">Paid</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $invoice->statusColour }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs {{ $invoice->isOverdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                            @if($invoice->isOverdue) <span class="ml-1">⚠</span> @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($invoice->trashed())
                                    @can('invoices.delete')
                                    <button wire:click="restore({{ $invoice->id }})"
                                        class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50">
                                        Restore
                                    </button>
                                    @endcan
                                @else
                                    <a href="{{ route('dashboard.invoices.show', $invoice) }}"
                                        class="text-xs font-semibold text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-100">
                                        View
                                    </a>
                                    @can('invoices.edit')
                                    <button wire:click="openEdit({{ $invoice->id }})"
                                        class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50">
                                        Edit
                                    </button>
                                    @endcan
                                    @can('invoices.delete')
                                    <button wire:click="confirmDelete({{ $invoice->id }})"
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
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p class="text-sm font-medium">No invoices found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $invoices->links() }}</div>
        @endif
    </div>

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Invoice?</h3>
            <p class="text-sm text-gray-500 mb-6">This invoice will be deleted. You can restore it from the deleted view.</p>
            <div class="flex gap-3">
                <button wire:click="deleteInvoice" wire:loading.attr="disabled"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteInvoice">Deleting…</span>
                    <span wire:loading.remove wire:target="deleteInvoice">Yes, Delete</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:invoices.invoice-form @invoice-saved="$refresh" />
</div>
