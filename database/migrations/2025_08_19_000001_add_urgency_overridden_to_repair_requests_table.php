<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->boolean('urgency_overridden')->default(false)->after('urgency_level');
            $table->index('urgency_overridden');
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropIndex(['urgency_overridden']);
            $table->dropColumn('urgency_overridden');
        });
    }
}; 