<div>
    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-center">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search description, model…"
            class="flex-1 min-w-48 px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">

        <select wire:model.live="event"
            class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">All Events</option>
            @foreach($events as $evt)
            <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
            @endforeach
        </select>

        @if($search || $event)
        <button wire:click="$set('search', ''); $set('event', '')"
            class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            Clear
        </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Activity Log</h2>
            <span class="text-xs text-gray-400">{{ $logs->total() }} entries</span>
        </div>

        @if($logs->isEmpty())
        <div class="py-16 text-center">
            <div class="text-5xl mb-4">📋</div>
            <p class="text-sm font-semibold text-gray-700">No activity recorded yet</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Event</th>
                        <th class="px-6 py-3 text-left">Subject</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-left">When</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" wire:click="viewDetail({{ $log->id }})">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                    {{ strtoupper(substr($log->causer?->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-gray-900 truncate max-w-28">
                                    {{ $log->causer?->name ?? 'System' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            @php
                            $eventColor = match($log->event) {
                                'created' => 'bg-green-100 text-green-800',
                                'updated' => 'bg-blue-100 text-blue-800',
                                'deleted' => 'bg-red-100 text-red-800',
                                'restored' => 'bg-amber-100 text-amber-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $eventColor }}">
                                {{ ucfirst($log->event ?? 'action') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-600">
                            {{ class_basename($log->subject_type ?? '—') }}
                            @if($log->subject_id)
                            <span class="text-gray-400 font-mono">#{{ $log->subject_id }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-500">
                            <span class="truncate max-w-xs block">{{ Str::limit($log->description, 70) }}</span>
                        </td>
                        <td class="px-6 py-3 text-xs text-gray-400 whitespace-nowrap">
                            <span title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <button class="text-xs font-semibold text-blue-700 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($detail)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    @php
                    $eventColor = match($detail->event) {
                        'created'  => 'bg-green-100 text-green-800',
                        'updated'  => 'bg-blue-100 text-blue-800',
                        'deleted'  => 'bg-red-100 text-red-800',
                        'restored' => 'bg-amber-100 text-amber-800',
                        default    => 'bg-gray-100 text-gray-700',
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $eventColor }}">
                        {{ ucfirst($detail->event ?? 'action') }}
                    </span>
                    <h2 class="text-base font-bold text-gray-900">
                        {{ class_basename($detail->subject_type ?? 'Activity') }}
                        @if($detail->subject_id)
                        <span class="font-mono text-gray-500 text-sm">#{{ $detail->subject_id }}</span>
                        @endif
                    </h2>
                </div>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-5">

                {{-- Meta --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Performed By</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($detail->causer?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $detail->causer?->name ?? 'System' }}</p>
                                @if($detail->causer?->email)
                                <p class="text-xs text-gray-500">{{ $detail->causer->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Timestamp</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detail->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $detail->created_at->format('H:i:s') }} · {{ $detail->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Description</p>
                    <p class="text-sm text-gray-800 bg-gray-50 rounded-xl px-4 py-3">{{ $detail->description }}</p>
                </div>

                {{-- Changed Properties --}}
                @if($detail->properties && $detail->properties->isNotEmpty())
                @php
                $old = $detail->properties->get('old', []);
                $new = $detail->properties->get('attributes', $detail->properties->get('new', []));
                @endphp

                @if(!empty($old) || !empty($new))
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">
                        {{ empty($old) ? 'Values' : 'Changes' }}
                    </p>
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">Field</th>
                                    @if(!empty($old)) <th class="text-left px-4 py-2 font-semibold text-red-500">Before</th> @endif
                                    <th class="text-left px-4 py-2 font-semibold {{ empty($old) ? 'text-gray-500' : 'text-green-600' }}">
                                        {{ empty($old) ? 'Value' : 'After' }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($new as $field => $value)
                                @php $oldValue = $old[$field] ?? null; @endphp
                                <tr class="{{ (!empty($old) && $oldValue !== $value) ? 'bg-amber-50/50' : '' }}">
                                    <td class="px-4 py-2 font-mono font-semibold text-gray-700">{{ $field }}</td>
                                    @if(!empty($old))
                                    <td class="px-4 py-2 text-red-600">
                                        {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?? '—') }}
                                    </td>
                                    @endif
                                    <td class="px-4 py-2 text-gray-900 {{ (!empty($old) && $oldValue !== $value) ? 'font-semibold text-green-700' : '' }}">
                                        {{ is_array($value) ? json_encode($value) : ($value ?? '—') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Raw JSON fallback for other properties --}}
                @php $otherProps = $detail->properties->except(['old', 'attributes', 'new']); @endphp
                @if($otherProps->isNotEmpty())
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Additional Data</p>
                    <pre class="text-xs bg-gray-900 text-green-400 rounded-xl px-4 py-3 overflow-x-auto">{{ json_encode($otherProps->toArray(), JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif

                @endif

                {{-- Log Name / Batch --}}
                <div class="flex gap-4 text-xs text-gray-400 pt-2 border-t border-gray-100">
                    <span>Log: <span class="font-mono">{{ $detail->log_name }}</span></span>
                    @if($detail->batch_uuid)
                    <span>Batch: <span class="font-mono">{{ substr($detail->batch_uuid, 0, 8) }}…</span></span>
                    @endif
                    <span>ID: <span class="font-mono">{{ $detail->id }}</span></span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
