<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header
            title="Rapport des Commissions"
            subtitle="Suivi des honoraires de gestion (10% des encaissements)"
            icon="calculator"
        >
            <x-slot name="actions">
                <x-report-print-button />
            </x-slot>
        </x-section-header>

        <x-report-month-filter id="mois" name="mois" label="Mois" :value="$mois" />

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-kpi-card
                label="Base Commissionnable"
                :value="number_format($baseCommissionnable, 0, ',', ' ')"
                suffix="FCFA"
                icon="money"
                color="blue"
                subtext="Total des loyers encaissés"
            />
            <x-kpi-card
                label="Honoraires de Gestion"
                :value="number_format($commissionHonoraires, 0, ',', ' ')"
                suffix="FCFA"
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
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-2">{{ $loyer->contrat->bien->nom ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $loyer->contrat->locataire->nom ?? '—' }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($loyer->montant, 0, ',', ' ') }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($encaisse, 0, ',', ' ') }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($encaisse * $tauxCommission, 0, ',', ' ') }}</td>
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
