<?php

namespace Tests\Feature;

use App\Livewire\Admin\BusManager;
use App\Livewire\Admin\DashboardOverview;
use App\Livewire\Admin\LiveTracker;
use App\Livewire\Admin\RouteManager;
use App\Livewire\Admin\ScheduleManager;
use App\Livewire\Admin\VerificationModerator;
use App\Livewire\Auth\Login;
use App\Models\Bus;
use App\Models\BusStop;
use App\Models\CommunityVerification;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\MPTransitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $operatorUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MPTransitSeeder::class);
        $this->seed(UserSeeder::class);

        $this->adminUser = User::where('role', 'admin')->first();
        $this->operatorUser = User::where('role', 'operator')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_can_authenticate_via_livewire_login_component(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@mpbussaathi.in')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_authenticated_admin_can_access_dashboard_overview(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('मध्य प्रदेश बस साथी');
        $response->assertSee('कुल बसें');
    }

    public function test_dashboard_overview_livewire_component_renders_stats(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(DashboardOverview::class)
            ->assertStatus(200)
            ->assertSee('पंजीकृत बस फ्लीट')
            ->assertSee('लाइव जीपीएस');
    }

    public function test_operator_dashboard_only_counts_their_fleet(): void
    {
        Livewire::actingAs($this->operatorUser)
            ->test(DashboardOverview::class)
            ->assertStatus(200)
            ->assertSet('isOperator', true);
    }

    public function test_bus_manager_can_create_new_bus_and_toggle_gps(): void
    {
        $operator = Operator::first();

        // 1. Create a new bus via Livewire as admin
        Livewire::actingAs($this->adminUser)
            ->test(BusManager::class)
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

        $createdBus = Bus::where('registration_number', 'MP-04-ZZ-9999')->first();

        // 2. Toggle GPS
        Livewire::actingAs($this->adminUser)
            ->test(BusManager::class)
            ->call('toggleGps', $createdBus->id)
            ->assertHasNoErrors();

        $this->assertFalse($createdBus->fresh()->has_gps_device);
    }

    public function test_route_manager_lists_active_mp_routes(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(RouteManager::class)
            ->assertStatus(200)
            ->assertSee('BPL-IND-EXP-01')
            ->assertSee('IND-UJN-ORD-01');
    }

    public function test_schedule_manager_renders_timetables(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ScheduleManager::class)
            ->assertStatus(200)
            ->assertSee('MP-04-EA-1024')
            ->assertSee('07:00');
    }

    public function test_live_tracker_renders_map_and_bus_telemetry(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(LiveTracker::class)
            ->assertStatus(200)
            ->assertSee('MP-04-EA-1024')
            ->assertSee('admin-map');
    }

    public function test_verification_moderator_can_approve_or_reject(): void
    {
        $schedule = Schedule::first();
        $stop = BusStop::first();

        $verification = CommunityVerification::create([
            'schedule_id' => $schedule->id,
            'bus_stop_id' => $stop->id,
            'report_type' => 'delayed',
            'delay_minutes' => 20,
            'device_hash' => hash('sha256', 'reporter-test-device'),
            'verified_at' => now(),
            'is_approved' => false,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(VerificationModerator::class)
            ->assertSee('+20 मिनट')
            ->call('approve', $verification->id)
            ->assertHasNoErrors();

        $this->assertTrue($verification->fresh()->is_approved);
    }

    public function test_data_management_renders_and_imports_stops(): void
    {
        \Livewire\Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\DataManagement::class)
            ->assertStatus(200)
            ->assertSee('डेटा प्रबंधन एवं कवरेज')
            ->set('importType', 'stops')
            ->set('rawJsonData', json_encode([
                [
                    'name_hi' => 'बैतूल बस स्टैंड',
                    'name_en' => 'Betul Bus Stand',
                    'district' => 'Betul',
                    'latitude' => 21.9015,
                    'longitude' => 77.9018,
                    'is_major_junction' => true,
                ]
            ]))
            ->call('executeImport')
            ->assertHasNoErrors()
            ->assertSee('सफलतापूर्वक सहेजे गए');

        $this->assertDatabaseHas('bus_stops', [
            'name_en' => 'Betul Bus Stand',
            'district' => 'Betul',
        ]);
    }

    public function test_privacy_policy_page_renders_successfully(): void
    {
        $response = $this->get('/privacy-policy');
        $response->assertStatus(200);
        $response->assertSee('Privacy Policy');
        $response->assertSee('MP Bus Saathi');
        $response->assertSee('DPDPA 2023');
    }
}
