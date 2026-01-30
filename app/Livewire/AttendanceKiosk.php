<?php

namespace App\Livewire;

use App\Models\NssAttendance;
use App\Models\User;
use App\Settings\AttendanceSettings;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceKiosk extends Component
{
    public string $mode = 'idle'; // idle, scanning, pin_entry, success, error

    public string $pin = '';

    public string $staffId = '';

    public string $errorMessage = '';

    public string $successMessage = '';

    public ?array $checkedInUser = null;

    public string $action = ''; // check_in, check_out

    public function mount(): void
    {
        $this->resetState();
    }

    public function resetState(): void
    {
        $this->mode = 'idle';
        $this->pin = '';
        $this->staffId = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->checkedInUser = null;
        $this->action = '';
    }

    public function startPinEntry(): void
    {
        $this->mode = 'pin_entry';
    }

    public function appendPin(string $digit): void
    {
        if (strlen($this->pin) < 4) {
            $this->pin .= $digit;
        }

        if (strlen($this->pin) === 4) {
            $this->processPinLogin();
        }
    }

    public function clearPin(): void
    {
        $this->pin = '';
    }

    public function processQrCode(string $token): void
    {
        $user = User::where('qr_token', $token)
            ->where('is_nss', true)
            ->where('is_active', true)
            ->first();

        if ($user) {
            $this->processAttendance($user, 'qr_code');
        } else {
            $this->showError('Invalid QR code. Please try again.');
        }
    }

    public function processFaceLogin(int $userId): void
    {
        $user = User::where('id', $userId)
            ->where('is_nss', true)
            ->where('is_active', true)
            ->first();

        if ($user) {
            $this->processAttendance($user, 'face');
        } else {
            $this->showError('User not recognized or inactive.');
        }
    }

    public function processPinLogin(): void
    {
        if (empty($this->staffId) || strlen($this->pin) !== 4) {
            $this->showError('Please enter Staff ID and 4-digit PIN');

            return;
        }

        $user = User::where('staff_id', $this->staffId)
            ->where('is_nss', true)
            ->where('is_active', true)
            ->first();

        if ($user && $user->verifyPin($this->pin)) {
            $this->processAttendance($user, 'pin');
        } else {
            $this->showError('Invalid Staff ID or PIN');
            $this->pin = '';
        }
    }

    protected function processAttendance(User $user, string $method): void
    {
        $today = today();
        $attendance = NssAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            // Check-in
            $this->handleCheckIn($user, $method);
        } elseif (! $attendance->check_out_time) {
            // Check-out
            $this->handleCheckOut($attendance, $method);
        } else {
            // Already checked out
            $this->showError('You have already checked out today.');
        }
    }

    protected function handleCheckIn(User $user, string $method): void
    {
        $now = now();
        $settings = app(AttendanceSettings::class);

        $expectedArrival = Carbon::createFromTimeString($settings->expected_arrival);
        $graceMinutes = $settings->grace_period_minutes;

        $isLate = $now->gt($expectedArrival->addMinutes($graceMinutes));

        NssAttendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'check_in_time' => $now->toTimeString(),
            'status' => $isLate ? 'late' : 'present',
            'check_in_method' => $method,
        ]);

        $this->showSuccess(
            'Welcome, '.$user->name.'!',
            $isLate ? 'Late Arrival - '.$now->format('h:i A') : 'Checked in at '.$now->format('h:i A'),
            $user
        );

        $this->action = 'check_in';
    }

    protected function handleCheckOut(NssAttendance $attendance, string $method): void
    {
        $now = now();

        $attendance->update([
            'check_out_time' => $now->toTimeString(),
            'check_out_method' => $method,
        ]);

        $user = $attendance->user;
        $checkIn = Carbon::createFromTimeString($attendance->check_in_time);
        $hours = round($checkIn->diffInMinutes($now) / 60, 1);

        $this->showSuccess(
            'Goodbye, '.$user->name.'!',
            'Checked out at '.$now->format('h:i A').' ('.$hours.' hrs)',
            $user
        );

        $this->action = 'check_out';
    }

    protected function showSuccess(string $title, string $message, User $user): void
    {
        $this->mode = 'success';
        $this->successMessage = $title;
        $this->errorMessage = $message;
        $this->checkedInUser = [
            'name' => $user->name,
            'photo' => $user->photo_url,
            'staff_id' => $user->staff_id,
            'department' => $user->department?->name ?? 'N/A',
        ];

        $this->dispatch('play-success-sound');
        $this->dispatch('auto-reset', delay: 5000);
    }

    protected function showError(string $message): void
    {
        $this->mode = 'error';
        $this->errorMessage = $message;

        $this->dispatch('play-error-sound');
        $this->dispatch('auto-reset', delay: 3000);
    }

    public function getCurrentTime(): string
    {
        return now()->format('h:i:s A');
    }

    public function getCurrentDate(): string
    {
        return now()->format('l, F j, Y');
    }

    public function render()
    {
        return view('livewire.attendance-kiosk')
            ->layout('components.layouts.kiosk');
    }
}
