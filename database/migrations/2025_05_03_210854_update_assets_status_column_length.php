<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('status', 20)->change();  // Increase length to accommodate longer status values
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('status', 15)->change();  // Revert to original length if needed
        });
    }
};