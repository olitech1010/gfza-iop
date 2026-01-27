<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AttendanceSettings extends Settings
{
    public string $expected_arrival;

    public string $expected_departure;

    public int $grace_period_minutes;

    public static function group(): string
    {
        return 'attendance';
    }
}
