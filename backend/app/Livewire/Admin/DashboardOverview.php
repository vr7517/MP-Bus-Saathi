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
    public function render()
    {
        $totalBuses = Bus::count();
        $totalStops = BusStop::count();
        $totalRoutes = Route::count();

        $freshThreshold = Carbon::now()->subSeconds(180);
        $liveGpsBuses = LiveBusTelemetry::where('last_ping_at', '>=', $freshThreshold)->count();

        $recentVerifications = CommunityVerification::with(['busStop', 'schedule.route'])
            ->orderByDesc('verified_at')
            ->limit(5)
            ->get();

        $activeBuses = Bus::with(['operator', 'telemetry'])->get();

        return view('livewire.admin.dashboard-overview', [
            'totalBuses' => $totalBuses,
            'totalStops' => $totalStops,
            'totalRoutes' => $totalRoutes,
            'liveGpsBuses' => $liveGpsBuses,
            'recentVerifications' => $recentVerifications,
            'activeBuses' => $activeBuses,
        ])->layout('components.layouts.app', ['title' => 'डैशबोर्ड - MP Bus Saathi']);
    }
}

