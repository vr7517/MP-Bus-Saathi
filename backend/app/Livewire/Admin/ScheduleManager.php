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
        $schedules = Schedule::with(['route.sourceStop', 'route.destinationStop', 'bus.operator'])->orderBy('departure_time')->get();

        return view('livewire.admin.schedule-manager', [
            'schedules' => $schedules,
        ])->layout('components.layouts.app', ['title' => 'समय-सारणी प्रबंधन - MP Bus Saathi']);
    }
}

