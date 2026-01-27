<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NssIdCardService
{
    /**
     * Generate an ID card PDF for the given user.
     */
    public function generateIdCard(User $user): string
    {
        // Ensure user has a QR token
        if (! $user->qr_token) {
            $user->generateQrToken();
            $user->refresh();
        }

        // Generate QR code as base64 SVG
        $qrCode = base64_encode(
            QrCode::format('svg')
                ->size(200)
                ->errorCorrection('H')
                ->generate($user->qr_token)
        );

        // Get logo as base64 (if exists)
        $logoPath = public_path('images/gfza-logo.png');
        $logo = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        // Get user photo as base64 (if exists)
        $photo = null;
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            $photoContent = Storage::disk('public')->get($user->photo);
            $extension = pathinfo($user->photo, PATHINFO_EXTENSION);
            $photo = 'data:image/'.$extension.';base64,'.base64_encode($photoContent);
        }

        $pdf = Pdf::loadView('pdf.nss-id-card', [
            'user' => $user,
            'qrCode' => $qrCode,
            'logo' => $logo,
            'photo' => $photo,
        ]);

        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // Credit card size in points

        return $pdf->output();
    }

    /**
     * Save the ID card to storage and return the path.
     */
    public function saveIdCard(User $user): string
    {
        $pdfContent = $this->generateIdCard($user);

        // Sanitize staff_id for filename (replace slashes with hyphens)
        $safeStaffId = str_replace('/', '-', $user->staff_id);
        $filename = 'id-cards/nss-'.$safeStaffId.'-'.time().'.pdf';

        Storage::disk('public')->put($filename, $pdfContent);

        return $filename;
    }

    /**
     * Verify a QR token and return the user if valid.
     */
    public function verifyQrToken(string $token): ?User
    {
        return User::where('qr_token', $token)
            ->where('is_nss', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Verify a PIN and return the user if valid.
     */
    public function verifyPin(string $staffId, string $pin): ?User
    {
        $user = User::where('staff_id', $staffId)
            ->where('is_nss', true)
            ->where('is_active', true)
            ->first();

        if ($user && $user->verifyPin($pin)) {
            return $user;
        }

        return null;
    }
}
