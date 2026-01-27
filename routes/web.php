<?php

use App\Livewire\AttendanceKiosk;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// NSS Attendance Kiosk (public access)
Route::get('/kiosk', AttendanceKiosk::class)->name('kiosk.attendance');
