<div class="h-full flex flex-col gap-8" id="biens-section-container">

    <!-- LIST VIEW -->
    <div id="bien-view-list" class="bien-sub-view space-y-8">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Parc Immobilier',
            'subtitle' => 'Gestion locative des appartements, villas et autres biens.',
            'icon' => 'building',
            'actions' => App\Helpers\PermissionHelper::can('biens.create')
                ? '<button onclick="bienSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Nouveau Bien
                </button>'
                : ''
        ])

        <!-- KPIs Uniformes -->
        <div id="bien-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', [
                'label' => 'Total Biens',
                'value' => count($data['biens_list']),
                'icon' => 'building',
                'color' => 'gray'
            ])
            @include('components.kpi-card', [
                'label' => 'Occupés',
                'value' => $data['biens_list']->where('statut', 'occupé')->count(),
                'icon' => 'user',
                'color' => 'blue'
            ])
            @include('components.kpi-card', [
                'label' => 'Disponibles',
                'value' => $data['biens_list']->whereIn('statut', ['libre', 'disponible'])->count(),
                'icon' => 'check',
                'color' => 'green'
            ])
        </div>

        <!-- Grid -->
        <div id="bien-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($data['biens_list'] as $bien)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm ontario-card-lift group overflow-hidden cursor-pointer" onclick="bienSection.showDetails({{ json_encode($bien) }})">
                <div class="h-48 bg-gray-50 relative overflow-hidden">
                    @if($bien->imagePrincipale)
                        <img src="{{ Storage::url($bien->imagePrincipale->chemin) }}" alt="Photo de {{ $bien->nom }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-300">
                            <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                        </div>
                    @endif

                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-widest shadow-sm backdrop-blur-md {{ $bien->statut === 'occupé' ? 'bg-blue-500/90 text-white' : 'bg-green-500/90 text-white' }}">
                            {{ $bien->statut }}
                        </span>
                    </div>
                    <div class="absolute bottom-0 inset-x-0 h-16 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <p class="text-xs font-bold uppercase tracking-wider opacity-90">{{ $bien->type }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-gray-900 text-lg leading-tight truncate mb-1 group-hover:text-[#cb2d2d] transition-colors">{{ $bien->nom }}</h3>
                    <p class="text-xs text-gray-500 truncate flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $bien->adresse ?? 'Adresse non renseignée' }}
                    </p>

                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex flex-col">
                             <span class="text-[11px] uppercase font-bold text-gray-400">Loyer</span>
                             <span class="font-black text-gray-900 text-lg">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} <span class="text-xs font-bold text-gray-400">F</span></span>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 group-hover:bg-[#cb2d2d] group-hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <p class="text-gray-900 font-bold mb-1">Aucun bien immobilier</p>
                <p class="text-gray-500 text-sm">Commencez par ajouter votre premier bien.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- DETAILS VIEW -->
    <div id="bien-view-details" class="bien-sub-view hidden space-y-6">
        <div class="flex items-center gap-4">
            <button onclick="bienSection.showView('list')" class="p-2 hover:bg-gray-100 rounded-full transition text-gray-600" aria-label="Retour à la liste">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </button>
            <h2 class="text-2xl font-bold text-gray-900">Détails du Bien</h2>
            <div class="ml-auto flex gap-3">
                @if(App\Helpers\PermissionHelper::can('biens.edit'))
                <button id="btn-edit-bien" class="text-sm font-bold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition shadow-sm">Modifier</button>
                @endif
                @if(App\Helpers\PermissionHelper::can('biens.delete'))
                <button id="btn-delete-bien" class="text-sm font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition">Supprimer</button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Image Card -->
                <div class="bg-white rounded-3xl p-3 border border-gray-100 shadow-sm overflow-hidden text-center">
                     <div id="det-bien-image-container" class="w-full h-64 bg-gray-50 rounded-2xl overflow-hidden mb-5 relative">
                         <!-- Image injected via JS -->
                     </div>
                     <h3 class="text-xl font-bold text-gray-900" id="det-bien-nom">...</h3>
                     <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1" id="det-bien-type">...
                     </p>

                     <div class="mt-6 flex justify-center mb-4">
                        <span id="det-bien-statut" class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-gray-100 text-gray-500">...</span>
                     </div>
                </div>

                <!-- Price Card -->
                <div class="bg-gray-900 rounded-3xl p-6 text-white shadow-lg text-center relative overflow-hidden">
                     <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <p class="text-xs font-bold opacity-60 uppercase tracking-widest mb-1">Loyer Mensuel</p>
                    <p class="text-3xl font-black" id="det-bien-prix">...</p>
                </div>
            </div>

            <!-- Right Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-4 border-b border-gray-50 pb-2">Localisation & Caractéristiques</h4>
                    <p class="text-gray-600 text-base mb-6" id="det-bien-adresse">...</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-[#cb2d2d]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase">Pièces</p>
                                <p class="font-bold text-gray-900" id="det-bien-pieces">-</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-[#cb2d2d]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M11 5v16M11 21h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase">Type</p>
                                <p class="font-bold text-gray-900" id="det-bien-meuble">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Contrat en cours</h4>
                    </div>
                    <div class="p-8" id="det-bien-occupe-info">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FORM (ULTRA COMPACT GRID) -->
    <div id="bien-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="bien-modal-title" role="dialog" aria-modal="true">
        <div id="bien-modal-overlay" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) bienSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) bienSection.closeModal()">
                <div id="bien-modal-container" class="relative transform overflow-hidden bg-white text-left shadow-2xl transition-all w-full h-full sm:h-auto sm:w-full sm:max-w-xl sm:my-8 rounded-none sm:rounded-2xl opacity-0 scale-95 duration-300 border border-gray-100">

                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="bien-modal-title" class="text-base font-bold text-gray-900">Nouveau Bien</h3>
                            <p class="text-[11px] text-gray-500 font-medium">Informations du logement.</p>
                        </div>
                        <button onclick="bienSection.closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="bien-main-form" class="p-6 space-y-4" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="bien-input-id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom du Bien -->
                            <div class="col-span-1 md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nom du Bien</label>
                                <input type="text" name="nom" id="bien-input-nom" required class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: Appartement Résidence Paix">
                            </div>

                            <!-- Type -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Type de Bien</label>
                                <select name="type" id="bien-input-type" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                                    <option value="appartement">Appartement</option>
                                    <option value="villa">Villa</option>
                                    <option value="studio">Studio</option>
                                    <option value="bureau">Bureau</option>
                                    <option value="magasin">Magasin</option>
                                    <option value="entrepot">Entrepôt</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <!-- Loyer -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Loyer Mensuel (F CFA)</label>
                                <input type="number" name="loyer_mensuel" id="bien-input-loyer" required class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0 text-right" placeholder="0">
                            </div>

                            <!-- Adresse -->
                            <div class="col-span-1 md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Adresse Complète</label>
                                <input type="text" name="adresse" id="bien-input-adresse" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: Grand Dakar, Rue 10">
                            </div>

                            <!-- Nombre de pièces -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nombre de pièces</label>
                                <input type="number" name="nombre_pieces" id="bien-input-pieces" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: 3">
                            </div>

                            <!-- Meublé -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 flex items-center justify-between">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Bien Meublé ?</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="meuble" id="bien-input-meuble" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:translate-x-[-100%] peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#cb2d2d]"></div>
                                </label>
                            </div>

                            <!-- Photos -->
                            <div class="col-span-1 md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all h-20 flex items-center justify-center border-dashed">
                                <input type="file" name="images[]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="bien-input-images" accept="image/*" multiple>
                                <div class="text-center">
                                    <div class="flex items-center justify-center gap-2 mb-1">
                                         <svg class="w-4 h-4 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                         <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Photos du Bien (Une ou plusieurs)</span>
                                    </div>
                                    <p id="file-name-display" class="text-[11px] font-black text-[#cb2d2d] truncate max-w-[300px]"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="bienSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="bien-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL (UNCHANGED) -->
    <div id="bien-delete-modal" role="dialog" aria-modal="true" aria-labelledby="bien-delete-modal-title" onclick="if(event.target === this) bienSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="bien-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
             <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 id="bien-delete-modal-title" class="text-xl font-bold text-gray-900 mb-2">Supprimer ce bien ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Cette action est irréversible. Toutes les données associées (photos, historique) seront supprimées.</p>
            <div class="flex flex-col gap-3">
                <button id="bien-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Supprimer
                </button>
                <button onclick="bienSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.bienSection = {
        deleteTargetId: null,

        showView: function(viewId) {
            document.querySelectorAll('.bien-sub-view').forEach(el => el.classList.add('hidden'));
            document.getElementById('bien-view-' + viewId).classList.remove('hidden');
        },

        openModal: function(mode, bien = null) {
            const wrapper = document.getElementById('bien-modal-wrapper');
            const overlay = document.getElementById('bien-modal-overlay');
            const container = document.getElementById('bien-modal-container');
            const form = document.getElementById('bien-main-form');
            const title = document.getElementById('bien-modal-title');
            const btn = document.getElementById('bien-submit-btn');

            wrapper.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);

            form.reset();
            document.getElementById('bien-input-id').value = '';
            document.getElementById('file-name-display').classList.add('hidden');

            if(mode === 'edit' && bien) {
                title.innerText = 'Modifier Bien';
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Mettre à jour';
                document.getElementById('bien-input-id').value = bien.id;
                document.getElementById('bien-input-nom').value = bien.nom;
                document.getElementById('bien-input-type').value = bien.type;
                document.getElementById('bien-input-loyer').value = Math.floor(bien.loyer_mensuel);
                document.getElementById('bien-input-adresse').value = bien.adresse || '';
                document.getElementById('bien-input-pieces').value = bien.nombre_pieces || '';
                document.getElementById('bien-input-meuble').checked = bien.meuble == 1;
            } else {
                title.innerText = 'Nouveau Bien';
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('bien-modal-wrapper');
            const overlay = document.getElementById('bien-modal-overlay');
            const container = document.getElementById('bien-modal-container');

            overlay.classList.add('opacity-0');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        showDetails: function(bien) {
            document.getElementById('det-bien-nom').innerText = bien.nom;
            document.getElementById('det-bien-type').innerText = bien.type;
            document.getElementById('det-bien-prix').innerText = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(bien.loyer_mensuel) + ' F';
            document.getElementById('det-bien-adresse').innerText = bien.adresse || (bien.nom + ' - ' + bien.type);
            document.getElementById('det-bien-pieces').innerText = bien.nombre_pieces ? bien.nombre_pieces + ' Pièce(s)' : 'Non spécifié';
            document.getElementById('det-bien-meuble').innerText = bien.meuble ? 'Meublé' : 'Non meublé';

            const statutEl = document.getElementById('det-bien-statut');
            statutEl.innerText = bien.statut;
            statutEl.className = `px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest ${bien.statut === 'occupé' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}`;

            // Image
            const imgContainer = document.getElementById('det-bien-image-container');
            if(bien.image_principale || (bien.images && bien.images.length > 0)) {
                const imgPath = bien.image_principale ? bien.image_principale.chemin : bien.images[0].chemin;
                const src = imgPath.startsWith('http') ? imgPath : '/storage/' + imgPath;
                imgContainer.innerHTML = `<img src="${src}" alt="Photo de ${bien.nom}" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-500" onclick="window.previewDoc({url: '${src}', nom_original: '${bien.nom}.jpg', type_label: 'Photo du Bien'})">`;
            } else {
                imgContainer.innerHTML = `<div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50"><svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg></div>`;
            }

            // Occupant Info
            const occBox = document.getElementById('det-bien-occupe-info');
            if(bien.statut === 'occupé' && bien.contrats && bien.contrats.length > 0) {
                const contrat = bien.contrats.find(c => c.statut === 'actif' || c.statut === 'encours');
                if(contrat && contrat.locataire) {
                    const loc = contrat.locataire;
                    occBox.innerHTML = `
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-900 text-white rounded-xl flex items-center justify-center font-bold text-lg">
                                ${loc.nom.substring(0,1).toUpperCase()}
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase">Locataire Actuel</p>
                                <p class="font-bold text-gray-900">${loc.nom}</p>
                                <p class="text-xs text-gray-500">${loc.telephone || ''}</p>
                            </div>
                        </div>
                    `;
                } else {
                     occBox.innerHTML = `<p class="text-xs text-gray-400 italic">Occupé maïs aucun contrat actif trouvé.</p>`;
                }
            } else {
                occBox.innerHTML = `<div class="text-center py-4"><span class="inline-block px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold">Actuellement Libre</span></div>`;
            }

            document.getElementById('btn-edit-bien').onclick = () => this.openModal('edit', bien);
            document.getElementById('btn-delete-bien').onclick = () => this.requestDelete(bien.id);

            this.showView('details');
        },

        requestDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('bien-delete-modal');
            const container = document.getElementById('bien-delete-container');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('bien-delete-modal');
            const container = document.getElementById('bien-delete-container');

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
            const btn = document.getElementById('bien-confirm-delete-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Traitement...';
            btn.disabled = true;

            try {
                const response = await fetch(`/dashboard/biens/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if(data.success) {
                    showToast('Bien supprimé par Ontario Group', 'success');
                    window.location.reload();
                } else {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
            } finally {
                 btn.innerText = originalText;
                 btn.disabled = false;
                 this.closeDeleteModal();
            }
        },

        // File Input UX
        setupFileInput: function() {
            const fileInput = document.getElementById('bien-input-images');
             if(fileInput) {
                 fileInput.addEventListener('change', function() {
                    const display = document.getElementById('file-name-display');
                    if(this.files && this.files.length > 0) {
                        display.innerText = this.files.length > 1
                            ? this.files.length + ' photos sélectionnées'
                            : this.files[0].name;
                        display.classList.remove('hidden');
                    } else {
                        display.classList.add('hidden');
                    }
                });
             }
        }
    };

    // Init
    window.bienSection.setupFileInput();

    document.getElementById('bien-confirm-delete-btn').addEventListener('click', function() {
        bienSection.executeDelete();
    });

    // Dynamic Refresher for Biens (similar to Loyers)
    window.bienRefresher = {
        triggerRefresh: function() {
            // Create or get iframe
            let refreshIframe = document.getElementById('bien_refresh_iframe');
            if(!refreshIframe) {
                refreshIframe = document.createElement('iframe');
                refreshIframe.id = 'bien_refresh_iframe';
                refreshIframe.style.display = 'none';
                document.body.appendChild(refreshIframe);
            }

            refreshIframe.src = '{{ route('dashboard') }}?t=' + new Date().getTime();
            refreshIframe.onload = () => {
                const iframeDoc = refreshIframe.contentDocument || refreshIframe.contentWindow.document;

                // 1. Refresh Global Dashboard KPIs (Direction / Comptable / Gestionnaire)
                const newGlobalGrid = iframeDoc.getElementById('dashboard-kpi-grid');
                const oldGlobalGrid = document.getElementById('dashboard-kpi-grid');

                if (newGlobalGrid && oldGlobalGrid) {
                    oldGlobalGrid.innerHTML = newGlobalGrid.innerHTML;
                    // Optional: Highlight effect to show update
                    oldGlobalGrid.style.transition = 'opacity 0.3s';
                    oldGlobalGrid.style.opacity = '0.5';
                    setTimeout(() => { oldGlobalGrid.style.opacity = '1'; }, 300);
                }

                // 2. Refresh the Biens LIST if we are in list view
                // We might need to re-fetch the list HTML or reload just the list container.
                // Since Biens list is large, parsing from iframe dashboard might work if the dashboard view contains the list.
                // However, the dashboard index loads specific sections.

                // For simplicity and robustness given user request about "Dashboard Stats", simply reloading the page IS safer for the Property list itself,
                // BUT the user specifically complained about "Comptabilite" stats not updating.

                // Compromise: We refresh the Global Stats via Iframe, AND then we update the local list if possible.
                // Actually, since updating a Bien affects many things, a reload is fine, BUT user said "donnees mis a jour" not taken into account.
                // This implies caching or lack of propagation.

                // Let's STICK to the reload for the Bien itself (to show new price in list),
                // BUT PRE-FETCH the Global Stats or let the reload handle it?
                // If the user says "it doesn't update", maybe the reload is too fast or hits cache?
                // Or maybe the Controller didn't update the stats?

                // My update in DashboardController updates the underlying data.
                // If I reload, it SHOULD show.
                // Why did the user complain? "on dirait que y'a des donnees toujours codes en dur" -> I fixed that (70%).
                // "Montant Encaissé ne prends pas en compte..."

                // The most likely reason is: The underlying update logic I added takes place,
                // then the page reloads. DOM should be fresh.

                // JUST IN CASE: I will execute the iframe refresh logic BEFORE the reload, or instead of it if I can fully swap the content.
                // Actually, simply adding the iframe method allows us to refresh "Other" tabs/sections if we were using a single-page app approach.
                // But here we are refreshing.

                // WAIT. If I reload the page, `loyers.blade.php`'s refresh logic is lost.
                // The User might be looking at the KPI cards at the top of the page.

                window.location.reload();
            };
        }
    };

    document.getElementById('bien-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('bien-submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...';
        btn.disabled = true;

        const formData = new FormData(this);
        const id = document.getElementById('bien-input-id').value;
        const url = id ? `/dashboard/biens/${id}` : `{{ route('dashboard.biens.store') }}`;

        if (id) {
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if(response.ok && data.success) {
                showToast(data.message || 'Succès', 'success');
                bienSection.closeModal();

                if(window.dashboard) {
                    window.dashboard.refresh();
                } else {
                    window.location.reload();
                }

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
