<div class="h-full flex flex-col gap-8" id="depenses-section-container">

    <div id="dep-view-list" class="dep-sub-view space-y-8">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Journal des Dépenses',
            'subtitle' => 'Suivi des charges, maintenance et travaux.',
            'icon' => 'money',
            'actions' => App\Helpers\PermissionHelper::can('depenses.create')
                ? '<button onclick="depSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Nouvelle Dépense
                </button>'
                : ''
        ])

        <div id="dep-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', ['label' => 'Total Mois Actuel', 'value' => number_format($data['depenses_list']->where('date_depense', '>=', now()->startOfMonth())->sum('montant'), 0, ',', ' '), 'suffix' => 'F', 'icon' => 'chart', 'color' => 'blue'])
            @include('components.kpi-card', ['label' => 'Total Annuel', 'value' => number_format($data['depenses_list']->where('date_depense', '>=', now()->startOfYear())->sum('montant'), 0, ',', ' '), 'suffix' => 'F', 'icon' => 'money', 'color' => 'gradient'])
            @include('components.kpi-card', ['label' => 'Maintenance (Mois)', 'value' => number_format($data['depenses_list']->where('categorie', 'maintenance')->where('date_depense', '>=', now()->startOfMonth())->sum('montant'), 0, ',', ' '), 'suffix' => 'F', 'icon' => 'cog', 'color' => 'gray'])
        </div>

        <div id="dep-table-container">
            <x-data-table :headers="[['label' => 'Date', 'classes' => 'text-white'], ['label' => 'Objet', 'classes' => 'text-white'], ['label' => 'Bien', 'classes' => 'text-white'], ['label' => 'Montant', 'classes' => 'text-white'], ['label' => 'Actions', 'classes' => 'text-right text-white']]" emptyMessage="Aucune dépense enregistrée.">
                @forelse($data['depenses_list'] as $dep)
                <tr class="hover:bg-gray-50/50 transition-all duration-300 group text-left">
                    <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ \Carbon\Carbon::parse($dep->date_depense)->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $dep->titre }}</div>
                        <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $dep->categorie }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $dep->bien->nom ?? '--' }}</td>
                    <td class="px-6 py-4 font-black text-[#cb2d2d]">{{ format_money($dep->montant) }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($dep->preuve)
                            <button onclick="window.previewDoc({url: '{{ Storage::url($dep->preuve) }}', nom_original: 'Facture', type_label: 'Justificatif'})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg></button>
                            @endif
                            @if(App\Helpers\PermissionHelper::can('depenses.edit'))
                            <button onclick="depSection.openModal('edit', {{ json_encode($dep) }})" class="p-2 text-gray-400 hover:text-[#cb2d2d] rounded-lg transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            @endif
                            @if(App\Helpers\PermissionHelper::can('depenses.delete'))
                            <button onclick="depSection.confirmDelete({{ json_encode($dep) }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </x-data-table>
        </div>
    </div>

    @push('modals')
    <!-- MODAL: CREATE/EDIT (ROUGE ONTARIO) -->
    <div id="dep-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="dep-modal-title" role="dialog" aria-modal="true">
        <div id="dep-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) depSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="dep-modal-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="dep-modal-title" class="text-lg font-black text-white">Gestion Dépense</h3>
                        <button onclick="depSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="dep-main-form" onsubmit="depSection.submitForm(event)" class="p-8 space-y-5 text-left">
                        <input type="hidden" name="id" id="dep-input-id">
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="dep-input-titre">Objet de la dépense</label>
                                <input type="text" name="titre" id="dep-input-titre" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative group">
                                    <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="dep-input-cat">Catégorie</label>
                                    <select name="categorie" id="dep-input-cat" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all appearance-none">
                                        <option value="maintenance">Maintenance</option><option value="travaux">Travaux</option><option value="taxe">Taxe</option><option value="assurance">Assurance</option><option value="autre">Autre</option>
                                    </select>
                                </div>
                                <div class="relative group">
                                    <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="dep-input-montant">Montant</label>
                                    <input type="number" name="montant" id="dep-input-montant" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="dep-input-date">Date</label>
                                <input type="date" name="date_depense" id="dep-input-date" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="dep-input-bien">Bien Concerné</label>
                                <select name="bien_id" id="dep-input-bien" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all appearance-none">
                                    <option value="">Choisir un bien...</option>
                                    @foreach($data['biens_list'] as $b)<option value="{{ $b->id }}">{{ $b->nom }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="dep-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Enregistrer</button>
                            <button type="button" onclick="depSection.closeModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE (ROUGE ONTARIO) -->
    <div id="dep-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="dep-delete-modal-title" role="dialog" aria-modal="true">
        <div id="dep-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) depSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="dep-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 id="dep-delete-modal-title" class="text-lg font-black text-white">Supprimer Dépense ?</h3>
                        <button onclick="depSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-dep-name" class="text-sm text-gray-900 mb-2 font-black uppercase tracking-tight"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">Cette action modifiera le bilan de rentabilité. Elle est irréversible.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="depSection.executeDelete()" id="dep-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer la suppression</button>
                            <button onclick="depSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
window.depSection = {
    deleteTargetId: null,
    openModal: function(mode, data = null) {
        const wrapper = document.getElementById('dep-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-modal-container');
        const title = document.getElementById('dep-modal-title');
        const form = document.getElementById('dep-main-form');
        if (!wrapper) return;
        wrapper.classList.remove('hidden');
        window.modalUX?.activate(wrapper, container);
        setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        if (form) form.reset();
        document.getElementById('dep-input-id').value = '';
        if(mode === 'edit' && data) {
            title.innerText = 'Modifier la Dépense';
            document.getElementById('dep-input-id').value = data.id;
            document.getElementById('dep-input-titre').value = data.titre;
            document.getElementById('dep-input-cat').value = data.categorie;
            document.getElementById('dep-input-montant').value = data.montant;
            document.getElementById('dep-input-date').value = data.date_depense;
            document.getElementById('dep-input-bien').value = data.bien_id;
        } else { title.innerText = 'Nouvelle Dépense'; }
    },
    closeModal: function() {
        const wrapper = document.getElementById('dep-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-modal-container');
        overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
        window.modalUX?.deactivate(wrapper);
        setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
    },
    submitForm: async function(e) {
        e.preventDefault();
        const btn = document.getElementById('dep-submit-btn');
        if(!btn || btn.disabled) return;
        const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
        const id = document.getElementById('dep-input-id').value;
        try {
            const res = await fetch(id ? `/depenses/${id}` : '/depenses', {
                method: id ? 'PUT' : 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(Object.fromEntries(new FormData(e.target)))
            });
            if(res.ok) { showToast('Dépense enregistrée', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
            else { const d = await res.json(); showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
    },
    confirmDelete: function(dep) {
        this.deleteTargetId = dep.id;
        const infoEl = document.getElementById('del-dep-name');
        if (infoEl) infoEl.textContent = dep.titre;
        const wrapper = document.getElementById('dep-delete-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-delete-container');
        if (!wrapper) return;
        wrapper.classList.remove('hidden');
        window.modalUX?.activate(wrapper, container);
        setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
    },
    closeDeleteModal: function() {
        const wrapper = document.getElementById('dep-delete-modal-wrapper');
        const overlay = document.getElementById('dep-modal-overlay');
        const container = document.getElementById('dep-delete-container');
        overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
        window.modalUX?.deactivate(wrapper);
        setTimeout(() => { wrapper.classList.add('hidden'); this.deleteTargetId = null; }, 300);
    },
    executeDelete: async function() {
        if(!this.deleteTargetId) return;
        const btn = document.getElementById('dep-confirm-delete-btn');
        const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
        try {
            const res = await fetch(`/depenses/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            if(res.ok) { showToast('Dépense supprimée', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
            else { showToast('Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
        } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
    }
};
</script>
