import 'package:flutter/material.dart';
import '../models/route_item.dart';
import '../screens/live_map_screen.dart';
import '../theme/app_theme.dart';

class ConnectingJourneyCard extends StatelessWidget {
  final ConnectingJourney journey;
  final bool isHindi;

  const ConnectingJourneyCard({
    super.key,
    required this.journey,
    required this.isHindi,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF99F6E4), width: 1.5), // Teal border
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
              color: Color(0xFFCCFBF1), // Teal 100
              borderRadius: BorderRadius.vertical(top: Radius.circular(14)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.transfer_within_a_station, size: 16, color: AppColors.teal),
                    const SizedBox(width: 6),
                    Text(
                      isHindi ? '1-बदलाव यात्रा (Connecting Transit)' : '1-Transfer Journey',
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                        color: AppColors.teal,
                      ),
                    ),
                  ],
                ),
                Text(
                  '${journey.totalDistanceKm} km • ~${journey.totalDurationMinutes} ${isHindi ? "मिनट" : "mins"}',
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF0F766E),
                  ),
                ),
              ],
            ),
          ),

          Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Leg 1
                _buildLegRow(
                  legNum: 1,
                  origin: journey.leg1.originStop,
                  destination: journey.leg1.finalDestinationStop,
                  schedules: journey.leg1.schedules,
                  routeId: journey.leg1.routeId,
                ),

                // Interchange Hub Box
                Container(
                  margin: const EdgeInsets.symmetric(vertical: 8),
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF0FDFA),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFF99F6E4)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.sync_alt, size: 16, color: AppColors.teal),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          '${isHindi ? "बदलाव जंक्शन" : "Transfer Hub"}: ${journey.interchangeName(isHindi)}',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.teal),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(6),
                          border: Border.all(color: const Color(0xFF99F6E4)),
                        ),
                        child: Text(
                          isHindi ? '~30 मि. अंतराल' : '~30m layover',
                          style: const TextStyle(fontSize: 10, color: Color(0xFF0F766E), fontWeight: FontWeight.w600),
                        ),
                      ),
                    ],
                  ),
                ),

                // Leg 2
                _buildLegRow(
                  legNum: 2,
                  origin: journey.leg2.originStop,
                  destination: journey.leg2.finalDestinationStop,
                  schedules: journey.leg2.schedules,
                  routeId: journey.leg2.routeId,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegRow({
    required int legNum,
    required String origin,
    required String destination,
    required List<ScheduleItem> schedules,
    required int routeId,
  }) {
    final firstSched = schedules.isNotEmpty ? schedules.first : null;

    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: AppColors.brandIndigo,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${isHindi ? "चरण" : "Leg"} $legNum',
                  style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  '$origin ➔ $destination',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: AppColors.textMainLight),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              if (firstSched != null)
                Builder(
                  builder: (context) {
                    return IconButton(
                      icon: const Icon(Icons.navigation, size: 16, color: AppColors.brandIndigo),
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => LiveMapScreen(
                              busId: firstSched.busId ?? 1,
                              busNumber: firstSched.busNumber,
                              scheduleId: firstSched.scheduleId,
                              routeId: routeId,
                              isHindi: isHindi,
                            ),
                          ),
                        );
                      },
                      padding: EdgeInsets.zero,
                      constraints: const BoxConstraints(),
                    );
                  }
                ),
            ],
          ),
          if (firstSched != null) ...[
            const SizedBox(height: 4),
            Text(
              '${firstSched.departureTime.substring(0, 5)} ➔ ${firstSched.arrivalTime.substring(0, 5)} • ${firstSched.busNumber} (${firstSched.operatorName})',
              style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
            ),
          ],
        ],
      ),
    );
  }
}
