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
        $user = auth()->user();
        $query = LiveBusTelemetry::with(['bus.operator', 'activeSchedule.route']);

        if ($user && $user->isOperator() && $user->operator_id) {
            $query->whereHas('bus', fn($b) => $b->where('operator_id', $user->operator_id));
        }

        $telemetries = $query->get();
        $stops = BusStop::all();

        return view('livewire.admin.live-tracker', [
            'telemetries' => $telemetries,
            'stops' => $stops,
        ])->layout('components.layouts.app', ['title' => 'लाइव जीपीएस ट्रैकर - MP Bus Saathi']);
    }
}
