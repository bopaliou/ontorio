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
                        <span class="font-extrabold text-green-600 text-lg">{{ format_money($pai->montant) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($pai->preuve)
                        <button onclick="window.previewDoc({url: '{{ get_secure_url($pai->preuve) }}', nom_original: 'Preuve_{{ $pai->reference }}.{{ pathinfo($pai->preuve, PATHINFO_EXTENSION) }}', type_label: 'Preuve de Paiement'})"
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
                                        <div class="font-bold text-green-600">{{ format_money($pai->montant) }}</div>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 uppercase">{{ $pai->mode ?? 'Espèces' }}</span>
                                    </div>
                                </div>

                                <x-slot name="actions">
                                    @if($pai->preuve)
                                        <button onclick="window.previewDoc({url: '{{ get_secure_url($pai->preuve) }}', nom_original: 'Preuve_{{ $pai->id }}.{{ pathinfo($pai->preuve, PATHINFO_EXTENSION) }}', type_label: 'Preuve de Paiement'})" class="p-3 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100" title="Voir la preuve jointe">
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

    <!-- Refresh iframe remains for background updates if needed, but not for form POST -->
    <iframe id="pai_refresh_iframe" class="hidden"></iframe>

    <!-- MODAL: ENREGISTREMENT PAIEMENT (DESIGN PREMIUM) -->
    <div id="pai-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="pai-modal-title" role="dialog" aria-modal="true">
        <div id="pai-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) paiSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) paiSection.closeModal()">
                <div id="pai-modal-container" class="app-modal-panel app-modal-panel-xl bg-white rounded-3xl overflow-hidden shadow-2xl opacity-0 scale-95 transition-all duration-300">

                    <!-- Header Premium -->
                    <div class="relative bg-[#274256] px-8 py-8 overflow-hidden">
                        <!-- Décoration en arrière-plan -->
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-[#cb2d2d]/20 rounded-full blur-xl"></div>
                        
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#cb2d2d] to-[#ef4444] rounded-2xl flex items-center justify-center shadow-lg shadow-red-900/40 transform -rotate-6">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-10c-1.105 0-2 1.119-2 2.5s.895 2.5 2 2.5 2-1.119 2-2.5-.895-2.5-2-2.5zm0 10c-1.105 0-2-1.119-2 2.5s.895 2.5 2 2.5 2-1.119 2-2.5-.895-2.5-2-2.5zM12 5V3m0 18v-2"/></svg>
                                </div>
                                <div>
                                    <h3 id="pai-modal-title" class="text-xl font-black text-white tracking-tight">Nouvel Encaissement</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                        <p class="text-blue-100/60 text-[10px] uppercase font-black tracking-[0.2em]">Flux de Trésorerie Ontario</p>
                                    </div>
                                </div>
                            </div>
                            <button onclick="paiSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all group" aria-label="Fermer">
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <form id="pai-main-form" action="{{ route('paiements.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8 bg-white">
                        @csrf

                        <!-- Étape 1: Identification -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-black bg-[#274256] text-white px-2 py-0.5 rounded">01</span>
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Identification du Revenu</h4>
                            </div>
                            
                            <div class="relative group">
                                <label for="pai-select-loyer" class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-[#274256] uppercase tracking-widest z-10 transition-colors group-focus-within:text-[#cb2d2d]">Loyer Concerné</label>
                                <div class="relative">
                                    <select name="loyer_id" id="pai-select-loyer" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#cb2d2d]/5 focus:border-[#cb2d2d] transition-all appearance-none cursor-pointer outline-none">
                                        @php
                                            $unpaidLoyers = $data['loyers_list']->filter(fn($l) => strtolower(trim($l->statut)) !== 'payé');
                                        @endphp

                                        @if($unpaidLoyers->count() > 0)
                                            <option value="">-- Rechercher un locataire ou une quittance --</option>
                                            @foreach($unpaidLoyers as $l)
                                                <option value="{{ $l->id }}"
                                                        data-montant="{{ $l->montant }}"
                                                        data-reste="{{ $l->reste_a_payer }}"
                                                        data-locataire="{{ $l->contrat->locataire->nom }}"
                                                        data-mois="{{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }}">
                                                    {{ $l->contrat->locataire->nom }} — {{ \Carbon\Carbon::parse($l->mois)->translatedFormat('F Y') }} — {{ format_money($l->reste_a_payer) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>Aucune créance en attente</option>
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Card Premium (Dynamique) -->
                        <div id="pai-locataire-card" class="hidden transform translate-y-4 opacity-0 transition-all duration-500">
                            <div class="bg-gradient-to-br from-gray-50 to-blue-50/30 rounded-3xl p-6 border border-blue-100/50 relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4">
                                    <div class="bg-blue-600/10 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter">Fiche Débitrice</div>
                                </div>
                                <div class="flex items-start gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl border border-blue-50">👤</div>
                                    <div class="flex-1">
                                        <p id="pai-card-locataire" class="text-lg font-black text-gray-900 leading-tight capitalize">--</p>
                                        <div class="flex items-center gap-3 mt-1 text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span id="pai-card-mois" class="text-xs font-bold">--</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 flex items-baseline gap-2">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Solde à percevoir :</span>
                                            <span id="pai-card-montant" class="text-xl font-black text-[#cb2d2d]">0 F</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Étape 2: Transaction -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-black bg-[#274256] text-white px-2 py-0.5 rounded">02</span>
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Détails du Règlement</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="relative group">
                                    <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10 group-focus-within:text-[#cb2d2d]" for="pai-input-montant">Somme Reçue</label>
                                    <div class="relative">
                                        <input type="number" name="montant" id="pai-input-montant" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-lg font-black text-gray-900 focus:ring-4 focus:ring-[#cb2d2d]/5 focus:border-[#cb2d2d] transition-all text-right font-mono" placeholder="0">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">XOF</span>
                                    </div>
                                    <div id="pai-live-balance" class="mt-2 px-2 flex items-center gap-2 hidden transition-all duration-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        <span id="pai-live-text" class="text-[10px] font-black uppercase tracking-wider"></span>
                                    </div>
                                </div>

                                <div class="relative group">
                                    <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10 group-focus-within:text-[#cb2d2d]" for="pai-input-date">Date de Réception</label>
                                    <input type="date" name="date_paiement" id="pai-input-date" value="{{ date('Y-m-d') }}" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#cb2d2d]/5 focus:border-[#cb2d2d] transition-all">
                                </div>
                            </div>

                            <!-- Sélecteur de Mode Premium -->
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Canal de Paiement</label>
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach(['espèces' => ['💵', 'Espèces'], 'virement' => ['🏦', 'Virement'], 'mobile' => ['📱', 'Mobile']] as $val => $info)
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="mode" value="{{ $val }}" {{ $val == 'espèces' ? 'checked' : '' }} class="hidden peer">
                                        <div class="peer-checked:border-[#cb2d2d] peer-checked:bg-red-50/50 peer-checked:ring-4 peer-checked:ring-[#cb2d2d]/5 border-2 border-gray-100 rounded-2xl p-4 flex flex-col items-center justify-center transition-all bg-white hover:border-gray-200 hover:bg-gray-50 h-20">
                                            <span class="text-2xl mb-1 transform group-hover:scale-110 transition-transform">{{ $info[0] }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-500 peer-checked:text-[#cb2d2d]">{{ $info[1] }}</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-[#cb2d2d] text-white rounded-full flex items-center justify-center scale-0 peer-checked:scale-100 transition-transform duration-300">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Étape 3: Justificatif -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-black bg-[#274256] text-white px-2 py-0.5 rounded">03</span>
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Pièce Justificative</h4>
                            </div>

                            <div class="relative bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 hover:border-[#cb2d2d] hover:bg-red-50/20 transition-all cursor-pointer group" onclick="document.getElementById('pai-preuve-input').click()">
                                <input type="file" name="preuve" id="pai-preuve-input" accept="image/*,.pdf" class="hidden" onchange="paiSection.updatePreuvePreview(this)">
                                
                                <div id="pai-preuve-placeholder" class="p-8 flex flex-col items-center justify-center text-center">
                                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:rotate-12 transition-transform">
                                        <svg class="w-6 h-6 text-gray-300 group-hover:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    </div>
                                    <p class="text-xs font-black text-gray-600 uppercase tracking-tighter">Attacher une preuve de paiement</p>
                                    <p class="text-[10px] text-gray-400 mt-1 font-bold">PDF, JPEG ou PNG • Max 5Mo</p>
                                </div>

                                <div id="pai-preuve-preview" class="hidden p-5 flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="pai-preuve-name" class="text-xs font-black text-gray-900 truncate">nom_du_fichier.pdf</p>
                                        <p id="pai-preuve-size" class="text-[10px] text-gray-400 font-bold uppercase">0 Ko</p>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); paiSection.clearPreuve();" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-100 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions Premium -->
                        <div class="pt-8 flex items-center justify-end gap-4 border-t border-gray-100">
                            <button type="button" onclick="paiSection.closeModal()" class="px-6 py-3 text-gray-400 font-black hover:text-gray-900 transition-colors text-[10px] uppercase tracking-[0.2em]">Annuler</button>
                            <button type="submit" id="pai-submit-btn" class="relative overflow-hidden bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-red-900/20 hover:shadow-red-900/30 transition-all hover:-translate-y-1 text-[11px] uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="relative z-10 flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Valider l'Encaissement
                                </span>
                                <div class="absolute inset-0 bg-white/20 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE CONFIRMATION -->
    <div id="pai-delete-modal" role="dialog" style="z-index: 10000;" aria-modal="true" aria-labelledby="pai-delete-modal-title" onclick="if(event.target === this) paiSection.closeDeleteModal()" class="fixed inset-0 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
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
</div>


<script>
    window.paiSection = {
        openModal: function(mode) {
            const wrapper = document.getElementById('pai-modal-wrapper');
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');

            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            
            setTimeout(() => { 
                overlay.classList.remove('opacity-0');
                container.classList.remove('scale-95', 'opacity-0'); 
            }, 10);
        },

        closeModal: function() {
            const wrapper = document.getElementById('pai-modal-wrapper');
            const overlay = document.getElementById('pai-modal-overlay');
            const container = document.getElementById('pai-modal-container');

            overlay.classList.add('opacity-0');
            container.classList.add('scale-95', 'opacity-0');
            
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { 
                wrapper.classList.add('hidden'); 
                // Reset card on close
                document.getElementById('pai-locataire-card').classList.add('hidden', 'opacity-0', 'translate-y-4');
            }, 300);
        }
    };

    // Feedback de chargement et soumission AJAX
    document.getElementById('pai-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('pai-submit-btn');
        const originalText = btn.innerHTML;

        btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...';
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
                btn.innerHTML = '✅ Transaction Réussie';
                showToast(data.message || 'Paiement enregistré avec succès', 'success');

                setTimeout(() => {
                    window.paiSection.closeModal();
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                }, 1000);
            } else {
                btn.innerHTML = '⚠️ Erreur';
                showToast(data.message || 'Erreur lors de l\'enregistrement', 'error');
                setTimeout(() => { 
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur de connexion au serveur', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // Mise à jour dynamique de la card locataire
    document.getElementById('pai-select-loyer').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const card = document.getElementById('pai-locataire-card');
        const inputMontant = document.getElementById('pai-input-montant');
        const liveBalance = document.getElementById('pai-live-balance');

        if (this.value) {
            const reste = selected.dataset.reste;
            const locataire = selected.dataset.locataire;
            const mois = selected.dataset.mois;

            document.getElementById('pai-card-locataire').textContent = locataire;
            document.getElementById('pai-card-mois').textContent = 'Période : ' + mois;
            document.getElementById('pai-card-montant').textContent = new Intl.NumberFormat('fr-FR').format(reste) + ' F';

            inputMontant.value = reste;
            inputMontant.dataset.target = reste;

            card.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('opacity-0', 'translate-y-4');
            }, 50);
            
            // Trigger input event to calculate balance immediately
            inputMontant.dispatchEvent(new Event('input'));
        } else {
            card.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => card.classList.add('hidden'), 500);
            inputMontant.value = '';
            liveBalance.classList.add('hidden');
        }
    });

    // Calcul en temps réel du restant (Premium UI)
    document.getElementById('pai-input-montant').addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        const target = parseFloat(this.dataset.target) || 0;
        const liveBalance = document.getElementById('pai-live-balance');
        const liveText = document.getElementById('pai-live-text');
        const cardMontant = document.getElementById('pai-card-montant');

        if (target > 0) {
            const diff = target - val;
            liveBalance.classList.remove('hidden');

            if (diff > 0) {
                liveBalance.className = "mt-2 px-2 flex items-center gap-2 text-amber-600 animate-pulse";
                liveText.textContent = `Reliquat à percevoir : ${new Intl.NumberFormat('fr-FR').format(diff)} F`;
                cardMontant.className = "text-xl font-black text-amber-600 transition-colors duration-300";
            } else if (diff === 0) {
                liveBalance.className = "mt-2 px-2 flex items-center gap-2 text-emerald-600";
                liveText.textContent = "Solde complet de la quittance ✅";
                cardMontant.className = "text-xl font-black text-emerald-600 transition-colors duration-300";
            } else {
                liveBalance.className = "mt-2 px-2 flex items-center gap-2 text-blue-600";
                liveText.textContent = `Trop-perçu (Crédit) : ${new Intl.NumberFormat('fr-FR').format(Math.abs(diff))} F`;
                cardMontant.className = "text-xl font-black text-blue-600 transition-colors duration-300";
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
