import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/bus_stop.dart';

class OfflineService {
  static const String _keyStops = 'mp_cached_stops';
  static const String _keySyncTime = 'mp_last_sync_timestamp';

  // Preloaded MP transit stops for guaranteed zero-network first-time launch
  static final List<Map<String, dynamic>> defaultStops = [
    {
      'id': 1,
      'name_hi': 'नादरा बस स्टैंड',
      'name_en': 'Nadra Bus Stand',
      'district': 'Bhopal',
      'latitude': 23.2642,
      'longitude': 77.4124,
      'is_major_junction': true,
    },
    {
      'id': 2,
      'name_hi': 'आईएसबीटी कुशाभाऊ ठाकरे',
      'name_en': 'ISBT Kushabhau Thakre',
      'district': 'Bhopal',
      'latitude': 23.2173,
      'longitude': 77.4378,
      'is_major_junction': true,
    },
    {
      'id': 3,
      'name_hi': 'हलालपुर बस स्टैंड',
      'name_en': 'Halalpur Bus Stand',
      'district': 'Bhopal',
      'latitude': 23.2798,
      'longitude': 77.3482,
      'is_major_junction': false,
    },
    {
      'id': 4,
      'name_hi': 'सीहोर मुख्य बस स्टैंड',
      'name_en': 'Sehore Main Bus Stand',
      'district': 'Sehore',
      'latitude': 23.2005,
      'longitude': 77.0833,
      'is_major_junction': true,
    },
    {
      'id': 5,
      'name_hi': 'आष्टा बस स्टैंड',
      'name_en': 'Ashta Bus Stand',
      'district': 'Sehore',
      'latitude': 23.0180,
      'longitude': 76.7190,
      'is_major_junction': false,
    },
    {
      'id': 6,
      'name_hi': 'देवास बस स्टैंड',
      'name_en': 'Dewas Bus Stand',
      'district': 'Dewas',
      'latitude': 22.9676,
      'longitude': 76.0534,
      'is_major_junction': true,
    },
    {
      'id': 7,
      'name_hi': 'सरवटे बस स्टैंड, इंदौर',
      'name_en': 'Sarwate Bus Stand, Indore',
      'district': 'Indore',
      'latitude': 22.7126,
      'longitude': 75.8672,
      'is_major_junction': true,
    },
    {
      'id': 8,
      'name_hi': 'नानाखेड़ा बस स्टैंड, उज्जैन',
      'name_en': 'Nanakheda Bus Stand, Ujjain',
      'district': 'Ujjain',
      'latitude': 23.1550,
      'longitude': 75.7720,
      'is_major_junction': true,
    },
  ];

  static Future<void> saveStops(List<BusStop> stops) async {
    final prefs = await SharedPreferences.getInstance();
    final jsonList = stops.map((s) => s.toJson()).toList();
    await prefs.setString(_keyStops, jsonEncode(jsonList));
    await prefs.setString(_keySyncTime, DateTime.now().toIso8601String());
  }

  static Future<List<BusStop>> loadStops() async {
    final prefs = await SharedPreferences.getInstance();
    final jsonStr = prefs.getString(_keyStops);

    if (jsonStr != null && jsonStr.isNotEmpty) {
      try {
        final List decoded = jsonDecode(jsonStr);
        return decoded.map((item) => BusStop.fromJson(item)).toList();
      } catch (_) {}
    }

    // Return bundled offline seed stops
    return defaultStops.map((item) => BusStop.fromJson(item)).toList();
  }

  static Future<String?> getLastSyncTime() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_keySyncTime);
  }
}

