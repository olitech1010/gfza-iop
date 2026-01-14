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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->string('attachment_path')->nullable(); // For PDF uploads
            $table->string('status')->default('draft'); // draft, published, sent
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('memo_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate sending to same user
            $table->unique(['memo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_recipients');
        Schema::dropIfExists('memos');
    }
};
