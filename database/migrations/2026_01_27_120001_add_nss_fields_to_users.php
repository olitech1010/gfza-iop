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
            $table->string('photo')->nullable()->after('email');
            $table->string('nss_pin')->nullable()->after('photo');
            $table->string('qr_token')->nullable()->unique()->after('nss_pin');
            $table->boolean('is_nss')->default(false)->after('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'nss_pin', 'qr_token', 'is_nss']);
        });
    }
};
