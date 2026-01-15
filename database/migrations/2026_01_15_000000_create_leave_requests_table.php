<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->text('reason')->nullable();
            
            // Workflow Status
            $table->string('status')->default('pending_dept_head'); 
            // pending_dept_head, pending_hr, approved, rejected
            
            // Approvals
            $table->timestamp('dept_head_approved_at')->nullable();
            $table->foreignId('dept_head_id')->nullable()->constrained('users');
            
            $table->timestamp('hr_approved_at')->nullable();
            $table->foreignId('hr_id')->nullable()->constrained('users');
            
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
