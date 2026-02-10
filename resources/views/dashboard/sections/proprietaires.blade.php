<div class="h-full flex flex-col gap-10" id="proprietaires-section-container">

    @php
        $agence = $data['proprietaires_list']->first();
    @endphp

    <div id="prop-view-list" class="prop-sub-view space-y-10">
        <!-- Header Section -->
        @include('components.section-header', [
            'title' => 'Identité de l\'Agence',
            'subtitle' => 'Paramétrez l\'entité légale qui apparaîtra sur vos documents.',
            'icon' => 'building',
            'actions' => $agence ? '<button onclick="propSection.openModal(\'edit\', '.json_encode($agence).')" class="group flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-700 hover:border-gray-300 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Modifier les Infos
            </button>' : ''
        ])

        @if($agence)
        <!-- Main Identity Card (Refined Premium Style) -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 ontario-card-lift overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gray-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50 pointer-events-none"></div>

            <div class="p-8 lg:p-14 grid grid-cols-1 lg:grid-cols-12 gap-12 relative z-10">

                <!-- Col 1: Brand & Identity (5 cols) -->
                <div class="lg:col-span-5 flex flex-col items-center lg:items-start text-center lg:text-left border-b lg:border-b-0 lg:border-r border-gray-100 pb-10 lg:pb-0 lg:pr-12">
                     {{-- Logo Container with Glow --}}
                    <div class="relative group">
                        <div class="absolute inset-0 bg-blue-100 rounded-[2rem] blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                        <div class="w-48 h-48 bg-white rounded-[2rem] flex items-center justify-center p-8 mb-10 border border-gray-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.08)] relative z-10 hover:scale-[1.02] transition-transform duration-500">
                            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Logo" class="w-full h-full object-contain filter">
                        </div>
                    </div>

                    <h3 class="text-4xl font-black text-gray-900 leading-none tracking-tighter">{{ $agence->nom }}</h3>
                    @if($agence->prenom)
                    <span class="inline-block mt-3 px-4 py-1.5 bg-gray-50 border border-gray-200 rounded-full text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $agence->prenom }}</span>
                    @endif

                    <div class="mt-8 flex items-start gap-4 text-left p-4 bg-gray-50 rounded-2xl border border-gray-100 w-full group hover:border-gray-200 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shrink-0 shadow-sm text-gray-400 group-hover:text-[#cb2d2d] transition-colors">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed font-medium pt-1">
                            {{ $agence->adresse ?? 'Adresse du siège social non renseignée.' }}
                        </p>
                    </div>
                </div>

                <!-- Col 2: Info & Stats (7 cols) -->
                <div class="lg:col-span-7 flex flex-col justify-center space-y-12">

                    {{-- Contact Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="flex items-center gap-5 group p-4 rounded-2xl hover:bg-gray-50 transition-colors cursor-default">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 flex items-center justify-center group-hover:border-[#cb2d2d] group-hover:text-[#cb2d2d] transition-all duration-300 shadow-sm text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Standard</p>
                                <p class="text-xl font-bold text-gray-900 font-mono tracking-tight">{{ $agence->telephone ?? '--' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-5 group p-4 rounded-2xl hover:bg-gray-50 transition-colors cursor-default">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 flex items-center justify-center group-hover:border-[#cb2d2d] group-hover:text-[#cb2d2d] transition-all duration-300 shadow-sm text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Email Officiel</p>
                                <p class="text-xl font-bold text-gray-900 tracking-tight">{{ $agence->email ?? '--' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Highlight Stat Box --}}
                    <div class="bg-gradient-to-br from-[#274256] to-[#1a2e3d] rounded-3xl p-10 flex flex-col md:flex-row items-center justify-between gap-8 border border-gray-800 shadow-2xl relative overflow-hidden group">
                        {{-- Deco --}}
                        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>

                         <div class="text-center md:text-left relative z-10">
                            <p class="text-[11px] font-bold text-blue-200 uppercase tracking-widest mb-2">Santé Financière (Mois)</p>
                            <div class="flex items-baseline gap-3 justify-center md:justify-start">
                                <p class="text-5xl font-black text-white tracking-tighter shadow-black/50 drop-shadow-sm">{{ number_format($agence->loyers_encaisses_mois ?? 0, 0, ',', ' ') }}</p>
                                <span class="text-lg font-bold text-blue-300">F CFA</span>
                            </div>
                        </div>

                        <div class="w-full md:w-auto h-px md:h-16 bg-white/10 relative z-10"></div>

                        <div class="text-center md:text-right relative z-10">
                             <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-full animate-pulse-subtle">
                                 <div class="w-2 h-2 rounded-full bg-green-400 shadow-[0_0_10px_rgba(74,222,128,0.5)]"></div>
                                 <span class="text-[10px] font-black text-green-300 uppercase tracking-widest">Activé & Vérifié</span>
                             </div>
                             <p class="text-[10px] text-white/40 mt-2 font-medium">Licence ID: #{{ str_pad($agence->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @else
        <!-- Empty State (Modern) -->
        <div class="max-w-xl mx-auto mt-24 text-center">
            <div class="w-32 h-32 bg-red-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-10 transform rotate-3 shadow-[0_20px_50px_-12px_rgba(203,45,45,0.2)]">
                 <svg class="w-16 h-16 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Configuration Requise</h3>
            <p class="text-gray-500 mb-12 leading-relaxed text-lg">Pour activer votre tableau de bord, veuillez configurer l'identité légale de votre agence.</p>
            <button onclick="propSection.openModal('create')" class="bg-[#cb2d2d] text-white px-10 py-5 rounded-2xl font-bold hover:bg-[#b02222] transition-all shadow-xl shadow-red-900/30 transform hover:-translate-y-1 text-sm uppercase tracking-widest">
                Configurer l'Agence
            </button>
        </div>
        @endif
    </div>

    <!-- MODAL: CONFIGURATION AGENCE (Refined) -->
    <div id="prop-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="prop-modal-title" role="dialog" aria-modal="true">
        <div id="prop-modal-overlay" class="fixed inset-0 bg-[#274256]/60 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) propSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) propSection.closeModal()">
                <div id="prop-modal-container" class="relative transform overflow-hidden bg-white text-left shadow-2xl transition-all w-full h-full sm:h-auto sm:w-full sm:max-w-2xl sm:my-8 rounded-none sm:rounded-3xl opacity-0 scale-95 duration-300 border border-gray-100">

                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <div>
                            <h3 id="prop-modal-title" class="text-xl font-black text-gray-900 tracking-tight">Identité Agence</h3>
                            <p class="text-xs text-gray-500 font-medium mt-1">Données légales et publiques.</p>
                        </div>
                        <button onclick="propSection.closeModal()" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 flex items-center justify-center transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="prop-main-form" class="p-8 space-y-6">
                        <input type="hidden" name="id" id="prop-input-id">

                         <div class="bg-blue-50 rounded-2xl p-4 flex items-start gap-4 mb-6">
                            <div class="shrink-0 p-2 bg-blue-100 rounded-lg text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs text-blue-800 leading-relaxed font-medium pt-1">
                                Ces informations (Nom, Adresse, Téléphone) seront automatiquement ajoutées en en-tête de vos quittances, contrats et relances.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="prop-input-nom">Nom de l'Entité</label>
                                <input type="text" name="nom" id="prop-input-nom" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="Ex: Ontario Group">
                            </div>

                            <!-- Suffixe -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="prop-input-prenom">Forme Juridique</label>
                                <input type="text" name="prenom" id="prop-input-prenom" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="S.A.R.L / S.A">
                            </div>

                            <!-- Téléphone -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="prop-input-telephone">Téléphone</label>
                                <input type="text" name="telephone" id="prop-input-telephone" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="Ex: 33 822 ...">
                            </div>

                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="prop-input-email">Email Officiel</label>
                                <input type="email" name="email" id="prop-input-email" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="contact@ontariogroup.net">
                            </div>

                            <!-- Adresse (Full width in grid) -->
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="prop-input-adresse">Adresse Siège Social</label>
                                <textarea name="adresse" id="prop-input-adresse" rows="2" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow resize-none" placeholder="Adresse complète..."></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-100">
                             <button type="button" onclick="propSection.closeModal()" class="px-6 py-3 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="prop-submit-btn" class="bg-[#cb2d2d] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
    // Keeping JS Logic intact
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
                title.innerText = 'Modifier Identité';
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
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Patientez...';
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
                showToast(data.message || 'Identité mise à jour', 'success');
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
