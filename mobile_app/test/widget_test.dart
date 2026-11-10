import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mp_bus_saathi/main.dart';
import 'package:mp_bus_saathi/screens/driver_mode_screen.dart';
import 'package:mp_bus_saathi/screens/language_select_screen.dart';
import 'package:mp_bus_saathi/screens/verification_dialog.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  setUp(() {
    SharedPreferences.setMockInitialValues({});
  });

  testWidgets('Splash screen displays branding and loader', (WidgetTester tester) async {
    await tester.pumpWidget(const MPBusSaathiApp());
    await tester.pump();

    // Verify Splash branding
    expect(find.text('मध्य प्रदेश बस साथी'), findsOneWidget);
    expect(find.text('MP Bus Saathi • Public Transit Navigator'), findsOneWidget);
    expect(find.byType(CircularProgressIndicator), findsOneWidget);

    // Allow splash timer to complete
    await tester.pump(const Duration(milliseconds: 2000));
  });

  testWidgets('Language selection screen displays Hindi and English choices', (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: LanguageSelectScreen(),
      ),
    );
    await tester.pump();

    expect(find.text('भाषा चुनें / Select Language'), findsOneWidget);
    expect(find.text('अपनी पसंदीदा भाषा चुनें'), findsOneWidget);
    expect(find.text('हिन्दी'), findsOneWidget);
    expect(find.text('English'), findsOneWidget);
    expect(find.text('आगे बढ़ें (Continue)'), findsOneWidget);
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

  testWidgets('Driver mode screen renders bus selector and toggle button', (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: DriverModeScreen(isHindi: true),
      ),
    );
    await tester.pump();

    expect(find.text('चालक / परिचालक मोड'), findsOneWidget);
    expect(find.text('⚪ ट्रैकिंग बंद'), findsOneWidget);
    expect(find.text('लाइव ट्रैकिंग शुरू करें (Start Live Tracking)'), findsOneWidget);
  });
}
