import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class StatusBadge extends StatelessWidget {
  final bool isLive;
  final bool isHindi;
  final int? lastPingSecondsAgo;

  const StatusBadge({
    super.key,
    required this.isLive,
    required this.isHindi,
    this.lastPingSecondsAgo,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: isLive ? AppColors.gpsLiveBg : AppColors.gpsUnavailableBg,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: isLive ? AppColors.gpsLive.withValues(alpha: 0.3) : AppColors.gpsUnavailable.withValues(alpha: 0.3),
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 7,
            height: 7,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: isLive ? AppColors.gpsLive : AppColors.gpsUnavailable,
            ),
          ),
          const SizedBox(width: 5),
          Text(
            isLive
                ? (isHindi ? 'लाइव जीपीएस' : 'GPS Live')
                : (isHindi ? 'जीपीएस अनुपलब्ध' : 'GPS Unavailable'),
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.bold,
              color: isLive ? const Color(0xFF065F46) : const Color(0xFF92400E),
            ),
          ),
          if (isLive && lastPingSecondsAgo != null && lastPingSecondsAgo! > 0) ...[
            const SizedBox(width: 4),
            Text(
              '(${lastPingSecondsAgo}s)',
              style: const TextStyle(fontSize: 9, color: Color(0xFF065F46)),
            ),
          ],
        ],
      ),
    );
  }
}
