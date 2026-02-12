<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header
            title="Rapport des Commissions"
            subtitle="Suivi des honoraires de gestion (10% des encaissements)"
            icon="calculator"
        >
            <x-slot name="actions">
                <div class="flex items-center gap-3">
                    <form action="{{ route('rapports.commissions') }}" method="GET" class="flex items-center gap-2">
                        <input type="month" name="mois" value="{{ $mois }}" onchange="this.form.submit()" class="bg-white border-gray-200 rounded-xl text-xs font-bold text-gray-700 px-4 py-2 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] transition-all shadow-sm">
                    </form>
                    <button type="button" onclick="window.print()" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black hover:bg-black transition shadow-lg shadow-gray-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimer
                    </button>
                </div>
            </x-slot>
        </x-section-header>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-kpi-card
                label="Base Commissionnable"
                :value="format_money($baseCommissionnable)"
                suffix=""
                icon="money"
                color="blue"
                subtext="Total des loyers encaissés"
            />
            <x-kpi-card
                label="Honoraires de Gestion"
                :value="format_money($commissionHonoraires)"
                suffix=""
                icon="calculator"
                color="green"
                trend="+{{ (int) ($tauxCommission * 100) }}%"
                subtext="Quote-part agence (10%)"
            />
            <x-kpi-card
                label="Nombre d'encaissements"
                :value="$encaissements->count()"
                suffix="Paiements"
                icon="document"
                color="gray"
                subtext="Opérations traitées"
            />
        </div>


        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 text-sm font-bold text-slate-700">Détail des encaissements commissionnables</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-2 text-left">Bien</th>
                            <th class="px-4 py-2 text-left">Locataire</th>
                            <th class="px-4 py-2 text-right">Montant loyer</th>
                            <th class="px-4 py-2 text-right">Montant encaissé</th>
                            <th class="px-4 py-2 text-right">Commission (10%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encaissements as $loyer)
                            @php($encaisse = min($loyer->montant, (float) ($loyer->paiements_sum_montant ?? 0)))
                            <tr class="border-t border-slate-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 font-bold text-gray-900">{{ $loyer->contrat->bien->nom ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $loyer->contrat->locataire->nom ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-500">{{ format_money($loyer->montant, '') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ format_money($encaisse, '') }}</td>
                                <td class="px-4 py-3 text-right font-black text-[#274256]">{{ format_money($encaisse * $tauxCommission, '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-500">Aucun encaissement commissionnable pour ce mois.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 flex items-start gap-4">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-500 shadow-sm shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-[#274256] font-black text-sm uppercase tracking-widest mb-1">Note de calcul</h4>
                <p class="text-xs text-blue-700 font-medium leading-relaxed">
                    Les commissions sont calculées sur la base des loyers effectivement encaissés.
                    Le taux standard appliqué pour Ontario Group est de 10% HT sur le montant principal du loyer.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
