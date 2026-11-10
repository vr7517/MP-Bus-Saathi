# MP Bus Saathi — Flutter Implementation Plan

## Phase 1: Core Models & Deserializer Bug Fixes
- [x] Fix `LiveBusStatus.fromJson` float deserialization (`last_ping_seconds_ago as int?` -> `(json['last_ping_seconds_ago'] as num?)?.round()`).
- [x] Fix `registration_number` vs `bus_number` property mapping.
- [x] Fix `NextStopInfo.fromJson` float ETA / distance mapping.
- [x] Add `ConnectingJourney` model in `route_item.dart` to support 1-transfer journeys returned by Laravel's `transfer_routes`.

## Phase 2: Design System & Theme Foundation
- [x] Create `lib/theme/app_theme.dart` with Material 3, light & dark theme data, centralized `AppColors` and `AppTypography`.
- [x] Create reusable UI widgets in `lib/widgets/`:
  - `status_badge.dart` (Strict 180s GPS Live vs Unavailable indicator)
  - `route_card.dart` (Bilingual route & schedule presentation)
  - `connecting_journey_card.dart` (Multi-leg transit routes with interchange display)
  - `custom_app_bar.dart` (Consistent branding & actions)

## Phase 3: Bottom Navigation Shell & Screens
- [x] Create `MainNavigationShell` (`lib/screens/main_navigation_shell.dart`) with 5 tabs:
  1. Home (Quick search, district filters, recent routes, offline status)
  2. Search (Advanced journey finder with direct + connecting routes, filters, sorting)
  3. Live Map (Full interactive OpenStreetMap with bus tracking, route polyline, stop markers, dynamic ETA)
  4. Saved (Bookmarked routes, favorite stops, offline schedules)
  5. Profile (Language toggle, Dark mode, Driver mode entry, Operator portal, Privacy Policy, Help)
- [x] Create `SearchScreen` (`lib/screens/search_screen.dart`).
- [x] Create `JourneyDetailScreen` (`lib/screens/journey_detail_screen.dart`).
- [x] Upgrade `LiveMapScreen` (`lib/screens/live_map_screen.dart`) to fetch real route stops from `/api/v1/routes/{id}/stops` instead of hardcoded coordinates.
- [x] Create `SavedScreen` (`lib/screens/saved_screen.dart`).
- [x] Create `ProfileScreen` (`lib/screens/profile_screen.dart`).
- [x] Create `OperatorPortalScreen` (`lib/screens/operator_portal_screen.dart`).

## Phase 4: Integration, Testing & Verification
- [x] Update `main.dart` with `AppTheme` and `MainNavigationShell`.
- [x] Run `flutter analyze` (Zero errors / warnings).
- [x] Update Flutter widget and unit tests.
- [x] Build debug APK (`flutter build apk --debug`).
- [x] Install on connected physical device (`adb install -r`).
- [x] Test live GPS tracking on device.
