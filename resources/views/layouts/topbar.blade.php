<header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0 transition-all duration-300">
    <!-- Left: Breadcrumb / Title -->
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-white hover:bg-[#cb2d2d] rounded-xl transition-all shadow-sm" aria-label="Ouvrir le menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <nav class="hidden sm:flex text-sm font-medium text-gray-500 gap-2 items-center">
            <span class="hover:text-[#274256] cursor-pointer transition-colors" 
                  role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') dashboard.show('overview')"
                  onclick="dashboard.show('overview')">
                <svg class="w-4 h-4 inline-block mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </span>
            <span class="text-gray-300">/</span>
            <span class="text-[#274256] font-bold tracking-wide uppercase text-xs" id="topbar-title">VUE D'ENSEMBLE</span>
        </nav>
    </div>

    <!-- Right: Search & Actions -->
    <div class="flex items-center gap-4">
        <!-- Search Bar -->
        <div class="relative hidden md:block group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   class="block w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-xl leading-5 bg-gray-50/50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#cb2d2d]/20 focus:border-[#cb2d2d] sm:text-sm transition-all shadow-sm focus:shadow-md"
                   placeholder="Rechercher...">
        </div>

        <!-- Notifications -->
        <button class="relative p-2 text-gray-400 hover:text-[#cb2d2d] hover:bg-red-50 rounded-xl transition-all duration-300 group">
            <span class="absolute top-2 right-2 w-2 h-2 bg-[#cb2d2d] rounded-full ring-2 ring-white animate-pulse"></span>
            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </button>

        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

        <!-- Role Badge / User Menu -->
        <div class="relative hidden sm:block" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex flex-col items-end cursor-pointer group focus:outline-none text-right">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#274256] uppercase tracking-wider group-hover:text-[#cb2d2d] transition-colors">
                        {{ ucfirst(Auth::user()->role ?? 'Gestionnaire') }}
                    </span>
                    <svg class="w-3 h-3 text-gray-400 group-hover:text-[#cb2d2d] transition-colors duration-300 transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <span class="text-[10px] text-[#cb2d2d] font-bold">ONTARIO GROUP</span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="absolute right-0 mt-4 w-56 bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 py-2 z-50 origin-top-right overflow-hidden" 
                 style="display: none;">
                 
                <div class="px-5 py-3 border-b border-gray-50 mb-1 bg-gray-50/50">
                    <p class="text-sm font-black text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] font-bold text-gray-400 truncate uppercase tracking-wider">{{ Auth::user()->email }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#274256] hover:bg-gray-50 transition-colors uppercase tracking-wide whitespace-nowrap">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mon Profil
                </a>

                <div class="border-t border-gray-50 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 text-left px-4 py-2 text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors uppercase tracking-wide whitespace-nowrap">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
<script>
    // Simple script to update topbar title based on section
    document.addEventListener('DOMContentLoaded', () => {
         const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if(mutation.attributeName === 'style' || mutation.attributeName === 'class'){
                     document.querySelectorAll('[id^="section-content-"]').forEach(section => {
                         if(!section.classList.contains('hidden')) {
                             const titleMap = {
                                 'overview': "VUE D'ENSEMBLE",
                                 'biens': 'PARC IMMOBILIER',
                                 'proprietaires': 'PROPRIÉTAIRES',
                                 'locataires': 'LOCATAIRES',
                                 'contrats': 'CONTRATS & BAUX',
                                 'loyers': 'GESTION DES LOYERS',
                                 'paiements': 'COMPTABILITÉ',
                                 'depenses': 'DÉPENSES',
                                 'utilisateurs': 'UTILISATEURS',
                                 'logs': 'JOURNAUX SYSTÈME',
                                 'parametres': 'CONFIGURATION',
                                 'rapports': 'RAPPORTS'
                             };
                             const id = section.id.replace('section-content-', '');
                             const titleEl = document.getElementById('topbar-title');
                             if(titleEl && titleMap[id]) titleEl.innerText = titleMap[id];
                         }
                     });
                }
            });
         });
         
         const dashboardContent = document.getElementById('dashboard-content');
         if(dashboardContent) {
             observer.observe(dashboardContent, {attributes: true, subtree: true, attributeFilter: ['class', 'style']});
         }
    });
</script>
