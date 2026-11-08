<?php

namespace Tests\Feature;

use App\Livewire\Admin\BusManager;
use App\Livewire\Admin\DashboardOverview;
use App\Livewire\Admin\LiveTracker;
use App\Livewire\Admin\RouteManager;
use App\Livewire\Admin\ScheduleManager;
use App\Livewire\Admin\VerificationModerator;
use App\Models\Bus;
use App\Models\BusStop;
use App\Models\CommunityVerification;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Schedule;
use Database\Seeders\MPTransitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MPTransitSeeder::class);
    }

    public function test_can_access_admin_dashboard_overview(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('मध्य प्रदेश बस साथी');
        $response->assertSee('कुल बसें');
    }

    public function test_dashboard_overview_livewire_component_renders_stats(): void
    {
        Livewire::test(DashboardOverview::class)
            ->assertStatus(200)
            ->assertSee('पंजीकृत बस फ्लीट')
            ->assertSee('लाइव जीपीएस');
    }

    public function test_bus_manager_can_create_new_bus_and_toggle_gps(): void
    {
        $operator = Operator::first();

        // 1. Create a new bus via Livewire
        Livewire::test(BusManager::class)
            ->set('registration_number', 'MP-04-ZZ-9999')
            ->set('operator_id', $operator->id)
            ->set('bus_type', 'ac')
            ->set('seating_capacity', 45)
            ->set('has_gps_device', true)
            ->call('createBus')
            ->assertHasNoErrors()
            ->assertSee('MP-04-ZZ-9999');

        $this->assertDatabaseHas('buses', [
            'registration_number' => 'MP-04-ZZ-9999',
            'bus_type' => 'ac',
            'has_gps_device' => true,
        ]);

        // 2. Toggle GPS capability
        $bus = Bus::where('registration_number', 'MP-04-ZZ-9999')->first();
        Livewire::test(BusManager::class)
            ->call('toggleGps', $bus->id);

        $this->assertFalse($bus->fresh()->has_gps_device);
    }

    public function test_route_manager_can_select_route_and_view_stops(): void
    {
        $route = Route::first();

        Livewire::test(RouteManager::class)
            ->call('selectRoute', $route->id)
            ->assertStatus(200)
            ->assertSee($route->route_code)
            ->assertSee('मार्ग के बस स्टॉप');
    }

    public function test_schedule_manager_renders_active_schedules(): void
    {
        Livewire::test(ScheduleManager::class)
            ->assertStatus(200)
            ->assertSee('बस समय-सारणी एवं अनुसूची')
            ->assertSee('07:00');
    }

    public function test_live_tracker_renders_map_and_telemetry_table(): void
    {
        Livewire::test(LiveTracker::class)
            ->assertStatus(200)
            ->assertSee('लाइव जीपीएस ट्रैकिंग मैप')
            ->assertSee('MP-04-EA-1024');
    }

    public function test_verification_moderator_can_approve_and_dismiss_reports(): void
    {
        $schedule = Schedule::first();
        $stop = BusStop::first();

        $report = CommunityVerification::create([
            'schedule_id' => $schedule->id,
            'bus_stop_id' => $stop->id,
            'device_hash' => 'hash123',
            'report_type' => 'delayed',
            'delay_minutes' => 20,
            'notes' => 'Toll plaza delay',
            'is_approved' => false,
        ]);

        // Test Approve
        Livewire::test(VerificationModerator::class)
            ->call('approve', $report->id);

        $this->assertTrue($report->fresh()->is_approved);

        // Test Dismiss (Deletes report)
        Livewire::test(VerificationModerator::class)
            ->call('dismiss', $report->id);

        $this->assertDatabaseMissing('community_verifications', [
            'id' => $report->id,
        ]);
    }
}
