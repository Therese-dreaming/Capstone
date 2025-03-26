<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropForeign(['technician_id']);
            $table->dropColumn(['technician_id', 'completed_at']);
        });
    }
};