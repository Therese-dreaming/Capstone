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
        Schema::table('non_registered_assets', function (Blueprint $table) {
            // Remove return-related columns
            $table->dropColumn(['returned_at', 'returned_by', 'return_remarks']);
            
            // Remove disposal-related columns
            $table->dropColumn(['disposal_details', 'disposed_at', 'disposed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_registered_assets', function (Blueprint $table) {
            // Add back return-related columns
            $table->timestamp('returned_at')->nullable();
            $table->string('returned_by')->nullable();
            $table->text('return_remarks')->nullable();
            
            // Add back disposal-related columns
            $table->text('disposal_details')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->string('disposed_by')->nullable();
        });
    }
};
