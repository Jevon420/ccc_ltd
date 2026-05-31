<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Equipment extends Model
{
    use HasAuditFields, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'created_by', 'updated_by', 'deleted_by', 'restored_by', 'restored_at',
        'name', 'type', 'serial_number', 'make_model',
        'purchase_date', 'condition', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'restored_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'condition'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Equipment {$eventName}");
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-700',
            'maintenance' => 'bg-amber-100 text-amber-700',
            'retired' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getConditionColourAttribute(): string
    {
        return match ($this->condition) {
            'excellent' => 'bg-green-100 text-green-700',
            'good' => 'bg-blue-100 text-blue-700',
            'fair' => 'bg-amber-100 text-amber-700',
            'poor' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
