<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search reference, invoice, client…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">

            <select wire:model.live="methodFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Methods</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
                <option value="card">Card</option>
                <option value="wipay">WiPay</option>
            </select>

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>

        @can('payments.create')
        <button wire:click="openRecordPayment"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Payment
        </button>
        @endcan
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Reference</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Invoice</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Amount</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Method</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Date</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-bold text-gray-700">{{ $payment->reference }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($payment->invoice)
                            <a href="{{ route('dashboard.invoices.show', $payment->invoice) }}"
                                class="font-mono text-xs font-bold text-blue-700 hover:text-blue-900">
                                {{ $payment->invoice->reference }}
                            </a>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-700">{{ $payment->client?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-semibold text-gray-900">TTD {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $payment->methodLabel }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $payment->statusColour }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($payment->status === 'confirmed')
                                @can('payments.refund')
                                <button wire:click="confirmRefund({{ $payment->id }})"
                                    class="text-xs font-semibold text-amber-600 hover:text-amber-800 px-2 py-1 rounded hover:bg-amber-50 transition-colors">
                                    Refund
                                </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <p class="text-sm font-medium">No payments recorded yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $payments->links() }}</div>
        @endif
    </div>

    {{-- Refund Confirm --}}
    @if($confirmingRefundId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Mark as Refunded?</h3>
            <p class="text-sm text-gray-500 mb-6">This will mark the payment as refunded and update the invoice balance. This action should only be taken after confirming the refund with the client.</p>
            <div class="flex gap-3">
                <button wire:click="refundPayment" wire:loading.attr="disabled"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="refundPayment">Processing…</span>
                    <span wire:loading.remove wire:target="refundPayment">Yes, Refund</span>
                </button>
                <button wire:click="$set('confirmingRefundId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:payments.record-payment @payment-saved="$refresh" />
</div>
