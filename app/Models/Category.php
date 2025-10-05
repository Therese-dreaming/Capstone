<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }
}