<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonRegisteredAsset extends Model
{
    protected $fillable = [
        'equipment_name',
        'location',
        'category',
        'findings',
        'remarks',
        'ticket_number',
        'pulled_out_by',
        'pulled_out_at',
        'status',
        'disposal_details',
        'disposed_at',
        'disposed_by',
        'returned_at',
        'returned_by',
        'return_remarks'
    ];

    protected $casts = [
        'pulled_out_at' => 'datetime',
        'disposed_at' => 'datetime',
        'returned_at' => 'datetime'
    ];

    public function repairRequest()
    {
        return $this->belongsTo(RepairRequest::class, 'ticket_number', 'ticket_number');
    }
} 