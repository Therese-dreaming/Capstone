<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('lab_number');
            $table->string('task');
            $table->string('status')->default('PENDING');
            $table->foreignId('technician_id')->constrained('users');
            $table->date('scheduled_date');
            $table->string('serial_number')->nullable();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('action_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
};