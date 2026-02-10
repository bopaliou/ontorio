<div class="h-full flex flex-col gap-8" id="parametres-section-container" x-data="{ activeTab: 'profil' }">

    <!-- Refresh iframe remains for background updates if needed -->
    <iframe id="param_refresh_iframe" class="hidden"></iframe>

    @include('components.section-header', [
        'title' => 'Paramètres',
        'subtitle' => 'Centre de configuration de votre espace de travail.',
        'icon' => 'cog',
        'actions' => ''
    ])

    {{-- Tab Navigation (Floating Pill Style) --}}
    <div class="flex justify-center mb-4">
        <nav class="bg-white/80 backdrop-blur-md p-1.5 rounded-full border border-gray-200/60 shadow-lg shadow-gray-200/50 flex items-center gap-1 relative z-10">
            <button @click="activeTab = 'profil'"
                :class="activeTab === 'profil' ? 'bg-[#cb2d2d] text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 flex items-center gap-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mon Profil
            </button>
            @if(auth()->user()->hasRole('admin'))
            <button @click="activeTab = 'users'"
                :class="activeTab === 'users' ? 'bg-[#cb2d2d] text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 flex items-center gap-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Utilisateurs
            </button>
            <button @click="activeTab = 'roles'"
                :class="activeTab === 'roles' ? 'bg-[#cb2d2d] text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'"
                class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 flex items-center gap-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Rôles & Accès
            </button>
            @endif
        </nav>
    </div>

    {{-- Content Area --}}
    <div class="animate-fade-in-up">

        {{-- TAB: PROFIL --}}
        <div x-show="activeTab === 'profil'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left: Personal Card --}}
            <div class="lg:col-span-7 bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gray-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6">Informations Personnelles</h3>

                <div class="flex items-start gap-8">
                    <div class="shrink-0 relative">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-[2rem] flex items-center justify-center text-gray-400 shadow-inner text-3xl font-black">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center" title="En ligne">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>

                    <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" class="w-full space-y-5">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label for="param-input-name" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Nom Complet</label>
                                <input type="text" name="name" id="param-input-name" value="{{ auth()->user()->name }}" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label for="param-input-email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1">Email</label>
                                <input type="email" name="email" id="param-input-email" value="{{ auth()->user()->email }}" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 transition-all" readonly title="Contactez l'admin pour changer">
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-[#274256] text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-[#1a2e3d] hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: Company Card (Glass) --}}
            <div class="lg:col-span-5 relative group h-full">
                <div class="absolute inset-0 bg-gradient-to-br from-[#cb2d2d] to-[#ef4444] rounded-3xl -rotate-1 opacity-80 group-hover:rotate-1 transition-transform duration-500 blur-sm"></div>
                <div class="relative h-full bg-[#1a1a1a] rounded-3xl p-8 text-white overflow-hidden border border-white/10 flex flex-col justify-between backdrop-blur-xl">

                    {{-- Noise Texture --}}
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noise\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noise)\'/%3E%3C/svg%3E');"></div>

                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20">
                                <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/></svg>
                            </div>
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-widest text-red-200">Workspace</span>
                        </div>

                        <h3 class="text-3xl font-black tracking-tight mb-2">Ontario Group</h3>
                        <p class="text-sm text-gray-400 font-medium leading-relaxed">Bien loger, dans un bon logement.</p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/10 w-full space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 uppercase tracking-widest font-bold">Rôle Actuel</span>
                            <span class="text-white font-bold bg-white/10 px-3 py-1 rounded-lg">{{ auth()->user()->roles->first()?->name ?? 'Utilisateur' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 uppercase tracking-widest font-bold">Dernière Connexion</span>
                            <span class="text-gray-300 font-mono">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: USERS (Admin) --}}
        @if(auth()->user()->hasRole('admin'))
        <div x-show="activeTab === 'users'" x-cloak>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membre</th>
                                <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contact</th>
                                <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rôle</th>
                                <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach(\App\Models\User::with('roles')->orderBy('name')->get() as $user)
                            <tr class="group hover:bg-gray-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 font-black text-xs shadow-sm group-hover:bg-white group-hover:shadow-md transition-all">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <span class="font-bold text-gray-900 text-sm">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs font-medium text-gray-500 font-mono">{{ $user->email }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $user->roles->first()?->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <select onchange="updateUserRole({{ $user->id }}, this.value)" class="text-xs font-bold bg-white border border-gray-200 text-gray-700 rounded-lg px-3 py-2 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] shadow-sm cursor-pointer hover:border-gray-300 transition-colors">
                                        @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}" {{ ($user->roles->first()?->name ?? '') === $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB: ROLES (Admin) --}}
        <div x-show="activeTab === 'roles'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(\Spatie\Permission\Models\Role::with('permissions')->get() as $role)
            @php
                $colors = [
                    'admin' => 'from-red-500 to-rose-600',
                    'gestionnaire' => 'from-blue-500 to-cyan-600',
                    'default' => 'from-gray-500 to-gray-600'
                ];
                $gradient = $colors[$role->name] ?? $colors['default'];
            @endphp
            <div x-data="{ expanded: false }" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                {{-- Card Header --}}
                <div @click="expanded = !expanded" class="p-6 cursor-pointer relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br {{ $gradient }} opacity-10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:opacity-20 transition-opacity"></div>

                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white shadow-lg shadow-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-lg tracking-tight">{{ ucfirst($role->name) }}</h4>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ $role->permissions->count() }} Permissions</p>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#cb2d2d] group-hover:text-white transition-all">
                            <svg class="w-4 h-4 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Permissions Content --}}
                <div x-show="expanded" x-collapse>
                    <div class="px-6 pb-6 pt-2 border-t border-gray-50 bg-gray-50/30">
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            @foreach(\Spatie\Permission\Models\Permission::all() as $perm)
                            <label class="group flex items-center gap-3 p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all cursor-pointer {{ $role->name === 'admin' ? 'opacity-50 pointer-events-none' : '' }}">
                                <div class="relative flex items-center">
                                    <input type="checkbox" class="peer h-4 w-4 rounded border-gray-300 text-[#cb2d2d] focus:ring-[#cb2d2d] transition-all perm-checkbox"
                                           data-role="{{ $role->name }}"
                                           data-permission="{{ $perm->name }}"
                                           {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                </div>
                                <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900 truncate" title="{{ $perm->name }}">{{ explode('.', $perm->name)[1] ?? $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>

                        @if($role->name !== 'admin')
                        <button onclick="saveRolePermissions('{{ $role->name }}')" class="w-full py-3 bg-white border border-gray-200 text-gray-700 font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-[#cb2d2d] hover:text-white hover:border-[#cb2d2d] transition-all shadow-sm">
                            Enregistrer les droits
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
    // Existing logic preserved and optimized
    function updateUserRole(userId, role) {
        if (!role) return;
        fetch(`/users/${userId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ role: role })
        })
        .then(r => r.json())
        .then(d => { showToast('Rôle mis à jour', 'success'); setTimeout(() => location.reload(), 800); })
        .catch(e => showToast('Erreur', 'error'));
    }

    function saveRolePermissions(roleName) {
        const perms = Array.from(document.querySelectorAll(`.perm-checkbox[data-role="${roleName}"]:checked`)).map(cb => cb.dataset.permission);
        fetch(`/settings/roles/${roleName}/permissions`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ permissions: perms })
        })
        .then(r => r.json())
        .then(d => showToast(d.success ? 'Droits enregistrés' : 'Erreur', d.success ? 'success' : 'error'))
        .catch(e => showToast('Erreur serveur', 'error'));
    }

    // Gestion de la mise à jour du profil via AJAX
    document.getElementById('profile-update-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        btn.disabled = true;

        const formData = new FormData(this);

        try {
            const response = await fetch(this.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PATCH' // Laravel @method('PATCH')
                },
                body: formData
            });

            const data = await response.json();

            if(response.ok) {
                showToast('Profil mis à jour', 'success');
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch(e) {
            console.error(e);
            showToast('Erreur serveur', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>
