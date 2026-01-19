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
        Schema::table('weekly_menus', function (Blueprint $table) {
            $table->json('available_days')->nullable()->after('week_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_menus', function (Blueprint $table) {
            $table->dropColumn('available_days');
        });
    }
};
