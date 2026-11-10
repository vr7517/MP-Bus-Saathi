import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../core/api_service.dart';
import '../models/live_status.dart';
import '../theme/app_theme.dart';
import '../widgets/status_badge.dart';
import 'verification_dialog.dart';

class LiveMapScreen extends StatefulWidget {
  final int busId;
  final String busNumber;
  final int scheduleId;
  final int? routeId;
  final bool isHindi;

  const LiveMapScreen({
    super.key,
    required this.busId,
    required this.busNumber,
    required this.scheduleId,
    this.routeId,
    required this.isHindi,
  });

  @override
  State<LiveMapScreen> createState() => _LiveMapScreenState();
}

class _LiveMapScreenState extends State<LiveMapScreen> {
  final MapController _mapController = MapController();
  LiveBusStatus? _status;
  bool _isLoading = true;
  Timer? _pollingTimer;

  // Live Bus Location (defaults to Bhopal-Indore midway near Sehore)
  LatLng _busLocation = const LatLng(23.2045, 77.0842);
  List<LatLng> _routePoints = [];

  @override
  void initState() {
    super.initState();
    _loadInitialData();
    // Poll every 6 seconds for continuous live telemetry updates
    _pollingTimer = Timer.periodic(const Duration(seconds: 6), (_) => _pollLiveStatus());
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  Future<void> _loadInitialData() async {
    // 1. Fetch Route Stops if routeId provided
    if (widget.routeId != null) {
      final stops = await ApiService.getRouteStops(widget.routeId!);
      if (mounted && stops.isNotEmpty) {
        setState(() {
          _routePoints = stops
              .where((s) => s['latitude'] != null && s['longitude'] != null)
              .map((s) => LatLng((s['latitude'] as num).toDouble(), (s['longitude'] as num).toDouble()))
              .toList();
        });
      }
    }

    // Default fallback corridor if route stops empty
    if (_routePoints.isEmpty) {
      _routePoints = const [
        LatLng(23.2642, 77.4124), // Bhopal Nadra
        LatLng(23.2798, 77.3482), // Lalghati
        LatLng(23.2005, 77.0833), // Sehore
        LatLng(23.0180, 76.7190), // Ashta
        LatLng(22.9676, 76.0534), // Dewas
        LatLng(22.7126, 75.8672), // Indore Sarwate
      ];
    }

    // 2. Fetch Live Bus Status
    await _pollLiveStatus();
  }

  Future<void> _pollLiveStatus() async {
    final status = await ApiService.getLiveStatus(widget.busId);
    if (!mounted) return;

    setState(() {
      _status = status;
      _isLoading = false;
      if (status?.latitude != null && status?.longitude != null) {
        _busLocation = LatLng(status!.latitude!, status.longitude!);
      }
    });
  }

  void _recenterMap() {
    _mapController.move(_busLocation, 12.0);
  }

  @override
  Widget build(BuildContext context) {
    final bool isLive = _status?.isLive ?? false;

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              '${widget.busNumber} • ${widget.isHindi ? "लाइव मैप" : "Live Map"}',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            Text(
              _status?.operatorName.isNotEmpty == true
                  ? _status!.operatorName
                  : (widget.isHindi ? 'मध्य प्रदेश राज्य परिवहन' : 'MP State Transit'),
              style: const TextStyle(fontSize: 11, color: Color(0xFFC7D2FE)),
            ),
          ],
        ),
        actions: [
          IconButton(
            tooltip: widget.isHindi ? 'रिफ्रेश' : 'Refresh',
            icon: const Icon(Icons.refresh),
            onPressed: _pollLiveStatus,
          ),
          IconButton(
            tooltip: widget.isHindi ? 'सत्यापन करें' : 'Verify Timetable',
            icon: const Icon(Icons.verified_outlined),
            onPressed: () {
              showDialog(
                context: context,
                builder: (_) => VerificationDialog(
                  scheduleId: widget.scheduleId,
                  busStopId: _status?.nextStop?.stopId ?? 1,
                  isHindi: widget.isHindi,
                ),
              );
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.brandIndigo))
          : Stack(
              children: [
                // OpenStreetMap Render
                FlutterMap(
                  mapController: _mapController,
                  options: MapOptions(
                    initialCenter: _busLocation,
                    initialZoom: 11.5,
                  ),
                  children: [
                    TileLayer(
                      urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                      userAgentPackageName: 'com.mpbussaathi.mp_bus_saathi',
                    ),
                    // Route Polyline
                    PolylineLayer(
                      polylines: [
                        Polyline(
                          points: _routePoints,
                          strokeWidth: 4.5,
                          color: AppColors.brandIndigo.withValues(alpha: 0.85),
                        ),
                      ],
                    ),
                    // Markers Layer
                    MarkerLayer(
                      markers: [
                        // Bus Stop Markers along route
                        ..._routePoints.map((pt) => Marker(
                              point: pt,
                              width: 16,
                              height: 16,
                              child: Container(
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: AppColors.brandIndigo, width: 3),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withValues(alpha: 0.15),
                                      blurRadius: 4,
                                    ),
                                  ],
                                ),
                              ),
                            )),
                        // Live Moving Bus Marker
                        Marker(
                          point: _busLocation,
                          width: 58,
                          height: 58,
                          child: Column(
                            children: [
                              Container(
                                width: 44,
                                height: 44,
                                decoration: BoxDecoration(
                                  color: isLive ? AppColors.gpsLive : AppColors.gpsUnavailable,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: Colors.white, width: 3),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withValues(alpha: 0.3),
                                      blurRadius: 8,
                                      offset: const Offset(0, 3),
                                    ),
                                  ],
                                ),
                                child: const Center(
                                  child: Icon(Icons.directions_bus, color: Colors.white, size: 24),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),

                // Top Floating Status Banner
                Positioned(
                  top: 14,
                  left: 14,
                  right: 14,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.1),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        ),
                      ],
                      border: Border.all(
                        color: isLive ? AppColors.gpsLive : AppColors.gpsUnavailable,
                        width: 1.5,
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 10,
                          height: 10,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: isLive ? AppColors.gpsLive : AppColors.gpsUnavailable,
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Text(
                                isLive
                                    ? (widget.isHindi ? '🟢 लाइव जीपीएस ट्रैकिंग चालू' : '🟢 Live GPS Tracking Active')
                                    : (widget.isHindi ? '🟡 जीपीएस सिग्नल प्रतीक्षारत (समय-सारणी)' : '🟡 GPS Signal Waiting (Scheduled)'),
                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight),
                              ),
                              if (isLive && _status?.speedKmh != null)
                                Text(
                                  '${widget.isHindi ? "गति" : "Speed"}: ${_status!.speedKmh.toStringAsFixed(1)} km/h • ${widget.isHindi ? "अंतिम पिंग" : "Last ping"}: ${_status!.lastPingSecondsAgo ?? 0}s ${widget.isHindi ? "पहले" : "ago"}',
                                  style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
                                ),
                            ],
                          ),
                        ),
                        StatusBadge(
                          isLive: isLive,
                          isHindi: widget.isHindi,
                          lastPingSecondsAgo: _status?.lastPingSecondsAgo,
                        ),
                      ],
                    ),
                  ),
                ),

                // Recenter & Zoom Controls
                Positioned(
                  right: 14,
                  bottom: 140,
                  child: Column(
                    children: [
                      FloatingActionButton.small(
                        heroTag: 'recenter_btn',
                        backgroundColor: Colors.white,
                        foregroundColor: AppColors.brandIndigo,
                        onPressed: _recenterMap,
                        child: const Icon(Icons.my_location),
                      ),
                      const SizedBox(height: 8),
                      FloatingActionButton.small(
                        heroTag: 'zoom_in_btn',
                        backgroundColor: Colors.white,
                        foregroundColor: AppColors.textMainLight,
                        onPressed: () {
                          _mapController.move(_mapController.camera.center, _mapController.camera.zoom + 1);
                        },
                        child: const Icon(Icons.add),
                      ),
                      const SizedBox(height: 8),
                      FloatingActionButton.small(
                        heroTag: 'zoom_out_btn',
                        backgroundColor: Colors.white,
                        foregroundColor: AppColors.textMainLight,
                        onPressed: () {
                          _mapController.move(_mapController.camera.center, _mapController.camera.zoom - 1);
                        },
                        child: const Icon(Icons.remove),
                      ),
                    ],
                  ),
                ),

                // Bottom Dynamic ETA Card
                Positioned(
                  left: 14,
                  right: 14,
                  bottom: 16,
                  child: Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.12),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              widget.isHindi ? 'अगला पड़ाव (Next Stop):' : 'Next Approaching Stop:',
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textMutedLight),
                            ),
                            if (_status?.nextStop != null)
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFEEF2FF),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Text(
                                  '~${_status!.nextStop!.etaMinutes} ${widget.isHindi ? "मिनट" : "mins"} ETA',
                                  style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.brandIndigo),
                                ),
                              ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          _status?.nextStop != null
                              ? (_status!.nextStop!.displayName(widget.isHindi))
                              : (widget.isHindi ? 'मार्ग के अनुसार निर्धारित स्टॉप' : 'Scheduled Stop along Route'),
                          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppColors.textMainLight),
                        ),
                        if (_status?.nextStop != null) ...[
                          const SizedBox(height: 4),
                          Text(
                            '${widget.isHindi ? "दूरी" : "Distance"}: ${_status!.nextStop!.distanceKm.toStringAsFixed(1)} km',
                            style: const TextStyle(fontSize: 12, color: AppColors.textMutedLight),
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}
