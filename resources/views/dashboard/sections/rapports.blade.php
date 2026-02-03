<div class="h-full flex flex-col gap-6" id="rapports-section-container">
    
    <!-- IFRAME MASQUE POUR LES POST (Anti-Reload Pattern) -->
    <iframe name="rapport_post_target" class="hidden"></iframe>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#274256]">Rapports & Analyses</h2>
            <p class="text-sm text-gray-500 mt-1">Vue d'ensemble de la performance financière et immobilière.</p>
        </div>
        <div class="flex gap-3">
             <form action="{{ route('rapports.mensuel') }}" method="GET" target="_blank" class="flex items-center gap-3">
                 <input type="month" name="mois" value="{{ date('Y-m') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-bold text-[#274256] focus:border-blue-500 outline-none">
                 <button type="submit" class="bg-[#274256] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/10 hover:bg-[#1a2e3d] transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger PDF
                </button>
             </form>
        </div>
    </div>

    <!-- KPIs Financiers (Basés sur les données du rôle, sinon données globales) -->
    @php
        // Récupérer les données selon le rôle ou des defaults safe
        $kpis = $data['kpis'] ?? [];
        $revenuMois = $kpis['revenu_mensuel'] ?? $kpis['loyers_payes'] ?? 0;
        $impayes = $kpis['impayes'] ?? $kpis['total_impaye'] ?? 0;
        $tauxCollecte = $kpis['taux_collecte'] ?? $kpis['taux_recouvrement'] ?? 0;
        $totalLogements = $kpis['total_logements'] ?? App\Models\Bien::count();
        $tauxOccupation = $kpis['taux_occupation'] ?? 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-[#274256] to-[#1a2e3d] p-6 rounded-3xl text-white shadow-xl shadow-blue-900/20">
            <p class="text-[10px] font-black text-blue-200 uppercase tracking-widest mb-2">Revenus du Mois</p>
            <h3 class="text-3xl font-black">{{ number_format($revenuMois, 0, ',', ' ') }} F</h3>
            <p class="text-xs text-blue-300 mt-2 font-medium flex items-center gap-1">
                <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Encaissé
            </p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-red-50 rounded-full transition group-hover:scale-110"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Impayés Cumulés</p>
            <h3 class="text-2xl font-black text-red-500 relative z-10">{{ number_format($impayes, 0, ',', ' ') }} F</h3>
            @if($impayes > 0)
                <p class="text-xs text-gray-400 mt-2 font-medium relative z-10">Action requise</p>
            @else
                <p class="text-xs text-green-500 mt-2 font-medium relative z-10">Aucun retard</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition">
             <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-green-50 rounded-full transition group-hover:scale-110"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Taux de Collecte</p>
            <h3 class="text-2xl font-black text-green-600 relative z-10">{{ $tauxCollecte }}%</h3>
            <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-green-500 h-full rounded-full" style="width: {{ $tauxCollecte }}%"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition">
             <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50 rounded-full transition group-hover:scale-110"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Taux d'Occupation</p>
            <h3 class="text-2xl font-black text-blue-600 relative z-10">{{ $tauxOccupation }}%</h3>
            <p class="text-xs text-gray-400 mt-2 font-medium relative z-10">Sur {{ $totalLogements }} biens</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performance Graph (Simulation CSS) -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <h4 class="text-lg font-bold text-[#274256] mb-6">Évolution des Revenus (6 derniers mois)</h4>
            
            <div class="flex items-end justify-between gap-4 h-64 w-full">
                @php
                    // Données fallback ou réelles si disponibles
                    $revenusData = $data['revenus_par_mois'] ?? [];
                    // Si vide, on simule des données vides pour l'affichage
                    if(empty($revenusData)) {
                        for($i=5; $i>=0; $i--) {
                           $revenusData[] = ['mois' => \Carbon\Carbon::now()->subMonths($i)->format('M'), 'montant' => 0]; 
                        }
                    }
                    
                    // Trouver le max pour l'échelle
                    $maxMontant = collect($revenusData)->max('montant');
                    $maxMontant = $maxMontant > 0 ? $maxMontant : 1;
                @endphp

                @foreach($revenusData as $point)
                    @php
                        $height = ($point['montant'] / $maxMontant) * 100;
                        $height = $height < 5 ? 5 : $height; // Min height visuelle
                    @endphp
                    <div class="flex flex-col items-center flex-1 gap-2 group cursor-pointer">
                        <div class="relative w-full bg-blue-50 rounded-t-xl group-hover:bg-blue-100 transition-all duration-500 flex items-end justify-center overflow-hidden" style="height: 100%;">
                            <div class="w-full bg-[#274256] rounded-t-xl transition-all duration-1000 ease-out group-hover:bg-[#1a2e3d]" style="height: 0%; animation: growBar{{$loop->index}} 1s forwards ease-in-out;"></div>
                            <style> @keyframes growBar{{$loop->index}} { to { height: {{ $height }}%; } } </style>
                            
                            <!-- Tooltip -->
                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                {{ number_format($point['montant'], 0, ',', ' ') }} F
                            </div>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $point['mois'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Répartition par Type -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <h4 class="text-lg font-bold text-[#274256] mb-6">Parc Immobilier</h4>
            
            @php
                $repartition = $data['repartition_type'] ?? [];
                $totalBiensCalc = collect($repartition)->sum('total');
            @endphp

            @if(count($repartition) > 0)
                <div class="space-y-4">
                    @foreach($repartition as $item)
                        @php $pct = $totalBiensCalc > 0 ? round(($item->total / $totalBiensCalc) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="capitalize text-gray-600">{{ $item->type }}</span>
                                <span class="text-[#274256]">{{ $item->total }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <p class="text-sm italic">Aucune donnée sur le parc.</p>
                </div>
            @endif
        </div>
    </div>
</div>
