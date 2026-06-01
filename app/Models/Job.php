<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Job extends Model implements HasMedia
{
    use HasAuditFields, HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    protected $table = 'work_jobs';

    protected $fillable = [
        'created_by', 'updated_by', 'deleted_by', 'restored_by', 'restored_at',
        'reference', 'client_id', 'service_type_id',
        'title', 'description', 'location',
        'status', 'priority',
        'scheduled_date', 'scheduled_time', 'completed_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_date' => 'date',
            'restored_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot — auto-generate reference
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Job $job) {
            if (empty($job->reference)) {
                $job->reference = 'JOB-'.str_pad(
                    (Job::withTrashed()->max('id') ?? 0) + 1,
                    4, '0', STR_PAD_LEFT
                );
            }
        });
    }

    // -------------------------------------------------------------------------
    // Activitylog
    // -------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'priority', 'scheduled_date', 'client_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Job {$eventName}");
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class)->orderBy('priority', 'desc')->orderBy('created_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_assignments')
            ->withPivot(['role', 'assigned_date', 'notes'])
            ->withTimestamps();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(JobReport::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'documentable_id')
            ->where('documentable_type', self::class);
    }

    // -------------------------------------------------------------------------
    // Media Library — Wasabi
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->useDisk('wasabi')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('photos');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getPriorityColourAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-gray-100 text-gray-600',
            'normal' => 'bg-blue-100 text-blue-700',
            'high' => 'bg-orange-100 text-orange-700',
            'urgent' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
