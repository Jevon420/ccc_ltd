<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Search reference, title, client…"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <select wire:model.live="priorityFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            @can('jobs.restore')
            <button wire:click="$toggle('showTrashed')"
                class="text-xs font-semibold px-3 py-2 rounded-lg border transition-colors {{ $showTrashed ? 'bg-amber-100 border-amber-300 text-amber-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $showTrashed ? 'Showing Cancelled' : 'Show Cancelled' }}
            </button>
            @endcan

            @can('jobs.create')
            <button wire:click="$dispatchTo('jobs.job-form', 'open-create')"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Job
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
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Reference</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Job</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Priority</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Scheduled</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jobs as $job)
                    <tr class="hover:bg-gray-50 transition-colors {{ $job->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('dashboard.jobs.show', $job) }}"
                                class="font-mono text-xs font-bold text-blue-700 hover:text-blue-900">
                                {{ $job->reference }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-900 truncate max-w-xs">{{ $job->title }}</p>
                            @if($job->serviceType)
                            <p class="text-xs text-gray-500">{{ $job->serviceType->name }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-700">{{ $job->client->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $job->statusColour }}">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $job->priorityColour }}">
                                {{ ucfirst($job->priority) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">
                            {{ $job->scheduled_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($job->trashed())
                                    @can('jobs.restore')
                                    <button wire:click="confirmRestore({{ $job->id }})"
                                        class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors">
                                        Restore
                                    </button>
                                    @endcan
                                @else
                                    <a href="{{ route('dashboard.jobs.show', $job) }}"
                                        class="text-xs font-semibold text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                        View
                                    </a>
                                    @can('jobs.edit')
                                    <button wire:click="$dispatchTo('jobs.job-form', 'open-edit', { jobId: {{ $job->id }} })"
                                        class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                        Edit
                                    </button>
                                    @endcan
                                    @can('jobs.delete')
                                    <button wire:click="confirmDelete({{ $job->id }})"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                        Cancel
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm font-medium">No jobs found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>

    {{-- Cancel Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Cancel Job?</h3>
            <p class="text-sm text-gray-500 mb-6">The job will be marked as cancelled and hidden from active lists. It can be restored later.</p>
            <div class="flex gap-3">
                <button wire:click="deleteJob" wire:loading.attr="disabled"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="deleteJob">Cancelling…</span>
                    <span wire:loading.remove wire:target="deleteJob">Yes, Cancel Job</span>
                </button>
                <button wire:click="$set('confirmingDeleteId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Keep Job
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
            <h3 class="text-base font-bold text-gray-900 mb-2">Restore Job?</h3>
            <p class="text-sm text-gray-500 mb-6">The job will be restored to active lists.</p>
            <div class="flex gap-3">
                <button wire:click="restoreJob" wire:loading.attr="disabled"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading wire:target="restoreJob">Restoring…</span>
                    <span wire:loading.remove wire:target="restoreJob">Yes, Restore</span>
                </button>
                <button wire:click="$set('confirmingRestoreId', null)"
                    class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:jobs.job-form @job-saved="$refresh" />
</div>
