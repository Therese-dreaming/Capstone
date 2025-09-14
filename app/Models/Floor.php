<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Floor extends Model
{
    protected $fillable = [
        'building_id',
        'name',
        'floor_number',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'floor_number' => 'integer',
    ];

    /**
     * Get the building that owns the floor.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
