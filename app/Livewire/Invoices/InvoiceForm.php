<?php

namespace App\Livewire\Invoices;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Quote;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceForm extends Component
{
    use HasToast;

    protected $listeners = [
        'open-create' => 'openCreate',
        'open-edit' => 'openEdit',
    ];

    public ?int $invoiceId = null;

    public bool $isOpen = false;

    public string $title = '';

    public string $description = '';

    public ?int $clientId = null;

    public ?int $jobId = null;

    public ?int $quoteId = null;

    public string $subtotal = '';

    public string $taxRate = '0';

    public string $status = 'draft';

    public string $issueDate = '';

    public string $dueDate = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'clientId' => ['required', 'exists:clients,id'],
            'jobId' => ['nullable', 'exists:work_jobs,id'],
            'quoteId' => ['nullable', 'exists:quotes,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'taxRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'])],
            'issueDate' => ['nullable', 'date'],
            'dueDate' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatedClientId(): void
    {
        $this->jobId = null;
        $this->quoteId = null;
    }

    public function updatedQuoteId(?int $value): void
    {
        if ($value && $quote = Quote::find($value)) {
            $this->clientId = $quote->client_id ?? $this->clientId;
            $this->title = $this->title ?: "Invoice for {$quote->reference}";
            $this->subtotal = $quote->amount ?? '';
        }
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);
        $this->reset(['invoiceId', 'title', 'description', 'clientId', 'jobId', 'quoteId', 'subtotal', 'notes', 'issueDate', 'dueDate']);
        $this->taxRate = '0';
        $this->status = 'draft';
        $this->issueDate = now()->format('Y-m-d');
        $this->dueDate = now()->addDays(30)->format('Y-m-d');
        $this->isOpen = true;
    }

    public function openEdit(int $invoiceId): void
    {
        abort_unless(auth()->user()->can('invoices.edit'), 403);

        $invoice = Invoice::findOrFail($invoiceId);
        $this->invoiceId = $invoice->id;
        $this->title = $invoice->title;
        $this->description = $invoice->description ?? '';
        $this->clientId = $invoice->client_id;
        $this->jobId = $invoice->job_id;
        $this->quoteId = $invoice->quote_id;
        $this->subtotal = $invoice->subtotal;
        $this->taxRate = $invoice->tax_rate;
        $this->status = $invoice->status;
        $this->issueDate = $invoice->issue_date?->format('Y-m-d') ?? '';
        $this->dueDate = $invoice->due_date?->format('Y-m-d') ?? '';
        $this->notes = $invoice->notes ?? '';
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'client_id' => $this->clientId,
            'job_id' => $this->jobId ?: null,
            'quote_id' => $this->quoteId ?: null,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->taxRate,
            'status' => $this->status,
            'issue_date' => $this->issueDate ?: null,
            'due_date' => $this->dueDate ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->invoiceId) {
            abort_unless(auth()->user()->can('invoices.edit'), 403);
            $invoice = Invoice::findOrFail($this->invoiceId);
            $invoice->update($data);
            $this->toastSuccess("Invoice {$invoice->reference} updated.");
        } else {
            abort_unless(auth()->user()->can('invoices.create'), 403);
            $invoice = Invoice::create($data);
            $this->toastSuccess("Invoice {$invoice->reference} created.");
        }

        $this->isOpen = false;
        $this->dispatch('invoiceSaved');
    }

    public function render(): View
    {
        $clients = Client::active()->orderBy('name')->get();
        $jobs = $this->clientId
            ? Job::where('client_id', $this->clientId)->whereNull('deleted_at')->latest()->get()
            : collect();
        $quotes = $this->clientId
            ? Quote::where('client_id', $this->clientId)->whereNull('deleted_at')->latest()->get()
            : collect();

        return view('livewire.invoices.invoice-form', compact('clients', 'jobs', 'quotes'));
    }
}
