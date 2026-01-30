<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
{
    /**
     * Enroll a face descriptor for the authenticated user.
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'descriptor' => 'required', // Can be array or JSON string
        ]);

        $user = auth()->user();
        
        // If descriptor comes as array, it's automatically handled by Laravel/JSON casting
        $descriptor = $request->input('descriptor');
        
        // Ensure it's stored as a simple array of numbers
        if (is_string($descriptor)) {
            $descriptor = json_decode($descriptor, true);
        }

        $user->update([
            'face_descriptor' => $descriptor,
            'face_enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face enrolled successfully',
        ]);
    }

    /**
     * Get all enrolled face descriptors for kiosk matching.
     * Returns minimal data needed for matching (id, name, photo, descriptor).
     */
    public function getDescriptors()
    {
        $users = User::query()
            ->where('is_active', true)
            ->where('is_nss', true) // Only NSS users need to check in at kiosk
            ->whereNotNull('face_descriptor')
            ->get(['id', 'first_name', 'last_name', 'staff_id', 'department_id', 'photo', 'face_descriptor']);

        // Transform to lightweight format
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $user->name, // Full name for display
                'descriptor' => $user->face_descriptor,
                'photo' => $user->photo_url, // Accessor or path
                'staff_id' => $user->staff_id,
            ];
        });

        return response()->json($data);
    }
}
