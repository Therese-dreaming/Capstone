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
        'excluded_assets'
    ];

    protected $casts = [
        'excluded_assets' => 'array',
        'scheduled_date' => 'datetime',
        'maintenance_task' => 'array'
    ];

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // Add this new method
    public function assets()
    {
        return Asset::where('location', 'LIKE', '%' . $this->lab_number)
            ->where('status', '!=', 'DISPOSED')
            ->where(function($query) {
                $query->whereNull('excluded_assets')
                    ->orWhereRaw('NOT JSON_CONTAINS(COALESCE(?, "[]"), CAST(id AS CHAR))', [$this->excluded_assets]);
            });
    }
}