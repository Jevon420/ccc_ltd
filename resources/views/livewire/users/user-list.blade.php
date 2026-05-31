<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search name or email…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">

            <select wire:model.live="roleFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Roles</option>
                @foreach($roles as $roleName)
                <option value="{{ $roleName }}">{{ $roleName }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            @can('users.restore')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Deactivated' : 'Show Deactivated' }}
            </button>
            @endcan

            @can('users.create')
            <button wire:click="$dispatchTo('users.user-form', 'open-create')"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
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
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">User</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Role</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Position</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Joined</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors {{ $user->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ $user->initials }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $user->position ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($user->trashed())
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">Deactivated</span>
                            @elseif($user->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->trashed())
                                    @can('users.restore')
                                    <button wire:click="confirmRestore({{ $user->id }})"
                                        class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors">
                                        Restore
                                    </button>
                                    @endcan
                                @else
                                    @can('users.edit')
                                    <button wire:click="$dispatchTo('users.user-form', 'open-edit', { userId: {{ $user->id }} })"
                                        class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                        Edit
                                    </button>
                                    @endcan

                                    @can('users.delete')
                                    @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                        Deactivate
                                    </button>
                                    @endif
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-sm font-medium">No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Delete Confirm Modal --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Deactivate User?</h3>
            <p class="text-sm text-gray-500 mb-6">The user will lose access immediately. You can restore them later.</p>
            <div class="flex gap-3">
                <button wire:click="deleteUser" wire:loading.attr="disabled"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteUser">Deactivating…</span>
                    <span wire:loading.remove wire:target="deleteUser">Yes, Deactivate</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Restore Confirm Modal --}}
    @if($confirmingRestoreId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Restore User?</h3>
            <p class="text-sm text-gray-500 mb-6">The user will regain access to the portal based on their assigned role.</p>
            <div class="flex gap-3">
                <button wire:click="restoreUser" wire:loading.attr="disabled"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="restoreUser">Restoring…</span>
                    <span wire:loading.remove wire:target="restoreUser">Yes, Restore</span>
                </button>
                <button wire:click="$set('confirmingRestoreId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:users.user-form @user-saved="$refresh" />
</div>
