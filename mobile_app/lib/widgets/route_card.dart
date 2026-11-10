import 'package:flutter/material.dart';
import '../models/route_item.dart';
import '../screens/live_map_screen.dart';
import '../screens/verification_dialog.dart';
import '../theme/app_theme.dart';
import 'status_badge.dart';

class RouteCard extends StatelessWidget {
  final RouteSearchResult route;
  final bool isHindi;
  final VoidCallback? onTap;

  const RouteCard({
    super.key,
    required this.route,
    required this.isHindi,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.borderLight),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
            decoration: const BoxDecoration(
              color: Color(0xFFF1F5F9),
              borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.alt_route, size: 16, color: AppColors.brandIndigo),
                    const SizedBox(width: 6),
                    Text(
                      route.routeCode,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                        color: AppColors.brandIndigo,
                      ),
                    ),
                  ],
                ),
                Text(
                  '${route.travelDistanceKm} km • ~${route.estimatedDurationMinutes} ${isHindi ? "मिनट" : "mins"}',
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textMutedLight,
                  ),
                ),
              ],
            ),
          ),

          // Route Body
          Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Origin to Destination Path
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Column(
                      children: [
                        const Icon(Icons.radio_button_checked, size: 14, color: AppColors.brandIndigo),
                        Container(width: 2, height: 16, color: const Color(0xFFCBD5E1)),
                        const Icon(Icons.location_on, size: 14, color: Color(0xFFDC2626)),
                      ],
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            route.originStop,
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight),
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 8),
                          Text(
                            route.finalDestinationStop,
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                const Divider(height: 1, color: Color(0xFFF1F5F9)),
                const SizedBox(height: 10),

                // Schedules List
                Text(
                  isHindi ? 'दैनिक समय-सारणी एवं बसें:' : 'Daily Timetable & Buses:',
                  style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textMutedLight),
                ),
                const SizedBox(height: 8),
                ...route.schedules.map((s) => _buildScheduleRow(context, s)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildScheduleRow(BuildContext context, ScheduleItem s) {
    final bool isLive = s.isGpsLive;

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          // Times
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${s.departureTime.length >= 5 ? s.departureTime.substring(0, 5) : s.departureTime} ➔ ${s.arrivalTime.length >= 5 ? s.arrivalTime.substring(0, 5) : s.arrivalTime}',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight),
                ),
                const SizedBox(height: 2),
                Text(
                  '${s.busNumber} • ${s.operatorName}',
                  style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),

          // Status Badge
          StatusBadge(isLive: isLive, isHindi: isHindi),
          const SizedBox(width: 8),

          // Action Menu (Live Map & Verify)
          IconButton(
            tooltip: isHindi ? 'सत्यापित करें' : 'Verify Timetable',
            icon: const Icon(Icons.verified_outlined, size: 18, color: Color(0xFF475569)),
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(),
            onPressed: () {
              showDialog(
                context: context,
                builder: (_) => VerificationDialog(
                  scheduleId: s.scheduleId,
                  busStopId: 1,
                  isHindi: isHindi,
                ),
              );
            },
          ),
          const SizedBox(width: 8),

          IconButton.filled(
            tooltip: isHindi ? 'लाइव ट्रैक' : 'Track Bus',
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => LiveMapScreen(
                    busId: s.busId ?? 1,
                    busNumber: s.busNumber,
                    scheduleId: s.scheduleId,
                    routeId: route.routeId,
                    isHindi: isHindi,
                  ),
                ),
              );
            },
            icon: const Icon(Icons.navigation, size: 16),
            style: IconButton.styleFrom(
              backgroundColor: AppColors.brandIndigo,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.all(8),
              minimumSize: const Size(32, 32),
            ),
          ),
        ],
      ),
    );
  }
}
