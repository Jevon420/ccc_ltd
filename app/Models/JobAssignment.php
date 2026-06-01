<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'user_id', 'role', 'assigned_date', 'notes',
    ];

    protected function casts(): array
    {
        return ['assigned_date' => 'date'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'lead' => 'Lead',
            'supervisor' => 'Supervisor',
            'driver' => 'Driver',
            default => 'Assistant',
        };
    }

    public function getRoleColourAttribute(): string
    {
        return match ($this->role) {
            'lead' => 'bg-purple-100 text-purple-700',
            'supervisor' => 'bg-blue-100 text-blue-700',
            'driver' => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}
