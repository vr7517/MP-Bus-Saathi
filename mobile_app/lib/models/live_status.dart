class LiveBusStatus {
  final int busId;
  final String busNumber;
  final String busType;
  final String operatorName;
  final String gpsStatus; // "live" or "unavailable"
  final String gpsStatusLabelHi;
  final String gpsStatusLabelEn;
  final double? latitude;
  final double? longitude;
  final double speedKmh;
  final double heading;
  final int? lastPingSecondsAgo;
  final NextStopInfo? nextStop;

  LiveBusStatus({
    required this.busId,
    required this.busNumber,
    required this.busType,
    required this.operatorName,
    required this.gpsStatus,
    required this.gpsStatusLabelHi,
    required this.gpsStatusLabelEn,
    this.latitude,
    this.longitude,
    required this.speedKmh,
    required this.heading,
    this.lastPingSecondsAgo,
    this.nextStop,
  });

  factory LiveBusStatus.fromJson(Map<String, dynamic> json) {
    var loc = json['current_location'] as Map<String, dynamic>?;
    var next = json['next_stop'] as Map<String, dynamic>?;

    return LiveBusStatus(
      busId: json['bus_id'] as int,
      busNumber: json['bus_number'] ?? '',
      busType: json['bus_type'] ?? 'ordinary',
      operatorName: json['operator'] ?? '',
      gpsStatus: json['gps_status'] ?? 'unavailable',
      gpsStatusLabelHi: json['gps_status_label_hi'] ?? 'जीपीएस अनुपलब्ध',
      gpsStatusLabelEn: json['gps_status_label_en'] ?? 'GPS Unavailable',
      latitude: loc != null ? (loc['latitude'] as num?)?.toDouble() : null,
      longitude: loc != null ? (loc['longitude'] as num?)?.toDouble() : null,
      speedKmh: loc != null ? (loc['speed_kmh'] as num?)?.toDouble() ?? 0.0 : 0.0,
      heading: loc != null ? (loc['heading'] as num?)?.toDouble() ?? 0.0 : 0.0,
      lastPingSecondsAgo: json['last_ping_seconds_ago'] as int?,
      nextStop: next != null ? NextStopInfo.fromJson(next) : null,
    );
  }

  bool get isLive => gpsStatus == 'live';
}

class NextStopInfo {
  final int stopId;
  final String nameHi;
  final String nameEn;
  final String district;
  final double distanceKm;
  final int etaMinutes;

  NextStopInfo({
    required this.stopId,
    required this.nameHi,
    required this.nameEn,
    required this.district,
    required this.distanceKm,
    required this.etaMinutes,
  });

  factory NextStopInfo.fromJson(Map<String, dynamic> json) {
    return NextStopInfo(
      stopId: json['stop_id'] as int,
      nameHi: json['name_hi'] ?? '',
      nameEn: json['name_en'] ?? '',
      district: json['district'] ?? '',
      distanceKm: (json['distance_km'] as num).toDouble(),
      etaMinutes: json['eta_minutes'] as int? ?? 0,
    );
  }
}

