<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->date('fuel_date');
            $table->enum('fuel_type', ['petrol', 'diesel'])->default('petrol');
            $table->decimal('litres', 8, 2);
            $table->decimal('cost_per_litre', 8, 2);
            $table->decimal('total_cost', 10, 2);
            $table->integer('mileage_at_fill')->nullable();
            $table->string('station')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
