class BusStop {
  final int id;
  final String nameHi;
  final String nameEn;
  final String district;
  final double latitude;
  final double longitude;
  final bool isMajorJunction;

  BusStop({
    required this.id,
    required this.nameHi,
    required this.nameEn,
    required this.district,
    required this.latitude,
    required this.longitude,
    required this.isMajorJunction,
  });

  factory BusStop.fromJson(Map<String, dynamic> json) {
    return BusStop(
      id: json['id'] as int,
      nameHi: json['name_hi'] ?? '',
      nameEn: json['name_en'] ?? '',
      district: json['district'] ?? '',
      latitude: (json['latitude'] as num).toDouble(),
      longitude: (json['longitude'] as num).toDouble(),
      isMajorJunction: json['is_major_junction'] == true || json['is_major_junction'] == 1,
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name_hi': nameHi,
    'name_en': nameEn,
    'district': district,
    'latitude': latitude,
    'longitude': longitude,
    'is_major_junction': isMajorJunction,
  };

  String displayName(bool isHindi) => isHindi ? '$nameHi ($nameEn)' : '$nameEn ($nameHi)';
}

