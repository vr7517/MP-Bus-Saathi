import 'dart:async';
import 'package:flutter/material.dart';
import '../core/api_service.dart';

class DriverModeScreen extends StatefulWidget {
  final bool isHindi;

  const DriverModeScreen({super.key, this.isHindi = true});

  @override
  State<DriverModeScreen> createState() => _DriverModeScreenState();
}

class _DriverModeScreenState extends State<DriverModeScreen> {
  bool _isTracking = false;
  int _selectedBusId = 1;
  String _selectedBusNumber = 'MP-04-EA-1024';
  int _selectedScheduleId = 1;
  String _selectedRouteName = 'BPL-IND-EXP-01 (भोपाल → इंदौर)';

  int _pingCount = 0;
  double _currentSpeed = 48.0;
  double _currentLat = 23.2642;
  double _currentLng = 77.4124;
  Timer? _telemetryTimer;
  String _statusLog = '';

  final List<Map<String, dynamic>> _buses = [
    {'id': 1, 'number': 'MP-04-EA-1024', 'operator': 'MPSRTC', 'type': 'Express'},
    {'id': 2, 'number': 'MP-09-FA-8899', 'operator': 'AiCTSL', 'type': 'AC'},
    {'id': 3, 'number': 'MP-04-HB-4422', 'operator': 'Hans Travels', 'type': 'Ordinary'},
  ];

  final List<Map<String, dynamic>> _schedules = [
    {'id': 1, 'code': 'BPL-IND-EXP-01', 'name': 'भोपाल (नादरा) → इंदौर (सरवटे)', 'time': '07:00'},
    {'id': 2, 'code': 'IND-UJN-ORD-01', 'name': 'इंदौर (सरवटे) → उज्जैन (नानाखेड़ा)', 'time': '12:00'},
  ];

  @override
  void dispose() {
    _telemetryTimer?.cancel();
    super.dispose();
  }

  void _toggleTracking() {
    if (_isTracking) {
      _stopTracking();
    } else {
      _startTracking();
    }
  }

  void _startTracking() {
    setState(() {
      _isTracking = true;
      _pingCount = 0;
      _statusLog = widget.isHindi
          ? 'लाइव जीपीएस ट्रांसमिशन शुरू हुआ...'
          : 'Live GPS transmission started...';
    });

    _emitPing();

    _telemetryTimer = Timer.periodic(const Duration(seconds: 5), (_) {
      _emitPing();
    });
  }

  void _stopTracking() {
    _telemetryTimer?.cancel();
    setState(() {
      _isTracking = false;
      _statusLog = widget.isHindi
          ? 'जीपीएस ट्रैकिंग रोकी गई।'
          : 'GPS tracking stopped.';
    });
  }

  Future<void> _emitPing() async {
    // Simulate slight movement along corridor
    setState(() {
      _currentLat -= 0.0012;
      _currentLng -= 0.0025;
      _currentSpeed = 45.0 + (_pingCount % 15);
    });

    final success = await ApiService.sendTelemetry(
      busId: _selectedBusId,
      latitude: _currentLat,
      longitude: _currentLng,
      speedKmh: _currentSpeed,
      heading: 240.0,
      activeScheduleId: _selectedScheduleId,
    );

    if (!mounted) return;

    setState(() {
      if (success) {
        _pingCount++;
        _statusLog = widget.isHindi
            ? 'पिंग #$_pingCount सफल (गति: ${_currentSpeed.toStringAsFixed(1)} km/h)'
            : 'Ping #$_pingCount OK (Speed: ${_currentSpeed.toStringAsFixed(1)} km/h)';
      } else {
        _statusLog = widget.isHindi
            ? 'पिंग असफल - ऑफ़लाइन कतार में सहेजा गया'
            : 'Ping failed - saved in offline queue';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final bool hi = widget.isHindi;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E1B4B),
        foregroundColor: Colors.white,
        title: Text(
          hi ? 'चालक / परिचालक मोड' : 'Driver / Conductor Mode',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Status Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: _isTracking
                      ? [const Color(0xFF065F46), const Color(0xFF047857)]
                      : [const Color(0xFF1E1B4B), const Color(0xFF312E81)],
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.15),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 12,
                            height: 12,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: _isTracking ? const Color(0xFF34D399) : const Color(0xFF94A3B8),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            _isTracking
                                ? (hi ? '🟢 लाइव जीपीएस चालू' : '🟢 Live GPS Active')
                                : (hi ? '⚪ ट्रैकिंग बंद' : '⚪ Tracking Inactive'),
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          '${hi ? 'पिंग्स' : 'Pings'}: $_pingCount',
                          style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  Text(
                    _isTracking ? '${_currentSpeed.toStringAsFixed(0)} km/h' : '0 km/h',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 48,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1,
                    ),
                  ),
                  Text(
                    hi ? 'अनुमानित बस गति (Speed)' : 'Estimated Bus Speed',
                    style: TextStyle(color: Colors.white.withValues(alpha: 0.8), fontSize: 12),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    '$_selectedBusNumber • $_selectedRouteName',
                    style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 11, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 12),
                  if (_statusLog.isNotEmpty)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: Colors.black.withValues(alpha: 0.25),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        _statusLog,
                        style: const TextStyle(color: Color(0xFFE2E8F0), fontSize: 11),
                        textAlign: TextAlign.center,
                      ),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Bus Selection Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    hi ? 'बस एवं ऑपरेटर चुनें' : 'Select Bus & Operator',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF0F172A)),
                  ),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<int>(
                    initialValue: _selectedBusId,
                    decoration: InputDecoration(
                      labelText: hi ? 'बस नंबर' : 'Bus Number',
                      prefixIcon: const Icon(Icons.directions_bus, color: Color(0xFF4338CA)),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    ),
                    items: _buses.map((b) {
                      return DropdownMenuItem<int>(
                        value: b['id'] as int,
                        child: Text('${b['number']} (${b['operator']})'),
                      );
                    }).toList(),
                    onChanged: _isTracking
                        ? null
                        : (val) {
                            if (val != null) {
                              final bus = _buses.firstWhere((b) => b['id'] == val);
                              setState(() {
                                _selectedBusId = val;
                                _selectedBusNumber = bus['number'];
                              });
                            }
                          },
                  ),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<int>(
                    initialValue: _selectedScheduleId,
                    decoration: InputDecoration(
                      labelText: hi ? 'मार्ग / शेड्यूल' : 'Route / Schedule',
                      prefixIcon: const Icon(Icons.alt_route, color: Color(0xFF4338CA)),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    ),
                    items: _schedules.map((s) {
                      return DropdownMenuItem<int>(
                        value: s['id'] as int,
                        child: Text('${s['code']} - ${s['time']}'),
                      );
                    }).toList(),
                    onChanged: _isTracking
                        ? null
                        : (val) {
                            if (val != null) {
                              final sch = _schedules.firstWhere((s) => s['id'] == val);
                              setState(() {
                                _selectedScheduleId = val;
                                _selectedRouteName = sch['name'];
                              });
                            }
                          },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Start / Stop Tracking Big Button
            SizedBox(
              width: double.infinity,
              height: 56,
              child: ElevatedButton.icon(
                onPressed: _toggleTracking,
                icon: Icon(_isTracking ? Icons.stop_circle : Icons.play_circle_fill, size: 28),
                label: Text(
                  _isTracking
                      ? (hi ? 'ट्रैकिंग समाप्त करें (Stop Tracking)' : 'Stop Live Tracking')
                      : (hi ? 'लाइव ट्रैकिंग शुरू करें (Start Live Tracking)' : 'Start Live Tracking'),
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: _isTracking ? const Color(0xFFDC2626) : const Color(0xFF059669),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  elevation: 3,
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Help info
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFEFF6FF),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFBFDBFE)),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('ℹ️', style: TextStyle(fontSize: 18)),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      hi
                          ? 'यह मोड ग्रामीण व निजी बस चालकों/परिचालकों को अपने स्मार्टफोन के जरिए बिना अतिरिक्त जीपीएस हार्डवेयर के यात्रियों को लाइव लोकेशन प्रदान करने की सुविधा देता है।'
                          : 'This mode allows drivers and conductors to broadcast live location to passengers using their smartphone, without requiring costly AIS-140 hardware.',
                      style: const TextStyle(fontSize: 11, color: Color(0xFF1E40AF), height: 1.4),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
