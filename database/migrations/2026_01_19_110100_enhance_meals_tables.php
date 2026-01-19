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
        // Add is_nss field to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_nss')->default(false)->after('is_active');
        });

        // Caterers table
        Schema::create('caterers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "AL GRAY AL HANNAH"
            $table->string('contact')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Weekly menus table
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caterer_id')->constrained()->cascadeOnDelete();
            $table->date('week_start'); // Monday of the week
            $table->date('week_end');   // Friday of the week
            $table->string('week_label')->nullable(); // "1st - 4th December 2025"
            $table->string('status')->default('draft'); // draft, published, closed
            $table->timestamps();

            $table->unique(['week_start']);
        });

        // Daily menu options - links meal items to specific days of a weekly menu
        Schema::create('weekly_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_item_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->timestamps();

            $table->unique(['weekly_menu_id', 'meal_item_id', 'day_of_week']);
        });

        // Meal requests - staff selections with payment and serving tracking
        Schema::create('meal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('weekly_menu_item_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_nss')->default(false); // Copied from user at time of request
            $table->decimal('amount_due', 8, 2)->default(5.00);
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->boolean('is_served')->default(false);
            $table->timestamp('served_at')->nullable();
            $table->foreignId('served_by')->nullable()->constrained('users');
            $table->timestamps();

            // One meal per user per menu item (prevents duplicate selection)
            $table->unique(['user_id', 'weekly_menu_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_requests');
        Schema::dropIfExists('weekly_menu_items');
        Schema::dropIfExists('weekly_menus');
        Schema::dropIfExists('caterers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_nss');
        });
    }
};
