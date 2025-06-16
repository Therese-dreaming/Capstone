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
        // First, update any null created_by values to the first admin user
        $adminId = DB::table('users')
            ->where('group_id', 1) // Assuming 1 is admin group
            ->value('id');

        if ($adminId) {
            DB::table('repair_requests')
                ->whereNull('created_by')
                ->update(['created_by' => $adminId]);
        }

        // Then make the column not nullable
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->change();
        });
    }
}; 