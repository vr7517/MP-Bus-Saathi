import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import 'home_screen.dart';
import 'live_map_screen.dart';
import 'profile_screen.dart';
import 'saved_screen.dart';
import 'search_screen.dart';

class MainNavigationShell extends StatefulWidget {
  final bool initialHindi;

  const MainNavigationShell({super.key, this.initialHindi = true});

  @override
  State<MainNavigationShell> createState() => _MainNavigationShellState();
}

class _MainNavigationShellState extends State<MainNavigationShell> {
  int _currentIndex = 0;
  late bool _isHindi;

  @override
  void initState() {
    super.initState();
    _isHindi = widget.initialHindi;
  }

  void _onLanguageToggle(bool isHindi) {
    setState(() {
      _isHindi = isHindi;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> screens = [
      HomeScreen(
        key: ValueKey('home_$_isHindi'),
        initialHindi: _isHindi,
      ),
      SearchScreen(
        key: ValueKey('search_$_isHindi'),
        isHindi: _isHindi,
      ),
      LiveMapScreen(
        key: ValueKey('live_map_$_isHindi'),
        busId: 1,
        busNumber: 'MP-04-EA-1024',
        scheduleId: 1,
        routeId: 1,
        isHindi: _isHindi,
      ),
      SavedScreen(
        key: ValueKey('saved_$_isHindi'),
        isHindi: _isHindi,
      ),
      ProfileScreen(
        key: ValueKey('profile_$_isHindi'),
        isHindi: _isHindi,
        onLanguageChanged: _onLanguageToggle,
      ),
    ];

    return Scaffold(
      body: screens[_currentIndex],
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border(
            top: BorderSide(color: AppColors.borderLight, width: 1),
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 10,
              offset: const Offset(0, -3),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (index) => setState(() => _currentIndex = index),
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          selectedItemColor: AppColors.brandIndigo,
          unselectedItemColor: const Color(0xFF64748B),
          selectedLabelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11),
          unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.w500, fontSize: 11),
          elevation: 0,
          items: [
            BottomNavigationBarItem(
              icon: const Icon(Icons.home_outlined),
              activeIcon: const Icon(Icons.home),
              label: _isHindi ? 'होम' : 'Home',
            ),
            BottomNavigationBarItem(
              icon: const Icon(Icons.search_outlined),
              activeIcon: const Icon(Icons.search),
              label: _isHindi ? 'खोजें' : 'Search',
            ),
            BottomNavigationBarItem(
              icon: const Icon(Icons.map_outlined),
              activeIcon: const Icon(Icons.map),
              label: _isHindi ? 'लाइव मैप' : 'Live Map',
            ),
            BottomNavigationBarItem(
              icon: const Icon(Icons.bookmark_outline),
              activeIcon: const Icon(Icons.bookmark),
              label: _isHindi ? 'पसंदीदा' : 'Saved',
            ),
            BottomNavigationBarItem(
              icon: const Icon(Icons.person_outline),
              activeIcon: const Icon(Icons.person),
              label: _isHindi ? 'प्रोफ़ाइल' : 'Profile',
            ),
          ],
        ),
      ),
    );
  }
}
