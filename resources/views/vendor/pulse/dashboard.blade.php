<x-pulse>
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
