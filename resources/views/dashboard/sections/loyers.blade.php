<div class="flex flex-col gap-6" id="loyers-section-container">

    <!-- SECTION: LISTE PRINCIPALE -->
    <div id="loy-view-list" class="loy-sub-view space-y-6">
        <!-- Header Uniforme -->
        @include('components.section-header', [
            'title' => 'Suivi des Loyers',
            'subtitle' => 'Pointage des quittances et gestion des impayés.',
            'icon' => 'money',
            'actions' => App\Helpers\PermissionHelper::can('loyers.generate')
                ? '<form id="loy-gen-form" method="POST" action="' . route('loyers.genererMois') . '" target="loy_post_target" class="inline">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <button type="submit" id="loy-gen-btn" class="bg-white border-2 border-gray-100 text-[#274256] px-5 py-2.5 rounded-xl text-sm font-black shadow-sm hover:bg-gray-50 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span id="loy-gen-text">Générer / Mettre à jour</span>
                    </button>
                </form>'
                : ''
        ])

        @php
            $moisActuel = \Carbon\Carbon::now()->format('Y-m');
            $factureMois = $data['loyers_list']->where('mois', $moisActuel)->sum('montant');
            $encaisseMois = $data['loyers_list']->where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
            $retardTotal = $data['loyers_list']->whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])->sum(function($l) {
                return $l->montant - $l->montant_paye_cache;
            });
            $tauxCollecte = $factureMois > 0 ? round(($encaisseMois / $factureMois) * 100) : 0;
        @endphp

        <!-- KPIs Uniformes -->
        <div id="loy-kpi-container" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @include('components.kpi-card', [
                'label' => 'Facturé (Mois)',
                'value' => number_format($factureMois, 0, ',', ' '),
                'suffix' => 'F',
                'icon' => 'chart',
                'color' => 'gray'
            ])
            @include('components.kpi-card', [
                'label' => 'Encaissé (Mois)',
                'value' => number_format($encaisseMois, 0, ',', ' '),
                'suffix' => 'F',
                'icon' => 'check',
                'color' => 'green'
            ])
            @include('components.kpi-card', [
                'label' => 'Impayés Global',
                'value' => number_format($retardTotal, 0, ',', ' '),
                'suffix' => 'F',
                'icon' => 'warning',
                'color' => 'red'
            ])
            @include('components.kpi-card', [
                'label' => 'Taux Collecte',
                'value' => $tauxCollecte,
                'suffix' => '%',
                'icon' => 'chart',
                'color' => 'blue'
            ])
        </div>

        <!-- Table -->
        <div id="loy-table-container">
            <x-data-table :headers="[
                ['label' => 'Période', 'classes' => 'text-white/80'],
                ['label' => 'Locataire & Bien', 'classes' => 'text-white/80'],
                ['label' => 'Montant', 'classes' => 'text-right text-white/80'],
                ['label' => 'Statut', 'classes' => 'text-center text-white/80'],
                ['label' => 'Actions', 'classes' => 'text-right text-white/80']
            ]" emptyMessage="Aucun loyer émis pour cette période.">

                @if(count($data['loyers_list']) > 0)
                    @foreach($data['loyers_list'] as $loy)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-6 py-4">
                        <div class="text-gray-900 font-black uppercase text-xs">{{ \Carbon\Carbon::parse($loy->mois)->translatedFormat('F Y') }}</div>
                        <div class="text-[11px] text-gray-400 font-bold uppercase mt-0.5">Échéance: {{ \Carbon\Carbon::parse($loy->mois)->endOfMonth()->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-700 font-bold capitalize">{{ $loy->contrat->locataire->nom ?? 'Inconnu' }}</div>
                        <div class="text-[11px] text-gray-400 italic">Bien: {{ $loy->contrat->bien->nom ?? 'Unité' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-[#274256]">
                        {{ number_format($loy->montant, 0, ',', ' ') }} F
                        @if($loy->statut === 'partiellement_payé')
                            <div class="text-[11px] text-red-500 font-bold mt-1">
                                Reste: {{ number_format($loy->montant - $loy->montant_paye_cache, 0, ',', ' ') }} F
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
                        @if($loy->statut === 'annulé' && $loy->note_annulation)
                            <div class="text-[11px] text-gray-400 mt-1 italic max-w-[120px] truncate mx-auto" title="{{ $loy->note_annulation }}">
                                "{{ $loy->note_annulation }}"
                            </div>
                        @endif
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
                            <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $loy->id) }}', nom_original: 'Quittance_{{ str_replace([' '], ['_'], $loy->contrat->locataire->nom ?? 'doc') }}_{{ $loy->mois }}.pdf', type_label: 'Quittance de Loyer'})" class="p-2 text-gray-400 hover:text-[#274256] hover:bg-white rounded-lg transition" title="Télécharger Quittance">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                    @endforeach
                @endif

                <x-slot name="mobile">
                    @if(count($data['loyers_list']) > 0)
                        @foreach($data['loyers_list'] as $loy)
                            <x-data-card
                                title="{{ \Carbon\Carbon::parse($loy->mois)->translatedFormat('F Y') }}"
                                status="{{ str_replace('_', ' ', $loy->statut) }}"
                                statusColor="{{ $loy->statut === 'payé' ? 'green' : ($loy->statut === 'en_retard' ? 'red' : 'gray') }}"
                            >
                                <div class="flex flex-col gap-1 text-gray-600">
                                    <div class="font-bold text-gray-900">{{ $loy->contrat->locataire->nom ?? 'Inconnu' }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded w-fit">{{ $loy->contrat->bien->nom ?? 'Unité' }}</div>
                                    <div class="font-black text-[#274256] mt-1 text-lg flex justify-between items-center">
                                        <span>{{ number_format($loy->montant, 0, ',', ' ') }} F</span>
                                        @if($loy->statut === 'partiellement_payé')
                                            <span class="text-xs text-red-500 font-bold">Reste: {{ number_format($loy->montant - $loy->montant_paye_cache, 0, ',', ' ') }}</span>
                                        @endif
                                    </div>
                                    @if($loy->note_annulation)
                                        <div class="text-[11px] text-gray-400 mt-1 italic w-full truncate">
                                            "{{ $loy->note_annulation }}"
                                        </div>
                                    @endif
                                </div>

                                <x-slot name="actions">
                                    @if($loy->statut !== 'payé' && App\Helpers\PermissionHelper::can('paiements.create'))
                                        <button onclick="loySection.openPaymentModal({{ json_encode($loy) }})" class="bg-[#274256] text-white p-2 rounded-lg hover:bg-[#1a2e3d] shadow-sm transition" title="Encaisser">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        </button>
                                    @endif
                                    @if(App\Helpers\PermissionHelper::can('loyers.edit'))
                                    <button onclick="loySection.openEditModal({{ json_encode($loy) }})" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endif
                                    @if($loy->statut === 'payé')
                                    <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $loy->id) }}', nom_original: 'Quittance_{{ str_replace([' '], ['_'], $loy->contrat->locataire->nom ?? 'doc') }}_{{ $loy->mois }}.pdf', type_label: 'Quittance de Loyer'})" class="p-2 text-gray-400 hover:text-[#274256] rounded-lg transition" title="Quittance">
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
    <div id="loy-payment-modal" role="dialog" aria-modal="true" aria-labelledby="loy-payment-modal-title" onclick="if(event.target === this) loySection.closePaymentModal()" class="fixed inset-0 z-[100] hidden bg-slate-900/40 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-4">
        <div id="loy-payment-container" class="bg-white w-full sm:max-w-xl rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Header Compact -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <div>
                    <h3 id="loy-payment-modal-title" class="text-base font-bold text-[#274256]">Enregistrer un Paiement</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5 font-medium">Solder la quittance.</p>
                </div>
                <button onclick="loySection.closePaymentModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="loy-payment-form" action="{{ route('paiements.store') }}" method="POST" target="loy_post_target" class="p-6 space-y-4" enctype="multipart/form-data">
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
                    <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-green-500/10 focus-within:border-green-600 transition-all">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Montant Encaissé</label>
                        <input type="number" name="montant" id="loy-payment-montant-input" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 text-right font-mono" placeholder="0">
                    </div>
                    <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-green-500/10 focus-within:border-green-600 transition-all">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Date Paiement</label>
                        <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
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
                            <input type="radio" name="mode" value="wave/om" class="peer hidden">
                            <div class="p-2 border border-gray-200 rounded-xl text-center transition peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 bg-white hover:border-gray-300 flex flex-col items-center justify-center h-14">
                                <span class="text-lg">📱</span>
                                <span class="text-[8px] font-black uppercase tracking-tighter text-gray-400 group-hover:text-gray-600 peer-checked:!text-green-700">Mobile</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Preuve de Paiement (Upload) -->
                <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500/10 focus-within:border-blue-600 transition-all">
                     <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Justificatif (PDF/Image)</label>
                     <input type="file" name="preuve" class="block w-full text-[11px] text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="loySection.closePaymentModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                    <button type="submit" id="loy-payment-submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-black shadow-lg shadow-green-900/20 active:scale-95 transition-all text-[11px] uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Valider Encaissement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: MODIFIER LOYER (ULTRA COMPACT) -->
    <div id="loy-edit-modal" role="dialog" aria-modal="true" aria-labelledby="loy-edit-modal-title" class="fixed inset-0 z-[105] hidden bg-slate-900/40 backdrop-blur-md transition-all duration-300 flex items-center justify-center p-4">
        <div id="loy-edit-container" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <div>
                    <h3 id="loy-edit-modal-title" class="text-sm font-bold text-[#274256]">Ajustement Loyer</h3>
                    <p class="text-[11px] text-gray-500 font-medium">Modification manuelle.</p>
                </div>
                <button onclick="loySection.closeEditModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="loy-edit-form" class="p-6 space-y-4">
                <input type="hidden" name="id" id="loy-edit-id">
                <input type="hidden" name="mois" id="loy-edit-mois">

                <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500/10 focus-within:border-blue-600 transition-all">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Montant Loyer</label>
                    <input type="number" name="montant" id="loy-edit-montant" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0">
                </div>

                <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500/10 focus-within:border-blue-600 transition-all">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Statut Actuel</label>
                    <select name="statut" id="loy-edit-statut" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer uppercase" onchange="loySection.toggleEditFields(this.value)">
                        <option value="émis">📅 Émis (Impayé)</option>
                        <option value="payé">✅ Payé</option>
                        <option value="en_retard">⚠️ En Retard</option>
                        <option value="annulé">❌ Annulé</option>
                        <option value="partiellement_payé">⏳ Partiel</option>
                    </select>
                </div>

                <div id="loy-edit-note-group" class="hidden relative bg-red-50/50 rounded-xl border border-red-100 px-3 py-2 focus-within:ring-2 focus-within:ring-red-500/10 focus-within:border-red-600 transition-all">
                    <label class="block text-[11px] font-black text-red-400 uppercase tracking-widest mb-0.5">Motif d'annulation</label>
                    <textarea name="note_annulation" id="loy-edit-note" rows="2" class="block w-full bg-transparent border-none p-0 text-xs font-bold text-gray-900 focus:ring-0 resize-none" placeholder="Pourquoi annuler ce loyer ?"></textarea>
                </div>

                <div id="loy-edit-partial-group" class="hidden relative bg-orange-50/50 rounded-xl border border-orange-100 px-3 py-2 focus-within:ring-2 focus-within:ring-orange-500/10 focus-within:border-orange-600 transition-all">
                    <label class="block text-[11px] font-black text-orange-400 uppercase tracking-widest mb-0.5">Montant déjà encaissé</label>
                    <input type="number" name="montant_paye" id="loy-edit-montant-paye" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0" placeholder="Ex: 50000">
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="loySection.closeEditModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                    <button type="submit" id="loy-edit-submit" class="bg-[#274256] text-white px-6 py-2.5 rounded-xl font-black shadow-lg shadow-blue-900/20 active:scale-95 transition-all text-[11px] uppercase tracking-widest">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- IFRAME MASQUE POUR LES POST (Anti-Reload Pattern) -->
    <iframe name="loy_post_target" class="hidden"></iframe>
    <iframe id="loy_refresh_iframe" class="hidden"></iframe>
</div>

<script>
    window.loySection = {
        openPaymentModal: function(loyer) {
            const overlay = document.getElementById('loy-payment-modal');
            const container = document.getElementById('loy-payment-container');

            // Remplir les infos
            document.getElementById('loy-payment-loyer-id').value = loyer.id;
            document.getElementById('loy-payment-locataire').innerText = loyer.contrat.locataire.nom;
            document.getElementById('loy-payment-periode').innerText = 'Période: ' + new Date(loyer.mois + '-01').toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
            document.getElementById('loy-payment-montant').innerText = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(loyer.montant) + ' F';
            document.getElementById('loy-payment-montant-input').value = Math.floor(loyer.montant);

            overlay.classList.remove('hidden');
            setTimeout(() => { container.classList.remove('scale-95', 'opacity-0'); }, 10);
        },

        closePaymentModal: function() {
            const overlay = document.getElementById('loy-payment-modal');
            const container = document.getElementById('loy-payment-container');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { overlay.classList.add('hidden'); }, 300);
        },

        onStoreSuccess: function(msg) {
            showToast(msg, 'success');

            const btn = document.getElementById('loy-payment-submit');
            const genBtn = document.getElementById('loy-gen-btn');
            const genText = document.getElementById('loy-gen-text');

            if(genBtn && genText) {
                genText.innerHTML = '✅ Terminé !';
                genBtn.classList.replace('border-gray-100', 'border-green-500');
            }

            if(btn) {
                btn.innerHTML = '✅ Encaissé !';
                btn.classList.replace('bg-green-600', 'bg-green-700');
            }

            if(window.dashboard) {
                setTimeout(() => {
                    this.closePaymentModal();
                    window.dashboard.refresh();
                }, 1000);
            } else {
                window.location.reload();
            }
        },

        onStoreError: function(msg) {
            showToast(msg, 'error');
            const btn = document.getElementById('loy-payment-submit');
            if(btn) {
                btn.innerHTML = 'Confirmer Encaissement';
                btn.classList.replace('bg-green-700', 'bg-green-600');
                btn.disabled = false;
            }

            const genBtn = document.getElementById('loy-gen-btn');
            const genText = document.getElementById('loy-gen-text');
            if(genBtn && genText) {
                 genText.innerHTML = 'Générer Mois Actuel';
                 genBtn.disabled = false;
            }
        },

        openEditModal: function(loyer) {
            const overlay = document.getElementById('loy-edit-modal');
            const container = document.getElementById('loy-edit-container');
            document.getElementById('loy-edit-id').value = loyer.id;
            document.getElementById('loy-edit-mois').value = loyer.mois;
            document.getElementById('loy-edit-montant').value = Math.floor(loyer.montant);
            document.getElementById('loy-edit-statut').value = loyer.statut;
            document.getElementById('loy-edit-note').value = loyer.note_annulation || '';
            document.getElementById('loy-edit-montant-paye').value = loyer.montant_paye_cache || 0;

            this.toggleEditFields(loyer.statut);

            overlay.classList.remove('hidden');
            setTimeout(() => { container.classList.remove('scale-95', 'opacity-0'); }, 10);
        },

        toggleEditFields: function(status) {
            const noteGroup = document.getElementById('loy-edit-note-group');
            const partialGroup = document.getElementById('loy-edit-partial-group');

            if (status === 'annulé') {
                noteGroup.classList.remove('hidden');
            } else {
                noteGroup.classList.add('hidden');
            }

            if (status === 'partiellement_payé') {
                partialGroup.classList.remove('hidden');
            } else {
                partialGroup.classList.add('hidden');
            }
        },

        closeEditModal: function() {
            const overlay = document.getElementById('loy-edit-modal');
            const container = document.getElementById('loy-edit-container');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { overlay.classList.add('hidden'); }, 300);
        },

        triggerRefresh: function() {
            const refreshIframe = document.getElementById('loy_refresh_iframe');
            refreshIframe.src = '{{ route('dashboard') }}?t=' + new Date().getTime();
            refreshIframe.onload = () => {
                const iframeDoc = refreshIframe.contentDocument || refreshIframe.contentWindow.document;

                // 1. Refresh Section Tables & KPIs
                const newTable = iframeDoc.getElementById('loy-table-container');
                const newKpi = iframeDoc.getElementById('loy-kpi-container');
                if(newTable) document.getElementById('loy-table-container').innerHTML = newTable.innerHTML;
                if(newKpi) document.getElementById('loy-kpi-container').innerHTML = newKpi.innerHTML;

                // 2. Refresh Global Dashboard Wrappers (Direction / Comptable / Admin)
                // Now using robust ID selection 'dashboard-kpi-grid' added to all role views
                const newGlobalGrid = iframeDoc.getElementById('dashboard-kpi-grid');
                const oldGlobalGrid = document.getElementById('dashboard-kpi-grid');

                if (newGlobalGrid && oldGlobalGrid) {
                    oldGlobalGrid.innerHTML = newGlobalGrid.innerHTML;
                }
            };
        }
    };

    document.getElementById('loy-edit-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('loy-edit-submit');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = '...';

        const id = document.getElementById('loy-edit-id').value;
        const formData = {
            montant: document.getElementById('loy-edit-montant').value,
            statut: document.getElementById('loy-edit-statut').value,
            mois: document.getElementById('loy-edit-mois').value,
            note_annulation: document.getElementById('loy-edit-note').value,
            montant_paye: document.getElementById('loy-edit-montant-paye').value
        };

        try {
            const res = await fetch(`/loyers/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if(res.ok) {
                 showToast('Loyer mis à jour', 'success');
                 loySection.closeEditModal();
                 loySection.triggerRefresh();
            } else {
                 showToast('Erreur mise à jour', 'error');
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur serveur', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    document.getElementById('loy-payment-form')?.addEventListener('submit', function() {
        const btn = document.getElementById('loy-payment-submit');
        btn.innerHTML = 'Traitement...';
        btn.disabled = true;
    });

    document.getElementById('loy-gen-form')?.addEventListener('submit', function() {
        const btn = document.getElementById('loy-gen-btn');
        const txt = document.getElementById('loy-gen-text');
        if(btn && txt) {
            txt.innerHTML = 'Génération...';
            btn.disabled = true;
        }
    });
</script>
