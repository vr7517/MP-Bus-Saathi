<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'प्रशासन पोर्टल - मध्य प्रदेश बस साथी' }}</title>
    <!-- Tailwind CSS (CDN - Zero Node/NPM dependencies) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet OpenStreetMap CSS & JS for Admin Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; }
    </style>
    @livewireStyles
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <header class="bg-indigo-900 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🚌</span>
                <div>
                    <h1 class="font-bold text-lg leading-tight">मध्य प्रदेश बस साथी</h1>
                    <p class="text-xs text-indigo-200">MP Bus Saathi • Operator & Authority Admin Portal</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold flex items-center justify-end space-x-1.5">
                            @if(auth()->user()->isAdmin())
                                <span class="bg-amber-400 text-amber-950 text-[10px] font-bold px-2 py-0.5 rounded-full">👑 एडमिन</span>
                            @else
                                <span class="bg-indigo-700 text-indigo-100 text-[10px] font-bold px-2 py-0.5 rounded-full">🏢 ऑपरेटर</span>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <div class="text-[11px] text-indigo-200">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="bg-indigo-800 hover:bg-rose-700 text-xs px-3 py-1.5 rounded-lg text-indigo-100 hover:text-white transition flex items-center space-x-1">
                            <span>🚪</span>
                            <span>लॉगआउट</span>
                        </button>
                    </form>
                @else
                    <a href="/login" class="bg-indigo-600 hover:bg-indigo-500 text-xs px-3 py-1.5 rounded-lg text-white font-medium">लॉगिन</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <aside class="md:col-span-1">
            <nav class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 space-y-1">
                <a href="/admin" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    📊 <span class="ml-3">डैशबोर्ड (Overview)</span>
                </a>
                <a href="/admin/tracking" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    🗺️ <span class="ml-3">लाइव ट्रैकिंग (Live Map)</span>
                </a>
                <a href="/admin/buses" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    🚍 <span class="ml-3">बस फ्लीट (Buses)</span>
                </a>
                <a href="/admin/routes" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    🛣️ <span class="ml-3">रूट्स व स्टॉप्स (Routes)</span>
                </a>
                <a href="/admin/schedules" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    ⏱️ <span class="ml-3">समय-सारणी (Timetables)</span>
                </a>
                <a href="/admin/verifications" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition">
                    🤝 <span class="ml-3">कम्युनिटी सत्यापन (Feedback)</span>
                </a>
            </nav>

            <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-xs text-indigo-900">
                <p class="font-bold mb-1">ℹ️ ऑफलाइन फर्स्ट व जीपीएस ट्रैकिंग</p>
                <p>यात्री मोबाइल ऐप बिना इंटरनेट के भी शेड्यूल सर्च कर सकता है। जीपीएस पिंग 180 सेकंड पुराना होने पर सिस्टम ऑटोमैटिकली <strong>GPS Unavailable</strong> स्टेटस प्रदर्शित करता है।</p>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="md:col-span-3">
            {{ $slot }}
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        मध्य प्रदेश बस साथी • MP Bus Saathi Open Transit Architecture • Powered by OpenStreetMap & Laravel Livewire
    </footer>

    @livewireScripts
</body>
</html>

