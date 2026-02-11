<div class="h-full flex flex-col gap-8" id="locataires-section-container">

    <div id="loc-view-list" class="loc-sub-view space-y-8">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Locataires',
            'subtitle' => 'Gestion des dossiers locataires et historique.',
            'icon' => 'users',
            'actions' => App\Helpers\PermissionHelper::can('locataires.create')
                ? '<button onclick="locSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Nouveau Dossier
                </button>'
                : ''
        ])

        <!-- KPIs Uniformes -->
        <div id="loc-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', [
                'label' => 'Total Locataires',
                'value' => count($data['locataires_list']),
                'icon' => 'users',
                'color' => 'gray'
            ])
            @include('components.kpi-card', [
                'label' => 'En Location',
                'value' => $data['locataires_list']->where('contrats_count', '>', 0)->count(),
                'icon' => 'home',
                'color' => 'blue'
            ])
            @include('components.kpi-card', [
                'label' => 'Nouveaux (Mois)',
                'value' => $data['locataires_list']->where('created_at', '>=', now()->startOfMonth())->count(),
                'icon' => 'plus',
                'color' => 'green'
            ])
        </div>

        <!-- Table -->
        <div id="loc-table-container">
            <x-data-table :headers="[
                ['label' => 'Ref', 'classes' => 'w-24 text-white/80'],
                ['label' => 'Locataire', 'classes' => 'text-white/80'],
                ['label' => 'Contact', 'classes' => 'text-white/80'],
                ['label' => 'Logement', 'classes' => 'text-white/80'],
                ['label' => 'Actions', 'classes' => 'text-right text-white/80']
            ]" emptyMessage="Aucun locataire enregistré pour le moment.">

                @forelse($data['locataires_list'] as $loc)
                <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs">L-{{ str_pad($loc->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center font-bold text-base group-hover:bg-[#cb2d2d] group-hover:text-white transition-colors">
                                {{ substr($loc->nom, 0, 1) }}
                            </div>
                            <span class="font-bold text-gray-900 text-base capitalize">{{ $loc->nom }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900 font-mono text-sm">{{ $loc->telephone ?? '--' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $loc->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($loc->contrats->isNotEmpty() && $loc->contrats->first()->bien)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                {{ Str::limit($loc->contrats->first()->bien->nom, 20) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs italic pl-2">Aucun contrat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="locSection.showDetails({{ json_encode($loc) }})" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-white rounded-lg transition" title="Voir Dossier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            @if(App\Helpers\PermissionHelper::can('locataires.edit'))
                            <button onclick="locSection.openModal('edit', {{ json_encode($loc) }})" class="p-2 text-gray-400 hover:text-[#cb2d2d] hover:bg-white rounded-lg transition" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endif
                            @if(App\Helpers\PermissionHelper::can('locataires.delete'))
                            <button onclick="locSection.requestDelete({{ $loc->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition" title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    <!-- Géré par le composant -->
                @endforelse

                <x-slot name="mobile">
                    @if(count($data['locataires_list']) > 0)
                        @foreach($data['locataires_list'] as $loc)
                            <x-data-card
                                title="{{ $loc->nom }}"
                                status="{{ $loc->contrats->isNotEmpty() ? 'Loué' : 'Inactif' }}"
                                statusColor="{{ $loc->contrats->isNotEmpty() ? 'green' : 'gray' }}"
                            >
                                <div class="flex flex-col gap-1 text-gray-600">
                                    <div class="text-sm border-b border-gray-100 pb-2 mb-2">Ref: L-{{ str_pad($loc->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span class="text-sm font-medium">{{ $loc->telephone ?? '--' }}</span>
                                    </div>
                                    @if($loc->contrats->isNotEmpty())
                                        <div class="flex items-center gap-2 text-blue-600 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            <span class="text-xs font-bold">{{ Str::limit($loc->contrats->first()->bien->nom, 25) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <x-slot name="actions">
                                    <button onclick="locSection.showDetails({{ json_encode($loc) }})" class="p-3 bg-gray-50 text-gray-500 rounded-lg hover:bg-gray-100" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @if(App\Helpers\PermissionHelper::can('locataires.edit'))
                                    <button onclick="locSection.openModal('edit', {{ json_encode($loc) }})" class="p-3 bg-gray-50 text-gray-500 rounded-lg hover:text-blue-600 hover:bg-gray-100" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endif
                                    @if(App\Helpers\PermissionHelper::can('locataires.delete'))
                                    <button onclick="locSection.requestDelete({{ $loc->id }})" class="p-3 bg-gray-50 text-gray-500 rounded-lg hover:text-red-600 hover:bg-gray-100" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </x-slot>
                            </x-data-card>
                        @endforeach
                    @endif
                </x-slot>
            </x-data-table>
        </div>
    </div>

    <!-- SUB-SECTION: DETAILS -->
    <div id="loc-view-details" class="loc-sub-view hidden space-y-8">
        <div class="flex items-center gap-4">
            <button onclick="locSection.showView('list')" class="p-2 hover:bg-gray-100 rounded-full transition text-gray-600" aria-label="Retour à la liste">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-gray-900">Dossier Locataire</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                 <!-- Main Info Card -->
                 <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-3xl font-bold text-gray-400 mx-auto mb-6 border-4 border-white shadow-sm" id="det-loc-avatar-bg">
                        <span id="det-loc-initials">?</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 capitalize mb-1" id="det-loc-nom">...</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Particulier</p>

                    <div class="mt-8 space-y-5 text-left bg-gray-50/50 p-6 rounded-2xl border border-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <span id="det-loc-tel" class="font-bold text-gray-900 font-mono">...</span>
                        </div>
                        <div class="flex items-center gap-4">
                             <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span id="det-loc-email" class="text-sm font-medium text-gray-600 break-all">...</span>
                        </div>
                        <div class="flex items-start gap-4">
                             <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-400 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0v1m0 0v1m0 1a1 1 0 011 1v1a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a1 1 0 011-1h3m2 0h1a1 1 0 011 1v1a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1a1 1 0 011-1z"/></svg>
                            </div>
                            <span id="det-loc-cni" class="text-sm font-medium text-gray-600 italic">...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <!-- Contracts & History -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">Contrat & Logement</h4>
                    <div id="det-loc-contract-container">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Documents Numérisés -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm" id="det-loc-documents-section">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-2">
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Documents Numérisés</h4>
                        <button onclick="locSection.openDocumentModal()" class="text-[#cb2d2d] text-xs font-bold hover:underline flex items-center gap-1.5 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Ajouter
                        </button>
                    </div>

                    <!-- Liste des documents -->
                    <div id="det-loc-documents-list" class="space-y-3">
                        <!-- Populated by JS -->
                    </div>

                    <!-- État vide -->
                    <div id="det-loc-documents-empty" class="flex flex-col items-center justify-center py-10 text-gray-400 border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/50">
                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-xs font-medium">Aucun document joint (CNI, Contrat signé...)</p>
                        <button onclick="locSection.openDocumentModal()" class="mt-3 text-[#cb2d2d] text-xs font-bold hover:underline">Ajouter un document</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL (ULTRA COMPACT GRID) -->
    <div id="loc-modal-wrapper" class="app-modal-root hidden" aria-labelledby="loc-modal-title" role="dialog" aria-modal="true">
        <div id="loc-modal-overlay" class="app-modal-overlay opacity-0"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) locSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) locSection.closeModal()">
                <div id="loc-modal-container" class="app-modal-panel app-modal-panel-xl opacity-0 scale-95">

                    <!-- Header -->
                    <div class="app-modal-header px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="loc-modal-title" class="text-base font-bold text-gray-900">Nouveau Dossier</h3>
                            <p class="text-[11px] text-gray-500 font-medium">Informations locataire.</p>
                        </div>
                        <button onclick="locSection.closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="loc-main-form" class="p-6 form-stack field-gap">
                        <input type="hidden" name="id" id="loc-input-id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <!-- Nom -->
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="loc-input-nom">Nom Complet</label>
                                <input type="text" name="nom" id="loc-input-nom" required class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-red-500/5 transition-all" placeholder="Ex: Moussa Diop">
                            </div>

                            <!-- CNI -->
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="loc-input-cni">Numéro CNI</label>
                                <input type="text" name="cni" id="loc-input-cni" class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-red-500/5 transition-all" placeholder="ID Document">
                            </div>

                            <!-- Email -->
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="loc-input-email">Email</label>
                                <input type="email" name="email" id="loc-input-email" class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-red-500/5 transition-all" placeholder="email@exemple.com">
                            </div>

                            <!-- Tel -->
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="loc-input-tel">Téléphone</label>
                                <input type="text" name="telephone" id="loc-input-tel" class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-red-500/5 transition-all" placeholder="77 ...">
                            </div>

                            <!-- Adresse -->
                            <div class="col-span-1 md:col-span-2 relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="loc-input-adresse">Infos Complémentaires</label>
                                <textarea name="adresse" id="loc-input-adresse" class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-red-500/5 transition-all resize-none" rows="2" placeholder="Adresse complète..."></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="app-modal-footer pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="locSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="loc-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL (UNCHANGED) -->
    <div id="loc-delete-modal" role="dialog" aria-modal="true" aria-labelledby="loc-delete-modal-title" onclick="if(event.target === this) locSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="loc-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                 <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
            </div>
            <h3 id="loc-delete-modal-title" class="text-xl font-bold text-gray-900 mb-2">Supprimer ce locataire ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Cette action est irréversible. Toutes les données associées (contrats, historique) seront affectées.</p>
            <div class="flex flex-col gap-3">
                <button id="loc-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Supprimer
                </button>
                <button onclick="locSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>

    <!-- DOCUMENT UPLOAD MODAL (ULTRA COMPACT) -->
    <div id="loc-doc-modal" role="dialog" aria-modal="true" aria-labelledby="loc-doc-modal-title" onclick="if(event.target === this) locSection.closeDocumentModal()" class="fixed inset-0 z-[130] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="loc-doc-container" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform scale-95 transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="app-modal-header px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <div>
                    <h3 id="loc-doc-modal-title" class="text-base font-bold text-gray-900">Ajouter un Document</h3>
                    <p class="text-[11px] text-gray-500 font-medium">PDF, JPG, PNG, DOC (max 10 Mo)</p>
                </div>
                <button onclick="locSection.closeDocumentModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition" aria-label="Fermer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="loc-doc-form" class="p-6 form-stack field-gap" enctype="multipart/form-data">
                <input type="hidden" id="doc-locataire-id" name="locataire_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Type de document -->
                    <div class="relative">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1" for="doc-type">Type de Document</label>
                        <div class="relative group">
                            <select name="type" id="doc-type" required class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-red-500/5 transition-all appearance-none cursor-pointer">
                                <option value="">Sélectionner...</option>
                                <option value="cni">Carte d'Identité</option>
                                <option value="contrat_signe">Contrat Signé</option>
                                <option value="attestation">Attestation</option>
                                <option value="justificatif">Justificatif</option>
                                <option value="autre">Autre Document</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Zone de drop fichier (Premium) -->
                    <div class="relative bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 flex items-center justify-center h-20 hover:border-[#cb2d2d] transition-all cursor-pointer group">
                        <input type="file" name="document" id="doc-file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" aria-label="Sélectionner un fichier">

                        <div id="doc-drop-content" class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[#cb2d2d]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ajouter le fichier</span>
                        </div>

                        <div id="doc-file-preview" class="hidden absolute inset-0 bg-white rounded-2xl px-4 flex items-center justify-between border border-green-100">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p id="doc-file-name" class="text-xs font-bold text-gray-900 truncate max-w-[150px]"></p>
                            </div>
                            <button type="button" @click="document.getElementById('doc-file-input').value = ''; document.getElementById('doc-drop-content').classList.remove('hidden'); document.getElementById('doc-file-preview').classList.add('hidden');" class="p-2 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="app-modal-footer pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="locSection.closeDocumentModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                    <button type="submit" id="doc-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Téléverser
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE DOCUMENT CONFIRMATION MODAL -->
    <div id="loc-doc-delete-modal" role="dialog" aria-modal="true" aria-labelledby="loc-doc-delete-modal-title" onclick="if(event.target === this) locSection.closeDocDeleteModal()" class="fixed inset-0 z-[140] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="loc-doc-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5 shadow-sm">
                 <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 id="loc-doc-delete-modal-title" class="text-lg font-bold text-gray-900 mb-2">Supprimer ce document ?</h3>
            <p class="text-sm text-gray-500 mb-6">Cette action est irréversible.</p>
            <div class="flex gap-3">
                <button onclick="locSection.closeDocDeleteModal()" class="flex-1 px-4 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Annuler
                </button>
                <button id="loc-confirm-doc-delete-btn" class="flex-1 px-4 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

</div>

<script>

    // Fonction globale pour l'aperçu des documents
    window.previewDoc = function(doc) {
        if (doc && doc.url) {
            window.open(doc.url, '_blank');
        } else {
            showToast('Impossible d\'ouvrir le document', 'error');
        }
    };

    window.locSection = {
        deleteTargetId: null,
        currentLocataireId: null,
        deleteDocId: null,

        showView: function(viewId) {
            document.querySelectorAll('.loc-sub-view').forEach(el => el.classList.add('hidden'));
            document.getElementById('loc-view-' + viewId).classList.remove('hidden');
        },

        openModal: function(mode, loc = null) {
            const wrapper = document.getElementById('loc-modal-wrapper');
            const overlay = document.getElementById('loc-modal-overlay');
            const container = document.getElementById('loc-modal-container');
            const form = document.getElementById('loc-main-form');
            const title = document.getElementById('loc-modal-title');
            const btn = document.getElementById('loc-submit-btn');

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);

            form.reset();
            document.getElementById('loc-input-id').value = '';

            if(mode === 'create') {
                title.innerText = 'Nouveau Dossier Locataire';
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Créer le dossier';
            } else {
                title.innerText = 'Modifier Locataire';
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Mettre à jour';
                document.getElementById('loc-input-id').value = loc.id;
                document.getElementById('loc-input-nom').value = loc.nom;
                document.getElementById('loc-input-email').value = loc.email;
                document.getElementById('loc-input-tel').value = loc.telephone;
                document.getElementById('loc-input-cni').value = loc.cni || '';
                document.getElementById('loc-input-adresse').value = loc.adresse || '';
            }
            btn.disabled = false;
        },

        closeModal: function() {
            const wrapper = document.getElementById('loc-modal-wrapper');
            const overlay = document.getElementById('loc-modal-overlay');
            const container = document.getElementById('loc-modal-container');

            overlay.classList.add('opacity-0');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');
            window.modalUX?.deactivate(wrapper);

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        showDetails: function(loc) {
            this.currentLocataireId = loc.id;
            document.getElementById('det-loc-nom').innerText = loc.nom;
            document.getElementById('det-loc-initials').innerText = loc.nom.substring(0,1).toUpperCase();
            document.getElementById('det-loc-tel').innerText = loc.telephone || '--';
            document.getElementById('det-loc-email').innerText = loc.email || '--';
            document.getElementById('det-loc-cni').innerText = loc.cni ? 'CNI/Pass: ' + loc.cni : 'CNI non renseignée';

            const container = document.getElementById('det-loc-contract-container');
            if(loc.contrats && loc.contrats.length > 0) {
                const con = loc.contrats[0];
                container.innerHTML = `
                    <div class="flex flex-col gap-4">
                        <div class="bg-gray-50 rounded-2xl p-5 flex items-center gap-5 border border-gray-100">
                             <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-gray-400">
                                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                             </div>
                             <div>
                                 <p class="text-xs font-bold text-gray-400 uppercase mb-0.5">Logement loué</p>
                                 <p class="font-bold text-gray-900 text-lg">${con.bien ? con.bien.nom : 'Bien supprimé'}</p>
                             </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                                 <p class="text-[11px] font-black text-blue-400 uppercase tracking-widest leading-none mb-2">Loyer Actuel</p>
                                 <p class="font-black text-blue-900 text-2xl">${new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(con.loyer_montant)} F</p>
                             </div>
                             <div class="bg-green-50 rounded-2xl p-5 border border-green-100">
                                 <p class="text-[11px] font-black text-green-400 uppercase tracking-widest leading-none mb-2">Statut</p>
                                 <p class="font-black text-green-700 text-lg uppercase">Actif</p>
                             </div>
                        </div>
                    </div>
                `;
            } else {
                container.innerHTML = `<p class="text-sm text-gray-400 italic text-center py-6 bg-gray-50 rounded-2xl">Aucun contrat actif pour ce locataire.</p>`;
            }

            // Charger les documents du locataire
            this.loadDocuments(loc.id);

            this.showView('details');
        },

        // =====================
        // DOCUMENTS MANAGEMENT
        // =====================

        loadDocuments: async function(locataireId) {
            const listContainer = document.getElementById('det-loc-documents-list');
            const emptyState = document.getElementById('det-loc-documents-empty');

            listContainer.innerHTML = '<div class="text-center py-6"><div class="animate-spin w-6 h-6 border-2 border-gray-300 border-t-[#cb2d2d] rounded-full mx-auto"></div><p class="text-xs text-gray-400 mt-2">Chargement...</p></div>';
            emptyState.classList.add('hidden');

            try {
                const response = await fetch(`/locataires/${locataireId}/documents`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();

                if(data.success && data.documents.length > 0) {
                    listContainer.innerHTML = data.documents.map(doc => this.renderDocumentItem(doc)).join('');
                    emptyState.classList.add('hidden');
                } else {
                    listContainer.innerHTML = '';
                    emptyState.classList.remove('hidden');
                }
            } catch(e) {
                console.error('Erreur chargement documents:', e);
                listContainer.innerHTML = '';
                emptyState.classList.remove('hidden');
            }
        },

        renderDocumentItem: function(doc) {
            const iconMap = {
                'cni': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0v1m0 0v1m0 1a1 1 0 011 1v1a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a1 1 0 011-1h3m2 0h1a1 1 0 011 1v1a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1a1 1 0 011-1z"/>',
                'contrat_signe': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                'attestation': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'justificatif': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                'autre': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'
            };

            const colorMap = {
                'cni': 'bg-blue-50 text-blue-500 border-blue-100',
                'contrat_signe': 'bg-green-50 text-green-500 border-green-100',
                'attestation': 'bg-purple-50 text-purple-500 border-purple-100',
                'justificatif': 'bg-amber-50 text-amber-500 border-amber-100',
                'autre': 'bg-gray-50 text-gray-500 border-gray-100'
            };

            const icon = iconMap[doc.type] || iconMap['autre'];
            const color = colorMap[doc.type] || colorMap['autre'];

            return `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-100 transition group">
                    <div class="w-10 h-10 ${color} border rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">${doc.nom_original}</p>
                        <p class="text-xs text-gray-400">${doc.type_label} • ${doc.created_at}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="window.previewDoc(${JSON.stringify(doc).replace(/"/g, '&quot;')})" class="p-2 text-[#cb2d2d] hover:bg-red-50 rounded-lg transition" title="Aperçu">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button onclick="locSection.requestDocDelete(${doc.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition" title="Supprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            `;
        },

        openDocumentModal: function() {
            if (!this.currentLocataireId) {
                console.error('Aucun locataire sélectionné');
                return;
            }

            const modal = document.getElementById('loc-doc-modal');
            const container = document.getElementById('loc-doc-container');
            const form = document.getElementById('loc-doc-form');

            form.reset();
            this.clearFilePreview();
            document.getElementById('doc-locataire-id').value = this.currentLocataireId;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDocumentModal: function() {
            const modal = document.getElementById('loc-doc-modal');
            const container = document.getElementById('loc-doc-container');

            modal.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        },

        clearFile: function(event) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('doc-file-input').value = '';
            this.clearFilePreview();
        },

        clearFilePreview: function() {
            document.getElementById('doc-drop-content').classList.remove('hidden');
            document.getElementById('doc-file-preview').classList.add('hidden');
        },

        showFilePreview: function(file) {
            document.getElementById('doc-drop-content').classList.add('hidden');
            document.getElementById('doc-file-preview').classList.remove('hidden');
            document.getElementById('doc-file-name').textContent = file.name;
            document.getElementById('doc-file-size').textContent = this.formatFileSize(file.size);
        },

        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 octets';
            const k = 1024;
            const sizes = ['octets', 'Ko', 'Mo', 'Go'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        uploadDocument: async function() {
            const form = document.getElementById('loc-doc-form');
            const btn = document.getElementById('doc-submit-btn');
            const originalHtml = btn.innerHTML;
            const locataireId = document.getElementById('doc-locataire-id').value;

            btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Téléversement...';
            btn.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch(`/locataires/${locataireId}/documents`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message || 'Document ajouté avec succès', 'success');
                    this.closeDocumentModal();
                    this.loadDocuments(locataireId);
                } else {
                    showToast(data.message || 'Erreur lors de l\'upload', 'error');
                }
            } catch (e) {
                console.error('Erreur upload:', e);
                showToast('Erreur serveur lors de l\'upload', 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        },

        requestDocDelete: function(docId) {
            this.deleteDocId = docId;
            const modal = document.getElementById('loc-doc-delete-modal');
            const container = document.getElementById('loc-doc-delete-container');

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDocDeleteModal: function() {
            const modal = document.getElementById('loc-doc-delete-modal');
            const container = document.getElementById('loc-doc-delete-container');

            modal.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                this.deleteDocId = null;
            }, 300);
        },

        executeDocDelete: async function() {
            if (!this.deleteDocId) return;

            const btn = document.getElementById('loc-confirm-doc-delete-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Suppression...';
            btn.disabled = true;

            try {
                const response = await fetch(`/documents/${this.deleteDocId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Document supprimé', 'success');
                    this.loadDocuments(this.currentLocataireId);
                } else {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                }
            } catch (e) {
                console.error('Erreur suppression:', e);
                showToast('Erreur serveur', 'error');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
                this.closeDocDeleteModal();
            }
        },

        // =====================
        // LOCATAIRE DELETE
        // =====================

        requestDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('loc-delete-modal');
            const container = document.getElementById('loc-delete-container');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('loc-delete-modal');
            const container = document.getElementById('loc-delete-container');

            modal.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                this.deleteTargetId = null;
            }, 300);
        },

        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('loc-confirm-delete-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Traitement...';
            btn.disabled = true;

            try {
                const response = await fetch(`/locataires/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if(data.success) {
                    showToast('Locataire supprimé', 'success');
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
                this.closeDeleteModal();
            }
        }
    };

    // Event Listeners
    document.getElementById('loc-confirm-delete-btn').addEventListener('click', function() {
        locSection.executeDelete();
    });

    document.getElementById('loc-confirm-doc-delete-btn').addEventListener('click', function() {
        locSection.executeDocDelete();
    });

    document.getElementById('doc-file-input').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            locSection.showFilePreview(this.files[0]);
        }
    });

    document.getElementById('loc-doc-form').addEventListener('submit', function(e) {
        e.preventDefault();
        locSection.uploadDocument();
    });

    document.getElementById('loc-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('loc-submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...';
        btn.disabled = true;

        const formData = new FormData(this);
        const id = document.getElementById('loc-input-id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/dashboard/locataires/${id}` : `{{ route('locataires.store') }}`;

        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonData)
            });

            const data = await response.json();

                if(response.ok && data.success) {
                    showToast(data.message || 'Succès', 'success');
                    locSection.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                showToast(data.message || 'Erreur de validation', 'error');
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur serveur', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>
