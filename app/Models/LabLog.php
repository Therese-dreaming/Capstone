<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabLog extends Model
{
    protected $fillable = [
        'user_id',
        'laboratory',
        'purpose',
        'time_in',
        'time_out',
        'status'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}