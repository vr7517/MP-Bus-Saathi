<?php

namespace App\Livewire\Admin;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\LiveBusTelemetry;
use Livewire\Component;

class LiveTracker extends Component
{
    public function render()
    {
        $telemetries = LiveBusTelemetry::with(['bus.operator', 'activeSchedule.route'])->get();
        $stops = BusStop::all();

        return view('livewire.admin.live-tracker', [
            'telemetries' => $telemetries,
            'stops' => $stops,
        ])->layout('components.layouts.app', ['title' => 'लाइव जीपीएस ट्रैकर - MP Bus Saathi']);
    }
}

