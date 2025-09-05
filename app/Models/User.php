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
        'status',
        'gender'
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

    /**
     * Get the appropriate default profile picture based on gender
     */
    public function getDefaultProfilePicture()
    {
        if ($this->gender === 'male') {
            return asset('images/default-profile-male.png');
        } elseif ($this->gender === 'female') {
            return asset('images/default-profile.png');
        }
        
        // Default fallback for null gender
        return asset('images/default-profile.png');
    }

    /**
     * Get the profile picture URL, using default if none is set
     */
    public function getProfilePictureUrl()
    {
        return $this->profile_picture ? asset($this->profile_picture) : $this->getDefaultProfilePicture();
    }
}
