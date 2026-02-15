<div class="h-full flex flex-col gap-8" id="biens-section-container">

    <!-- LISTE DES BIENS -->
    <div id="bien-view-list" class="bien-sub-view space-y-8">
        @include('components.section-header', [
            'title' => 'Parc Immobilier',
            'subtitle' => 'Gestion des appartements, villas et locaux commerciaux.',
            'icon' => 'building',
            'actions' => '
                <div class="flex items-center gap-3">
                    <button onclick="window.dashboard.refresh()" class="bg-white border-2 border-gray-100 text-gray-400 px-4 py-2.5 rounded-xl text-sm font-bold hover:border-[#274256] hover:text-[#274256] transition-all flex items-center gap-2 shadow-sm group">
                        <svg class="w-5 h-5 group-active:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span class="hidden sm:inline">Actualiser</span>
                    </button>' . 
                    (App\Helpers\PermissionHelper::can('biens.create')
                    ? '<button onclick="bienSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nouveau Bien
                    </button>'
                    : '') . '
                </div>'
        ])

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($data['biens_list'] as $bien)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm ontario-card-lift group overflow-hidden cursor-pointer text-left" 
                 onclick="bienSection.showDetails({{ json_encode($bien->load('imagePrincipale', 'images', 'proprietaire', 'contrats.locataire')) }})">
                <div class="h-48 bg-gray-100 relative overflow-hidden">
                    @if($bien->imagePrincipale)
                        <img src="{{ Storage::url($bien->imagePrincipale->chemin) }}" alt="Photo" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-300">
                            <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $bien->statut === 'occupé' ? 'bg-[#274256] text-white' : 'bg-green-500 text-white' }}">
                            {{ $bien->statut }}
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 text-base truncate mb-1 group-hover:text-[#cb2d2d] transition-colors">{{ $bien->nom }}</h3>
                    <p class="text-xs text-gray-400 truncate">{{ $bien->adresse }}</p>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <span class="font-black text-gray-900 text-base">{{ format_money($bien->loyer_mensuel) }}</span>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $bien->type }}</div>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-full py-20 text-center text-gray-400 font-medium">Aucun bien immobilier disponible.</div>
            @endforelse
        </div>
    </div>

    <!-- DÉTAILS DU BIEN -->
    <div id="bien-view-details" class="bien-sub-view hidden space-y-8 animate-fade-in text-left">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <button onclick="bienSection.showView('list')" class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 rounded-xl transition text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-black text-[#1a2e3d]" id="det-bien-nom">Détails du Bien</h2>
                        <span id="det-bien-status-badge" class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm"></span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5" id="det-bien-type-label">Logement</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if(App\Helpers\PermissionHelper::can('biens.edit'))
                <button id="det-bien-edit-btn" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-100 transition">Modifier</button>
                @endif
                @if(App\Helpers\PermissionHelper::can('biens.delete'))
                <button id="det-bien-del-btn" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition">Supprimer</button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div id="det-bien-gallery" class="bg-gray-50 rounded-[2rem] overflow-hidden h-64 md:h-80 relative group border border-gray-100 shadow-inner"></div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Surface</p>
                        <p class="text-lg font-black text-[#1a2e3d]" id="det-bien-surface">--</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Pièces</p>
                        <p class="text-lg font-black text-[#1a2e3d]" id="det-bien-pieces">--</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Meublé</p>
                        <p class="text-lg font-black text-[#1a2e3d]" id="det-bien-meuble">--</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm text-center">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Loyer</p>
                        <p class="text-lg font-black text-green-600" id="det-bien-loyer-value">--</p>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Localisation & Description</h4>
                    <p class="text-gray-700 font-bold text-lg mb-4" id="det-bien-adresse">--</p>
                    <p class="text-gray-500 leading-relaxed text-sm" id="det-bien-description">--</p>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-[#1a2e3d] p-6 rounded-[2rem] text-white relative overflow-hidden">
                    <h4 class="text-[9px] font-black text-white/40 uppercase mb-4">Propriétaire</h4>
                    <p class="text-lg font-black" id="det-bien-prop-name">--</p>
                    <p class="text-xs text-white/60 mt-1" id="det-bien-prop-tel">--</p>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm text-left">
                    <h4 class="text-[9px] font-black text-gray-400 uppercase mb-4 border-b border-gray-50 pb-2">Occupation Actuelle</h4>
                    <div id="det-bien-tenant-info" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- MODAL: AJOUT/MODIF (ROUGE ONTARIO) -->
    <div id="bien-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;">
        <div id="bien-modal-overlay" class="app-modal-overlay opacity-0"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" onclick="if(event.target === this) bienSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="bien-modal-container" class="app-modal-panel max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300 my-8 min-h-[600px]">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-[#cb2d2d] rounded-t-3xl text-white text-left">
                        <h3 id="bien-modal-title" class="text-lg font-black text-white">Configuration du Bien</h3>
                        <button onclick="bienSection.closeModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="bien-main-form" onsubmit="bienSection.submitForm(event)" class="p-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="bien-input-id">
                        
                        <!-- PREMIUM GRID LAYOUT -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            <!-- Désignation (Full Width) -->
                            <div class="sm:col-span-2">
                                <label class="ontario-label">Désignation du Bien <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    </div>
                                    <input type="text" name="nom" id="bien-input-nom" required placeholder="Ex: Appartement T3 - Résidence du Parc" class="ontario-input pl-10">
                                </div>
                            </div>

                            <!-- Ligne 2 : Type, Loyer -->
                            <div>
                                <label class="ontario-label">Type <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <select name="type" id="bien-input-type" required class="ontario-input pl-10 appearance-none">
                                        <option value="appartement">Appartement</option>
                                        <option value="villa">Villa</option>
                                        <option value="studio">Studio</option>
                                        <option value="bureau">Bureau</option>
                                        <option value="magasin">Magasin</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Loyer Mensuel (F CFA) <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <input type="number" name="loyer_mensuel" id="bien-input-loyer" required class="ontario-input pl-10 font-bold text-gray-900" placeholder="0">
                                </div>
                            </div>

                            <!-- Ligne 3: Surface, Pièces -->
                            <div>
                                <label class="ontario-label">Surface (m²)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                    </div>
                                    <input type="number" step="0.01" name="surface" id="bien-input-surface" class="ontario-input pl-10" placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Nombre de Pièces</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <input type="number" name="nombre_pieces" id="bien-input-pieces" class="ontario-input pl-10" placeholder="Ex: 3">
                                </div>
                            </div>

                            <!-- Meublé -->
                             <div class="sm:col-span-2">
                                <label class="ontario-label">Meublé ?</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <select name="meuble" id="bien-input-meuble" class="ontario-input pl-10 appearance-none">
                                        <option value="0">Non</option>
                                        <option value="1">Oui</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Adresse -->
                            <div class="col-span-12">
                                <label class="ontario-label">Adresse Complète <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" name="adresse" id="bien-input-adresse" required class="ontario-input pl-10" placeholder="Ex: Quartier Acal, Rue 123, Dakar">
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-span-12">
                                <label class="ontario-label">Description / Notes</label>
                                <textarea name="description" id="bien-input-description" rows="3" class="ontario-input resize-none" placeholder="Détails supplémentaires sur le bien..."></textarea>
                            </div>

                            <!-- Photos -->
                            <div class="col-span-12">
                                <label class="ontario-label">Photos du Bien</label>
                                <div class="relative group mt-2">
                                    <div class="flex justify-center rounded-2xl border-2 border-dashed border-gray-300 px-6 py-8 hover:border-[#cb2d2d] hover:bg-red-50/30 transition-all cursor-pointer group-hover:shadow-sm" onclick="document.getElementById('bien-input-images').click()">
                                        <div class="text-center">
                                            <div class="mx-auto h-12 w-12 text-gray-300 group-hover:text-[#cb2d2d] transition-colors">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 48 48" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 3.582 8 8 8h16c4.418 0 8-3.582 8-8V14m-16 14V4m0 0l-8 8m8-8l8 8" /></svg>
                                            </div>
                                            <div class="flex text-sm text-gray-600 justify-center mt-2">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md font-bold text-[#cb2d2d] focus-within:outline-none focus-within:ring-2 focus-within:ring-[#cb2d2d] focus-within:ring-offset-2 hover:text-[#a82020]">
                                                    <span>Télécharger des images</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF jusqu'à 10MB</p>
                                            <p id="file-count" class="text-xs font-bold text-[#cb2d2d] mt-2 hidden"></p>
                                        </div>
                                    </div>
                                    <input type="file" name="images[]" id="bien-input-images" multiple class="hidden" accept="image/*" onchange="const c=document.getElementById('file-count'); if(this.files.length>0){c.textContent=this.files.length+' fichier(s) sélectionné(s)';c.classList.remove('hidden');}else{c.classList.add('hidden');}">
                                </div>
                            </div>

                        </div>

                        <!-- ACTIONS -->
                        <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <button type="button" onclick="bienSection.closeModal()" class="ontario-btn ontario-btn-secondary">
                                Annuler
                            </button>
                            <button type="submit" id="bien-submit-btn" class="ontario-btn ontario-btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Enregistrer
                            </button>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: SUPPRESSION (ROUGE ONTARIO) -->
    <div id="bien-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="bien-delete-modal-title" role="dialog" aria-modal="true">
        <div id="bien-delete-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) bienSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div id="bien-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 class="text-lg font-black text-white">Supprimer le bien ?</h3>
                        <button onclick="bienSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-bien-info" class="text-sm text-gray-900 mb-2 font-black uppercase tracking-tight"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">Cette action est définitive et entraînera la suppression de toutes les données liées.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="bienSection.executeDelete()" id="bien-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer la suppression</button>
                            <button onclick="bienSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.bienSection = {
        deleteTargetId: null,
        showView: function(v) { document.querySelectorAll('.bien-sub-view').forEach(x => x.classList.add('hidden')); document.getElementById('bien-view-' + v)?.classList.remove('hidden'); },
        showDetails: function(bien) {
            this.currentBien = bien;
            document.getElementById('det-bien-nom').textContent = bien.nom;
            document.getElementById('det-bien-type-label').textContent = bien.type;
            const statusBadge = document.getElementById('det-bien-status-badge');
            if(statusBadge) {
                statusBadge.textContent = bien.statut;
                statusBadge.className = `px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm ${bien.statut === 'occupé' ? 'bg-[#274256] text-white' : 'bg-green-100 text-green-700'}`;
            }
            document.getElementById('det-bien-surface').textContent = (bien.surface || '--') + ' m²';
            document.getElementById('det-bien-pieces').textContent = bien.nombre_pieces || '--';
            document.getElementById('det-bien-meuble').textContent = bien.meuble ? 'OUI' : 'NON';
            document.getElementById('det-bien-loyer-value').textContent = new Intl.NumberFormat().format(bien.loyer_mensuel) + ' F';
            document.getElementById('det-bien-adresse').textContent = bien.adresse || '--';
            document.getElementById('det-bien-description').textContent = bien.description || 'Aucune description.';
            document.getElementById('det-bien-prop-name').textContent = bien.proprietaire?.nom || 'Inconnu';
            document.getElementById('det-bien-prop-tel').textContent = bien.proprietaire?.telephone || '---';
            const gallery = document.getElementById('det-bien-gallery');
            const mainImg = bien.image_principale || (bien.images && bien.images.length > 0 ? bien.images[0] : null);
            gallery.innerHTML = mainImg ? `<img src="/storage/${mainImg.chemin}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-gray-300"><svg class="w-20 h-20 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>`;
            const tenantInfo = document.getElementById('det-bien-tenant-info');
            const activeContrat = bien.contrats ? bien.contrats.find(c => c.statut === 'actif') : null;
            tenantInfo.innerHTML = activeContrat ? `<div class="p-4 bg-gray-50 rounded-2xl space-y-2 text-left"><p class="font-black text-gray-900">${activeContrat.locataire?.nom}</p><p class="text-[10px] font-bold text-gray-400 uppercase">Bail ${activeContrat.type_bail}</p><div class="h-px bg-gray-200 my-2"></div><p class="text-xs font-black text-[#cb2d2d]">Loyer: ${new Intl.NumberFormat().format(activeContrat.loyer_montant)} F</p></div>` : `<div class="py-6 text-center border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/50"><p class="text-xs font-bold text-gray-400 uppercase">Disponible</p></div>`;
            const eb = document.getElementById('det-bien-edit-btn'); const db = document.getElementById('det-bien-del-btn');
            if(eb) eb.onclick = () => this.openModal('edit', bien); if(db) db.onclick = () => this.confirmDelete(bien);
            this.showView('details');
        },
        openModal: function(mode, bien = null) {
            const wrapper = document.getElementById('bien-modal-wrapper'); const overlay = document.getElementById('bien-modal-overlay'); const container = document.getElementById('bien-modal-container'); const title = document.getElementById('bien-modal-title'); const form = document.getElementById('bien-main-form');
            if (!wrapper) return; wrapper.classList.remove('hidden'); window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset(); document.getElementById('bien-input-id').value = '';
            if(mode === 'edit' && bien) {
                title.innerText = 'Modifier le Dossier';
                document.getElementById('bien-input-id').value = bien.id;
                document.getElementById('bien-input-nom').value = bien.nom;
                document.getElementById('bien-input-type').value = bien.type;
                document.getElementById('bien-input-loyer').value = bien.loyer_mensuel;
                document.getElementById('bien-input-surface').value = bien.surface || '';
                document.getElementById('bien-input-pieces').value = bien.nombre_pieces || '';
                document.getElementById('bien-input-meuble').value = bien.meuble ? '1' : '0';
                document.getElementById('bien-input-description').value = bien.description || '';
                document.getElementById('bien-input-adresse').value = bien.adresse;
            } else { title.innerText = 'Nouveau Dossier'; }
        },
        closeModal: function() {
            const w = document.getElementById('bien-modal-wrapper'); const o = document.getElementById('bien-modal-overlay'); const c = document.getElementById('bien-modal-container');
            o?.classList.add('opacity-0'); c?.classList.add('scale-95', 'opacity-0'); window.modalUX?.deactivate(w); setTimeout(() => { w.classList.add('hidden'); }, 300);
        },
        submitForm: async function(e) {
            e.preventDefault(); const btn = document.getElementById('bien-submit-btn'); if(!btn || btn.disabled) return; const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
            const id = document.getElementById('bien-input-id').value;
            const formData = new FormData(e.target);
            if (id) formData.append('_method', 'PUT');
            try {
                const res = await fetch(id ? `/dashboard/biens/${id}` : '/dashboard/biens', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: formData });
                const d = await res.json();
                if(res.ok) { showToast('Bien enregistré', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        confirmDelete: function(bien) {
            this.deleteTargetId = bien.id; 
            const infoEl = document.getElementById('del-bien-info');
            if (infoEl) infoEl.textContent = bien.nom;
            const w = document.getElementById('bien-delete-modal-wrapper'); const o = document.getElementById('bien-delete-modal-overlay'); const c = document.getElementById('bien-delete-container');
            if (!w) return; w.classList.remove('hidden'); window.modalUX?.activate(w, c); setTimeout(() => { o?.classList.remove('opacity-0'); c?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() {
            const w = document.getElementById('bien-delete-modal-wrapper'); const o = document.getElementById('bien-delete-modal-overlay'); const c = document.getElementById('bien-delete-container');
            o?.classList.add('opacity-0'); c?.classList.add('scale-95', 'opacity-0'); window.modalUX?.deactivate(w); setTimeout(() => { w.classList.add('hidden'); this.deleteTargetId = null; }, 300);
        },
        executeDelete: async function() {
            if(!this.deleteTargetId) return; const btn = document.getElementById('bien-confirm-delete-btn'); const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
            try {
                const res = await fetch(`/dashboard/biens/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                const d = await res.json(); if(d.success) { showToast('Supprimé', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(d.message || 'Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
        }
    };
</script>
