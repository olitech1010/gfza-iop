<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_trips', function (Blueprint $table) {
            $table->id();
            $table->string('team_name'); // TEAM 1, TEAM 2, etc.
            $table->enum('audit_type', ['compliance', 'monitoring']);
            $table->enum('schedule_type', ['internal', 'external']);
            $table->string('region')->nullable(); // For external: VOLTA 2, ASHANTI 1, etc.
            $table->integer('sequence_number')->nullable(); // The No. column
            $table->string('company_name');
            $table->string('scheduled_date'); // Raw date text from CSV
            $table->date('start_date')->nullable(); // Parsed start date
            $table->date('end_date')->nullable(); // Parsed end date
            $table->text('team_members'); // Comma-separated
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trips');
    }
};
