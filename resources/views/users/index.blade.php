<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Gestion des Utilisateurs ✨</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <!-- Add User Button -->
                <button onclick="userModal.open('create')" class="btn bg-[#cb2d2d] hover:bg-[#b02222] text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Ajouter un Utilisateur</span>
                </button>
            </div>
        </div>

        <!-- Cards Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <x-dashboard-card title="Utilisateurs Total" :value="$stats['total']" icon="users" color="blue" />
            <x-dashboard-card title="Administrateurs" :value="$stats['admin']" icon="shield-check" color="purple" />
            <x-dashboard-card title="Gestionnaires" :value="$stats['gestionnaire']" icon="user" color="green" />
            <x-dashboard-card title="Direction" :value="$stats['direction']" icon="briefcase" color="indigo" />
        </div>

        <!-- Table -->
        <div class="bg-white/80 backdrop-blur-md border border-white/20 shadow-xl rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Liste des Comptes</h2>
                <form action="{{ route('users.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="form-input text-sm border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] rounded-lg">
                    <select name="role" class="form-select text-sm border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] rounded-lg">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="gestionnaire" {{ request('role') == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                        <option value="direction" {{ request('role') == 'direction' ? 'selected' : '' }}>Direction</option>
                        <option value="comptable" {{ request('role') == 'comptable' ? 'selected' : '' }}>Comptable</option>
                    </select>
                    <button type="submit" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold border-b border-gray-100">Utilisateur</th>
                            <th class="px-6 py-4 font-bold border-b border-gray-100">Rôle</th>
                            <th class="px-6 py-4 font-bold border-b border-gray-100">Email</th>
                            <th class="px-6 py-4 font-bold border-b border-gray-100">Date Ajout</th>
                            <th class="px-6 py-4 font-bold border-b border-gray-100 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-blue-700 font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'gestionnaire' => 'bg-green-100 text-green-700 border-green-200',
                                        'direction' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                        'comptable' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    ];
                                    $roleColor = $colors[$user->role] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $roleColor }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="userModal.open('edit', {{ json_encode($user) }})" class="p-2 bg-white border border-gray-200 rounded-lg hover:border-blue-400 hover:text-blue-600 shadow-sm transition-all" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-white border border-gray-200 rounded-lg hover:border-red-400 hover:text-red-600 shadow-sm transition-all" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal User (Create/Edit) -->
    <div id="user-modal" class="fixed inset-0 z-[200] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    <form id="user-form" onsubmit="handleUserSubmit(event)">
                        @csrf
                        <input type="hidden" id="user-method" name="_method" value="POST">
                        <input type="hidden" id="user-id" name="user_id">
                        
                        <div class="bg-gradient-to-r from-[#274256] to-[#1a2e3d] px-6 py-4 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">
                                <span id="modal-title-text">Nouvel Utilisateur</span>
                            </h3>
                            <button type="button" onclick="userModal.close()" class="text-white/60 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                                <input type="text" name="name" id="user-name" required class="w-full rounded-xl border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" id="user-email" required class="w-full rounded-xl border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                                <select name="role" id="user-role" required class="w-full rounded-xl border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] shadow-sm">
                                    <option value="gestionnaire">Gestionnaire</option>
                                    <option value="comptable">Comptable</option>
                                    <option value="direction">Direction</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span id="pwd-hint" class="text-xs text-gray-400 font-normal">(Laisser vide si inchangé)</span></label>
                                <input type="password" name="password" id="user-password" class="w-full rounded-xl border-gray-300 focus:border-[#cb2d2d] focus:ring-[#cb2d2d] shadow-sm">
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" onclick="userModal.close()" class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 shadow-sm transition-all">Annuler</button>
                            <button type="submit" class="px-6 py-2 bg-[#cb2d2d] text-white rounded-xl font-bold hover:bg-[#b02222] shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        const userModal = {
            el: document.getElementById('user-modal'),
            form: document.getElementById('user-form'),
            title: document.getElementById('modal-title-text'),
            method: document.getElementById('user-method'),
            idInput: document.getElementById('user-id'),
            
            open(mode, user = null) {
                this.el.classList.remove('hidden');
                
                if (mode === 'create') {
                    this.title.textContent = 'Nouvel Utilisateur';
                    this.form.action = "{{ route('users.store') }}";
                    this.method.value = 'POST';
                    this.form.reset();
                    document.getElementById('pwd-hint').classList.add('hidden');
                    document.getElementById('user-password').required = true;
                } else {
                    this.title.textContent = 'Modifier Utilisateur';
                    this.form.action = `/users/${user.id}`;
                    this.method.value = 'PUT';
                    this.idInput.value = user.id;
                    
                    document.getElementById('user-name').value = user.name;
                    document.getElementById('user-email').value = user.email;
                    document.getElementById('user-role').value = user.role;
                    
                    document.getElementById('pwd-hint').classList.remove('hidden');
                    document.getElementById('user-password').required = false;
                }
            },
            
            close() {
                this.el.classList.add('hidden');
            }
        };

        async function handleUserSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST', // Always POST, _method handles PUT
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    userModal.close();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.message || 'Une erreur est survenue', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('Erreur de connexion', 'error');
            }
        }
    </script>

</x-app-layout>
