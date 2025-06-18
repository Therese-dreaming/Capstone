<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * Get the assets for this vendor.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the total number of assets from this vendor.
     */
    public function getAssetCountAttribute()
    {
        return $this->assets()->count();
    }

    /**
     * Get the total value of assets from this vendor.
     */
    public function getTotalAssetValueAttribute()
    {
        return $this->assets()->sum('purchase_price');
    }
}
