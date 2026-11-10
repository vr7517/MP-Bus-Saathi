<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">📁 डेटा प्रबंधन एवं कवरेज डैशबोर्ड (Data Acquisition & Completeness)</h1>
            <p class="text-xs text-slate-500">मध्य प्रदेश के सभी 55 जिलों में सार्वजनिक परिवहन डेटा की पूर्णता, सत्यापन एवं बैच आयात</p>
        </div>
        <div class="flex space-x-2">
            <button wire:click="$set('activeTab', 'completeness')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeTab === 'completeness' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200' }}">
                📊 जिला कवरेज (Coverage)
            </button>
            <button wire:click="$set('activeTab', 'importer')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeTab === 'importer' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200' }}">
                📥 बैच आयातक (Batch Import)
            </button>
        </div>
    </div>

    @if ($importMessage)
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            ✅ {{ $importMessage }}
        </div>
    @endif

    @if ($importError)
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
            ⚠️ {{ $importError }}
        </div>
    @endif

    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-400">राज्य कवरेज (Coverage)</span>
            <div class="text-2xl font-bold text-indigo-700 mt-1">{{ $stats['coverage_percentage'] }}%</div>
            <span class="text-[11px] text-slate-500">{{ $stats['covered_districts'] }} / {{ $stats['total_districts'] }} जिले सक्रिय</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-400">कुल बस स्टॉप्स (Stops)</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_stops'] }}</div>
            <span class="text-[11px] text-slate-500">सभी प्रमुख जंक्शन व स्टैंड</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-400">सक्रिय रूट्स (Routes)</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_routes'] }}</div>
            <span class="text-[11px] text-slate-500">{{ $stats['total_schedules'] }} दैनिक अनुसूचियां</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-400">पंजीकृत ऑपरेटर</span>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['total_operators'] }}</div>
            <span class="text-[11px] text-slate-500">{{ $stats['total_buses'] }} पंजीकृत बसें</span>
        </div>
    </div>

    <!-- TAB 1: District Completeness Grid -->
    @if ($activeTab === 'completeness')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-sm text-slate-800">मध्य प्रदेश के सभी 55 जिलों की ट्रांजिट मैपिंग स्थिति</h2>
            <span class="text-xs text-slate-500 font-medium">स्त्रोत: Local Government Directory (LGD) & MP RTO Gazette</span>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($districts as $d)
                @php $hasStops = $d->bus_stops_count > 0; @endphp
                <div class="p-3 rounded-lg border {{ $hasStops ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex justify-between items-center">
                    <div>
                        <div class="text-sm font-bold {{ $hasStops ? 'text-emerald-950' : 'text-slate-700' }}">
                            {{ $d->name_hi }}
                        </div>
                        <div class="text-xs text-slate-400">{{ $d->name_en }} ({{ $d->division }} संभाग)</div>
                    </div>
                    <div>
                        @if ($hasStops)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                🟢 {{ $d->bus_stops_count }} स्टॉप
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-200 text-slate-600">
                                ⚪ सर्वेक्षण बाकी
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 2: Batch Importer Form -->
    @if ($activeTab === 'importer')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        <div>
            <h2 class="font-bold text-base text-slate-900">बैच डेटा इनजेशन (Batch Data Ingestion Pipeline)</h2>
            <p class="text-xs text-slate-500 mt-0.5">सरकारी पोर्टल, गजट नोटिफिकेशन अथवा ऑपरेटर एक्सेल/JSON डेटा को सीधे सिस्टम में आयात करें</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">डेटा प्रकार (Dataset Type)</label>
                <select wire:model="importType" class="w-full text-sm rounded-lg border border-slate-300 p-2.5">
                    <option value="stops">बस स्टॉप्स एवं टर्मिनल (Bus Stops)</option>
                    <option value="routes">परिवहन मार्ग (Transit Routes)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">डेटा स्त्रोत नाम (Provenance Source)</label>
                <input type="text" wire:model="sourceName" class="w-full text-sm rounded-lg border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">स्त्रोत URL / संदर्भ (Source URL)</label>
                <input type="text" wire:model="sourceUrl" class="w-full text-sm rounded-lg border border-slate-300 p-2.5">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">JSON डेटा पेस्ट करें (Paste JSON Array)</label>
            <textarea wire:model="rawJsonData" rows="6"
                class="w-full font-mono text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                placeholder='[{"name_hi": "...", "name_en": "...", "district": "...", "latitude": 23.2, "longitude": 77.4}]'></textarea>
        </div>

        <div class="flex space-x-3">
            <button wire:click="parsePreview" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">
                🔍 डेटा का पूर्वावलोकन करें (Preview)
            </button>
            @if(count($previewData) > 0)
            <button wire:click="executeImport" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-500 transition flex items-center space-x-1">
                <span>✅ पुष्टि करें एवं आयात करें (Execute Import)</span>
            </button>
            @endif
        </div>

        @if(count($previewData) > 0)
        <div class="border border-indigo-100 rounded-xl p-4 bg-indigo-50/50">
            <h3 class="text-xs font-bold text-indigo-900 mb-2">पूर्वावलोकन (Preview First {{ count($previewData) }} Records):</h3>
            <pre class="bg-white p-3 rounded-lg border border-slate-200 text-[11px] overflow-x-auto text-slate-700 font-mono">{{ json_encode($previewData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>
    @endif
</div>
