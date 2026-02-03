<div class="h-full flex flex-col gap-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#274256] tracking-tight">Vue d'ensemble 🏠</h1>
            <p class="text-gray-500 mt-2 text-lg">Bienvenue, voici ce qui se passe aujourd'hui.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('rapports.mensuel') }}" target="_blank" class="bg-white border-2 border-[#274256] text-[#274256] px-6 py-3 rounded-xl text-sm font-bold hover:bg-[#274256] hover:text-white transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Rapport Mensuel
            </a>
            @if(App\Helpers\PermissionHelper::can('biens.create'))
            <button onclick="dashboard.show('biens'); setTimeout(() => bienSection.openModal('create'), 100);" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajouter un bien
            </button>
            @endif
        </div>
    </div>

    <!-- Metric Cards: Parc Activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div data-show-section="biens" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 cursor-pointer hover:border-blue-500 transition-colors">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Parc Immobilier</h3>
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ count($data['biens_list'] ?? []) }} <span class="text-base font-normal text-gray-400">unités</span></p>
            <div class="mt-2 text-xs text-gray-400">
                <span class="text-green-600 font-bold">{{ $data['kpis']['contrats_actifs'] ?? 0 }}</span> baux actifs
            </div>
        </div>
        <div data-show-section="biens" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 cursor-pointer hover:border-blue-500 transition-colors">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Taux d'Occupation</h3>
            @php
                $total = count($data['biens_list'] ?? []);
                $libres = $data['biens_list']->where('statut', 'libre')->count();
                $rate = $total > 0 ? round((($total - $libres) / $total) * 100) : 0;
            @endphp
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ $rate }}%</p>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $rate }}%"></div>
            </div>
        </div>
        <div data-show-section="biens" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 cursor-pointer hover:border-blue-500 transition-colors">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Disponibilité</h3>
            <p class="text-3xl font-bold text-orange-600 mt-1">{{ $libres }}</p>
            <p class="mt-2 text-xs text-gray-400">Unités vacantes à louer</p>
        </div>
    </div>

    <!-- Metric Cards: Financial Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Emis (Month) -->
        <div data-show-section="loyers" class="bg-[#274256] p-6 rounded-2xl shadow-lg text-white cursor-pointer hover:bg-[#1a2e3d] transition-colors">
            <h3 class="text-gray-300 text-xs font-semibold uppercase tracking-wider">Total Émis (Ce mois)</h3>
            <p class="text-3xl font-bold mt-2">{{ number_format($data['kpis']['loyers_emis_mois'] ?? 0, 0, ',', ' ') }} F CFA</p>
            <div class="mt-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                <span class="text-xs text-gray-300">Objectif mensuel</span>
            </div>
        </div>

        <!-- Total Payé (Month) -->
        <div data-show-section="loyers" class="bg-white p-6 rounded-2xl shadow-sm border border-green-100 border-l-4 border-l-green-500 cursor-pointer hover:border-green-300 transition-colors">
            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Montant Encaissé</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($data['kpis']['loyers_payes_mois'] ?? 0, 0, ',', ' ') }} F CFA</p>
            @php
                $paidRate = ($data['kpis']['loyers_emis_mois'] ?? 0) > 0 ? round(($data['kpis']['loyers_payes_mois'] / $data['kpis']['loyers_emis_mois']) * 100) : 0;
            @endphp
            <div class="mt-4 text-xs font-medium text-green-700">
                 {{ $paidRate }}% du total émis ce mois
            </div>
        </div>

        <!-- Total en Retard (Global) -->
        <div data-show-section="loyers" class="bg-white p-6 rounded-2xl shadow-sm border {{ ($data['kpis']['total_en_retard'] ?? 0) > 0 ? 'border-red-100 border-l-4 border-l-red-500 hover:border-red-300' : 'border-gray-200 hover:border-blue-500' }} cursor-pointer transition-colors">
            <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Impayés Global</h3>
            <p class="text-3xl font-bold text-[#cb2d2d] mt-2">{{ number_format($data['kpis']['total_en_retard'] ?? 0, 0, ',', ' ') }} F CFA</p>
            <div class="mt-4 flex items-center justify-between">
                @if(($data['kpis']['total_en_retard'] ?? 0) > 0)
                    <span class="text-xs text-red-500 font-semibold italic">Action requise</span>
                @else
                    <span class="text-xs text-green-500 font-semibold italic">À jour</span>
                @endif
                <button data-show-section="loyers" class="text-xs text-[#274256] hover:underline font-bold">Voir détails &rarr;</button>
            </div>
        </div>
    </div>

    <!-- Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-[#274256] mb-6">Derniers Contrats</h3>
            <div class="space-y-4">
                @forelse($data['derniers_contrats'] ?? [] as $contrat)
                    <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition border border-gray-100">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-[#274256]">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900">{{ $contrat->locataire->nom ?? 'Locataire' }}</h4>
                            <p class="text-xs text-gray-500">{{ $contrat->bien->nom ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-[#274256]">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} F CFA</p>
                            <p class="text-[10px] text-gray-400 capitalize">{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">Aucune donnée disponible.</p>
                @endforelse
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 font-sans">
            <h3 class="text-lg font-bold text-[#274256] mb-6">Raccourcis</h3>
            <div class="grid grid-cols-2 gap-4 h-full pb-8">
                <a href="#" data-show-section="biens" class="flex flex-col items-center justify-center p-6 border border-gray-100 rounded-2xl hover:bg-gray-50 transition drop-shadow-sm group">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Parc Immobilier</span>
                </a>
                <a href="#" data-show-section="loyers" class="flex flex-col items-center justify-center p-6 border border-gray-100 rounded-2xl hover:bg-gray-50 transition drop-shadow-sm group">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Suivi Paiements</span>
                </a>
            </div>
        </div>
    </div>
</div>
