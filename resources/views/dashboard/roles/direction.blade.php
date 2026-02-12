<div class="h-full flex flex-col gap-8 pb-10">
    <!-- Premium Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-[#274256] tracking-tight">
                Direction Stratégique <span class="text-blue-600">Ontario Group</span>
            </h1>
            <p class="text-gray-500 mt-2 text-lg font-medium"> Pilotage global de la performance et vision 360° du patrimoine. </p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="bg-white border-2 border-gray-100 text-gray-600 px-6 py-3 rounded-2xl text-sm font-bold hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimer Vue
            </button>
            <button onclick="window.previewDoc({url: '{{ route('rapports.mensuel') }}', nom_original: 'Rapport_Mensuel_{{ date('m_Y') }}.pdf', type_label: 'Rapport Stratégique'})" class="bg-[#274256] text-white px-8 py-4 rounded-2xl text-sm font-black shadow-xl shadow-blue-900/20 hover:bg-[#1a2e3d] transition flex items-center gap-2 uppercase tracking-widest">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Rapport Décisionnel
            </button>
        </div>
    </div>

    <!-- KPIs Direction -->
    <div id="dashboard-kpi-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
        <div data-show-section="loyers" class="bg-[#274256] text-white p-8 rounded-[2rem] shadow-2xl relative overflow-hidden group cursor-pointer ontario-card-lift">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-3">Revenus Encaissés (Mensuel)</p>
            <h3 class="text-3xl font-black">{{ number_format($data['kpis']['revenu_mensuel'], 0, ',', ' ') }} <span class="text-xs font-normal">F CFA</span></h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-lg text-[10px] font-black">+ {{ $data['kpis']['taux_collecte'] }}% Collecte</span>
            </div>
        </div>

        <div data-show-section="paiements" class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 group cursor-pointer ontario-card-lift">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Commissions Agence</p>
            <h3 class="text-3xl font-black text-green-600">{{ number_format($data['kpis']['commission_mensuelle'], 0, ',', ' ') }} <span class="text-xs font-normal">F CFA</span></h3>
            <div class="mt-6 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $data['kpis']['taux_collecte'] }}%"></div>
                </div>
                <span class="text-[9px] font-bold text-gray-400">{{ $data['kpis']['taux_collecte'] }}%</span>
            </div>
        </div>

        <div data-show-section="biens" class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 cursor-pointer ontario-card-lift">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Taux d'Occupation</p>
            <h3 class="text-3xl font-black text-[#274256]">{{ $data['kpis']['taux_occupation'] }}%</h3>
            <p class="text-xs text-gray-500 mt-4">{{ $data['kpis']['biens_occupes'] }} sur {{ $data['kpis']['total_logements'] }} unités sous gestion</p>
        </div>

        <div data-show-section="loyers" class="bg-white p-8 rounded-[2rem] shadow-sm border {{ ($data['kpis']['impayes'] ?? 0) > 0 ? 'border-red-50 ring-4 ring-red-50/50' : 'border-gray-100' }} cursor-pointer ontario-card-lift">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-[0.2em] mb-3">Impayés à Recouvrer</p>
            <h3 class="text-3xl font-black text-red-600">{{ number_format($data['kpis']['impayes'], 0, ',', ' ') }} <span class="text-xs font-normal">F CFA</span></h3>
            <div class="mt-6">
                @if(($data['kpis']['impayes'] ?? 0) > 0)
                    <span class="text-[9px] font-black bg-red-100 text-red-700 px-3 py-1 rounded-full uppercase">Action requise urgente</span>
                @else
                    <span class="text-[9px] font-black bg-green-100 text-green-700 px-3 py-1 rounded-full uppercase">Situation Saine</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tier 2: Strategic Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Evolution Chart Area -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-[#274256]">Performance Semestrielle</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Évolution des encaissements sur les 6 derniers mois</p>
                </div>
                <div class="flex gap-2">
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-md bg-[#274256]"></div> <span class="text-[10px] font-black text-gray-400 uppercase">Revenus</span></div>
                </div>
            </div>

            <div class="flex-1 h-64 flex items-end justify-between gap-6 px-4">
                @php
                    $montants = array_column($data['revenus_par_mois'], 'montant');
                    $maxVal = !empty($montants) ? max($montants) : 0;
                    $maxVal = $maxVal ?: 1;
                @endphp
                @foreach(array_reverse($data['revenus_par_mois']) as $moisData)
                    @php $height = ($moisData['montant'] / $maxVal) * 100; @endphp
                    <div class="flex-1 flex flex-col justify-end group cursor-pointer">
                        <div class="relative w-full bg-gray-50 rounded-t-2xl group-hover:bg-blue-50 transition-colors duration-300" style="height: 100%">
                            <div class="absolute bottom-0 left-0 right-0 bg-[#274256] rounded-t-2xl transition-all duration-700 delay-100 group-hover:bg-blue-600" style="height: {{ $height }}%">
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-12 left-1/2 -translate-x-1/2 bg-[#274256] text-white text-[10px] font-black py-2 px-3 rounded-xl whitespace-nowrap shadow-xl z-20 transition-all">
                                    {{ format_money($moisData['montant']) }}
                                </div>
                            </div>

                        </div>
                        <p class="text-center mt-4 text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $moisData['mois'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Portefeuille Insights -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-[#274256] mb-6">Actifs & Valeur</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Masse Locative</span>
                        <span class="text-sm font-black text-[#274256]">{{ format_money($data['kpis']['valeur_portefeuille']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Loyer Moyen Unitaire</span>
                        <span class="text-sm font-black text-[#274256]">{{ format_money($data['kpis']['loyer_moyen']) }}</span>
                    </div>
                    <div class="h-px bg-gray-100 my-4"></div>
                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                        <p class="text-[10px] font-black text-blue-400 uppercase mb-1">Projection Annuelle (Commissions)</p>
                        <p class="text-xl font-black text-blue-700 font-mono">{{ format_money($data['kpis']['projection_annuelle']) }}</p>
                    </div>

                </div>
            </div>

            <div class="bg-[#f8fafc] p-8 rounded-[2.5rem] border border-slate-200">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-6">Typologie du Parc</h3>
                <div class="space-y-4">
                    @foreach($data['repartition_type'] as $type)
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <div class="flex justify-between text-[11px] font-bold text-slate-600 mb-1.5">
                                    <span class="uppercase tracking-wide">{{ str_replace('_', ' ', $type->type) }}</span>
                                    <span>{{ $type->total }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-slate-400" style="width: {{ ($type->total / max(1, $data['kpis']['total_logements'])) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 4: Operational Foresight -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Flows (Already there, but moving into a grid for balance) -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-[#274256]">Journal des Flux récents</h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Dernières entrées de trésorerie validées</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Locataire</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data['derniers_paiements'] as $paiement)
                        <tr class="hover:bg-blue-50/20 transition duration-300 group">
                            <td class="px-8 py-5">
                                <div class="font-bold text-[#274256] text-sm">{{ $paiement->loyer->contrat->locataire->nom ?? 'Inconnu' }}</div>
                                <div class="text-[9px] text-gray-400 font-medium tracking-widest">{{ \Carbon\Carbon::parse($paiement->date_paiement)->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-green-600 text-sm">
                                + {{ format_money($paiement->montant) }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-8 py-10 text-center text-gray-400 italic">Aucun flux récent.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expiring Leases -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between bg-orange-50/10">
                <div>
                    <h3 class="text-xl font-black text-[#274256]">Alerte Vacances <span class="text-orange-500 font-black tracking-tighter"> Imminentes</span></h3>
                    <p class="text-xs text-gray-400 font-medium mt-1">Baux arrivant à expiration (< 60j)</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="p-8 space-y-4">
                @forelse($data['contrats_expiration'] as $con)
                    @php
                        // Utilisation de diffInDays avec arrondi pour éviter les décimales
                        $days = round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($con->date_fin), false));
                        $isOverdue = $days < 0;
                    @endphp
                    <div class="flex items-center gap-5 p-5 rounded-[2rem] border {{ $isOverdue ? 'border-red-100 bg-red-50/30' : 'border-orange-100 bg-orange-50/20' }} group hover:shadow-md transition-all duration-300">
                        <div class="relative w-16 h-16 bg-white rounded-2xl flex flex-col items-center justify-center shadow-sm group-hover:scale-105 transition-transform flex-shrink-0">
                            <span class="text-xl font-black {{ $isOverdue ? 'text-red-600' : 'text-orange-600' }} leading-none">{{ abs($days) }}</span>
                            <span class="text-[7px] font-black uppercase text-gray-400 mt-1">{{ $isOverdue ? 'Retard' : 'Jours' }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-black text-[#274256] truncate">{{ $con->locataire->nom }}</h4>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">{{ $con->bien->nom }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[10px] font-black text-[#274256]">{{ \Carbon\Carbon::parse($con->date_fin)->translatedFormat('d M Y') }}</p>
                            <span class="mt-1 inline-block text-[8px] px-2 py-0.5 {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }} rounded-full font-black uppercase whitespace-nowrap">
                                {{ $isOverdue ? 'Expiré' : 'Fin de bail' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm italic font-medium">Aucune vacance prévue prochainement.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
