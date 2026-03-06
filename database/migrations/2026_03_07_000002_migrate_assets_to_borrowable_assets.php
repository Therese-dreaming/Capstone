<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the new column to borrowing_items
        Schema::table('borrowing_items', function (Blueprint $table) {
            $table->foreignId('borrowable_asset_id')->nullable()->after('asset_id')->constrained('borrowable_assets')->onDelete('cascade');
        });

        // Copy assets that are used in borrowing system to borrowable_assets
        $borrowingAssets = DB::table('borrowing_items')
            ->select('asset_id')
            ->distinct()
            ->get();

        foreach ($borrowingAssets as $borrowingAsset) {
            $asset = DB::table('assets')->where('id', $borrowingAsset->asset_id)->first();
            
            if ($asset) {
                // Get location_id from location string (if location table exists)
                $locationId = null;
                if (!empty($asset->location)) {
                    // Try to find matching location
                    $location = DB::table('locations')
                        ->where(DB::raw("CONCAT(building, ' - ', floor, ' - ', room_number)"), $asset->location)
                        ->first();
                    
                    if ($location) {
                        $locationId = $location->id;
                    }
                }

                // Insert into borrowable_assets
                $borrowableAssetId = DB::table('borrowable_assets')->insertGetId([
                    'name' => $asset->name,
                    'serial_number' => $asset->serial_number,
                    'category_id' => $asset->category_id,
                    'location_id' => $locationId,
                    'model' => $asset->model ?? null,
                    'specification' => $asset->specification ?? null,
                    'purchase_price' => $asset->purchase_price ?? null,
                    'purchase_date' => $asset->purchase_date ?? null,
                    'photo' => $asset->photo ?? null,
                    'status' => $asset->status ?? 'active',
                    'notes' => null,
                    'created_by' => $asset->created_by ?? null,
                    'created_at' => $asset->created_at ?? now(),
                    'updated_at' => $asset->updated_at ?? now(),
                ]);

                // Update borrowing_items to reference the new borrowable_asset_id
                DB::table('borrowing_items')
                    ->where('asset_id', $asset->id)
                    ->update(['borrowable_asset_id' => $borrowableAssetId]);
            }
        }

        // Now remove the old asset_id foreign key and column
        Schema::table('borrowing_items', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn('asset_id');
        });

        // Make borrowable_asset_id NOT NULL after data is migrated
        Schema::table('borrowing_items', function (Blueprint $table) {
            $table->foreignId('borrowable_asset_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back asset_id column
        Schema::table('borrowing_items', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->after('borrowing_id')->constrained('assets')->onDelete('cascade');
        });

        // Note: Reversing this migration will lose the separation
        // You would need manual intervention to map back

        // Remove borrowable_asset_id
        Schema::table('borrowing_items', function (Blueprint $table) {
            $table->dropForeign(['borrowable_asset_id']);
            $table->dropColumn('borrowable_asset_id');
        });
    }
};
