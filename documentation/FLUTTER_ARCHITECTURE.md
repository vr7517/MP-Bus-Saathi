# MP Bus Saathi — Flutter Architecture Document

## 1. Architectural Philosophy
- **Clean Feature-Oriented Architecture:** Modular segregation of models, core network services, offline database cache, reusable UI widgets, and screen controllers.
- **Resilience-First Design:** Graceful degradation on network timeout or cellular connectivity loss in rural areas.

```
mobile_app/
├── lib/
│   ├── core/
│   │   ├── api_service.dart       # REST API integration with timeout resilience
│   │   └── offline_service.dart   # SharedPreferences persistent transit cache
│   ├── models/
│   │   ├── bus_stop.dart          # Stop entities with bilingual names
│   │   ├── district.dart          # Official 55 MP LGD districts
│   │   ├── live_status.dart       # Live GPS telemetry & next-stop ETA
│   │   └── route_item.dart        # Direct & connecting journey models
│   ├── screens/
│   │   ├── splash_screen.dart
│   │   ├── language_select_screen.dart
│   │   ├── main_navigation_shell.dart # 5-tab Bottom Navigation
│   │   ├── home_screen.dart           # Home dashboard & district chips
│   │   ├── search_screen.dart         # Journey search & filters
│   │   ├── live_map_screen.dart       # OpenStreetMap interactive tracking
│   │   ├── saved_screen.dart          # Bookmarked routes & cache sync
│   │   ├── profile_screen.dart        # Settings, privacy & role portals
│   │   ├── driver_mode_screen.dart    # Smartphone GPS beacon
│   │   ├── operator_portal_screen.dart# Operator dashboard
│   │   └── verification_dialog.dart   # Community timetable feedback
│   ├── theme/
│   │   └── app_theme.dart             # Material 3 Design Tokens
│   ├── widgets/
│   │   ├── status_badge.dart
│   │   ├── route_card.dart
│   │   └── connecting_journey_card.dart
│   └── main.dart
```

---

## 2. Data Flow & GPS Freshness Lifecycle
1. **GPS Simulator / Hardware VLTD / Smartphone Beacon:** Transmits position every 6s to `POST /api/v1/telemetry/ping`.
2. **Backend Engine:** Computes `$secondsAgo` and dynamic next stop ETA along route path.
3. **Flutter App (`LiveMapScreen`):**
   - Polls `/api/v1/buses/{id}/live-status` every 6s.
   - If `lastPingSecondsAgo <= 180s`: State is `GPS Live 🟢`, marker animates to fresh coordinates, ETA updates.
   - If `lastPingSecondsAgo > 180s`: State reverts to `GPS Unavailable 🟡` with scheduled timetable fallback.
