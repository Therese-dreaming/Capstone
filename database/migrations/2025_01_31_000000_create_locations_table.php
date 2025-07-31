<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('building');
            $table->string('floor');
            $table->string('room_number');
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate locations
            $table->unique(['building', 'floor', 'room_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
