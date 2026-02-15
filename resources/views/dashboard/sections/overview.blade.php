<div class="h-full flex flex-col gap-8">
    {{-- Header avec titre et actions Premium --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#274256] tracking-tight flex items-center gap-3">
                <span class="bg-gradient-to-r from-[#274256] to-[#375a76] text-transparent bg-clip-text">VUE D'ENSEMBLE</span>
                <span class="text-2xl animate-bounce">📊</span>
            </h1>
            <p class="text-gray-500 mt-1 text-sm font-medium">Bienvenue sur votre tableau de bord <span class="text-[#cb2d2d] font-bold">Ontario</span>.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.dashboard.refresh()" class="bg-white border-2 border-gray-100 text-gray-400 px-4 py-2.5 rounded-xl text-sm font-bold hover:border-[#274256] hover:text-[#274256] transition-all flex items-center gap-2 shadow-sm group">
                <svg class="w-5 h-5 group-active:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="hidden sm:inline">Actualiser</span>
            </button>
            <a href="{{ route('rapports.mensuel') }}" target="_blank"
               class="bg-white border-2 border-[#274256] text-[#274256] px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#274256] hover:text-white transition-all flex items-center gap-2 shadow-sm hover:shadow-lg transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Rapport PDF
            </a>
            @if(App\Helpers\PermissionHelper::can('biens.create'))
            <button onclick="dashboard.show('biens'); setTimeout(() => bienSection.openModal('create'), 100);"
                    class="bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:shadow-red-900/40 hover:scale-105 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau Bien
            </button>
            @endif
        </div>
    </div>

    {{-- Section: Performance Financière --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- KPIs à gauche (2/3) --}}
        <div class="xl:col-span-2">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#cb2d2d]"></span>
                Indicateurs Clés
            </h2>
            @include('components.kpis-widget')
        </div>

        {{-- Graphique à droite (1/3) --}}
        <div class="xl:col-span-1">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                Analytique
            </h2>
            @include('components.financial-chart')
        </div>
    </div>

    {{-- Section: Activité Locative + Alertes --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#274256]"></span>
                Activité Locative
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne gauche : Stats Parc (2/3) --}}
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6 animate-stagger">

                {{-- Parc Immobilier --}}
                <div data-show-section="biens" onclick="dashboard.show('biens')"
                     class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-gray-200 shadow-sm cursor-pointer h-full">
                     {{-- Watermark --}}
                    <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-32 h-32 text-[#274256]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col h-full justify-between">
                         <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Parc Immobilier</h3>
                        <div class="mt-4">
                            <p class="text-4xl font-black tracking-tighter text-[#274256] tabular-nums">
                                {{ count($data['biens_list'] ?? []) }}
                            </p>
                            <p class="mt-1 text-xs font-medium text-gray-400">Unités gérées</p>
                        </div>
                    </div>
                </div>

                {{-- Taux d'Occupation --}}
                @php
                    $total = count($data['biens_list'] ?? []);
                    $libres = $data['biens_list']->where('statut', 'libre')->count();
                    $rate = $total > 0 ? round((($total - $libres) / $total) * 100) : 0;
                @endphp
                <div data-show-section="biens" onclick="dashboard.show('biens')"
                     class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-gray-200 shadow-sm cursor-pointer h-full">
                    {{-- Watermark --}}
                    <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-32 h-32 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="flex items-start justify-between">
                            <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Occupation</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100">
                                {{ $rate }}%
                            </span>
                        </div>

                        <div class="mt-4">
                            {{-- Gauge visual using SVG for precision or simple bar --}}
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $rate }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs font-medium text-green-600">
                                    {{ $total - $libres }} occupés / {{ $total }}
                                </p>
                                @if(isset($data['taux_occupation_financier']))
                                <span class="text-[10px] font-bold text-gray-400" title="Taux d'Occupation Financier (Facturé / Potentiel)">
                                    Financier: {{ $data['taux_occupation_financier'] }}%
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Disponibilité --}}
                <div data-show-section="biens" onclick="dashboard.show('biens')"
                     class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-red-100 shadow-sm cursor-pointer h-full">
                     {{-- Watermark --}}
                    <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
                        <svg class="w-32 h-32 text-[#cb2d2d]" fill="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col h-full justify-between">
                         <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Disponibles</h3>
                        <div class="mt-4">
                            <p class="text-4xl font-black tracking-tighter text-[#cb2d2d] tabular-nums">
                                {{ $libres }}
                            </p>
                            <p class="mt-1 text-xs font-medium text-[#cb2d2d]">À commercialiser</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne droite : Alertes (1/3) --}}
            <div class="lg:col-span-1 h-full">
                @include('components.alerts-widget')
            </div>
        </div>
    </div>

    {{-- Section activité récente --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Derniers Contrats --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-6 flex flex-col h-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#274256] flex items-center gap-2 uppercase tracking-wide">
                    <div class="w-1 h-6 bg-[#cb2d2d] rounded-full mr-2"></div>
                    Derniers Baux
                </h3>
                <a href="#" data-show-section="contrats" onclick="dashboard.show('contrats')"
                   class="text-xs text-[#cb2d2d] hover:text-[#a82020] hover:underline font-bold uppercase tracking-wider transition-colors">Voir tout →</a>
            </div>
            <div class="space-y-4 flex-1 overflow-y-auto max-h-80 pr-2 custom-scrollbar">
                @forelse(($data['derniers_contrats'] ?? collect())->take(4) as $contrat)
                    <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 bg-gray-50/50 hover:bg-white hover:border-gray-200 hover:shadow-md transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-[#274256] font-bold text-lg group-hover:scale-110 transition-transform ring-1 ring-gray-100">
                            {{ strtoupper(substr($contrat->locataire->nom ?? 'L', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate group-hover:text-[#cb2d2d] transition-colors">{{ $contrat->locataire->nom ?? 'Locataire' }}</h4>
                            <p class="text-xs text-gray-500 truncate flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                {{ $contrat->bien->nom ?? '' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-[#274256]">{{ format_money($contrat->loyer_montant) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400 flex flex-col items-center justify-center h-full">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Aucun contrat récent</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Raccourcis rapides Premium --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-[#274256] mb-6 flex items-center gap-2 uppercase tracking-wide">
                 <div class="w-1 h-6 bg-[#cb2d2d] rounded-full mr-2"></div>
                Accès Rapides
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="#" data-show-section="biens" onclick="dashboard.show('biens')"
                   class="flex flex-col items-center gap-3 p-6 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-white hover:border-[#cb2d2d]/30 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white text-[#cb2d2d] rounded-2xl shadow-sm flex items-center justify-center group-hover:bg-[#cb2d2d] group-hover:text-white group-hover:scale-110 transition-all duration-300 ring-1 ring-gray-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-[#cb2d2d] transition-colors">Gérer les Biens</span>
                </a>
                <a href="#" data-show-section="loyers" onclick="dashboard.show('loyers')"
                   class="flex flex-col items-center gap-3 p-6 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-white hover:border-green-500/30 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white text-green-600 rounded-2xl shadow-sm flex items-center justify-center group-hover:bg-green-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 ring-1 ring-gray-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-green-600 transition-colors">Encaissements</span>
                </a>
                <a href="#" data-show-section="locataires" onclick="dashboard.show('locataires')"
                   class="flex flex-col items-center gap-3 p-6 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-white hover:border-purple-500/30 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white text-purple-600 rounded-2xl shadow-sm flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 ring-1 ring-gray-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-purple-600 transition-colors">Nouveau Locataire</span>
                </a>
                <a href="#" data-show-section="depenses" onclick="dashboard.show('depenses')"
                   class="flex flex-col items-center gap-3 p-6 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-white hover:border-orange-500/30 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white text-orange-600 rounded-2xl shadow-sm flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 ring-1 ring-gray-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition-colors">Dépenses</span>
                </a>
            </div>
        </div>
    </div>
</div>
