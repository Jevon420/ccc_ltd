<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        HasRoles,
        HasAuditFields,
        Notifiable,
        SoftDeletes,
        LogsActivity,
        InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'avatar',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'restored_by',
        'restored_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'initials',
        'avatar_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'restored_at'       => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Spatie Activitylog
    // -------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'position', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "User {$eventName}");
    }

    // -------------------------------------------------------------------------
    // Spatie Media Library
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk(config('filesystems.default'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=1d4ed8&color=fff&size=128';
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($part) => strtoupper($part[0] ?? ''))
            ->take(2)
            ->implode('');
    }

    // -------------------------------------------------------------------------
    // Relationships (to be expanded in Phase 2)
    // -------------------------------------------------------------------------

    public function tourProgress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserTourProgress::class);
    }
}
