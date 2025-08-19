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
            $table->unsignedBigInteger('linked_asset_id')->nullable()->after('return_remarks');
            $table->timestamp('linked_at')->nullable()->after('linked_asset_id');
            
            // Add foreign key constraint
            $table->foreign('linked_asset_id')->references('id')->on('assets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_registered_assets', function (Blueprint $table) {
            $table->dropForeign(['linked_asset_id']);
            $table->dropColumn(['linked_asset_id', 'linked_at']);
        });
    }
};
