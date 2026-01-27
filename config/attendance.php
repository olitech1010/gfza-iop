<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Configuration
    |--------------------------------------------------------------------------
    |
    | These are default values. The actual values can be configured by
    | HR/Admin through the AttendanceSettings page in the admin panel.
    | Settings are stored in the database using Spatie Laravel Settings.
    |
    */

    'defaults' => [
        'expected_arrival' => '08:30',
        'expected_departure' => '17:00',
        'grace_period_minutes' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Kiosk Settings
    |--------------------------------------------------------------------------
    */

    'kiosk' => [
        // Auto-reset timeout in milliseconds after successful action
        'reset_timeout' => 5000,

        // Enable sound feedback
        'sound_enabled' => true,
    ],
];
