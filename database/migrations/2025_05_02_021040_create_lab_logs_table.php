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
            $table->string('laboratory');
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out');
            $table->string('professor_name');
            $table->string('subject_course');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_logs');
    }
};