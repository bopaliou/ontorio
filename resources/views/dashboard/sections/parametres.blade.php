<div class="h-full flex flex-col gap-8" id="parametres-section-container">
    
    <!-- IFRAME MASQUE POUR LES POST (Anti-Reload Pattern) -->
    <iframe name="param_post_target" class="hidden"></iframe>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#274256]">Paramètres</h2>
            <p class="text-sm text-gray-500 mt-1">Gérez votre profil et les configurations de l'application.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- PROFIL UTILISATEUR -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm space-y-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-[#274256] rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[#274256]">Mon Profil</h3>
                    <p class="text-xs text-gray-400 uppercase tracking-widest">{{ auth()->user()->role }}</p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" target="param_post_target" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Nom Complet</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full px-5 py-3 rounded-2xl border-2 border-gray-50 bg-gray-50/30 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-[#274256] outline-none transition-all font-bold text-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-5 py-3 rounded-2xl border-2 border-gray-50 bg-gray-50/30 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-[#274256] outline-none transition-all font-bold text-sm">
                </div>

                <div class="pt-2 text-right">
                    <button type="submit" class="bg-[#274256] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#1a2e3d] transition shadow-lg shadow-blue-900/10">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- SÉCURITÉ (MOT DE PASSE) - Placeholder visuel pour l'instant car nécessite confirm password complex -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm space-y-6 opacity-60 pointer-events-none relative">
            <div class="absolute inset-0 z-10 flex items-center justify-center">
                 <span class="bg-gray-100 px-3 py-1 rounded-full text-[10px] font-bold text-gray-500 uppercase">À venir</span>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[#274256]">Sécurité</h3>
                    <p class="text-xs text-gray-400 uppercase tracking-widest">Mot de passe</p>
                </div>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Mot de passe actuel</label>
                    <input type="password" class="w-full px-5 py-3 rounded-2xl border-2 border-gray-50 bg-gray-50/30" disabled>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2 ml-1">Nouveau mot de passe</label>
                    <input type="password" class="w-full px-5 py-3 rounded-2xl border-2 border-gray-50 bg-gray-50/30" disabled>
                </div>
                <div class="pt-2 text-right">
                    <button disabled class="bg-gray-200 text-gray-400 px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest">
                        Modifier
                    </button>
                </div>
            </form>
        </div>

        <!-- INFO AGENCE -->
        <div class="lg:col-span-2 bg-gradient-to-r from-[#274256] to-[#1a2e3d] p-8 rounded-3xl text-white shadow-xl shadow-blue-900/20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/10">
                    <!-- Fake Logo -->
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-blue-300 uppercase tracking-widest mb-1">Espace de travail</p>
                    <h3 class="text-2xl font-black">Ontario Group S.A.</h3>
                    <p class="text-sm text-gray-400 mt-1">Bien loger dans un bon logement</p>
                    <p class="text-xs text-blue-200 mt-3 font-medium">5 Félix Faure x Colbert, Dakar Plateau</p>
                </div>
            </div>
            
            <div class="text-right hidden md:block">
                 <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Version Logiciel</p>
                 <p class="text-lg font-bold text-white">v1.2.0 (Beta)</p>
            </div>
        </div>
    </div>
</div>
