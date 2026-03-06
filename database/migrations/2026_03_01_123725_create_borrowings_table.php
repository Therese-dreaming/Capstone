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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('purpose');
            $table->string('department')->nullable();
            $table->dateTime('borrow_date');
            $table->date('expected_return_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'active', 'returned', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
