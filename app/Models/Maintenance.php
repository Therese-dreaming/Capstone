<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'task',
        'technician_id',
        'status',
        'scheduled_date',
        'completion_date',  // Changed from completed_date to completion_date
        'serial_number'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completion_date' => 'datetime'  // Changed from completed_date to completion_date
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}