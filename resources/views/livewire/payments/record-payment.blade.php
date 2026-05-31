<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">Record Payment</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Invoice <span class="text-red-500">*</span></label>
                    <select wire:model="invoiceId"
                        class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('invoiceId') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">Select invoice…</option>
                        @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" @selected($invoiceId === $invoice->id)>
                            {{ $invoice->reference }} — {{ $invoice->client?->name }} (Balance: TTD {{ number_format($invoice->balanceDue, 2) }})
                        </option>
                        @endforeach
                    </select>
                    @error('invoiceId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (TTD) <span class="text-red-500">*</span></label>
                        <input wire:model="amount" type="number" step="0.01" min="0.01" placeholder="0.00"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('amount') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input wire:model="paidAt" type="date"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('paidAt') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('paidAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Method</label>
                        <select wire:model="method"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="card">Card</option>
                            <option value="wipay">WiPay</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                        <select wire:model="status"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Transaction Reference</label>
                    <input wire:model="transactionReference" type="text" placeholder="e.g. TXN-12345678"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="2" placeholder="Any additional payment notes…"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save">Recording…</span>
                        <span wire:loading.remove wire:target="save">Record Payment</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
