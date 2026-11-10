import 'package:flutter/material.dart';
import '../core/api_service.dart';
import '../core/offline_service.dart';
import '../models/bus_stop.dart';
import '../theme/app_theme.dart';
import 'live_map_screen.dart';

class SavedScreen extends StatefulWidget {
  final bool isHindi;

  const SavedScreen({super.key, required this.isHindi});

  @override
  State<SavedScreen> createState() => _SavedScreenState();
}

class _SavedScreenState extends State<SavedScreen> {
  String? _syncTime;
  List<BusStop> _cachedStops = [];
  bool _isSyncing = false;

  final List<Map<String, dynamic>> _savedRoutes = [
    {
      'code': 'BPL-IND-EXP-01',
      'from_hi': 'भोपाल नादरा बस स्टैंड',
      'from_en': 'Bhopal Nadra Bus Stand',
      'to_hi': 'इंदौर सरवटे बस स्टैंड',
      'to_en': 'Indore Sarwate Bus Stand',
      'distance': '192.5 km',
      'duration': '240 min',
      'bus_number': 'MP-04-EA-1024',
      'bus_id': 1,
      'schedule_id': 1,
      'route_id': 1,
    },
    {
      'code': 'GWL-JBL-EXP-02',
      'from_hi': 'ग्वालियर अंतर्राज्यीय बस टर्मिनल',
      'from_en': 'Gwalior ISBT',
      'to_hi': 'जबलपुर दीनदयाल चौक ISBT',
      'to_en': 'Jabalpur ISBT',
      'distance': '440 km',
      'duration': '510 min',
      'bus_number': 'MP-07-GA-1122',
      'bus_id': 3,
      'schedule_id': 3,
      'route_id': 2,
    },
    {
      'code': 'BPL-UJN-ORD-03',
      'from_hi': 'भोपाल नादरा बस स्टैंड',
      'from_en': 'Bhopal Nadra Bus Stand',
      'to_hi': 'उज्जैन नानाखेड़ा बस स्टैंड',
      'to_en': 'Ujjain Nanakheda Bus Stand',
      'distance': '188 km',
      'duration': '220 min',
      'bus_number': 'MP-04-HB-4422',
      'bus_id': 4,
      'schedule_id': 4,
      'route_id': 3,
    },
  ];

  @override
  void initState() {
    super.initState();
    _loadCacheStatus();
  }

  Future<void> _loadCacheStatus() async {
    final syncTime = await OfflineService.getLastSyncTime();
    final stops = await OfflineService.loadStops();
    if (!mounted) return;
    setState(() {
      _syncTime = syncTime;
      _cachedStops = stops;
    });
  }

  Future<void> _syncFreshData() async {
    setState(() => _isSyncing = true);
    final stops = await ApiService.getStops();
    await OfflineService.saveStops(stops);
    final syncTime = await OfflineService.getLastSyncTime();

    if (!mounted) return;
    setState(() {
      _cachedStops = stops;
      _syncTime = syncTime;
      _isSyncing = false;
    });

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          widget.isHindi
              ? 'ऑफलाइन समय-सारणी सफलतापूर्वक अपडेट की गई!'
              : 'Offline timetables successfully synced!',
        ),
        backgroundColor: AppColors.gpsLive,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.isHindi ? 'पसंदीदा एवं ऑफलाइन डेटा' : 'Saved & Offline Data',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Offline Cache Health Banner
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: const Color(0xFFEEF2FF),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFC7D2FE)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.offline_pin, color: AppColors.brandIndigo, size: 22),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          widget.isHindi
                              ? 'ग्रामीण ऑफलाइन मोड सक्रिय'
                              : 'Rural Offline Mode Active',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.primary),
                        ),
                      ),
                      if (_isSyncing)
                        const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
                      else
                        TextButton.icon(
                          onPressed: _syncFreshData,
                          icon: const Icon(Icons.sync, size: 16),
                          label: Text(widget.isHindi ? 'सिंक करें' : 'Sync'),
                          style: TextButton.styleFrom(
                            foregroundColor: AppColors.brandIndigo,
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    widget.isHindi
                        ? 'बिना मोबाइल इंटरनेट या 2G नेटवर्क में भी ${_cachedStops.length} बस स्टैंड्स और समय-सारणी आपके फोन में सुरक्षित हैं।'
                        : '${_cachedStops.length} bus stops and timetables cached offline for zero-connectivity rural travel.',
                    style: const TextStyle(fontSize: 12, color: AppColors.textMutedLight),
                  ),
                  if (_syncTime != null) ...[
                    const SizedBox(height: 6),
                    Text(
                      '${widget.isHindi ? "अंतिम सिंक समय" : "Last Synced"}: $_syncTime',
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.brandIndigo),
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Bookmarked Popular MP Routes
            Text(
              widget.isHindi ? 'पसंदीदा बस मार्ग (Saved Routes)' : 'Saved Transit Routes',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 12),
            ..._savedRoutes.map((r) => _buildSavedRouteCard(r)),
            const SizedBox(height: 16),

            // Quick Access to Cached Bus Stops
            Text(
              widget.isHindi ? 'कैश किए गए प्रमुख बस स्टैंड (${_cachedStops.length})' : 'Cached Primary Bus Stops (${_cachedStops.length})',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 12),
            Material(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: ListView.separated(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: _cachedStops.take(8).length,
                  separatorBuilder: (_, _) => const Divider(height: 1, color: Color(0xFFF1F5F9)),
                  itemBuilder: (context, idx) {
                    final stop = _cachedStops[idx];
                    return ListTile(
                      dense: true,
                      leading: const Icon(Icons.location_on, color: AppColors.brandIndigo, size: 20),
                      title: Text(
                        stop.displayName(widget.isHindi),
                        style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textMainLight),
                      ),
                      subtitle: Text(
                        '${widget.isHindi ? "जिला" : "District"}: ${stop.district}',
                        style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
                      ),
                      trailing: const Icon(Icons.chevron_right, size: 18, color: Color(0xFF94A3B8)),
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSavedRouteCard(Map<String, dynamic> r) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
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
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFFEEF2FF),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.directions_bus, color: AppColors.brandIndigo, size: 24),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.isHindi ? '${r['from_hi']} ➔ ${r['to_hi']}' : '${r['from_en']} ➔ ${r['to_en']}',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 3),
                Text(
                  '${r['code']} • ${r['distance']} • ${r['bus_number']}',
                  style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
                ),
              ],
            ),
          ),
          IconButton.filled(
            tooltip: widget.isHindi ? 'लाइव ट्रैक' : 'Track Live',
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => LiveMapScreen(
                    busId: r['bus_id'],
                    busNumber: r['bus_number'],
                    scheduleId: r['schedule_id'],
                    routeId: r['route_id'],
                    isHindi: widget.isHindi,
                  ),
                ),
              );
            },
            icon: const Icon(Icons.navigation, size: 16),
            style: IconButton.styleFrom(
              backgroundColor: AppColors.brandIndigo,
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }
}
