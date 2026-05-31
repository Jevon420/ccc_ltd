<div
    x-data="{
        toasts: [],
        add(toast) {
            toast.id = Date.now();
            this.toasts.push(toast);
            setTimeout(() => this.remove(toast.id), toast.duration ?? 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 w-80 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto flex items-start gap-3 rounded-xl shadow-lg border px-4 py-3"
            :class="{
                'bg-green-50 border-green-200': toast.type === 'success',
                'bg-red-50 border-red-200':     toast.type === 'error',
                'bg-amber-50 border-amber-200': toast.type === 'warning',
                'bg-blue-50 border-blue-200':   toast.type === 'info',
            }"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>

            {{-- Message --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium"
                    :class="{
                        'text-green-800': toast.type === 'success',
                        'text-red-800':   toast.type === 'error',
                        'text-amber-800': toast.type === 'warning',
                        'text-blue-800':  toast.type === 'info',
                    }"
                    x-text="toast.message"></p>
            </div>

            {{-- Dismiss --}}
            <button @click="remove(toast.id)" class="flex-shrink-0 ml-1"
                :class="{
                    'text-green-500 hover:text-green-700': toast.type === 'success',
                    'text-red-500 hover:text-red-700':     toast.type === 'error',
                    'text-amber-500 hover:text-amber-700': toast.type === 'warning',
                    'text-blue-500 hover:text-blue-700':   toast.type === 'info',
                }"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
