<?php

namespace Tests\Feature;

use Database\Seeders\MPTransitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_can_access_live_tracking_map_page(): void
    {
        $response = $this->get('/admin/tracking');
        $response->assertStatus(200);
        $response->assertSee('लाइव जीपीएस ट्रैकिंग मैप');
    }

    public function test_can_access_fleet_and_routes_pages(): void
    {
        $this->get('/admin/buses')->assertStatus(200)->assertSee('बस फ्लीट प्रबंधन');
        $this->get('/admin/routes')->assertStatus(200)->assertSee('बस मार्ग व स्टॉप');
        $this->get('/admin/schedules')->assertStatus(200)->assertSee('बस समय-सारणी');
        $this->get('/admin/verifications')->assertStatus(200)->assertSee('कम्युनिटी फीडबैक');
    }
}

