<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Maintenance;
use App\Models\Location;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the location_id column
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('lab_number')->constrained();
        });
        
        // Update existing maintenance records to use location_id
        $this->migrateExistingMaintenances();
        
        // Make location_id non-nullable and drop lab_number
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
            $table->dropColumn('lab_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add lab_number column
        Schema::table('maintenances', function (Blueprint $table) {
            $table->string('lab_number')->after('id');
        });
        
        // Restore lab_number data from location relationship
        $maintenances = Maintenance::with('location')->get();
        foreach ($maintenances as $maintenance) {
            if ($maintenance->location) {
                $maintenance->lab_number = $maintenance->location->room_number;
                $maintenance->save();
            }
        }
        
        // Drop location_id column
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
    
    private function migrateExistingMaintenances()
    {
        $maintenances = Maintenance::all();
        
        foreach ($maintenances as $maintenance) {
            // Find location by room_number matching lab_number
            $location = Location::where('room_number', $maintenance->lab_number)->first();
            
            if ($location) {
                $maintenance->location_id = $location->id;
                $maintenance->save();
            } else {
                // Create a new location if it doesn't exist
                $location = Location::create([
                    'building' => 'Unknown',
                    'floor' => '1',
                    'room_number' => $maintenance->lab_number,
                ]);
                
                $maintenance->location_id = $location->id;
                $maintenance->save();
            }
        }
    }
};
