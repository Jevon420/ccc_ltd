<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobReport extends Model implements HasMedia
{
    use HasAuditFields, HasFactory, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'created_by', 'updated_by',
        'job_id', 'title', 'description',
        'work_performed', 'issues_encountered', 'recommendations', 'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Job Report {$eventName}");
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('wasabi');
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-600',
            'submitted' => 'bg-blue-100 text-blue-700',
            'approved' => 'bg-green-100 text-green-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
