<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $table = 'assets'; // Explicitly set the table name

    protected $fillable = [
        'name',
        'category_id',
        'location_id',
        'status',
        'model',
        'specification',
        'vendor_id',
        'purchase_date',
        'warranty_period',
        'lifespan',
        'photo',
        'serial_number',
        'qr_code',
        'purchase_price',
        'disposal_date',
        'disposal_reason',
        'calculated_lifespan',
        'remaining_life',
        'end_of_life_date',
        'life_status',
        'acquisition_document',
    ];

    protected $casts = [
        'end_of_life_date' => 'datetime',
        'disposal_date' => 'datetime',
        'calculated_lifespan' => 'decimal:2',
        'remaining_life' => 'decimal:2'
    ];

    protected $dates = [
        'disposal_date',
    ];

    // Boot method to automatically generate serial number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->serial_number)) {
                $asset->serial_number = self::generateUniqueSerialNumber();
            }
        });
    }

    // Generate unique serial number
    public static function generateUniqueSerialNumber()
    {
        do {
            // Generate a serial number with format: ASST-YYYYMMDD-XXXX
            $date = date('Ymd');
            $random = strtoupper(Str::random(4));
            $serialNumber = "ASST-{$date}-{$random}";
        } while (self::where('serial_number', $serialNumber)->exists());

        return $serialNumber;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class, 'serial_number', 'serial_number');
    }

    // Add this new method
    public function maintenances()
    {
        if (!$this->location_id) {
            return Maintenance::whereRaw('1=0'); // Return empty query if no location
        }
        
        return Maintenance::where('location_id', $this->location_id)
            ->where('status', 'completed')
            ->where(function($query) {
                $query->whereNull('excluded_assets')
                    ->orWhere(function($q) {
                        $q->whereJsonDoesntContain('excluded_assets', $this->id);
                    });
            });
    }

    // Add this accessor
    public function getLabNumberAttribute()
    {
        return $this->location ? $this->location->room_number : null;
    }

    /**
     * Get the non-registered asset that was linked to this asset
     */
    public function nonRegisteredAsset()
    {
        return $this->hasOne(NonRegisteredAsset::class, 'linked_asset_id');
    }
}
