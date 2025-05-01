<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'date_called',
        'time_called',
        'location',
        'category_id',
        'equipment',
        'serial_number',
        'issue',
        'status',
        'technician_id',
        'remarks',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Add this relationship
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'serial_number', 'serial_number');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}