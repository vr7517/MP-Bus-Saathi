# MP Bus Saathi — Flutter Screen Inventory

## 1. Passenger App Screen Inventory

| Screen Name | File Path | Route / Navigation | Purpose & Core Features |
|---|---|---|---|
| **Splash Screen** | `lib/screens/splash_screen.dart` | `/splash` (Initial) | Brand logo animation, loader, checks language preference. |
| **Language Select** | `lib/screens/language_select_screen.dart` | `/language-select` | Hindi (Default) and English selection with persistent storage. |
| **Main Navigation Shell** | `lib/screens/main_navigation_shell.dart` | `/` (Shell) | Bottom Navigation hosting Home, Search, Live Map, Saved, and Profile. |
| **Home Screen** | `lib/screens/home_screen.dart` | Tab 0 (`Home`) | Quick route finder, 55-district filter chips, nearby stops, popular routes, offline status bar. |
| **Journey Search Screen** | `lib/screens/search_screen.dart` | Tab 1 (`Search`) | Comprehensive search with direct + 1-transfer connecting routes, bus-type filter (Express/AC/Ordinary), sort by departure/duration. |
| **Journey Detail Screen** | `lib/screens/journey_detail_screen.dart` | Push on Route Card | Full route timeline, all stops with arrival times, distance, operator credentials, timetable verification button. |
| **Live Map Screen** | `lib/screens/live_map_screen.dart` | Tab 2 (`Live Map`) / Direct Bus Track | Full-screen interactive OpenStreetMap, real-time bus marker with heading, route polylines, nearest stop ETA, recenter button. |
| **Saved & Favorites Screen** | `lib/screens/saved_screen.dart` | Tab 3 (`Saved`) | Bookmarked routes, favorite bus stops, offline downloaded timetables. |
| **Profile & Settings Screen** | `lib/screens/profile_screen.dart` | Tab 4 (`Profile`) | Language switch, Dark/Light mode toggle, Driver Mode launcher, Operator login portal, Privacy Policy, About MP Bus Saathi. |
| **Driver / Conductor Mode** | `lib/screens/driver_mode_screen.dart` | Push from Profile / AppBar | Smartphone beacon GPS broadcasting to `/api/v1/telemetry/ping` every 6 seconds for buses without hardware AIS-140 GPS. |
| **Community Verification Modal**| `lib/screens/verification_dialog.dart` | Dialog | On-time, Delay, or Cancellation reporting with anti-spam device fingerprint. |
| **Operator Portal Screen** | `lib/screens/operator_portal_screen.dart` | Push from Profile | Operator dashboard showing fleet status, active schedules, and direct link to web admin. |
