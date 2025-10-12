<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_requests', 'rework_count')) {
                $table->unsignedInteger('rework_count')->default(0)->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            if (Schema::hasColumn('repair_requests', 'rework_count')) {
                $table->dropColumn('rework_count');
            }
        });
    }
};
