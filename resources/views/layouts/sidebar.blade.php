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
    <nav role="tablist" aria-label="Menu principal" class="flex-1 overflow-y-auto no-scrollbar py-6 px-3 space-y-1">
        <a href="{{ route('dashboard') }}#overview" id="nav-link-overview" data-show-section="overview" role="tab" aria-selected="true" class="sidebar-nav-link">
            <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="font-bold">Dashboard</span>
        </a>

        @if(App\Helpers\PermissionHelper::can('biens.view'))
        <div class="sidebar-section-label cursor-pointer flex justify-between items-center pr-4" onclick="toggleSection('gestion')">
            Gestion
            <svg id="icon-gestion" class="w-4 h-4 transition-transform duration-300 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-gestion" class="transition-all duration-300 origin-top overflow-hidden">
            <a href="{{ route('dashboard') }}#proprietaires" id="nav-link-proprietaires" data-show-section="proprietaires" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Mon Agence</span>
            </a>
            <a href="{{ route('dashboard') }}#biens" id="nav-link-biens" data-show-section="biens" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Biens Immo</span>
            </a>
            <a href="{{ route('dashboard') }}#locataires" id="nav-link-locataires" data-show-section="locataires" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Locataires</span>
            </a>
            <a href="{{ route('dashboard') }}#contrats" id="nav-link-contrats" data-show-section="contrats" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Contrats</span>
            </a>
        </div>
        @endif

        @if(App\Helpers\PermissionHelper::can('loyers.view') || App\Helpers\PermissionHelper::can('paiements.view'))
        <div class="sidebar-section-label cursor-pointer flex justify-between items-center pr-4" onclick="toggleSection('finance')">
            Finance
            <svg id="icon-finance" class="w-4 h-4 transition-transform duration-300 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-finance" class="transition-all duration-300 origin-top overflow-hidden">
            @if(App\Helpers\PermissionHelper::can('loyers.view'))
            <a href="{{ route('dashboard') }}#loyers" id="nav-link-loyers" data-show-section="loyers" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Loyers/Factures</span>
            </a>
            @endif

            @if(App\Helpers\PermissionHelper::can('paiements.view'))
            <a href="{{ route('dashboard') }}#paiements" id="nav-link-paiements" data-show-section="paiements" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Comptabilité</span>
            </a>
            @endif

            @if(App\Helpers\PermissionHelper::can('depenses.view'))
            <a href="{{ route('dashboard') }}#depenses" id="nav-link-depenses" data-show-section="depenses" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Dépenses</span>
            </a>
            @endif
        </div>
        @endif

        @if(Auth::user()->role === 'admin')
        <div class="sidebar-section-label cursor-pointer flex justify-between items-center pr-4" onclick="toggleSection('admin')">
            Admin
            <svg id="icon-admin" class="w-4 h-4 transition-transform duration-300 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-admin" class="transition-all duration-300 origin-top overflow-hidden">
            <a href="{{ route('dashboard') }}#utilisateurs" id="nav-link-utilisateurs" data-show-section="utilisateurs" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Utilisateurs</span>
            </a>
            <a href="{{ route('dashboard') }}#logs" id="nav-link-logs" data-show-section="logs" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Logs</span>
            </a>
        </div>
        @endif

        @if(Auth::user()->role === 'admin')
        <div class="sidebar-section-label cursor-pointer flex justify-between items-center pr-4" onclick="toggleSection('config')">
            Config
            <svg id="icon-config" class="w-4 h-4 transition-transform duration-300 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-config" class="transition-all duration-300 origin-top overflow-hidden">
            <a href="{{ route('dashboard') }}#parametres" id="nav-link-parametres" data-show-section="parametres" role="tab" aria-selected="false" class="sidebar-nav-link">
                <svg class="text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Paramètres</span>
            </a>
        </div>
        @endif
    </nav>

    <!-- User -->
    <div class="p-4 border-t border-[#1e3342] bg-[#1a2e3d]">
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 group no-underline">
                <div class="w-9 h-9 rounded-full bg-[#cb2d2d] flex items-center justify-center font-bold text-white shadow-md group-hover:ring-2 group-hover:ring-white transition-all">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate group-hover:text-gray-300 transition-colors">{{ Auth::user()->name ?? 'Utilisateur' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
                </div>
            </a>
             <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-[#cb2d2d] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    function toggleSection(sectionId) {
        const section = document.getElementById('section-' + sectionId);
        const icon = document.getElementById('icon-' + sectionId);

        // If maxHeight is set and not 0px, it means it's open
        if (section.style.maxHeight && section.style.maxHeight !== '0px') {
            // Close
            section.style.maxHeight = '0px';
            icon.classList.remove('rotate-180'); // Icon points down (default/closed view depends on pref, assuming chevron down for closed?)
            // Wait, previous logic: 
            // open -> icon rotate-0 (v), closed -> icon rotate-180 (^) ?
            // Let's stick to: chevron down (v) = closed/expandable? Or chevron up (^) = open/collapsible?
            // Usually: > (points right) closed, v (points down) open.
            // My icon is: d="M19 9l-7 7-7-7" (chevron down).
            // So default (no rotate) is Down.
            
            // Let's align with common pattern:
            // Open: Chevron Up (rotate-180)
            // Closed: Chevron Down (rotate-0)
            
            // Current code in blade:
            // Icon has "rotate-180" class inline? No, let's check view_file output. 
            // In replace_file_content #161: 
            // <svg ... class="... transform rotate-180" ...>
            // So default init is rotate-180.
            
            // Logic in #161:
            // Close -> add rotate-180.
            // Open -> remove rotate-180.
             
            // So rotate-180 means Closed? Or Open?
            // If I init with rotate-180 and sections match open/closed state.
            // Re-reading logic in #161:
            // Init: sections are open (maxHeight set).
            // HTML: class="... rotate-180"
            // So Open = rotate-180.
            
            // New Logic:
            // Close: set 0px, remove rotate-180 (so it becomes rotate-0).
            // Open: set scrollHeight, add rotate-180.
            
            icon.classList.remove('rotate-180');
            icon.classList.add('rotate-0');
        } else {
            // Open
            section.style.maxHeight = section.scrollHeight + "px";
            icon.classList.add('rotate-180');
            icon.classList.remove('rotate-0');
        }
    }

    // Init sections as open
    document.addEventListener('DOMContentLoaded', () => {
        ['gestion', 'finance', 'admin', 'config'].forEach(id => {
            const section = document.getElementById('section-' + id);
            if (section) {
                section.style.maxHeight = section.scrollHeight + "px";
            }
        });
    });
</script>
