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

    /**
     * Get locations that reference this building
     */
    public function locations()
    {
        return Location::where('building', $this->name);
    }

    /**
     * Check if building has assets through locations
     */
    public function hasAssets()
    {
        return Location::where('building', $this->name)
            ->withCount('assets')
            ->having('assets_count', '>', 0)
            ->exists();
    }

    /**
     * Check if building has maintenance records through locations
     */
    public function hasMaintenanceRecords()
    {
        return Location::where('building', $this->name)
            ->withCount('maintenances')
            ->having('maintenances_count', '>', 0)
            ->exists();
    }
}

