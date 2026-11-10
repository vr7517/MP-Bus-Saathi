# MP Bus Saathi — Flutter Design System & Tokens

## 1. Brand Identity & Visual Language
- **Identity:** Madhya Pradesh Public Transit (मध्य प्रदेश लोक परिवहन)
- **Design Philosophy:** Modern, minimal, accessible, high-contrast, optimized for low-end Android hardware and poor network conditions in rural MP.

---

## 2. Color Palette & Semantic Tokens

### Primary & Secondary
- **Primary Navy (`AppColors.primary`):** `#1E1B4B` (Deep Indigo / Navy) — Trustworthy, authoritative, government-grade.
- **Brand Indigo (`AppColors.indigo`):** `#4338CA` — Active buttons, selected chips, focus rings.
- **Secondary Teal (`AppColors.teal`):** `#0D9488` — Interchange transfers, connecting routes, eco/electric buses.
- **Accent Amber (`AppColors.amber`):** `#D97706` — Scheduled/untracked buses, warnings, intermediate stops.

### Status Indicators
- **GPS Live (`AppColors.gpsLive`):** `#059669` (Green 600) / Surface: `#D1FAE5` (Green 100)
- **GPS Unavailable (`AppColors.gpsUnavailable`):** `#D97706` (Amber 600) / Surface: `#FEF3C7` (Amber 100)
- **Cancelled / Disrupted (`AppColors.error`):** `#DC2626` (Red 600) / Surface: `#FEE2E2` (Red 100)
- **Delayed (`AppColors.warning`):** `#EA580C` (Orange 600) / Surface: `#FFEDD5` (Orange 100)

### Backgrounds & Surfaces
- **Light Scaffold (`AppColors.backgroundLight`):** `#F8FAFC` (Slate 50)
- **Light Surface (`AppColors.surfaceLight`):** `#FFFFFF` (White)
- **Light Border (`AppColors.borderLight`):** `#E2E8F0` (Slate 200)
- **Dark Scaffold (`AppColors.backgroundDark`):** `#0F172A` (Slate 900)
- **Dark Surface (`AppColors.surfaceDark`):** `#1E293B` (Slate 800)
- **Dark Border (`AppColors.borderDark`):** `#334155` (Slate 700)

---

## 3. Typography & Bilingual Standards
- **Font Family:** `Noto Sans Devanagari` & System sans-serif for universal rendering on Indian Android devices.
- **Scale:**
  - `Display:` 24sp / Bold (Brand Title & Headers)
  - `Headline:` 18sp / SemiBold (Screen Titles)
  - `Title:` 15sp / SemiBold (Card Titles, Stop Names)
  - `Body:` 13sp / Regular (Timetables, Operators, Notes)
  - `Label:` 11sp / Medium (Badges, Chips, Distance metrics)
  - `Caption:` 10sp / Regular (Sync timestamp, Disclaimers)

---

## 4. Reusable Component Standards
1. **StatusBadge:** Pill badge strictly enforcing the 180s GPS freshness rule (Live 🟢 vs Unavailable 🟡).
2. **RouteCard:** Clean card displaying Origin -> Destination, Departure, Arrival, Bus Operator, Bus Category, and Map Action.
3. **ConnectingJourneyCard:** Displays Leg 1 + Interchange Stop + Leg 2 for rural village connecting trips.
4. **BottomNavigationShell:** 5 tab bar with clear Hindi and English labels and haptic feedback.
5. **InteractiveMapController:** Full-screen OpenStreetMap with custom bus icon, heading orientation, and route polylines.
