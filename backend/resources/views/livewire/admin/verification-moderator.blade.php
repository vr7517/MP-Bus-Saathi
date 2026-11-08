<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">🤝 कम्युनिटी फीडबैक एवं समय-सारणी सत्यापन (Community Verifications)</h1>
        <p class="text-xs text-slate-500">यात्रियों द्वारा ग्राउंड लेवल पर दर्ज की गई बस समय, देरी एवं निरस्तीकरण रिपोर्ट की समीक्षा</p>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">बस स्टैंड</th>
                        <th class="px-6 py-3">रूट कोड</th>
                        <th class="px-6 py-3">रिपोर्ट प्रकार</th>
                        <th class="px-6 py-3">देरी (Delay)</th>
                        <th class="px-6 py-3">टिप्पणी (Notes)</th>
                        <th class="px-6 py-3">समय</th>
                        <th class="px-6 py-3">कार्रवाई (Action)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($verifications as $v)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3.5 font-bold text-slate-900">{{ $v->busStop->name_hi }}</td>
                        <td class="px-6 py-3.5">{{ $v->schedule->route->route_code }}</td>
                        <td class="px-6 py-3.5">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold 
                                {{ $v->report_type === 'on_time' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $v->report_type }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-semibold {{ $v->delay_minutes > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $v->delay_minutes > 0 ? "+{$v->delay_minutes} मिनट" : 'समय पर' }}
                        </td>
                        <td class="px-6 py-3.5 text-xs text-slate-500">{{ $v->notes ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-xs text-slate-400">{{ $v->verified_at->diffForHumans() }}</td>
                        <td class="px-6 py-3.5 space-x-2">
                            @if(!$v->is_approved)
                                <button wire:click="approve({{ $v->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                    स्वीकारें
                                </button>
                            @else
                                <span class="text-xs text-emerald-600 font-bold">स्वीकृत ✅</span>
                            @endif
                            <button wire:click="dismiss({{ $v->id }})" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-xs font-semibold">
                                खारिज करें
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">
                            कोई सत्यापन रिपोर्ट लंबित नहीं है।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

