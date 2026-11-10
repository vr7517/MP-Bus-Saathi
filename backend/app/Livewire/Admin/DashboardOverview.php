<?php

namespace App\Livewire\Admin;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\CommunityVerification;
use App\Models\LiveBusTelemetry;
use App\Models\Route;
use Carbon\Carbon;
use Livewire\Component;

class DashboardOverview extends Component
{
    public bool $isOperator = false;

    public function render()
    {
        $user = auth()->user();
        $this->isOperator = ($user && $user->isOperator() && $user->operator_id !== null);

        $busQuery = Bus::query();
        if ($this->isOperator) {
            $busQuery->where('operator_id', $user->operator_id);
        }
        $totalBuses = $busQuery->count();

        $totalStops = BusStop::count();
        $totalRoutes = Route::count();

        $freshThreshold = Carbon::now()->subSeconds(180);
        $telemetryQuery = LiveBusTelemetry::where('last_ping_at', '>=', $freshThreshold);
        if ($this->isOperator) {
            $telemetryQuery->whereHas('bus', fn($b) => $b->where('operator_id', $user->operator_id));
        }
        $liveGpsBuses = $telemetryQuery->count();

        $recentVerifications = CommunityVerification::with(['busStop', 'schedule.route'])
            ->orderByDesc('verified_at')
            ->limit(5)
            ->get();

        $activeBusesQuery = Bus::with(['operator', 'telemetry']);
        if ($this->isOperator) {
            $activeBusesQuery->where('operator_id', $user->operator_id);
        }
        $activeBuses = $activeBusesQuery->get();

        return view('livewire.admin.dashboard-overview', [
            'totalBuses' => $totalBuses,
            'totalStops' => $totalStops,
            'totalRoutes' => $totalRoutes,
            'liveGpsBuses' => $liveGpsBuses,
            'recentVerifications' => $recentVerifications,
            'activeBuses' => $activeBuses,
            'isOperator' => $this->isOperator,
        ])->layout('components.layouts.app', ['title' => 'डैशबोर्ड - MP Bus Saathi']);
    }
}
