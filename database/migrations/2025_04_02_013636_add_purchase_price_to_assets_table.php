<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable()->after('serial_number');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
