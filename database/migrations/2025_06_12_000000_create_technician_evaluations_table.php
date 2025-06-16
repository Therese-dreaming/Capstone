<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('technician_evaluations')) {
            Schema::create('technician_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('repair_request_id')->constrained()->onDelete('cascade');
                $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating')->comment('1-5 rating');
                $table->text('feedback')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->timestamps();
                
                // Ensure one evaluation per repair request
                $table->unique('repair_request_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('technician_evaluations')) {
            Schema::dropIfExists('technician_evaluations');
        }
    }
}; 