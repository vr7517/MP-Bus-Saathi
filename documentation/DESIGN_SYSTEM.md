# MP Bus Saathi — Design System Specification

## 1. Visual Language
- **Foundational Standard:** Material 3 customized for Indian Public Transit.
- **Palette Identity:** High contrast, accessible in direct sunlight, and lightweight for low-cost Android screens.

---

## 2. Color System Reference
```dart
class AppColors {
  // Brand
  static const Color primary = Color(0xFF1E1B4B);        // Deep Navy
  static const Color brandIndigo = Color(0xFF4338CA);    // Active Accent
  static const Color teal = Color(0xFF0D9488);           // Connecting Hubs
  static const Color amber = Color(0xFFD97706);          // Scheduled Transit

  // GPS Telemetry
  static const Color gpsLive = Color(0xFF059669);        // Live GPS (<=180s)
  static const Color gpsLiveBg = Color(0xFFD1FAE5);
  static const Color gpsUnavailable = Color(0xFFD97706); // Stale / Untracked
  static const Color gpsUnavailableBg = Color(0xFFFEF3C7);

  // Surfaces
  static const Color bgLight = Color(0xFFF8FAFC);
  static const Color surfaceLight = Colors.white;
  static const Color borderLight = Color(0xFFE2E8F0);
}
```

---

## 3. Typography & Sizing Scale
| Style Token | Size / Weight | Intended Purpose |
|---|---|---|
| `Brand Title` | 18sp / Bold | Top App Bar and main headers |
| `Card Header` | 14sp / Bold | Stop names, Departure times |
| `Body Main` | 13sp / SemiBold | Operator names, district badges |
| `Caption / Label` | 11sp / Regular | Distance, duration, GPS timestamps |
| `Pill Badge` | 10sp / Bold | Status indicators (GPS Live / Unavailable) |

---

## 4. Component Library
1. `StatusBadge` (`lib/widgets/status_badge.dart`): Real-time freshness indicator enforcing 180s threshold.
2. `RouteCard` (`lib/widgets/route_card.dart`): Direct journey presenter with schedule timeline and live tracking launcher.
3. `ConnectingJourneyCard` (`lib/widgets/connecting_journey_card.dart`): Dual-leg connecting transit card displaying interchange station and layover interval.
4. `MainNavigationShell` (`lib/screens/main_navigation_shell.dart`): Persistent bottom navigation with 5 primary tabs.
