# MP Bus Saathi - REST API Specification (v1)

Base URL: `/api/v1`

---

## 1. Bus Stops

### `GET /api/v1/stops`
Search stops by name or query nearby stops.
* **Query Parameters:**
  * `query` (optional, string): Search by Hindi or English name (e.g., "Nadra" or "नादरा")
  * `district` (optional, string): Filter by district
  * `lat`, `lng`, `radius_km` (optional, floats): Nearby stops within radius using PostGIS `ST_DWithin`
* **Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name_hi": "नादरा बस स्टैंड",
      "name_en": "Nadra Bus Stand",
      "district": "Bhopal",
      "latitude": 23.2642,
      "longitude": 77.4124,
      "is_major_junction": true,
      "distance_km": 0.45
    }
  ]
}
```

---

## 2. Source-to-Destination Route Finder

### `GET /api/v1/routes/search`
Find direct and transfer routes between source and destination bus stops.
* **Query Parameters:**
  * `source_stop_id` (required, int)
  * `destination_stop_id` (required, int)
  * `bus_type` (optional, enum: ordinary, express, ac, sleeper)
* **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "direct_routes": [
      {
        "route_id": 10,
        "route_code": "BPL-IND-EXP-01",
        "operator_name": "Atal Indore City Transport Services",
        "bus_type": "express",
        "total_distance_km": 192.5,
        "estimated_duration_minutes": 240,
        "stops_count": 8,
        "schedules": [
          {
            "schedule_id": 101,
            "departure_time": "07:30:00",
            "arrival_time": "11:30:00",
            "bus_number": "MP-04-AB-1234",
            "gps_status": "live",
            "last_ping_seconds_ago": 45,
            "eta_minutes": 18
          }
        ]
      }
    ],
    "transfer_routes": []
  }
}
```

---

## 3. Route Timetable & Schedules (Offline Syncable)

### `GET /api/v1/sync/snapshot`
Allows the Flutter mobile app to download or update the offline timetable dataset.
* **Query Parameters:**
  * `since` (optional, ISO timestamp): For incremental sync
* **Response (200 OK):**
```json
{
  "status": "success",
  "synced_at": "2026-11-08T02:45:00Z",
  "data": {
    "stops": [ ... ],
    "routes": [ ... ],
    "route_stops": [ ... ],
    "schedules": [ ... ]
  }
}
```

---

## 4. Live Bus Tracking & ETA

### `GET /api/v1/buses/{id}/live-status`
Get the real-time position, speed, and GPS health of a specific bus.
* **Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "bus_id": 12,
    "bus_number": "MP-04-AB-1234",
    "operator": "MP Intercity Bus",
    "gps_status": "live", // "live" if ping <= 180s, else "unavailable"
    "current_location": {
      "latitude": 23.1845,
      "longitude": 77.2512
    },
    "speed_kmh": 62.4,
    "heading": 245.0,
    "last_ping_at": "2026-11-08T02:44:20Z",
    "last_ping_seconds_ago": 40,
    "next_stop": {
      "stop_id": 4,
      "name_hi": "सीहोर बस स्टैंड",
      "name_en": "Sehore Bus Stand",
      "eta_minutes": 14
    }
  }
}
```

---

## 5. Telemetry Ingestion (Driver / GPS Device)

### `POST /api/v1/telemetry/ping`
Endpoint for vehicle GPS units or driver/conductor mobile apps.
* **Request Body:**
```json
{
  "bus_id": 12,
  "latitude": 23.1845,
  "longitude": 77.2512,
  "speed_kmh": 62.4,
  "heading": 245.0,
  "timestamp": "2026-11-08T02:44:20Z",
  "active_schedule_id": 101
}
```
* **Response (200 OK):**
```json
{
  "status": "success",
  "message": "Telemetry acknowledged"
}
```

---

## 6. Community Route & Timetable Verification

### `POST /api/v1/community/verify`
Passengers verify bus arrival, delays, or schedule discrepancies at a bus stop.
* **Request Body:**
```json
{
  "schedule_id": 101,
  "bus_stop_id": 4,
  "device_hash": "a8f5c...3e1",
  "report_type": "delayed", // "on_time", "delayed", "cancelled", "fare_update"
  "delay_minutes": 20,
  "notes": "Traffic jam near flyover construction"
}
```
* **Response (201 Created):**
```json
{
  "status": "success",
  "message": "Verification recorded. Thank you for helping your fellow passengers!"
}
```
