<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h2 class="text-base font-bold text-gray-900">
                    {{ $clientId ? 'Edit Client' : 'Add New Client' }}
                </h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">

                {{-- Type Toggle --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Client Type</label>
                    <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                        <button type="button" wire:click="$set('type', 'company')"
                            class="flex-1 py-2 text-sm font-semibold transition-colors {{ $type === 'company' ? 'bg-blue-700 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            🏢 Company
                        </button>
                        <button type="button" wire:click="$set('type', 'individual')"
                            class="flex-1 py-2 text-sm font-semibold transition-colors {{ $type === 'individual' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                            👤 Individual
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            {{ $type === 'company' ? 'Company Name' : 'Full Name' }} <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text"
                            placeholder="{{ $type === 'company' ? 'e.g. ABC Corporation Ltd' : 'e.g. John Smith' }}"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($type === 'company')
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Person</label>
                        <input wire:model="contactPerson" type="text" placeholder="Primary contact at the company"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input wire:model="email" type="email" placeholder="client@email.com"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                        <input wire:model="phone" type="text" placeholder="+1 868 000-0000"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Address</label>
                        <input wire:model="address" type="text" placeholder="Street address"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">City</label>
                        <select wire:model="city"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select city…</option>
                            @foreach(['Port of Spain','San Fernando','Chaguanas','Arima','Scarborough','Point Fortin','Couva','Sangre Grande','Siparia','Other'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-5">
                        <button type="button" wire:click="$toggle('isActive')"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $isActive ? 'bg-blue-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out {{ $isActive ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                        <span class="text-xs font-semibold text-gray-700">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="3" placeholder="Any additional notes about this client…"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $clientId ? 'Save Changes' : 'Create Client' }}</span>
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
