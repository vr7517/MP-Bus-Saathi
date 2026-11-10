# MP Bus Saathi (मध्य प्रदेश बस साथी) — Project Audit Report

**Date of Audit:** November 10, 2026  
**Auditor:** Principal Software Architect & QA Lead  
**Scope:** Monorepo (`/data/Projects/MP-Bus-Saathi`) encompassing Backend REST API, Livewire Admin Portal, Flutter Android Client, and Telemetry Daemons.

---

## 1. Executive Summary

MP Bus Saathi is an offline-friendly, bilingual (Hindi-first), public bus transit tracking and route discovery platform designed specifically for the state of Madhya Pradesh, India.

The existing repository is cleanly structured as a production-grade monorepo containing:
1. **Laravel 12 / PHP 8.5 Backend:** Full REST API v1 (`/api/v1/`) with geospatial support, routing engine, ETA calculation, and telemetry ingestion.
2. **Laravel Blade & Livewire Admin & Operator Portal:** Modern responsive portal with zero Node.js/React dependencies, role-based access control (Super Admin vs. Operators), and interactive OpenStreetMap radar.
3. **Flutter 3.47+ / Dart 3.13+ Android Client:** Bilingual passenger app with onboarding splash, language selection, route search, offline fallback cache, live OpenStreetMap vehicle tracking, community verification dialog, and driver/conductor GPS beacon mode.
4. **GPS Streaming Daemons:** Continuous waypoint simulation engines across the Bhopal-Indore expressway corridor.

---

## 2. Technology Stack & Environment Inspection

| Subsystem | Framework / Tech | Version / Details | Status |
|---|---|---|---|
| **Backend Framework** | Laravel REST API | 12.x / PHP 8.5.0RC1 | Operational |
| **Admin Portal** | Laravel Blade + Livewire | Livewire 3.x with Tailwind CSS CDN + Leaflet | Operational |
| **Mobile Client** | Flutter / Dart | Flutter 3.47+ / Dart 3.13+, Android Target SDK 36 | Operational |
| **Mapping Engine** | OpenStreetMap (OSM) | `flutter_map` 7.0.2 in Flutter; Leaflet 1.9.4 in Web Admin | Operational |
| **Database** | SQLite (Dev) / PostGIS (Prod) | 10 Migrations (SRID 4326 PostGIS geometry ready) | Operational |
| **Cache & Offline** | Local SQLite & SharedPreferences | Offline fallback stops, schedules, snapshot sync | Operational |
| **Physical Hardware** | Android 11 Physical Device | Realme C11 (RMX3231) via ADB reverse tunnel | Connected & Tested |

---

## 3. Database Schema & Migration Status

All 10 database migrations are executed and verified:
1. `0001_01_01_000000_create_users_table`: Core user accounts and session store.
2. `2026_11_08_000000_enable_postgis_extension`: Automatic PostGIS extension initialization for PostgreSQL.
3. `2026_11_08_000001_create_operators_table`: Transport operator registry (MPSRTC, AiCTSL, Private).
4. `2026_11_08_000002_create_buses_table`: Fleet registry with vehicle categories, seating, and GPS flags.
5. `2026_11_08_000003_create_bus_stops_table`: Bilingual stops with district, latitude, longitude, and junction tags.
6. `2026_11_08_000004_create_routes_table`: Inter-district and rural route definitions with distance and runtime.
7. `2026_11_08_000005_create_route_stops_table`: Ordered stop sequences along routes with cumulative distance.
8. `2026_11_08_000006_create_schedules_table`: Timetables, departure/arrival schedules, operating days.
9. `2026_11_08_000007_create_live_bus_telemetry_table`: High-frequency vehicle GPS telemetry coordinates and speed.
10. `2026_11_08_000008_create_community_verifications_table`: Crowdsourced passenger delay and cancellation logs.
11. `2026_11_10_000001_add_role_and_operator_id_to_users_table`: Multi-tenant operator role scoping.

---

## 4. Current Test Suite Health

- **Backend Feature & Unit Tests:** `24 passed, 0 failed, 97 assertions` (`php artisan test`).
- **Flutter Widget & Model Tests:** `9 passed, 0 failed` (`flutter test`).
- **Flutter Static Analysis:** `0 errors, 0 warnings` (`flutter analyze`).
