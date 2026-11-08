<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">🚍 बस फ्लीट प्रबंधन (Fleet Management)</h1>
            <p class="text-xs text-slate-500">मध्य प्रदेश की पंजीकृत बसों की सूची एवं जीपीएस हार्डवेयर कॉन्फ़िगरेशन</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add New Bus Card -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-sm font-bold text-slate-800 mb-3">➕ नई बस पंजीकृत करें (Register New Bus)</h2>
        <form wire:submit.prevent="createBus" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">पंजीकरण संख्या</label>
                <input type="text" wire:model="registration_number" placeholder="MP-04-AB-1234" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 uppercase">
                @error('registration_number') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">ऑपरेटर एजेंसी</label>
                <select wire:model="operator_id" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- ऑपरेटर चुनें --</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}">{{ $op->name }}</option>
                    @endforeach
                </select>
                @error('operator_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">बस श्रेणी (Type)</label>
                <select wire:model="bus_type" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="ordinary">साधारण (Ordinary)</option>
                    <option value="express">एक्सप्रेस (Express)</option>
                    <option value="ac">एसी (AC)</option>
                    <option value="sleeper">स्लीपर (Sleeper)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">सीट क्षमता (Seats)</label>
                <input type="number" wire:model="seating_capacity" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                    बस जोड़ें
                </button>
            </div>
        </form>
    </div>

    <!-- Buses Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">पंजीकरण संख्या</th>
                        <th class="px-6 py-3">ऑपरेटर</th>
                        <th class="px-6 py-3">श्रेणी</th>
                        <th class="px-6 py-3">सीटें</th>
                        <th class="px-6 py-3">जीपीएस डिवाइस</th>
                        <th class="px-6 py-3">सिस्टम स्टेटस</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($buses as $bus)
                    @php $isLive = $bus->telemetry?->isGpsLive() ?? false; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3.5 font-bold text-slate-900">{{ $bus->registration_number }}</td>
                        <td class="px-6 py-3.5">{{ $bus->operator?->name }}</td>
                        <td class="px-6 py-3.5 capitalize">{{ $bus->bus_type }}</td>
                        <td class="px-6 py-3.5">{{ $bus->seating_capacity }}</td>
                        <td class="px-6 py-3.5">
                            <button wire:click="toggleGps({{ $bus->id }})" class="px-2.5 py-1 rounded text-xs font-semibold {{ $bus->has_gps_device ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $bus->has_gps_device ? '✅ GPS सक्षम (Enabled)' : '❌ GPS नहीं (Disabled)' }}
                            </button>
                        </td>
                        <td class="px-6 py-3.5">
                            @if($isLive)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    🟢 GPS Live
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                                    🟡 GPS Unavailable
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

