<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">
                    {{ $userId ? 'Edit User' : 'Add New User' }}
                </h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="e.g. John Smith"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input wire:model="email" type="email" placeholder="john@example.com"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                        <input wire:model="phone" type="text" placeholder="+1 868 000-0000"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Position / Title</label>
                        <input wire:model="position" type="text" placeholder="e.g. Supervisor"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                        <select wire:model="role"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('role') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Select role…</option>
                            @foreach($roles as $roleName)
                            <option value="{{ $roleName }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            {{ $userId ? 'New Password' : 'Password' }}
                            @if($userId) <span class="text-gray-400 font-normal">(leave blank to keep)</span> @else <span class="text-red-500">*</span> @endif
                        </label>
                        <input wire:model="password" type="password" placeholder="Min 8 characters"
                            class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2 flex items-center gap-3">
                        <button type="button" wire:click="$toggle('isActive')"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $isActive ? 'bg-blue-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 ease-in-out {{ $isActive ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                        <span class="text-xs font-semibold text-gray-700">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $userId ? 'Save Changes' : 'Create User' }}</span>
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
