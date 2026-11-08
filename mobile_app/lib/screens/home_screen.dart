import 'package:flutter/material.dart';
import '../core/api_service.dart';
import '../core/offline_service.dart';
import '../models/bus_stop.dart';
import '../models/route_item.dart';
import 'live_map_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  bool _isHindi = true;
  List<BusStop> _stops = [];
  BusStop? _sourceStop;
  BusStop? _destinationStop;
  bool _isLoadingStops = true;
  bool _isSearching = false;
  List<RouteSearchResult> _searchResults = [];
  String? _lastSyncTime;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  Future<void> _loadInitialData() async {
    final stops = await ApiService.getStops();
    final syncTime = await OfflineService.getLastSyncTime();

    if (!mounted) return;
    setState(() {
      _stops = stops;
      _isLoadingStops = false;
      _lastSyncTime = syncTime;

      // Default select Bhopal Nadra and Indore Sarwate for instant demo preview
      if (stops.isNotEmpty) {
        _sourceStop = stops.firstWhere((s) => s.id == 1, orElse: () => stops.first);
        _destinationStop = stops.firstWhere((s) => s.id == 7, orElse: () => stops.last);
      }
    });

    if (_sourceStop != null && _destinationStop != null) {
      _performSearch();
    }
  }

  Future<void> _performSearch() async {
    if (_sourceStop == null || _destinationStop == null) return;

    setState(() => _isSearching = true);
    final results = await ApiService.searchRoutes(_sourceStop!.id, _destinationStop!.id);

    if (!mounted) return;
    setState(() {
      _searchResults = results;
      _isSearching = false;
    });
  }

  void _swapStops() {
    setState(() {
      final temp = _sourceStop;
      _sourceStop = _destinationStop;
      _destinationStop = temp;
    });
    _performSearch();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              _isHindi ? 'मध्य प्रदेश बस साथी' : 'MP Bus Saathi',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
            ),
            Text(
              _isHindi ? 'ऑफलाइन फ्रेंडली बस ट्रैकिंग' : 'Offline-Friendly Bus Transit',
              style: const TextStyle(fontSize: 11, color: Color(0xFFC7D2FE)),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF1E1B4B),
        foregroundColor: Colors.white,
        actions: [
          // Language Switcher Toggle
          TextButton.icon(
            onPressed: () => setState(() => _isHindi = !_isHindi),
            icon: const Icon(Icons.translate, color: Colors.white, size: 16),
            label: Text(
              _isHindi ? 'English' : 'हिन्दी',
              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
            ),
          ),
        ],
      ),
      body: _isLoadingStops
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF4338CA)))
          : RefreshIndicator(
              onRefresh: _performSearch,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Offline Cache Status Bar
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEEF2FF),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFC7D2FE)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.offline_pin, color: Color(0xFF4338CA), size: 18),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              _lastSyncTime != null
                                  ? (_isHindi ? 'ऑफलाइन डेटा सिंक किया गया • बिना इंटरनेट के भी चालू' : 'Offline Data Cached • Works without internet')
                                  : (_isHindi ? 'ऑफलाइन मोड उपलब्ध: बिना इंटरनेट के भी समय-सारणी उपलब्ध' : 'Offline Ready: Timetables accessible without internet'),
                              style: const TextStyle(fontSize: 11, color: Color(0xFF312E81), fontWeight: FontWeight.w600),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Search Card
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.06),
                            blurRadius: 10,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _isHindi ? 'बस खोजें (Route Finder)' : 'Find Routes',
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF0F172A)),
                          ),
                          const SizedBox(height: 14),

                          // Source Dropdown
                          _buildStopDropdown(
                            label: _isHindi ? 'कहाँ से (Source)' : 'From (Origin)',
                            icon: Icons.trip_origin,
                            value: _sourceStop,
                            onChanged: (val) {
                              setState(() => _sourceStop = val);
                              _performSearch();
                            },
                          ),
                          const SizedBox(height: 10),

                          // Swap Button Center
                          Center(
                            child: IconButton(
                              onPressed: _swapStops,
                              icon: const Icon(Icons.swap_vert, color: Color(0xFF4338CA)),
                              style: IconButton.styleFrom(
                                backgroundColor: const Color(0xFFEEF2FF),
                                padding: const EdgeInsets.all(6),
                              ),
                            ),
                          ),
                          const SizedBox(height: 2),

                          // Destination Dropdown
                          _buildStopDropdown(
                            label: _isHindi ? 'कहाँ तक (Destination)' : 'To (Destination)',
                            icon: Icons.location_on,
                            value: _destinationStop,
                            onChanged: (val) {
                              setState(() => _destinationStop = val);
                              _performSearch();
                            },
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Search Results Header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          _isHindi ? 'उपलब्ध बसें व समय-सारणी' : 'Available Buses & Timetable',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
                        ),
                        if (_isSearching)
                          const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Results List
                    if (_searchResults.isEmpty && !_isSearching)
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Center(
                          child: Text(
                            _isHindi
                                ? 'इस मार्ग पर कोई सीधी बस नहीं मिली।'
                                : 'No direct buses found for this route.',
                            style: const TextStyle(color: Color(0xFF64748B), fontSize: 13),
                          ),
                        ),
                      )
                    else
                      ..._searchResults.map((r) => _buildRouteCard(r)),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStopDropdown({
    required String label,
    required IconData icon,
    required BusStop? value,
    required ValueChanged<BusStop?> onChanged,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<BusStop>(
          isExpanded: true,
          value: value,
          hint: Text(label, style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8))),
          icon: const Icon(Icons.arrow_drop_down, color: Color(0xFF4338CA)),
          items: _stops.map((s) {
            return DropdownMenuItem<BusStop>(
              value: s,
              child: Row(
                children: [
                  Icon(icon, size: 16, color: const Color(0xFF4338CA)),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      s.displayName(_isHindi),
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF1E293B)),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            );
          }).toList(),
          onChanged: onChanged,
        ),
      ),
    );
  }

  Widget _buildRouteCard(RouteSearchResult route) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Route Header
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: const BoxDecoration(
              color: Color(0xFFF1F5F9),
              borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: const Color(0xFF1E1B4B),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          route.routeCode,
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 11),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Flexible(
                        child: Text(
                          '${route.travelDistanceKm} km • ~${route.estimatedDurationMinutes} ${_isHindi ? 'मिनट' : 'mins'}',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Color(0xFF475569)),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 6),
                Text(
                  '${route.stopsInSegment} ${_isHindi ? 'स्टॉप' : 'stops'}',
                  style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                ),
              ],
            ),
          ),

          // Schedules inside Route
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: route.schedules.length,
            separatorBuilder: (_, _) => const Divider(height: 1, indent: 16, endIndent: 16),
            itemBuilder: (context, idx) {
              final s = route.schedules[idx];
              final bool isLive = s.isGpsLive;

              return Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    // Departure & Arrival Time Box
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          s.departureTime.length >= 5 ? s.departureTime.substring(0, 5) : s.departureTime,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 20, color: Color(0xFF0F172A)),
                        ),
                        Text(
                          '${_isHindi ? 'आगमन' : 'Arrival'}: ${s.arrivalTime.length >= 5 ? s.arrivalTime.substring(0, 5) : s.arrivalTime}',
                          style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                        ),
                      ],
                    ),
                    const SizedBox(width: 16),

                    // Operator & Status
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            s.busNumber,
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Color(0xFF1E293B)),
                          ),
                          Text(
                            '${s.operatorName} • ${s.busType.toUpperCase()}',
                            style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 6),

                          // GPS Live vs GPS Unavailable Badge
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: isLive ? const Color(0xFFD1FAE5) : const Color(0xFFFEF3C7),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Container(
                                  width: 6,
                                  height: 6,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: isLive ? const Color(0xFF059669) : const Color(0xFFD97706),
                                  ),
                                ),
                                const SizedBox(width: 5),
                                Text(
                                  isLive ? 'GPS Live' : 'GPS Unavailable',
                                  style: TextStyle(
                                    fontSize: 10,
                                    fontWeight: FontWeight.bold,
                                    color: isLive ? const Color(0xFF065F46) : const Color(0xFF92400E),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),

                    // Live Track Button
                    IconButton.filled(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => LiveMapScreen(
                              busId: s.busId ?? 1,
                              busNumber: s.busNumber,
                              scheduleId: s.scheduleId,
                              isHindi: _isHindi,
                            ),
                          ),
                        );
                      },
                      icon: const Icon(Icons.map, size: 20),
                      style: IconButton.styleFrom(
                        backgroundColor: const Color(0xFF4338CA),
                        foregroundColor: Colors.white,
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}

