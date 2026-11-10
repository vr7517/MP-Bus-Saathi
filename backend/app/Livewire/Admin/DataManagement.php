<?php

namespace App\Livewire\Admin;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\District;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DataManagement extends Component
{
    public string $activeTab = 'completeness'; // 'completeness' or 'importer'
    public string $importType = 'stops'; // 'stops', 'routes', 'schedules'
    public string $sourceName = 'MP Transport Portal (LGD / Gazette)';
    public string $sourceUrl = 'https://transport.mp.gov.in/';
    public string $rawJsonData = '';
    public string $importMessage = '';
    public string $importError = '';
    public array $previewData = [];

    public function parsePreview()
    {
        $this->importMessage = '';
        $this->importError = '';
        $this->previewData = [];

        if (empty(trim($this->rawJsonData))) {
            $this->importError = 'कृपया JSON डेटा दर्ज करें। (Please enter valid JSON text)';
            return;
        }

        try {
            $decoded = json_decode($this->rawJsonData, true);
            if (!is_array($decoded)) {
                $this->importError = 'अमान्य JSON प्रारूप। (Invalid JSON array)';
                return;
            }
            $this->previewData = array_slice($decoded, 0, 10);
            $this->importMessage = 'डेटा पूर्वावलोकन तैयार है: कुल ' . count($decoded) . ' रिकॉर्ड्स पहचाने गए।';
        } catch (\Exception $e) {
            $this->importError = 'त्रुटि: ' . $e->getMessage();
        }
    }

    public function executeImport()
    {
        $this->importError = '';
        $this->importMessage = '';

        if (empty(trim($this->rawJsonData))) {
            $this->importError = 'कोई डेटा नहीं मिला।';
            return;
        }

        $records = json_decode($this->rawJsonData, true);
        if (!is_array($records) || empty($records)) {
            $this->importError = 'अमान्य डेटा।';
            return;
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $skippedCount = 0;

            if ($this->importType === 'stops') {
                foreach ($records as $item) {
                    if (empty($item['name_en']) || empty($item['district']) || !isset($item['latitude'], $item['longitude'])) {
                        $skippedCount++;
                        continue;
                    }

                    BusStop::updateOrCreate(
                        ['name_en' => trim($item['name_en'])],
                        [
                            'name_hi' => $item['name_hi'] ?? $item['name_en'],
                            'district' => trim($item['district']),
                            'latitude' => (float)$item['latitude'],
                            'longitude' => (float)$item['longitude'],
                            'is_major_junction' => $item['is_major_junction'] ?? false,
                        ]
                    );
                    $importedCount++;
                }
            } elseif ($this->importType === 'routes') {
                foreach ($records as $item) {
                    if (empty($item['route_code']) || empty($item['source_stop_id']) || empty($item['destination_stop_id'])) {
                        $skippedCount++;
                        continue;
                    }

                    Route::updateOrCreate(
                        ['route_code' => trim($item['route_code'])],
                        [
                            'source_stop_id' => (int)$item['source_stop_id'],
                            'destination_stop_id' => (int)$item['destination_stop_id'],
                            'total_distance_km' => (float)($item['total_distance_km'] ?? 50.0),
                            'estimated_duration_minutes' => (int)($item['estimated_duration_minutes'] ?? 60),
                            'is_active' => true,
                        ]
                    );
                    $importedCount++;
                }
            }

            DB::commit();
            $this->importMessage = "आयात सफल! {$importedCount} रिकॉर्ड्स सफलतापूर्वक सहेजे गए ({$skippedCount} छोड़े गए)।";
            $this->rawJsonData = '';
            $this->previewData = [];
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importError = 'आयात विफल: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $districts = District::withCount('busStops')->orderBy('name_en')->get();
        $coveredDistrictsCount = $districts->filter(fn($d) => $d->bus_stops_count > 0)->count();
        $totalDistrictsCount = $districts->count();
        $coveragePercentage = $totalDistrictsCount > 0 ? round(($coveredDistrictsCount / $totalDistrictsCount) * 100, 1) : 0;

        $stats = [
            'total_districts' => $totalDistrictsCount,
            'covered_districts' => $coveredDistrictsCount,
            'coverage_percentage' => $coveragePercentage,
            'total_stops' => BusStop::count(),
            'total_routes' => Route::count(),
            'total_buses' => Bus::count(),
            'total_operators' => Operator::count(),
            'total_schedules' => Schedule::count(),
        ];

        return view('livewire.admin.data-management', [
            'stats' => $stats,
            'districts' => $districts,
        ])->layout('components.layouts.app', ['title' => 'डेटा प्रबंधन एवं कवरेज - MP Bus Saathi']);
    }
}
