<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">Job Reports ({{ $reports->count() }})</h3>
        @can('job_reports.create')
        <button wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Report
        </button>
        @endcan
    </div>

    {{-- Reports List --}}
    @forelse($reports as $report)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h4 class="font-semibold text-gray-900">{{ $report->title }}</h4>
                <p class="text-xs text-gray-500 mt-0.5">By {{ $report->creator?->name ?? 'Unknown' }} · {{ $report->created_at->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $report->statusColour }}">{{ ucfirst($report->status) }}</span>
                @if($report->status === 'submitted')
                    @can('job_reports.edit')
                    <button wire:click="approve({{ $report->id }})" class="text-xs font-semibold text-green-700 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50">Approve</button>
                    @endcan
                @endif
                @can('job_reports.edit')
                <button wire:click="openEdit({{ $report->id }})" class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50">Edit</button>
                @endcan
                @can('job_reports.delete')
                <button wire:click="confirmDelete({{ $report->id }})" class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">Delete</button>
                @endcan
            </div>
        </div>

        <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ $report->description }}</p>

        @if($report->work_performed)
        <div class="mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Work Performed</p>
            <p class="text-sm text-gray-700">{{ $report->work_performed }}</p>
        </div>
        @endif

        @if($report->issues_encountered)
        <div class="mb-2 bg-amber-50 border border-amber-200 rounded-lg p-3">
            <p class="text-xs font-semibold text-amber-800 uppercase mb-1">Issues Encountered</p>
            <p class="text-sm text-amber-900">{{ $report->issues_encountered }}</p>
        </div>
        @endif

        @if($report->recommendations)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="text-xs font-semibold text-blue-800 uppercase mb-1">Recommendations</p>
            <p class="text-sm text-blue-900">{{ $report->recommendations }}</p>
        </div>
        @endif

        {{-- Attachment --}}
        @if($report->getFirstMedia('attachments'))
        @php $att = $report->getFirstMedia('attachments'); @endphp
        <div class="mt-3 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            <a href="{{ $att->getUrl() }}" target="_blank" class="text-xs font-semibold text-blue-700 hover:underline truncate">
                {{ $att->file_name }}
            </a>
            <span class="text-xs text-gray-400 ml-auto flex-shrink-0">{{ round($att->size / 1024) }}KB</span>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-10 text-center text-gray-400">
        <div class="text-3xl mb-3">📋</div>
        <p class="text-sm font-medium">No reports submitted yet</p>
    </div>
    @endforelse

    {{-- Create / Edit Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h2 class="text-base font-bold text-gray-900">{{ $editingId ? 'Edit Report' : 'New Report' }}</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" placeholder="e.g. Weekly Site Report — 2 June 2026"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Description / Summary <span class="text-red-500">*</span></label>
                    <textarea wire:model="description" rows="3" placeholder="Brief summary of the report" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('description') border-red-400 @enderror"></textarea>
                    @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Work Performed</label>
                    <textarea wire:model="workPerformed" rows="3" placeholder="Describe what was done" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Issues Encountered</label>
                    <textarea wire:model="issuesEncountered" rows="2" placeholder="Any problems or obstacles" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Recommendations</label>
                    <textarea wire:model="recommendations" rows="2" placeholder="Suggestions for follow-up" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">Draft</option>
                            <option value="submitted">Submit for Review</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Attachment</label>
                        <input wire:model="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png"
                            class="w-full text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('attachment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg disabled:opacity-60">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Save Report' }}</span>
                    </button>
                    <button type="button" wire:click="$set('isOpen', false)" class="px-4 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Report?</h3>
            <p class="text-sm text-gray-500 mb-6">This will permanently delete the report and any attached files.</p>
            <div class="flex gap-3">
                <button wire:click="deleteReport" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg">Yes, Delete</button>
                <button wire:click="$set('confirmingDeleteId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
