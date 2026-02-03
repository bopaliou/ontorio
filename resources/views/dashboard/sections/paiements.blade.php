<div class="flex flex-col gap-6" id="paiements-section-container">
    
    <!-- SECTION: LISTE PRINCIPALE -->
    <div id="pai-view-list" class="pai-sub-view space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-[#1A365D]">Journal des Encaissements</h2>
                <p class="text-sm text-gray-500 mt-1">Historique complet des transactions financières.</p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'comptable']))
            <button onclick="paiSection.openModal('create')" class="bg-[#D32F2F] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-200 hover:bg-[#C62828] transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Enregistrer un Paiement
            </button>
            @endif
        </div>

        <!-- KPIs Comptabilité -->
        <div id="pai-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Encaissé Aujourd'hui</p>
                @php
                    $encaisseToday = $data['paiements_list']->filter(function($p) {
                        return \Carbon\Carbon::parse($p->date_paiement)->isToday();
                    })->sum('montant');
                    
                    $thisMonth = \Carbon\Carbon::now()->format('Y-m');
                    $encaisseMonth = $data['paiements_list']->filter(function($p) use ($thisMonth) {
                        return \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth;
                    })->sum('montant');
                @endphp
                <p class="text-3xl font-extrabold text-green-600 mt-2">{{ number_format($encaisseToday, 0, ',', ' ') }} <span class="text-lg">F</span></p>
            </div>
            <div class="bg-gradient-to-br from-[#1A365D] to-[#243B55] p-6 rounded-2xl shadow-lg text-white">
                <p class="text-xs font-bold text-blue-200 uppercase tracking-wider">Global Mois Actuel</p>
                <p class="text-3xl font-extrabold mt-2">{{ number_format($encaisseMonth, 0, ',', ' ') }} <span class="text-lg">F</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Transactions (Mois)</p>
                <p class="text-3xl font-extrabold text-[#1A365D] mt-2">{{ $data['paiements_list']->filter(function($p) use ($thisMonth) {
                    return \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth;
                })->count() }}</p>
            </div>
        </div>

        <!-- Table -->
        <div id="pai-table-container" class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ontario-table w-full text-left border-collapse">
                    <thead class="bg-[#1A365D]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white">Date & Référence</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white">Locataire</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-center">Méthode</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-right">Montant Encaissé</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-center">Preuve</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data['paiements_list'] as $pai)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($pai->date_paiement)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5">REF: {{ $pai->reference ?? 'TRAN-'.$pai->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 capitalize">{{ $pai->loyer->contrat->locataire->nom ?? 'Inconnu' }}</div>
                                <div class="text-xs text-gray-400">Mois: {{ \Carbon\Carbon::parse($pai->loyer->mois)->translatedFormat('F Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold uppercase
                                    @if(($pai->mode ?? 'espèces') == 'espèces') bg-amber-100 text-amber-700
                                    @elseif($pai->mode == 'virement') bg-blue-100 text-blue-700
                                    @else bg-purple-100 text-purple-700
                                    @endif">
                                    {{ $pai->mode ?? 'Espèces' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-extrabold text-green-600 text-lg">+ {{ number_format($pai->montant, 0, ',', ' ') }} F</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pai->preuve)
                                <button onclick="window.previewDoc({url: '{{ asset('storage/' . $pai->preuve) }}', nom_original: 'Preuve_{{ $pai->reference }}.{{ pathinfo($pai->preuve, PATHINFO_EXTENSION) }}', type_label: 'Preuve de Paiement'})" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all" 
                                   title="Voir Preuve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @else
                                <span class="text-gray-300 text-[10px] font-bold uppercase italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $pai->loyer_id) }}', nom_original: 'Quittance_{{ str_replace([' ', "'"], ['_', '_'], $pai->loyer->contrat->locataire->nom) }}.pdf', type_label: 'Quittance de Loyer'})" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-[#D32F2F] hover:text-white transition-all" 
                                       title="Visualiser le Reçu">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @if(in_array(auth()->user()->role, ['admin', 'comptable']))
                                    <button onclick="paiSection.confirmDelete({{ $pai->id }})" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all" 
                                       title="Supprimer (Annuler)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-gray-400 font-medium">Aucun paiement enregistré</p>
                                    <p class="text-gray-300 text-sm mt-1">Les encaissements apparaîtront ici</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL (ULTRA COMPACT GRID) -->
    <div id="pai-modal-overlay" onclick="if(event.target === this) paiSection.closeModal()" class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-4">
        <div id="pai-modal-container" class="bg-white w-full sm:max-w-xl rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100">
            
            <!-- Header Compact -->
            <div class="bg-[#1A365D] px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white">Nouvel Encaissement</h3>
                    <p class="text-blue-100/60 text-[10px] uppercase font-black tracking-widest mt-0.5">Enregistrer un paiement</p>
                </div>
                <button onclick="paiSection.closeModal()" class="text-white/60 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="pai-main-form" action="{{ route('paiements.store') }}" method="POST" target="pai_post_target" class="p-6 space-y-4">
                @csrf
                
                <!-- Sélection Loyer -->
                <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#1A365D]/10 focus-within:border-[#1A365D] transition-all">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Loyer à solder</label>
                    <select name="loyer_id" id="pai-select-loyer" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                        @php
                            $unpaidLoyers = $data['loyers_list']->filter(fn($l) => strtolower(trim($l->statut)) !== 'payé');
                        @endphp
                        
                        @if($unpaidLoyers->count() > 0)
                            <option value="">-- Sélectionner un loyer --</option>
                            @foreach($unpaidLoyers as $l)
                                <option value="{{ $l->id }}" data-montant="{{ $l->montant }}" data-locataire="{{ $l->contrat->locataire->nom }}" data-mois="{{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }}">
                                    {{ $l->contrat->locataire->nom }} — {{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }} — {{ number_format($l->montant,0,',',' ') }} F
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled selected>Aucun loyer impayé trouvé</option>
                        @endif
                    </select>
                </div>

                <!-- Info Card Compact -->
                <div id="pai-locataire-card" class="hidden bg-blue-50/50 rounded-xl p-3 border border-blue-100 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-[#1A365D] uppercase tracking-widest opacity-60">Locataire</p>
                        <p id="pai-card-locataire" class="text-xs font-bold text-gray-900 leading-tight">--</p>
                        <p id="pai-card-mois" class="text-[10px] text-gray-500 mt-0.5">--</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-[#cb2d2d] uppercase tracking-widest opacity-60">Montant Dû</p>
                        <p id="pai-card-montant" class="text-sm font-black text-[#1A365D] leading-tight">0 F</p>
                    </div>
                </div>

                <!-- Grid Montant / Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#1A365D]/10 focus-within:border-[#1A365D] transition-all">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Montant Encaissé (F)</label>
                        <input type="number" name="montant" id="pai-input-montant" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 text-right font-mono" placeholder="0">
                    </div>
                    <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#1A365D]/10 focus-within:border-[#1A365D] transition-all">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Date Paiement</label>
                        <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
                    </div>
                </div>

                <!-- Mode de Règlement Compact -->
                <div>
                     <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mode de Règlement</label>
                     <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer group">
                            <input type="radio" name="mode" value="espèces" checked class="hidden peer">
                            <div class="peer-checked:border-[#cb2d2d] peer-checked:bg-red-50 peer-checked:text-[#cb2d2d] border border-gray-200 rounded-xl p-2 flex flex-col items-center justify-center transition-all bg-white hover:border-gray-300 h-14">
                                <span class="text-lg">💵</span>
                                <span class="text-[8px] font-black uppercase tracking-tighter">Espèces</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="mode" value="virement" class="hidden peer">
                            <div class="peer-checked:border-[#cb2d2d] peer-checked:bg-red-50 peer-checked:text-[#cb2d2d] border border-gray-200 rounded-xl p-2 flex flex-col items-center justify-center transition-all bg-white hover:border-gray-300 h-14">
                                <span class="text-lg">🏦</span>
                                <span class="text-[8px] font-black uppercase tracking-tighter">Virement</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="mode" value="mobile" class="hidden peer">
                            <div class="peer-checked:border-[#cb2d2d] peer-checked:bg-red-50 peer-checked:text-[#cb2d2d] border border-gray-200 rounded-xl p-2 flex flex-col items-center justify-center transition-all bg-white hover:border-gray-300 h-14">
                                <span class="text-lg">📱</span>
                                <span class="text-[8px] font-black uppercase tracking-tighter">Mobile</span>
                            </div>
                        </label>
                     </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="paiSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                    <button type="submit" id="pai-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/20 text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Valider
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DELETE CONFIRMATION -->
    <div id="pai-delete-modal" onclick="if(event.target === this) paiSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="pai-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
             <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Annuler ce paiement ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                Le montant sera retiré de la caisse et le loyer associé repassera en "Impayé" pour le locataire.
            </p>
            <div class="flex flex-col gap-3">
                <button onclick="paiSection.executeDelete()" id="pai-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Supprimer
                </button>
                <button onclick="paiSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>

    <!-- IFRAME MASQUE POUR LES POST (Anti-Reload Pattern) -->
    <iframe name="pai_post_target" class="hidden"></iframe>
    <iframe id="pai_refresh_iframe" class="hidden"></iframe>
</div>

<script>
    window.paiSection = {
        openModal: function(mode) {
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');
            overlay.classList.remove('hidden');
            setTimeout(() => { container.classList.remove('scale-95', 'opacity-0'); }, 10);
        },

        closeModal: function() {
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { overlay.classList.add('hidden'); }, 300);
        },

        onStoreSuccess: function(msg) {
             const btn = document.getElementById('pai-submit-btn');
             btn.innerHTML = '✅ Encaissé !';
             btn.classList.replace('from-[#C62828]', 'from-green-600');
             btn.classList.replace('to-[#D32F2F]', 'to-green-500');
             
             // Refresh UI logic
             const refreshIframe = document.getElementById('pai_refresh_iframe');
             refreshIframe.src = '{{ route('dashboard') }}#paiements';
             refreshIframe.onload = () => {
                 const iframeDoc = refreshIframe.contentDocument || refreshIframe.contentWindow.document;
                 const newTable = iframeDoc.getElementById('pai-table-container');
                 const newKpi = iframeDoc.getElementById('pai-kpi-container');
                 const newSelect = iframeDoc.getElementById('pai-select-loyer');

                 if(newTable) document.getElementById('pai-table-container').innerHTML = newTable.innerHTML;
                 if(newKpi) document.getElementById('pai-kpi-container').innerHTML = newKpi.innerHTML;
                 // Mettre à jour aussi le select car un loyer a disparu
                 if(newSelect) document.getElementById('pai-select-loyer').innerHTML = newSelect.innerHTML;
                 
                 setTimeout(() => {
                     this.closeModal();
                     // Reset bouton
                     btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Valider';
                     btn.classList.replace('from-green-600', 'from-[#C62828]');
                     btn.classList.replace('to-green-500', 'to-[#D32F2F]');
                     btn.disabled = false;
                 }, 1000);
             };
        },

        onStoreError: function(msg) {
            const btn = document.getElementById('pai-submit-btn');
            btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Erreur';
            btn.disabled = false;
            alert(msg); // Plus simple pour debug, mais on peut faire un toast si dispo
            setTimeout(() => {
                btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Valider';
            }, 3000);
        }
    };

    // Feedback de chargement
    document.getElementById('pai-main-form').addEventListener('submit', function() {
        const btn = document.getElementById('pai-submit-btn');
        btn.innerHTML = '<svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Envoi...';
        btn.disabled = true;
    });

    // Mise à jour dynamique de la card locataire
    document.getElementById('pai-select-loyer').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const card = document.getElementById('pai-locataire-card');
        const inputMontant = document.getElementById('pai-input-montant');
        
        if (this.value) {
            const montant = selected.dataset.montant;
            const locataire = selected.dataset.locataire;
            const mois = selected.dataset.mois;
            
            document.getElementById('pai-card-locataire').textContent = locataire;
            document.getElementById('pai-card-mois').textContent = 'Période: ' + mois;
            document.getElementById('pai-card-montant').textContent = new Intl.NumberFormat('fr-FR').format(montant) + ' F';
            inputMontant.value = montant;
            inputMontant.placeholder = montant;
            
            card.classList.remove('hidden');
            card.classList.add('animate-fade-in');
        } else {
            card.classList.add('hidden');
            inputMontant.value = '';
        }
    });

    // Gestion Suppression Paiement
    window.paiSection.confirmDelete = function(id) {
        this.deleteTargetId = id;
        const modal = document.getElementById('pai-delete-modal');
        const container = document.getElementById('pai-delete-container');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }, 10);
    };

    window.paiSection.closeDeleteModal = function() {
        const modal = document.getElementById('pai-delete-modal');
        const container = document.getElementById('pai-delete-container');
        modal.classList.add('opacity-0');
        container.classList.remove('scale-100');
        container.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            this.deleteTargetId = null;
        }, 300);
    };

    window.paiSection.executeDelete = async function() {
        if(!this.deleteTargetId) return;
        const btn = document.getElementById('pai-confirm-delete-btn');
        const originalText = btn.innerText;
        btn.innerText = 'Suppression...';
        btn.disabled = true;

        try {
            const response = await fetch(`/dashboard/paiements/${this.deleteTargetId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();

            if(data.success) {
                // Refresh logic
                const refreshIframe = document.getElementById('pai_refresh_iframe');
                refreshIframe.src = '{{ route('dashboard') }}#paiements';
                refreshIframe.onload = () => {
                     const iframeDoc = refreshIframe.contentDocument || refreshIframe.contentWindow.document;
                     const newTable = iframeDoc.getElementById('pai-table-container');
                     const newKpi = iframeDoc.getElementById('pai-kpi-container');
                     
                     if(newTable) document.getElementById('pai-table-container').innerHTML = newTable.innerHTML;
                     if(newKpi) document.getElementById('pai-kpi-container').innerHTML = newKpi.innerHTML;
                     
                     showToast('Paiement supprimé et loyer restauré', 'success');
                };
            } else {
                showToast(data.message || 'Erreur lors de la suppression', 'error');
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur serveur', 'error');
        } finally {
             btn.innerText = originalText;
             btn.disabled = false;
             this.closeDeleteModal();
        }
    };
</script>

<style>
    .mode-card input:checked + div {
        border-color: #D32F2F !important;
        background-color: #FEF2F2 !important;
        box-shadow: 0 10px 15px -3px rgb(211 47 47 / 0.2) !important;
    }
    
    .mode-card input:checked + div span:last-child {
        color: #D32F2F !important;
    }
    
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>
