<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained();
            $table->string('serial_number');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('location');
            $table->string('status', 20);
            $table->string('model');
            $table->text('specification');
            $table->string('vendor');
            $table->date('purchase_date');
            $table->date('warranty_period');
            $table->decimal('calculated_lifespan', 8, 2)->nullable();
            $table->decimal('remaining_life', 8, 2)->nullable();
            $table->timestamp('end_of_life_date')->nullable();
            $table->string('life_status', 20)->nullable();
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamp('disposal_date')->nullable();
            $table->string('disposal_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};