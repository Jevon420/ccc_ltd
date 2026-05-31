<div class="max-w-3xl">

    {{-- Add Button --}}
    @can('service_types.create')
    <div class="flex justify-end mb-4">
        <button wire:click="$toggle('showCreateForm')"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Service Type
        </button>
    </div>

    {{-- Create Form --}}
    @if($showCreateForm)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-5">
        <h3 class="text-sm font-bold text-blue-900 mb-4">New Service Type</h3>
        <form wire:submit="create" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input wire:model="newName" type="text" placeholder="e.g. Commercial Cleaning"
                    class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('newName') ? 'border-red-400' : 'border-gray-300' }}">
                @error('newName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                <textarea wire:model="newDescription" rows="2" placeholder="Optional description…"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" wire:loading.attr="disabled"
                    class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="create">Saving…</span>
                    <span wire:loading.remove wire:target="create">Save</span>
                </button>
                <button type="button" wire:click="$set('showCreateForm', false)"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif
    @endcan

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Name</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Description</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($serviceTypes as $type)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        @if($editingId === $type->id)
                        <input wire:model="editName" type="text"
                            class="w-full text-sm border border-blue-400 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        @else
                        <span class="font-semibold text-gray-900">{{ $type->name }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-gray-500">
                        @if($editingId === $type->id)
                        <input wire:model="editDescription" type="text" placeholder="Description…"
                            class="w-full text-sm border border-blue-400 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @else
                        {{ $type->description ?? '—' }}
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @can('service_types.edit')
                        <button wire:click="toggleActive({{ $type->id }})"
                            class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full transition-colors
                            {{ $type->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $type->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </button>
                        @else
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @endcan
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            @if($editingId === $type->id)
                            <button wire:click="saveEdit"
                                class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                Save
                            </button>
                            <button wire:click="$set('editingId', null)"
                                class="text-xs font-semibold text-gray-500 hover:text-gray-700 px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                Cancel
                            </button>
                            @else
                            @can('service_types.edit')
                            <button wire:click="startEdit({{ $type->id }})"
                                class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                Edit
                            </button>
                            @endcan
                            @can('service_types.delete')
                            <button wire:click="confirmDelete({{ $type->id }})"
                                class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                Delete
                            </button>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                        <p class="text-sm font-medium">No service types yet. Add one above.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Service Type?</h3>
            <p class="text-sm text-gray-500 mb-6">This cannot be undone. Consider deactivating it instead if jobs are linked to it.</p>
            <div class="flex gap-3">
                <button wire:click="deleteServiceType" wire:loading.attr="disabled"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteServiceType">Deleting…</span>
                    <span wire:loading.remove wire:target="deleteServiceType">Yes, Delete</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
