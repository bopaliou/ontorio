<div class="h-full flex flex-col gap-10" id="proprietaires-section-container">

    @php
        $agence = $data['agency'] ?? $data['proprietaires_list']->first();
    @endphp

    <div id="prop-view-list" class="prop-sub-view space-y-10">
        <!-- Header Section -->
        @include('components.section-header', [
            'title' => 'Gestion des Propriétaires',
            'subtitle' => 'Gérez les entités propriétaires des biens (Ontario Group et propriétaires tiers).',
            'icon' => 'building',
            'actions' => '<button onclick="propSection.openModal(\'create\')" class="group flex items-center gap-2 px-5 py-2.5 bg-[#cb2d2d] rounded-2xl text-sm font-bold text-white hover:bg-[#a82020] shadow-lg shadow-red-900/20 transition-all transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouveau Propriétaire
            </button>'
        ])

        @if($agence)
        <!-- Main Identity Card -->
        <div class="relative group mt-6">
            <div class="absolute -inset-1 bg-gradient-to-r from-[#cb2d2d] to-[#274256] rounded-[3.5rem] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
            <div class="relative bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden z-10">
                <div class="flex flex-col lg:flex-row text-left">
                    <div class="lg:w-1/4 bg-[#1a2e3d] p-10 flex flex-col items-center justify-center relative overflow-hidden text-center">
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-white opacity-5 rounded-full"></div>
                        <div class="relative w-32 h-32 bg-white rounded-3xl p-5 shadow-2xl flex items-center justify-center transform group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="mt-6"><span class="px-4 py-1.5 bg-[#cb2d2d] text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg">Officiel</span></div>
                    </div>
                    <div class="flex-1 p-8 lg:p-12">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h3 class="text-3xl font-black text-[#1a2e3d] tracking-tight">{{ $agence->nom }}</h3>
                                        <svg class="w-6 h-6 text-blue-500 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    </div>
                                    <p class="text-[#cb2d2d] font-bold uppercase tracking-[0.2em] text-[11px]">{{ $agence->prenom ?? 'Propriétaire Principal' }}</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
                                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Téléphone</p><p class="text-sm font-bold text-gray-700">{{ $agence->telephone }}</p></div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</p><p class="text-sm font-bold text-gray-700">{{ $agence->email }}</p></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row lg:flex-col gap-4">
                                <button onclick="propSection.openModal('edit', {{ json_encode($agence) }})" class="px-6 py-4 bg-gray-50 text-[#1a2e3d] font-bold text-xs uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all">Modifier</button>
                                <a href="{{ route('proprietaires.bilan', $agence->id) }}" target="_blank" class="px-8 py-4 bg-[#1a2e3d] text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl transition-all">Générer Bilan PDF</a>
                            </div>
                        </div>

                        <!-- KPIs Section -->
                        @php
                            $stats = $data['owner_stats'][$agence->id] ?? null;
                        @endphp
                        @if($stats)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-8 border-t border-gray-100">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Occupation</p>
                                <div class="flex items-end gap-2">
                                    <span class="text-xl font-black text-[#1a2e3d]">{{ $stats['occupancy_rate'] }}%</span>
                                    <span class="text-[10px] font-bold text-gray-400 mb-1">({{ $stats['occupied_units'] }}/{{ $stats['total_units'] }})</span>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Revenu (Mois)</p>
                                <p class="text-xl font-black text-green-600">{{ number_format($stats['revenue_this_month'], 0, ',', ' ') }} <span class="text-[10px] text-gray-400">F</span></p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Arriérés</p>
                                <p class="text-xl font-black text-red-600">{{ number_format($stats['total_arrears'], 0, ',', ' ') }} <span class="text-[10px] text-gray-400">F</span></p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Logements</p>
                                <p class="text-xl font-black text-[#1a2e3d]">{{ $stats['total_units'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($data['proprietaires_list']->count() > 1)
        <!-- Secondary Owners -->
        <div class="space-y-6">
            <h4 class="text-sm font-black text-[#1a2e3d] uppercase tracking-[0.2em] flex items-center gap-3">
                <span class="w-8 h-px bg-gray-200"></span>
                Propriétaires Partenaires
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                @foreach($data['proprietaires_list']->where('id', '!=', $agence?->id) as $prop)
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 hover:border-[#cb2d2d]/20 transition-all group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-[#1a2e3d] font-black text-lg">
                                {{ substr($prop->nom, 0, 1) }}
                            </div>
                            <div class="flex gap-2">
                                <button onclick="propSection.openModal('edit', {{ json_encode($prop) }})" class="p-2 text-gray-400 hover:text-[#cb2d2d] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            </div>
                        </div>
                        <h5 class="text-lg font-black text-[#1a2e3d] mb-1">{{ $prop->nom }}</h5>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">{{ $prop->prenom ?? 'Propriétaire' }}</p>
                        
                        @php $s = $data['owner_stats'][$prop->id] ?? null; @endphp
                        @if($s)
                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-50">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.1em]">Occupation</p>
                                <p class="text-sm font-black text-[#1a2e3d]">{{ $s['occupancy_rate'] }}%</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.1em]">Revenu</p>
                                <p class="text-sm font-black text-green-600">{{ number_format($s['revenue_this_month'], 0, ',', ' ') }} F</p>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>

    @push('modals')
    <!-- MODAL: CREATE/EDIT (ROUGE ONTARIO) -->
    <div id="prop-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="prop-modal-title" role="dialog" aria-modal="true">
        <div id="prop-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) propSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="prop-modal-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center border-b border-white/10">
                        <h3 id="prop-modal-title" class="text-lg font-black text-white">Gestion Propriétaire</h3>
                        <button onclick="propSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 text-white/70 hover:bg-white/20 hover:text-white transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="prop-main-form" onsubmit="propSection.submitForm(event)" method="POST" class="p-8 space-y-5 text-left">
                        @csrf
                        <input type="hidden" name="id" id="prop-input-id">
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="prop-input-nom">Nom entité / Société</label>
                                <input type="text" name="nom" id="prop-input-nom" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="prop-input-prenom">Prénom / Type (ex: S.A.)</label>
                                <input type="text" name="prenom" id="prop-input-prenom" class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="prop-input-email">Email</label>
                                <input type="email" name="email" id="prop-input-email" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="prop-input-telephone">Téléphone</label>
                                <input type="text" name="telephone" id="prop-input-telephone" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="prop-input-adresse">Adresse</label>
                                <textarea name="adresse" id="prop-input-adresse" rows="2" class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all resize-none"></textarea>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="prop-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Enregistrer</button>
                            <button type="button" onclick="propSection.closeModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.propSection = {
        openModal: function(mode, prop = null) {
            const wrapper = document.getElementById('prop-modal-wrapper');
            const overlay = document.getElementById('prop-modal-overlay');
            const container = document.getElementById('prop-modal-container');
            const form = document.getElementById('prop-main-form');
            const title = document.getElementById('prop-modal-title');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset();
            document.getElementById('prop-input-id').value = '';
            if(mode === 'edit' && prop) {
                title.innerText = 'Modifier Propriétaire';
                document.getElementById('prop-input-id').value = prop.id;
                document.getElementById('prop-input-nom').value = prop.nom;
                document.getElementById('prop-input-prenom').value = prop.prenom || '';
                document.getElementById('prop-input-email').value = prop.email;
                document.getElementById('prop-input-telephone').value = prop.telephone;
                document.getElementById('prop-input-adresse').value = prop.adresse;
            } else { title.innerText = 'Nouveau Propriétaire'; }
        },
        closeModal: function() {
            const wrapper = document.getElementById('prop-modal-wrapper');
            const overlay = document.getElementById('prop-modal-overlay');
            const container = document.getElementById('prop-modal-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        submitForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('prop-submit-btn');
            if (!btn || btn.disabled) return;
            const orig = btn.innerHTML; btn.innerHTML = 'Patientez...'; btn.disabled = true;
            const id = document.getElementById('prop-input-id').value;
            const url = id ? `/dashboard/proprietaires/${id}` : '/dashboard/proprietaires';
            const formData = new FormData(e.target);
            if (id) formData.append('_method', 'PUT');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const d = await res.json();
                if(res.ok) { 
                    showToast('Propriétaire enregistré', 'success'); 
                    this.closeModal(); 
                    if(window.dashboard) window.dashboard.refresh(); 
                    else window.location.reload(); 
                } else { 
                    showToast(d.message || 'Erreur lors de l\'enregistrement', 'error'); 
                    btn.innerHTML = orig; btn.disabled = false; 
                }
            } catch(e) { 
                showToast('Erreur de connexion au serveur', 'error'); 
                btn.innerHTML = orig; btn.disabled = false; 
            }
        }
    };
</script>
