<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competency_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained('appraisals')->onDelete('cascade');
            $table->enum('competency_type', ['core', 'non_core']);
            $table->string('competency_name');
            $table->integer('manager_score')->default(3); // Default to Meets Expectations
            $table->decimal('weight_factor', 3, 2); // 0.30 or 0.10
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competency_scores');
    }
};
