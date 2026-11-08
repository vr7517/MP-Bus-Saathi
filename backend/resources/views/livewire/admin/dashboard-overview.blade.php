<div class="space-y-6">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">कुल बसें (Buses)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalBuses }}</span>
                <span class="text-indigo-600 bg-indigo-50 p-2 rounded-lg text-lg">🚍</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">पंजीकृत बस फ्लीट</p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">लाइव जीपीएस (Live GPS)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-emerald-600">{{ $liveGpsBuses }}</span>
                <span class="text-emerald-600 bg-emerald-50 p-2 rounded-lg text-lg">📡</span>
            </div>
            <p class="text-xs text-emerald-600 mt-1">🟢 &lt; 3 मिनट पिंग सक्रिय</p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">सक्रिय रूट्स (Routes)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalRoutes }}</span>
                <span class="text-indigo-600 bg-indigo-50 p-2 rounded-lg text-lg">🛣️</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">इंटर-सिटी व ग्रामीण मार्ग</p>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">बस स्टैंड (Stops)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalStops }}</span>
                <span class="text-indigo-600 bg-indigo-50 p-2 rounded-lg text-lg">📍</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">PostGIS मैप लोकेशन्स</p>
        </div>
    </div>

    <!-- Active Fleet Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-slate-800 text-base">🚍 बस फ्लीट लाइव स्थिति (Fleet Status)</h2>
            <a href="/admin/buses" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">सभी देखें &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">बस नंबर</th>
                        <th class="px-6 py-3">ऑपरेटर</th>
                        <th class="px-6 py-3">श्रेणी</th>
                        <th class="px-6 py-3">जीपीएस स्टेटस (GPS Health)</th>
                        <th class="px-6 py-3">अंतिम पिंग</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activeBuses as $bus)
                    @php $isLive = $bus->telemetry?->isGpsLive() ?? false; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3.5 font-bold text-slate-900">{{ $bus->registration_number }}</td>
                        <td class="px-6 py-3.5">{{ $bus->operator?->name }}</td>
                        <td class="px-6 py-3.5">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 capitalize">
                                {{ $bus->bus_type }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            @if($isLive)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    GPS Live (लाइव)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    GPS Unavailable (अनुपलब्ध)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-xs text-slate-500">
                            {{ $bus->telemetry?->last_ping_at ? $bus->telemetry->last_ping_at->diffForHumans() : 'कभी नहीं' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Community Verifications -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-slate-800 text-base">🤝 नवीनतम कम्युनिटी सत्यापन (Recent Passenger Feedback)</h2>
            <a href="/admin/verifications" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">क्यू देखें &rarr;</a>
        </div>
        <div class="p-6 divide-y divide-slate-100">
            @forelse($recentVerifications as $report)
            <div class="py-3 flex justify-between items-center">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-slate-900 text-sm">{{ $report->busStop->name_hi }} ({{ $report->busStop->name_en }})</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium 
                            {{ $report->report_type === 'on_time' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $report->report_type }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $report->notes ?? 'कोई टिप्पणी नहीं' }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ $report->verified_at->diffForHumans() }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-4">अभी तक कोई कम्युनिटी सत्यापन रिपोर्ट नहीं है।</p>
            @endforelse
        </div>
    </div>
</div>

