<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Operator;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ExpandedMPTransitSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Additional Operators
        $chartered = Operator::firstOrCreate(
            ['name' => 'Chartered Speed Limited (चार्टर्ड बस)'],
            [
                'contact_person' => 'Pankaj Gandhi',
                'phone' => '0731-4288888',
                'email' => 'support@charteredbus.in',
                'is_verified' => true,
            ]
        );

        $verma = Operator::firstOrCreate(
            ['name' => 'Verma Travels (वर्मा ट्रेवल्स)'],
            [
                'contact_person' => 'Sanjay Verma',
                'phone' => '0755-4077777',
                'email' => 'info@vermatravels.com',
                'is_verified' => true,
            ]
        );

        // 2. Additional Major Bus Terminals across MP
        $newStops = [
            'gwl_isbt' => [
                'name_hi' => 'ग्वालियर अंतर्राज्यीय बस टर्मिनल (ISBT)',
                'name_en' => 'Gwalior ISBT (Padav / Thathipur)',
                'district' => 'Gwalior',
                'lat' => 26.2235,
                'lng' => 78.1798,
            ],
            'sgr_bus_stand' => [
                'name_hi' => 'सागर मुख्य बस स्टैंड',
                'name_en' => 'Sagar Central Bus Stand',
                'district' => 'Sagar',
                'lat' => 23.8344,
                'lng' => 78.7422,
            ],
            'rew_new_stand' => [
                'name_hi' => 'रीवा नया बस स्टैंड',
                'name_en' => 'Rewa New Bus Stand',
                'district' => 'Rewa',
                'lat' => 24.5389,
                'lng' => 81.3094,
            ],
            'stn_bus_stand' => [
                'name_hi' => 'सतना बस स्टैंड',
                'name_en' => 'Satna Central Bus Stand',
                'district' => 'Satna',
                'lat' => 24.5989,
                'lng' => 80.8294,
            ],
            'rtm_bus_stand' => [
                'name_hi' => 'रतलाम नया बस स्टैंड',
                'name_en' => 'Ratlam New Bus Stand',
                'district' => 'Ratlam',
                'lat' => 23.3344,
                'lng' => 75.0412,
            ],
            'kha_bus_stand' => [
                'name_hi' => 'खंडवा मुख्य बस स्टैंड',
                'name_en' => 'Khandwa Main Bus Stand',
                'district' => 'Khandwa',
                'lat' => 21.8289,
                'lng' => 76.3456,
            ],
            'nrm_bus_stand' => [
                'name_hi' => 'नर्मदापुरम (होशंगाबाद) बस स्टैंड',
                'name_en' => 'Narmadapuram Bus Stand',
                'district' => 'Narmadapuram',
                'lat' => 22.7544,
                'lng' => 77.7256,
            ],
            'shi_bus_stand' => [
                'name_hi' => 'शिवपुरी बस स्टैंड',
                'name_en' => 'Shivpuri Bus Stand',
                'district' => 'Shivpuri',
                'lat' => 25.4212,
                'lng' => 77.6589,
            ],
            'vid_bus_stand' => [
                'name_hi' => 'विदिशा मुख्य बस स्टैंड',
                'name_en' => 'Vidisha Main Bus Stand',
                'district' => 'Vidisha',
                'lat' => 23.5289,
                'lng' => 77.8122,
            ],
            'kat_bus_stand' => [
                'name_hi' => 'कटनी बस स्टैंड',
                'name_en' => 'Katni Bus Stand',
                'district' => 'Katni',
                'lat' => 23.8389,
                'lng' => 80.4012,
            ],
        ];

        $stopModels = [];
        foreach ($newStops as $key => $s) {
            $stopModels[$key] = BusStop::firstOrCreate(
                ['name_en' => $s['name_en']],
                [
                    'name_hi' => $s['name_hi'],
                    'district' => $s['district'],
                    'latitude' => $s['lat'],
                    'longitude' => $s['lng'],
                    'is_major_junction' => true,
                ]
            );
        }

        // Fetch existing stops
        $bplNadra = BusStop::where('name_en', 'Nadra Bus Stand')->first();
        $indSarwate = BusStop::where('name_en', 'Sarwate Bus Stand, Indore')->first();
        $jblIsbt = BusStop::where('name_en', 'Jabalpur ISBT (Deendayal Chowk)')->first();

        // 3. New Routes
        // Route A: Bhopal to Gwalior (BPL-GWL-EXP-01) ~420km
        if ($bplNadra && isset($stopModels['gwl_isbt'])) {
            $bplGwl = Route::firstOrCreate(
                ['route_code' => 'BPL-GWL-EXP-01'],
                [
                    'source_stop_id' => $bplNadra->id,
                    'destination_stop_id' => $stopModels['gwl_isbt']->id,
                    'total_distance_km' => 418.0,
                    'estimated_duration_minutes' => 480,
                    'is_active' => true,
                ]
            );

            RouteStop::firstOrCreate(['route_id' => $bplGwl->id, 'stop_sequence' => 1], [
                'bus_stop_id' => $bplNadra->id,
                'distance_from_source_km' => 0.0,
                'typical_travel_time_minutes' => 0,
            ]);
            if (isset($stopModels['shi_bus_stand'])) {
                RouteStop::firstOrCreate(['route_id' => $bplGwl->id, 'stop_sequence' => 2], [
                    'bus_stop_id' => $stopModels['shi_bus_stand']->id,
                    'distance_from_source_km' => 305.0,
                    'typical_travel_time_minutes' => 350,
                ]);
            }
            RouteStop::firstOrCreate(['route_id' => $bplGwl->id, 'stop_sequence' => 3], [
                'bus_stop_id' => $stopModels['gwl_isbt']->id,
                'distance_from_source_km' => 418.0,
                'typical_travel_time_minutes' => 480,
            ]);

            // Add bus and schedule for BPL-GWL
            $gwlBus = Bus::firstOrCreate(
                ['registration_number' => 'MP-07-BA-5511'],
                [
                    'operator_id' => $chartered->id,
                    'bus_type' => 'sleeper',
                    'seating_capacity' => 36,
                    'has_gps_device' => true,
                    'is_active' => true,
                ]
            );

            Schedule::firstOrCreate(
                ['route_id' => $bplGwl->id, 'departure_time' => '21:30:00'],
                [
                    'bus_id' => $gwlBus->id,
                    'expected_arrival_time' => '05:30:00',
                    'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                    'is_active' => true,
                ]
            );
        }

        // Route B: Indore to Khandwa (IND-KHA-EXP-01) ~130km
        if ($indSarwate && isset($stopModels['kha_bus_stand'])) {
            $indKha = Route::firstOrCreate(
                ['route_code' => 'IND-KHA-EXP-01'],
                [
                    'source_stop_id' => $indSarwate->id,
                    'destination_stop_id' => $stopModels['kha_bus_stand']->id,
                    'total_distance_km' => 132.0,
                    'estimated_duration_minutes' => 180,
                    'is_active' => true,
                ]
            );

            RouteStop::firstOrCreate(['route_id' => $indKha->id, 'stop_sequence' => 1], [
                'bus_stop_id' => $indSarwate->id,
                'distance_from_source_km' => 0.0,
                'typical_travel_time_minutes' => 0,
            ]);
            RouteStop::firstOrCreate(['route_id' => $indKha->id, 'stop_sequence' => 2], [
                'bus_stop_id' => $stopModels['kha_bus_stand']->id,
                'distance_from_source_km' => 132.0,
                'typical_travel_time_minutes' => 180,
            ]);

            $khaBus = Bus::firstOrCreate(
                ['registration_number' => 'MP-12-CA-3388'],
                [
                    'operator_id' => $verma->id,
                    'bus_type' => 'express',
                    'seating_capacity' => 45,
                    'has_gps_device' => true,
                    'is_active' => true,
                ]
            );

            Schedule::firstOrCreate(
                ['route_id' => $indKha->id, 'departure_time' => '08:15:00'],
                [
                    'bus_id' => $khaBus->id,
                    'expected_arrival_time' => '11:15:00',
                    'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                    'is_active' => true,
                ]
            );
        }

        // Route C: Jabalpur to Rewa (JBL-REW-EXP-01) ~230km
        if ($jblIsbt && isset($stopModels['rew_new_stand'])) {
            $jblRew = Route::firstOrCreate(
                ['route_code' => 'JBL-REW-EXP-01'],
                [
                    'source_stop_id' => $jblIsbt->id,
                    'destination_stop_id' => $stopModels['rew_new_stand']->id,
                    'total_distance_km' => 236.0,
                    'estimated_duration_minutes' => 300,
                    'is_active' => true,
                ]
            );

            RouteStop::firstOrCreate(['route_id' => $jblRew->id, 'stop_sequence' => 1], [
                'bus_stop_id' => $jblIsbt->id,
                'distance_from_source_km' => 0.0,
                'typical_travel_time_minutes' => 0,
            ]);
            if (isset($stopModels['kat_bus_stand'])) {
                RouteStop::firstOrCreate(['route_id' => $jblRew->id, 'stop_sequence' => 2], [
                    'bus_stop_id' => $stopModels['kat_bus_stand']->id,
                    'distance_from_source_km' => 95.0,
                    'typical_travel_time_minutes' => 120,
                ]);
            }
            if (isset($stopModels['stn_bus_stand'])) {
                RouteStop::firstOrCreate(['route_id' => $jblRew->id, 'stop_sequence' => 3], [
                    'bus_stop_id' => $stopModels['stn_bus_stand']->id,
                    'distance_from_source_km' => 180.0,
                    'typical_travel_time_minutes' => 230,
                ]);
            }
            RouteStop::firstOrCreate(['route_id' => $jblRew->id, 'stop_sequence' => 4], [
                'bus_stop_id' => $stopModels['rew_new_stand']->id,
                'distance_from_source_km' => 236.0,
                'typical_travel_time_minutes' => 300,
            ]);

            $rewBus = Bus::firstOrCreate(
                ['registration_number' => 'MP-17-JA-7744'],
                [
                    'operator_id' => $chartered->id,
                    'bus_type' => 'ac',
                    'seating_capacity' => 40,
                    'has_gps_device' => true,
                    'is_active' => true,
                ]
            );

            Schedule::firstOrCreate(
                ['route_id' => $jblRew->id, 'departure_time' => '06:30:00'],
                [
                    'bus_id' => $rewBus->id,
                    'expected_arrival_time' => '11:30:00',
                    'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                    'is_active' => true,
                ]
            );
        }
    }
}
