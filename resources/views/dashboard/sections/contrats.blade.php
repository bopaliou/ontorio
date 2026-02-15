<div class="h-full flex flex-col gap-8" id="contrats-section-container">

    <div id="con-view-list" class="con-sub-view space-y-8 text-left">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Contrats & Baux',
            'subtitle' => 'Gestion juridique du patrimoine et relations locatives.',
            'icon' => 'document',
            'actions' => App\Helpers\PermissionHelper::can('contrats.create')
                ? '<button onclick="conSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Nouveau Contrat
                </button>'
                : ''
        ])

        <div id="con-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', ['label' => 'Contrats Actifs', 'value' => count($data['contrats_list']->where('statut', 'actif')), 'icon' => 'check', 'color' => 'blue'])
            @include('components.kpi-card', ['label' => 'En Attente', 'value' => count($data['contrats_list']->where('statut', 'en_attente')), 'icon' => 'plus', 'color' => 'gray'])
            @include('components.kpi-card', ['label' => 'Valeur Mensuelle', 'value' => number_format($data['contrats_list']->where('statut', 'actif')->sum('loyer_montant'), 0, ',', ' '), 'suffix' => 'F', 'icon' => 'money', 'color' => 'green'])
        </div>

        <div id="con-table-container">
            <x-data-table :headers="[['label' => 'Réf', 'classes' => 'text-white'], ['label' => 'Bien & Locataire', 'classes' => 'text-white'], ['label' => 'Période', 'classes' => 'text-white'], ['label' => 'Loyer', 'classes' => 'text-white'], ['label' => 'Statut', 'classes' => 'text-center text-white'], ['label' => 'Actions', 'classes' => 'text-right text-white']]" emptyMessage="Aucun contrat actif.">
                @forelse($data['contrats_list'] as $con)
                <tr class="hover:bg-gray-50/50 transition-all duration-300 group">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs">CNT-{{ str_pad($con->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $con->bien->nom ?? 'Bien inconnu' }}</div>
                        <div class="text-xs text-[#cb2d2d] font-bold uppercase mt-0.5">{{ $con->locataire->nom ?? 'Locataire inconnu' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-700">Du {{ \Carbon\Carbon::parse($con->date_debut)->translatedFormat('d M Y') }}</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $con->type_bail }}</div>
                    </td>
                    <td class="px-6 py-4 font-black text-gray-900">{{ format_money($con->loyer_montant) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $con->statut === 'actif' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $con->statut }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(App\Helpers\PermissionHelper::can('contrats.print'))
                            <a href="{{ route('contrats.print', $con->id) }}" target="_blank" class="group flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-[#1a2e3d] hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Imprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Imprimer</span>
                            </a>
                            @endif
                            @if(App\Helpers\PermissionHelper::can('contrats.edit'))
                            <button onclick="conSection.openModal('edit', {{ json_encode($con) }})" class="group flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Modifier</span>
                            </button>
                            @endif
                            @if(App\Helpers\PermissionHelper::can('contrats.delete'))
                            <button onclick="conSection.confirmDelete({{ json_encode($con) }})" class="group flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm gap-2 text-xs font-bold uppercase tracking-wider" title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Supprimer</span>
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
    <!-- MODAL: CREATE/EDIT (ROUGE ONTARIO) -->
    <div id="con-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="con-modal-title" role="dialog" aria-modal="true">
        <div id="con-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) conSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="con-modal-container" class="app-modal-panel max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300 my-8 min-h-[600px]">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="con-modal-title" class="text-lg font-black text-white">Gestion de Bail</h3>
                        <button onclick="conSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="con-main-form" onsubmit="conSection.submitForm(event)" class="p-8 text-left">
                        <input type="hidden" name="id" id="con-input-id">
                        <input type="hidden" name="statut" id="con-input-statut" value="actif">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <!-- Bien & Locataire -->
                            <div>
                                <label class="ontario-label">Bien Immobilier <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    </div>
                                    <select name="bien_id" id="con-input-bien" required class="ontario-input pl-10 appearance-none">
                                        <option value="">Choisir un bien...</option>
                                        @foreach($data['biens_all'] as $b)<option value="{{ $b->id }}">{{ $b->nom }}</option>@endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Locataire Titulaire <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <select name="locataire_id" id="con-input-locataire" required class="ontario-input pl-10 appearance-none">
                                        <option value="">Choisir un locataire...</option>
                                        @foreach($data['locataires_all'] as $l)<option value="{{ $l->id }}">{{ $l->nom }}</option>@endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Type & Loyer -->
                            <div>
                                <label class="ontario-label">Type de Bail <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <select name="type_bail" id="con-input-type" required class="ontario-input pl-10 appearance-none">
                                        <option value="habitation">Habitation</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="professionnel">Professionnel</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Loyer Mensuel <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <input type="number" name="loyer_montant" id="con-input-loyer" required class="ontario-input pl-10 font-bold" placeholder="0">
                                </div>
                            </div>

                            <!-- Caution & Frais -->
                            <div>
                                <label class="ontario-label">Caution</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <input type="number" name="caution" id="con-input-caution" class="ontario-input pl-10" placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Frais de Dossier</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="number" name="frais_dossier" id="con-input-frais" class="ontario-input pl-10" placeholder="0">
                                </div>
                            </div>

                            <!-- Dates -->
                            <div>
                                <label class="ontario-label">Date Début <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="date" name="date_debut" id="con-input-date-debut" required class="ontario-input pl-10">
                                </div>
                            </div>

                            <div>
                                <label class="ontario-label">Date Fin (Optionnel)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="date" name="date_fin" id="con-input-date-fin" class="ontario-input pl-10">
                                </div>
                            </div>

                            <!-- Signature & Renouvellement -->
                            <div>
                                <label class="ontario-label">Date Signature</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </div>
                                    <input type="date" name="date_signature" id="con-input-signature" class="ontario-input pl-10">
                                </div>
                            </div>

                            <div class="flex items-center h-full pt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="renouvellement_auto" id="con-input-renouv" value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#cb2d2d]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#cb2d2d]"></div>
                                    <span class="ms-3 text-sm font-bold text-gray-700">Renouvellement Auto</span>
                                </label>
                            </div>

                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="con-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Enregistrer le contrat</button>
                            <button type="button" onclick="conSection.closeModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE (ROUGE ONTARIO) -->
    <div id="con-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="con-delete-modal-title" role="dialog" aria-modal="true">
        <div id="con-delete-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) conSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="con-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 id="con-delete-modal-title" class="text-lg font-black text-white">Résilier le contrat ?</h3>
                        <button onclick="conSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-con-info" class="text-sm text-gray-900 mb-2 font-black uppercase"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">Cette action libérera le bien immédiatement. Elle est irréversible.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="conSection.executeDelete()" id="con-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer la résiliation</button>
                            <button onclick="conSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.conSection = {
        deleteTargetId: null,
        openModal: function(mode, data = null) {
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');
            const title = document.getElementById('con-modal-title');
            const form = document.getElementById('con-main-form');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset();
            document.getElementById('con-input-id').value = '';
            if(mode === 'edit' && data) {
                title.innerText = 'Modifier le Contrat';
                document.getElementById('con-input-id').value = data.id;
                document.getElementById('con-input-bien').value = data.bien_id;
                document.getElementById('con-input-locataire').value = data.locataire_id;
                document.getElementById('con-input-type').value = data.type_bail || 'habitation';
                document.getElementById('con-input-loyer').value = data.loyer_montant;
                document.getElementById('con-input-caution').value = data.caution || '';
                document.getElementById('con-input-frais').value = data.frais_dossier || '';
                document.getElementById('con-input-date-debut').value = data.date_debut ? data.date_debut.split('T')[0] : '';
                document.getElementById('con-input-date-fin').value = data.date_fin ? data.date_fin.split('T')[0] : '';
                document.getElementById('con-input-signature').value = data.date_signature ? data.date_signature.split('T')[0] : '';
                document.getElementById('con-input-renouv').checked = !!data.renouvellement_auto;
                document.getElementById('con-input-statut').value = data.statut;
            } else { 
                title.innerText = 'Nouveau Contrat';
                document.getElementById('con-input-statut').value = 'actif';
            }
        },
        closeModal: function() {
            const wrapper = document.getElementById('con-modal-wrapper');
            const overlay = document.getElementById('con-modal-overlay');
            const container = document.getElementById('con-modal-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        submitForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('con-submit-btn');
            if(!btn || btn.disabled) return;
            const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
            const id = document.getElementById('con-input-id').value;
            try {
                const formData = new FormData(e.target);
                const payload = Object.fromEntries(formData);
                // Handle checkbox type manually if needed or let PHP handle '1'
                if (!payload.renouvellement_auto) payload.renouvellement_auto = 0;

                const res = await fetch(id ? `/contrats/${id}` : '/contrats', {
                    method: id ? 'PUT' : 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if(res.ok) { showToast('Succès', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { const d = await res.json(); showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        confirmDelete: function(con) {
            this.deleteTargetId = con.id;
            const infoEl = document.getElementById('del-con-info');
            if (infoEl) infoEl.textContent = (con.bien?.nom || 'Bien') + ' - ' + (con.locataire?.nom || 'Locataire');
            const wrapper = document.getElementById('con-delete-modal-wrapper');
            const overlay = document.getElementById('con-delete-modal-overlay');
            const container = document.getElementById('con-delete-container');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() {
            const wrapper = document.getElementById('con-delete-modal-wrapper');
            const overlay = document.getElementById('con-delete-modal-overlay');
            const container = document.getElementById('con-delete-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); this.deleteTargetId = null; }, 300);
        },
        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('con-confirm-delete-btn');
            const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
            try {
                const res = await fetch(`/contrats/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                if(res.ok) { showToast('Résilié', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast('Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
        }
    };
</script>
