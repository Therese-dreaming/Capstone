<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('non_registered_assets', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_name');
            $table->string('location');
            $table->string('category')->nullable();
            $table->text('findings');
            $table->text('remarks');
            $table->string('ticket_number');
            $table->string('pulled_out_by');
            $table->timestamp('pulled_out_at');
            $table->string('status')->default('PULLED OUT'); // PULLED OUT, DISPOSED, RETURNED
            $table->text('disposal_details')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->string('disposed_by')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->string('returned_by')->nullable();
            $table->text('return_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('non_registered_assets');
    }
}; 