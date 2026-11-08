import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mp_bus_saathi/main.dart';
import 'package:mp_bus_saathi/screens/verification_dialog.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  setUp(() {
    SharedPreferences.setMockInitialValues({});
  });

  testWidgets('App launches in Hindi-first mode and toggles to English', (WidgetTester tester) async {
    await tester.pumpWidget(const MPBusSaathiApp());
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 500));

    // 1. Verify Hindi Title & Offline Banner by default
    expect(find.text('मध्य प्रदेश बस साथी'), findsOneWidget);
    expect(find.text('ऑफलाइन फ्रेंडली बस ट्रैकिंग'), findsOneWidget);
    expect(find.text('English'), findsOneWidget); // Language switch button

    // 2. Tap English toggle
    await tester.tap(find.text('English'));
    await tester.pump();

    // 3. Verify English UI renders
    expect(find.text('MP Bus Saathi'), findsOneWidget);
    expect(find.text('Offline-Friendly Bus Transit'), findsOneWidget);
    expect(find.text('हिन्दी'), findsOneWidget); // Button toggled to Hindi
  });

  testWidgets('Verification dialog renders on-time, delay, and cancellation options', (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: VerificationDialog(
            scheduleId: 1,
            busStopId: 1,
            isHindi: true,
          ),
        ),
      ),
    );
    await tester.pump();

    expect(find.text('कम्युनिटी बस सत्यापन'), findsOneWidget);
    expect(find.text('समय पर'), findsOneWidget);
    expect(find.text('देरी'), findsOneWidget);
    expect(find.text('रद्द'), findsOneWidget);
    expect(find.text('सबमिट करें (Submit)'), findsOneWidget);
  });
}
