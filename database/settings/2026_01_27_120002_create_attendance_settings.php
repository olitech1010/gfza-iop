<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('attendance.expected_arrival', '08:30');
        $this->migrator->add('attendance.expected_departure', '17:00');
        $this->migrator->add('attendance.grace_period_minutes', 0);
    }

    public function down(): void
    {
        $this->migrator->delete('attendance.expected_arrival');
        $this->migrator->delete('attendance.expected_departure');
        $this->migrator->delete('attendance.grace_period_minutes');
    }
};
