<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets'; // Explicitly set the table name

    protected $fillable = [
        'name',
        'category_id',
        'location',
        'status',
        'model',
        'specification',
        'vendor',
        'purchase_date',
        'warranty_period',
        'lifespan',
        'photo',
        'serial_number',
        'qr_code',
        'purchase_price', // Add this if not already present
        'disposal_date',
        'disposal_reason',
    ];

    protected $dates = [
        'disposal_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function repairs()
    {
        return $this->hasMany(RepairRequest::class);
    }
}
