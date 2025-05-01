<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;

class Asset extends Model
{
    protected $table = 'assets'; // Explicitly set the table name

    protected $fillable = [
        'name',
        'category_id',
        'location',
        'status',
        'model',
        'specification',
        'vendor',
        'purchase_date',
        'warranty_period',
        'lifespan',
        'photo',
        'serial_number',
        'qr_code',
        'purchase_price', // Add this if not already present
        'disposal_date',
        'disposal_reason',
    ];

    protected $dates = [
        'disposal_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }

    // Add this new method
    public function maintenances()
    {
        $labNumber = substr($this->location, -3); // Extract lab number from location
        return Maintenance::where('lab_number', $labNumber)
            ->where('status', 'completed')
            ->where(function($query) {
                $query->whereNull('excluded_assets')
                    ->orWhereRaw('NOT JSON_CONTAINS(COALESCE(excluded_assets, "[]"), ?)', [(string) $this->id]);
            });
    }

    // Add this accessor
    public function getLabNumberAttribute()
    {
        return substr($this->location, -3);
    }
}
