<?php

use App\Livewire\AttendanceKiosk;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Industry-standard route structure:
| - / redirects to admin login (main entry point)
| - /admin/* handles all admin panel routes (via Filament)
| - /kiosk is public for NSS attendance terminal
|
*/

// Homepage redirects to admin panel
Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('home');

// NSS Attendance Kiosk - Public access (no auth required)
Route::get('/kiosk', AttendanceKiosk::class)->name('kiosk.attendance');

// Health check endpoint for monitoring
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
})->name('health');
