import 'package:flutter_test/flutter_test.dart';
import 'package:mp_bus_saathi/main.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  testWidgets('App smoke test and title verification', (WidgetTester tester) async {
    SharedPreferences.setMockInitialValues({});
    await tester.pumpWidget(const MPBusSaathiApp());
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 500));

    expect(find.text('मध्य प्रदेश बस साथी'), findsOneWidget);
  });
}
