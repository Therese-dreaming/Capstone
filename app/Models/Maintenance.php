<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'lab_number',
        'maintenance_task',
        'technician_id',
        'scheduled_date',
        'status',
        'action_by_id',
        'completed_at',
        'excluded_assets',
        'asset_issues',
        'serial_number'
    ];

    protected $casts = [
        'excluded_assets' => 'array',
        'scheduled_date' => 'datetime',
        'maintenance_task' => 'array',
        'asset_issues' => 'array',
        'completed_at' => 'datetime'
    ];

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function assets()
    {
        return Asset::where('location', 'LIKE', '%' . $this->lab_number)
            ->where('status', '!=', 'DISPOSED')
            ->where(function($query) {
                $query->whereNull('excluded_assets')
                    ->orWhereRaw('NOT JSON_CONTAINS(COALESCE(?, "[]"), CAST(id AS CHAR))', [$this->excluded_assets]);
            });
    }

    public function laboratory()
    {
        return $this->belongsTo(Asset::class, 'lab_number', 'location');
    }
    
    public function maintenanceAssets()
    {
        return $this->hasMany(Asset::class, 'location', 'lab_number')
            ->where('status', '!=', 'DISPOSED')
            ->whereNotIn('id', function($query) {
                $query->select('id')
                    ->from('assets')
                    ->whereIn('id', $this->excluded_assets ?? []);
            });
    }
}