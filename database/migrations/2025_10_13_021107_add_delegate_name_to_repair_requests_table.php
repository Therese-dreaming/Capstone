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
            // Add delegate_name field and remove delegate_id if it exists
            if (Schema::hasColumn('repair_requests', 'delegate_id')) {
                $table->dropForeign(['delegate_id']);
                $table->dropColumn('delegate_id');
            }
            
            if (!Schema::hasColumn('repair_requests', 'delegate_name')) {
                $table->string('delegate_name')->nullable()->after('signature_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropColumn('delegate_name');
        });
    }
};
