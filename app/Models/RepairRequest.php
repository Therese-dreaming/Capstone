<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_called',
        'time_called',
        'department',
        'office_room',
        'category_id',
        'equipment',
        'issue',
        'status',
        'technician_id',
        'completed_at'
    ];

    protected $dates = [
        'completed_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}