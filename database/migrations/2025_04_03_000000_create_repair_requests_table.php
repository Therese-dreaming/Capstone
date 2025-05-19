<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->string('serial_number')->nullable();
            $table->date('date_called');
            $table->time('time_called');
            $table->foreignId('category_id')->constrained();
            $table->string('equipment');
            $table->text('issue');
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repair_requests');
    }
};