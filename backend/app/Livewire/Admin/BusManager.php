<?php

namespace App\Livewire\Admin;

use App\Models\Bus;
use App\Models\Operator;
use Livewire\Component;

class BusManager extends Component
{
    public $registration_number = '';
    public $operator_id = '';
    public $bus_type = 'ordinary';
    public $seating_capacity = 40;
    public $has_gps_device = false;
    public $is_active = true;

    protected function rules(): array
    {
        return [
            'registration_number' => 'required|string|unique:buses,registration_number',
            'operator_id' => 'required|exists:operators,id',
            'bus_type' => 'required|in:ordinary,express,ac,sleeper',
            'seating_capacity' => 'required|integer|min:10|max:100',
            'has_gps_device' => 'boolean',
        ];
    }

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->isOperator() && $user->operator_id) {
            $this->operator_id = $user->operator_id;
        }
    }

    public function createBus()
    {
        $user = auth()->user();
        if ($user && $user->isOperator() && $user->operator_id) {
            $this->operator_id = $user->operator_id;
        }

        $this->validate();

        Bus::create([
            'registration_number' => strtoupper(trim($this->registration_number)),
            'operator_id' => $this->operator_id,
            'bus_type' => $this->bus_type,
            'seating_capacity' => $this->seating_capacity,
            'has_gps_device' => $this->has_gps_device,
            'is_active' => $this->is_active,
        ]);

        $this->reset(['registration_number', 'bus_type', 'seating_capacity', 'has_gps_device']);
        if ($user && $user->isOperator()) {
            $this->operator_id = $user->operator_id;
        }
        session()->flash('message', 'नई बस सफलतापूर्वक पंजीकृत की गई! (Bus registered successfully!)');
    }

    public function toggleGps(int $busId)
    {
        $user = auth()->user();
        $bus = Bus::findOrFail($busId);

        // Security check: Operators can only toggle their own buses
        if ($user && $user->isOperator() && $bus->operator_id !== $user->operator_id) {
            abort(403, 'Unauthorized action.');
        }

        $bus->has_gps_device = !$bus->has_gps_device;
        $bus->save();
    }

    public function render()
    {
        $user = auth()->user();
        $query = Bus::with(['operator', 'telemetry'])->orderBy('id');

        if ($user && $user->isOperator() && $user->operator_id) {
            $query->where('operator_id', $user->operator_id);
            $operators = Operator::where('id', $user->operator_id)->get();
        } else {
            $operators = Operator::where('is_verified', true)->get();
        }

        return view('livewire.admin.bus-manager', [
            'buses' => $query->get(),
            'operators' => $operators,
        ])->layout('components.layouts.app', ['title' => 'बस फ्लीट प्रबंधन - MP Bus Saathi']);
    }
}
