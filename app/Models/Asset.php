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
        'serial_number',
        'specification',
        'vendor',
        'purchase_date',
        'warranty_period',
        'lifespan',
        'photo',
        'qr_code'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
