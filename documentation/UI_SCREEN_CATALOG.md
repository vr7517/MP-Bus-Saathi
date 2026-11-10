# MP Bus Saathi — UI Screen Catalog

This catalog documents the complete suite of screens implemented in the Flutter application for MP Bus Saathi (मध्य प्रदेश बस साथी).

---

## 1. Onboarding & Navigation Shell

### 1.1 Splash Screen (`lib/screens/splash_screen.dart`)
- **Branding:** State transit logo with animated fade curve.
- **Loader:** Circular progress indicator with `डेटा लोड हो रहा है...` label.
- **Auto-Routing:** Reads persistent `SharedPreferences`:
  - If first-time user: Routes to `LanguageSelectScreen`.
  - If returning user: Routes directly to `MainNavigationShell(initialHindi: ...)`.

### 1.2 Language Selection Screen (`lib/screens/language_select_screen.dart`)
- **Bilingual Option Cards:** Hindi (Primary & Recommended) and English.
- **Persistent Selection:** Saves selection to `is_hindi` preference key.
- **Action:** Full-width continue button navigating to `MainNavigationShell`.

### 1.3 Main Navigation Shell (`lib/screens/main_navigation_shell.dart`)
- **Persistent 5-Tab Bottom Navigation:**
  1. `होम (Home)`: Quick route search, 55-district filter chips, offline status indicator.
  2. `खोजें (Search)`: Advanced journey finder with direct and 1-transfer connecting routes.
  3. `लाइव मैप (Live Map)`: Full-screen interactive OpenStreetMap with bus marker, dynamic speed, and next stop ETA.
  4. `पसंदीदा (Saved)`: Saved popular routes, cached stops, and offline timetable synchronization.
  5. `प्रोफ़ाइल (Profile)`: Language toggle, Driver/Conductor mode, Operator portal, Community feedback, and Privacy Policy.

---

## 2. Core Passenger Screens

### 2.1 Home Screen (`lib/screens/home_screen.dart`)
- **Offline Cache Status Bar:** Real-time indicator displaying offline availability and last synchronization timestamp.
- **55-District Filter Chips:** Horizontally scrollable chips for MP districts (Bhopal, Indore, Gwalior, Jabalpur, Ujjain, Sagar, Rewa, etc.).
- **Route Finder Card:** Origin & Destination selectors with one-tap location swap button.
- **Direct & Connecting Results:** Automatically displays direct bus options followed by 1-transfer connecting journey options.

### 2.2 Advanced Journey Search Screen (`lib/screens/search_screen.dart`)
- **Bus Category Filters:** All Buses, Express, AC, Ordinary, Sleeper.
- **Connecting Transit Support:** Renders `ConnectingJourneyCard` displaying Leg 1, Interchange Junction Hub, and Leg 2.
- **Empty States:** Clear guidance suggesting alternate hubs or resetting filters.

### 2.3 Live Map Screen (`lib/screens/live_map_screen.dart`)
- **Map Engine:** `flutter_map` with OpenStreetMap raster tiles.
- **Dynamic Polyline:** Fetches actual stop coordinates from `/api/v1/routes/{id}/stops`.
- **Moving Bus Marker:** Live coordinates, heading orientation, and speed (km/h).
- **Status Indicator:** Green pill badge (`GPS Live 🟢`) when ping is within 180s, Amber (`GPS Unavailable 🟡`) when waiting for telemetry.
- **Dynamic ETA Card:** Real-time distance (km) and estimated arrival time to the next upcoming bus stop.
- **Recenter & Zoom Controls:** One-touch bus centering and zoom in/out floating action buttons.

### 2.4 Saved & Offline Transit Screen (`lib/screens/saved_screen.dart`)
- **Offline Sync Manager:** Displays cached stop count and one-tap "Sync Latest Timetables" button.
- **Bookmarked Routes:** Pre-saved corridors (Bhopal-Indore, Gwalior-Jabalpur, Bhopal-Ujjain) with direct tracking links.
- **Cached Bus Terminals:** Quick-reference list of terminals accessible without cellular internet.

### 2.5 Profile & Settings Screen (`lib/screens/profile_screen.dart`)
- **User Identity:** Commuter Guest Mode with verification status.
- **Runtime Language Switcher:** One-tap toggle between हिन्दी and English.
- **Professional Portals:** Direct access to Driver/Conductor Beacon Mode and Bus Operator Dashboard.
- **Community Contribution:** Report missing routes, delayed buses, or incorrect stop locations.
- **DPDPA 2023 Privacy Policy:** Modal summary and transparent compliance information.

---

## 3. Professional Roles & Operator Screens

### 3.1 Driver / Conductor Mode (`lib/screens/driver_mode_screen.dart`)
- **Smartphone Telemetry Beacon:** Solves the missing hardware GPS problem in rural MP by allowing conductors to transmit live vehicle coordinates every 6 seconds to `/api/v1/telemetry/ping`.
- **Bus & Schedule Selector:** Dropdown selection of assigned vehicle registration number.

### 3.2 Bus Operator Portal (`lib/screens/operator_portal_screen.dart`)
- **Fleet KPI Metrics:** Total buses, active routes, and GPS-connected vehicles.
- **Quick Operator Actions:** Driver mode launcher, timetable management, and RTO permit validity overview.
- **Web Admin Sync:** Connects operators to the Livewire administration portal.
