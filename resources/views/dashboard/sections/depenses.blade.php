<div class="flex flex-col gap-6" id="depenses-section-container">
    
    <!-- SECTION: LISTE PRINCIPALE -->
    <div id="dep-view-list" class="dep-sub-view space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-[#1A365D]">Gestion des Dépenses</h2>
                <p class="text-sm text-gray-500 mt-1">Suivi de la maintenance, des taxes et des travaux.</p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
            <button onclick="depSection.openModal('create')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-200 hover:bg-[#a82020] transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouvelle Dépense
            </button>
            @endif
        </div>

        <!-- KPIs Dépenses -->
        <div id="dep-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Maintenance & Travaux (Mois)</p>
                @php
                    $thisMonth = \Carbon\Carbon::now()->format('Y-m');
                    $depensesMois = $data['depenses_list']->filter(function($d) use ($thisMonth) {
                        return \Carbon\Carbon::parse($d->date_depense)->format('Y-m') === $thisMonth;
                    })->sum('montant');
                @endphp
                <p class="text-3xl font-extrabold text-[#cb2d2d] mt-2">{{ number_format($depensesMois, 0, ',', ' ') }} <span class="text-lg">F</span></p>
            </div>
            <div class="bg-[#1A365D] p-6 rounded-2xl shadow-lg text-white">
                <p class="text-xs font-bold text-blue-200 uppercase tracking-wider">Total Annuel</p>
                @php
                    $thisYear = \Carbon\Carbon::now()->year;
                    $depensesAnnee = $data['depenses_list']->filter(function($d) use ($thisYear) {
                        return \Carbon\Carbon::parse($d->date_depense)->year === $thisYear;
                    })->sum('montant');
                @endphp
                <p class="text-3xl font-extrabold mt-2">{{ number_format($depensesAnnee, 0, ',', ' ') }} <span class="text-lg">F</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Principale Catégorie</p>
                @php
                    $topCat = $data['depenses_list']->groupBy('categorie')->sortByDesc(function($cat) {
                        return $cat->sum('montant');
                    })->keys()->first() ?? 'N/A';
                @endphp
                <p class="text-3xl font-extrabold text-[#1A365D] mt-2 capitalize">{{ $topCat }}</p>
            </div>
        </div>

        <!-- Table -->
        <div id="dep-table-container" class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#1A365D]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white">Description</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-center">Catégorie</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white">Bien Concerné</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-right">Montant</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-center">Date</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data['depenses_list'] as $dep)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $dep->titre }}</div>
                                <div class="text-xs text-gray-400 truncate max-w-xs">{{ $dep->description ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                    @if($dep->categorie == 'maintenance') bg-blue-50 text-blue-600
                                    @elseif($dep->categorie == 'travaux') bg-purple-50 text-purple-600
                                    @elseif($dep->categorie == 'taxe') bg-orange-50 text-orange-600
                                    @else bg-gray-50 text-gray-600
                                    @endif">
                                    {{ $dep->categorie }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                {{ $dep->bien->nom ?? 'Bien supprimé' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-extrabold text-gray-900">{{ number_format($dep->montant, 0, ',', ' ') }} F</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500 font-bold text-xs">
                                {{ \Carbon\Carbon::parse($dep->date_depense)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($dep->justificatif)
                                    <button onclick="window.previewDoc({url: '{{ asset('storage/' . $dep->justificatif) }}', nom_original: 'Note_{{ $dep->id }}.{{ pathinfo($dep->justificatif, PATHINFO_EXTENSION) }}', type_label: 'Justificatif Dépense'})" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @endif
                                    @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
                                    <button onclick="depSection.openModal('edit', {{ json_encode($dep) }})" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button onclick="depSection.requestDelete({{ $dep->id }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium italic">Aucune dépense enregistrée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL FORM -->
    <div id="dep-modal-wrapper" class="relative z-[100] hidden" role="dialog" aria-modal="true">
        <div id="dep-modal-overlay" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) depSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="dep-modal-container" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 duration-300 border border-gray-100">
                    
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 id="dep-modal-title" class="text-lg font-bold text-gray-900">Enregistrer une dépense</h3>
                        <button onclick="depSection.closeModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    <form id="dep-main-form" class="p-6 space-y-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="dep-input-id">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Bien Concerné</label>
                                <select name="bien_id" id="dep-input-bien" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-2.5">
                                    <option value="">Sélectionner un bien...</option>
                                    @foreach($data['biens_list'] as $bien)
                                        <option value="{{ $bien->id }}">{{ $bien->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Titre / Objet</label>
                                    <input type="text" name="titre" id="dep-input-titre" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-2.5" placeholder="Ex: Réparation Plomberie">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Catégorie</label>
                                    <select name="categorie" id="dep-input-cat" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-2.5">
                                        <option value="maintenance">Maintenance</option>
                                        <option value="travaux">Travaux</option>
                                        <option value="taxe">Taxe</option>
                                        <option value="assurance">Assurance</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Montant (F CFA)</label>
                                    <input type="number" name="montant" id="dep-input-montant" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-2.5 text-right" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Date</label>
                                    <input type="date" name="date_depense" id="dep-input-date" required value="{{ date('Y-m-d') }}" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-2.5">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Description (Optionnel)</label>
                                <textarea name="description" id="dep-input-desc" rows="2" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d]"></textarea>
                            </div>

                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-3 border-dashed text-center">
                                <input type="file" name="justificatif" id="dep-input-file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*,application/pdf">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-bold text-gray-500 uppercase">Justificatif (PDF/Image)</span>
                                </div>
                                <p id="dep-file-name" class="text-[10px] font-black text-[#cb2d2d] hidden italic"></p>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                            <button type="button" onclick="depSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="dep-submit-btn" class="bg-[#cb2d2d] text-white px-8 py-3 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-xs uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="dep-delete-modal" onclick="if(event.target === this) depSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="dep-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Supprimer la dépense ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Cette opération annulera le débit associé dans le bilan propriétaire. Action irréversible.</p>
            <div class="flex flex-col gap-3">
                <button id="dep-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">Confirmer la suppression</button>
                <button onclick="depSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">Annuler</button>
            </div>
        </div>
    </div>
</div>

<script>
window.depSection = {
    deleteTargetId: null,

    openModal: function(mode, data = null) {
        const wrapper = document.getElementById('dep-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-modal-container');
        const form = document.getElementById('dep-main-form');
        const title = document.getElementById('dep-modal-title');
        const btn = document.getElementById('dep-submit-btn');

        wrapper.classList.remove('hidden');
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            container.classList.remove('opacity-0', 'scale-95');
            container.classList.add('opacity-100', 'scale-100');
        }, 10);

        form.reset();
        document.getElementById('dep-input-id').value = '';
        document.getElementById('dep-file-name').classList.add('hidden');
        
        if(mode === 'edit' && data) {
            title.innerText = 'Modifier la dépense';
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Mettre à jour';
            document.getElementById('dep-input-id').value = data.id;
            document.getElementById('dep-input-bien').value = data.bien_id;
            document.getElementById('dep-input-titre').value = data.titre;
            document.getElementById('dep-input-montant').value = Math.floor(data.montant);
            document.getElementById('dep-input-cat').value = data.categorie;
            document.getElementById('dep-input-date').value = data.date_depense.split('T')[0];
            document.getElementById('dep-input-desc').value = data.description || '';
        } else {
            title.innerText = 'Nouvelle Dépense';
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
        }
    },

    closeModal: function() {
        const wrapper = document.getElementById('dep-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-modal-container');
        overlay.classList.add('opacity-0');
        container.classList.remove('opacity-100', 'scale-100');
        container.classList.add('opacity-0', 'scale-95');
        setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
    },

    requestDelete: function(id) {
        this.deleteTargetId = id;
        const modal = document.getElementById('dep-delete-modal');
        const container = document.getElementById('dep-delete-container');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }, 10);
    },

    closeDeleteModal: function() {
        const modal = document.getElementById('dep-delete-modal');
        const container = document.getElementById('dep-delete-container');
        modal.classList.add('opacity-0');
        container.classList.remove('scale-100', 'scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    },

    init: function() {
        // Form Submit
        document.getElementById('dep-main-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('dep-submit-btn');
            const originalBtn = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Traitement...';

            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/depenses/${id}` : '/depenses';
            
            if(id) formData.append('_method', 'PUT');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const res = await response.json();
                if(res.success) {
                    showToast(res.message, 'success');
                    this.closeModal();
                    // Refresh via helper
                    if(window.dashRefresher) window.dashRefresher.triggerRefresh();
                } else {
                    showToast(res.message, 'error');
                }
            } catch(err) {
                showToast('Erreur lors de la communication serveur', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalBtn;
            }
        });

        // Delete Confirm
        document.getElementById('dep-confirm-delete-btn').addEventListener('click', async () => {
            if(!this.deleteTargetId) return;
            try {
                const response = await fetch(`/depenses/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const res = await response.json();
                if(res.success) {
                    showToast('Dépense supprimée', 'success');
                    this.closeDeleteModal();
                    if(window.dashRefresher) window.dashRefresher.triggerRefresh();
                }
            } catch(err) { showToast('Erreur serveur', 'error'); }
        });

        // File Display
        document.getElementById('dep-input-file').addEventListener('change', function() {
            const nameEl = document.getElementById('dep-file-name');
            if(this.files.length > 0) {
                nameEl.innerText = this.files[0].name;
                nameEl.classList.remove('hidden');
            } else {
                nameEl.classList.add('hidden');
            }
        });
    }
};

depSection.init();
</script>
