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
        Schema::create('store_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_item_id')->constrained()->cascadeOnDelete();
            
            $table->string('type'); // receipt, issue, adjustment
            $table->date('transaction_date');
            
            $table->integer('quantity'); // Positive for receipt, negative for issue
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->integer('balance_after'); // Snapshot
            
            // Receipt Details
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->nullable();
            $table->string('sra_number')->nullable();
            
            // Issue Details
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('requisition_number')->nullable();
            $table->string('siv_number')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_transactions');
    }
};
