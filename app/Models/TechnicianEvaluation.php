<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianEvaluation extends Model
{
    protected $fillable = [
        'repair_request_id',
        'technician_id',
        'evaluator_id',
        'rating',
        'feedback',
        'is_anonymous'
    ];

    public function repairRequest()
    {
        return $this->belongsTo(RepairRequest::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
} 