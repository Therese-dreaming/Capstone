<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowableAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'category_id',
        'location_id',
        'model',
        'specification',
        'purchase_price',
        'purchase_date',
        'photo',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the asset
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the location that owns the asset
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who created the asset
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all borrowing items for this asset
     */
    public function borrowingItems()
    {
        return $this->hasMany(BorrowingItem::class, 'borrowable_asset_id');
    }

    /**
     * Check if asset is currently borrowed
     */
    public function isBorrowed()
    {
        return $this->status === 'in_use';
    }

    /**
     * Check if asset is available for borrowing
     */
    public function isAvailable()
    {
        return $this->status === 'active';
    }

    /**
     * Check if asset is missing
     */
    public function isMissing()
    {
        return $this->status === 'missing';
    }

    /**
     * Scope to get only available assets
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only borrowed assets
     */
    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    /**
     * Scope to get assets by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to search assets
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%");
        });
    }
}
