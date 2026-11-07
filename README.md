# MP Bus Saathi (मध्य प्रदेश बस साथी)

> An offline-friendly, bilingual public bus transit tracking and route discovery platform for Madhya Pradesh, India.

---

## 🌟 Key Features
- **Hindi-First Interface (हिन्दी-प्रथम):** Tailored for MP commuters with full English support.
- **Offline-Friendly (ऑफलाइन सुविधा):** Bus routes, stops, and scheduled timetables are locally cached in the mobile app, enabling searching even in low/no connectivity rural areas.
- **GPS Live vs GPS Unavailable Clarity:** Transparent status indicator — green for real-time live telemetry (< 180s ping), amber/gray for scheduled timetables when GPS is offline.
- **Source-to-Destination Route Finder:** Direct and 1-transfer transit discovery across major bus terminals (Bhopal, Indore, Ujjain, Jabalpur, Gwalior) and intermediate rural stops.
- **Community Timetable Verification:** Crowdsourced verification from commuters at bus stops to maintain up-to-date ground truth.
- **OpenStreetMap Integration:** Free, open mapping without proprietary map API billing.
- **Livewire Operator & Admin Portal:** Lightweight, modern administrative dashboard without Node.js or React overhead.

---

## 🏗 Repository Structure (Monorepo)
```
MP-Bus-Saathi/
├── backend/            # Laravel REST API + Blade/Livewire Admin
├── mobile_app/         # Flutter Android Passenger App (Offline Drift DB + OSM)
├── docs/               # Architecture, PostGIS Schema & API Specifications
│   ├── architecture.md
│   ├── database_schema.md
│   └── api_specification.md
├── scripts/            # Telemetry simulators and utilities
│   └── simulate_bus_gps.py
└── README.md
```

---

## 🛠 Tech Stack
- **Mobile Client:** Flutter 3.47+ / Dart 3.13+ (Android SDK 36.1.0)
- **Backend Framework:** Laravel (PHP 8.5)
- **Admin Dashboard:** Laravel Blade + Livewire
- **Database:** PostgreSQL 18 with PostGIS 3.6 extension
- **Maps:** OpenStreetMap (`flutter_map`)

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.5+ & Composer 2.9+
- PostgreSQL 18 with PostGIS extension (`CREATE EXTENSION postgis;`)
- Flutter 3.47+ & Android SDK 36
- Python 3.14+ (for simulation utilities)

### Initial Setup
Detailed setup instructions for the backend and mobile client are documented in [`docs/architecture.md`](file:///data/Projects/MP-Bus-Saathi/docs/architecture.md).
