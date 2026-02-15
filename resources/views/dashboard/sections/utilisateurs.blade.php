@php
    $roleConfig = [
        'admin' => ['bg' => 'bg-red-50', 'text' => 'text-[#cb2d2d]', 'border' => 'border-red-100', 'label' => 'Administrateur'],
        'direction' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'label' => 'Direction'],
        'comptable' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'label' => 'Comptabilité'],
        'gestionnaire' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-100', 'label' => 'Gestionnaire'],
        'locataire' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100', 'label' => 'Locataire'],
        'proprietaire' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'label' => 'Propriétaire'],
    ];
    try { $dbRoles = \Spatie\Permission\Models\Role::all()->pluck('name')->toArray(); } catch (\Exception $e) { $dbRoles = []; }
    $availableRoles = !empty($dbRoles) ? $dbRoles : array_keys($roleConfig);
@endphp

<div class="h-full flex flex-col gap-8" id="utilisateurs-section-container">
    <div id="user-view-list" class="user-sub-view space-y-8">
        @include('components.section-header', [
            'title' => 'Équipe & Accès',
            'subtitle' => 'Gérez les membres de votre agence et leurs permissions.',
            'icon' => 'users',
            'actions' => '<button onclick="userSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajouter un Membre
            </button>'
        ])

        <div id="user-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 text-left">
            @forelse($data['users_list'] as $u)
                @php
                    $initials = strtoupper(substr($u->name, 0, 1) . (strpos($u->name, ' ') ? substr(explode(' ', $u->name)[1], 0, 1) : ''));
                    $role = $roleConfig[$u->role] ?? $roleConfig['gestionnaire'];
                @endphp
                <div class="group relative bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="absolute top-4 right-4 flex gap-2">
                        <button onclick="userSection.openModal('edit', {{ json_encode($u) }})" class="group flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm gap-1.5 text-[10px] font-bold uppercase tracking-wider" title="Modifier">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit</span>
                        </button>
                        @if($u->id !== auth()->id())
                        <button onclick="userSection.confirmDelete({{ json_encode($u) }})" class="group flex items-center px-2 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm gap-1.5 text-[10px] font-bold uppercase tracking-wider" title="Supprimer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Suppr</span>
                        </button>
                        @endif
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-2xl font-black mb-4 {{ $role['bg'] }} {{ $role['text'] }}">{{ $initials }}</div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $u->name }}</h3>
                        <span class="mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest border {{ $role['bg'] }} {{ $role['text'] }} {{ $role['border'] }}">{{ $role['label'] }}</span>
                        <div class="w-full h-px bg-gray-50 my-5"></div>
                        <p class="text-xs text-gray-400 truncate w-full">{{ $u->email }}</p>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>

    @push('modals')
    <!-- MODAL: CREATE/EDIT -->
    <div id="user-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="user-modal-title" role="dialog" aria-modal="true">
        <div id="user-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) userSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="user-modal-container" class="app-modal-panel max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="relative bg-[#cb2d2d] px-8 py-6 overflow-hidden text-left text-white flex justify-between items-center border-b border-white/10">
                        <div class="flex items-center gap-4">
                            <h3 id="user-modal-title" class="text-lg font-black text-white tracking-tight">Profil Membre</h3>
                        </div>
                        <button onclick="userSection.closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition-all text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form id="user-main-form" onsubmit="userSection.submitForm(event)" class="p-8 space-y-5 text-left">
                        <input type="hidden" name="id" id="user-input-id">
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="user-input-name">Nom Complet</label>
                                <input type="text" name="name" id="user-input-name" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="user-input-email">Email</label>
                                <input type="email" name="email" id="user-input-email" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all">
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="user-input-role">Rôle</label>
                                <select name="role" id="user-input-role" required class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all appearance-none">
                                    <option value="">-- Choisir --</option>
                                    @foreach($availableRoles as $r)<option value="{{ $r }}">{{ ucfirst($r) }}</option>@endforeach
                                </select>
                            </div>
                            <div class="relative group">
                                <label class="absolute -top-2 left-4 px-2 bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest z-10" for="user-input-password">Mot de passe</label>
                                <input type="password" name="password" id="user-input-password" class="block w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-gray-900 focus:border-[#cb2d2d] outline-none transition-all" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" id="user-submit-btn" class="w-full bg-[#cb2d2d] text-white py-4 rounded-2xl font-black hover:bg-[#b02222] transition shadow-xl text-xs uppercase tracking-widest">Enregistrer</button>
                            <button type="button" onclick="userSection.closeModal()" class="w-full py-3 text-gray-400 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE -->
    <div id="user-delete-modal-wrapper" class="app-modal-root hidden" style="z-index: 10000;" aria-labelledby="user-delete-modal-title" role="dialog" aria-modal="true">
        <div id="user-delete-modal-overlay" class="app-modal-overlay opacity-0" style="z-index: 10001;"></div>
        <div class="fixed inset-0 w-screen overflow-y-auto" style="z-index: 10002;" onclick="if(event.target === this) userSection.closeDeleteModal()">
            <div class="flex min-h-full items-center justify-center p-4">
                <div id="user-delete-container" class="app-modal-panel max-w-sm w-full bg-white rounded-3xl shadow-2xl overflow-hidden opacity-0 scale-95 transition-all duration-300">
                    <div class="px-8 py-6 bg-[#cb2d2d] text-white text-left flex justify-between items-center">
                        <h3 id="user-delete-modal-title" class="text-lg font-black text-white">Supprimer Membre ?</h3>
                        <button onclick="userSection.closeDeleteModal()" class="text-white/60 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                            <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <p id="del-user-name" class="text-sm text-gray-900 mb-2 font-black"></p>
                        <p class="text-xs text-gray-500 mb-8 leading-relaxed font-medium">L'utilisateur perdra tout accès immédiatement. Cette action est irréversible.</p>
                        <div class="flex flex-col gap-3">
                            <button onclick="userSection.executeDelete()" id="user-confirm-delete-btn" class="w-full py-4 bg-[#cb2d2d] text-white font-black rounded-2xl hover:shadow-xl transition-all text-xs uppercase tracking-widest">Confirmer la suppression</button>
                            <button onclick="userSection.closeDeleteModal()" class="w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition text-xs uppercase tracking-widest">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush
</div>

<script>
    window.userSection = {
        deleteTargetId: null,
        openModal: function(mode, user = null) {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');
            const title = document.getElementById('user-modal-title');
            const form = document.getElementById('user-main-form');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
            if (form) form.reset();
            document.getElementById('user-input-id').value = '';
            if(mode === 'edit' && user) {
                title.innerText = 'Modifier le Membre';
                document.getElementById('user-input-id').value = user.id;
                document.getElementById('user-input-name').value = user.name;
                document.getElementById('user-input-email').value = user.email;
                document.getElementById('user-input-role').value = user.role;
            } else { title.innerText = 'Ajouter un Membre'; }
        },
        closeModal: function() {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },
        submitForm: async function(e) {
            e.preventDefault();
            const btn = document.getElementById('user-submit-btn');
            if(!btn || btn.disabled) return;
            const orig = btn.innerHTML; btn.innerHTML = 'Traitement...'; btn.disabled = true;
            const id = document.getElementById('user-input-id').value;
            try {
                const res = await fetch(id ? `/users/${id}` : '/users', {
                    method: id ? 'PUT' : 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(Object.fromEntries(new FormData(e.target)))
                });
                if(res.ok) { showToast('Opération réussie', 'success'); this.closeModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { const d = await res.json(); showToast(d.message || 'Erreur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerHTML = orig; btn.disabled = false; }
        },
        confirmDelete: function(user) {
            this.deleteTargetId = user.id;
            const infoEl = document.getElementById('del-user-name');
            if (infoEl) infoEl.textContent = user.name;
            const wrapper = document.getElementById('user-delete-modal-wrapper');
            const overlay = document.getElementById('user-delete-modal-overlay');
            const container = document.getElementById('user-delete-container');
            if (!wrapper) return;
            wrapper.classList.remove('hidden');
            window.modalUX?.activate(wrapper, container);
            setTimeout(() => { overlay?.classList.remove('opacity-0'); container?.classList.remove('scale-95', 'opacity-0'); }, 10);
        },
        closeDeleteModal: function() {
            const wrapper = document.getElementById('user-delete-modal-wrapper');
            const overlay = document.getElementById('user-delete-modal-overlay');
            const container = document.getElementById('user-delete-container');
            overlay?.classList.add('opacity-0'); container?.classList.add('scale-95', 'opacity-0');
            window.modalUX?.deactivate(wrapper);
            setTimeout(() => { wrapper.classList.add('hidden'); this.deleteTargetId = null; }, 300);
        },
        executeDelete: async function() {
            if(!this.deleteTargetId) return;
            const btn = document.getElementById('user-confirm-delete-btn');
            const orig = btn.innerText; btn.innerText = 'Suppression...'; btn.disabled = true;
            try {
                const res = await fetch(`/users/${this.deleteTargetId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                if(res.ok) { showToast('Supprimé', 'success'); this.closeDeleteModal(); if(window.dashboard) window.dashboard.refresh(); else window.location.reload(); }
                else { showToast('Erreur', 'error'); btn.innerText = orig; btn.disabled = false; }
            } catch(e) { showToast('Erreur serveur', 'error'); btn.innerText = orig; btn.disabled = false; }
        }
    };
</script>
