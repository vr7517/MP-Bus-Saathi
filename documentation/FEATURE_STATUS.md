# MP Bus Saathi — Feature Status Matrix

**Classification Standard:**
- **Complete & Verified (🟢):** Fully functional UI, Backend API, Database models, and passing automated tests.
- **Partially Implemented (🟡):** Core logic operational; pending additional datasets or edge-case workflows.
- **Not Implemented (⚪):** Planned in future milestone.
- **Blocked (🔴):** Requires external proprietary credentials, paid API key, or signed government MOU.

---

## Feature Matrix

| Feature / Module | Status | Verification Details |
|---|:---:|---|
| **Bilingual Support (Hindi / English)** | 🟢 | Hindi default with English toggle in Mobile app, Admin portal, and Database bilingual columns (`name_hi`, `name_en`). |
| **Monorepo Architecture** | 🟢 | Unified Git monorepo (`backend/`, `mobile_app/`, `docs/`, `scripts/`). |
| **Bus Fleet Registry** | 🟢 | `buses` & `operators` entities, Livewire Bus Manager, validation rules. |
| **Bus Stops & Terminals Registry** | 🟢 | 21 real MP stops/terminals seeded across Bhopal, Indore, Sehore, Dewas, Ujjain, Jabalpur, Gwalior, Rewa, Satna, Sagar, Khandwa, Ratlam, Narmadapuram, Katni, Vidisha, Shivpuri. |
| **Route & Timetable Management** | 🟢 | Sequence-ordered stops with cumulative distances, departure and arrival times. |
| **Source-to-Destination Route Finder** | 🟢 | Direct routes and 1-transfer interchange algorithm (`RouteFinderService.php`). |
| **GPS Live vs GPS Unavailable Policy** | 🟢 | 180-second freshness threshold rigorously enforced. |
| **Driver & Conductor Telemetry Beacon** | 🟢 | Smartphone GPS beacon in Flutter client (`DriverModeScreen.dart`) transmitting to `/api/v1/telemetry/ping`. |
| **Continuous GPS Simulator Daemon** | 🟢 | Background Python daemon streaming waypoints along Bhopal-Indore expressway. |
| **OpenStreetMap Mobile Navigation** | 🟢 | `flutter_map` displaying route polylines, bus marker, and stop pins. |
| **Admin Web Portal (Blade + Livewire)** | 🟢 | Zero Node/React dependencies. 7 Livewire components (Dashboard, Fleet, Routes, Timetables, Live Tracking, Verifications, Data Management & Completeness). |
| **Admin & Operator Role-Based Access** | 🟢 | Super Admin manages all; Operators scoped strictly to their own fleet. Livewire Login/Logout. |
| **Community Timetable Verification** | 🟢 | Mobile reporting dialog + backend anti-spam device hash + Livewire moderation queue. |
| **Rural Offline-Friendly Experience** | 🟢 | Bundled offline MP stops cache (16 major hubs), SQLite persistent storage, offline route generation. |
| **Statewide 55 Districts Expansion** | 🟢 | All 55 MP districts seeded from LGD with Census codes, coordinates, bilingual names; API (`/api/v1/districts`), mobile filter chips, and Livewire completeness tracking. |
| **Data Ingestion & Provenance Pipeline** | 🟢 | Batch CSV & JSON import with source provenance (LGD, Parivahan, OSM), duplicate validation, and audit trail in Admin portal. |
| **Play Store Privacy Policy & Compliance** | 🟢 | Bilingual Privacy Policy at `/privacy-policy` complying with India's DPDPA 2023. |
| **Govt API Auto-Sync (NIC Parivahan)** | 🔴 | Blocked: Direct automated sync requires official NIC Parivahan API credentials & state MOU. |
| **AIS-140 Hardware VLTD Gateway** | 🟡 | REST webhook endpoint ready; awaiting physical AIS-140 device server IP forward. |
