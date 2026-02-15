<div class="flex flex-col gap-6" id="paiements-section-container">

    <!-- SECTION: LISTE PRINCIPALE -->
    <div id="pai-view-list" class="pai-sub-view space-y-6">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Journal des Encaissements',
            'subtitle' => 'Historique complet des transactions financières.',
            'icon' => 'money',
            'actions' => in_array(auth()->user()->role, ['admin', 'comptable'])
                ? '<button onclick="paiSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Enregistrer un Paiement
                </button>'
                : ''
        ])

        @php
            $encaisseToday = $data['paiements_list']->filter(fn($p) => \Carbon\Carbon::parse($p->date_paiement)->isToday())->sum('montant');
            $thisMonth = \Carbon\Carbon::now()->format('Y-m');
            $encaisseMonth = $data['paiements_list']->filter(fn($p) => \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth)->sum('montant');
            $transactionsMonth = $data['paiements_list']->filter(fn($p) => \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth)->count();
        @endphp

        <!-- KPIs Uniformes -->
        <div id="pai-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', ['label' => 'Encaissé Aujourd\'hui', 'value' => number_format($encaisseToday, 0, ',', ' '), 'suffix' => 'F', 'icon' => 'check', 'color' => 'green'])
            @include('components.kpi-card', ['label' => 'Global Mois Actuel', 'value' => number_format($encaisseMonth, 0, ',', ' '), 'suffix' => 'F', 'icon' => 'money', 'color' => 'gradient'])
            @include('components.kpi-card', ['label' => 'Transactions (Mois)', 'value' => $transactionsMonth, 'icon' => 'chart', 'color' => 'gray'])
        </div>

        <!-- Table -->
        <div id="pai-table-container">
            <x-data-table :headers="[['label' => 'Date & Référence', 'classes' => 'text-white'], ['label' => 'Locataire', 'classes' => 'text-white'], ['label' => 'Méthode', 'classes' => 'text-white text-center'], ['label' => 'Montant Encaissé', 'classes' => 'text-right text-white'], ['label' => 'Preuve', 'classes' => 'text-center text-white'], ['label' => 'Actions', 'classes' => 'text-right text-white']]" emptyMessage="Aucun paiement enregistré.">
                @forelse($data['paiements_list'] as $pai)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group text-left">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($pai->date_paiement)->translatedFormat('d M Y') }}</div>
                        <div class="text-xs text-gray-400 font-medium mt-0.5">REF: {{ $pai->reference ?? 'TRAN-'.$pai->id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800 capitalize">{{ $pai->loyer->contrat->locataire->nom ?? 'Inconnu' }}</div>
                        <div class="text-xs text-gray-400">Mois: {{ \Carbon\Carbon::parse($pai->loyer->mois)->translatedFormat('F Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold uppercase {{ $pai->mode == 'virement' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ $pai->mode ?? 'Espèces' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right"><span class="font-extrabold text-green-600 text-lg">{{ format_money($pai->montant) }}</span></td>
                    <td class="px-6 py-4 text-center">
                        @if($pai->preuve)
                        <button onclick="window.previewDoc({url: '{{ get_secure_url($pai->preuve) }}', nom_original: 'Preuve_{{ $pai->id }}', type_label: 'Preuve'})" class="text-amber-600 hover:underline text-xs font-bold">Voir</button>
                        @else<span class="text-gray-300">—</span>@endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $pai->loyer_id) }}', nom_original: 'Recu.pdf', type_label: 'Reçu'})" class="group flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-900 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Voir Reçu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Reçu</span>
                            </button>
                            @if(in_array(auth()->user()->role, ['admin', 'comptable']))
                            <button onclick="paiSection.confirmDelete({{ json_encode($pai) }})" class="group flex items-center px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Suppr</span>
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

    @push('modals')
    <!-- MODAL: ENREGISTREMENT PAIEMENT (ROUGE ONTARIO) -->
    <div id="pai-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="pai-modal-title" role="dialog" aria-modal="true">
        <div id="pai-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) paiSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="pai-modal-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="pai-modal-title" class="text-lg font-black text-white">Saisie Encaissement</h3>
                        <button onclick="paiSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="pai-main-form" action="{{ route('paiements.store') }}" onsubmit="paiSection.submitForm(event)" method="POST" enctype="multipart/form-data" class="p-8 space-y-5 text-left">
                        @csrf
                        <div class="relative group">
                            <label for="pai-select-loyer" class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-[#274256] uppercase tracking-widest z-10 transition-colors group-focus-within:text-[#cb2d2d]">Loyer Concerné</label>
                            <select name="loyer_id" id="pai-select-loyer" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all appearance-none">
                                <option value="">-- Sélectionner --</option>
                                @foreach($data['loyers_list']->filter(fn($l) => strtolower(trim($l->statut)) !== 'payé') as $l)
                                    <option value="{{ $l->id }}" data-reste="{{ $l->reste_a_payer }}" data-locataire="{{ $l->contrat->locataire->nom }}" data-mois="{{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }}">
                                        {{ $l->contrat->locataire->nom }} — {{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="pai-locataire-card" class="hidden bg-blue-50/30 rounded-2xl p-5 border border-blue-100/50">
                            <p id="pai-card-locataire" class="text-base font-black text-gray-900 capitalize">--</p>
                            <p id="pai-card-mois" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">--</p>
                            <div class="mt-3 pt-3 border-t border-blue-100/50 flex justify-between items-baseline">
                                <span class="text-[9px] font-black text-gray-400 uppercase">Restant dû :</span>
                                <span id="pai-card-montant" class="text-lg font-black text-[#cb2d2d]">0 F</span>
                            </div>
                        </div>
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="pai-input-montant">Montant Reçu</label>
                                <input type="number" name="montant" id="pai-input-montant" required class="block w-full bg-white border-2 border-gray-300 rounded-2xl px-5 py-3.5 text-xl font-black text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                                <div id="pai-live-balance" class="mt-2 px-2 hidden">
                                    <span class="text-[10px] font-bold uppercase tracking-wider" id="pai-live-text"></span>
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="pai-input-date">Date de perception</label>
                                <input type="date" name="date_paiement" id="pai-input-date" value="{{ date('Y-m-d') }}" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="pai-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Enregistrer le paiement</button>
                            <button type="button" onclick="paiSection.closeModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE (ROUGE ONTARIO) -->
    <div id="pai-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="pai-delete-modal-title" role="dialog" aria-modal="true">
        <div id="pai-delete-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) paiSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="pai-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 id="pai-delete-modal-title" class="text-lg font-black text-white">Annuler le paiement ?</h3>
                        <button onclick="paiSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-pai-name" class="text-sm text-gray-900 mb-2 font-black uppercase tracking-tight"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">Le montant sera retiré et le loyer repassera en "Impayé". Cette action est irréversible.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="paiSection.executeDelete()" id="pai-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer l'annulation</button>
                            <button onclick="paiSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Retour</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.paiSection = {
        deleteTargetId: null, currentLoyerReste: 0,
        openModal: function(mode) {
            const wrapper = document.getElementById('pai-modal-wrapper');
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');
            const form = document.getElementById('pai-main-form');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset();
        },
        closeModal: function() {
            const wrapper = document.getElementById('pai-modal-wrapper');
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); const card = document.getElementById('pai-locataire-card'); if(card) card.classList.add('hidden'); }, 300);
        },
        submitForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('pai-submit-btn');
            if (!btn || btn.disabled) return;
            const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
            try {
                const response = await fetch(e.target.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(e.target) });
                const data = await response.json();
                if(response.ok) { showToast('Succès', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(data.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        confirmDelete: function(pai) {
            this.deleteTargetId = pai.id;
            const infoEl = document.getElementById('del-pai-name');
            if (infoEl) infoEl.textContent = (pai.loyer?.contrat?.locataire?.nom || 'Paiement') + ' - ' + new Intl.NumberFormat().format(pai.montant) + ' F';
            const wrapper = document.getElementById('pai-delete-modal-wrapper');
            const overlay = document.getElementById('pai-delete-modal-overlay');
            const container = document.getElementById('pai-delete-container');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() {
            const wrapper = document.getElementById('pai-delete-modal-wrapper');
            const overlay = document.getElementById('pai-delete-modal-overlay');
            const container = document.getElementById('pai-delete-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); this.deleteTargetId = null; }, 300);
        },
        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('pai-confirm-delete-btn');
            const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
            try {
                const response = await fetch(`/dashboard/paiements/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                const data = await response.json();
                if(data.success) { showToast('Annulé', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(data.message || 'Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
        },
        updateLiveBalance: function() {
            const val = parseFloat(document.getElementById('pai-input-montant').value) || 0;
            const target = this.currentLoyerReste || 0;
            const lb = document.getElementById('pai-live-balance');
            const lt = document.getElementById('pai-live-text');
            if (lb && lt) {
                lb.classList.remove('hidden');
                const diff = target - val;
                if (diff > 0) { lb.className = "mt-2 px-2 text-amber-600 font-bold"; lt.textContent = "Reliquat: " + new Intl.NumberFormat('fr-FR').format(diff) + " F"; }
                else if (diff === 0) { lb.className = "mt-2 px-2 text-green-600 font-bold"; lt.textContent = "Solde complet ✅"; }
                else { lb.className = "mt-2 px-2 text-blue-600 font-bold"; lt.textContent = "Trop-perçu: " + new Intl.NumberFormat('fr-FR').format(Math.abs(diff)) + " F"; }
            }
        }
    };

    document.getElementById('pai-input-montant')?.addEventListener('input', () => window.paiSection.updateLiveBalance());

    document.getElementById('pai-select-loyer')?.addEventListener('change', function() {
        const sel = this.options[this.selectedIndex];
        const card = document.getElementById('pai-locataire-card');
        const inp = document.getElementById('pai-input-montant');
        if (this.value && card) {
            document.getElementById('pai-card-locataire').textContent = sel.dataset.locataire;
            document.getElementById('pai-card-mois').textContent = 'Période : ' + sel.dataset.mois;
            document.getElementById('pai-card-montant').textContent = new Intl.NumberFormat('fr-FR').format(sel.dataset.reste) + ' F';
            const reste = parseFloat(sel.dataset.reste) || 0;
            window.paiSection.currentLoyerReste = reste;
            inp.value = reste;
            card.classList.remove('hidden');
            window.paiSection.updateLiveBalance();
        } else if (card) { card.classList.add('hidden'); window.paiSection.currentLoyerReste = 0; document.getElementById('pai-live-balance')?.classList.add('hidden'); }
    });
</script>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.3s ease-out; }
</style>
