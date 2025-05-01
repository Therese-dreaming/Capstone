<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabLog extends Model
{
    protected $fillable = [
        'laboratory',
        'date',
        'time_in',
        'time_out',
        'professor_name',
        'subject_course',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}