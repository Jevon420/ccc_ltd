<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * HasAuditFields
 *
 * Automatically populates created_by, updated_by, deleted_by, restored_by, restored_at
 * on any model that uses this trait.
 *
 * Usage: Add `use HasAuditFields;` to any Eloquent model.
 * The model's table must have these columns (add via migration with addAuditFields helper).
 */
trait HasAuditFields
{
    public static function bootHasAuditFields(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && $model->isFillable('created_by') && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
            if (Auth::check() && $model->isFillable('updated_by') && empty($model->updated_by)) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleted(function ($model) {
            // Only set deleted_by for soft deletes
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                if (Auth::check() && $model->isFillable('deleted_by')) {
                    $model->deleted_by = Auth::id();
                    $model->saveQuietly();
                }
            }
        });

        // Handle restore (if model uses SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if (Auth::check()) {
                    $model->restored_by = Auth::id();
                    $model->restored_at = now();
                    $model->deleted_by  = null;
                    $model->saveQuietly();
                }
            });
        }
    }

    /**
     * Helper: add audit columns to a Blueprint (use in migrations).
     * Example: HasAuditFields::addAuditColumns($table);
     */
    public static function addAuditColumns(\Illuminate\Database\Schema\Blueprint $table): void
    {
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
        $table->unsignedBigInteger('restored_by')->nullable();
        $table->timestamp('restored_at')->nullable();
    }

    // Relationships

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by')->withTrashed();
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by')->withTrashed();
    }

    public function deleter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by')->withTrashed();
    }

    public function restorer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'restored_by')->withTrashed();
    }
}
