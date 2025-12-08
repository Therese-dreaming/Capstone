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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('manufacturer_serial_number')->nullable()->unique()->after('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the unique index exists and drop it if it does
        // This prevents errors when rolling back if the index doesn't exist
        $indexExists = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'assets' 
            AND index_name = 'assets_manufacturer_serial_number_unique'
        ");
        
        if ($indexExists && $indexExists->count > 0) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropUnique(['manufacturer_serial_number']);
            });
        }
        
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('manufacturer_serial_number');
        });
    }
};
