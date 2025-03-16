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
        'password',
        'group_id',
        'status',
        'last_login'
    ];

    protected $casts = [
        'last_login' => 'datetime'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
