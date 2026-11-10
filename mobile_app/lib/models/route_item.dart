class SearchResultContainer {
  final List<RouteSearchResult> directRoutes;
  final List<ConnectingJourney> transferRoutes;

  SearchResultContainer({
    required this.directRoutes,
    required this.transferRoutes,
  });

  bool get isEmpty => directRoutes.isEmpty && transferRoutes.isEmpty;
  bool get isNotEmpty => !isEmpty;
}

class RouteSearchResult {
  final int routeId;
  final String routeCode;
  final String originStop;
  final String finalDestinationStop;
  final double travelDistanceKm;
  final int estimatedDurationMinutes;
  final int stopsInSegment;
  final List<ScheduleItem> schedules;

  RouteSearchResult({
    required this.routeId,
    required this.routeCode,
    required this.originStop,
    required this.finalDestinationStop,
    required this.travelDistanceKm,
    required this.estimatedDurationMinutes,
    required this.stopsInSegment,
    required this.schedules,
  });

  factory RouteSearchResult.fromJson(Map<String, dynamic> json) {
    var schedList = json['schedules'] as List? ?? [];
    return RouteSearchResult(
      routeId: (json['route_id'] as num?)?.toInt() ?? 0,
      routeCode: json['route_code'] ?? '',
      originStop: json['origin_stop'] ?? '',
      finalDestinationStop: json['final_destination_stop'] ?? '',
      travelDistanceKm: (json['travel_distance_km'] as num?)?.toDouble() ?? 0.0,
      estimatedDurationMinutes: (json['estimated_duration_minutes'] as num?)?.round() ?? 0,
      stopsInSegment: (json['stops_in_segment'] as num?)?.round() ?? 0,
      schedules: schedList.map((s) => ScheduleItem.fromJson(s as Map<String, dynamic>)).toList(),
    );
  }
}

class ScheduleItem {
  final int scheduleId;
  final int? busId;
  final String busNumber;
  final String operatorName;
  final String busType;
  final bool hasGps;
  final String gpsStatus; // "live" or "unavailable"
  final String departureTime;
  final String arrivalTime;

  ScheduleItem({
    required this.scheduleId,
    this.busId,
    required this.busNumber,
    required this.operatorName,
    required this.busType,
    required this.hasGps,
    required this.gpsStatus,
    required this.departureTime,
    required this.arrivalTime,
  });

  factory ScheduleItem.fromJson(Map<String, dynamic> json) {
    return ScheduleItem(
      scheduleId: (json['schedule_id'] as num?)?.toInt() ?? 0,
      busId: (json['bus_id'] as num?)?.toInt(),
      busNumber: json['bus_number'] ?? json['registration_number'] ?? 'Not Assigned',
      operatorName: json['operator_name'] ?? json['operator'] ?? 'MP Transit',
      busType: json['bus_type'] ?? 'ordinary',
      hasGps: json['has_gps'] == true,
      gpsStatus: json['gps_status'] ?? 'unavailable',
      departureTime: json['departure_time'] ?? '',
      arrivalTime: json['arrival_time'] ?? '',
    );
  }

  bool get isGpsLive => gpsStatus == 'live';
}

class ConnectingJourney {
  final String interchangeNameHi;
  final String interchangeNameEn;
  final String interchangeDistrict;
  final RouteSearchResult leg1;
  final RouteSearchResult leg2;
  final double totalDistanceKm;
  final int totalDurationMinutes;

  ConnectingJourney({
    required this.interchangeNameHi,
    required this.interchangeNameEn,
    required this.interchangeDistrict,
    required this.leg1,
    required this.leg2,
    required this.totalDistanceKm,
    required this.totalDurationMinutes,
  });

  factory ConnectingJourney.fromJson(Map<String, dynamic> json) {
    final junction = json['interchange_stop'] as Map<String, dynamic>? ?? {};
    return ConnectingJourney(
      interchangeNameHi: junction['name_hi'] ?? '',
      interchangeNameEn: junction['name_en'] ?? '',
      interchangeDistrict: junction['district'] ?? '',
      leg1: RouteSearchResult.fromJson(json['leg_1'] as Map<String, dynamic>? ?? {}),
      leg2: RouteSearchResult.fromJson(json['leg_2'] as Map<String, dynamic>? ?? {}),
      totalDistanceKm: (json['total_distance_km'] as num?)?.toDouble() ?? 0.0,
      totalDurationMinutes: (json['total_estimated_duration_minutes'] as num?)?.round() ?? 0,
    );
  }

  String interchangeName(bool isHindi) =>
      isHindi ? '$interchangeNameHi ($interchangeNameEn)' : '$interchangeNameEn ($interchangeNameHi)';
}
