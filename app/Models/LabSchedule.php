<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabSchedule extends Model
{
    protected $fillable = [
        'laboratory',
        'start',
        'end',
        'collaborator_id',
        'department',
        'subject_course',
        'professor',
        'status'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    public function collaborator()
    {
        return $this->belongsTo(User::class, 'collaborator_id');
    }

    public function validate(Request $request)
    {
        session()->flash('error', $request->error);
        return response()->json(['status' => 'success']);
    }
}
