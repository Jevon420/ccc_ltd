<div class="fixed bottom-5 right-5 z-[9998] flex flex-col items-end gap-3">

    {{-- Chat Window --}}
    @if($isOpen)
    <div class="w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
         style="height: 480px;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-slate-900 to-blue-900 px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <p class="text-white text-xs font-bold leading-tight">CCC Assistant</p>
                    <p class="text-blue-300 text-[10px]">
                        @if($thinking) Typing… @else Online @endif
                    </p>
                </div>
            </div>
            <button wire:click="close" class="text-white/60 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
             x-data x-ref="messages"
             x-init="$el.scrollTop = $el.scrollHeight"
             x-effect="$nextTick(() => $el.scrollTop = $el.scrollHeight)">

            @foreach($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] {{ $msg['role'] === 'user'
                    ? 'bg-blue-700 text-white rounded-2xl rounded-br-sm'
                    : 'bg-white text-gray-800 rounded-2xl rounded-bl-sm border border-gray-200 shadow-sm' }}
                    px-3.5 py-2.5 text-xs leading-relaxed">
                    {{ $msg['content'] }}
                    <p class="text-[10px] mt-1 {{ $msg['role'] === 'user' ? 'text-blue-200' : 'text-gray-400' }} text-right">
                        {{ $msg['time'] ?? '' }}
                    </p>
                </div>
            </div>
            @endforeach

            @if($thinking)
            <div class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Input --}}
        @if(!$limitReached)
        <div class="flex-shrink-0 p-3 bg-white border-t border-gray-100">
            <form wire:submit="send" class="flex gap-2">
                <input wire:model="input" type="text"
                    placeholder="Type a message…"
                    maxlength="500"
                    autocomplete="off"
                    class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    {{ $thinking ? 'disabled' : '' }}>
                <button type="submit" wire:loading.attr="disabled"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-3 py-2 rounded-xl transition-colors disabled:opacity-60 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 text-center mt-1.5">
                AI assistant · Not financial or legal advice
            </p>
        </div>
        @else
        <div class="flex-shrink-0 p-3 bg-gray-50 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500 mb-2">Session limit reached.</p>
            <a href="{{ route('contact') }}"
                class="inline-block bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                Use Contact Form
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- Toggle Button --}}
    <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
        class="w-14 h-14 bg-blue-700 hover:bg-blue-800 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105">
        @if($isOpen)
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        @else
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        @endif
    </button>
</div>
