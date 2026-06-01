<div wire:poll.60000ms="refresh">

    {{-- Stats Cards --}}
    <div id="dashboard-cards" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @php
        $cards = [
            ['label' => 'Active Jobs',       'key' => 'active_jobs',          'route' => 'dashboard.jobs.index',     'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'blue'],
            ['label' => 'Pending Quotes',    'key' => 'pending_quotes',       'route' => 'dashboard.quotes.index',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'violet'],
            ['label' => 'Unpaid Invoices',   'key' => 'unpaid_invoices',      'route' => 'dashboard.invoices.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber'],
            ['label' => 'Overdue Invoices',  'key' => 'overdue_invoices',     'route' => 'dashboard.invoices.index', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'red'],
            ['label' => 'In Maintenance',    'key' => 'equipment_issues',     'route' => 'dashboard.equipment.index','icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'color' => 'orange'],
            ['label' => 'Done This Month',   'key' => 'completed_this_month', 'route' => 'dashboard.jobs.index',    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
        ];
        $colorMap = [
            'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-600',   'num' => 'text-blue-700'],
            'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-600',  'num' => 'text-green-700'],
            'amber'  => ['bg' => 'bg-amber-50',  'icon' => 'text-amber-600',  'num' => 'text-amber-700'],
            'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-600',    'num' => 'text-red-700'],
            'violet' => ['bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'num' => 'text-violet-700'],
            'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'num' => 'text-orange-700'],
        ];
        @endphp

        @foreach($cards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        @if(Route::has($card['route']))
        <a href="{{ route($card['route']) }}"
            class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all group block">
        @else
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
        @endif
            <div class="w-9 h-9 {{ $c['bg'] }} rounded-xl flex items-center justify-center mb-3">
                <svg class="w-4 h-4 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div class="text-2xl font-bold {{ $stats[$card['key']] > 0 ? $c['num'] : 'text-gray-900' }}">
                {{ $stats[$card['key']] }}
            </div>
            <div class="text-xs text-gray-500 mt-0.5 group-hover:text-gray-700 transition-colors">{{ $card['label'] }}</div>
        @if(Route::has($card['route']))
        </a>
        @else
        </div>
        @endif
        @endforeach
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div id="recent-activity" class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Recent Activity</h2>
                <button wire:click="refresh" class="text-xs text-gray-400 hover:text-blue-700 transition-colors" title="Refresh">
                    <svg class="w-4 h-4" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentActivity as $activity)
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-xs font-bold text-blue-700">
                            {{ strtoupper(substr($activity->causer?->name ?? 'S', 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-700">
                            <span class="font-semibold">{{ $activity->causer?->name ?? 'System' }}</span>
                            {{ $activity->description }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <div class="text-3xl mb-3">📋</div>
                    <p class="text-sm text-gray-500 font-medium">No activity yet</p>
                    <p class="text-xs text-gray-400 mt-1">Actions taken across the portal will appear here.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @can('jobs.create')
                    @if(Route::has('dashboard.jobs.index'))
                    <a href="{{ route('dashboard.jobs.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Job
                    </a>
                    @endif
                    @endcan
                    @can('quotes.create')
                    <a href="{{ route('dashboard.quotes.create') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        AI Quote Drafter
                    </a>
                    @endcan
                    @can('clients.create')
                    @if(Route::has('dashboard.clients.index'))
                    <a href="{{ route('dashboard.clients.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Client
                    </a>
                    @endif
                    @endcan
                    @can('invoices.create')
                    @if(Route::has('dashboard.invoices.index'))
                    <a href="{{ route('dashboard.invoices.index') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Invoice
                    </a>
                    @endif
                    @endcan
                    @can('ai_tools.use')
                    <a href="{{ route('dashboard.ai-tools') }}" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-700 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        AI Tools
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
