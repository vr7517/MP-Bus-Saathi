# Database Schema Specification (PostgreSQL 18 + PostGIS 3.6)

All spatial geometries use spatial reference identifier **SRID 4326** (WGS 84 coordinate reference system: standard latitude and longitude).

## Spatial Extension Setup
```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

---

## Tables & Entity Definitions

### 1. `operators`
Bus operating agencies, private travels, or municipal corporations.
```sql
CREATE TABLE operators (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(150),
    phone VARCHAR(20),
    email VARCHAR(150),
    is_verified BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
```

### 2. `buses`
Individual vehicles registered under an operator.
```sql
CREATE TYPE bus_type_enum AS ENUM ('ordinary', 'express', 'ac', 'sleeper');

CREATE TABLE buses (
    id BIGSERIAL PRIMARY KEY,
    operator_id BIGINT REFERENCES operators(id) ON DELETE CASCADE,
    registration_number VARCHAR(30) UNIQUE NOT NULL, -- e.g., MP-04-AB-1234
    bus_type bus_type_enum DEFAULT 'ordinary',
    seating_capacity INT DEFAULT 40,
    has_gps_device BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
```

### 3. `bus_stops`
Bus stands, terminals, and intermediate rural pickup points.
```sql
CREATE TABLE bus_stops (
    id BIGSERIAL PRIMARY KEY,
    name_hi VARCHAR(255) NOT NULL, -- Hindi name (e.g., 'नादरा बस स्टैंड')
    name_en VARCHAR(255) NOT NULL, -- English name (e.g., 'Nadra Bus Stand')
    district VARCHAR(100) NOT NULL, -- e.g., 'Bhopal', 'Indore', 'Sehore'
    location GEOMETRY(Point, 4326) NOT NULL,
    is_major_junction BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_bus_stops_location ON bus_stops USING GIST (location);
CREATE INDEX idx_bus_stops_name_en ON bus_stops(name_en);
CREATE INDEX idx_bus_stops_name_hi ON bus_stops(name_hi);
```

### 4. `routes`
Defined transit routes between major source and destination stops.
```sql
CREATE TABLE routes (
    id BIGSERIAL PRIMARY KEY,
    route_code VARCHAR(50) UNIQUE NOT NULL, -- e.g., 'BPL-IND-EXP-01'
    source_stop_id BIGINT REFERENCES bus_stops(id) ON DELETE RESTRICT,
    destination_stop_id BIGINT REFERENCES bus_stops(id) ON DELETE RESTRICT,
    total_distance_km DECIMAL(6, 2) NOT NULL,
    estimated_duration_minutes INT NOT NULL,
    route_path GEOMETRY(LineString, 4326),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_routes_source_dest ON routes(source_stop_id, destination_stop_id);
CREATE INDEX idx_routes_path ON routes USING GIST (route_path);
```

### 5. `route_stops`
Ordered sequence of stops along a route.
```sql
CREATE TABLE route_stops (
    id BIGSERIAL PRIMARY KEY,
    route_id BIGINT REFERENCES routes(id) ON DELETE CASCADE,
    bus_stop_id BIGINT REFERENCES bus_stops(id) ON DELETE RESTRICT,
    stop_sequence INT NOT NULL, -- 1, 2, 3...
    distance_from_source_km DECIMAL(6, 2) DEFAULT 0.00,
    typical_travel_time_minutes INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_route_stop_sequence UNIQUE (route_id, stop_sequence)
);

CREATE INDEX idx_route_stops_lookup ON route_stops(bus_stop_id, route_id);
```

### 6. `schedules`
Timetables defining departure times and operating days.
```sql
CREATE TABLE schedules (
    id BIGSERIAL PRIMARY KEY,
    route_id BIGINT REFERENCES routes(id) ON DELETE CASCADE,
    bus_id BIGINT REFERENCES buses(id) ON DELETE SET NULL,
    departure_time TIME NOT NULL,
    expected_arrival_time TIME NOT NULL,
    days_of_week JSONB DEFAULT '["MON","TUE","WED","THU","FRI","SAT","SUN"]'::jsonb,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_schedules_route_time ON schedules(route_id, departure_time);
```

### 7. `live_bus_telemetry`
Real-time tracking snapshot (in-place updates for current state).
```sql
CREATE TYPE gps_status_enum AS ENUM ('live', 'delayed', 'unavailable');

CREATE TABLE live_bus_telemetry (
    id BIGSERIAL PRIMARY KEY,
    bus_id BIGINT UNIQUE REFERENCES buses(id) ON DELETE CASCADE,
    current_location GEOMETRY(Point, 4326) NOT NULL,
    current_speed FLOAT DEFAULT 0.0,
    heading FLOAT DEFAULT 0.0,
    gps_status gps_status_enum DEFAULT 'unavailable',
    last_ping_at TIMESTAMP WITH TIME ZONE NOT NULL,
    active_schedule_id BIGINT REFERENCES schedules(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_telemetry_location ON live_bus_telemetry USING GIST (current_location);
CREATE INDEX idx_telemetry_last_ping ON live_bus_telemetry(last_ping_at);
```

### 8. `community_verifications`
Crowdsourced status and timetable verification by passengers.
```sql
CREATE TYPE verification_type_enum AS ENUM ('on_time', 'delayed', 'cancelled', 'fare_update');

CREATE TABLE community_verifications (
    id BIGSERIAL PRIMARY KEY,
    schedule_id BIGINT REFERENCES schedules(id) ON DELETE CASCADE,
    bus_stop_id BIGINT REFERENCES bus_stops(id) ON DELETE RESTRICT,
    device_hash VARCHAR(64) NOT NULL,
    report_type verification_type_enum NOT NULL,
    delay_minutes INT DEFAULT 0,
    notes TEXT,
    verified_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_verifications_schedule ON community_verifications(schedule_id, verified_at);
```
