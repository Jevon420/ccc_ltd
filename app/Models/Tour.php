<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $fillable = [
        'name', 'title', 'description', 'route_name',
        'role_scope', 'is_active', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'role_scope'  => 'array',
        'is_active'   => 'boolean',
        'is_required' => 'boolean',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(TourStep::class)->orderBy('sort_order');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserTourProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this tour applies to a given role.
     */
    public function appliesToRole(string $role): bool
    {
        if (empty($this->role_scope)) {
            return true; // applies to all roles
        }

        return in_array($role, $this->role_scope);
    }
}
