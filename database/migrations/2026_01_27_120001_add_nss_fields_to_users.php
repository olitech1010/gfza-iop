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
        Schema::table('users', function (Blueprint $table) {
            // Note: is_nss already added in 2026_01_19_110100_enhance_meals_tables migration
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'nss_pin')) {
                $table->string('nss_pin')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('users', 'qr_token')) {
                $table->string('qr_token')->nullable()->unique()->after('nss_pin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Note: is_nss belongs to 2026_01_19_110100_enhance_meals_tables migration
            $table->dropColumn(['photo', 'nss_pin', 'qr_token']);
        });
    }
};
