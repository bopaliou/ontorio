<div class="h-full flex flex-col gap-8" id="parametres-section-container" x-data="{ activeTab: 'profil' }">
    
    <!-- IFRAME MASQUE POUR LES POST (Anti-Reload Pattern) -->
    <iframe name="param_post_target" class="hidden"></iframe>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-ontario-gradient flex items-center justify-center text-white shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                Paramètres
            </h2>
            <p class="text-gray-500 text-sm mt-1">Gestion du profil, des utilisateurs et des rôles</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/50">
            <nav class="flex gap-1 p-2">
                <button @click="activeTab = 'profil'" 
                    :class="activeTab === 'profil' ? 'bg-white text-[#cb2d2d] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mon Profil
                </button>
                @if(auth()->user()->hasRole('admin'))
                <button @click="activeTab = 'users'" 
                    :class="activeTab === 'users' ? 'bg-white text-[#cb2d2d] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Utilisateurs
                </button>
                <button @click="activeTab = 'roles'" 
                    :class="activeTab === 'roles' ? 'bg-white text-[#cb2d2d] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Rôles & Permissions
                </button>
                @endif
            </nav>
        </div>

        <div class="p-6">
            {{-- Profil Tab --}}
            <div x-show="activeTab === 'profil'" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- PROFIL UTILISATEUR --}}
                    <div class="bg-gray-50 p-6 rounded-2xl space-y-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 bg-ontario-gradient rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900">{{ auth()->user()->name }}</h3>
                                <p class="text-xs text-gray-500">{{ auth()->user()->roles->first()?->name ?? auth()->user()->role }}</p>
                            </div>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST" target="param_post_target" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Nom Complet</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full px-5 py-3 rounded-xl border-2 border-gray-200 bg-white focus:ring-4 focus:ring-red-100 focus:border-[#cb2d2d] outline-none transition-all font-bold text-sm">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-5 py-3 rounded-xl border-2 border-gray-200 bg-white focus:ring-4 focus:ring-red-100 focus:border-[#cb2d2d] outline-none transition-all font-bold text-sm">
                            </div>

                            <div class="pt-2 text-right">
                                <button type="submit" class="bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg transition">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- INFO AGENCE --}}
                    <div class="bg-ontario-gradient p-6 rounded-2xl text-white flex flex-col justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-blue-300 uppercase tracking-widest mb-1">Espace de travail</p>
                                <h3 class="text-xl font-black">Ontario Group S.A.</h3>
                                <p class="text-sm text-gray-400 mt-1">Bien loger dans un bon logement</p>
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-white/10 flex justify-between items-center">
                            <p class="text-xs text-blue-200">5 Félix Faure x Colbert, Dakar</p>
                            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold">v1.3.0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Users Tab (Admin only) --}}
            @if(auth()->user()->hasRole('admin'))
            <div x-show="activeTab === 'users'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Utilisateur</th>
                                <th class="text-left py-3 px-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="text-left py-3 px-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Rôle</th>
                                <th class="text-right py-3 px-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach(\App\Models\User::with('roles')->orderBy('name')->get() as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $colors = ['from-[#274256]', 'from-blue-500', 'from-green-500', 'from-amber-500', 'from-red-500'];
                                            $color = $colors[$user->id % count($colors)];
                                        @endphp
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $color }} to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <span class="font-bold text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-gray-600 text-sm">{{ $user->email }}</td>
                                <td class="py-4 px-4">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-red-100 text-red-700',
                                            'gestionnaire' => 'bg-blue-100 text-blue-700',
                                            'comptable' => 'bg-green-100 text-green-700',
                                            'direction' => 'bg-purple-100 text-purple-700',
                                            'agent_commercial' => 'bg-amber-100 text-amber-700',
                                            'technicien' => 'bg-gray-100 text-gray-700',
                                            'proprietaire' => 'bg-indigo-100 text-indigo-700',
                                        ];
                                        $currentRole = $user->roles->first()?->name ?? $user->role ?? 'N/A';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $roleColors[$currentRole] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst(str_replace('_', ' ', $currentRole)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <select 
                                        onchange="updateUserRole({{ $user->id }}, this.value)"
                                        class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-red-500 focus:border-[#cb2d2d] bg-white"
                                    >
                                        <option value="">Changer...</option>
                                        @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                            <option value="{{ $role->name }}" {{ $currentRole === $role->name ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
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

            {{-- Roles Tab (Admin only) --}}
            <div x-show="activeTab === 'roles'" x-cloak>
                <div class="grid gap-4">
                    @foreach(\Spatie\Permission\Models\Role::with('permissions')->get() as $role)
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow" x-data="{ expanded: false }">
                        <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
                            <div class="flex items-center gap-3">
                                @php
                                    $roleConfig = [
                                        'admin' => ['bg' => 'from-red-500 to-rose-600', 'desc' => 'Tous les droits'],
                                        'gestionnaire' => ['bg' => 'from-blue-500 to-cyan-600', 'desc' => 'Gestion patrimoine'],
                                        'comptable' => ['bg' => 'from-green-500 to-emerald-600', 'desc' => 'Finances'],
                                        'direction' => ['bg' => 'from-[#274256] to-[#1a2e3d]', 'desc' => 'Supervision'],
                                        'agent_commercial' => ['bg' => 'from-amber-500 to-orange-600', 'desc' => 'Prospection'],
                                        'technicien' => ['bg' => 'from-gray-500 to-slate-600', 'desc' => 'Maintenance'],
                                        'proprietaire' => ['bg' => 'from-indigo-500 to-blue-600', 'desc' => 'Lecture seule'],
                                    ];
                                    $config = $roleConfig[$role->name] ?? ['bg' => 'from-gray-400 to-gray-500', 'desc' => ''];
                                @endphp
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $config['bg'] }} flex items-center justify-center text-white shadow">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h3>
                                    <p class="text-xs text-gray-500">{{ $config['desc'] }} • {{ $role->permissions->count() }} permissions</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($role->name === 'admin')
                                <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">Protégé</span>
                                @endif
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-gray-100">
                            @php
                                $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
                                    return explode('.', $p->name)[0];
                                });
                            @endphp
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($permissions as $module => $modulePerms)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <h4 class="font-bold text-xs text-gray-500 uppercase mb-2">{{ ucfirst($module) }}</h4>
                                    @foreach($modulePerms as $perm)
                                    <label class="flex items-center gap-2 text-sm py-1 cursor-pointer hover:text-[#cb2d2d]">
                                        <input 
                                            type="checkbox" 
                                            class="rounded border-gray-300 text-[#cb2d2d] focus:ring-[#cb2d2d] perm-checkbox"
                                            data-role="{{ $role->name }}"
                                            data-permission="{{ $perm->name }}"
                                            {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                            {{ $role->name === 'admin' ? 'disabled' : '' }}
                                        >
                                        <span class="text-gray-700 text-xs">{{ explode('.', $perm->name)[1] ?? $perm->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                            @if($role->name !== 'admin')
                            <div class="mt-4 flex justify-end">
                                <button 
                                    onclick="saveRolePermissions('{{ $role->name }}')"
                                    class="px-4 py-2 bg-gradient-to-r from-[#cb2d2d] to-[#ef4444] text-white font-bold text-sm rounded-lg hover:shadow-lg transition"
                                >
                                    Enregistrer
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateUserRole(userId, role) {
    if (!role) return;
    
    fetch(`/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ role: role })
    })
    .then(response => response.json())
    .then(data => {
        window.showToast?.('Rôle mis à jour', 'success');
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        console.error(err);
        window.showToast?.('Erreur', 'error');
    });
}

function saveRolePermissions(roleName) {
    const checkboxes = document.querySelectorAll(`.perm-checkbox[data-role="${roleName}"]:checked`);
    const permissions = Array.from(checkboxes).map(cb => cb.dataset.permission);

    fetch(`/settings/roles/${roleName}/permissions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ permissions: permissions })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.showToast?.('Permissions mises à jour', 'success');
        } else {
            window.showToast?.(data.message || 'Erreur', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        window.showToast?.('Erreur de connexion', 'error');
    });
}
</script>
