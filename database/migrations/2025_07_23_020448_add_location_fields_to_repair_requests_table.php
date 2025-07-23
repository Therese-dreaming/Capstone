<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('building')->after('time_called');
            $table->string('floor')->after('building');
            $table->string('room')->after('floor');
            $table->dropColumn('location');
        });
    }

    public function down()
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('location')->after('time_called');
            $table->dropColumn(['building', 'floor', 'room']);
        });
    }
};