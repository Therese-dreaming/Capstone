<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Location;

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

    /**
     * Get locations that reference this floor
     */
    public function locations()
    {
        return Location::where('floor', $this->name);
    }

    /**
     * Check if floor has assets through locations
     */
    public function hasAssets()
    {
        return Location::where('floor', $this->name)
            ->withCount('assets')
            ->having('assets_count', '>', 0)
            ->exists();
    }

    /**
     * Check if floor has maintenance records through locations
     */
    public function hasMaintenanceRecords()
    {
        return Location::where('floor', $this->name)
            ->withCount('maintenances')
            ->having('maintenances_count', '>', 0)
            ->exists();
    }
}
