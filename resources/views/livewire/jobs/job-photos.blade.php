<div class="space-y-5">

    {{-- Upload Area --}}
    @can('job_photos.upload')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Upload Photos</h3>
        <form wire:submit="upload" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Select photos (JPG, PNG, WEBP — max 10MB each)</label>
                <input wire:model="photos" type="file" multiple accept="image/*"
                    class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('photos.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div wire:loading wire:target="photos" class="text-xs text-blue-600">Processing files…</div>
            <button type="submit" wire:loading.attr="disabled" wire:target="upload"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors disabled:opacity-60">
                <span wire:loading.remove wire:target="upload">📷 Upload Photos</span>
                <span wire:loading wire:target="upload">Uploading to Wasabi…</span>
            </button>
        </form>
    </div>
    @endcan

    {{-- Photo Grid --}}
    @if($media->count())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($media as $photo)
        <div class="group relative bg-gray-100 rounded-xl overflow-hidden aspect-square">
            <img src="{{ $photo->getUrl('thumb') }}"
                alt="{{ $photo->file_name }}"
                class="w-full h-full object-cover"
                loading="lazy">

            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                <div class="flex gap-2">
                    <a href="{{ $photo->getUrl() }}" target="_blank"
                        class="bg-white text-gray-900 text-xs font-semibold px-2 py-1 rounded-lg shadow hover:bg-gray-100 transition-colors">
                        View
                    </a>
                    @can('job_photos.delete')
                    <button wire:click="confirmDelete({{ $photo->id }})"
                        class="bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded-lg shadow hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                    @endcan
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-2 py-1">
                <p class="text-white text-[10px] truncate">{{ $photo->file_name }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-10 text-center text-gray-400">
        <div class="text-4xl mb-3">📷</div>
        <p class="text-sm font-medium">No photos uploaded yet</p>
        <p class="text-xs mt-1">Photos are stored securely on Wasabi cloud storage</p>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($confirmingDeleteId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-base font-bold text-gray-900 mb-2">Delete Photo?</h3>
            <p class="text-sm text-gray-500 mb-6">This will permanently remove the photo from cloud storage.</p>
            <div class="flex gap-3">
                <button wire:click="deletePhoto" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-lg">Yes, Delete</button>
                <button wire:click="$set('confirmingDeleteId', null)" class="flex-1 border border-gray-300 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
