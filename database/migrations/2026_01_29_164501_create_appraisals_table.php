<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('appraiser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('hod_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appraisal_period_id')->constrained('appraisal_periods')->onDelete('cascade');
            
            $table->string('current_grade')->nullable();
            $table->string('job_title')->nullable();
            $table->date('date_appointed_present_grade')->nullable();
            
            $table->enum('status', ['goal_setting', 'hod_review', 'hr_review', 'completed'])->default('goal_setting');
            $table->decimal('final_score', 5, 2)->default(0);
            
            $table->enum('promotion_verdict', ['outstanding', 'suitable', 'ready_2_3_years', 'not_ready', 'unlikely'])->nullable();
            
            $table->text('appraisee_comment')->nullable();
            $table->text('appraiser_comment')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};
