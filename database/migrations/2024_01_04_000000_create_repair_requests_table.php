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
            $table->date('date_called');
            $table->time('time_called');
            $table->string('department');
            $table->string('office_room');
            $table->foreignId('category_id')->constrained();
            $table->string('equipment');  // Add this line
            $table->text('issue');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repair_requests');
    }
};