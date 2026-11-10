class District {
  final int id;
  final String nameEn;
  final String nameHi;
  final String code;
  final String division;
  final double latitude;
  final double longitude;

  District({
    required this.id,
    required this.nameEn,
    required this.nameHi,
    required this.code,
    required this.division,
    required this.latitude,
    required this.longitude,
  });

  factory District.fromJson(Map<String, dynamic> json) {
    return District(
      id: json['id'] as int,
      nameEn: json['name_en'] ?? '',
      nameHi: json['name_hi'] ?? '',
      code: json['code'] ?? '',
      division: json['division'] ?? '',
      latitude: (json['latitude'] as num).toDouble(),
      longitude: (json['longitude'] as num).toDouble(),
    );
  }

  String displayName(bool isHindi) => isHindi ? nameHi : nameEn;
}
