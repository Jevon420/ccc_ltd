<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $invoice->reference }}</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $invoice->statusColour }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    @if($invoice->isOverdue)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700">⚠ Overdue</span>
                    @endif
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $invoice->title }}</h1>
                <p class="text-sm text-gray-500">{{ $invoice->client?->name ?? '—' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($invoice->status === 'draft')
                    @can('invoices.send')
                    <button wire:click="markSent"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Mark as Sent
                    </button>
                    @endcan
                @endif

                @if(in_array($invoice->status, ['sent', 'partial', 'overdue']))
                    @can('invoices.mark_paid')
                    <button wire:click="markPaid"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark as Paid
                    </button>
                    @endcan
                @endif

                @if(in_array($invoice->status, ['sent', 'partial', 'overdue']))
                    @can('payments.create')
                    <button wire:click="$dispatchTo('payments.record-payment', 'open-record-payment', { invoiceId: {{ $invoice->id }} })"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold px-4 py-2 border border-green-600 text-green-700 hover:bg-green-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Record Payment
                    </button>
                    @endcan
                @endif

                @if(! in_array($invoice->status, ['cancelled', 'paid']))
                    @can('invoices.edit')
                    <button wire:click="cancel"
                        class="text-sm font-semibold px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        Cancel Invoice
                    </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="md:col-span-2 space-y-5">

            {{-- Financials --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">Financials</h2>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium">TTD {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if($invoice->tax_rate > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tax ({{ $invoice->tax_rate }}%)</span>
                        <span class="font-medium">TTD {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-2 mt-2">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-bold text-gray-900">TTD {{ number_format($invoice->total, 2) }}</span>
                    </div>
                    @if($invoice->amount_paid > 0)
                    <div class="flex justify-between text-sm text-green-700">
                        <span>Amount Paid</span>
                        <span class="font-semibold">TTD {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold {{ $invoice->balanceDue > 0 ? 'text-red-700' : 'text-green-700' }}">
                        <span>Balance Due</span>
                        <span>TTD {{ number_format($invoice->balanceDue, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payments --}}
            @if($payments->count())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">Payment History</h2>
                <div class="space-y-2">
                    @foreach($payments as $payment)
                    <div class="flex items-center justify-between text-sm py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">TTD {{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->method }} · {{ $payment->paid_at->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">{{ ucfirst($payment->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($invoice->description || $invoice->notes)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                @if($invoice->description)
                <h2 class="text-sm font-bold text-gray-900 mb-3">Description</h2>
                <p class="text-sm text-gray-700 leading-relaxed mb-4">{{ $invoice->description }}</p>
                @endif
                @if($invoice->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-xs font-semibold text-amber-800 mb-1">Payment Notes</p>
                    <p class="text-sm text-amber-900">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Client</h3>
                <p class="font-semibold text-gray-900">{{ $invoice->client?->name ?? '—' }}</p>
                @if($invoice->client?->email)
                <a href="mailto:{{ $invoice->client->email }}" class="text-xs text-blue-600 hover:underline block mt-1">{{ $invoice->client->email }}</a>
                @endif
                @if($invoice->client?->phone)
                <p class="text-xs text-gray-500 mt-1">{{ $invoice->client->phone }}</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Dates</h3>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Issued</dt>
                        <dd class="font-medium">{{ $invoice->issue_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Due</dt>
                        <dd class="font-medium {{ $invoice->isOverdue ? 'text-red-600' : '' }}">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    @if($invoice->paid_date)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Paid</dt>
                        <dd class="font-medium text-green-600">{{ $invoice->paid_date->format('d M Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($invoice->job || $invoice->quote)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">Linked Records</h3>
                @if($invoice->job)
                <a href="{{ route('dashboard.jobs.show', $invoice->job) }}"
                    class="flex items-center gap-2 text-sm text-blue-700 hover:underline mb-2">
                    <span class="font-mono text-xs font-bold">{{ $invoice->job->reference }}</span>
                    <span class="text-xs text-gray-500 truncate">{{ $invoice->job->title }}</span>
                </a>
                @endif
                @if($invoice->quote)
                <a href="{{ route('dashboard.quotes.show', $invoice->quote) }}"
                    class="flex items-center gap-2 text-sm text-blue-700 hover:underline">
                    <span class="font-mono text-xs font-bold">{{ $invoice->quote->reference }}</span>
                    <span class="text-xs text-gray-500">Quote</span>
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <livewire:payments.record-payment @payment-saved="$refresh" />

    <a href="{{ route('dashboard.invoices.index') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Invoices
    </a>
</div>
