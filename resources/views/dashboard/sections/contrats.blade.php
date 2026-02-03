<div class="h-full flex flex-col gap-8" id="contrats-section-container">
    
    <div id="con-view-list" class="con-sub-view space-y-8">
        <!-- Header -->
        <div class="flex items-end justify-between border-b border-gray-100 pb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Contrats de Bail</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Administration des baux et suivis locatifs.</p>
            </div>
            @if(App\Helpers\PermissionHelper::can('contrats.create'))
            <button onclick="conSection.openModal('create')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouveau Bail
            </button>
            @endif
        </div>

        <!-- KPIs -->
        <div id="con-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Contrats</p>
                     <p class="text-3xl font-black text-gray-900">{{ count($data['contrats_list']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Actifs</p>
                     <p class="text-3xl font-black text-green-600">{{ $data['contrats_list']->where('statut', 'actif')->count() }}</p>
                </div>
                 <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
             <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Valeur Mensuelle</p>
                     <p class="text-3xl font-black text-[#cb2d2d]">{{ number_format($data['contrats_list']->where('statut', 'actif')->sum('loyer_montant'), 0, ',', ' ') }} <span class="text-sm text-gray-400 font-medium">F</span></p>
                </div>
                 <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-[#cb2d2d]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div id="con-table-container" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider w-24">Ref</th>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Logement</th>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Locataire</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Loyer</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-600">
                        @forelse($data['contrats_list'] as $con)
                        <tr class="hover:bg-gray-50/50 transition duration-150 group">
                            <td class="px-8 py-5 text-gray-400 font-mono text-xs">C-{{ str_pad($con->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900">{{ $con->bien->nom ?? 'Bien supprimé' }}</div>
                                <div class="text-xs text-gray-400 hidden group-hover:block transition-all">{{ $con->bien->type ?? '' }}</div>
                            </td>
                            <td class="px-8 py-5 capitalize">
                                @if($con->locataire)
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                            {{ substr($con->locataire->nom, 0, 1) }}
                                        </div>
                                        {{ $con->locataire->nom }}
                                    </div>
                                @else
                                    <span class="italic text-gray-300">Inconnu</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right font-mono font-bold">{{ number_format($con->loyer_montant, 0, ',', ' ') }} F</td>
                            <td class="px-8 py-5 text-center">
                                @if($con->statut === 'actif')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 border border-green-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-500 border border-gray-200 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                        {{ $con->statut }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="window.previewDoc({url: '{{ route('contrats.print', $con->id) }}', nom_original: 'Contrat_{{ str_replace([' ', "'"], ['_', '_'], $con->locataire->nom) }}.pdf', type_label: 'Contrat de Location'})" class="p-2 text-gray-400 hover:text-[#cb2d2d] hover:bg-white rounded-lg transition" title="Visualiser le Contrat (PDF)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                     </button>
                                    @if(App\Helpers\PermissionHelper::can('contrats.edit'))
                                    <button onclick="conSection.openModal('edit', {{ json_encode($con) }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-white rounded-lg transition" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endif
                                    @if(App\Helpers\PermissionHelper::can('contrats.delete'))
                                    <button onclick="conSection.requestDelete({{ $con->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition" title="Résilier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                             <td colspan="6" class="px-8 py-16 text-center text-gray-400 italic bg-gray-50/30">Aucun contrat en cours.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL (ULTRA COMPACT GRID) -->
    <div id="con-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div id="con-modal-overlay" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) conSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0" onclick="if(event.target === this) conSection.closeModal()">
                <div id="con-modal-container" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl opacity-0 scale-95 duration-300 border border-gray-100">
                    
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="con-modal-title" class="text-base font-bold text-gray-900">Éditer un Bail</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5 font-medium">Création du contrat.</p>
                        </div>
                        <button onclick="conSection.closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="con-main-form" class="p-6 space-y-4">
                        <input type="hidden" name="id" id="con-input-id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bien -->
                            <div class="col-span-1 md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Bien Immobilier (Libre)</label>
                                <select name="bien_id" id="con-input-bien" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                                    <option value="">Sélectionner un bien...</option>
                                    @foreach($data['biens_list']->whereIn('statut', ['libre', 'disponible']) as $bien)
                                        <option value="{{ $bien->id }}" data-loyer="{{ $bien->loyer_mensuel }}">{{ $bien->nom }} — {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Locataire -->
                            <div class="col-span-1 md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Locataire</label>
                                <select name="locataire_id" id="con-input-locataire" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                                    <option value="">Choisir un locataire...</option>
                                    @foreach($data['locataires_list'] as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Loyer -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Loyer Mensuel</label>
                                <input type="number" name="loyer_montant" id="con-input-loyer" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0 text-right" placeholder="0">
                            </div>

                            <!-- Date Début -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Date d'entrée</label>
                                <input type="date" name="date_debut" id="con-input-date" required value="{{ date('Y-m-d') }}" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
                            </div>

                            <!-- Date Fin -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Date de Fin (Facultatif)</label>
                                <input type="date" name="date_fin" id="con-input-date-fin" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
                            </div>

                            <!-- Type de Bail -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Type de Bail</label>
                                <select name="type_bail" id="con-input-type-bail" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                                    <option value="habitation">Habitation</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="professionnel">Professionnel</option>
                                </select>
                            </div>

                            <!-- Date de Signature -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Date de Signature</label>
                                <input type="date" name="date_signature" id="con-input-signature" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
                            </div>

                            <!-- Caution -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Caution (Garantie)</label>
                                <input type="number" name="caution" id="con-input-caution" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0 text-right" placeholder="0">
                            </div>

                            <!-- Frais de Dossier -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Frais de Dossier</label>
                                <input type="number" name="frais_dossier" id="con-input-frais" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0 text-right" placeholder="0">
                            </div>
                        </div>

                         <!-- Footer Actions -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="conSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="con-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Activer le Bail
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL (UNCHANGED) -->
    <div id="con-delete-modal" onclick="if(event.target === this) conSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="con-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Résilier ce bail ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Cette action est irréversible. Le bien sera libéré immédiatement et le contrat archivé.</p>
            <div class="flex flex-col gap-3">
                <button id="con-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Résilier le contrat
                </button>
                <button onclick="conSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.conSection = {
        deleteTargetId: null,

        currentMode: 'create',
        editId: null,

        openModal: function(mode, data = null) {
            this.currentMode = mode;
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');
            const form = document.getElementById('con-main-form');
            const btn = document.getElementById('con-submit-btn');
            const title = document.getElementById('con-modal-title');
            
            form.reset();
            
            if (mode === 'edit' && data) {
                this.editId = data.id;
                title.innerText = 'Modifier le Bail';
                
                // Populate fields
                document.getElementById('con-input-id').value = data.id;
                document.getElementById('con-input-bien').value = data.bien_id;
                document.getElementById('con-input-locataire').value = data.locataire_id;
                document.getElementById('con-input-loyer').value = Math.floor(data.loyer_montant);
                document.getElementById('con-input-date').value = data.date_debut;
                document.getElementById('con-input-date-fin').value = data.date_fin || '';
                document.getElementById('con-input-caution').value = data.caution ? Math.floor(data.caution) : '';
                document.getElementById('con-input-frais').value = data.frais_dossier ? Math.floor(data.frais_dossier) : '';
                document.getElementById('con-input-type-bail').value = data.type_bail || 'habitation';
                document.getElementById('con-input-signature').value = data.date_signature || '';

                // Disable fields that shouldn't be changed easily in edit to avoid conflicts
                document.getElementById('con-input-bien').disabled = true;
                document.getElementById('con-input-locataire').disabled = true;

                btn.innerHTML = 'Enregistrer modifications';
            } else {
                this.editId = null;
                title.innerText = 'Nouveau Bail';
                document.getElementById('con-input-id').value = '';
                document.getElementById('con-input-date').value = new Date().toISOString().split('T')[0];
                
                document.getElementById('con-input-bien').disabled = false;
                document.getElementById('con-input-locataire').disabled = false;
                
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Activer le Bail';
            }
            
            wrapper.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);
            
            btn.disabled = false;
        },

        closeModal: function() {
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');
            
            overlay.classList.add('opacity-0');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        requestDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('con-delete-modal');
            const container = document.getElementById('con-delete-container');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('con-delete-modal');
            const container = document.getElementById('con-delete-container');
            
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
            
            const btn = document.getElementById('con-confirm-delete-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Traitement...';
            btn.disabled = true;

            try {
                const response = await fetch(`/contrats/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if(data.success) {
                    showToast('Contrat résilié avec succès', 'success');
                    window.location.reload();
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

    // Bind Delete Confirmation Button
    document.getElementById('con-confirm-delete-btn').addEventListener('click', function() {
        conSection.executeDelete();
    });

    // Auto-fill loyer when selecting a Bien
    document.getElementById('con-input-bien').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if(option && option.dataset.loyer) {
            document.getElementById('con-input-loyer').value = Math.floor(option.dataset.loyer);
        }
    });

    document.getElementById('con-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('con-submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Activation...';
        btn.disabled = true;

        const formData = new FormData(this);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            let url = "{{ route('contrats.store') }}";
            let method = 'POST';

            if (conSection.currentMode === 'edit' && conSection.editId) {
                url = `/contrats/${conSection.editId}`;
                method = 'PUT';
            }

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
                conSection.closeModal();
                window.location.reload();
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
