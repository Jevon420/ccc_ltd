<div class="relative" wire:poll.30000ms="$refresh">

    {{-- Bell Button --}}
    <button wire:click="toggleOpen"
        class="relative text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
        @if($unreadCount > 0)
        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 rounded-full text-white text-[9px] font-bold flex items-center justify-center">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($isOpen)
    <div class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 overflow-hidden"
         x-data @click.outside="$wire.set('isOpen', false)">

        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllRead"
                class="text-xs font-semibold text-blue-700 hover:text-blue-900 transition-colors">
                Mark all read
            </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notification)
            @php
            $data    = $notification->data;
            $isUnread = is_null($notification->read_at);
            $icon = match($data['type'] ?? 'info') {
                'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'emoji' => '✅'],
                'warning' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'emoji' => '⚠️'],
                'error'   => ['bg' => 'bg-red-100',   'text' => 'text-red-700',   'emoji' => '❌'],
                default   => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'emoji' => 'ℹ️'],
            };
            @endphp
            <div class="flex items-start gap-3 px-4 py-3 {{ $isUnread ? 'bg-blue-50/50' : '' }} hover:bg-gray-50 transition-colors"
                 wire:key="notif-{{ $notification->id }}">
                <div class="w-7 h-7 {{ $icon['bg'] }} rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 text-xs">
                    {{ $icon['emoji'] }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-800 leading-relaxed {{ $isUnread ? 'font-semibold' : '' }}">
                        {{ $data['message'] ?? '' }}
                    </p>
                    @if(isset($data['action_label']) && isset($data['action_url']))
                    <a href="{{ $data['action_url'] }}" wire:click="markRead('{{ $notification->id }}')"
                        class="text-xs font-semibold text-blue-700 hover:underline mt-1 inline-block">
                        {{ $data['action_label'] }} →
                    </a>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @if($isUnread)
                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></div>
                @endif
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-400">
                <div class="text-3xl mb-2">🔔</div>
                <p class="text-xs font-medium">No notifications yet</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
