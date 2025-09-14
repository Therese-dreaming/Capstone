<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the floors for the building.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    /**
     * Get active floors for the building.
     */
    public function activeFloors(): HasMany
    {
        return $this->hasMany(Floor::class)->where('is_active', true);
    }
}

