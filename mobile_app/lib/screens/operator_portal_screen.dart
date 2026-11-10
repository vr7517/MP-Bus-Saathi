import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import 'driver_mode_screen.dart';

class OperatorPortalScreen extends StatelessWidget {
  final bool isHindi;

  const OperatorPortalScreen({super.key, required this.isHindi});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          isHindi ? 'बस ऑपरेटर पोर्टल' : 'Bus Operator Portal',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Operator Profile Banner
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [AppColors.primary, AppColors.brandIndigo],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.15),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.business, color: Colors.white, size: 28),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          isHindi ? 'अधिकृत बस ऑपरेटर डैशबोर्ड' : 'Authorized Operator Fleet',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          isHindi ? 'मध्य प्रदेश राज्य परिवहन निगम (MPSRTS)' : 'MP State Road Transport Services',
                          style: const TextStyle(color: Color(0xFFC7D2FE), fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Fleet KPI Stats
            Row(
              children: [
                _buildStatCard(
                  title: isHindi ? 'कुल बसें' : 'Total Fleet',
                  value: '12',
                  icon: Icons.directions_bus,
                  color: AppColors.brandIndigo,
                ),
                const SizedBox(width: 12),
                _buildStatCard(
                  title: isHindi ? 'सक्रिय मार्ग' : 'Active Routes',
                  value: '8',
                  icon: Icons.alt_route,
                  color: AppColors.teal,
                ),
                const SizedBox(width: 12),
                _buildStatCard(
                  title: isHindi ? 'जीपीएस चालू' : 'GPS Live',
                  value: '5',
                  icon: Icons.gps_fixed,
                  color: AppColors.gpsLive,
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Operator Actions
            Text(
              isHindi ? 'त्वरित ऑपरेटर कार्य (Quick Actions)' : 'Quick Operator Actions',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 12),

            _buildActionTile(
              icon: Icons.drive_eta,
              color: AppColors.brandIndigo,
              title: isHindi ? 'चालक / परिचालक मोड चालू करें' : 'Start Driver / Conductor Mode',
              subtitle: isHindi
                  ? 'स्मार्टफोन के जीपीएस से अपनी बस का लाइव स्थान साझा करें'
                  : 'Broadcast real-time location via smartphone GPS beacon',
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => DriverModeScreen(isHindi: isHindi),
                  ),
                );
              },
            ),

            _buildActionTile(
              icon: Icons.schedule,
              color: AppColors.teal,
              title: isHindi ? 'समय-सारणी व स्टॉप प्रबंधन' : 'Timetable & Stops Management',
              subtitle: isHindi
                  ? 'अपनी अनुमत बसों के प्रस्थान व आगमन समय प्रबंधित करें'
                  : 'Configure departure and arrival timings for assigned routes',
              onTap: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      isHindi
                          ? 'पूर्ण ऑपरेटर नियंत्रण वेब पोर्टल http://127.0.0.1:8000/admin पर भी उपलब्ध है।'
                          : 'Full management is also active on web portal at http://127.0.0.1:8000/admin',
                    ),
                  ),
                );
              },
            ),

            _buildActionTile(
              icon: Icons.verified_user,
              color: AppColors.amber,
              title: isHindi ? 'परमिट एवं दस्तावेज सत्यापन' : 'Permits & Document Status',
              subtitle: isHindi
                  ? 'RTO स्टेज कैरिज परमिट, फिटनेस एवं बीमा वैधता'
                  : 'RTO Stage carriage permits, fitness and insurance validity',
              onTap: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      isHindi
                          ? 'सभी परमिट कानूनी रूप से सत्यापित हैं (Verified Official).'
                          : 'All vehicle permits are marked as Verified Official.',
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard({
    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.borderLight),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, size: 20, color: color),
            const SizedBox(height: 8),
            Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.textMainLight)),
            const SizedBox(height: 2),
            Text(title, style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight)),
          ],
        ),
      ),
    );
  }

  Widget _buildActionTile({
    required IconData icon,
    required Color color,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: ListTile(
        onTap: onTap,
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 22),
        ),
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight)),
        subtitle: Text(subtitle, style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight)),
        trailing: const Icon(Icons.chevron_right, size: 20, color: Color(0xFF94A3B8)),
      ),
    );
  }
}
