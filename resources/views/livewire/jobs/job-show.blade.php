<div class="space-y-6">

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $job->reference }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $job->statusColour }}">
                        {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $job->priorityColour }}">
                        {{ ucfirst($job->priority) }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $job->title }}</h1>
                <p class="text-sm text-gray-500">
                    {{ $job->client->name ?? '—' }}
                    @if($job->serviceType) · {{ $job->serviceType->name }} @endif
                </p>
            </div>

            @can('jobs.edit')
            <div class="flex flex-wrap gap-2">
                @foreach(['pending' => 'Pending', 'scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                @if($job->status !== $value)
                <button wire:click="updateStatus('{{ $value }}')"
                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                    → {{ $label }}
                </button>
                @endif
                @endforeach
            </div>
            @endcan
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Job Details --}}
        <div class="md:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Job Details</h2>
            <dl class="space-y-3">
                <div class="grid grid-cols-3 gap-2">
                    <dt class="text-xs font-semibold text-gray-500 uppercase">Location</dt>
                    <dd class="col-span-2 text-sm text-gray-800">{{ $job->location ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <dt class="text-xs font-semibold text-gray-500 uppercase">Scheduled</dt>
                    <dd class="col-span-2 text-sm text-gray-800">
                        {{ $job->scheduled_date?->format('d M Y') ?? '—' }}
                        @if($job->scheduled_time) at {{ \Carbon\Carbon::parse($job->scheduled_time)->format('g:i A') }} @endif
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <dt class="text-xs font-semibold text-gray-500 uppercase">Completed</dt>
                    <dd class="col-span-2 text-sm text-gray-800">{{ $job->completed_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                @if($job->description)
                <div class="pt-2 border-t border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-2">Description</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $job->description }}</dd>
                </div>
                @endif
                @if($job->notes)
                <div class="pt-2 border-t border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase mb-2">Internal Notes</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed bg-amber-50 border border-amber-200 rounded-lg p-3">{{ $job->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            {{-- Client --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Client</h3>
                <p class="font-semibold text-gray-900">{{ $job->client->name ?? '—' }}</p>
                @if($job->client?->contact_person)
                <p class="text-xs text-gray-500 mt-1">{{ $job->client->contact_person }}</p>
                @endif
                @if($job->client?->phone)
                <p class="text-xs text-gray-500">{{ $job->client->phone }}</p>
                @endif
                @if($job->client?->email)
                <a href="mailto:{{ $job->client->email }}" class="text-xs text-blue-600 hover:underline">{{ $job->client->email }}</a>
                @endif
            </div>

            {{-- Created --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Record</h3>
                <p class="text-xs text-gray-600">Created {{ $job->created_at->format('d M Y') }}</p>
                <p class="text-xs text-gray-600">Updated {{ $job->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs: Work Orders, Team, Photos, Reports, Documents --}}
    <div x-data="{ tab: 'workorders' }" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            @foreach(['workorders' => '✅ Work Orders', 'team' => '👥 Team', 'photos' => '📷 Photos', 'reports' => '📋 Reports', 'documents' => '📎 Documents'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'border-b-2 border-blue-700 text-blue-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-5 py-3 text-sm transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <div class="p-5">
            @can('work_orders.view')
            <div x-show="tab === 'workorders'"><livewire:jobs.work-orders :job="$job" /></div>
            @endcan
            @can('job_assignments.view')
            <div x-show="tab === 'team'"><livewire:jobs.job-assignments :job="$job" /></div>
            @endcan
            @can('job_photos.view')
            <div x-show="tab === 'photos'"><livewire:jobs.job-photos :job="$job" /></div>
            @endcan
            @can('job_reports.view')
            <div x-show="tab === 'reports'"><livewire:jobs.job-reports :job="$job" /></div>
            @endcan
            @can('documents.view')
            <div x-show="tab === 'documents'"><livewire:documents.document-manager :documentable="$job" /></div>
            @endcan
        </div>
    </div>

    {{-- Back --}}
    <div>
        <a href="{{ route('dashboard.jobs.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Jobs
        </a>
    </div>
</div>
