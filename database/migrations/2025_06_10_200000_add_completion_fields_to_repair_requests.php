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
        Schema::table('repair_requests', function (Blueprint $table) {
            // Add new fields for completion form
            $table->string('caller_name')->nullable()->after('issue');
            $table->text('findings')->nullable()->after('caller_name');
            $table->text('technician_signature')->nullable()->after('findings');
            $table->text('caller_signature')->nullable()->after('technician_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            // Remove the added fields
            $table->dropColumn([
                'caller_name',
                'findings',
                'technician_signature',
                'caller_signature'
            ]);
        });
    }
}; 