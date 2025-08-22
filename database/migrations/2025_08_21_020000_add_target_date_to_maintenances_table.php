<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('maintenances', function (Blueprint $table) {
			$table->date('target_date')->nullable()->after('scheduled_date');
			$table->index('target_date');
		});
	}

	public function down(): void
	{
		Schema::table('maintenances', function (Blueprint $table) {
			$table->dropIndex(['target_date']);
			$table->dropColumn('target_date');
		});
	}
}; 