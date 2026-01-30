<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competency_scores', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('manager_score');
        });
    }

    public function down(): void
    {
        Schema::table('competency_scores', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
