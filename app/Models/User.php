<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'rfid_number',
        'password',
        'department',
        'position',
        'group_id',
        'status'
    ];

    protected $casts = [
        'last_login' => 'datetime'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Add this relationship
    public function labLogs()
    {
        return $this->hasMany(LabLog::class);
    }
}
