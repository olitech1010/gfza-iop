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
        Schema::create('driver_trip_reviews', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('vehicle_requisition_id')->nullable()->constrained('vehicle_requisitions')->nullOnDelete();
            $table->foreignId('audit_trip_id')->nullable()->constrained('audit_trips')->nullOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();

            // Type & context
            $table->enum('review_type', ['admin', 'passenger']);
            $table->date('review_date');
            $table->enum('transmission_used', ['manual', 'automatic']);

            // Admin ratings (1–5, nullable for passenger reviews)
            $table->tinyInteger('vehicle_condition')->nullable();
            $table->tinyInteger('cleanliness')->nullable();
            $table->tinyInteger('fuel_efficiency')->nullable();
            $table->tinyInteger('timeliness')->nullable();
            $table->tinyInteger('rule_compliance')->nullable();

            // Passenger ratings (1–5, nullable for admin reviews)
            $table->tinyInteger('punctuality')->nullable();
            $table->tinyInteger('driving_quality')->nullable();
            $table->tinyInteger('professionalism')->nullable();
            $table->tinyInteger('safety_feeling')->nullable();
            $table->tinyInteger('overall_satisfaction')->nullable();

            // Computed
            $table->decimal('overall_rating', 3, 2)->nullable();

            // Admin qualitative
            $table->enum('damage_severity', ['none', 'minor', 'moderate', 'severe'])->nullable();
            $table->text('damage_notes')->nullable();
            $table->text('incidents')->nullable();
            $table->text('mechanical_issues')->nullable();
            $table->enum('recommendation', ['recommended', 'needs_training', 'restricted', 'not_recommended'])->nullable();

            // Passenger qualitative
            $table->text('compliments')->nullable();
            $table->text('complaints')->nullable();

            // Shared
            $table->text('comments')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['driver_id', 'review_type']);
            $table->index('vehicle_requisition_id');
            $table->index('review_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_trip_reviews');
    }
};
