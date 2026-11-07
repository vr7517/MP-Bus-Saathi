# MP Bus Saathi (मध्य प्रदेश बस साथी) - Architecture Specification

## 1. Executive Overview
MP Bus Saathi is an offline-friendly, bilingual (Hindi-first), public bus transit tracking and discovery platform designed specifically for the state of Madhya Pradesh, India. It serves passengers across urban hubs and rural routes, bus operators, and transport authorities.

## 2. Technology Stack

| Layer | Technology | Key Details |
|---|---|---|
| **Mobile Client** | Flutter 3.47+ / Dart 3.13+ | Android-first passenger app with offline SQLite/Drift cache & OSM mapping. |
| **Backend API** | Laravel (PHP 8.5) | Clean RESTful API, Eloquent ORM, background jobs for telemetry processing. |
| **Admin Portal** | Laravel Blade + Livewire | Interactive operator/admin UI without Node.js/React dependencies. |
| **Database** | PostgreSQL 18 + PostGIS 3.6 | Spatial indexing (`GIST`), PostGIS geometries (`Point`, `LineString` SRID 4326). |
| **Mapping Engine** | OpenStreetMap (OSM) | `flutter_map` (Leaflet-compatible Dart engine) with offline tile caching. |
| **Localization** | Flutter l10n ARB + DB bilingual columns | Hindi (default) and English. |

## 3. Core Architecture Tenets

### 3.1 Offline-First for Rural Connectivity
- **Static Transit Data Sync:** Routes, stops, and scheduled timetables are synchronized from the backend and stored locally inside the mobile app using SQLite/Drift.
- **Zero-Network Searching:** Source-to-destination search and timetable queries execute completely against the local SQLite database when offline.
- **Graceful Degradation:** When cellular network is unavailable, the UI automatically transitions to offline mode, explicitly presenting the scheduled timetable while hiding real-time telemetry.

### 3.2 GPS Live vs GPS Unavailable Status Indicator
To establish passenger trust:
- **GPS Live (Green Badge):** Displayed when a telemetry ping was recorded within the last **180 seconds** (3 minutes). Real-time map location and dynamic ETA are shown.
- **GPS Unavailable - Scheduled (Amber/Gray Badge):** Displayed when telemetry is older than 180 seconds or the assigned bus has no active GPS unit. The user is shown the planned scheduled timetable with explicit transparency.

### 3.3 Community Route & Timetable Verification
- Passengers at bus stands can verify whether a bus arrived on time, was delayed, or was cancelled.
- Anti-spam safeguards: Device fingerprint hash, geofence radius check (user must be within 1.5 km of the bus stop), and multiple corroborating reports before marking timetable anomalies.

### 3.4 OpenStreetMap Integration
- Free from proprietary map API billing and rate limits.
- Supports client-side tile caching for highway corridors across Madhya Pradesh (e.g., Bhopal-Indore expressway, NH-46).
