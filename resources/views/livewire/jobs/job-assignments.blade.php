<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">Team Assignments ({{ $assignments->count() }})</h3>
        @can('job_assignments.create')
        @if($availableStaff->isNotEmpty())
        <button wire:click="openAssign"
            class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Assign Staff
        </button>
        @endif
        @endcan
    </div>

    {{-- Assigned Staff --}}
    @forelse($assignments as $assignment)
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
            {{ strtoupper(substr($assignment->user?->name ?? '?', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900">{{ $assignment->user?->name ?? 'Unknown' }}</p>
            <p class="text-xs text-gray-500">{{ $assignment->user?->position ?? $assignment->user?->getRoleNames()->first() }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $assignment->roleColour }}">
                {{ $assignment->roleLabel }}
            </span>
            @if($assignment->assigned_date)
            <span class="text-xs text-gray-400">{{ $assignment->assigned_date->format('d M') }}</span>
            @endif
            @can('job_assignments.delete')
            <button wire:click="confirmRemove({{ $assignment->id }})"
                class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                Remove
            </button>
            @endcan
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-400">
        <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-sm font-medium">No staff assigned yet</p>
    </div>
    @endforelse

    {{-- Assign Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">Assign Staff Member</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="assign" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Staff Member <span class="text-red-500">*</span></label>
                    <select wire:model="userId" class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('userId') border-red-400 @enderror border-gray-300">
                        <option value="">Select staff member…</option>
                        @foreach($availableStaff as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }} — {{ $staff->getRoleNames()->first() }}</option>
                        @endforeach
                    </select>
                    @error('userId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Role on Job</label>
                        <select wire:model="role" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="lead">Lead</option>
                            <option value="assistant">Assistant</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Assigned Date</label>
                        <input wire:model="assignedDate" type="date" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <input wire:model="notes" type="text" placeholder="e.g. Bring heavy equipment" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg disabled:opacity-60">
                        <span wire:loading wire:target="assign">Assigning…</span>
                        <span wire:loading.remove wire:target="assign">Assign to Job</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)" class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Remove Confirm --}}
    @if($confirmingRemoveId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Remove Assignment?</h3>
            <p class="text-sm text-gray-500 mb-5">This staff member will be unassigned from this job.</p>
            <div class="flex gap-3">
                <button wire:click="removeAssignment" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg">Remove</button>
                <button wire:click="$set('confirmingRemoveId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
