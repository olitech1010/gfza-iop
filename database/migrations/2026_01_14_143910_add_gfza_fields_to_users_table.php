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
            $table->string('staff_id')->unique()->nullable()->after('id');
            $table->string('first_name')->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->after('middle_name');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('email');
            $table->string('job_title')->nullable()->after('department_id');
            $table->boolean('is_active')->default(true)->after('password');
            
            // Cleanup default name field if we are splitting it, OR keep it as full name.
            // Let's keep 'name' as display name but populate it automatically or just ignore it.
            // Actually, best practice is to drop 'name' if we have first/last, BUT Laravel Auth uses 'name'.
            // I'll make 'name' nullable or just keep it sync via model observer later.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['staff_id', 'first_name', 'middle_name', 'last_name', 'department_id', 'job_title', 'is_active']);
        });
    }
};
