<?php

use App\Http\Controllers\FaceEnrollmentController;
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

// Face Recognition API Routes (Web middleware for session auth / Public for kiosk)
Route::prefix('api/face')->group(function () {
    // Kiosk needs public access to descriptors for client-side matching
    Route::get('/descriptors', [FaceEnrollmentController::class, 'getDescriptors'])->name('api.face.descriptors');
    
    // Enrollment requires authentication (done via admin/user session)
    Route::post('/enroll', [FaceEnrollmentController::class, 'enroll'])
        ->middleware('auth')
        ->name('api.face.enroll');
});

// Health check endpoint for monitoring
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
})->name('health');

// Appraisal PDF Report
use App\Http\Controllers\AppraisalPdfController;

Route::get('/appraisal/{appraisal}/pdf', AppraisalPdfController::class)
    ->name('appraisal.pdf')
    ->middleware(['auth']);


