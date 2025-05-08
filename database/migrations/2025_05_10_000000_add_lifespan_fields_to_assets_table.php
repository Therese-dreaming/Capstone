<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('calculated_lifespan', 8, 2)->nullable()->after('warranty_period');
            $table->decimal('remaining_life', 8, 2)->nullable()->after('calculated_lifespan');
            $table->timestamp('end_of_life_date')->nullable()->after('remaining_life');
            $table->string('life_status', 20)->nullable()->after('end_of_life_date');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['calculated_lifespan', 'remaining_life', 'end_of_life_date', 'life_status']);
        });
    }
};