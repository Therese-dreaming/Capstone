<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('laboratory');
            $table->timestamp('time_in');
            $table->timestamp('time_out')->nullable();
            $table->enum('status', ['on-going', 'completed'])
                  ->default('on-going');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_logs');
    }
};