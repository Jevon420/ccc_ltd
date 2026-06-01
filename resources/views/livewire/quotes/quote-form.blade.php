<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h2 class="text-base font-bold text-gray-900">{{ $quoteId ? 'Edit Quote' : 'New Quote' }}</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input wire:model="title" type="text" placeholder="e.g. Debris Removal — ABC Corp Site"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror border-gray-300">
                        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                        <select wire:model="clientId" class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('clientId') border-red-400 @enderror border-gray-300">
                            <option value="">Select client…</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('clientId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Service Type <span class="text-red-500">*</span></label>
                        <select wire:model="serviceType" class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('serviceType') border-red-400 @enderror border-gray-300">
                            <option value="">Select service…</option>
                            @foreach($serviceTypes as $type)
                            <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('serviceType') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="accepted">Accepted</option>
                            <option value="declined">Declined</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (TTD)</label>
                        <input wire:model="amount" type="number" step="0.01" min="0" placeholder="0.00"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Valid Until</label>
                        <input wire:model="validUntil" type="date" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
                        <input wire:model="location" type="text" placeholder="Site address or description"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Job Details / Scope <span class="text-red-500">*</span></label>
                        <textarea wire:model="jobDetails" rows="4" placeholder="Describe the scope of work…"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('jobDetails') border-red-400 @enderror border-gray-300"></textarea>
                        @error('jobDetails') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Internal Notes</label>
                        <textarea wire:model="notes" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $quoteId ? 'Save Changes' : 'Create Quote' }}</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
