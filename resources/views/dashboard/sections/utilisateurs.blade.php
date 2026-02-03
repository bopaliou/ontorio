<div class="h-full flex flex-col gap-8" id="utilisateurs-section-container">
    
    <div id="user-view-list" class="user-sub-view space-y-8">
        <!-- Header -->
        <div class="flex items-end justify-between border-b border-gray-100 pb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Utilisateurs</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Gestion des comptes et des permissions d'accès.</p>
            </div>
            <button onclick="userSection.openModal('create')" class="bg-[#cb2d2d] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-900/20 hover:bg-[#a82020] transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nouvel Utilisateur
            </button>
        </div>

        <!-- Table -->
        <div id="user-table-container" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Identité</th>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rôle</th>
                            <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Depuis</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-gray-600">
                        @forelse($data['users_list'] as $u)
                        <tr class="hover:bg-gray-50/50 transition duration-150 group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center font-bold text-sm group-hover:bg-[#cb2d2d] group-hover:text-white transition-colors">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-900 text-base">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'direction' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'comptable' => 'bg-green-50 text-green-700 border-green-100',
                                        'gestionnaire' => 'bg-orange-50 text-orange-700 border-orange-100'
                                    ];
                                    $cls = $roleColors[$u->role] ?? 'bg-gray-50 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $cls }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-gray-500 font-medium">{{ $u->email }}</td>
                            <td class="px-8 py-5 text-center text-gray-400 text-xs">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="userSection.openModal('edit', {{ json_encode($u) }})" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-white rounded-lg transition" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($u->id !== auth()->id())
                                    <button onclick="userSection.requestDelete({{ $u->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-gray-400 italic bg-gray-50/30">Aucun utilisateur enregistré.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MAIN FORM MODAL (ULTRA COMPACT GRID) -->
    <div id="user-modal-wrapper" class="relative z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div id="user-modal-overlay" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity opacity-0 duration-300"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" onclick="if(event.target === this) userSection.closeModal()">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0" onclick="if(event.target === this) userSection.closeModal()">
                <div id="user-modal-container" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl opacity-0 scale-95 duration-300 border border-gray-100">
                    
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div>
                            <h3 id="user-modal-title" class="text-base font-bold text-gray-900">Gestion Utilisateur</h3>
                            <p class="text-[10px] text-gray-500 font-medium">Paramètres du compte.</p>
                        </div>
                        <button onclick="userSection.closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="user-main-form" class="p-6 space-y-4">
                        <input type="hidden" name="id" id="user-input-id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Nom Complet</label>
                                <input type="text" name="name" id="user-input-name" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="Ex: Jean Dupont">
                            </div>

                            <!-- Email -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Email Professionnel</label>
                                <input type="email" name="email" id="user-input-email" required class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="jean@ontariogroup.net">
                            </div>

                            <!-- Role -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Rôle / Accès</label>
                                <select name="role" id="user-input-role" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 focus:ring-0 appearance-none cursor-pointer">
                                    <option value="gestionnaire">Gestionnaire</option>
                                    <option value="comptable">Comptable</option>
                                    <option value="direction">Direction</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="relative bg-gray-50 rounded-xl border border-gray-200 px-3 py-2 focus-within:ring-2 focus-within:ring-[#cb2d2d]/10 focus-within:border-[#cb2d2d] transition-all">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Mot de passe</label>
                                <input type="password" name="password" id="user-input-password" class="block w-full bg-transparent border-none p-0 text-sm font-bold text-gray-900 placeholder-gray-300 focus:ring-0" placeholder="••••••••">
                            </div>
                        </div>

                        <p class="text-[9px] text-gray-400 italic hidden text-center" id="user-pwd-hint">Laisser vide pour conserver l'actuel</p>

                        <!-- Footer Actions -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" onclick="userSection.closeModal()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition text-[11px] uppercase tracking-widest">Annuler</button>
                            <button type="submit" id="user-submit-btn" class="bg-[#cb2d2d] text-white px-6 py-2.5 rounded-xl font-black hover:bg-[#a82020] transition shadow-lg shadow-red-900/10 text-[11px] uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL (UNCHANGED) -->
    <div id="user-delete-modal" onclick="if(event.target === this) userSection.closeDeleteModal()" class="fixed inset-0 z-[120] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0 duration-300">
        <div id="user-delete-container" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform scale-95 transition-all duration-300">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Supprimer l'accès ?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Cet utilisateur ne pourra plus se connecter à la plateforme. Êtes-vous sûr ?</p>
            <div class="flex flex-col gap-3">
                <button id="user-confirm-delete-btn" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 text-sm tracking-wide">
                    Oui, Supprimer
                </button>
                <button onclick="userSection.closeDeleteModal()" class="w-full px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-sm">
                    Non, Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
                title.innerText = 'Modifier Utilisateur';
                btn.innerHTML = 'Mettre à jour';
                document.getElementById('user-input-id').value = user.id;
                document.getElementById('user-input-name').value = user.name;
                document.getElementById('user-input-email').value = user.email;
                document.getElementById('user-input-role').value = user.role;
                document.getElementById('user-pwd-hint').classList.remove('hidden');
            } else {
                title.innerText = 'Nouvel Utilisateur';
                btn.innerHTML = 'Enregistrer';
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
            btn.innerText = 'Traitement...';
            btn.disabled = true;

            try {
                const response = await fetch(`/users/${this.deleteTargetId}`, { // Attention Route /users/
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if(response.ok) {
                    showToast('Utilisateur supprimé', 'success');
                    window.location.reload();
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
                window.location.reload();
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
            btn.disabled = false;
        }
    });
</script>
