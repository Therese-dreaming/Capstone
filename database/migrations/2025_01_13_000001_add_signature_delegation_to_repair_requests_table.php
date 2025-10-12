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
        Schema::table('repair_requests', function (Blueprint $table) {
            // Signature delegation fields
            $table->enum('signature_type', ['caller', 'delegate', 'deferred', 'none'])->default('none')->after('caller_signature');
            $table->string('delegate_name')->nullable()->after('signature_type'); // Store delegate name as text
            $table->timestamp('signature_deadline')->nullable()->after('delegate_name');
            $table->enum('verification_status', ['pending', 'verified', 'disputed'])->default('pending')->after('signature_deadline');
            
            // Photo evidence fields
            $table->json('before_photos')->nullable()->after('verification_status');
            $table->json('after_photos')->nullable()->after('before_photos');
            
            // Caller signature timestamp
            $table->timestamp('caller_signed_at')->nullable()->after('caller_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropColumn([
                'signature_type',
                'delegate_name',
                'signature_deadline',
                'verification_status',
                'before_photos',
                'after_photos',
                'caller_signed_at'
            ]);
        });
    }
};
