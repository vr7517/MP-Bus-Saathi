import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import 'driver_mode_screen.dart';
import 'operator_portal_screen.dart';
import 'verification_dialog.dart';

class ProfileScreen extends StatelessWidget {
  final bool isHindi;
  final ValueChanged<bool> onLanguageChanged;

  const ProfileScreen({
    super.key,
    required this.isHindi,
    required this.onLanguageChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          isHindi ? 'प्रोफ़ाइल एवं सेटिंग्स' : 'Profile & Settings',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // User Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.borderLight),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: const BoxDecoration(
                      color: Color(0xFFEEF2FF),
                      shape: BoxShape.circle,
                    ),
                    child: const Center(
                      child: Icon(Icons.person, color: AppColors.brandIndigo, size: 28),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          isHindi ? 'यात्री (Guest Mode)' : 'Commuter (Guest Mode)',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppColors.textMainLight),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          isHindi ? 'मध्य प्रदेश सार्वजनिक परिवहन' : 'Madhya Pradesh Public Transit',
                          style: const TextStyle(fontSize: 12, color: AppColors.textMutedLight),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppColors.gpsLiveBg,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      isHindi ? 'सत्यापित' : 'Active',
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.gpsLive),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Settings Section
            Text(
              isHindi ? 'एप्लिकेशन प्राथमिकताएं' : 'Application Preferences',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 10),

            // Language Toggle Tile
            Material(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: ListTile(
                  leading: const Icon(Icons.translate, color: AppColors.brandIndigo),
                  title: Text(
                    isHindi ? 'भाषा / Language' : 'Language / भाषा',
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                  ),
                  subtitle: Text(
                    isHindi ? 'वर्तमान: हिन्दी (Hindi)' : 'Current: English',
                    style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight),
                  ),
                  trailing: TextButton(
                    onPressed: () => onLanguageChanged(!isHindi),
                    child: Text(
                      isHindi ? 'Switch to English' : 'हिन्दी में बदलें',
                      style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.brandIndigo),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Professional Transit Roles
            Text(
              isHindi ? 'परिवहन सेवा एवं ऑपरेटर विकल्प' : 'Transport Service & Operator',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 10),

            // Driver Mode Tile
            _buildActionItem(
              icon: Icons.drive_eta,
              color: AppColors.brandIndigo,
              title: isHindi ? 'चालक / परिचालक मोड' : 'Driver / Conductor Mode',
              subtitle: isHindi ? 'बिना जीपीएस वाली बसों के लिए स्मार्टफोन से लाइव ट्रैकिंग' : 'Smartphone GPS beacon broadcast for rural buses',
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => DriverModeScreen(isHindi: isHindi),
                  ),
                );
              },
            ),

            // Operator Portal Tile
            _buildActionItem(
              icon: Icons.business_center,
              color: AppColors.teal,
              title: isHindi ? 'बस ऑपरेटर डैशबोर्ड' : 'Bus Operator Portal',
              subtitle: isHindi ? 'निजी एवं सरकारी ऑपरेटर फ्लीट व समय प्रबंधन' : 'Private & State operator fleet and timetable manager',
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => OperatorPortalScreen(isHindi: isHindi),
                  ),
                );
              },
            ),

            // Community Feedback / Report Issue
            _buildActionItem(
              icon: Icons.rate_review_outlined,
              color: AppColors.amber,
              title: isHindi ? 'कम्युनिटी रिपोर्ट एवं सुधार सुझाव' : 'Report Missing Bus or Correction',
              subtitle: isHindi ? 'गलत समय-सारणी, बंद बस या नए रूट की सूचना दें' : 'Submit corrections or notify unlisted rural services',
              onTap: () {
                showDialog(
                  context: context,
                  builder: (_) => VerificationDialog(
                    scheduleId: 1,
                    busStopId: 1,
                    isHindi: isHindi,
                  ),
                );
              },
            ),

            const SizedBox(height: 16),
            // Privacy & Legal
            Text(
              isHindi ? 'कानूनी एवं प्राइवेसी' : 'Legal & Privacy',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.textMainLight),
            ),
            const SizedBox(height: 10),

            _buildActionItem(
              icon: Icons.privacy_tip_outlined,
              color: const Color(0xFF64748B),
              title: isHindi ? 'गोपनीयता नीति (Privacy Policy)' : 'Privacy Policy (DPDPA 2023)',
              subtitle: isHindi ? 'डेटा सुरक्षा, स्थान अनुमति एवं नागरिक अधिकार' : 'Personal data protection and location permissions',
              onTap: () {
                showDialog(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: Text(isHindi ? 'गोपनीयता नीति' : 'Privacy Policy'),
                    content: Text(
                      isHindi
                          ? 'MP Bus Saathi भारत सरकार के डिजिटल व्यक्तिगत डेटा संरक्षण अधिनियम (DPDPA 2023) का पूर्ण अनुपालन करता है। हम आपका व्यक्तिगत डेटा कभी किसी तीसरे पक्ष को नहीं बेचते। विस्तृत नीति http://127.0.0.1:8000/privacy-policy पर उपलब्ध है।'
                          : 'MP Bus Saathi strictly adheres to India\'s Digital Personal Data Protection Act (DPDPA 2023). We never sell your personal data. Full policy accessible at http://127.0.0.1:8000/privacy-policy',
                      style: const TextStyle(fontSize: 12),
                    ),
                    actions: [
                      TextButton(
                        onPressed: () => Navigator.pop(context),
                        child: Text(isHindi ? 'समझ गया' : 'Understood'),
                      ),
                    ],
                  ),
                );
              },
            ),

            const SizedBox(height: 24),
            // App Footer
            Center(
              child: Column(
                children: [
                  Text(
                    isHindi ? 'मध्य प्रदेश बस साथी • संस्करण 1.0.0' : 'MP Bus Saathi • Version 1.0.0',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: AppColors.textMutedLight),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    isHindi ? 'हर गाँव, हर शहर — हर बस की जानकारी' : 'Connecting every village, town & city of Madhya Pradesh',
                    style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildActionItem({
    required IconData icon,
    required Color color,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      child: Material(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          decoration: BoxDecoration(
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
              child: Icon(icon, color: color, size: 20),
            ),
            title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.textMainLight)),
            subtitle: Text(subtitle, style: const TextStyle(fontSize: 11, color: AppColors.textMutedLight)),
            trailing: const Icon(Icons.chevron_right, size: 18, color: Color(0xFF94A3B8)),
          ),
        ),
      ),
    );
  }
}
