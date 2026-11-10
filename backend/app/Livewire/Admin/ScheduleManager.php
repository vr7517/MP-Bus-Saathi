<?php

namespace App\Livewire\Admin;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use Livewire\Component;

class ScheduleManager extends Component
{
    public function render()
    {
        $user = auth()->user();
        $query = Schedule::with(['route.sourceStop', 'route.destinationStop', 'bus.operator'])->orderBy('departure_time');

        if ($user && $user->isOperator() && $user->operator_id) {
            $query->whereHas('bus', fn($b) => $b->where('operator_id', $user->operator_id));
        }

        return view('livewire.admin.schedule-manager', [
            'schedules' => $query->get(),
        ])->layout('components.layouts.app', ['title' => 'समय-सारणी प्रबंधन - MP Bus Saathi']);
    }
}
