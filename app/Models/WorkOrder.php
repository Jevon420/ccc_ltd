<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkOrder extends Model
{
    use HasAuditFields, HasFactory, LogsActivity;

    protected $fillable = [
        'created_by', 'updated_by',
        'job_id', 'title', 'description',
        'status', 'priority', 'due_date',
        'completed_at', 'completion_notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'priority'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Work Order {$eventName}");
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-700',
            'in_progress' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getPriorityColourAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-gray-100 text-gray-500',
            'normal' => 'bg-blue-50 text-blue-600',
            'high' => 'bg-orange-100 text-orange-700',
            'urgent' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
