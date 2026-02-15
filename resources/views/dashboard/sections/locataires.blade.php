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

        <div id="loc-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', ['label' => 'Total Locataires', 'value' => count($data['locataires_list']), 'icon' => 'users', 'color' => 'gray'])
            @include('components.kpi-card', ['label' => 'En Location', 'value' => $data['locataires_list']->where('contrats_count', '>', 0)->count(), 'icon' => 'home', 'color' => 'blue'])
            @include('components.kpi-card', ['label' => 'Nouveaux (Mois)', 'value' => $data['locataires_list']->where('created_at', '>=', now()->startOfMonth())->count(), 'icon' => 'plus', 'color' => 'green'])
        </div>

        <div id="loc-table-container">
            <x-data-table :headers="[['label' => 'Ref', 'classes' => 'text-white'], ['label' => 'Locataire', 'classes' => 'text-white'], ['label' => 'Contact', 'classes' => 'text-white'], ['label' => 'Logement', 'classes' => 'text-white'], ['label' => 'Actions', 'classes' => 'text-right text-white']]" emptyMessage="Aucun locataire.">
                @forelse($data['locataires_list'] as $loc)
                <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs text-left">L-{{ str_pad($loc->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-left"><div class="flex items-center gap-4"><div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center font-bold">{{ substr($loc->nom, 0, 1) }}</div><span class="font-bold text-gray-900 capitalize">{{ $loc->nom }}</span></div></td>
                    <td class="px-6 py-4 text-left"><div class="text-gray-900 font-mono text-sm">{{ $loc->telephone ?? '--' }}</div><div class="text-xs text-gray-400">{{ $loc->email }}</div></td>
                    <td class="px-6 py-4 text-left">@if($loc->contrats->isNotEmpty()) <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">{{ Str::limit($loc->contrats->first()->bien->nom, 20) }}</span> @else <span class="text-gray-400 text-xs italic">Aucun contrat</span> @endif</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="locSection.showDetails({{ json_encode($loc) }})" class="group flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-900 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Voir Dossier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Voir</span>
                            </button>
                            @if(App\Helpers\PermissionHelper::can('locataires.edit'))
                            <button onclick="locSection.openModal('edit', {{ json_encode($loc) }})" class="group flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Modifier</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </x-data-table>
        </div>
    </div>

    <!-- SUB-SECTION: DETAILS -->
    <div id="loc-view-details" class="loc-sub-view hidden space-y-8 animate-fade-in text-left">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <button onclick="locSection.showView('list')" class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 rounded-xl transition text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>
                <div><h2 class="text-xl font-black text-[#1a2e3d]">Dossier Locataire</h2><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5" id="det-loc-ref">Réf: L-000</p></div>
            </div>
            <div class="flex gap-2">
                @if(App\Helpers\PermissionHelper::can('locataires.edit'))
                <button onclick="locSection.openModal('edit', locSection.currentLoc)" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-100 transition">Modifier</button>
                @endif
                @if(App\Helpers\PermissionHelper::can('locataires.delete'))
                <button onclick="locSection.confirmDelete(locSection.currentLoc)" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition">Supprimer</button>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                 <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm text-center">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 border-4 border-white shadow-sm" id="det-loc-avatar-bg"><span id="det-loc-initials">?</span></div>
                    <h3 class="text-2xl font-black text-gray-900 capitalize mb-1" id="det-loc-nom">...</h3>
                    <div class="mt-8 space-y-5 text-left bg-gray-50/50 p-6 rounded-2xl border border-gray-50">
                        <div class="flex items-center gap-4"><svg class="w-4 h-4 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><span id="det-loc-tel" class="font-bold text-gray-900 font-mono">...</span></div>
                        <div class="flex items-center gap-4"><svg class="w-4 h-4 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span id="det-loc-email" class="text-sm font-medium text-gray-600 break-all">...</span></div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm"><h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">Contrat & Logement</h4><div id="det-loc-contract-container"></div></div>
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-2"><h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Pièces Jointes</h4><button onclick="locSection.openDocumentModal()" class="text-[#cb2d2d] text-xs font-bold hover:underline">+ Ajouter</button></div>
                    <div id="det-loc-documents-list" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- MODAL: CREATE/EDIT (ROUGE ONTARIO) -->
    <div id="loc-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="loc-modal-title" role="dialog" aria-modal="true">
        <div id="loc-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) locSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loc-modal-container" class="app-modal-panel max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300 my-8 min-h-[600px]">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="loc-modal-title" class="text-lg font-black text-white">Dossier Ontario Group</h3>
                        <button onclick="locSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="loc-main-form" onsubmit="locSection.submitForm(event)" class="p-8">
                        <input type="hidden" name="id" id="loc-input-id">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <!-- Nom & Prénom -->
                            <div class="sm:col-span-2">
                                <label class="ontario-label" for="loc-input-nom">Nom Complet <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <input type="text" name="nom" id="loc-input-nom" required class="ontario-input pl-10" placeholder="Ex: Amadou Diallo">
                                </div>
                            </div>

                            <!-- Email & Téléphone -->
                            <div>
                                <label class="ontario-label" for="loc-input-email">Email <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="email" name="email" id="loc-input-email" required class="ontario-input pl-10" placeholder="Ex: client@exemple.com">
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label" for="loc-input-tel">Téléphone <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <input type="text" name="telephone" id="loc-input-tel" required class="ontario-input pl-10" placeholder="Ex: +221 77 000 00 00">
                                </div>
                            </div>

                            <!-- CNI -->
                            <div class="sm:col-span-2">
                                <label class="ontario-label" for="loc-input-id-card">N° Pièce d'Identité <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    </div>
                                    <input type="text" name="pieces_identite" id="loc-input-id-card" required class="ontario-input pl-10 font-bold tracking-wider" placeholder="Ex: 1234567890123">
                                </div>
                            </div>

                            <!-- Profession & Revenus -->
                            <div class="col-span-12 md:col-span-6">
                                <label class="ontario-label" for="loc-input-profession">Profession</label>
                                <input type="text" name="profession" id="loc-input-profession" class="ontario-input" placeholder="Ex: Comptable">
                            </div>

                            <div class="col-span-12 md:col-span-6">
                                <label class="ontario-label" for="loc-input-revenus">Revenus mensuels</label>
                                <input type="number" name="revenus_mensuels" id="loc-input-revenus" class="ontario-input" placeholder="0">
                            </div>

                            <!-- Adresse -->
                            <div class="col-span-12">
                                <label class="ontario-label" for="loc-input-adresse">Adresse Complète</label>
                                <textarea name="adresse" id="loc-input-adresse" rows="2" class="ontario-input resize-none" placeholder="Adresse du locataire..."></textarea>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <button type="button" onclick="locSection.closeModal()" class="ontario-btn ontario-btn-secondary">
                                Annuler
                            </button>
                            <button type="submit" id="loc-submit-btn" class="ontario-btn ontario-btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE (ROUGE ONTARIO) -->
    <div id="loc-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="loc-delete-modal-title" role="dialog" aria-modal="true">
        <div id="loc-delete-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) locSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loc-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 class="text-lg font-black text-white">Supprimer le locataire ?</h3>
                        <button onclick="locSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-loc-name" class="text-sm text-gray-900 mb-2 font-black uppercase tracking-tight"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">Toutes les données associées seront supprimées. Cette action est irréversible.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="locSection.executeDelete()" id="loc-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer la suppression</button>
                            <button onclick="locSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DOCUMENTS -->
    <div id="loc-doc-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;">
        <div id="loc-doc-modal-overlay" class="app-modal-overlay opacity-0"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) locSection.closeDocumentModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loc-doc-container" class="app-modal-panel max-w-lg w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 class="text-lg font-black text-white">Nouveau Document</h3>
                        <button onclick="locSection.closeDocumentModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="loc-doc-form" onsubmit="locSection.submitDocForm(event)" class="p-8 space-y-6 text-left" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="doc-locataire-id" name="locataire_id">
                        <div class="space-y-4">
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Type de document</label>
                                <select name="type" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 outline-none focus:border-[#cb2d2d] transition-all">
                                    <option value="cni">CNI / Passeport</option><option value="contrat_signe">Contrat Signé</option><option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fichier</label>
                                <input type="file" name="document" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-red-50 file:text-[#cb2d2d] hover:file:bg-red-100 file:uppercase file:tracking-widest">
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="doc-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#a82020] transition shadow-lg text-xs uppercase tracking-widest">Téléverser</button>
                            <button type="button" onclick="locSection.closeDocumentModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.locSection = {
        deleteTargetId: null, currentLoc: null,
        showView: function(v) { document.querySelectorAll('.loc-sub-view').forEach(x => x.classList.add('hidden')); document.getElementById('loc-view-'+v)?.classList.remove('hidden'); },
        showDetails: function(loc) {
             this.currentLoc = loc;
             document.getElementById('det-loc-nom').textContent = loc.nom;
             document.getElementById('det-loc-ref').textContent = 'Réf: L-' + String(loc.id).padStart(3, '0');
             document.getElementById('det-loc-initials').textContent = loc.nom ? loc.nom.charAt(0).toUpperCase() : '?';
             document.getElementById('det-loc-tel').textContent = loc.telephone || '-';
             document.getElementById('det-loc-email').textContent = loc.email || '-';
             document.getElementById('det-loc-avatar-bg').className = `w-24 h-24 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 border-4 border-white shadow-sm bg-blue-100 text-blue-600`;
             const cc = document.getElementById('det-loc-contract-container');
             cc.innerHTML = (loc.contrats && loc.contrats.length > 0) ? loc.contrats.map(c => `<div class="bg-gray-50 rounded-2xl p-5 mb-3 border border-gray-100 text-left"><h5 class="font-bold text-gray-900">${c.bien ? c.bien.nom : 'Bien inconnu'}</h5><p class="text-xs text-gray-500">Loyer: ${new Intl.NumberFormat().format(c.loyer_montant)} F</p></div>`).join('') : '<p class="text-gray-400 italic">Aucun contrat</p>';
             const dl = document.getElementById('det-loc-documents-list');
             dl.innerHTML = (loc.documents && loc.documents.length > 0) ? loc.documents.map(d => {
                 const docData = JSON.stringify(d).replace(/'/g, "&#39;");
                 return `<div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100 group">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">${d.type_label || d.type}</p>
                        <button onclick='window.previewDoc(${docData})' class="text-[10px] text-blue-600 hover:underline text-left">Aperçu</button>
                    </div>
                    @if(App\Helpers\PermissionHelper::can('documents.delete'))
                    <button onclick="locSection.deleteDoc(${d.id})" class="p-2 text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-all" title="Supprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @endif
                 </div>`;
             }).join('') : '<p class="text-gray-400 text-xs italic text-center py-4">Aucune pièce jointe</p>';
             this.currentLocId = loc.id;
             this.showView('details');
        },
        openModal: function(mode, loc = null) {
            const wrapper = document.getElementById('loc-modal-wrapper'); const overlay = document.getElementById('loc-modal-overlay'); const container = document.getElementById('loc-modal-container'); const title = document.getElementById('loc-modal-title'); const form = document.getElementById('loc-main-form');
            if (!wrapper) return; wrapper.classList.remove('hidden'); window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset(); document.getElementById('loc-input-id').value = '';
            if(mode === 'edit' && loc) { 
                title.innerText = 'Modifier le Dossier'; 
                document.getElementById('loc-input-id').value = loc.id; 
                document.getElementById('loc-input-nom').value = loc.nom; 
                document.getElementById('loc-input-email').value = loc.email || ''; 
                document.getElementById('loc-input-tel').value = loc.telephone || ''; 
                document.getElementById('loc-input-id-card').value = loc.pieces_identite || '';
                document.getElementById('loc-input-profession').value = loc.profession || '';
                document.getElementById('loc-input-revenus').value = loc.revenus_mensuels || '';
                document.getElementById('loc-input-adresse').value = loc.adresse || ''; 
            } else { title.innerText = 'Nouveau Dossier'; }
        },
        closeModal: function() { const w = document.getElementById('loc-modal-wrapper'); const o = document.getElementById('loc-modal-overlay'); const c = document.getElementById('loc-modal-container'); o?.classList.add('opacity-0'); c?.classList.add('scale-95', 'opacity-0'); window.modalUX?.deactivate(w); setTimeout(() => { w.classList.add('hidden'); }, 300); },
        submitForm: async function(e) {
            e.preventDefault(); const btn = document.getElementById('loc-submit-btn'); if (!btn || btn.disabled) return; const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
            const id = document.getElementById('loc-input-id').value;
            try { const res = await fetch(id ? `/locataires/${id}` : '/locataires', { method: id ? 'PUT' : 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(Object.fromEntries(new FormData(e.target))) });
                if(res.ok) { showToast('Succès', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { const d = await res.json(); showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        confirmDelete: function(loc) {
            this.deleteTargetId = loc.id; 
            const infoEl = document.getElementById('del-loc-name');
            if (infoEl) infoEl.textContent = loc.nom;
            const w = document.getElementById('loc-delete-modal-wrapper'); const o = document.getElementById('loc-delete-modal-overlay'); const c = document.getElementById('loc-delete-container');
            if (!w) return; w.classList.remove('hidden'); window.modalUX?.activate(w, c); setTimeout(() => { o?.classList.remove('opacity-0'); c?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() { const w = document.getElementById('loc-delete-modal-wrapper'); const o = document.getElementById('loc-delete-modal-overlay'); const c = document.getElementById('loc-delete-container'); o?.classList.add('opacity-0'); c?.classList.add('scale-95', 'opacity-0'); window.modalUX?.deactivate(w); setTimeout(() => { w.classList.add('hidden'); }, 300); },
        executeDelete: async function() {
            if(!this.deleteTargetId) return; const btn = document.getElementById('loc-confirm-delete-btn'); const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
            try { const res = await fetch(`/locataires/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                const d = await res.json(); if(d.success) { showToast('Supprimé', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(d.message || 'Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
        },
        openDocumentModal: function(id = null) { if (id) this.currentLocId = id; document.getElementById('doc-locataire-id').value = this.currentLocId; const w = document.getElementById('loc-doc-modal-wrapper'); const o = document.getElementById('loc-doc-modal-overlay'); const c = document.getElementById('loc-doc-container'); if (!w) return; w.classList.remove('hidden'); setTimeout(() => { o?.classList.remove('opacity-0'); c?.classList.remove('scale-95', 'opacity-0'); }, 10); },
        closeDocumentModal: function() { const w = document.getElementById('loc-doc-modal-wrapper'); const o = document.getElementById('loc-doc-modal-overlay'); const c = document.getElementById('loc-doc-container'); o?.classList.add('opacity-0'); c?.classList.add('scale-95', 'opacity-0'); setTimeout(() => { w.classList.add('hidden'); }, 300); },
        submitDocForm: async function(e) {
            e.preventDefault(); const btn = document.getElementById('doc-submit-btn'); const orig = btn.innerHTML; btn.disabled = true; btn.innerHTML = 'Envoi...';
            try { const res = await fetch(`/locataires/${this.currentLocId}/documents`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(e.target) });
                const d = await res.json();
                if(res.ok) { 
                    showToast('Document ajouté', 'success'); 
                    this.closeDocumentModal();
                    // Mettre à jour l'objet local pour affichage immédiat
                    if (!this.currentLoc.documents) this.currentLoc.documents = [];
                    this.currentLoc.documents.push(d.document);
                    this.showDetails(this.currentLoc); // Rafraîchir la vue dossier
                    
                    if(window.dashboard) window.dashboard.refresh(); 
                }
                else { showToast(d.message || 'Erreur lors de l\'envoi', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        deleteDoc: async function(id) {
            if (!confirm('Supprimer ce document ?')) return;
            try {
                const res = await fetch(`/documents/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const d = await res.json();
                if (d.success) {
                    showToast('Document supprimé', 'success');
                    if (this.currentLoc && this.currentLoc.documents) {
                        this.currentLoc.documents = this.currentLoc.documents.filter(doc => doc.id !== id);
                        this.showDetails(this.currentLoc);
                    }
                    if(window.dashboard) window.dashboard.refresh();
                } else {
                    showToast(d.message || 'Erreur', 'error');
                }
            } catch (e) {
                showToast('Erreur serveur', 'error');
            }
        }
    };
</script>
