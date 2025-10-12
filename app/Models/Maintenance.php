<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'location_id',
        'maintenance_task',
        'technician_id',
        'scheduled_date',
        'target_date',
        'status',
        'approval_status',
        'approved_by_id',
        'approved_at',
        'admin_notes',
        'quality_issues',
        'admin_signature',
        'requires_rework',
        'rework_count',
        'rework_instructions',
        'action_by_id',
        'completed_at',
        'excluded_assets',
        'asset_issues',
        'notes',
        'serial_number'
    ];

    protected $casts = [
        'excluded_assets' => 'array',
        'scheduled_date' => 'datetime',
        'target_date' => 'date',
        'asset_issues' => 'array',
        'quality_issues' => 'array',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assets()
    {
        return $this->hasManyThrough(Asset::class, Location::class, 'id', 'location_id', 'location_id', 'id')
            ->whereNotIn('assets.status', ['DISPOSED', 'LOST', 'PULLED OUT']);
    }
    
    public function maintenanceAssets()
    {
        return $this->hasManyThrough(Asset::class, Location::class, 'id', 'location_id', 'location_id', 'id')
            ->whereNotIn('assets.status', ['DISPOSED', 'LOST', 'PULLED OUT']);
    }
    
    // Keep laboratory() for backward compatibility, but make it point to location
    public function laboratory()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}