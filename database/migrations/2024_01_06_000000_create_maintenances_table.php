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
            $table->string('maintenance_task');
            $table->string('status')->default('PENDING');
            $table->foreignId('technician_id')->constrained('users');
            $table->date('scheduled_date');
            $table->string('serial_number')->nullable();
            $table->foreignId('action_by_id')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->longText('excluded_assets')->nullable();
            $table->json('asset_issues')->nullable(); // Added for storing asset issues
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
};