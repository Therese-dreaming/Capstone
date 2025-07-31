<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;
use App\Models\Location;

return new class extends Migration
{
    public function up()
    {
        // First, let's populate the locations table with existing location data
        $this->migrateExistingLocations();
        
        // Then update the assets table
        Schema::table('assets', function (Blueprint $table) {
            // Add the new location_id column
            $table->foreignId('location_id')->nullable()->after('purchase_price')->constrained();
        });
        
        // Update existing assets with the new location_id
        $this->updateAssetLocations();
        
        // Make location_id non-nullable after data migration
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
            // Drop the old location column
            $table->dropColumn('location');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Re-add the location column
            $table->string('location')->after('purchase_price');
        });
        
        // Restore location data from locations table
        $assets = Asset::with('location')->get();
        foreach ($assets as $asset) {
            if ($asset->location) {
                $asset->location = $asset->location->full_location;
                $asset->save();
            }
        }
        
        Schema::table('assets', function (Blueprint $table) {
            // Drop the foreign key and location_id column
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
    
    private function migrateExistingLocations()
    {
        $assets = Asset::select('location')->distinct()->whereNotNull('location')->get();
        
        foreach ($assets as $asset) {
            $locationString = $asset->location;
            
            // Try to parse existing location format
            // Assuming format like "Building A - Floor 1 - Room 101" or similar
            $parts = explode(' - ', $locationString);
            
            if (count($parts) >= 3) {
                $building = trim($parts[0]);
                $floor = trim(str_replace('Floor ', '', $parts[1]));
                $room = trim(str_replace('Room ', '', $parts[2]));
            } else {
                // Fallback: use the entire string as room number
                $building = 'Unknown';
                $floor = '1';
                $room = $locationString;
            }
            
            // Create or find the location
            Location::firstOrCreate([
                'building' => $building,
                'floor' => $floor,
                'room_number' => $room,
            ]);
        }
    }
    
    private function updateAssetLocations()
    {
        $assets = Asset::whereNotNull('location')->get();
        
        foreach ($assets as $asset) {
            $locationString = $asset->location;
            
            // Parse location string same as above
            $parts = explode(' - ', $locationString);
            
            if (count($parts) >= 3) {
                $building = trim($parts[0]);
                $floor = trim(str_replace('Floor ', '', $parts[1]));
                $room = trim(str_replace('Room ', '', $parts[2]));
            } else {
                $building = 'Unknown';
                $floor = '1';
                $room = $locationString;
            }
            
            // Find the location and update the asset
            $location = Location::where([
                'building' => $building,
                'floor' => $floor,
                'room_number' => $room,
            ])->first();
            
            if ($location) {
                $asset->location_id = $location->id;
                $asset->save();
            }
        }
    }
};
