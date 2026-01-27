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
        Schema::create('nss_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['present', 'late', 'absent'])->default('present');
            $table->enum('check_in_method', ['qr_code', 'pin', 'manual'])->nullable();
            $table->enum('check_out_method', ['qr_code', 'pin', 'manual'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one attendance record per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nss_attendances');
    }
};
