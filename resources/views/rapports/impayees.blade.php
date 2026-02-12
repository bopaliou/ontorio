<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header
            title="Rapport des Impayés"
            subtitle="Suivi des arriérés et relances locataires"
            icon="clock"
        >
            <x-slot name="actions">
                <div class="flex items-center gap-3">
                    <form action="{{ route('rapports.impayees') }}" method="GET" class="flex items-center gap-2">
                        <input type="month" name="mois" value="{{ $mois }}" onchange="this.form.submit()" class="bg-white border-gray-200 rounded-xl text-xs font-bold text-gray-700 px-4 py-2 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] transition-all">
                    </form>
                    <a href="{{ route('rapports.impayees.csv', ['mois' => $mois]) }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-black hover:bg-emerald-700 transition shadow-lg shadow-emerald-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        CSV
                    </a>
                    <button type="button" onclick="window.print()" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black hover:bg-black transition shadow-lg shadow-gray-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimer
                    </button>
                </div>
            </x-slot>
        </x-section-header>

        @php
            $totalArrieres = $impayees['en_retard']->sum('montant') + $impayees['partiellement_paye']->sum(fn($l) => $l->reste_a_payer);
            $totalCas = count($impayees['en_retard']) + count($impayees['partiellement_paye']);
        @endphp

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-kpi-card
                label="Total Arriérés"
                :value="format_money($totalArrieres)"
                suffix=""
                icon="money"
                color="red"
                subtext="Montant total à recouvrer"
            />
            <x-kpi-card
                label="Dossiers Critique"
                :value="$totalCas"
                suffix="Locataires"
                icon="users"
                color="gray"
                subtext="Affectés par des impayés"
            />
            <x-kpi-card
                label="Ancienneté Moyenne"
                :value="round($impayees['en_retard']->avg('jours_retard') ?? 0, 0)"
                suffix="Jours"
                icon="clock"
                color="blue"
                subtext="Délai de retard moyen"
            />
        </div>

        {{-- Section 1: Retards Critiques --}}
        <div class="bg-white rounded-3xl border border-red-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-red-50 flex items-center justify-between bg-red-50/30">
                <div>
                    <h3 class="text-lg font-black text-red-900 tracking-tight">Retards Critiques</h3>
                    <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mt-0.5">Loyers échus et non régularisés</p>
                </div>
                <span class="px-4 py-1.5 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-900/20">
                    {{ count($impayees['en_retard']) }} Dossiers
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Locataire</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Bien / Logement</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">Montant Dû</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">Retard</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($impayees['en_retard'] as $loyer)
                            @php
                                $retard = $loyer->jours_retard;
                                $retardColor = $retard > 30 ? 'text-red-600' : 'text-amber-600';
                                $retardBg = $retard > 30 ? 'bg-red-50' : 'bg-amber-50';
                            @endphp
                            <tr class="hover:bg-red-50/10 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm capitalize">{{ $loyer->contrat->locataire->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium tracking-tight">Tél: {{ $loyer->contrat->locataire->telephone }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->bien->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium uppercase tracking-tight">Mois: {{ Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                                </td>
                                <td class="px-8 py-5 text-right font-black text-red-600 text-sm">
                                    {{ format_money($loyer->montant) }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full {{ $retardBg }} {{ $retardColor }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $retard }} jours
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">Aucun retard critique détecté.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 2: Paiements Partiels --}}
        <div class="bg-white rounded-3xl border border-amber-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-amber-50 flex items-center justify-between bg-amber-50/30">
                <div>
                    <h3 class="text-lg font-black text-amber-900 tracking-tight">Reliquats / Partiels</h3>
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mt-0.5">Sommes partiellement versées</p>
                </div>
                <span class="px-4 py-1.5 bg-amber-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-amber-900/20">
                    {{ count($impayees['partiellement_paye']) }} Dossiers
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($impayees['partiellement_paye'] as $loyer)
                            <tr class="hover:bg-amber-50/10 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm capitalize">{{ $loyer->contrat->locataire->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium">Facturé: {{ format_money($loyer->montant) }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->bien->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium uppercase">Mois: {{ Carbon\Carbon::parse($loyer->mois)->translatedFormat('F Y') }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Reste à payer</div>
                                    <div class="font-black text-amber-600 text-sm">{{ format_money($loyer->reste_a_payer) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-12 text-center text-gray-400 italic">Aucun paiement partiel en cours.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 3: Émis Non Échus --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden opacity-70 hover:opacity-100 transition-opacity">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-black text-gray-500 tracking-tight">Facturation en cours</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Émis mais non encore échus</p>
                </div>
                <span class="px-4 py-1.5 bg-gray-100 text-gray-500 rounded-full text-[10px] font-black uppercase tracking-widest">
                    {{ count($impayees['non_echu']) }} Loyers
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($impayees['non_echu'] as $loyer)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-400 text-sm capitalize">{{ $loyer->contrat->locataire->nom }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-400">{{ $loyer->contrat->bien->nom }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="font-bold text-gray-400 text-sm">{{ format_money($loyer->montant) }}</div>
                                    <div class="text-[10px] font-medium text-gray-400">Échéance: {{ $loyer->date_echeance->translatedFormat('d M Y') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-12 text-center text-gray-400 italic">Aucun loyer émis pour cette période.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

