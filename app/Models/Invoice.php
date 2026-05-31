<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasAuditFields, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'created_by', 'updated_by', 'deleted_by', 'restored_by', 'restored_at',
        'reference', 'client_id', 'job_id', 'quote_id',
        'title', 'description',
        'subtotal', 'tax_rate', 'tax_amount', 'total', 'amount_paid',
        'status', 'issue_date', 'due_date', 'paid_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
            'restored_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->reference)) {
                $invoice->reference = 'INV-'.str_pad(
                    (Invoice::withTrashed()->max('id') ?? 0) + 1,
                    4, '0', STR_PAD_LEFT
                );
            }
        });

        static::saving(function (Invoice $invoice) {
            $invoice->tax_amount = round($invoice->subtotal * ($invoice->tax_rate / 100), 2);
            $invoice->total = round($invoice->subtotal + $invoice->tax_amount, 2);
        });
    }

    // -------------------------------------------------------------------------
    // Activitylog
    // -------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'amount_paid', 'due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Invoice {$eventName}");
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['paid', 'cancelled']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->amount_paid);
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-600',
            'sent' => 'bg-blue-100 text-blue-700',
            'partial' => 'bg-amber-100 text-amber-700',
            'paid' => 'bg-green-100 text-green-700',
            'overdue' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-red-50 text-red-500',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! in_array($this->status, ['paid', 'cancelled'])
            && $this->due_date
            && $this->due_date->isPast();
    }
}
