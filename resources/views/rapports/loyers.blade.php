<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <x-section-header
            title="Rapport des Loyers"
            subtitle="Analyse détaillée des encaissements et performances financières"
            icon="money"
        >
            <x-slot name="actions">
                <form action="{{ route('rapports.loyers') }}" method="GET" class="flex items-center gap-3">
                    <input type="month" name="mois" value="{{ $mois }}" onchange="this.form.submit()"
                           class="bg-white border-gray-200 rounded-xl text-sm font-bold text-gray-700 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] shadow-sm">

                    <a href="{{ route('rapports.loyers.csv', ['mois' => $mois]) }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-black hover:bg-emerald-700 transition shadow-lg shadow-emerald-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        CSV
                    </a>

                    <button type="button" onclick="window.print()" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black hover:bg-black transition shadow-lg shadow-gray-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimer
                    </button>
                </x-slot>
        </x-section-header>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-kpi-card
                label="Total Encaissé"
                :value="format_money($data['loyers_encaisses'])"
                suffix=""
                icon="money"
                color="green"
                trend="+8.2%"
                subtext="Ce mois-ci"
            />
            <x-kpi-card
                label="Taux de Recouvrement"
                :value="$data['taux_recouvrement']"
                suffix="%"
                icon="chart"
                color="blue"
                :trendUp="$data['taux_recouvrement'] > 80"
                subtext="Moyenne globale"
            />
            <x-kpi-card
                label="Loyers Impayés"
                :value="format_money($data['arrieres_total'])"
                suffix=""
                icon="clock"
                color="red"
                trend="-5%"
                :trendUp="false"
                subtext="Total à recouvrer"
            />
            <x-kpi-card
                label="Loyers Émis"
                :value="count($loyers)"
                suffix="Termes"
                icon="document"
                color="gray"
                subtext="Pour le mois sélectionné"
            />
        </div>

        {{-- Graphique d'évolution --}}
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm overflow-hidden relative group">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-[#274256] tracking-tight">Évolution des encaissements</h3>
                    <p class="text-xs text-gray-500 font-medium">Historique des 6 derniers mois</p>
                </div>
            </div>
            <div id="encaissements-chart" class="w-full h-80"></div>
        </div>

        {{-- Liste des loyers --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-black text-[#274256] tracking-tight">Détail des Loyers - {{ Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</h3>
                <span class="px-4 py-1.5 bg-white border border-gray-200 rounded-full text-[10px] font-black uppercase tracking-widest text-gray-500 shadow-sm">
                    {{ count($loyers) }} Enregistrements
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Bien</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Locataire</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">Montant</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Statut</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">Date Échéance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($loyers as $loyer)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->bien->nom }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium uppercase tracking-tight">{{ $loyer->contrat->bien->type }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 text-sm">{{ $loyer->contrat->locataire->nom_complet }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium">Contrat #{{ $loyer->contrat->id }}</div>
                                </td>
                                <td class="px-8 py-5 text-right font-black text-gray-900 text-sm">
                                    {{ format_money($loyer->montant) }}
                                </td>
                                <td class="px-8 py-5">
                                    @php
                                        $statusConfig = [
                                            'payé' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Payé'],
                                            'partiel' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'Partiel'],
                                            'en_retard' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'En retard'],
                                            'émis' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'label' => 'Émis'],
                                        ];
                                        $s = $statusConfig[$loyer->statut] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500', 'label' => $loyer->statut];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $s['bg'] }} {{ $s['text'] }} text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                        {{ $s['label'] }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-600">{{ $loyer->date_echeance->format('d/m/Y') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- ApexCharts bundled via Vite (app.js) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);

            const options = {
                series: [{
                    name: 'Encaissements',
                    data: chartData.encaissements
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#cb2d2d'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 4
                },
                xaxis: {
                    categories: chartData.labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#94a3b8', fontWeight: 600 }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) { return (val / 1000000).toFixed(1) + 'M'; },
                        style: { colors: '#94a3b8', fontWeight: 600 }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4
                },
                dataLabels: { enabled: false },
                tooltip: {
                    style: { fontSize: '12px', fontFamily: 'Inter' },
                    y: {
                        formatter: function(val) { return val.toLocaleString() + ' FCFA'; }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#encaissements-chart"), options);
            chart.render();
        });
    </script>
    @endpush
</x-app-layout>
