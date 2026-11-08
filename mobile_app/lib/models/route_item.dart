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
      routeId: json['route_id'] as int,
      routeCode: json['route_code'] ?? '',
      originStop: json['origin_stop'] ?? '',
      finalDestinationStop: json['final_destination_stop'] ?? '',
      travelDistanceKm: (json['travel_distance_km'] as num).toDouble(),
      estimatedDurationMinutes: json['estimated_duration_minutes'] as int? ?? 0,
      stopsInSegment: json['stops_in_segment'] as int? ?? 0,
      schedules: schedList.map((s) => ScheduleItem.fromJson(s)).toList(),
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
      scheduleId: json['schedule_id'] as int,
      busId: json['bus_id'] as int?,
      busNumber: json['bus_number'] ?? 'Not Assigned',
      operatorName: json['operator_name'] ?? 'MP Transit',
      busType: json['bus_type'] ?? 'ordinary',
      hasGps: json['has_gps'] == true,
      gpsStatus: json['gps_status'] ?? 'unavailable',
      departureTime: json['departure_time'] ?? '',
      arrivalTime: json['arrival_time'] ?? '',
    );
  }

  bool get isGpsLive => gpsStatus == 'live';
}

