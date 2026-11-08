<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">⏱️ बस समय-सारणी एवं अनुसूची (Timetable & Schedules)</h1>
        <p class="text-xs text-slate-500">प्रस्थान एवं आगमन का समय, ऑपरेटिंग दिन एवं आवंटित बस फ्लीट</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">रूट कोड</th>
                        <th class="px-6 py-3">मार्ग (Origin & Destination)</th>
                        <th class="px-6 py-3">प्रस्थान (Departure)</th>
                        <th class="px-6 py-3">आगमन (Arrival)</th>
                        <th class="px-6 py-3">आवंटित बस</th>
                        <th class="px-6 py-3">दिन (Days)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($schedules as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3.5 font-bold text-indigo-900">{{ $s->route->route_code }}</td>
                        <td class="px-6 py-3.5">
                            {{ $s->route->sourceStop->name_hi }} &rarr; {{ $s->route->destinationStop->name_hi }}
                        </td>
                        <td class="px-6 py-3.5 font-bold text-emerald-700">{{ substr($s->departure_time, 0, 5) }}</td>
                        <td class="px-6 py-3.5 font-bold text-slate-700">{{ substr($s->expected_arrival_time, 0, 5) }}</td>
                        <td class="px-6 py-3.5">
                            @if($s->bus)
                                <span class="font-semibold text-slate-900">{{ $s->bus->registration_number }}</span>
                                <span class="block text-xs text-slate-400">{{ $s->bus->operator?->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">अनसाइंड (Unassigned)</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-600 font-medium">प्रतिदिन (Daily)</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

