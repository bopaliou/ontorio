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
            $encaisseToday = $data['paiements_list']->filter(function($p) {
                return \Carbon\Carbon::parse($p->date_paiement)->isToday();
            })->sum('montant');

            $thisMonth = \Carbon\Carbon::now()->format('Y-m');
            $encaisseMonth = $data['paiements_list']->filter(function($p) use ($thisMonth) {
                return \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth;
            })->sum('montant');

            $transactionsMonth = $data['paiements_list']->filter(function($p) use ($thisMonth) {
                return \Carbon\Carbon::parse($p->date_paiement)->format('Y-m') === $thisMonth;
            })->count();
        @endphp

        <!-- KPIs Uniformes -->
        <div id="pai-kpi-container" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @include('components.kpi-card', [
                'label' => 'Encaissé Aujourd\'hui',
                'value' => number_format($encaisseToday, 0, ',', ' '),
                'suffix' => 'F',
                'icon' => 'check',
                'color' => 'green'
            ])
            @include('components.kpi-card', [
                'label' => 'Global Mois Actuel',
                'value' => number_format($encaisseMonth, 0, ',', ' '),
                'suffix' => 'F',
                'icon' => 'money',
                'color' => 'gradient'
            ])
            @include('components.kpi-card', [
                'label' => 'Transactions (Mois)',
                'value' => $transactionsMonth,
                'icon' => 'chart',
                'color' => 'gray'
            ])
        </div>

        <!-- Table -->
        <!-- Table -->
        <div id="pai-table-container">
            <x-data-table :headers="[
                ['label' => 'Date & Référence', 'classes' => 'text-white'],
                ['label' => 'Locataire', 'classes' => 'text-white'],
                ['label' => 'Méthode', 'classes' => 'text-white text-center'],
                ['label' => 'Montant Encaissé', 'classes' => 'text-right text-white'],
                ['label' => 'Preuve', 'classes' => 'text-center text-white'],
                ['label' => 'Actions', 'classes' => 'text-right text-white']
            ]" emptyMessage="Aucun paiement enregistré pour le moment.">

                @forelse($data['paiements_list'] as $pai)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
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
                        <span class="font-extrabold text-green-600 text-lg">{{ number_format($pai->montant, 0, ',', ' ') }} F</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($pai->preuve)
                        <button onclick="window.previewDoc({url: '/storage/{{ $pai->preuve }}', nom_original: 'Preuve_{{ $pai->reference }}.{{ pathinfo($pai->preuve, PATHINFO_EXTENSION) }}', type_label: 'Preuve de Paiement'})"
                           class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all text-xs font-bold"
                           title="Voir la preuve jointe">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Preuve
                        </button>
                        @else
                        <span class="text-xs text-gray-300 italic">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                             {{-- WhatsApp Sharing --}}
                             @if($pai->loyer->contrat->locataire->telephone)
                             @php
                                $msgTemplate = "Bonjour ". ($pai->loyer->contrat->locataire->nom ?? 'Cher client') .", nous confirmons la réception de votre paiement de ". number_format($pai->montant, 0, ',', ' ') ." F pour le loyer de ". \Carbon\Carbon::parse($pai->loyer->mois)->translatedFormat('F Y') .". Merci de votre confiance. Ontario Group.";
                                $waUrl = "https://wa.me/". str_replace([' ', '+', '(', ')', '-'], '', $pai->loyer->contrat->locataire->telephone) . "?text=" . urlencode($msgTemplate);
                             @endphp
                             <a href="{{ $waUrl }}" target="_blank"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm"
                                title="Envoyer par WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-4.821 7.454c-1.679 0-3.325-.453-4.764-1.31l-.342-.204-3.548.93.947-3.461-.224-.357c-.943-1.503-1.441-3.243-1.441-5.028 0-5.18 4.215-9.395 9.395-9.395 2.51 0 4.87.978 6.645 2.753 1.775 1.775 2.753 4.136 2.753 6.642 0 5.181-4.215 9.395-9.395 9.395m8.404-17.801c-2.245-2.243-5.23-3.479-8.404-3.479-6.551 0-11.884 5.334-11.884 11.885 0 2.094.547 4.139 1.584 5.975l-1.683 6.148 6.291-1.65c1.764.962 3.75 1.47 5.772 1.47h.005c6.551 0 11.885-5.335 11.885-11.886 0-3.176-1.236-6.162-3.48-8.408z"/></svg>
                             </a>
                             @endif

                             <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $pai->loyer_id) }}', nom_original: 'Recu_{{ $pai->id }}.pdf', type_label: 'Reçu'})"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                title="Reçu PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </button>
                            @if(in_array(auth()->user()->role, ['admin', 'comptable']))
                            <button onclick="paiSection.confirmDelete({{ $pai->id }})"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] hover:text-white transition-all"
                               title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    <!-- Géré par le composant -->
                @endforelse

                <x-slot name="mobile">
                    @if(count($data['paiements_list']) > 0)
                        @foreach($data['paiements_list'] as $pai)
                            <x-data-card
                                title="{{ \Carbon\Carbon::parse($pai->date_paiement)->translatedFormat('d M Y') }}"
                                status="Payé"
                                statusColor="green"
                            >
                                <div class="flex flex-col gap-1 text-gray-600">
                                    <div class="font-bold text-gray-900 capitalize">{{ $pai->loyer->contrat->locataire->nom ?? 'Inconnu' }}</div>
                                    <div class="flex justify-between items-center mt-1">
                                        <div class="font-bold text-green-600">{{ number_format($pai->montant, 0, ',', ' ') }} F</div>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 uppercase">{{ $pai->mode ?? 'Espèces' }}</span>
                                    </div>
                                </div>

                                <x-slot name="actions">
                                    @if($pai->preuve)
                                        <button onclick="window.previewDoc({url: '/storage/{{ $pai->preuve }}', nom_original: 'Preuve_{{ $pai->id }}.{{ pathinfo($pai->preuve, PATHINFO_EXTENSION) }}', type_label: 'Preuve de Paiement'})" class="p-3 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100" title="Voir la preuve jointe">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        </button>
                                    @endif
                                    <button onclick="window.previewDoc({url: '{{ route('loyers.quittance', $pai->loyer_id) }}', nom_original: 'Recu_{{ $pai->id }}.pdf', type_label: 'Reçu'})" class="p-3 bg-gray-50 text-gray-500 rounded-lg hover:bg-gray-100" title="Reçu">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </button>
                                    @if(in_array(auth()->user()->role, ['admin', 'comptable']))
                                    <button onclick="paiSection.confirmDelete({{ $pai->id }})" class="p-3 bg-red-50 text-red-500 rounded-lg hover:text-red-600 hover:bg-red-100" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

    <!-- MODAL (ULTRA COMPACT GRID) -->
    <div id="pai-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="pai-modal-title" onclick="if(event.target === this) paiSection.closeModal()" class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-md transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div id="pai-modal-container" class="bg-white w-full h-full sm:h-auto sm:max-w-xl rounded-none sm:rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100">

            <!-- Header Compact -->
            <div class="bg-[#274256] px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 id="pai-modal-title" class="text-base font-bold text-white">Nouvel Encaissement</h3>
                    <p class="text-blue-100/60 text-[11px] uppercase font-black tracking-widest mt-0.5">Enregistrer un paiement</p>
                </div>
                <button onclick="paiSection.closeModal()" class="text-white/60 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition" aria-label="Fermer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="pai-main-form" action="{{ route('paiements.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                @csrf

                <!-- Sélection Loyer -->
                <div class="relative bg-gray-50 rounded-2xl border-2 border-gray-100 px-4 py-3 focus-within:ring-4 focus-within:ring-[#274256]/5 focus-within:border-[#274256] transition-all duration-300">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[2px] mb-1">Loyer à solder</label>
                    <select name="loyer_id" id="pai-select-loyer" required class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                        @php
                            $unpaidLoyers = $data['loyers_list']->filter(fn($l) => strtolower(trim($l->statut)) !== 'payé');
                        @endphp
                            $unpaidLoyers = $data['loyers_list']->filter(fn($l) => strtolower(trim($l->statut)) !== 'payé');
                        @endphp

                        @if($unpaidLoyers->count() > 0)
                            <option value="">-- Sélectionner un loyer --</option>
                            @foreach($unpaidLoyers as $l)
                                <option value="{{ $l->id }}" 
                                        data-montant="{{ $l->montant }}" 
                                        data-reste="{{ $l->reste_a_payer }}"
                                        data-locataire="{{ $l->contrat->locataire->nom }}" 
                                        data-mois="{{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }}">
                                    {{ $l->contrat->locataire->nom }} — {{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }} — {{ number_format($l->reste_a_payer,0,',',' ') }} F (Restant)
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled selected>Aucun loyer impayé trouvé</option>
                        @endif
                    </select>
                </div>

                <!-- Info Card Compact -->
                <div id="pai-locataire-card" class="hidden bg-blue-50/50 rounded-xl p-3 border border-blue-100 flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-[11px] font-black text-[#274256] uppercase tracking-widest opacity-60">Détails Locataire</p>
                        <p id="pai-card-locataire" class="text-xs font-bold text-gray-900 leading-tight">--</p>
                        <p id="pai-card-mois" class="text-[11px] text-gray-500 mt-0.5 font-medium">--</p>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <p class="text-[11px] font-black text-[#cb2d2d] uppercase tracking-widest opacity-60">Solde Restant</p>
                        <p id="pai-card-montant" class="text-sm font-black text-[#274256] leading-tight transition-all">0 F</p>
                        <div id="pai-live-balance" class="text-[10px] font-bold mt-1 text-emerald-600 hidden">
                             Vers le solde : <span id="pai-live-val">0</span> F
                        </div>
                    </div>
                </div>

                \u003c!-- Grid Montant / Date --\u003e\n                \u003cdiv class=\"grid grid-cols-1 md:grid-cols-2 gap-4\"\u003e\n                    \u003cdiv class=\"relative bg-gray-50 rounded-2xl border-2 border-gray-100 px-4 py-3 focus-within:ring-4 focus-within:ring-[#274256]/5 focus-within:border-[#274256] transition-all duration-300\"\u003e\n                        \u003clabel class=\"block text-[10px] font-black text-gray-400 uppercase tracking-[2px] mb-1\"\u003eMontant Encaiss\u00e9 (F)\u003c/label\u003e\n                        \u003cinput type=\"number\" name=\"montant\" id=\"pai-input-montant\" required class=\"block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 focus:ring-0 text-right font-mono\" placeholder=\"0\"\u003e\n                    \u003c/div\u003e\n                    \u003cdiv class=\"relative bg-gray-50 rounded-2xl border-2 border-gray-100 px-4 py-3 focus-within:ring-4 focus-within:ring-[#274256]/5 focus-within:border-[#274256] transition-all duration-300\"\u003e\n                        \u003clabel class=\"block text-[10px] font-black text-gray-400 uppercase tracking-[2px] mb-1\"\u003eDate Paiement\u003c/label\u003e\n                        \u003cinput type=\"date\" name=\"date_paiement\" value=\"{{ date('Y-m-d') }}\" required class=\"block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 focus:ring-0\"\u003e\n                    \u003c/div\u003e\n                \u003c/div\u003e\n
                <!-- Mode de Règlement Compact -->
                <div>
                     <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mode de Règlement</label>
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

                <!-- Preuve de Paiement (Upload) -->
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Preuve de Paiement (Optionnel)</label>
                    <div class="relative bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 hover:border-amber-400 transition-all cursor-pointer group" onclick="document.getElementById('pai-preuve-input').click()">
                        <input type="file" name="preuve" id="pai-preuve-input" accept="image/*,.pdf" class="hidden" onchange="paiSection.updatePreuvePreview(this)">
                        <div id="pai-preuve-placeholder" class="p-4 flex flex-col items-center justify-center text-center">
                            <svg class="w-8 h-8 text-gray-300 group-hover:text-amber-500 transition mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <p class="text-xs font-bold text-gray-500 group-hover:text-amber-600 transition">Cliquez pour joindre une preuve</p>
                            <p class="text-[10px] text-gray-400 mt-1">Image ou PDF (Max 5 Mo)</p>
                        </div>
                        <div id="pai-preuve-preview" class="hidden p-3 flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p id="pai-preuve-name" class="text-xs font-bold text-gray-900 truncate">fichier.pdf</p>
                                <p id="pai-preuve-size" class="text-[10px] text-gray-400">0 Ko</p>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); paiSection.clearPreuve();" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
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
    <div id="pai-delete-modal" role="dialog" aria-modal="true" aria-labelledby="pai-delete-modal-title" onclick="if(event.target === this) paiSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="pai-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
             <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 id="pai-delete-modal-title" class="text-xl font-bold text-gray-900 mb-2">Annuler ce paiement ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                Le montant sera retiré de la caisse et le loyer associé repassera en "Impayé" pour le locataire.
            </p>
            <div class="flex flex-col gap-3">
                <button onclick="paiSection.executeDelete()" id="pai-confirm-delete-btn" class="w-full px-6 py-3.5 bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Supprimer
                </button>
                <button onclick="paiSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>

    <!-- Refresh iframe remains for background updates if needed, but not for form POST -->
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
        }
    };

    // Feedback de chargement et soumission AJAX
    document.getElementById('pai-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('pai-submit-btn');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Envoi...';
        btn.disabled = true;

        const formData = new FormData(this);
        const url = this.getAttribute('action');

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

            if(response.ok) {
                btn.innerHTML = '✅ Encaissé !';
                showToast(data.message || 'Paiement enregistré avec succès', 'success');
                
                setTimeout(() => {
                    window.paiSection.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                }, 800);
            } else {
                btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Erreur';
                showToast(data.message || 'Erreur lors de l\'enregistrement', 'error');
                setTimeout(() => { btn.innerHTML = originalText; }, 3000);
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur de connexion au serveur', 'error');
            btn.innerHTML = originalText;
        } finally {
            // Re-enable button after 3s if there was an error, or immediately if we want to allow retry
            // If success, the page reloads/refreshes anyway.
            setTimeout(() => {
                if (btn.disabled) btn.disabled = false;
            }, 3000);
        }
    });

    // Mise à jour dynamique de la card locataire
    document.getElementById('pai-select-loyer').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const card = document.getElementById('pai-locataire-card');
        const inputMontant = document.getElementById('pai-input-montant');
        const liveBalance = document.getElementById('pai-live-balance');

        if (this.value) {
            const montantTotal = selected.dataset.montant;
            const reste = selected.dataset.reste;
            const locataire = selected.dataset.locataire;
            const mois = selected.dataset.mois;

            document.getElementById('pai-card-locataire').textContent = locataire;
            document.getElementById('pai-card-mois').textContent = 'Période: ' + mois;
            document.getElementById('pai-card-montant').textContent = new Intl.NumberFormat('fr-FR').format(reste) + ' F';
            
            // On pré-remplit avec le reste à payer
            inputMontant.value = reste;
            inputMontant.dataset.target = reste;
            
            card.classList.remove('hidden');
            card.classList.add('animate-fade-in');
            liveBalance.classList.add('hidden');
        } else {
            card.classList.add('hidden');
            inputMontant.value = '';
        }
    });

    // Calcul en temps réel du restant
    document.getElementById('pai-input-montant').addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        const target = parseFloat(this.dataset.target) || 0;
        const liveBalance = document.getElementById('pai-live-balance');
        const liveVal = document.getElementById('pai-live-val');
        const cardMontant = document.getElementById('pai-card-montant');

        if (target > 0) {
            const diff = target - val;
            liveBalance.classList.remove('hidden');
            
            if (diff > 0) {
                liveBalance.className = "text-[10px] font-bold mt-1 text-orange-600";
                liveBalance.innerHTML = `Reliquat : <span>${new Intl.NumberFormat('fr-FR').format(diff)}</span> F`;
                cardMontant.classList.add('text-orange-600');
                cardMontant.classList.remove('text-[#274256]', 'text-emerald-600');
            } else if (diff === 0) {
                liveBalance.className = "text-[10px] font-bold mt-1 text-emerald-600";
                liveBalance.innerHTML = "Solde complet ✅";
                cardMontant.classList.add('text-emerald-600');
                cardMontant.classList.remove('text-[#274256]', 'text-orange-600');
            } else {
                liveBalance.className = "text-[10px] font-bold mt-1 text-blue-600";
                liveBalance.innerHTML = `Trop-perçu : <span>${new Intl.NumberFormat('fr-FR').format(Math.abs(diff))}</span> F`;
                cardMontant.classList.add('text-blue-600');
                cardMontant.classList.remove('text-[#274256]', 'text-orange-600', 'text-emerald-600');
            }
        }
    });

    // Gestion de l'aperçu du fichier preuve
    window.paiSection.updatePreuvePreview = function(input) {
        const placeholder = document.getElementById('pai-preuve-placeholder');
        const preview = document.getElementById('pai-preuve-preview');
        const nameEl = document.getElementById('pai-preuve-name');
        const sizeEl = document.getElementById('pai-preuve-size');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            nameEl.textContent = file.name;
            sizeEl.textContent = (file.size / 1024).toFixed(1) + ' Ko';
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
        }
    };

    window.paiSection.clearPreuve = function() {
        const input = document.getElementById('pai-preuve-input');
        const placeholder = document.getElementById('pai-preuve-placeholder');
        const preview = document.getElementById('pai-preuve-preview');

        input.value = '';
        placeholder.classList.remove('hidden');
        preview.classList.add('hidden');
    };

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
                showToast('Paiement supprimé et loyer restauré', 'success');
                if(window.dashboard) window.dashboard.refresh();
                else window.location.reload();
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
