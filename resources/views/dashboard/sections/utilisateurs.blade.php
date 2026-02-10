@php
    $roleConfig = [
        'admin' => ['bg' => 'bg-red-50', 'text' => 'text-[#cb2d2d]', 'border' => 'border-red-100', 'label' => 'Administrateur'],
        'direction' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'label' => 'Direction'],
        'comptable' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'label' => 'Comptabilité'],
        'gestionnaire' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-100', 'label' => 'Gestionnaire'],
        'locataire' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100', 'label' => 'Locataire'],
        'proprietaire' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'label' => 'Propriétaire'],
    ];

    // Try to get roles from DB, otherwise fallback to config keys
    try {
        $dbRoles = \Spatie\Permission\Models\Role::all()->pluck('name')->toArray();
    } catch (\Exception $e) {
        $dbRoles = [];
    }

    $availableRoles = !empty($dbRoles) ? $dbRoles : array_keys($roleConfig);
@endphp

<div class="h-full flex flex-col gap-8" id="utilisateurs-section-container">

    <div id="user-view-list" class="user-sub-view space-y-8">
        <!-- Header -->
        @include('components.section-header', [
            'title' => 'Équipe & Accès',
            'subtitle' => 'Gérez les membres de votre agence et leurs permissions.',
            'icon' => 'users',
            'actions' => '<button onclick="userSection.openModal(\'create\')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5 animate-bounce-subtle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajouter un Membre
            </button>'
        ])

        <!-- Team Grid Layout -->
        <div id="user-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-stagger">
            @forelse($data['users_list'] as $u)
                @php
                    $initials = strtoupper(substr($u->name, 0, 1) . substr(explode(' ', $u->name)[1] ?? '', 0, 1));
                    $role = $roleConfig[$u->role] ?? $roleConfig['gestionnaire'];
                @endphp

                <!-- User Card -->
                <div class="group relative bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden cursor-default">

                    {{-- Decorative Top Line --}}
                    <div class="absolute top-0 left-0 w-full h-1 {{ str_replace('bg-', 'bg-', $role['bg']) }} opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    {{-- Actions (Top Right) --}}
                    <div class="absolute top-4 right-4 flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                        <button onclick="userSection.openModal('edit', {{ json_encode($u) }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        @if($u->id !== auth()->id())
                        <button onclick="userSection.requestDelete({{ $u->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Supprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @endif
                    </div>

                    <div class="flex flex-col items-center text-center mt-2">
                        {{-- Avatar --}}
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-2xl font-black mb-4 transition-transform group-hover:scale-110 shadow-inner {{ $role['bg'] }} {{ $role['text'] }}">
                            {{ $initials }}
                        </div>

                        {{-- Name & Role --}}
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">{{ $u->name }}</h3>
                        <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest border {{ $role['bg'] }} {{ $role['text'] }} {{ $role['border'] }}">
                            {{ $role['label'] }}
                        </span>

                        {{-- Divider --}}
                        <div class="w-full h-px bg-gray-50 my-5"></div>

                        {{-- Details --}}
                        <div class="w-full space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400 font-medium">Email</span>
                                <span class="text-gray-600 font-semibold truncate max-w-[120px]" title="{{ $u->email }}">{{ $u->email }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400 font-medium">Membre depuis</span>
                                <span class="text-gray-900 font-bold">{{ $u->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-lg">Aucun membre</h3>
                    <p class="text-gray-500 text-sm mt-1 max-w-xs">Commencez par ajouter des utilisateurs pour collaborer sur la plateforme.</p>
                </div>
            @endforelse

            {{-- "Add New" Placeholder Card --}}
            <button type="button" onclick="userSection.openModal('create')"
                 class="group flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-200 rounded-3xl hover:border-[#cb2d2d] hover:bg-red-50/10 cursor-pointer transition-all duration-300 min-h-[320px] w-full bg-transparent">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#cb2d2d] group-hover:text-white transition-all duration-300 shadow-sm group-hover:scale-110 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="text-gray-400 group-hover:text-[#cb2d2d] font-bold text-sm uppercase tracking-widest transition-colors">Ajouter un Membre</h3>
            </div>
        </button>
    </div>

    <!-- MAIN FORM MODAL (Revised Layout) -->
    <div id="user-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="user-modal-title" role="dialog" aria-modal="true">
        <div id="user-modal-overlay" class="fixed inset-0 bg-[#274256]/60 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) userSection.closeModal()">
            <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-0" onclick="if(event.target === this) userSection.closeModal()">
                <div id="user-modal-container" class="relative transform overflow-hidden bg-white text-left shadow-2xl transition-all w-full h-full sm:h-auto sm:w-full sm:max-w-xl sm:my-8 rounded-none sm:rounded-3xl opacity-0 scale-95 duration-300 border border-gray-100">

                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <div>
                            <h3 id="user-modal-title" class="text-xl font-black text-gray-900 tracking-tight">Nouveau Membre</h3>
                            <p class="text-xs text-gray-500 font-medium mt-1">Configuration du profil et des accès.</p>
                        </div>
                        <button onclick="userSection.closeModal()" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 flex items-center justify-center transition" aria-label="Fermer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="user-main-form" class="p-8 space-y-6">
                        <input type="hidden" name="id" id="user-input-id">

                        {{-- Avatar Visual Placeholder (Static) --}}
                        <div class="flex justify-center mb-6">
                             <div class="w-20 h-20 rounded-full bg-gray-100 border-4 border-white shadow-lg flex items-center justify-center">
                                 <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                             </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="user-input-name">Nom Complet</label>
                                <input type="text" name="name" id="user-input-name" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="Ex: Jean Dupont">
                            </div>

                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="user-input-email">Email Professionnel</label>
                                <input type="email" name="email" id="user-input-email" required class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="jean@ontariogroup.net">
                            </div>

                            <!-- Role -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="user-input-role">Rôle / Accès</label>
                                <div class="relative">
                                    <select name="role" id="user-input-role" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 appearance-none cursor-pointer pr-10">
                                        <option value="">-- Sélectionner un rôle --</option>
                                        @foreach($availableRoles as $roleName)
                                            <option value="{{ $roleName }}">{{ ucfirst(str_replace('_', ' ', $roleName)) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1" for="user-input-password">Mot de passe</label>
                                <input type="password" name="password" id="user-input-password" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-shadow" placeholder="••••••••">
                            </div>
                        </div>

                        <div id="user-pwd-hint" class="hidden flex items-center justify-center gap-2 p-3 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Laisser le mot de passe vide pour conserver l'actuel.
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="userSection.closeModal()" class="px-6 py-3 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-xs uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="user-submit-btn" class="bg-[#274256] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#1a2e3d] transition shadow-lg shadow-blue-900/20 text-xs uppercase tracking-widest flex items-center gap-2 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL (Reused but styled) -->
    <div id="user-delete-modal" role="dialog" aria-modal="true" aria-labelledby="user-delete-modal-title" onclick="if(event.target === this) userSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="user-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                <svg class="w-10 h-10 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
            </div>
            <h3 id="user-delete-modal-title" class="text-xl font-black text-gray-900 mb-2">Supprimer ce membre ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed font-medium">Cette action est irréversible. L'utilisateur perdra immédiatement tout accès à la plateforme.</p>
            <div class="flex flex-col gap-3">
                <button id="user-confirm-delete-btn" class="w-full px-6 py-3.5 bg-[#cb2d2d] text-white font-bold rounded-xl hover:bg-[#b02222] transition shadow-lg shadow-red-900/20 text-xs tracking-widest uppercase">
                    Oui, Supprimer définitivement
                </button>
                <button onclick="userSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-xs tracking-widest uppercase">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic remains mostly the same, just keeping it alive
    window.userSection = {
        deleteTargetId: null,

        openModal: function(mode, user = null) {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');
            const form = document.getElementById('user-main-form');
            const title = document.getElementById('user-modal-title');
            const btn = document.getElementById('user-submit-btn');

            wrapper.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);

            form.reset();
            document.getElementById('user-input-id').value = '';
            document.getElementById('user-pwd-hint').classList.add('hidden');

            if(mode === 'edit' && user) {
                title.innerText = 'Modifier le Membre';
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mettre à jour';
                document.getElementById('user-input-id').value = user.id;
                document.getElementById('user-input-name').value = user.name;
                document.getElementById('user-input-email').value = user.email;
                document.getElementById('user-input-role').value = user.role;
                document.getElementById('user-pwd-hint').classList.remove('hidden');
            } else {
                title.innerText = 'Nouveau Membre';
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
            }
        },

        closeModal: function() {
            const wrapper = document.getElementById('user-modal-wrapper');
            const overlay = document.getElementById('user-modal-overlay');
            const container = document.getElementById('user-modal-container');

            overlay.classList.add('opacity-0');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');

            setTimeout(() => { wrapper.classList.add('hidden'); }, 300);
        },

        requestDelete: function(id) {
            this.deleteTargetId = id;
            const modal = document.getElementById('user-delete-modal');
            const container = document.getElementById('user-delete-container');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        },

        closeDeleteModal: function() {
            const modal = document.getElementById('user-delete-modal');
            const container = document.getElementById('user-delete-container');

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

            const btn = document.getElementById('user-confirm-delete-btn');
            const originalText = btn.innerText;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            btn.disabled = true;

            try {
                const response = await fetch(`/users/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if(response.ok) {
                    showToast('Utilisateur supprimé', 'success');
                    if(window.dashboard) window.dashboard.refresh();
                    else window.location.reload();
                } else {
                    showToast('Erreur lors de la suppression', 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('Erreur serveur', 'error');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
                this.closeDeleteModal();
            }
        }
    };

    document.getElementById('user-confirm-delete-btn').addEventListener('click', function() {
        userSection.executeDelete();
    });

    document.getElementById('user-main-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('user-submit-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sauvegarde...';
        btn.disabled = true;

        const formData = new FormData(this);
        const id = document.getElementById('user-input-id').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/users/${id}` : `{{ route('users.store') }}`;

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
                showToast('Utilisateur enregistré', 'success');
                userSection.closeModal();
                if(window.dashboard) window.dashboard.refresh();
                else window.location.reload();
            } else {
                if(data.errors) {
                     showToast(Object.values(data.errors)[0][0], 'error');
                } else {
                     showToast(data.message || 'Erreur', 'error');
                }
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
