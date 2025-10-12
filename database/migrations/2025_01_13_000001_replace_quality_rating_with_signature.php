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
            // Remove quality_rating column
            $table->dropColumn('quality_rating');
            
            // Add admin_signature column (stores base64 encoded signature image)
            $table->text('admin_signature')->nullable()->after('quality_issues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Remove admin_signature column
            $table->dropColumn('admin_signature');
            
            // Restore quality_rating column
            $table->integer('quality_rating')->nullable()->after('quality_issues');
        });
    }
};
