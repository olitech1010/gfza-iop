<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('make'); // Toyota, Nissan, etc.
            $table->string('model'); // Land Cruiser, Patrol, etc.
            $table->integer('year')->nullable();
            $table->enum('type', ['sedan', 'suv', 'pickup', 'bus', 'van', 'motorcycle', 'other'])->default('sedan');
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid'])->default('petrol');
            $table->string('color')->nullable();
            $table->integer('current_mileage')->default(0);
            $table->enum('status', ['available', 'in_use', 'maintenance', 'decommissioned'])->default('available');
            $table->date('insurance_expiry')->nullable();
            $table->date('roadworthy_expiry')->nullable();
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
