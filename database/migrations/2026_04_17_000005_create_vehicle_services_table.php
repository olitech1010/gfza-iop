<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('service_type', [
                'routine',
                'repair',
                'inspection',
                'tire_change',
                'oil_change',
                'brake_service',
                'battery',
                'accident_repair',
                'body_work',
                'electrical',
                'other',
            ]);
            $table->date('service_date');
            $table->integer('mileage_at_service')->nullable();
            $table->text('description');
            $table->string('service_provider'); // Mechanic/garage name
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('parts_replaced')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->string('invoice_number')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_services');
    }
};
