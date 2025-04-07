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
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('location');
            $table->enum('status', ['IN USE', 'UNDER REPAIR', 'DISPOSED', 'UPGRADE', 'PENDING DEPLOYMENT']);
            $table->string('model');
            $table->text('specification');
            $table->string('vendor');
            $table->date('purchase_date');
            $table->date('warranty_period');
            $table->integer('lifespan');
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};