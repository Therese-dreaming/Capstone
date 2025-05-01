<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        // Continue with the repair_requests changes
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->dropColumn(['department', 'office_room']);
        });
    }

    public function down()
    {
        // Revert repair_requests changes
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('office_room')->nullable();
            $table->dropColumn('location');
        });
    }
};