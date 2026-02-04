<div class="h-full flex flex-col gap-6 animate-stagger">
    {{-- Header avec titre et actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#274256] tracking-tight flex items-center gap-3">
                Vue d'ensemble 
                <span class="text-2xl animate-bounce">🏠</span>
            </h1>
            <p class="text-gray-500 mt-2 text-lg">Bienvenue, voici ce qui se passe aujourd'hui.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('rapports.mensuel') }}" target="_blank" 
               class="bg-white border-2 border-[#274256] text-[#274256] px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#274256] hover:text-white transition-all flex items-center gap-2 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Rapport PDF
            </a>
            @if(App\Helpers\PermissionHelper::can('biens.create'))
            <button onclick="dashboard.show('biens'); setTimeout(() => bienSection.openModal('create'), 100);" 
                    class="bg-[#cb2d2d] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-[#a82020] hover:shadow-xl hover:scale-[1.02] transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau Bien
            </button>
            @endif
        </div>
    </div>

    {{-- KPIs Dynamiques --}}
    @include('components.kpis-widget')

    {{-- Section principale : Stats Parc + Alertes --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne gauche : Stats Parc (2/3) --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 animate-stagger">
            {{-- Parc Immobilier --}}
            <div data-show-section="biens" onclick="dashboard.show('biens')"
                 class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 cursor-pointer ontario-card-lift group">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Parc Immobilier</h3>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-[#274256]">{{ count($data['biens_list'] ?? []) }}</p>
                <p class="text-xs text-gray-400 mt-1">unités gérées</p>
            </div>

            {{-- Taux d'Occupation --}}
            @php
                $total = count($data['biens_list'] ?? []);
                $libres = $data['biens_list']->where('statut', 'libre')->count();
                $rate = $total > 0 ? round((($total - $libres) / $total) * 100) : 0;
            @endphp
            <div data-show-section="biens" onclick="dashboard.show('biens')"
                 class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 cursor-pointer ontario-card-lift group">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Taux Occupation</h3>
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-green-600">{{ $rate }}%</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                    <div class="bg-green-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $rate }}%"></div>
                </div>
            </div>

            {{-- Disponibilité --}}
            <div data-show-section="biens" onclick="dashboard.show('biens')"
                 class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 cursor-pointer ontario-card-lift group">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Disponibles</h3>
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-orange-600">{{ $libres }}</p>
                <p class="text-xs text-gray-400 mt-1">unités à louer</p>
            </div>
        </div>

        {{-- Colonne droite : Alertes (1/3) --}}
        <div class="lg:col-span-1">
            @include('components.alerts-widget')
        </div>
    </div>

    {{-- Section activité récente --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Derniers Contrats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-[#274256] flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Derniers Baux
                </h3>
                <a href="#" data-show-section="contrats" onclick="dashboard.show('contrats')" 
                   class="text-xs text-blue-600 hover:underline font-medium">Voir tout →</a>
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse(($data['derniers_contrats'] ?? collect())->take(4) as $contrat)
                    <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 ontario-card-lift">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ strtoupper(substr($contrat->locataire->nom ?? 'L', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $contrat->locataire->nom ?? 'Locataire' }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $contrat->bien->nom ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-[#274256]">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} F</p>
                            <p class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">Aucun contrat récent</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        {{-- Raccourcis rapides --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-[#274256] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Accès Rapides
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="#" data-show-section="biens" onclick="dashboard.show('biens')" 
                   class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl ontario-card-lift group">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Biens</span>
                </a>
                <a href="#" data-show-section="loyers" onclick="dashboard.show('loyers')"
                   class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl ontario-card-lift group">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Loyers</span>
                </a>
                <a href="#" data-show-section="locataires" onclick="dashboard.show('locataires')"
                   class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl ontario-card-lift group">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Locataires</span>
                </a>
                <a href="#" data-show-section="depenses" onclick="dashboard.show('depenses')"
                   class="flex items-center gap-3 p-4 border border-gray-100 rounded-xl ontario-card-lift group">
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Dépenses</span>
                </a>
            </div>
        </div>
    </div>
</div>

