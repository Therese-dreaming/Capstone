<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'building',
        'floor',
        'room_number',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // Helper method to get full location display name
    public function getFullLocationAttribute()
    {
        return "{$this->building} - {$this->floor} - {$this->room_number}";
    }

    // Helper method to get lab number (for backward compatibility)
    public function getLabNumberAttribute()
    {
        return $this->room_number;
    }
}
