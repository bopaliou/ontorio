<div class="h-full flex flex-col gap-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#274256] tracking-tight">
                Gestion Immobilière 🏠
            </h1>
            <p class="text-gray-500 mt-2 text-lg">
                Vue d'ensemble opérationnelle du parc.
            </p>
        </div>
        <div>
            <a href="{{ route('logements.create') }}" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajouter un bien
            </a>
        </div>
    </div>

    <!-- KPIs Gestionnaire -->
    <div id="dashboard-kpi-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
        <!-- Total Logements -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 ontario-card-lift transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#274256] bg-opacity-10 rounded-xl flex items-center justify-center text-[#274256]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Parc Immobilier</h3>
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ $data['kpis']['total_logements'] }} <span class="text-base font-normal text-gray-400">unités</span></p>
        </div>

        <!-- Logements Libres -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 ontario-card-lift transition-all">
             <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Logements Vacants</h3>
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ $data['kpis']['logements_libres'] }} <span class="text-base font-normal text-gray-400">libres</span></p>
        </div>

        <!-- Contrats Actifs -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 ontario-card-lift transition-all">
             <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Contrats Actifs</h3>
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ $data['kpis']['contrats_actifs'] }}</p>
        </div>

        <!-- Loyers émis -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 ontario-card-lift transition-all">
             <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#cb2d2d] bg-opacity-10 rounded-xl flex items-center justify-center text-[#cb2d2d]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-4 4 4 4-4 4 4z"/></svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Loyers du mois</h3>
            <p class="text-3xl font-bold text-[#274256] mt-1">{{ $data['kpis']['loyers_mois'] }}</p>
        </div>
    </div>

    <!-- Derniers Contrats -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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
                        <th class="px-6 py-4">Date Début</th>
                        <th class="px-6 py-4">Loyer</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($data['derniers_contrats'] as $contrat)
                    <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $contrat->logement->immeuble->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $contrat->locataire->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $contrat->date_debut }}</td>
                        <td class="px-6 py-4 font-bold text-[#274256]">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} F CFA</td>
                        <td class="px-6 py-4 text-right">
                           <a href="{{ route('contrats.edit', $contrat->id) }}" class="text-gray-400 hover:text-[#cb2d2d]">Modifier</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun contrat à afficher.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
