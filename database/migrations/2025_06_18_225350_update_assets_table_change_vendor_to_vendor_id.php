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
        Schema::table('assets', function (Blueprint $table) {
            // Drop the old vendor column
            $table->dropColumn('vendor');
            
            // Add the new vendor_id column
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['vendor_id']);
            
            // Drop the vendor_id column
            $table->dropColumn('vendor_id');
            
            // Add back the old vendor column
            $table->string('vendor')->nullable();
        });
    }
};
