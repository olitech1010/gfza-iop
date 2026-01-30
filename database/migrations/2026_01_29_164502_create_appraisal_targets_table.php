<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appraisal_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained('appraisals')->onDelete('cascade');
            $table->text('objective');
            $table->text('target_criteria');
            $table->integer('self_score')->nullable();
            $table->integer('manager_score')->default(3); // Default to Meets Expectations
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisal_targets');
    }
};
