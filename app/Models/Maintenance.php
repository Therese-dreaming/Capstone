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
        'completed_at'
    ];

    protected $casts = [
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
}