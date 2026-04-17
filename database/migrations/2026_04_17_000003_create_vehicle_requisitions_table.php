<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('destination');
            $table->text('purpose');
            $table->date('requested_date');
            $table->time('requested_time')->nullable();
            $table->date('return_date')->nullable();
            $table->integer('number_of_passengers')->default(1);
            $table->enum('status', [
                'pending',
                'vehicle_assigned',
                'transport_approved',
                'admin_approved',
                'in_progress',
                'completed',
                'rejected',
                'cancelled',
            ])->default('pending');
            $table->text('rejection_reason')->nullable();

            // Assignment fields (Head of Drivers)
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();

            // Approval fields
            $table->foreignId('transport_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('transport_approved_at')->nullable();
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('admin_approved_at')->nullable();

            // Trip log fields (Head of Drivers)
            $table->integer('start_mileage')->nullable();
            $table->integer('end_mileage')->nullable();
            $table->dateTime('departure_time')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->decimal('fuel_used', 8, 2)->nullable();
            $table->text('trip_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_requisitions');
    }
};
