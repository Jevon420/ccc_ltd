<div class="space-y-3">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Work Orders ({{ $workOrders->count() }})</h3>
            @php $done = $workOrders->where('status', 'completed')->count(); $total = $workOrders->whereNotIn('status', ['cancelled'])->count(); @endphp
            @if($total > 0)
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden w-24">
                    <div class="h-full bg-green-500 rounded-full transition-all duration-500"
                         style="width: {{ $total > 0 ? round(($done / $total) * 100) : 0 }}%"></div>
                </div>
                <span class="text-xs text-gray-400">{{ $done }}/{{ $total }} done</span>
            </div>
            @endif
        </div>
        @can('work_orders.create')
        <button wire:click="openCreate"
            class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Task
        </button>
        @endcan
    </div>

    {{-- Work Orders List --}}
    @forelse($workOrders as $wo)
    <div class="bg-white rounded-xl border {{ $wo->status === 'completed' ? 'border-green-200 bg-green-50/30' : ($wo->status === 'cancelled' ? 'border-gray-200 opacity-60' : 'border-gray-200') }} p-4">
        <div class="flex items-start gap-3">
            {{-- Complete checkbox --}}
            @can('work_orders.complete')
            @if($wo->status === 'pending' || $wo->status === 'in_progress')
            <button wire:click="openComplete({{ $wo->id }})"
                class="flex-shrink-0 mt-0.5 w-5 h-5 rounded border-2 border-gray-300 hover:border-green-500 hover:bg-green-50 transition-colors flex items-center justify-center group">
                <svg class="w-3 h-3 text-gray-300 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </button>
            @else
            <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded border-2 border-green-500 bg-green-500 flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            @endif
            @else
            <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded border-2 {{ $wo->status === 'completed' ? 'border-green-500 bg-green-500' : 'border-gray-300' }} flex items-center justify-center">
                @if($wo->status === 'completed')
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            @endcan

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-sm font-semibold text-gray-900 {{ $wo->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                        {{ $wo->title }}
                    </p>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $wo->priorityColour }}">{{ ucfirst($wo->priority) }}</span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $wo->statusColour }}">{{ ucfirst(str_replace('_', ' ', $wo->status)) }}</span>
                    </div>
                </div>

                @if($wo->description)
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $wo->description }}</p>
                @endif

                <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                    @if($wo->due_date)
                    <span class="{{ $wo->due_date->isPast() && $wo->status !== 'completed' ? 'text-red-500 font-semibold' : '' }}">
                        Due {{ $wo->due_date->format('d M Y') }}
                    </span>
                    @endif
                    @if($wo->completed_at)
                    <span class="text-green-600">Completed {{ $wo->completed_at->diffForHumans() }}</span>
                    @endif
                    @if($wo->completion_notes)
                    <span class="italic">{{ Str::limit($wo->completion_notes, 50) }}</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1 flex-shrink-0">
                @can('work_orders.edit')
                @if($wo->status !== 'completed')
                <button wire:click="openEdit({{ $wo->id }})"
                    class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50">Edit</button>
                @endif
                @endcan
                @can('work_orders.delete')
                <button wire:click="confirmDelete({{ $wo->id }})"
                    class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">Delete</button>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-6 text-center text-gray-400">
        <p class="text-sm font-medium">No work orders yet</p>
        <p class="text-xs mt-1">Break this job down into specific tasks</p>
    </div>
    @endforelse

    {{-- Create / Edit Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">{{ $editingId ? 'Edit Task' : 'New Task' }}</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Task Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" placeholder="e.g. Clear perimeter fence line"
                        class="w-full text-sm border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror border-gray-300">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Priority</label>
                        <select wire:model="priority" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Due Date</label>
                        <input wire:model="dueDate" type="date" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save' : 'Create Task' }}</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)" class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Complete Modal --}}
    @if($completingId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
            <h3 class="text-base font-bold text-gray-900 mb-1">Mark as Complete?</h3>
            <p class="text-xs text-gray-500 mb-4">Add optional completion notes for the record.</p>
            <textarea wire:model="completionNotes" rows="3" placeholder="e.g. Work completed, site left clean and secure."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none mb-4"></textarea>
            <div class="flex gap-3">
                <button wire:click="complete" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg">Mark Complete</button>
                <button wire:click="$set('completingId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Work Order?</h3>
            <div class="flex gap-3 mt-4">
                <button wire:click="deleteWorkOrder" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg">Delete</button>
                <button wire:click="$set('confirmingDeleteId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
