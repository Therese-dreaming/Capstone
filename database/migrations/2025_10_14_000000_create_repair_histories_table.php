<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repair_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained('repair_requests')->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
            $table->integer('attempt_number')->default(1); // 1st attempt, 2nd attempt, etc.
            
            // Snapshot of completion data
            $table->text('findings')->nullable();
            $table->text('remarks')->nullable();
            $table->json('before_photos')->nullable();
            $table->json('after_photos')->nullable();
            $table->text('technician_signature')->nullable();
            $table->text('caller_signature')->nullable();
            $table->timestamp('time_started')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('caller_signed_at')->nullable();
            
            // Verification outcome
            $table->enum('verification_status', ['approved', 'disputed'])->nullable();
            $table->text('caller_feedback')->nullable(); // Why rework was requested
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['repair_request_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_histories');
    }
};
