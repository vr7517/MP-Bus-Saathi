<?php

namespace App\Livewire\Admin;

use App\Models\BusStop;
use App\Models\Route;
use Livewire\Component;

class RouteManager extends Component
{
    public $selectedRouteId = null;

    public function selectRoute(int $routeId)
    {
        $this->selectedRouteId = $routeId;
    }

    public function render()
    {
        $routes = Route::with(['sourceStop', 'destinationStop', 'stops.busStop'])->get();
        $selectedRoute = $this->selectedRouteId ? Route::with(['stops.busStop', 'sourceStop', 'destinationStop'])->find($this->selectedRouteId) : $routes->first();

        return view('livewire.admin.route-manager', [
            'routes' => $routes,
            'selectedRoute' => $selectedRoute,
        ])->layout('components.layouts.app', ['title' => 'रूट्स व स्टॉप्स प्रबंधन - MP Bus Saathi']);
    }
}

