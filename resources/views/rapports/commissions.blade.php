<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header 
            title="Rapport des Commissions" 
            subtitle="Suivi des honoraires de gestion (10% des encaissements)"
            icon="calculator"
        >
            <x-slot name="actions">
                <button type="button" onclick="window.print()" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black hover:bg-black transition shadow-lg shadow-gray-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Exporter
                </button>
            </x-slot>
        </x-section-header>

        {{-- Logic calculation (Mockup for now as we use $data from stats service) --}}
        @php
            $totalEncaisse = $data['loyers_encaisses'];
            $commissionHonoraires = $totalEncaisse * 0.10;
        @endphp

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-kpi-card 
                label="Base Commissionnable" 
                :value="number_format($totalEncaisse, 0, ',', ' ')" 
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
                trend="+10%"
                subtext="Quote-part agence (10%)"
            />
            <x-kpi-card 
                label="Nombre d'encaissements" 
                :value="$data['nb_payes'] ?? 0" 
                suffix="Paiements"
                icon="document"
                color="gray"
                subtext="Opérations traitées"
            />
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
