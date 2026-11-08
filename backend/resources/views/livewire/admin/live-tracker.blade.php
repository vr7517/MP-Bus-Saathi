<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">🗺️ लाइव जीपीएस ट्रैकिंग मैप (OpenStreetMap Real-Time Radar)</h1>
            <p class="text-xs text-slate-500">मध्य प्रदेश राजमार्गों पर बसों की वास्तविक स्थिति एवं जीपीएस उपलब्धता निगरानी</p>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-100 text-emerald-800 font-bold">🟢 GPS Live (&lt;180s)</span>
            <span class="inline-flex items-center px-2 py-1 rounded bg-amber-100 text-amber-800 font-bold">🟡 GPS Unavailable (&gt;180s)</span>
        </div>
    </div>

    <!-- Map Container -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-2">
        <div id="admin-map" style="height: 480px; width: 100%; border-radius: 8px;"></div>
    </div>

    <!-- Bus Telemetry Details -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-xs uppercase text-slate-500">
            सक्रिय जीपीएस टेलीमेट्री रिकॉर्ड्स (Active Telemetry Streams)
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($telemetries as $t)
            @php $isLive = $t->isGpsLive(); @endphp
            <div class="p-4 flex justify-between items-center hover:bg-slate-50">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">{{ $isLive ? '🟢' : '🟡' }}</span>
                    <div>
                        <span class="font-bold text-slate-900">{{ $t->bus->registration_number }}</span>
                        <span class="text-xs text-slate-500 ml-2">({{ $t->bus->operator?->name }})</span>
                        <p class="text-xs text-slate-400 mt-0.5">
                            अक्षांश: {{ $t->latitude }}, देशांतर: {{ $t->longitude }} • गति: {{ $t->current_speed }} km/h
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 rounded text-xs font-bold {{ $isLive ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $isLive ? 'GPS Live (लाइव)' : 'GPS Unavailable (अनुपलब्ध)' }}
                    </span>
                    <p class="text-xs text-slate-400 mt-1">अंतिम पिंग: {{ $t->last_ping_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Center map between Bhopal and Indore
            var map = L.map('admin-map').setView([23.1, 76.8], 8);

            // OpenStreetMap Standard Tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Bus Stops Markers
            @foreach($stops as $stop)
                L.circleMarker([{{ $stop->latitude }}, {{ $stop->longitude }}], {
                    radius: {{ $stop->is_major_junction ? 8 : 5 }},
                    fillColor: "{{ $stop->is_major_junction ? '#4f46e5' : '#94a3b8' }}",
                    color: "#ffffff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map).bindPopup("<b>{{ $stop->name_hi }}</b><br>{{ $stop->name_en }}<br>जिला: {{ $stop->district }}");
            @endforeach

            // Moving Buses Markers
            @foreach($telemetries as $t)
                @php $isLive = $t->isGpsLive(); @endphp
                var busIcon = L.divIcon({
                    className: 'custom-bus-icon',
                    html: '<div style="background-color: {{ $isLive ? '#059669' : '#d97706' }}; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: 2px solid white;">🚌</div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                L.marker([{{ $t->latitude }}, {{ $t->longitude }}], { icon: busIcon })
                    .addTo(map)
                    .bindPopup("<b>{{ $t->bus->registration_number }}</b><br>ऑपरेटर: {{ $t->bus->operator?->name }}<br>गति: {{ $t->current_speed }} km/h<br>स्थिति: <b>{{ $isLive ? '🟢 GPS Live' : '🟡 GPS Unavailable' }}</b>");
            @endforeach
        });
    </script>
</div>

