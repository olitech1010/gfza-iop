<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class FaceEnrollment extends Component
{
    public User $user;
    public bool $enrolled = false;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->enrolled = !empty($user->face_descriptor);
    }

    public function saveFaceDescriptor($descriptorJson)
    {
        $descriptor = json_decode($descriptorJson, true);
        
        if (is_array($descriptor)) {
            $this->user->update([
                'face_descriptor' => $descriptor,
                'face_enrolled_at' => now(),
            ]);
            
            $this->enrolled = true;
            $this->dispatch('face-enrolled');
            // Notification dispatch handled by view or global notification
        }
    }

    public function render()
    {
        return view('livewire.face-enrollment');
    }
}
