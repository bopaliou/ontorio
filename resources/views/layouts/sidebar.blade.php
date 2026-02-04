<aside id="main-sidebar" class="w-64 bg-gradient-to-b from-[#274256] to-[#1a2e3d] border-r border-[#1e3342] fixed h-full z-[100] transform -translate-x-full lg:translate-x-0 flex flex-col text-white transition-all duration-300 shadow-2xl lg:shadow-none">
    <!-- Overlay Mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[-1] lg:hidden hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Close button mobile -->
    <button onclick="toggleSidebar()" class="lg:hidden absolute top-4 -right-12 w-10 h-10 bg-[#274256] text-white flex items-center justify-center rounded-r-xl shadow-lg border-l border-[#1e3342]" aria-label="Fermer le menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <!-- Logo -->
    <div class="h-20 flex items-center justify-center px-6 border-b border-[#1e3342] bg-white shadow-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group Logo" class="h-12 w-auto object-contain">
        </a>
    </div>

    <!-- Nav -->
    <nav role="tablist" aria-label="Menu principal" class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        <a href="#" id="nav-link-overview" data-show-section="overview" role="tab" aria-selected="true" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-[#cb2d2d] text-white shadow-md transition-all">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        @if(App\Helpers\PermissionHelper::can('biens.view'))
        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestion</div>

        <a href="#" id="nav-link-proprietaires" data-show-section="proprietaires" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Mon Agence (Ontario)
        </a>
        <a href="#" id="nav-link-biens" data-show-section="biens" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Biens Immobiliers
        </a>
        <a href="#" id="nav-link-locataires" data-show-section="locataires" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Locataires
        </a>
        <a href="#" id="nav-link-contrats" data-show-section="contrats" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Contrats & Baux
        </a>
        @endif

        @if(App\Helpers\PermissionHelper::can('loyers.view') || App\Helpers\PermissionHelper::can('paiements.view'))
        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</div>

        @if(App\Helpers\PermissionHelper::can('loyers.view'))
        <a href="#" id="nav-link-loyers" data-show-section="loyers" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Loyers & Factures
        </a>
        @endif

        @if(App\Helpers\PermissionHelper::can('paiements.view'))
        <a href="#" id="nav-link-paiements" data-show-section="paiements" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Comptabilité
        </a>
        @endif

        @if(App\Helpers\PermissionHelper::can('depenses.view'))
        <a href="#" id="nav-link-depenses" data-show-section="depenses" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Dépenses & Travaux
        </a>
        @endif
        @endif

        @if(App\Helpers\PermissionHelper::can('users.view'))
        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</div>

        <a href="#" id="nav-link-utilisateurs" data-show-section="utilisateurs" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Utilisateurs
        </a>
        <a href="#" id="nav-link-logs" data-show-section="logs" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Logs Système
        </a>
        @endif

        <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Configuration</div>

        <a href="#" id="nav-link-parametres" data-show-section="parametres" role="tab" aria-selected="false" class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#1e3342] transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Paramètres
        </a>
    </nav>

    <!-- User -->
    <div class="p-4 border-t border-[#1e3342] bg-[#1a2e3d]">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#cb2d2d] flex items-center justify-center font-bold text-white shadow-md">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Utilisateur' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
            </div>
             <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-[#cb2d2d] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
