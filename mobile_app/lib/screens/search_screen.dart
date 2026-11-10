import 'package:flutter/material.dart';
import '../core/api_service.dart';
import '../models/bus_stop.dart';
import '../models/district.dart';
import '../models/route_item.dart';
import '../theme/app_theme.dart';
import '../widgets/connecting_journey_card.dart';
import '../widgets/route_card.dart';

class SearchScreen extends StatefulWidget {
  final bool isHindi;

  const SearchScreen({super.key, required this.isHindi});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  List<BusStop> _stops = [];
  List<District> _districts = [];
  District? _selectedDistrict;
  BusStop? _sourceStop;
  BusStop? _destinationStop;
  String _selectedBusType = '';
  bool _isLoading = true;
  bool _isSearching = false;
  SearchResultContainer? _results;

  List<District> get _districtsWithStops {
    if (_districts.isEmpty) return [];
    final activeNames = _stops.map((s) => s.district.toLowerCase()).toSet();
    final withStops = _districts.where((d) => activeNames.contains(d.nameEn.toLowerCase())).toList();
    return withStops.isNotEmpty ? withStops : _districts.take(10).toList();
  }

  List<BusStop> get _displayedStops {
    if (_selectedDistrict == null) return _stops;
    final inDist = _stops.where((s) => s.district.toLowerCase() == _selectedDistrict!.nameEn.toLowerCase()).toList();
    if (inDist.isEmpty) return _stops;
    final set = Set<BusStop>.from(inDist);
    if (_sourceStop != null) set.add(_sourceStop!);
    if (_destinationStop != null) set.add(_destinationStop!);
    return set.toList();
  }

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final results = await Future.wait([
      ApiService.getStops(),
      ApiService.getDistricts(),
    ]);

    if (!mounted) return;
    setState(() {
      _stops = results[0] as List<BusStop>;
      _districts = results[1] as List<District>;
      _isLoading = false;

      if (_stops.isNotEmpty) {
        _sourceStop = _stops.firstWhere((s) => s.id == 1, orElse: () => _stops.first);
        _destinationStop = _stops.firstWhere((s) => s.id == 7, orElse: () => _stops.last);
      }
    });

    if (_sourceStop != null && _destinationStop != null) {
      _executeSearch();
    }
  }

  Future<void> _executeSearch() async {
    if (_sourceStop == null || _destinationStop == null) return;

    setState(() => _isSearching = true);
    final res = await ApiService.searchJourney(
      _sourceStop!.id,
      _destinationStop!.id,
      busType: _selectedBusType.isNotEmpty ? _selectedBusType : null,
    );

    if (!mounted) return;
    setState(() {
      _results = res;
      _isSearching = false;
    });
  }

  void _swapStops() {
    setState(() {
      final temp = _sourceStop;
      _sourceStop = _destinationStop;
      _destinationStop = temp;
    });
    _executeSearch();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.isHindi ? 'बस मार्ग खोजें (Journey Finder)' : 'Find Bus Journey',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.brandIndigo))
          : RefreshIndicator(
              onRefresh: _executeSearch,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Search Card
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.borderLight),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.05),
                            blurRadius: 10,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (_districtsWithStops.isNotEmpty) ...[
                            SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              child: Row(
                                children: [
                                  ChoiceChip(
                                    label: Text(widget.isHindi ? 'सभी जिले' : 'All Districts'),
                                    selected: _selectedDistrict == null,
                                    onSelected: (val) {
                                      if (val) setState(() => _selectedDistrict = null);
                                    },
                                    selectedColor: AppColors.brandIndigo,
                                    labelStyle: TextStyle(
                                      color: _selectedDistrict == null ? Colors.white : AppColors.textMainLight,
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  const SizedBox(width: 6),
                                  ..._districtsWithStops.map((d) {
                                    final isSelected = _selectedDistrict?.id == d.id;
                                    return Padding(
                                      padding: const EdgeInsets.only(right: 6),
                                      child: ChoiceChip(
                                        label: Text(d.displayName(widget.isHindi)),
                                        selected: isSelected,
                                        onSelected: (val) {
                                          setState(() => _selectedDistrict = val ? d : null);
                                        },
                                        selectedColor: AppColors.brandIndigo,
                                        labelStyle: TextStyle(
                                          color: isSelected ? Colors.white : AppColors.textMainLight,
                                          fontSize: 11,
                                        ),
                                      ),
                                    );
                                  }),
                                ],
                              ),
                            ),
                            const SizedBox(height: 12),
                          ],

                          // Source Stop Dropdown
                          _buildStopSelector(
                            label: widget.isHindi ? 'कहाँ से (Boarding Stop)' : 'From (Boarding Stop)',
                            icon: Icons.trip_origin,
                            iconColor: AppColors.brandIndigo,
                            selected: _sourceStop,
                            onChanged: (val) {
                              setState(() => _sourceStop = val);
                              _executeSearch();
                            },
                          ),

                          // Swap Locations Button
                          Center(
                            child: IconButton(
                              tooltip: widget.isHindi ? 'स्थान बदलें' : 'Swap Locations',
                              onPressed: _swapStops,
                              icon: const Icon(Icons.swap_vert, color: AppColors.brandIndigo),
                              style: IconButton.styleFrom(
                                backgroundColor: const Color(0xFFEEF2FF),
                                padding: const EdgeInsets.all(6),
                              ),
                            ),
                          ),

                          // Destination Stop Dropdown
                          _buildStopSelector(
                            label: widget.isHindi ? 'कहाँ तक (Destination Stop)' : 'To (Destination Stop)',
                            icon: Icons.location_on,
                            iconColor: const Color(0xFFDC2626),
                            selected: _destinationStop,
                            onChanged: (val) {
                              setState(() => _destinationStop = val);
                              _executeSearch();
                            },
                          ),
                          const SizedBox(height: 14),

                          // Bus Category Filter Chips
                          SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            child: Row(
                              children: [
                                _buildFilterChip('', widget.isHindi ? 'सभी बसें (All)' : 'All Buses'),
                                const SizedBox(width: 6),
                                _buildFilterChip('express', widget.isHindi ? 'एक्सप्रेस (Express)' : 'Express'),
                                const SizedBox(width: 6),
                                _buildFilterChip('ac', widget.isHindi ? 'वातानुकूलित (AC)' : 'AC'),
                                const SizedBox(width: 6),
                                _buildFilterChip('ordinary', widget.isHindi ? 'साधारण (Ordinary)' : 'Ordinary'),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Results Header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          widget.isHindi ? 'उपलब्ध यात्रा विकल्प' : 'Available Journey Options',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppColors.textMainLight),
                        ),
                        if (_isSearching)
                          const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
                      ],
                    ),
                    const SizedBox(height: 12),

                    // Direct Routes List
                    if (_results != null && _results!.directRoutes.isNotEmpty) ...[
                      Text(
                        widget.isHindi ? 'सीधी बस सेवा (Direct Routes):' : 'Direct Routes:',
                        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.brandIndigo),
                      ),
                      const SizedBox(height: 8),
                      ..._results!.directRoutes.map((r) => RouteCard(route: r, isHindi: widget.isHindi)),
                    ],

                    // Connecting 1-Transfer Routes List
                    if (_results != null && _results!.transferRoutes.isNotEmpty) ...[
                      const SizedBox(height: 12),
                      Text(
                        widget.isHindi ? 'कनेक्टिंग बस सेवा (1-Transfer Routes):' : 'Connecting 1-Transfer Routes:',
                        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.teal),
                      ),
                      const SizedBox(height: 8),
                      ..._results!.transferRoutes.map((cj) => ConnectingJourneyCard(journey: cj, isHindi: widget.isHindi)),
                    ],

                    // Empty State
                    if (_results != null && _results!.isEmpty && !_isSearching)
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(28),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: AppColors.borderLight),
                        ),
                        child: Column(
                          children: [
                            const Icon(Icons.directions_bus_filled_outlined, size: 48, color: Color(0xFF94A3B8)),
                            const SizedBox(height: 12),
                            Text(
                              widget.isHindi
                                  ? 'इस मार्ग पर कोई सीधी या कनेक्टिंग बस नहीं मिली।'
                                  : 'No direct or connecting buses found for this selection.',
                              textAlign: TextAlign.center,
                              style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.textMainLight, fontSize: 14),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              widget.isHindi
                                  ? 'कृपया अन्य नजदीकी बस स्टैंड का चयन करें अथवा बस प्रकार फ़िल्टर को "सभी बसें" पर रखें।'
                                  : 'Please choose an alternate nearby terminal or reset filter to "All Buses".',
                              textAlign: TextAlign.center,
                              style: const TextStyle(color: AppColors.textMutedLight, fontSize: 12),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStopSelector({
    required String label,
    required IconData icon,
    required Color iconColor,
    required BusStop? selected,
    required ValueChanged<BusStop?> onChanged,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<BusStop>(
          isExpanded: true,
          value: selected,
          hint: Text(label, style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8))),
          icon: const Icon(Icons.arrow_drop_down, color: AppColors.brandIndigo),
          items: _displayedStops.map((s) {
            return DropdownMenuItem<BusStop>(
              value: s,
              child: Row(
                children: [
                  Icon(icon, size: 16, color: iconColor),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      s.displayName(widget.isHindi),
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textMainLight),
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

  Widget _buildFilterChip(String value, String label) {
    final isSelected = _selectedBusType == value;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) {
        setState(() => _selectedBusType = value);
        _executeSearch();
      },
      selectedColor: AppColors.brandIndigo,
      labelStyle: TextStyle(
        color: isSelected ? Colors.white : AppColors.textMainLight,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
        fontSize: 11,
      ),
    );
  }
}
