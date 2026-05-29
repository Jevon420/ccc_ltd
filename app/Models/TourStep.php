<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourStep extends Model
{
    protected $fillable = [
        'tour_id', 'title', 'description', 'element',
        'placement', 'sort_order', 'is_required',
        'action_label', 'action_route',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
