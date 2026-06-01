<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0
                    {{ $client->type === 'company' ? 'bg-blue-600' : 'bg-purple-600' }}">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-xl font-bold text-gray-900">{{ $client->name }}</h1>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $client->type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($client->type) }}
                        </span>
                        @if($client->is_active)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                        </span>
                        @else
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </div>
                    @if($client->contact_person)
                    <p class="text-sm text-gray-500">Contact: {{ $client->contact_person }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                @can('clients.edit')
                <button wire:click="openEdit"
                    class="text-sm font-semibold px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                    Edit
                </button>
                <button wire:click="toggleActive"
                    class="text-sm font-semibold px-4 py-2 {{ $client->is_active ? 'border border-amber-300 text-amber-700 hover:bg-amber-50' : 'border border-green-300 text-green-700 hover:bg-green-50' }} rounded-lg transition-colors">
                    {{ $client->is_active ? 'Deactivate' : 'Activate' }}
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Contact Details --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Contact Details</h3>
            <dl class="space-y-3 text-sm">
                @if($client->email)
                <div>
                    <dt class="text-xs text-gray-400">Email</dt>
                    <dd><a href="mailto:{{ $client->email }}" class="text-blue-700 hover:underline">{{ $client->email }}</a></dd>
                </div>
                @endif
                @if($client->phone)
                <div>
                    <dt class="text-xs text-gray-400">Phone</dt>
                    <dd class="text-gray-800">{{ $client->phone }}</dd>
                </div>
                @endif
                @if($client->address)
                <div>
                    <dt class="text-xs text-gray-400">Address</dt>
                    <dd class="text-gray-800">{{ $client->address }}</dd>
                </div>
                @endif
                @if($client->city)
                <div>
                    <dt class="text-xs text-gray-400">City</dt>
                    <dd class="text-gray-800">{{ $client->city }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-gray-400">Client since</dt>
                    <dd class="text-gray-800">{{ $client->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
            @if($client->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Notes</p>
                <p class="text-sm text-gray-700">{{ $client->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Recent Jobs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Recent Jobs</h3>
                @if(Route::has('dashboard.jobs.index'))
                <a href="{{ route('dashboard.jobs.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
                @endif
            </div>
            @forelse($jobs as $job)
            <div class="py-2 border-b border-gray-50 last:border-0">
                <a href="{{ route('dashboard.jobs.show', $job) }}" class="font-mono text-xs font-bold text-blue-700 hover:underline">{{ $job->reference }}</a>
                <p class="text-xs text-gray-600 truncate">{{ $job->title }}</p>
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $job->statusColour }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">No jobs yet</p>
            @endforelse
        </div>

        {{-- Financial Summary --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Financial</h3>
            <div class="space-y-3">
                @forelse($invoices as $invoice)
                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                    <div>
                        <a href="{{ route('dashboard.invoices.show', $invoice) }}" class="font-mono text-xs font-bold text-blue-700 hover:underline">{{ $invoice->reference }}</a>
                        <p class="text-[10px] text-gray-400">{{ $invoice->issue_date?->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-900">TTD {{ number_format($invoice->total, 2) }}</p>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $invoice->statusColour }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">No invoices yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Documents for this client --}}
    @can('documents.view')
    <livewire:documents.document-manager :documentable="$client" />
    @endcan

    <livewire:clients.client-form />

    <a href="{{ route('dashboard.clients.index') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Clients
    </a>
</div>
