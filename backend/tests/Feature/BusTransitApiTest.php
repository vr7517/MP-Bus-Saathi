<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Route;
use App\Models\Schedule;
use Database\Seeders\MPTransitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusTransitApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MPTransitSeeder::class);
    }

    public function test_can_search_bus_stops_in_hindi_and_english(): void
    {
        // Search in English
        $responseEn = $this->getJson('/api/v1/stops?query=Nadra');
        $responseEn->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.name_en', 'Nadra Bus Stand');

        // Search in Hindi
        $responseHi = $this->getJson('/api/v1/stops?query=नादरा');
        $responseHi->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.name_hi', 'नादरा बस स्टैंड');
    }

    public function test_can_search_source_to_destination_direct_route(): void
    {
        $bhopalStop = BusStop::where('name_en', 'Nadra Bus Stand')->first();
        $indoreStop = BusStop::where('name_en', 'Sarwate Bus Stand, Indore')->first();

        $response = $this->getJson("/api/v1/routes/search?source_stop_id={$bhopalStop->id}&destination_stop_id={$indoreStop->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data.direct_routes')
            ->assertJsonPath('data.direct_routes.0.route_code', 'BPL-IND-EXP-01');
    }

    public function test_live_bus_status_reports_correct_gps_health(): void
    {
        // Bus 1 has fresh ping (45s ago) -> Should be "live"
        $bus1 = Bus::where('registration_number', 'MP-04-EA-1024')->first();
        $response1 = $this->getJson("/api/v1/buses/{$bus1->id}/live-status");

        $response1->assertStatus(200)
            ->assertJsonPath('data.gps_status', 'live')
            ->assertJsonPath('data.gps_status_label_hi', 'लाइव जीपीएस ट्रैकिंग चालू');

        // Bus 3 has no GPS device -> Should be "unavailable"
        $bus3 = Bus::where('registration_number', 'MP-04-HB-4422')->first();
        $response3 = $this->getJson("/api/v1/buses/{$bus3->id}/live-status");

        $response3->assertStatus(200)
            ->assertJsonPath('data.gps_status', 'unavailable')
            ->assertJsonPath('data.gps_status_label_hi', 'जीपीएस अनुपलब्ध - निर्धारित समय-सारणी');
    }

    public function test_can_ingest_telemetry_ping(): void
    {
        $bus = Bus::first();

        $response = $this->postJson('/api/v1/telemetry/ping', [
            'bus_id' => $bus->id,
            'latitude' => 23.2100,
            'longitude' => 77.0900,
            'speed_kmh' => 64.0,
            'heading' => 240.0,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('live_bus_telemetry', [
            'bus_id' => $bus->id,
            'gps_status' => 'live',
        ]);
    }

    public function test_can_submit_community_verification(): void
    {
        $schedule = Schedule::first();
        $stop = BusStop::first();

        $response = $this->postJson('/api/v1/community/verify', [
            'schedule_id' => $schedule->id,
            'bus_stop_id' => $stop->id,
            'device_hash' => 'test-device-uuid-12345',
            'report_type' => 'on_time',
            'notes' => 'Bus arrived exactly on scheduled time',
            'user_latitude' => $stop->latitude,
            'user_longitude' => $stop->longitude,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.verified_on_site', true);
    }

    public function test_offline_sync_snapshot_returns_required_transit_data(): void
    {
        $response = $this->getJson('/api/v1/sync/snapshot');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'synced_at',
                'data' => [
                    'stops',
                    'routes',
                    'route_stops',
                    'schedules',
                    'buses',
                ],
            ]);
    }
}

