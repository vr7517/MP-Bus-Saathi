<?php

namespace App\Livewire\Admin;

use App\Models\CommunityVerification;
use Livewire\Component;

class VerificationModerator extends Component
{
    public function approve(int $id)
    {
        $verification = CommunityVerification::findOrFail($id);
        $verification->is_approved = true;
        $verification->save();
        session()->flash('message', 'सत्यापन स्वीकृत किया गया! (Verification approved!)');
    }

    public function dismiss(int $id)
    {
        $verification = CommunityVerification::findOrFail($id);
        $verification->delete();
        session()->flash('message', 'रिपोर्ट खारिज कर दी गई। (Report dismissed.)');
    }

    public function render()
    {
        $verifications = CommunityVerification::with(['busStop', 'schedule.route'])->orderByDesc('verified_at')->get();

        return view('livewire.admin.verification-moderator', [
            'verifications' => $verifications,
        ])->layout('components.layouts.app', ['title' => 'कम्युनिटी फीडबैक मॉडरेशन - MP Bus Saathi']);
    }
}

