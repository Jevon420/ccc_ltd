<div class="fixed bottom-5 right-5 z-[9998] flex flex-col items-end gap-3"
     x-data="{
        sending: false,
        inputValue: '',
        submitMessage() {
            if (!this.inputValue.trim() || this.sending) return;
            this.sending = true;
            $wire.input = this.inputValue;
            this.inputValue = '';
            $wire.send().then(() => { this.sending = false; });
        }
     }">

    {{-- Chat Window --}}
    @if($isOpen)
    <div class="w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
         style="height: 500px;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-slate-900 to-blue-900 px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="relative w-8 h-8 flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    {{-- Status dot: amber when thinking, green when idle --}}
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-slate-900 {{ $thinking ? 'bg-amber-400 animate-pulse' : 'bg-green-400' }}"></span>
                </div>
                <div>
                    <p class="text-white text-xs font-bold leading-tight">CCC Assistant</p>
                    <p class="text-[10px] leading-tight {{ $thinking ? 'text-amber-300' : 'text-blue-300' }} transition-colors duration-300">
                        @if($thinking) Thinking… @else Online · Here to help @endif
                    </p>
                </div>
            </div>
            <button wire:click="close" class="text-white/60 hover:text-white transition-colors p-1 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
             x-ref="chatMessages"
             x-init="$el.scrollTop = $el.scrollHeight"
             x-effect="$nextTick(() => $el.scrollTop = $el.scrollHeight)">

            @foreach($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                @if($msg['role'] === 'assistant')
                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mr-2 self-end mb-4">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @endif
                <div class="max-w-[78%]">
                    <div class="{{ $msg['role'] === 'user'
                        ? 'bg-blue-700 text-white rounded-2xl rounded-br-sm'
                        : 'bg-white text-gray-800 rounded-2xl rounded-bl-sm border border-gray-200 shadow-sm' }}
                        px-3.5 py-2.5 text-xs leading-relaxed">
                        {{ $msg['content'] }}
                    </div>
                    <p class="text-[9px] mt-1 text-gray-400 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left ml-1' }}">
                        {{ $msg['time'] ?? '' }}
                    </p>
                </div>
            </div>
            @endforeach

            {{-- Livewire thinking animation (Phase 2 — AI actually running) --}}
            @if($thinking)
            <div class="flex justify-start">
                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mr-2 self-end">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3.5 shadow-sm">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay:0ms; animation-duration:900ms;"></span>
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay:180ms; animation-duration:900ms;"></span>
                        <span class="w-2 h-2 bg-blue-300 rounded-full animate-bounce" style="animation-delay:360ms; animation-duration:900ms;"></span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Alpine "Analyzing" indicator — shows instantly when user hits send,
                 before the Livewire round-trip completes and $thinking becomes true --}}
            <template x-if="sending && !$wire.thinking">
                <div class="flex justify-start">
                    <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mr-2 self-end">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span class="text-[10px] text-gray-500 font-medium">Analyzing…</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input --}}
        @if(!$limitReached)
        <div class="flex-shrink-0 bg-white border-t border-gray-100 p-3">
            <form @submit.prevent="submitMessage()" class="flex gap-2 items-end">
                <textarea
                    x-model="inputValue"
                    @keydown.enter.prevent="if(!$event.shiftKey) submitMessage()"
                    placeholder="Type a message… (Enter to send)"
                    maxlength="500"
                    rows="1"
                    :disabled="$wire.thinking || sending"
                    x-init="$el.style.height = 'auto'"
                    @input="$el.style.height = 'auto'; $el.style.height = (Math.min($el.scrollHeight, 96)) + 'px'"
                    x-effect="if (!$wire.thinking && !sending) $nextTick(() => $el.focus())"
                    class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none leading-relaxed transition-colors disabled:bg-gray-50 disabled:text-gray-400"
                    style="max-height: 96px; overflow-y: auto;">
                </textarea>
                <button type="submit"
                    :disabled="$wire.thinking || sending || !inputValue.trim()"
                    class="flex-shrink-0 self-end bg-blue-700 hover:bg-blue-800 text-white p-2.5 rounded-xl transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg x-show="!sending && !$wire.thinking" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <svg x-show="sending || $wire.thinking" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </button>
            </form>
            <div class="flex items-center justify-between mt-1.5">
                <p class="text-[10px] text-gray-400">AI assistant · Not financial or legal advice</p>
                <p class="text-[10px] text-gray-300" x-show="inputValue.length > 400">
                    <span x-text="500 - inputValue.length"></span> remaining
                </p>
            </div>
        </div>
        @else
        <div class="flex-shrink-0 p-4 bg-gray-50 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500 mb-3">Session limit reached. Our team is ready to help.</p>
            <div class="flex gap-2 justify-center">
                <a href="{{ route('contact') }}"
                    class="inline-block bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                    Contact Form
                </a>
                @php $phone = \App\Models\Setting::get('company_phone', ''); @endphp
                @if($phone)
                <a href="tel:{{ $phone }}"
                    class="inline-block border border-blue-700 text-blue-700 text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                    Call Us
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Toggle Button --}}
    <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
        class="w-14 h-14 bg-blue-700 hover:bg-blue-800 text-white rounded-full shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95">
        <div class="transition-transform duration-300 {{ $isOpen ? 'rotate-0' : '' }}">
            @if($isOpen)
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            @endif
        </div>
    </button>
</div>
