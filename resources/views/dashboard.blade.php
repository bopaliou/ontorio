<x-app-layout>
    <div class="h-full flex flex-col gap-8">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#274256] tracking-tight">
                    Vue d'ensemble �
                </h1>
                <p class="text-gray-500 mt-2 text-lg">
                    Bienvenue {{ Auth::user()->name }}, voici ce qui se passe aujourd'hui.
                </p>
            </div>
            @if(App\Helpers\PermissionHelper::can('biens.create'))
            <div>
                <a href="{{ route('logements.create') }}" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Ajouter un bien
                </a>
            </div>
            @endif
        </div>

        @if(isset($data))
            <!-- Metric Cards (Dynamic) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Logements -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-[#274256] bg-opacity-10 rounded-xl flex items-center justify-center text-[#274256]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <h3 class="text-gray-500 text-sm font-medium">Parc Immobilier</h3>
                    <p class="text-3xl font-bold text-[#274256] mt-1">
                        {{ $data['kpis']['total_logements'] ?? 0 }}
                        <span class="text-base font-normal text-gray-400">unités</span>
                    </p>
                </div>

                <!-- Occupation -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    @php
                        $total = $data['kpis']['total_logements'] ?? 0;
                        $libres = $data['kpis']['logements_libres'] ?? 0;
                        $occupied = $total - $libres;
                        $rate = $total > 0 ? round(($occupied / $total) * 100) : 0;
                    @endphp
                    <h3 class="text-gray-500 text-sm font-medium">Taux d'occupation</h3>
                    <p class="text-3xl font-bold text-[#274256] mt-1">{{ $rate }}%</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $libres }} logements libres</p>
                </div>

                <!-- Contrats Actifs -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-gray-500 text-sm font-medium">Contrats Actifs</h3>
                    <p class="text-3xl font-bold text-[#274256] mt-1">
                        {{ $data['kpis']['contrats_actifs'] ?? 0 }}
                    </p>
                </div>

                <!-- Loyers du Mois (Nombre) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-[#cb2d2d] bg-opacity-10 rounded-xl flex items-center justify-center text-[#cb2d2d]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-gray-500 text-sm font-medium">Loyers émis (ce mois)</h3>
                    <p class="text-3xl font-bold text-[#274256] mt-1">
                        {{ $data['kpis']['loyers_mois'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Derniers Contrats List -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                     <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-[#274256]">Derniers Contrats Signés</h3>
                        <a href="{{ route('contrats.index') }}" class="text-sm text-[#cb2d2d] font-medium hover:underline">Voir tout</a>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                                    <th class="px-6 py-4">Immeuble</th>
                                    <th class="px-6 py-4">Locataire</th>
                                    <th class="px-6 py-4">Début</th>
                                    <th class="px-6 py-4">Loyer</th>
                                    <th class="px-6 py-4 text-right">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($data['derniers_contrats'] ?? [] as $contrat)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $contrat->logement->immeuble->nom ?? 'N/A' }} - {{ $contrat->logement->numero ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $contrat->locataire->nom ?? 'Inconnu' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-semibold text-[#274256]">{{ number_format($contrat->loyer_montant, 2) }} €</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Aucun contrat récent.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                     </div>
                </div>

                <!-- Quick Actions or Secondary Info -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold text-[#274256] mb-6">Actions Rapides</h3>
                    <div class="space-y-3">
                        @if(App\Helpers\PermissionHelper::can('contrats.create'))
                        <a href="{{ route('contrats.create') }}" class="block w-full text-center py-3 px-4 border border-[#cb2d2d] text-[#cb2d2d] font-medium rounded-xl hover:bg-red-50 transition">
                            Nouveau Contrat
                        </a>
                        @endif

                        @if(App\Helpers\PermissionHelper::can('locataires.create'))
                        <a href="{{ route('locataires.create') }}" class="block w-full text-center py-3 px-4 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">
                            Ajouter Locataire
                        </a>
                        @endif

                        @if(App\Helpers\PermissionHelper::can('loyers.generate'))
                        <form method="POST" action="{{ route('loyers.genererMois') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-center py-3 px-4 bg-[#274256] text-white font-medium rounded-xl hover:bg-[#1a2e3d] transition">
                                Générer Loyers du Mois
                            </button>
                        </form>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Besoin d'aide ?</h4>
                        <p class="text-xs text-gray-500 mb-4">Consultez la documentation ou contactez le support.</p>
                        <a href="#" class="text-sm text-[#cb2d2d] font-medium hover:underline">Documentation &to;</a>
                    </div>
                </div>
            </div>

        @else
            <!-- Fallback if no data -->
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-yellow-800">
                Données non disponibles. Veuillez vérifier le contrôleur.
            </div>
        @endif

    </div>
</x-app-layout>
