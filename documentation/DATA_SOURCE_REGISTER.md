# MP Bus Saathi — Government & Open Transit Data Source Register

**Version:** 1.0.0  
**Updated:** November 2026  
**Objective:** Maintain strict data provenance, licensing compliance, and official transport record references across Madhya Pradesh.

---

## 1. Official Government & Open Transit Sources

### Source 1: Madhya Pradesh Transport Department (मध्य प्रदेश परिवहन विभाग)
- **Official URL:** `https://transport.mp.gov.in/` and `https://services.mp.gov.in/`
- **Data Owner:** Government of Madhya Pradesh, Transport Department
- **Geographic Coverage:** All 55 Districts of Madhya Pradesh
- **Data Categories:** RTO/DTO jurisdiction, Stage carriage permit policies, bus fare structures, approved routes.
- **Access Method:** Public web access & official gazette notifications.
- **Licensing / Terms:** Government open-access notice; attribution required.
- **Freshness:** Updated periodically based on state gazette notifications.
- **Integration Status:** Baseline route guidelines and fare structure imported.

### Source 2: Ministry of Road Transport and Highways (MoRTH) & Parivahan Sewa
- **Official URL:** `https://parivahan.gov.in/`
- **Data Owner:** National Informatics Centre (NIC) / MoRTH, Govt. of India
- **Geographic Coverage:** National / Madhya Pradesh State (MP-01 to MP-74 RTO codes)
- **Data Categories:** Vehicle class, registration guidelines, AIS-140 VLTD compliance standards.
- **Access Method:** Public portal and statutory standards documentation.
- **Licensing / Terms:** Official public resource.
- **Integration Status:** AIS-140 telemetry schema specifications implemented.

### Source 3: Local Government Directory (LGD) — Ministry of Panchayati Raj
- **Official URL:** `https://lgdirectory.gov.in/`
- **Data Owner:** Ministry of Panchayati Raj, Govt. of India
- **Geographic Coverage:** All 55 Districts, 428 Tehsils, 313 Development Blocks, and 23,000+ Gram Panchayats of MP.
- **Data Categories:** Hierarchical administrative codes (State -> District -> Tehsil -> Block -> Gram Panchayat -> Village).
- **Access Method:** Downloadable datasets (CSV / Excel) and official directory lookup.
- **Licensing / Terms:** Open Government Data (OGD) Platform India (`https://data.gov.in/`).
- **Integration Status:** 55 Districts and major Tehsil/Junction coordinates mapped into geographic entities.

### Source 4: OpenStreetMap (OSM) & Overpass API (Transit Geometries)
- **Official URL:** `https://www.openstreetmap.org/`
- **Data Owner:** OpenStreetMap Foundation & Contributors
- **Geographic Coverage:** Statewide highways (NH-44, NH-46, NH-52, SH-18, SH-23), MP State Highway corridors, and bus terminals.
- **Data Categories:** Road geometries, bus stations (`amenity=bus_station`), highway stop nodes (`highway=bus_stop`), waypoint coordinates.
- **Access Method:** Overpass QL API & open planet extracts.
- **Licensing / Terms:** Open Database License (ODbL); attribution required: `© OpenStreetMap contributors`.
- **Integration Status:** Fully integrated in mobile Flutter (`flutter_map`) and web admin Leaflet radar.

### Source 5: Urban & Intercity Transit Corporations
- **AiCTSL (Atal Indore City Transport Services Ltd):** `https://aictsl.in/` — Indore urban and BRTS schedules.
- **BCLL (Bhopal City Link Limited):** Bhopal urban transit lines.
- **MPSRTC (Erstwhile records and authorized private stage carriage operators):** Inter-district timetables and bus stands.

---

## 2. Data Provenance & Verification Classification

Every record in the MP Bus Saathi database carries a strict provenance tracking tag:

1. **`verified_official`:** Record matches official Transport Department or Municipal transit gazette/permit.
2. **`verified_operator`:** Directly submitted by an authorized and verified bus operator or company manager.
3. **`verified_field_survey`:** Validated by ground team or verified conductor beacon.
4. **`community_reported`:** Crowdsourced passenger submission (marked as unverified until corroboration).
5. **`pending_verification`:** New submission awaiting admin or operator review.
6. **`stale_unverified`:** Telemetry or schedule not updated within defined freshness windows.
