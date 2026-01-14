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
        // Asset Categories / Ticket Categories could be separate or shared.
        // Let's keep it simple with explicit columns or a simple categories table if needed.
        
        // IT Assets
        Schema::create('mis_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); // Barcode/Inventory Number
            $table->string('serial_number')->nullable();
            $table->string('name'); // e.g. "Dell Latitude 5420"
            $table->string('type'); // Laptop, Monitor, Printer
            $table->string('status')->default('active'); // active, repair, retired, lost
            $table->date('purchase_date')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Support Tickets
        Schema::create('mis_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('category'); // hardware, software, network, other
            $table->foreignId('user_id')->constrained(); // Requester
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users'); // MIS Staff
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mis_tickets');
        Schema::dropIfExists('mis_assets');
    }
};
