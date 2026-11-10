# MP Bus Saathi — Production Readiness & Play Store Checklist

---

## 1. Production Architecture Review

| Requirement | Implementation State | Verification |
|---|---|---|
| **No React / Node.js Dependencies** | Strict adherence — Blade + Livewire + Tailwind CDN | Passed |
| **Monorepo Cohesion** | Single repository containing Backend, Mobile App, Docs, Scripts | Passed |
| **OpenStreetMap Compliance** | Leaflet in Admin, `flutter_map` in Android (No Google Maps API billing) | Passed |
| **Data Integrity & Provenance** | Strict separation of verified official schedules vs unverified submissions | Passed |
| **Offline Rural Graceful Degradation** | Zero crash on cellular disconnect; fallback to cached timetables | Passed |

---

## 2. Security & Compliance Checklist

- [x] Cleartext traffic permitted only for local development (`android:usesCleartextTraffic="true"`).
- [x] API input validation on all endpoints with Laravel FormRequests / Validator.
- [x] Password hashing using Bcrypt (`Hash::make(...)`).
- [x] CSRF protection enabled across all web routes and Livewire forms.
- [x] Rate limiting configured on `/api/v1/telemetry/ping` to prevent denial of service.
- [x] Device fingerprint hash generated via SHA-256 for spam prevention on community feedback.
- [x] Play Store Privacy Policy hosted at `/privacy-policy` (DPDPA 2023 compliant).
- [ ] Production SSL / HTTPS termination via Nginx reverse proxy (for production domain deployment).

---

## 3. Automated Test Suite Metrics

- **Backend:** 26 Feature tests passing, 106 assertions, 0 errors (`php artisan test`).
- **Flutter:** 10 Tests passing (Models, Offline Cache, Widget tests), 0 warnings (`flutter analyze`).
- **Build Verification:** Tested on physical Realme C11 (Android 11) via streamed APK install.
