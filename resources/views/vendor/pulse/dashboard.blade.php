<x-pulse>
    {{-- Mobile notice --}}
    <div class="col-span-full block md:hidden">
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Pulse is optimised for desktop. For the best experience, open on a larger screen.
        </div>
    </div>

    {{-- Server & Queue health --}}
    <livewire:pulse.servers cols="full" />
    <livewire:pulse.queues cols="4" />
    <livewire:pulse.cache cols="4" />
    <livewire:pulse.usage cols="4" rows="2" />

    {{-- Performance --}}
    <livewire:pulse.slow-requests cols="8" rows="2" />
    <livewire:pulse.slow-jobs cols="4" rows="2" />
    <livewire:pulse.slow-queries cols="8" rows="2" />
    <livewire:pulse.slow-outgoing-requests cols="4" rows="2" />

    {{-- Errors --}}
    <livewire:pulse.exceptions cols="full" rows="2" />
</x-pulse>
