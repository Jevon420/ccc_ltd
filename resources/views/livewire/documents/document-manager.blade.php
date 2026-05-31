<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">Documents ({{ $documents->count() }})</h3>
        @can('documents.upload')
        <button wire:click="openUpload"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Document
        </button>
        @endcan
    </div>

    {{-- Document List --}}
    @forelse($documents as $doc)
    @php $file = $doc->getFirstMedia('file'); @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
        {{-- Icon by type --}}
        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-700">
            @if($file && str_contains($file->mime_type, 'pdf'))
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            @elseif($file && str_contains($file->mime_type, 'image'))
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-900 text-sm truncate">{{ $doc->title }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                @if($doc->category)
                <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium">{{ $doc->category }}</span>
                @endif
                @if($file)
                <span class="text-xs text-gray-400">{{ $file->file_name }} · {{ round($file->size / 1024) }}KB</span>
                @endif
                <span class="text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            @if($file)
            <a href="{{ $file->getUrl() }}" target="_blank" rel="noopener"
                class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                @can('documents.download') Download @else View @endcan
            </a>
            @endif
            @can('documents.delete')
            <button wire:click="confirmDelete({{ $doc->id }})"
                class="text-xs font-semibold text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition-colors">
                Delete
            </button>
            @endcan
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-400">
        <div class="text-3xl mb-3">📎</div>
        <p class="text-sm font-medium">No documents attached</p>
        <p class="text-xs mt-1">Files are stored securely on Wasabi cloud storage</p>
    </div>
    @endforelse

    {{-- Upload Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">Upload Document</h2>
                <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit="upload" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Document Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" placeholder="e.g. Site Permit 2026" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Category</label>
                    <select wire:model="category" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select category…</option>
                        @foreach(\App\Livewire\Documents\DocumentManager::$categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                    <input wire:model="file" type="file" class="w-full text-xs text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, images, ZIP — max 50MB</p>
                    @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="file" class="text-xs text-blue-600 mt-1">Processing…</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="upload"
                        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-2.5 rounded-lg disabled:opacity-60">
                        <span wire:loading.remove wire:target="upload">Upload to Wasabi</span>
                        <span wire:loading wire:target="upload">Uploading…</span>
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
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Document?</h3>
            <p class="text-sm text-gray-500 mb-6">The file will be permanently removed from Wasabi cloud storage.</p>
            <div class="flex gap-3">
                <button wire:click="deleteDocument" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg">Yes, Delete</button>
                <button wire:click="$set('confirmingDeleteId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
