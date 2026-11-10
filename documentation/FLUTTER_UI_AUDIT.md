# MP Bus Saathi — Flutter UI/UX Audit

## 1. Project Overview & Environment
- **Project Name:** MP Bus Saathi (मध्य प्रदेश बस साथी)
- **Tagline:** हर गाँव, हर शहर — हर बस की जानकारी
- **SDK & Environment:** Flutter 3.47.9 / Dart 3.13.7 (Stable Channel)
- **Target OS:** Android (API 21+), with iOS compatibility architecture
- **Active Dependencies:**
  - `flutter_map: ^7.0.2` & `latlong2: ^0.9.1` (OpenStreetMap integration)
  - `http: ^1.2.2` (REST API client)
  - `intl: ^0.20.3` (Bilingual date/time and currency formatting)
  - `shared_preferences: ^2.3.2` (Offline schedule cache and persistent preferences)
  - `flutter_localizations: sdk`

---

## 2. Existing Screen Audit & Pain Points

| Component / Screen | Existing State | Identified Issue & Root Cause | Resolution Required |
|---|---|---|---|
| **Live Map Tracking** | `live_map_screen.dart` | Map rendered static coordinates and did not track bus dynamically. Root cause: `LiveBusStatus.fromJson` crashed on float `last_ping_seconds_ago as int?` causing `ApiService.getLiveStatus` to return `null`. Also `_routePoints` was hardcoded to only 6 Bhopal-Indore stops. | Fix JSON deserializer with `num.round()`, load dynamic route stops polyline via API (`/api/v1/routes/{id}/stops`), and add auto-centering bus follower. |
| **Route & Journey Search** | `home_screen.dart` / `api_service.dart` | Dropped `transfer_routes`. Only direct routes were parsed; connecting village-to-city trips returned "No buses found". | Support both direct and connecting 1-transfer journeys in `searchRoutes`, displaying interchange stops and transfer waiting time. |
| **Navigation Structure** | Single screen (`home_screen.dart`) | Lacked bottom navigation bar. Features like Live Map, Search, Saved Routes, and Profile were scattered or inaccessible. | Implement persistent 5-tab Bottom Navigation: (1) Home, (2) Journey Search, (3) Live Map, (4) Saved/Favorites, (5) Profile & Settings. |
| **Operator / Driver Access** | Single driver mode screen | Hidden behind an appbar icon. No operator fleet overview or trip dispatcher. | Add dedicated Driver & Operator navigation portal with role-based access control. |
| **Design System & Tokens** | Inline colors & ad-hoc styles | Inconsistent color codes (`0xFF1E1B4B`, `0xFF4338CA`, etc.), lack of dark mode tokens, and missing typography standards. | Create centralized `AppTheme`, `AppColors`, `AppTypography`, and reusable component library. |

---

## 3. Architecture & Refactoring Plan
- Migrate to modular, clean architecture:
  - `lib/theme/` (Centralized design tokens, Light & Dark themes, Material 3)
  - `lib/models/` (Robust typed models for stops, routes, transfers, schedules, telemetry)
  - `lib/core/` (API service with full error resilience, Offline service)
  - `lib/widgets/` (Reusable UI components: BusCard, RouteCard, StatusBadge, BottomNavigation, SearchField)
  - `lib/screens/` (Splash, LanguageSelect, MainNavigationShell, Home, Search, JourneyDetail, LiveMap, Saved, Profile, DriverMode, OperatorDashboard)
