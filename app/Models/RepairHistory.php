<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_request_id',
        'technician_id',
        'attempt_number',
        'findings',
        'remarks',
        'before_photos',
        'after_photos',
        'technician_signature',
        'caller_signature',
        'time_started',
        'completed_at',
        'caller_signed_at',
        'verification_status',
        'caller_feedback',
    ];

    protected $casts = [
        'before_photos' => 'array',
        'after_photos' => 'array',
        'time_started' => 'datetime',
        'completed_at' => 'datetime',
        'caller_signed_at' => 'datetime',
    ];

    public function repairRequest()
    {
        return $this->belongsTo(RepairRequest::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
