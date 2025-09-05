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
        'linked_asset_id',
        'linked_at'
    ];

    protected $casts = [
        'pulled_out_at' => 'datetime',
        'linked_at' => 'datetime'
    ];

    public function repairRequest()
    {
        return $this->belongsTo(RepairRequest::class, 'ticket_number', 'ticket_number');
    }

    public function linkedAsset()
    {
        return $this->belongsTo(Asset::class, 'linked_asset_id');
    }
} 