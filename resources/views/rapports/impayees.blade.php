<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header
            title="Rapport des Impayés"
            subtitle="Suivi des arriérés et relances locataires"
            icon="clock"
        >
            <x-slot name="actions">
                <button type="button" onclick="window.print()" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black hover:bg-black transition shadow-lg shadow-gray-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Exporter
                </button>
            </x-slot>
        </x-section-header>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-kpi-card
                label="Total Arriérés"
                :value="number_format($impayees->sum('montant'), 0, ',', ' ')"
                suffix="FCFA"
                icon="money"
                color="red"
                subtext="Montant total à recouvrer"
            />
            <x-kpi-card
                label="Dossiers en retard"
                :value="count($impayees)"
                suffix="Locataires"
                icon="users"
                color="gray"
                subtext="Affectés par des impayés"
            />
            <x-kpi-card
                label="Ancienneté Moyenne"
                :value="round($impayees->avg('jours_retard'), 0)"
                suffix="Jours"
                icon="clock"
                color="blue"
                subtext="Délai de retard moyen"
            />
        </div>

        {{-- Liste des impayés --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-black text-[#274256] tracking-tight">Liste des Arriérés</h3>
                <span class="px-4 py-1.5 bg-red-50 text-red-700 border border-red-100 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                    {{ count($impayees) }} Cas critiques
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Locataire</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Bien / Logement</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">Montant Dû</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Mois</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Retard</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($impayees as $loyer)
                            @php
                                $retard = $loyer->jours_retard;
                                $retardColor = $retard > 30 ? 'text-red-600' : ($retard > 15 ? 'text-amber-600' : 'text-gray-600');
                                $retardBg = $retard > 30 ? 'bg-red-50' : ($retard > 15 ? 'bg-amber-50' : 'bg-gray-50');
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->locataire->nom_complet }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium tracking-tight">Tél: {{ $loyer->contrat->locataire->telephone }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->bien->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium uppercase tracking-tight">{{ $loyer->contrat->bien->adresse }}</div>
                                </td>
                                <td class="px-8 py-5 text-right font-black text-red-600 text-sm">
                                    {{ number_format($loyer->montant, 0, ',', ' ') }} <span class="text-[10px] font-bold ml-0.5">F</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-600">{{ Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full {{ $retardBg }} {{ $retardColor }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $retard }} jours de retard
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
