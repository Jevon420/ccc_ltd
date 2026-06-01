<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use HasAuditFields, HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'restored_by',
        'restored_at',
        'type',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'restored_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Client {$eventName}");
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCompanies(Builder $query): Builder
    {
        return $query->where('type', 'company');
    }

    public function scopeIndividuals(Builder $query): Builder
    {
        return $query->where('type', 'individual');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getDisplayNameAttribute(): string
    {
        return $this->type === 'company' && $this->contact_person
            ? "{$this->name} ({$this->contact_person})"
            : $this->name;
    }
}
