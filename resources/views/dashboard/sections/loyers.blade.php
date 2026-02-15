<div class="h-full flex flex-col gap-8" id="loyers-section-container">

    <div id="loy-view-list" class="loy-sub-view space-y-8 text-left">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Appels de Loyers',
            'subtitle' => 'Suivi des quittances, facturation mensuelle et recouvrement.',
            'icon' => 'money',
            'actions' => App\Helpers\PermissionHelper::can('loyers.generate')
                ? '<button onclick="loySection.generateMois()" class="bg-[#274256] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition-all hover:bg-[#1a2e3d] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Générer Quittances (Mois)
                    </button>'
                : ''
        ])

        <!-- KPIs / Stats Rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Taux de Recouvrement -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recouvrement</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-gray-900">{{ $data['financial_stats']['taux_recouvrement'] }}%</div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $data['financial_stats']['taux_recouvrement'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Émis -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gray-50 text-gray-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Émis</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">{{ format_money($data['financial_stats']['loyers_factures']) }}</div>
                    <p class="text-xs text-gray-500 font-medium mt-1">Facturé ce mois</p>
                </div>
            </div>

            <!-- Total Encaissé -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Encaissé</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-green-600">{{ format_money($data['financial_stats']['loyers_encaisses']) }}</div>
                    <p class="text-xs text-gray-500 font-medium mt-1">{{ $data['financial_stats']['nb_payes'] }} quittances réglées</p>
                </div>
            </div>

            <!-- Impayés -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Impayés</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-red-600">{{ format_money($data['financial_stats']['arrieres_total']) }}</div>
                    <p class="text-xs text-red-400 font-bold mt-1">{{ $data['financial_stats']['nb_impayes'] }} en attente</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div id="loy-table-container">
            <x-data-table :headers="[['label' => 'Période', 'classes' => 'text-white'], ['label' => 'Locataire & Bien', 'classes' => 'text-white'], ['label' => 'Montant', 'classes' => 'text-white'], ['label' => 'Statut', 'classes' => 'text-center text-white'], ['label' => 'Actions', 'classes' => 'text-right text-white']]" emptyMessage="Aucune quittance générée.">
                @forelse($data['loyers_list'] as $loy)
                <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                    <td class="px-6 py-4 font-bold text-gray-900">{{ \Carbon\Carbon::parse($loy->mois)->translatedFormat('F Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $loy->contrat->locataire->nom ?? 'Inconnu' }}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $loy->contrat->bien->nom ?? '--' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($loy->statut === 'annulé')
                            <div class="font-black text-gray-400 line-through">{{ format_money($loy->montant) }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">ANNULÉ</div>
                        @else
                            <div class="font-black text-gray-900">{{ format_money($loy->montant) }}</div>
                            @if($loy->penalite > 0)
                                <div class="text-[10px] text-red-500 font-bold uppercase" title="Pénalité de retard">+ {{ format_money($loy->penalite, '') }}</div>
                            @endif
                            @if($loy->reste_a_payer > 0 && $loy->statut !== 'payé')
                                <div class="text-[10px] text-red-600 font-black uppercase mt-1">Dû: {{ format_money($loy->reste_a_payer) }}</div>
                            @endif
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                            @if($loy->statut === 'payé') bg-green-100 text-green-700
                            @elseif($loy->statut === 'partiellement_payé') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $loy->statut }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($loy->statut !== 'payé')
                            <button onclick="loySection.openPaymentModal({{ json_encode($loy) }})" class="group flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Encaisser">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Encaisser</span>
                            </button>
                            @endif
                            <button onclick="loySection.openEditModal({{ json_encode($loy) }})" class="group flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Modifier</span>
                            </button>
                            <button onclick="loySection.openDeleteModal({{ $loy->id }})" class="group flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Supprimer</span>
                            </button>
                            @if(App\Helpers\PermissionHelper::can('loyers.quittance'))
                            <a href="{{ route('loyers.quittance', $loy->id) }}" target="_blank" class="group flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-[#1a2e3d] hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Télécharger PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>PDF</span>
                            </a>
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
    <!-- MODAL: ENCAISSEMENT (ROUGE ONTARIO) -->
    <div id="loy-payment-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="loy-payment-title" role="dialog" aria-modal="true">
        <div id="loy-payment-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) loySection.closePaymentModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loy-payment-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="loy-payment-title" class="text-lg font-black text-white">Encaisser un loyer</h3>
                        <button onclick="loySection.closePaymentModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="loy-payment-form" action="{{ route('paiements.store') }}" onsubmit="loySection.submitPaymentForm(event)" method="POST" enctype="multipart/form-data" class="p-8 space-y-5 text-left">
                        @csrf
                        <input type="hidden" name="loyer_id" id="loy-payment-loyer-id">
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <p id="loy-payment-locataire" class="text-base font-black text-[#1a2e3d] capitalize">--</p>
                            <p id="loy-payment-periode" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">--</p>
                            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-baseline">
                                <span class="text-[9px] font-black text-gray-400 uppercase">Total à percevoir</span>
                                <span id="loy-payment-montant" class="text-xl font-black text-[#cb2d2d]">0 F</span>
                            </div>
                        </div>
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10">Somme perçue</label>
                                <input type="number" name="montant" id="loy-payment-montant-input" required class="block w-full bg-white border-2 border-gray-300 rounded-2xl px-5 py-3.5 text-xl font-black text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                                <div id="loy-payment-live-balance" class="mt-2 px-2 hidden">
                                    <span class="text-[10px] font-bold uppercase tracking-wider" id="loy-payment-live-text"></span>
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10">Date de paiement</label>
                                <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="loy-payment-submit" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Confirmer le paiement</button>
                            <button type="button" onclick="loySection.closePaymentModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endpush
    @push('modals')
    <!-- MODAL: EDIT LOYER (BLUE ONTARIO) -->
    <div id="loy-edit-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="loy-edit-title" role="dialog" aria-modal="true">
        <div id="loy-edit-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) loySection.closeEditModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loy-edit-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#1a2e3d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="loy-edit-title" class="text-lg font-black text-white">Modifier le Loyer</h3>
                        <button onclick="loySection.closeEditModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="loy-edit-form" action="" onsubmit="loySection.submitEditForm(event)" method="POST" class="p-8 space-y-5 text-left">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="mois" id="loy-edit-mois">
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 mb-4">
                             <p id="loy-edit-info" class="text-sm font-bold text-gray-700">--</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Montant -->
                             <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Montant</label>
                                <input type="number" name="montant" id="loy-edit-montant" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-[#1a2e3d] outline-none">
                             </div>
                             
                             <!-- Statut -->
                             <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Statut</label>
                                <select name="statut" id="loy-edit-statut" onchange="loySection.togglePartialInput()" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-[#1a2e3d] outline-none">
                                    <option value="émis">Émis</option>
                                    <option value="payé">Payé</option>
                                    <option value="partiellement_payé">Partiellement Payé</option>
                                    <option value="en_retard">En Retard</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                             </div>

                             <!-- Pénalité de Retard -->
                             <div id="loy-edit-penalty-wrapper" class="hidden">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pénalité de Retard (Optionnel)</label>
                                <input type="number" name="penalite" id="loy-edit-penalite" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-[#1a2e3d] outline-none" placeholder="0 (Laisser vide si aucune)">
                             </div>

                             <!-- Montant Encaissé (Partiel) -->
                             <div id="loy-edit-partial-wrapper" class="hidden">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Montant Encaissé</label>
                                <input type="number" name="montant_paye" id="loy-edit-montant-paye" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-bold focus:border-[#1a2e3d] outline-none" placeholder="Montant reçu...">
                             </div>

                             <!-- Note -->
                             <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Note (Optionnel)</label>
                                <textarea name="note_annulation" id="loy-edit-note" rows="2" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-medium focus:border-[#1a2e3d] outline-none" placeholder="Motif ou détail..."></textarea>
                             </div>
                        </div>

                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="loy-edit-submit" class="w-full bg-[#1a2e3d] text-white py-4 rounded-2xl font-black hover:bg-[#0f1d2a] transition shadow-xl text-xs uppercase tracking-widest">Sauvegarder</button>
                            <button type="button" onclick="loySection.closeEditModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL: SUPPRESSION (DANGER) -->
    <div id="loy-delete-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="loy-delete-title" role="dialog" aria-modal="true">
        <div id="loy-delete-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) loySection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="loy-delete-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="p-8 text-center space-y-6">
                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <h3 id="loy-delete-title" class="text-2xl font-black text-gray-900">Supprimer la quittance ?</h3>
                        <p class="text-gray-500 font-medium">Attention, cette action est irréversible. La quittance ainsi que tous les paiements associés seront définitivement effacés.</p>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <button onclick="loySection.closeDeleteModal()" class="w-full py-4 text-gray-400 font-bold hover:bg-gray-50 rounded-2xl transition text-xs uppercase tracking-widest">Annuler</button>
                            <button onclick="loySection.confirmDelete()" class="w-full bg-red-600 text-white py-4 rounded-2xl font-black hover:bg-red-700 transition shadow-xl text-xs uppercase tracking-widest">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.loySection = {
        deleteTargetId: null,
        
        // --- PAYMENT MODAL LOGIC ---
        openPaymentModal: function(loyer) {
            const wrapper = document.getElementById('loy-payment-wrapper');
            const overlay = document.getElementById('loy-payment-overlay');
            const container = document.getElementById('loy-payment-container');
            if (!wrapper) return;
            document.getElementById('loy-payment-loyer-id').value = loyer.id;
            document.getElementById('loy-payment-locataire').textContent = loyer.contrat?.locataire?.nom || 'Inconnu';
            document.getElementById('loy-payment-periode').textContent = 'Terme : ' + (loyer.mois || '--');
            document.getElementById('loy-payment-montant').textContent = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(loyer.montant) + ' F';
            
            const reste = loyer.montant - (loyer.montant_paye_cache || 0);
            this.currentLoyerReste = reste;
            document.getElementById('loy-payment-montant-input').value = Math.round(reste);

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); this.updateLiveBalance(); }, 10);
        },
        closePaymentModal: function() {
            const wrapper = document.getElementById('loy-payment-wrapper');
            const overlay = document.getElementById('loy-payment-overlay');
            const container = document.getElementById('loy-payment-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        submitPaymentForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('loy-payment-submit');
            const orig = btn.innerHTML; btn.innerHTML = 'Envoi...'; btn.disabled = true;
            try {
                const res = await fetch(e.target.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(e.target) });
                const d = await res.json();
                if(res.ok) { showToast('Payé', 'success'); this.closePaymentModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        updateLiveBalance: function() {
            const val = parseFloat(document.getElementById('loy-payment-montant-input').value) || 0;
            const target = this.currentLoyerReste || 0;
            const lb = document.getElementById('loy-payment-live-balance');
            const lt = document.getElementById('loy-payment-live-text');
            if (lb && lt) {
                lb.classList.remove('hidden');
                const diff = target - val;
                if (diff > 0) { lb.className = "mt-2 px-2 text-amber-600 font-bold"; lt.textContent = "Reliquat: " + new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(diff) + " F"; }
                else if (diff === 0) { lb.className = "mt-2 px-2 text-green-600 font-bold"; lt.textContent = "Solde complet ✅"; }
                else { lb.className = "mt-2 px-2 text-blue-600 font-bold"; lt.textContent = "Trop-perçu: " + new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.abs(diff)) + " F"; }
            }
        },

        // --- EDIT MODAL LOGIC ---
        openEditModal: function(loyer) {
            const wrapper = document.getElementById('loy-edit-wrapper');
            const overlay = document.getElementById('loy-edit-overlay');
            const container = document.getElementById('loy-edit-container');
            if (!wrapper) return;

            // Populate Form
            const form = document.getElementById('loy-edit-form');
            form.action = `/loyers/${loyer.id}`;
            
            document.getElementById('loy-edit-info').textContent = `${loyer.contrat?.locataire?.nom || 'Inconnu'} - ${loyer.mois}`;
            document.getElementById('loy-edit-mois').value = loyer.mois;
            document.getElementById('loy-edit-montant').value = loyer.montant;
            document.getElementById('loy-edit-statut').value = loyer.statut;
            document.getElementById('loy-edit-note').value = loyer.note_annulation || '';
            document.getElementById('loy-edit-penalite').value = loyer.penalite || '';
            
            // Handle Partial Payment
            const partialInput = document.getElementById('loy-edit-montant-paye');
            if(partialInput) {
                // If already partially paid, get the paid amount (assuming 'montant_paye_cache' exists or similar logic)
                // Since 'montant_paye_cache' is available in the object from data-table usually
                partialInput.value = loyer.montant_paye_cache || '';
            }
            this.togglePartialInput();

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        togglePartialInput: function() {
            const status = document.getElementById('loy-edit-statut').value;
            const partialWrapper = document.getElementById('loy-edit-partial-wrapper');
            const penaltyWrapper = document.getElementById('loy-edit-penalty-wrapper');
            
            if(partialWrapper) {
                if(status === 'partiellement_payé') {
                    partialWrapper.classList.remove('hidden');
                } else {
                    partialWrapper.classList.add('hidden');
                }
            }

            if(penaltyWrapper) {
                if(status === 'en_retard') {
                    penaltyWrapper.classList.remove('hidden');
                } else {
                    penaltyWrapper.classList.add('hidden');
                }
            }
        },
        closeEditModal: function() {
            const wrapper = document.getElementById('loy-edit-wrapper');
            const overlay = document.getElementById('loy-edit-overlay');
            const container = document.getElementById('loy-edit-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        submitEditForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('loy-edit-submit');
            const orig = btn.innerHTML; btn.innerHTML = 'Sauvegarde...'; btn.disabled = true;
            try {
                const res = await fetch(e.target.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(e.target) });
                const d = await res.json();
                if(res.ok) { showToast('Loyer mis à jour', 'success'); this.closeEditModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },

        // --- GENERATION LOGIC ---
        generateMois: async function() {
            if(!confirm('Voulez-vous générer les appels de loyers pour le mois en cours ?')) return;
            
            try {
                const res = await fetch('/loyers/generer-mois', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const d = await res.json();
                if(res.ok) {
                    showToast(d.message || 'Génération terminée', 'success');
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast(d.message || 'Erreur lors de la génération', 'error');
                }
            } catch(e) {
                showToast('Erreur serveur', 'error');
            }
        },

        // --- DELETE LOGIC ---
        openDeleteModal: function(id) {
            this.deleteTargetId = id;
            const wrapper = document.getElementById('loy-delete-wrapper');
            const overlay = document.getElementById('loy-delete-overlay');
            const container = document.getElementById('loy-delete-container');
            
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() {
            this.deleteTargetId = null;
            const wrapper = document.getElementById('loy-delete-wrapper');
            const overlay = document.getElementById('loy-delete-overlay');
            const container = document.getElementById('loy-delete-container');
            
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        confirmDelete: async function() {
            if(!this.deleteTargetId) return;
            
            // Disable button to prevent double-click
            const btn = document.getElementById('loy-confirm-delete-btn');
            const originalText = btn ? btn.innerText : 'Confirmer';
            if(btn) {
                btn.innerText = '...';
                btn.disabled = true;
            }

            try {
                const res = await fetch(`/loyers/${this.deleteTargetId}`, { 
                    method: 'DELETE', 
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } 
                });
                const d = await res.json();
                
                if(res.ok || res.status === 404) { 
                    showToast('Loyer supprimé', 'success'); 
                    this.closeDeleteModal();
                    if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); 
                } else { 
                    showToast(d.message || 'Erreur', 'error');
                    if(btn) {
                        btn.innerText = originalText;
                        btn.disabled = false;
                    }
                }
            } catch(e) { 
                showToast('Erreur serveur', 'error');
                if(btn) {
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            }
        }
    };

    document.getElementById('loy-payment-montant-input')?.addEventListener('input', () => window.loySection.updateLiveBalance());
</script>
