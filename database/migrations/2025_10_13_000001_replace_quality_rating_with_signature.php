<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Remove quality_rating column only if it exists
            if (Schema::hasColumn('maintenances', 'quality_rating')) {
                $table->dropColumn('quality_rating');
            }
            
            // Add admin_signature column (stores base64 encoded signature image)
            $table->text('admin_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Remove admin_signature column if it exists
            if (Schema::hasColumn('maintenances', 'admin_signature')) {
                $table->dropColumn('admin_signature');
            }
            
            // Restore quality_rating column only if it doesn't exist
            if (!Schema::hasColumn('maintenances', 'quality_rating')) {
                $table->integer('quality_rating')->nullable();
            }
        });
    }
};
