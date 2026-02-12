<div class="h-full flex flex-col gap-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#274256] tracking-tight">
                Tableau de Bord Comptable 📊
            </h1>
            <p class="text-gray-500 mt-2 text-lg">
                Suivi des encaissements et de la trésorerie.
            </p>
        </div>
        <div>
            <button onclick="dashboard.show('paiements'); setTimeout(() => paiSection.openModal('create'), 100);" class="bg-[#274256] text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-blue-900/20 hover:bg-[#1a2e3d] transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Enregistrer un paiement
            </button>
        </div>
    </div>

    <!-- KPIs Comptable -->
    <div id="dashboard-kpi-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
        <!-- Loyers Émis -->
        <div data-show-section="loyers" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 cursor-pointer ontario-card-lift">
            <h3 class="text-gray-500 text-sm font-medium">Facturé ce mois</h3>
            <p class="text-2xl font-bold text-[#274256] mt-1">{{ format_money($data['kpis']['loyers_emis']) }}</p>
        </div>

        <!-- Loyers Payés -->
        <div data-show-section="paiements" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 cursor-pointer ontario-card-lift">
            <h3 class="text-gray-500 text-sm font-medium">Encaissé ce mois</h3>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ format_money($data['kpis']['loyers_payes']) }}</p>
        </div>

        <!-- Impayés -->
        <div data-show-section="loyers" class="bg-white p-6 rounded-2xl shadow-sm border border-red-100 ring-2 ring-red-50 cursor-pointer ontario-card-lift">
            <h3 class="text-red-600 text-sm font-medium">Impayés Global</h3>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ format_money($data['kpis']['total_impaye']) }}</p>
        </div>

         <!-- Taux Recouvrement -->
         <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-gray-500 text-sm font-medium">Taux de Recouvrement</h3>
            <p class="text-2xl font-bold text-[#274256] mt-1">{{ $data['kpis']['taux_recouvrement'] }}%</p>
            <div class="w-full bg-gray-100 rounded-full h-2 mt-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $data['kpis']['taux_recouvrement'] }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Loyers en attente -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#274256]">Priorités Recouvrement</h3>
             </div>
             <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($data['loyers_en_attente'] as $loyer)
                        <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $loyer->contrat->locataire->nom ?? 'Inconnu' }}</p>
                                <p class="text-xs text-gray-500">{{ $loyer->contrat->bien->nom ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 font-bold text-[#274256]">{{ format_money($loyer->montant) }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ ucfirst($loyer->statut) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucun impayé détecté.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
        </div>

        <!-- Derniers Paiements -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#274256]">Derniers Encaissements</h3>
             </div>
             <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($data['derniers_paiements'] as $paiement)
                        <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $paiement->loyer->contrat->locataire->nom ?? 'Inconnu' }}</p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d F Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">+ {{ format_money($paiement->montant) }}</td>
                        </tr>
                         @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">Aucun paiement récent.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
        </div>
    </div>
</div>
