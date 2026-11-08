import 'package:flutter_test/flutter_test.dart';
import 'package:mp_bus_saathi/models/bus_stop.dart';
import 'package:mp_bus_saathi/models/live_status.dart';
import 'package:mp_bus_saathi/models/route_item.dart';

void main() {
  group('BusTransit Models Unit Tests', () {
    test('BusStop parses JSON correctly and supports bilingual display', () {
      final json = {
        'id': 1,
        'name_hi': 'नादरा बस स्टैंड',
        'name_en': 'Nadra Bus Stand',
        'district': 'Bhopal',
        'latitude': 23.2642,
        'longitude': 77.4124,
        'is_major_junction': true,
      };

      final stop = BusStop.fromJson(json);

      expect(stop.id, 1);
      expect(stop.nameHi, 'नादरा बस स्टैंड');
      expect(stop.nameEn, 'Nadra Bus Stand');
      expect(stop.district, 'Bhopal');
      expect(stop.latitude, 23.2642);
      expect(stop.longitude, 77.4124);
      expect(stop.isMajorJunction, true);

      // Test bilingual display name
      expect(stop.displayName(true), 'नादरा बस स्टैंड (Nadra Bus Stand)');
      expect(stop.displayName(false), 'Nadra Bus Stand (नादरा बस स्टैंड)');
    });

    test('RouteSearchResult & ScheduleItem correctly identifies GPS Live vs Unavailable', () {
      final json = {
        'route_id': 10,
        'route_code': 'BPL-IND-EXP-01',
        'origin_stop': 'नादरा बस स्टैंड',
        'final_destination_stop': 'सरवटे बस स्टैंड',
        'travel_distance_km': 192.5,
        'estimated_duration_minutes': 240,
        'stops_in_segment': 6,
        'schedules': [
          {
            'schedule_id': 101,
            'bus_id': 1,
            'bus_number': 'MP-04-EA-1024',
            'operator_name': 'MPSRTS',
            'bus_type': 'express',
            'has_gps': true,
            'gps_status': 'live',
            'departure_time': '07:00:00',
            'arrival_time': '11:00:00',
          },
          {
            'schedule_id': 102,
            'bus_id': 2,
            'bus_number': 'MP-09-FA-8899',
            'operator_name': 'AiCTSL',
            'bus_type': 'ac',
            'has_gps': false,
            'gps_status': 'unavailable',
            'departure_time': '13:30:00',
            'arrival_time': '17:30:00',
          }
        ]
      };

      final result = RouteSearchResult.fromJson(json);

      expect(result.routeId, 10);
      expect(result.routeCode, 'BPL-IND-EXP-01');
      expect(result.schedules.length, 2);

      expect(result.schedules[0].isGpsLive, true);
      expect(result.schedules[0].busNumber, 'MP-04-EA-1024');

      expect(result.schedules[1].isGpsLive, false);
      expect(result.schedules[1].gpsStatus, 'unavailable');
    });

    test('LiveBusStatus accurately computes health and parses next stop ETA', () {
      final json = {
        'bus_id': 1,
        'bus_number': 'MP-04-EA-1024',
        'bus_type': 'express',
        'operator': 'MPSRTS',
        'gps_status': 'live',
        'gps_status_label_hi': 'लाइव जीपीएस ट्रैकिंग चालू',
        'gps_status_label_en': 'Live GPS Tracking Active',
        'current_location': {
          'latitude': 23.2045,
          'longitude': 77.0842,
          'speed_kmh': 58.5,
          'heading': 245.0,
        },
        'last_ping_seconds_ago': 42,
        'next_stop': {
          'stop_id': 4,
          'name_hi': 'सीहोर मुख्य बस स्टैंड',
          'name_en': 'Sehore Main Bus Stand',
          'district': 'Sehore',
          'distance_km': 6.2,
          'eta_minutes': 12,
        }
      };

      final status = LiveBusStatus.fromJson(json);

      expect(status.isLive, true);
      expect(status.busNumber, 'MP-04-EA-1024');
      expect(status.speedKmh, 58.5);
      expect(status.latitude, 23.2045);
      expect(status.longitude, 77.0842);
      expect(status.lastPingSecondsAgo, 42);

      expect(status.nextStop, isNotNull);
      expect(status.nextStop!.nameHi, 'सीहोर मुख्य बस स्टैंड');
      expect(status.nextStop!.distanceKm, 6.2);
      expect(status.nextStop!.etaMinutes, 12);
    });
  });
}
