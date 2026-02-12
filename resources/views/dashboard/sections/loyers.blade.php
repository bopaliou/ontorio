<div class="flex flex-col gap-6" id="loyers-section-container">

    <!-- SECTION: LISTE PRINCIPALE -->
    <div id="loy-view-list" class="loy-sub-view space-y-6">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Gestion des Loyers',
            'subtitle' => 'Suivi de la facturation mensuelle et des recouvrements.',
            'icon' => 'money',
            'actions' => in_array(auth()->user()->role, ['admin', 'gestionnaire'])
                ? '<button onclick="dashboard.show(\'loyers\'); setTimeout(() => { document.querySelector(\'#loyers-section-container\').scrollIntoView(); }, 100);" class="bg-[#274256] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 hover:bg-[#1a2e3d] transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Générer le Mois
                </button>'
                : ''
        ])

        <!-- Table -->
        <div id="loy-table-container">
            <x-data-table :headers="[
                ['label' => 'Locataire & Bien', 'classes' => 'text-white'],
                ['label' => 'Montant Dû', 'classes' => 'text-right text-white'],
                ['label' => 'Statut', 'classes' => 'text-center text-white'],
                ['label' => 'Actions', 'classes' => 'text-right text-white']
            ]" emptyMessage="Aucun loyer généré pour ce mois.">

                @if(isset($data['loyers_list']))
                    @foreach($data['loyers_list'] as $loy)
                    <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-6 py-4">
                        <div class="text-gray-700 font-bold capitalize">{{ $loy->contrat->locataire->nom ?? 'Inconnu' }}</div>
                        <div class="text-[11px] text-gray-400 italic">Bien: {{ $loy->contrat->bien->nom ?? 'Unité' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-[#274256]">
                        {{ format_money($loy->montant) }}
                        @if($loy->statut === 'partiellement_payé')
                            <div class="text-[11px] text-red-500 font-bold mt-1">
                                Reste: {{ format_money($loy->montant - $loy->montant_paye_cache) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $st = $loy->statut;
                            $cls = $st === 'payé' ? 'bg-green-100 text-green-700' : ($st === 'en_retard' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500');
                        @endphp
                        <span class="px-2.5 py-1 {{ $cls }} text-[11px] uppercase font-black rounded-full">
                            {{ str_replace('_', ' ', $st) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                             @if($loy->statut !== 'payé' && App\Helpers\PermissionHelper::can('paiements.create'))
                                <button onclick="loySection.openPaymentModal({{ json_encode($loy) }})" class="bg-[#274256] text-white p-2 rounded-lg hover:bg-[#1a2e3d] shadow-sm hover:shadow-md active:scale-95 transition-all" title="Encaisser">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </button>
                            @endif

                            @if(App\Helpers\PermissionHelper::can('loyers.edit'))
                            <button onclick="loySection.openEditModal({{ json_encode($loy) }})" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endif

                            @if($loy->statut === 'payé')
                            <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $loy->id) }}', nom_original: 'Quittance_{{ $loy->id }}.pdf', type_label: 'Quittance'})" class="p-2 text-gray-400 hover:text-[#274256] rounded-lg transition" title="Quittance">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                    @endforeach
                @endif

                <x-slot name="mobile">
                    @if(isset($data['loyers_list']))
                        @foreach($data['loyers_list'] as $loy)
                            <x-data-card
                                title="{{ $loy->contrat->locataire->nom ?? 'Inconnu' }}"
                                status="{{ $loy->statut }}"
                                statusColor="{{ $loy->statut === 'payé' ? 'green' : ($loy->statut === 'en_retard' ? 'red' : 'gray') }}"
                            >
                                <div class="flex flex-col gap-1 text-gray-600">
                                    <div class="text-xs">{{ $loy->contrat->bien->nom ?? 'Bien' }}</div>
                                    <div class="flex justify-between items-center mt-1">
                                        <div class="font-bold text-gray-900">{{ format_money($loy->montant) }}</div>
                                        <div class="text-[10px] uppercase font-bold text-gray-400">{{ \Carbon\Carbon::parse($loy->mois)->translatedFormat('F Y') }}</div>
                                    </div>
                                </div>

                                <x-slot name="actions">
                                    @if($loy->statut !== 'payé' && App\Helpers\PermissionHelper::can('paiements.create'))
                                        <button onclick="loySection.openPaymentModal({{ json_encode($loy) }})" class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        </button>
                                    @endif
                                    @if($loy->statut === 'payé')
                                        <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $loy->id) }}', nom_original: 'Quittance.pdf', type_label: 'Quittance'})" class="p-3 bg-gray-50 text-gray-500 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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

    <!-- MODAL: RÈGLEMENT RAPIDE (ULTRA COMPACT GRID) -->
    <div id="loy-payment-wrapper" class="app-modal-root hidden" style="z-index: 10000;" role="dialog" aria-modal="true" aria-labelledby="loy-payment-modal-title">
        <div id="loy-payment-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) loySection.closePaymentModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) loySection.closePaymentModal()">
                <div id="loy-payment-container" class="app-modal-panel app-modal-panel-xl opacity-0 scale-95">
                    
                    <!-- Header Compact -->
                    <div class="app-modal-header px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="loy-payment-modal-title" class="text-base font-bold text-[#274256]">Enregistrer un Paiement</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5 font-medium">Solder la quittance.</p>
                        </div>
                        <button onclick="loySection.closePaymentModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="loy-payment-form" onsubmit="loySection.submitPaymentForm(event)" action="{{ route('paiements.store') }}" method="POST" class="p-6 form-stack field-gap" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="loyer_id" id="loy-payment-loyer-id">

                        <!-- Info Loyer Compact -->
                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center text-white flex-shrink-0 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-400 uppercase tracking-widest">Locataire</p>
                                    <p class="text-xs font-bold text-gray-900 capitalize truncate" id="loy-payment-locataire">...</p>
                                    <p class="text-[11px] text-gray-500 font-medium" id="loy-payment-periode">...</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] font-black text-blue-400 uppercase tracking-widest">À Payer</p>
                                <p class="text-lg font-black text-[#274256]" id="loy-payment-montant">0 F</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Montant Encaissé</label>
                                <input type="number" name="montant" id="loy-payment-montant-input" required class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-green-500/5 transition-all text-right font-mono" placeholder="0">
                            </div>
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Date Paiement</label>
                                <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-4 focus:ring-green-500/5 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mode de règlement</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="mode" value="espèces" checked class="peer hidden">
                                    <div class="p-2 border border-gray-200 rounded-xl text-center transition peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 bg-white hover:border-gray-300 flex flex-col items-center justify-center h-14">
                                        <span class="text-lg">💵</span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400 group-hover:text-gray-600 peer-checked:!text-green-700">Espèces</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="mode" value="virement" class="peer hidden">
                                    <div class="p-2 border border-gray-200 rounded-xl text-center transition peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 bg-white hover:border-gray-300 flex flex-col items-center justify-center h-14">
                                        <span class="text-lg">🏦</span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400 group-hover:text-gray-600 peer-checked:!text-green-700">Virement</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="mode" value="mobile" class="peer hidden">
                                    <div class="p-2 border border-gray-200 rounded-xl text-center transition peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 bg-white hover:border-gray-300 flex flex-col items-center justify-center h-14">
                                        <span class="text-lg">📱</span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400 group-hover:text-gray-600 peer-checked:!text-green-700">Mobile</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                         <div class="relative">
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Justificatif (Optionnel)</label>
                             <input type="file" name="preuve" class="block w-full text-[11px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer bg-gray-50 rounded-2xl border border-gray-100 p-2">
                        </div>

                        <!-- Footer Actions -->
                        <div class="app-modal-footer pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="loySection.closePaymentModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="loy-payment-submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-black shadow-lg shadow-green-900/20 active:scale-95 transition-all text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Valider Encaissement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: MODIFIER LOYER (ULTRA COMPACT) -->
    <div id="loy-edit-wrapper" class="app-modal-root hidden" style="z-index: 10000;" role="dialog" aria-modal="true" aria-labelledby="loy-edit-modal-title">
        <div id="loy-edit-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) loySection.closeEditModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) loySection.closeEditModal()">
                <div id="loy-edit-container" class="app-modal-panel app-modal-panel-lg opacity-0 scale-95">
                    <div class="app-modal-header px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="loy-edit-modal-title" class="text-base font-bold text-[#274256]">Ajustement Loyer</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5 font-medium">Modifier montant ou statut.</p>
                        </div>
                        <button onclick="loySection.closeEditModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="loy-edit-form" onsubmit="loySection.submitEditForm(event)" class="p-6 form-stack field-gap">
                        @csrf
                        <input type="hidden" name="id" id="loy-edit-id">
                        
                        <div class="space-y-4">
                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Montant Loyer (FCFA)</label>
                                <input type="number" name="montant" id="loy-edit-montant" required class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-blue-500/5 transition-all text-right font-mono">
                            </div>

                            <div class="relative">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Statut du terme</label>
                                <select name="statut" id="loy-edit-statut" class="input-focus block w-full bg-gray-50 border-none px-4 py-4 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-blue-500/5 transition-all appearance-none cursor-pointer">
                                    <option value="émis">Émis (En attente)</option>
                                    <option value="partiellement_payé">Partiellement Payé</option>
                                    <option value="payé">Payé (Soldé)</option>
                                    <option value="en_retard">En Retard</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                            </div>
                        </div>

                        <div class="app-modal-footer pt-4 flex items-center justify-end gap-3 border-t border-gray-100 mt-6">
                            <button type="button" onclick="loySection.closeEditModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="loy-edit-submit" class="bg-[#274256] text-white px-6 py-2.5 rounded-xl font-black shadow-lg shadow-blue-900/20 active:scale-95 transition-all text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.loySection = {
        openPaymentModal: function(loyer) {
            const wrapper = document.getElementById('loy-payment-wrapper');
            const overlay = document.getElementById('loy-payment-overlay');
            const container = document.getElementById('loy-payment-container');

            if (!wrapper) return;
            document.getElementById('loy-payment-loyer-id').value = loyer.id;
            document.getElementById('loy-payment-locataire').textContent = loyer.contrat?.locataire?.nom || 'Inconnu';
            document.getElementById('loy-payment-periode').textContent = 'Terme : ' + (loyer.mois || '--');
            document.getElementById('loy-payment-montant').textContent = new Intl.NumberFormat('fr-FR').format(loyer.montant) + ' F';
            document.getElementById('loy-payment-montant-input').value = loyer.montant - (loyer.montant_paye_cache || 0);

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('scale-95', 'opacity-0');
            }, 10);
        },

        closePaymentModal: function() {
            const wrapper = document.getElementById('loy-payment-wrapper');
            const overlay = document.getElementById('loy-payment-overlay');
            const container = document.getElementById('loy-payment-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitPaymentForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('loy-payment-submit');
            if (!btn || btn.disabled) return;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Envoi...';
            btn.disabled = true;

            try {
                const response = await fetch(e.target.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: new FormData(e.target)
                });
                const data = await response.json();
                if(response.ok) {
                    showToast('Paiement validé', 'success');
                    this.closePaymentModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(err) {
                showToast('Erreur serveur', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        },

        openEditModal: function(loyer) {
            const wrapper = document.getElementById('loy-edit-wrapper');
            const overlay = document.getElementById('loy-edit-overlay');
            const container = document.getElementById('loy-edit-container');

            if (!wrapper) return;
            document.getElementById('loy-edit-id').value = loyer.id;
            document.getElementById('loy-edit-montant').value = Math.floor(loyer.montant);
            document.getElementById('loy-edit-statut').value = loyer.statut;

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => {
                overlay?.classList.remove('opacity-0');
                container?.classList.remove('scale-95', 'opacity-0');
            }, 10);
        },

        closeEditModal: function() {
            const wrapper = document.getElementById('loy-edit-wrapper');
            const overlay = document.getElementById('loy-edit-overlay');
            const container = document.getElementById('loy-edit-container');

            if (!wrapper) return;
            overlay?.classList.add('opacity-0');
            container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        submitEditForm: async function(e) {
            e.preventDefault();
            const id = document.getElementById('loy-edit-id').value;
            const btn = document.getElementById('loy-edit-submit');
            if (!btn || btn.disabled) return;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Envoi...';
            btn.disabled = true;

            try {
                const response = await fetch(`/loyers/${id}`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(Object.fromEntries(new FormData(e.target)))
                });
                const data = await response.json();
                if(response.ok) {
                    showToast('Loyer mis à jour', 'success');
                    this.closeEditModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(err) {
                showToast('Erreur serveur', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    };
</script>
