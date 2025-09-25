<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to avoid requiring doctrine/dbal for column modifications
        DB::statement('ALTER TABLE lab_logs MODIFY COLUMN time_in DATETIME NOT NULL');
        DB::statement('ALTER TABLE lab_logs MODIFY COLUMN time_out DATETIME NULL');
    }

    public function down(): void
    {
        // Revert back to TIMESTAMP types (without ON UPDATE). Adjust as needed for your MySQL defaults
        DB::statement("ALTER TABLE lab_logs MODIFY COLUMN time_in TIMESTAMP NOT NULL");
        DB::statement("ALTER TABLE lab_logs MODIFY COLUMN time_out TIMESTAMP NULL");
    }
};


