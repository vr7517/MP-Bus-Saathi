import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../core/api_service.dart';
import '../models/live_status.dart';
import 'verification_dialog.dart';

class LiveMapScreen extends StatefulWidget {
  final int busId;
  final String busNumber;
  final int scheduleId;
  final bool isHindi;

  const LiveMapScreen({
    super.key,
    required this.busId,
    required this.busNumber,
    required this.scheduleId,
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

  // Default coordinate: Bhopal-Indore midway near Sehore
  LatLng _busLocation = const LatLng(23.2045, 77.0842);

  // Highway waypoints for route polyline
  final List<LatLng> _routePoints = const [
    LatLng(23.2642, 77.4124), // Bhopal Nadra
    LatLng(23.2798, 77.3482), // Halalpur
    LatLng(23.2005, 77.0833), // Sehore
    LatLng(23.0180, 76.7190), // Ashta
    LatLng(22.9676, 76.0534), // Dewas
    LatLng(22.7126, 75.8672), // Indore Sarwate
  ];

  @override
  void initState() {
    super.initState();
    _fetchLiveStatus();
    // Poll every 10 seconds for real-time live map updates
    _pollingTimer = Timer.periodic(const Duration(seconds: 10), (_) => _fetchLiveStatus());
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  Future<void> _fetchLiveStatus() async {
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

  @override
  Widget build(BuildContext context) {
    final bool isLive = _status?.isLive ?? false;

    return Scaffold(
      appBar: AppBar(
        title: Text(
          '${widget.busNumber} • ${widget.isHindi ? 'लाइव ट्रैकिंग' : 'Live Tracking'}',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        backgroundColor: const Color(0xFF1E1B4B),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchLiveStatus,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF4338CA)))
          : Stack(
        children: [
          // OpenStreetMap Render using flutter_map
          FlutterMap(
            mapController: _mapController,
            options: MapOptions(
              initialCenter: _busLocation,
              initialZoom: 10.5,
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.mpbussaathi.mp_bus_saathi',
              ),
              PolylineLayer(
                polylines: [
                  Polyline(
                    points: _routePoints,
                    strokeWidth: 4.5,
                    color: const Color(0xFF4338CA).withValues(alpha: 0.85),
                  ),
                ],
              ),
              MarkerLayer(
                markers: [
                  // Bus Stops Markers
                  ..._routePoints.map((pt) => Marker(
                    point: pt,
                    width: 14,
                    height: 14,
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: const Color(0xFF4338CA), width: 3),
                      ),
                    ),
                  )),
                  // Moving Bus Marker
                  Marker(
                    point: _busLocation,
                    width: 52,
                    height: 52,
                    child: Container(
                      decoration: BoxDecoration(
                        color: isLive ? const Color(0xFF059669) : const Color(0xFFD97706),
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
                        child: Icon(Icons.directions_bus, color: Colors.white, size: 28),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),

          // Top Prominent Status Badge (GPS Live vs GPS Unavailable)
          Positioned(
            top: 12,
            left: 14,
            right: 14,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
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
                border: Border.all(
                  color: isLive ? const Color(0xFF10B981) : const Color(0xFFF59E0B),
                  width: 2,
                ),
              ),
              child: Row(
                children: [
                  Container(
                    width: 12,
                    height: 12,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: isLive ? const Color(0xFF059669) : const Color(0xFFD97706),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          isLive
                              ? (widget.isHindi ? '🟢 लाइव जीपीएस ट्रैकिंग चालू' : '🟢 Live GPS Tracking Active')
                              : (widget.isHindi ? '🟡 जीपीएस अनुपलब्ध - निर्धारित समय-सारणी' : '🟡 GPS Unavailable - Scheduled Timetable'),
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: isLive ? const Color(0xFF065F46) : const Color(0xFF92400E),
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          isLive
                              ? '${widget.isHindi ? 'गति' : 'Speed'}: ${_status?.speedKmh.toStringAsFixed(1) ?? '55'} km/h • ${widget.isHindi ? 'अंतिम पिंग' : 'Last Ping'}: ${_status?.lastPingSecondsAgo ?? 45}s ago'
                              : (widget.isHindi
                                  ? 'बस निर्धारित समय-सारणी के अनुसार चल रही है।'
                                  : 'Bus is moving according to published timetable.'),
                          style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Bottom Sheet Card: Next Stop & ETA
          Positioned(
            bottom: 20,
            left: 14,
            right: 14,
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(18),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.15),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            widget.isHindi ? 'अगला बस स्टॉप (Next Stop)' : 'Next Bus Stop',
                            style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), fontWeight: FontWeight.w600),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            _status?.nextStop != null
                                ? (widget.isHindi ? _status!.nextStop!.nameHi : _status!.nextStop!.nameEn)
                                : (widget.isHindi ? 'सीहोर मुख्य बस स्टैंड' : 'Sehore Main Bus Stand'),
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEEF2FF),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          children: [
                            Text(
                              '${_status?.nextStop?.etaMinutes ?? 14} min',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF4338CA)),
                            ),
                            Text(
                              widget.isHindi ? 'अनुमानित समय' : 'Est. Arrival',
                              style: const TextStyle(fontSize: 9, color: Color(0xFF6366F1), fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 24),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () {
                            showModalBottomSheet(
                              context: context,
                              isScrollControlled: true,
                              builder: (_) => VerificationDialog(
                                scheduleId: widget.scheduleId,
                                busStopId: _status?.nextStop?.stopId ?? 4,
                                isHindi: widget.isHindi,
                              ),
                            );
                          },
                          icon: const Icon(Icons.rate_review_outlined, size: 18),
                          label: Text(
                            widget.isHindi ? 'समय सत्यापित करें' : 'Verify Timetable',
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                          ),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF4338CA),
                            side: const BorderSide(color: Color(0xFFC7D2FE)),
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      IconButton.filled(
                        onPressed: () {
                          _mapController.move(_busLocation, 12);
                        },
                        icon: const Icon(Icons.my_location, size: 20),
                        style: IconButton.styleFrom(
                          backgroundColor: const Color(0xFF1E1B4B),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

