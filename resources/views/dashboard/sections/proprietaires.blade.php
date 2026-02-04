<div class="h-full flex flex-col gap-8" id="proprietaires-section-container">

    @php
        $agence = $data['proprietaires_list']->first();
    @endphp

    <div id="prop-view-list" class="prop-sub-view space-y-8">
        <!-- Header Section -->
        <div class="flex items-end justify-between border-b border-gray-100 pb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Mon Agence</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Administration et coordonnées de l'entité légale.</p>
            </div>
            @if($agence)
            <button onclick="propSection.openModal('edit', {{ json_encode($agence) }})" class="group flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:border-gray-300 hover:shadow-md transition-all">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Modifier les infos
            </button>
            @endif
        </div>

        @if($agence)
        <!-- Main Identity Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 ontario-card-lift overflow-hidden">
            <div class="p-8 lg:p-12 grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- Col 1: Logo & Identity (4 cols) -->
                <div class="lg:col-span-4 flex flex-col items-center lg:items-start text-center lg:text-left border-b lg:border-b-0 lg:border-r border-gray-100 pb-10 lg:pb-0 lg:pr-10">
                    <div class="w-40 h-40 bg-white rounded-3xl flex items-center justify-center p-6 mb-8 border border-gray-100 shadow-sm relative group">
                        <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Logo" class="w-full h-full object-contain filter group-hover:scale-105 transition duration-500">
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 leading-tight">{{ $agence->nom }}</h3>
                    @if($agence->prenom)
                    <span class="inline-block mt-2 px-3 py-1 bg-gray-50 border border-gray-100 rounded-full text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $agence->prenom }}</span>
                    @endif

                    <div class="mt-6 flex items-start gap-3 text-left">
                        <svg class="w-5 h-5 text-gray-300 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm text-gray-500 leading-relaxed font-medium">
                            {{ $agence->adresse ?? 'Adresse du siège social non renseignée.' }}
                        </p>
                    </div>
                </div>

                <!-- Col 2: Details & Contacts (5 cols) -->
                <div class="lg:col-span-5 flex flex-col justify-center space-y-10">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-100 pb-2 inline-block">Coordonnées Officielles</h4>
                        <div class="space-y-8">
                            <div class="flex items-center gap-5 group">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center group-hover:bg-[#cb2d2d] group-hover:text-white transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Téléphone</p>
                                    <p class="text-lg font-bold text-gray-900 font-mono tracking-tight">{{ $agence->telephone ?? '--' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-5 group">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center group-hover:bg-[#cb2d2d] group-hover:text-white transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Email</p>
                                    <p class="text-lg font-bold text-gray-900 tracking-tight">{{ $agence->email ?? '--' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col 3: Quick Stats (3 cols) -->
                <div class="lg:col-span-3 bg-gradient-to-br from-gray-50 to-white rounded-3xl p-8 flex flex-col justify-center gap-8 border border-gray-100">
                    <div>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Patrimoine</p>
                        <div class="flex items-baseline gap-2">
                             <p class="text-4xl font-black text-gray-900">{{ $agence->logements_count ?? 0 }}</p>
                             <span class="text-sm font-medium text-gray-500">Biens</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-gray-200 dashed"></div>

                    <div>
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Revenus (Mois)</p>
                         <p class="text-3xl font-black text-[#cb2d2d]">{{ number_format($agence->loyers_encaisses_mois ?? 0, 0, ',', ' ') }} <span class="text-sm text-gray-400 font-medium">F CFA</span></p>
                    </div>

                    <div class="mt-auto">
                         <div class="flex items-center gap-2 text-xs font-bold text-green-700 bg-green-50 border border-green-100 px-4 py-2 rounded-full w-max">
                             <span class="relative flex h-2 w-2">
                               <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                               <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                             </span>
                             Compte Vérifié
                         </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- SECTION: BILAN FINANCIER GLOBAL -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Totaux Recettes -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mb-6 shadow-sm border border-green-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Recettes Totales</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($agence->total_encaisse_global ?? 0, 0, ',', ' ') }} <span class="text-sm font-medium text-gray-400">F</span></p>
                    <p class="text-[11px] text-green-600 font-bold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        Flux entrant constant
                    </p>
                </div>
            </div>

            <!-- Totaux Dépenses -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#cb2d2d] flex items-center justify-center mb-6 shadow-sm border border-red-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Dépenses Cumulées</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($agence->total_depenses ?? 0, 0, ',', ' ') }} <span class="text-sm font-medium text-gray-400">F</span></p>
                    <p class="text-[11px] text-gray-500 font-bold mt-2">Maintenance & Opérations</p>
                </div>
            </div>

            <!-- Bénéfice Net -->
            <div class="bg-[#1A365D] rounded-3xl p-8 shadow-xl relative overflow-hidden group">
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-24 -mb-24 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-6 border border-white/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <p class="text-xs font-black text-blue-200 uppercase tracking-widest mb-1">Solde Net (Bénéfice)</p>
                    @php
                        $bilanNet = ($agence->total_encaisse_global ?? 0) - ($agence->total_depenses ?? 0);
                    @endphp
                    <p class="text-3xl font-black text-white">{{ number_format($bilanNet, 0, ',', ' ') }} <span class="text-sm font-medium text-blue-300">F CFA</span></p>
                    <div class="mt-4 flex items-center justify-between">
                         <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-500/20 rounded-full">
                              <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                              <span class="text-[11px] font-black text-green-300 uppercase tracking-widest">Rentabilité Positive</span>
                         </div>
                         <a href="{{ route('proprietaires.bilan', $agence->id) }}" target="_blank" class="px-4 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-lg text-[11px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Exporter Bilan
                         </a>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Empty State (Modern) -->
        <div class="max-w-xl mx-auto mt-24 text-center">
            <div class="w-24 h-24 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 transform rotate-3 shadow-lg shadow-red-100">
                 <svg class="w-12 h-12 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Bienvenue sur Ontario Dashboard</h3>
            <p class="text-gray-500 mb-10 leading-relaxed text-lg">Pour commencer, veuillez configurer l'identité de votre agence. Ces informations apparaîtront sur tous vos documents officiels.</p>
            <button onclick="propSection.openModal('create')" class="bg-[#cb2d2d] text-white px-10 py-4 rounded-2xl font-bold hover:bg-[#b02222] transition-all shadow-xl shadow-red-900/20 transform hover:-translate-y-1">
                Configurer l'Agence
            </button>
        </div>
        @endif
    </div>

    <!-- MODAL: CONFIGURATION AGENCE (ULTRA COMPACT GRID) -->
    <div id="prop-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="prop-modal-title" role="dialog" aria-modal="true">
        <div id="prop-modal-overlay" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) propSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) propSection.closeModal()">
                <div id="prop-modal-container" class="relative transform overflow-hidden bg-white text-left shadow-2xl transition-all w-full h-full sm:h-auto sm:w-full sm:max-w-xl sm:my-8 rounded-none sm:rounded-2xl opacity-0 scale-95 duration-300 border border-gray-100">

                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="prop-modal-title" class="text-base font-bold text-gray-900">Configuration Agence</h3>
                            <p class="text-[11px] text-gray-500 font-medium">Coordonnées légales de l'entité.</p>
                        </div>
                        <button onclick="propSection.closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="prop-main-form" class="p-6 space-y-4">
                        <input type="hidden" name="id" id="prop-input-id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nom de l'Entité</label>
                                <input type="text" name="nom" id="prop-input-nom" required class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: Ontario Group">
                            </div>

                            <!-- Suffixe -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Suffixe Juridique</label>
                                <input type="text" name="prenom" id="prop-input-prenom" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="S.A.R.L / S.A">
                            </div>

                            <!-- Téléphone -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Téléphone</label>
                                <input type="text" name="telephone" id="prop-input-telephone" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: 33 822 ...">
                            </div>

                            <!-- Email -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Email Officiel</label>
                                <input type="email" name="email" id="prop-input-email" required class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="contact@ontariogroup.net">
                            </div>

                            <!-- Adresse (Full width in grid) -->
                            <div class="md:col-span-2 relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Adresse Siège Social</label>
                                <textarea name="adresse" id="prop-input-adresse" rows="1" class="block w-full bg-transparent border-none p-0 text-base sm:text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0 resize-none" placeholder="Adresse complète..."></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="propSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="prop-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.propSection = {
        openModal: function(mode, prop = null) {
            const wrapper = document.getElementById('prop-modal-wrapper');
            const overlay = document.getElementById('prop-modal-overlay');
            const container = document.getElementById('prop-modal-container');
            const form = document.getElementById('prop-main-form');
            const title = document.getElementById('prop-modal-title');

            wrapper.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);

            form.reset();
            document.getElementById('prop-input-id').value = '';

            if(mode === 'edit' && prop) {
                title.classList.remove('text-gray-900');
                title.innerText = 'Modifier Agence';
                title.classList.add('text-gray-900');

                document.getElementById('prop-input-id').value = prop.id;
                document.getElementById('prop-input-nom').value = prop.nom;
                document.getElementById('prop-input-prenom').value = prop.prenom || '';
                document.getElementById('prop-input-email').value = prop.email;
                document.getElementById('prop-input-telephone').value = prop.telephone;
                document.getElementById('prop-input-adresse').value = prop.adresse;
            } else {
                title.innerText = 'Configurer Agence';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('prop-modal-wrapper');
            const overlay = document.getElementById('prop-modal-overlay');
            const container = document.getElementById('prop-modal-container');

            overlay.classList.add('opacity-0');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        }
    };

    document.getElementById('prop-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('prop-submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sauvegarde...';
        btn.disabled = true;

        const formData = new FormData(this);
        const id = document.getElementById('prop-input-id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/dashboard/proprietaires/${id}` : `{{ route('dashboard.proprietaires.store') }}`;

        const jsonData = Object.fromEntries(formData.entries());

        try {
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

            if(response.ok) {
                showToast(data.message || 'Informations mises à jour', 'success');
                propSection.closeModal();
                window.location.reload();
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
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
