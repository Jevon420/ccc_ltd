<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search name, email, phone…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">

            <select wire:model.live="typeFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="company">Company</option>
                <option value="individual">Individual</option>
            </select>

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            @can('clients.restore')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Archived' : 'Show Archived' }}
            </button>
            @endcan

            @can('clients.create')
            <button wire:click="$dispatchTo('clients.client-form', 'open-create')"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Client
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
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Type</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Contact</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">City</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition-colors {{ $client->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-white font-bold text-sm
                                    {{ $client->type === 'company' ? 'bg-blue-600' : 'bg-purple-600' }}">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $client->name }}</p>
                                    @if($client->email)
                                    <p class="text-xs text-gray-500">{{ $client->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                                {{ $client->type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($client->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-gray-700">{{ $client->contact_person ?? '—' }}</p>
                            @if($client->phone)
                            <p class="text-xs text-gray-500">{{ $client->phone }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $client->city ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($client->trashed())
                                <span class="text-xs font-semibold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">Archived</span>
                            @elseif($client->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($client->trashed())
                                    @can('clients.restore')
                                    <button wire:click="confirmRestore({{ $client->id }})"
                                        class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors">
                                        Restore
                                    </button>
                                    @endcan
                                @else
                                    @can('clients.edit')
                                    <button wire:click="$dispatchTo('clients.client-form', 'open-edit', { clientId: {{ $client->id }} })"
                                        class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                        Edit
                                    </button>
                                    @endcan

                                    @can('clients.delete')
                                    <button wire:click="confirmDelete({{ $client->id }})"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                        Archive
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <p class="text-sm font-medium">No clients found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    {{-- Archive Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4v4m4-4v4"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Archive Client?</h3>
            <p class="text-sm text-gray-500 mb-6">The client will be archived and hidden from active lists. You can restore them later.</p>
            <div class="flex gap-3">
                <button wire:click="deleteClient" wire:loading.attr="disabled"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteClient">Archiving…</span>
                    <span wire:loading.remove wire:target="deleteClient">Yes, Archive</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Restore Confirm --}}
    @if($confirmingRestoreId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Restore Client?</h3>
            <p class="text-sm text-gray-500 mb-6">The client will be restored to active lists.</p>
            <div class="flex gap-3">
                <button wire:click="restoreClient" wire:loading.attr="disabled"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="restoreClient">Restoring…</span>
                    <span wire:loading.remove wire:target="restoreClient">Yes, Restore</span>
                </button>
                <button wire:click="$set('confirmingRestoreId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:clients.client-form @client-saved="$refresh" />
</div>
