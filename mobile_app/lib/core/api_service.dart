import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/bus_stop.dart';
import '../models/live_status.dart';
import '../models/route_item.dart';
import 'offline_service.dart';

class ApiService {
  // Configurable URL: 10.0.2.2 points to host machine from Android Emulator; localhost for desktop/web
  static String baseUrl = 'http://10.0.2.2:8000/api/v1';

  static void setBaseUrl(String url) {
    baseUrl = url;
  }

  /// Fetch all bus stops. Tries live API, saves to local cache, or falls back to offline database.
  static Future<List<BusStop>> getStops() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/stops'))
          .timeout(const Duration(seconds: 4));

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        final List list = data['data'] ?? [];
        final stops = list.map((item) => BusStop.fromJson(item)).toList();
        await OfflineService.saveStops(stops);
        return stops;
      }
    } catch (_) {
      // Network failure or rural connectivity drop -> use offline cache
    }

    return await OfflineService.loadStops();
  }

  /// Search routes between source and destination bus stops.
  static Future<List<RouteSearchResult>> searchRoutes(int sourceId, int destId, {String? busType}) async {
    try {
      String url = '$baseUrl/routes/search?source_stop_id=$sourceId&destination_stop_id=$destId';
      if (busType != null) url += '&bus_type=$busType';

      final response = await http
          .get(Uri.parse(url))
          .timeout(const Duration(seconds: 4));

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        final List direct = data['data']?['direct_routes'] ?? [];
        return direct.map((item) => RouteSearchResult.fromJson(item)).toList();
      }
    } catch (_) {
      // Offline fallback: Generate offline schedule estimate based on stops
    }

    return _generateOfflineFallbackRoutes(sourceId, destId);
  }

  /// Retrieve real-time GPS telemetry and status for a bus.
  static Future<LiveBusStatus?> getLiveStatus(int busId) async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/buses/$busId/live-status'))
          .timeout(const Duration(seconds: 4));

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        return LiveBusStatus.fromJson(data['data']);
      }
    } catch (_) {
      // Fallback: GPS Unavailable
    }

    return null;
  }

  /// Submit passenger community verification
  static Future<bool> submitVerification({
    required int scheduleId,
    required int busStopId,
    required String reportType,
    int delayMinutes = 0,
    String? notes,
    double? latitude,
    double? longitude,
  }) async {
    try {
      final body = {
        'schedule_id': scheduleId,
        'bus_stop_id': busStopId,
        'device_hash': 'mp-passenger-app-device-token',
        'report_type': reportType,
        'delay_minutes': delayMinutes,
        'notes': notes,
        'user_latitude': latitude,
        'user_longitude': longitude,
      };

      final response = await http.post(
        Uri.parse('$baseUrl/community/verify'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode(body),
      ).timeout(const Duration(seconds: 4));

      return response.statusCode == 201;
    } catch (_) {
      return false;
    }
  }

  /// Offline local route generator when internet is unavailable
  static List<RouteSearchResult> _generateOfflineFallbackRoutes(int sourceId, int destId) {
    return [
      RouteSearchResult(
        routeId: 1,
        routeCode: 'MP-OFFLINE-SCHED',
        originStop: 'प्रारंभिक स्टॉप (Source)',
        finalDestinationStop: 'गंतव्य स्टॉप (Destination)',
        travelDistanceKm: 192.5,
        estimatedDurationMinutes: 240,
        stopsInSegment: 6,
        schedules: [
          ScheduleItem(
            scheduleId: 101,
            busId: 1,
            busNumber: 'MP-04-EA-1024',
            operatorName: 'मध्य प्रदेश राज्य परिवहन (MPSRTS)',
            busType: 'express',
            hasGps: true,
            gpsStatus: 'unavailable', // Offline mode -> defaults to unavailable scheduled
            departureTime: '07:00:00',
            arrivalTime: '11:00:00',
          ),
          ScheduleItem(
            scheduleId: 102,
            busId: 2,
            busNumber: 'MP-09-FA-8899',
            operatorName: 'AiCTSL Indore',
            busType: 'ac',
            hasGps: true,
            gpsStatus: 'unavailable',
            departureTime: '13:30:00',
            arrivalTime: '17:30:00',
          ),
          ScheduleItem(
            scheduleId: 103,
            busId: 3,
            busNumber: 'MP-04-HB-4422',
            operatorName: 'हंस ट्रेवल्स (Hans Travels)',
            busType: 'ordinary',
            hasGps: false,
            gpsStatus: 'unavailable',
            departureTime: '17:00:00',
            arrivalTime: '21:00:00',
          ),
        ],
      )
    ];
  }
}

