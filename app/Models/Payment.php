<?php

namespace App\Models;

use App\Jobs\SyncInvoicePaymentStatus;
use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasAuditFields, HasFactory, LogsActivity;

    protected $fillable = [
        'created_by', 'updated_by', 'deleted_by', 'restored_by', 'restored_at',
        'reference', 'invoice_id', 'client_id',
        'amount', 'method', 'status',
        'paid_at', 'transaction_reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot — auto-reference + update invoice amount_paid
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'PAY-'.str_pad(
                    (Payment::max('id') ?? 0) + 1,
                    4, '0', STR_PAD_LEFT
                );
            }
        });

        static::saved(function (Payment $payment) {
            SyncInvoicePaymentStatus::dispatch($payment->invoice_id);
        });

        static::deleted(function (Payment $payment) {
            SyncInvoicePaymentStatus::dispatch($payment->invoice_id);
        });
    }

    // -------------------------------------------------------------------------
    // Activitylog
    // -------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'method', 'status', 'paid_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Payment {$eventName}");
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'bank_transfer' => 'Bank Transfer',
            'wipay' => 'WiPay',
            default => ucfirst($this->method),
        };
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-green-100 text-green-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'failed' => 'bg-red-100 text-red-700',
            'refunded' => 'bg-gray-100 text-gray-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
