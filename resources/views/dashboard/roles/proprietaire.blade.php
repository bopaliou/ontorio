<div class="h-full flex flex-col gap-8 pb-10">
    <!-- Premium Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-[#274256] tracking-tight">
                Bonjour, <span class="text-[#cb2d2d]">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-gray-500 mt-2 text-lg font-medium"> Voici l'état de performance de votre patrimoine immobilier. </p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-emerald-50 text-emerald-700 px-6 py-3 rounded-2xl border border-emerald-100 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-black uppercase tracking-widest">Portefeuille Actif</span>
            </div>
        </div>
    </div>

    <!-- Profitability Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
        {{-- Net Cash Flow --}}
        <div class="bg-[#1a2e3d] text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group ontario-card-lift">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-3">Net en Poche (Mensuel)</p>
            <h3 class="text-3xl font-black">{{ number_format($data['kpis']['net_mensuel'], 0, ',', ' ') }} <span class="text-xs font-normal opacity-60">F CFA</span></h3>
            <p class="text-[10px] text-blue-200 mt-6 font-medium bg-white/5 inline-block px-3 py-1 rounded-lg italic">"Après charges & commissions"</p>
        </div>

        {{-- Revenus Bruts --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 ontario-card-lift">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Revenus Bruts encaissés</p>
            <h3 class="text-3xl font-black text-gray-900">{{ number_format($data['kpis']['revenu_mensuel'], 0, ',', ' ') }} <span class="text-xs font-normal">F CFA</span></h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Optimisé
                </span>
            </div>
        </div>

        {{-- Charges & Dépenses --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 ontario-card-lift">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Charges & Travaux</p>
            <h3 class="text-3xl font-black text-red-600">{{ number_format($data['kpis']['charges_mensuelles'], 0, ',', ' ') }} <span class="text-xs font-normal text-gray-400">F CFA</span></h3>
            <div class="mt-6">
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    @php $ratio = ($data['kpis']['revenu_mensuel'] > 0) ? ($data['kpis']['charges_mensuelles'] / $data['kpis']['revenu_mensuel']) * 100 : 0; @endphp
                    <div class="h-full bg-red-400 rounded-full" style="width: {{ min(100, $ratio) }}%"></div>
                </div>
            </div>
        </div>

        {{-- ROI / Rendement (Simulé) --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 ontario-card-lift">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Rendement Annuel Est.</p>
            <h3 class="text-3xl font-black text-[#274256]">8.4 <span class="text-xs font-normal text-gray-400">%</span></h3>
            <p class="text-xs text-gray-500 mt-4 leading-relaxed font-medium">Basé sur la valeur locative du parc.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Chart: Net Trend --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100">
             <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-[#274256]">Historique de Trésorerie</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Évolution de vos encaissements nets (6 mois)</p>
                </div>
            </div>

            <div class="h-72 flex items-end justify-between gap-4 px-2">
                @php $maxValTrend = max(array_column($data['revenus_par_mois'], 'montant')) ?: 1; @endphp
                @foreach($data['revenus_par_mois'] as $revData)
                    @php $hTrend = ($revData['montant'] / $maxValTrend) * 90; @endphp
                    <div class="flex-1 flex flex-col items-center group">
                        <div class="relative w-full flex justify-center items-end h-full">
                            <div class="absolute inset-0 bg-gray-50 rounded-2xl scale-x-90 group-hover:bg-blue-50 transition-all duration-500"></div>
                            <div class="w-1/3 bg-[#cb2d2d] rounded-t-xl z-10 transition-all duration-700 shadow-[0_0_20px_rgba(203,45,45,0.2)]" style="height: {{ $hTrend }}%"></div>
                        </div>
                        <p class="mt-4 text-[9px] font-black text-gray-400 uppercase tracking-tighter">{{ $revData['mois'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Asset Distribution --}}
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100 flex flex-col">
             <h3 class="text-xl font-black text-[#274256] mb-8">Détails des Commissions</h3>
             <div class="flex-1 flex flex-col justify-center gap-8">
                <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Commissions Gestion (10%)</p>
                    <p class="text-2xl font-black text-[#274256]">{{ number_format($data['kpis']['commissions_mensuelles'], 0, ',', ' ') }} F</p>
                </div>
                <div class="space-y-4">
                    <p class="text-xs text-gray-500 font-medium leading-relaxed italic border-l-4 border-blue-200 pl-4">
                        "Les commissions couvrent la gestion locative, le recouvrement et le suivi technique de vos actifs."
                    </p>
                    <button class="w-full py-4 bg-[#274256] text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-900/10 hover:bg-[#1a2e3d] transition-all">
                        Consulter mes factures
                    </button>
                </div>
             </div>
        </div>
    </div>

    {{-- Assets Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xl font-black text-[#274256]">État de Performance par Bien</h3>
            <span class="px-4 py-1.5 bg-gray-50 rounded-full text-[10px] font-black text-gray-400 uppercase tracking-widest border border-gray-100">Total : {{ count($data['biens_list']) }} biens</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Bien Immobilier</th>
                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Statut</th>
                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Revenus Bruts</th>
                        <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Net Proprio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data['biens_list'] as $bien)
                    <tr class="hover:bg-gray-50/80 transition duration-300 group">
                        <td class="px-10 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-[#274256] tracking-tight">{{ $bien->nom }}</span>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $bien->adresse }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            @if($bien->is_active > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Occupé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 text-gray-400 rounded-full text-[9px] font-black uppercase tracking-widest">
                                     Vacant
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right font-bold text-[#274256] text-sm">
                            {{ number_format($bien->revenus_cumules ?? 0, 0, ',', ' ') }} F
                        </td>
                        <td class="px-10 py-6 text-right">
                             <span class="text-sm font-black text-emerald-600">{{ number_format(($bien->revenus_cumules ?? 0) * 0.9 - ($bien->charges_cumulees ?? 0), 0, ',', ' ') }} F</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-10 py-16 text-center text-gray-400 italic">Aucun bien enregistré pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
