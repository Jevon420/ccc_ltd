<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search name, serial, model…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
            <select wire:model.live="typeFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="vehicle">Vehicle</option>
                <option value="machinery">Machinery</option>
                <option value="tool">Tool</option>
                <option value="ppe">PPE</option>
                <option value="other">Other</option>
            </select>
            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="maintenance">Maintenance</option>
                <option value="retired">Retired</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            @can('equipment.restore')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Archived' : 'Show Archived' }}
            </button>
            @endcan
            @can('equipment.create')
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Equipment
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
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Name</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Type</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Serial / Model</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Condition</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($equipment as $item)
                    <tr class="hover:bg-gray-50 transition-colors {{ $item->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $item->name }}</td>
                        <td class="px-5 py-3.5 text-gray-600 capitalize">{{ $item->type }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">
                            {{ $item->serial_number ?? '' }}
                            @if($item->make_model) <span class="text-gray-400">· {{ $item->make_model }}</span> @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $item->conditionColour }}">{{ ucfirst($item->condition) }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $item->statusColour }}">{{ ucfirst($item->status) }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($item->trashed())
                                    @can('equipment.restore')
                                    <button wire:click="confirmRestore({{ $item->id }})" class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50">Restore</button>
                                    @endcan
                                @else
                                    @can('equipment.edit')
                                    <button wire:click="openEdit({{ $item->id }})" class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50">Edit</button>
                                    @endcan
                                    @can('equipment.delete')
                                    <button wire:click="confirmDelete({{ $item->id }})" class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">Archive</button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No equipment found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipment->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $equipment->links() }}</div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h2 class="text-base font-bold text-gray-900">{{ $editingId ? 'Edit Equipment' : 'Add Equipment' }}</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="e.g. Pressure Washer #2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Type</label>
                        <select wire:model="type" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="vehicle">Vehicle</option>
                            <option value="machinery">Machinery</option>
                            <option value="tool">Tool</option>
                            <option value="ppe">PPE</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Condition</label>
                        <select wire:model="condition" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Serial Number</label>
                        <input wire:model="serialNumber" type="text" placeholder="SN-########" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Make / Model</label>
                        <input wire:model="makeModel" type="text" placeholder="e.g. Karcher K5 Premium" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Purchase Date</label>
                        <input wire:model="purchaseDate" type="date" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Add Equipment' }}</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)" class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Archive Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Archive Equipment?</h3>
            <p class="text-sm text-gray-500 mb-6">It can be restored later.</p>
            <div class="flex gap-3">
                <button wire:click="deleteEquipment" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-lg">Archive</button>
                <button wire:click="$set('confirmingDeleteId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Restore Confirm --}}
    @if($confirmingRestoreId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Restore Equipment?</h3>
            <div class="flex gap-3 mt-6">
                <button wire:click="restoreEquipment" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg">Restore</button>
                <button wire:click="$set('confirmingRestoreId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
