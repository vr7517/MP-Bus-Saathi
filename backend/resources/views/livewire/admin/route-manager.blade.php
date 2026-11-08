<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">🛣️ बस मार्ग व स्टॉप अनुक्रम (Routes & Stop Sequencer)</h1>
        <p class="text-xs text-slate-500">मध्य प्रदेश के सक्रिय रूट कोड, दूरी, यात्रा समय एवं मार्ग में आने वाले स्टॉप्स</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Routes List -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-xs uppercase text-slate-500">
                सक्रिय रूट्स (Select Route)
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($routes as $r)
                <button wire:click="selectRoute({{ $r->id }})" class="w-full text-left p-4 hover:bg-indigo-50 transition {{ $selectedRoute?->id === $r->id ? 'bg-indigo-50 border-l-4 border-indigo-600' : '' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-sm text-indigo-900">{{ $r->route_code }}</span>
                        <span class="text-xs text-slate-400">{{ $r->total_distance_km }} किमी</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        {{ $r->sourceStop->name_hi }} &rarr; {{ $r->destinationStop->name_hi }}
                    </p>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Route Details & Stops Sequence -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @if($selectedRoute)
                <div class="border-b border-slate-200 pb-4 mb-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ $selectedRoute->route_code }}</h2>
                        <p class="text-xs text-slate-500">
                            {{ $selectedRoute->sourceStop->name_en }} to {{ $selectedRoute->destinationStop->name_en }}
                            • कुल दूरी: {{ $selectedRoute->total_distance_km }} km • अनुमानित समय: {{ $selectedRoute->estimated_duration_minutes }} मिनट
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">सक्रिय (Active)</span>
                </div>

                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">मार्ग के बस स्टॉप (Ordered Stops Sequence)</h3>
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-indigo-200">
                    @foreach($selectedRoute->stops as $rs)
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full border-2 border-white bg-indigo-600 shadow"></div>
                        <div class="flex justify-between items-baseline">
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">{{ $rs->busStop->name_hi }} ({{ $rs->busStop->name_en }})</h4>
                                <p class="text-xs text-slate-400">जिला: {{ $rs->busStop->district }} • अक्षांश: {{ $rs->busStop->latitude }}, देशांतर: {{ $rs->busStop->longitude }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold text-slate-700">{{ $rs->distance_from_source_km }} km</span>
                                <p class="text-xs text-slate-400">+{{ $rs->typical_travel_time_minutes }} मिनट</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

