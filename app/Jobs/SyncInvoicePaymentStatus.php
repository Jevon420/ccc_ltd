<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncInvoicePaymentStatus implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public readonly int $invoiceId)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $invoice = Invoice::find($this->invoiceId);

        if (! $invoice) {
            return;
        }

        $paid = Payment::where('invoice_id', $this->invoiceId)
            ->where('status', 'confirmed')
            ->sum('amount');

        $status = $invoice->status;

        if ($paid >= $invoice->total && $invoice->total > 0) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        } elseif (in_array($status, ['paid', 'partial'])) {
            // Payments were refunded — revert to sent
            $status = 'sent';
        }

        $invoice->updateQuietly([
            'amount_paid' => $paid,
            'status' => $status,
            'paid_date' => $paid >= $invoice->total && $invoice->total > 0 ? now() : $invoice->paid_date,
        ]);
    }
}
