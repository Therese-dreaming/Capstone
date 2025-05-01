<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('laboratory');
            $table->string('subject_course');
            $table->string('professor');
            $table->datetime('start');
            $table->datetime('end');
            $table->foreignId('collaborator_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_schedules');
    }
};