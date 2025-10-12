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
        Schema::table('maintenances', function (Blueprint $table) {
            // Add approval workflow fields
            $table->string('approval_status')->default('not_required')->after('status');
            // Possible values: 'not_required', 'pending_approval', 'approved', 'rejected', 'needs_rework'
            
            $table->unsignedBigInteger('approved_by_id')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
            
            // Quality control fields
            $table->text('admin_notes')->nullable()->after('approved_at');
            $table->json('quality_issues')->nullable()->after('admin_notes');
            // Will store: ['incomplete_tasks' => [], 'poor_quality' => [], 'missing_documentation' => []]
            
            $table->integer('quality_rating')->nullable()->after('quality_issues');
            // 1-5 scale: 1=Poor, 2=Below Average, 3=Satisfactory, 4=Good, 5=Excellent
            
            $table->boolean('requires_rework')->default(false)->after('quality_rating');
            $table->text('rework_instructions')->nullable()->after('requires_rework');
            
            // Add foreign key constraint
            $table->foreign('approved_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn([
                'approval_status',
                'approved_by_id', 
                'approved_at',
                'admin_notes',
                'quality_issues',
                'quality_rating',
                'requires_rework',
                'rework_instructions'
            ]);
        });
    }
};
