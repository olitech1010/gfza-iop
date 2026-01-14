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
        // Master list of food items
        Schema::create('meal_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Jollof Rice with Chicken"
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Specific meals scheduled for a date
        Schema::create('served_meals', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('meal_item_id')->constrained();
            $table->unsignedInteger('max_orders')->nullable(); // Optional limit
            $table->timestamps();
            
            // Allow multiple options per date, but maybe uniqueness on combination?
            $table->unique(['date', 'meal_item_id']);
        });

        // User selections
        Schema::create('meal_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('served_meal_id')->constrained()->cascadeOnDelete();
            $table->timestamp('collected_at')->nullable();
            $table->string('status')->default('ordered'); // ordered, cancelled, collected
            $table->timestamps();

            // Prevent ordering multiple meals for the same day?
            // Actually, served_meals has a date. We need to enforce "One meal per user per day" logic.
            // That is best done in application logic or a more complex key.
            // For now, simple index.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_orders');
        Schema::dropIfExists('served_meals');
        Schema::dropIfExists('meal_items');
    }
};
