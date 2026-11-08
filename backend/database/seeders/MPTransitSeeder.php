<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\LiveBusTelemetry;
use App\Models\Operator;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MPTransitSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Operators
        $mpsrts = Operator::create([
            'name' => 'MP State Road Transport Services (मध्य प्रदेश राज्य परिवहन)',
            'contact_person' => 'Rajesh Sharma',
            'phone' => '0755-2555111',
            'email' => 'support@mptransport.gov.in',
            'is_verified' => true,
        ]);

        $aictsl = Operator::create([
            'name' => 'Atal Indore City Transport Services (AiCTSL)',
            'contact_person' => 'Manoj Verma',
            'phone' => '0731-2499888',
            'email' => 'info@aictsl.in',
            'is_verified' => true,
        ]);

        $hansTravels = Operator::create([
            'name' => 'Hans Travels (हंस ट्रेवल्स)',
            'contact_person' => 'Dinesh Patel',
            'phone' => '9826012345',
            'email' => 'booking@hanstravels.com',
            'is_verified' => true,
        ]);

        // 2. Bus Stops
        $stops = [
            'bpl_nadra' => BusStop::create([
                'name_hi' => 'नादरा बस स्टैंड',
                'name_en' => 'Nadra Bus Stand',
                'district' => 'Bhopal',
                'latitude' => 23.2642000,
                'longitude' => 77.4124000,
                'is_major_junction' => true,
            ]),
            'bpl_isbt' => BusStop::create([
                'name_hi' => 'आईएसबीटी कुशाभाऊ ठाकरे',
                'name_en' => 'ISBT Kushabhau Thakre',
                'district' => 'Bhopal',
                'latitude' => 23.2173000,
                'longitude' => 77.4378000,
                'is_major_junction' => true,
            ]),
            'bpl_halalpur' => BusStop::create([
                'name_hi' => 'हलालपुर बस स्टैंड',
                'name_en' => 'Halalpur Bus Stand',
                'district' => 'Bhopal',
                'latitude' => 23.2798000,
                'longitude' => 77.3482000,
                'is_major_junction' => false,
            ]),
            'sehore_bypass' => BusStop::create([
                'name_hi' => 'सीहोर क्रेसेंट चौराहा',
                'name_en' => 'Sehore Crescent Square',
                'district' => 'Sehore',
                'latitude' => 23.2080000,
                'longitude' => 77.0850000,
                'is_major_junction' => false,
            ]),
            'sehore_stand' => BusStop::create([
                'name_hi' => 'सीहोर मुख्य बस स्टैंड',
                'name_en' => 'Sehore Main Bus Stand',
                'district' => 'Sehore',
                'latitude' => 23.2005000,
                'longitude' => 77.0833000,
                'is_major_junction' => true,
            ]),
            'ashta' => BusStop::create([
                'name_hi' => 'आष्टा बस स्टैंड',
                'name_en' => 'Ashta Bus Stand',
                'district' => 'Sehore',
                'latitude' => 23.0180000,
                'longitude' => 76.7190000,
                'is_major_junction' => false,
            ]),
            'dewas' => BusStop::create([
                'name_hi' => 'देवास बस स्टैंड',
                'name_en' => 'Dewas Bus Stand',
                'district' => 'Dewas',
                'latitude' => 22.9676000,
                'longitude' => 76.0534000,
                'is_major_junction' => true,
            ]),
            'ind_sarwate' => BusStop::create([
                'name_hi' => 'सरवटे बस स्टैंड, इंदौर',
                'name_en' => 'Sarwate Bus Stand, Indore',
                'district' => 'Indore',
                'latitude' => 22.7126000,
                'longitude' => 75.8672000,
                'is_major_junction' => true,
            ]),
            'ind_navlakha' => BusStop::create([
                'name_hi' => 'नवलखा बस स्टैंड, इंदौर',
                'name_en' => 'Navlakha Bus Stand, Indore',
                'district' => 'Indore',
                'latitude' => 22.6980000,
                'longitude' => 75.8750000,
                'is_major_junction' => false,
            ]),
            'ujn_nanakheda' => BusStop::create([
                'name_hi' => 'नानाखेड़ा बस स्टैंड, उज्जैन',
                'name_en' => 'Nanakheda Bus Stand, Ujjain',
                'district' => 'Ujjain',
                'latitude' => 23.1550000,
                'longitude' => 75.7720000,
                'is_major_junction' => true,
            ]),
            'jbp_isbt' => BusStop::create([
                'name_hi' => 'जबलपुर आईएसबीटी (दीनदयाल चौक)',
                'name_en' => 'Jabalpur ISBT (Deendayal Chowk)',
                'district' => 'Jabalpur',
                'latitude' => 23.1950000,
                'longitude' => 79.9140000,
                'is_major_junction' => true,
            ]),
        ];

        // 3. PostGIS Geometry update if PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            foreach ($stops as $stop) {
                DB::statement(
                    "UPDATE bus_stops SET location = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?",
                    [$stop->longitude, $stop->latitude, $stop->id]
                );
            }
        }

        // 4. Routes
        // Route 1: Bhopal Nadra -> Indore Sarwate (via Halalpur, Sehore Stand, Ashta, Dewas)
        $routeBplInd = Route::create([
            'route_code' => 'BPL-IND-EXP-01',
            'source_stop_id' => $stops['bpl_nadra']->id,
            'destination_stop_id' => $stops['ind_sarwate']->id,
            'total_distance_km' => 192.50,
            'estimated_duration_minutes' => 240,
            'is_active' => true,
        ]);

        $bplIndStops = [
            ['stop' => $stops['bpl_nadra'], 'dist' => 0.00, 'time' => 0],
            ['stop' => $stops['bpl_halalpur'], 'dist' => 8.50, 'time' => 20],
            ['stop' => $stops['sehore_stand'], 'dist' => 38.00, 'time' => 60],
            ['stop' => $stops['ashta'], 'dist' => 85.00, 'time' => 125],
            ['stop' => $stops['dewas'], 'dist' => 152.00, 'time' => 190],
            ['stop' => $stops['ind_sarwate'], 'dist' => 192.50, 'time' => 240],
        ];

        foreach ($bplIndStops as $seq => $item) {
            RouteStop::create([
                'route_id' => $routeBplInd->id,
                'bus_stop_id' => $item['stop']->id,
                'stop_sequence' => $seq + 1,
                'distance_from_source_km' => $item['dist'],
                'typical_travel_time_minutes' => $item['time'],
            ]);
        }

        // Route 2: Indore Sarwate -> Ujjain Nanakheda
        $routeIndUjn = Route::create([
            'route_code' => 'IND-UJN-ORD-01',
            'source_stop_id' => $stops['ind_sarwate']->id,
            'destination_stop_id' => $stops['ujn_nanakheda']->id,
            'total_distance_km' => 54.00,
            'estimated_duration_minutes' => 75,
            'is_active' => true,
        ]);

        $indUjnStops = [
            ['stop' => $stops['ind_sarwate'], 'dist' => 0.00, 'time' => 0],
            ['stop' => $stops['ujn_nanakheda'], 'dist' => 54.00, 'time' => 75],
        ];

        foreach ($indUjnStops as $seq => $item) {
            RouteStop::create([
                'route_id' => $routeIndUjn->id,
                'bus_stop_id' => $item['stop']->id,
                'stop_sequence' => $seq + 1,
                'distance_from_source_km' => $item['dist'],
                'typical_travel_time_minutes' => $item['time'],
            ]);
        }

        // 5. Buses
        $bus1 = Bus::create([
            'operator_id' => $mpsrts->id,
            'registration_number' => 'MP-04-EA-1024',
            'bus_type' => 'express',
            'seating_capacity' => 48,
            'has_gps_device' => true,
            'is_active' => true,
        ]);

        $bus2 = Bus::create([
            'operator_id' => $aictsl->id,
            'registration_number' => 'MP-09-FA-8899',
            'bus_type' => 'ac',
            'seating_capacity' => 42,
            'has_gps_device' => true,
            'is_active' => true,
        ]);

        $bus3 = Bus::create([
            'operator_id' => $hansTravels->id,
            'registration_number' => 'MP-04-HB-4422',
            'bus_type' => 'ordinary',
            'seating_capacity' => 52,
            'has_gps_device' => false, // No GPS device -> GPS Unavailable
            'is_active' => true,
        ]);

        // 6. Schedules
        $scheduleMorning = Schedule::create([
            'route_id' => $routeBplInd->id,
            'bus_id' => $bus1->id,
            'departure_time' => '07:00:00',
            'expected_arrival_time' => '11:00:00',
            'days_of_week' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            'is_active' => true,
        ]);

        $scheduleAfternoon = Schedule::create([
            'route_id' => $routeBplInd->id,
            'bus_id' => $bus2->id,
            'departure_time' => '13:30:00',
            'expected_arrival_time' => '17:30:00',
            'days_of_week' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            'is_active' => true,
        ]);

        $scheduleEvening = Schedule::create([
            'route_id' => $routeBplInd->id,
            'bus_id' => $bus3->id,
            'departure_time' => '17:00:00',
            'expected_arrival_time' => '21:00:00',
            'days_of_week' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            'is_active' => true,
        ]);

        $scheduleUjn = Schedule::create([
            'route_id' => $routeIndUjn->id,
            'bus_id' => $bus1->id,
            'departure_time' => '12:00:00',
            'expected_arrival_time' => '13:15:00',
            'days_of_week' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            'is_active' => true,
        ]);

        // 7. Live Telemetry
        // Bus 1: Fresh GPS ping (45 seconds ago) -> GPS LIVE (Green)
        LiveBusTelemetry::create([
            'bus_id' => $bus1->id,
            'latitude' => 23.2045000,
            'longitude' => 77.0842000, // Near Sehore
            'current_speed' => 58.5,
            'heading' => 245.0,
            'gps_status' => 'live',
            'last_ping_at' => Carbon::now()->subSeconds(45),
            'active_schedule_id' => $scheduleMorning->id,
        ]);

        // Bus 2: Stale GPS ping (35 minutes ago) -> GPS UNAVAILABLE (Amber)
        LiveBusTelemetry::create([
            'bus_id' => $bus2->id,
            'latitude' => 23.2798000,
            'longitude' => 77.3482000,
            'current_speed' => 0.0,
            'heading' => 0.0,
            'gps_status' => 'unavailable',
            'last_ping_at' => Carbon::now()->subMinutes(35),
            'active_schedule_id' => $scheduleAfternoon->id,
        ]);
    }
}

