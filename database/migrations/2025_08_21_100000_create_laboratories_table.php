<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('laboratories', function (Blueprint $table) {
			$table->id();
			$table->string('number')->unique();
			$table->string('name')->nullable();
			$table->string('building')->nullable();
			$table->string('floor')->nullable();
			$table->string('room_number')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('laboratories');
	}
}; 