import 'package:flutter_test/flutter_test.dart';
import 'package:mp_bus_saathi/core/offline_service.dart';
import 'package:mp_bus_saathi/models/bus_stop.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  group('OfflineService Unit Tests', () {
    test('Default offline seed stops are loaded when SharedPreferences cache is empty', () async {
      SharedPreferences.setMockInitialValues({});

      final stops = await OfflineService.loadStops();

      expect(stops.isNotEmpty, true);
      expect(stops.any((s) => s.nameEn == 'Nadra Bus Stand'), true);
      expect(stops.any((s) => s.nameEn == 'Sarwate Bus Stand, Indore'), true);
      expect(stops.any((s) => s.nameEn == 'Gwalior ISBT (Padav / Thathipur)'), true);
      expect(stops.any((s) => s.nameEn == 'Jabalpur ISBT (Deendayal Chowk)'), true);
      expect(stops.length, 16);
    });

    test('Saving stops caches them to SharedPreferences and reloads correctly', () async {
      SharedPreferences.setMockInitialValues({});

      final customStops = [
        BusStop(
          id: 99,
          nameHi: 'रीवा नया बस स्टैंड',
          nameEn: 'Rewa New Bus Stand',
          district: 'Rewa',
          latitude: 24.5362,
          longitude: 81.3037,
          isMajorJunction: true,
        ),
      ];

      await OfflineService.saveStops(customStops);
      final loaded = await OfflineService.loadStops();

      expect(loaded.length, 1);
      expect(loaded.first.id, 99);
      expect(loaded.first.nameEn, 'Rewa New Bus Stand');
      expect(loaded.first.district, 'Rewa');

      final syncTime = await OfflineService.getLastSyncTime();
      expect(syncTime, isNotNull);
    });
  });
}

