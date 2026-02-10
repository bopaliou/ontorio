<aside id="main-sidebar" class="w-72 bg-[#1a2e3d] border-r border-[#263a4d] fixed h-full z-[100] transform -translate-x-full lg:translate-x-0 flex flex-col transition-transform duration-300 shadow-2xl lg:shadow-none font-sans">
    <!-- Overlay Mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[-1] lg:hidden hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Close button mobile -->
    <button onclick="toggleSidebar()" class="lg:hidden absolute top-4 -right-12 w-10 h-10 bg-[#cb2d2d] text-white flex items-center justify-center rounded-r-xl shadow-lg hover:bg-[#a82020] transition-colors" aria-label="Fermer le menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <!-- Logo Area -->
    <div class="h-28 flex items-center justify-center bg-white border-b border-gray-100 relative overflow-hidden ring-1 ring-black/5">
        <a href="{{ route('dashboard') }}" class="block transition-transform hover:scale-105 duration-300">
            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="h-16 w-auto object-contain mx-auto">
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-0 space-y-1 custom-scrollbar scroll-smooth" id="sidebar-nav">
        
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}#overview" class="sidebar-link group relative flex items-center px-8 py-3.5 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="overview">
            <svg class="w-5 h-5 mr-3 transition-colors duration-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span class="tracking-wide">Tableau de Bord</span>
            <div class="absolute inset-y-0 right-0 w-1 bg-[#cb2d2d] shadow-[0_0_8px_rgba(203,45,45,0.6)] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </a>

        <!-- Section: GESTION -->
        @if(App\Helpers\PermissionHelper::can('biens.view'))
        <div class="mt-8 mb-2 px-8 flex justify-between items-center group cursor-pointer" 
             role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') toggleSection('gestion')"
             onclick="toggleSection('gestion')">
            <h3 class="text-xs font-bold text-[#cb2d2d] uppercase tracking-[0.15em] transition-colors group-hover:text-white">Gestion</h3>
            <svg id="icon-gestion" class="w-3 h-3 text-gray-500 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-gestion" class="space-y-0.5 overflow-hidden transition-all duration-300 origin-top">
            <a href="{{ route('dashboard') }}#proprietaires" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="proprietaires">
               <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
               Propriétaires
            </a>
            <a href="{{ route('dashboard') }}#biens" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="biens">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Biens Immobiliers
            </a>
             <a href="{{ route('dashboard') }}#locataires" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="locataires">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Locataires
            </a>
            <a href="{{ route('dashboard') }}#contrats" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="contrats">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contrats
            </a>
        </div>
        @endif

        <!-- Section: FINANCE -->
        @if(App\Helpers\PermissionHelper::can('loyers.view') || App\Helpers\PermissionHelper::can('paiements.view'))
        <div class="mt-8 mb-2 px-8 flex justify-between items-center group cursor-pointer" 
             role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') toggleSection('finance')"
             onclick="toggleSection('finance')">
            <h3 class="text-xs font-bold text-[#cb2d2d] uppercase tracking-[0.15em] transition-colors group-hover:text-white">Finance</h3>
            <svg id="icon-finance" class="w-3 h-3 text-gray-500 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-finance" class="space-y-0.5 overflow-hidden transition-all duration-300 origin-top">
            @if(App\Helpers\PermissionHelper::can('loyers.view'))
            <a href="{{ route('dashboard') }}#loyers" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="loyers">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Appels de Loyers
            </a>
            @endif
            @if(App\Helpers\PermissionHelper::can('paiements.view'))
            <a href="{{ route('dashboard') }}#paiements" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="paiements">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Comptabilité
            </a>
            @endif
            @if(App\Helpers\PermissionHelper::can('loyers.view'))
            <a href="{{ route('dashboard') }}#relances" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="relances">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Relances & Relais
            </a>
            @endif
            @if(App\Helpers\PermissionHelper::can('depenses.view'))
             <a href="{{ route('dashboard') }}#depenses" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="depenses">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Dépenses
            </a>
            @endif
        </div>
        @endif

        <!-- Section: RAPPORTS -->
        <div class="mt-8 mb-2 px-8 flex justify-between items-center group cursor-pointer" 
             role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') toggleSection('rapports')"
             onclick="toggleSection('rapports')">
            <h3 class="text-xs font-bold text-[#cb2d2d] uppercase tracking-[0.15em] transition-colors group-hover:text-white">Rapports</h3>
             <svg id="icon-rapports" class="w-3 h-3 text-gray-500 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-rapports" class="space-y-0.5 overflow-hidden transition-all duration-300 origin-top">
            <a href="{{ route('rapports.loyers') }}" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Rapport Loyers
            </a>
            <a href="{{ route('rapports.impayees') }}" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Impayés & Retards
            </a>
            <a href="{{ route('rapports.commissions') }}" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Commissions Agence
            </a>
        </div>

        <!-- Section: ADMIN -->
        @if(Auth::user()->role === 'admin')
        <div class="mt-8 mb-2 px-8 flex justify-between items-center group cursor-pointer" 
             role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') toggleSection('admin')"
             onclick="toggleSection('admin')">
            <h3 class="text-xs font-bold text-[#cb2d2d] uppercase tracking-[0.15em] transition-colors group-hover:text-white">Admin</h3>
             <svg id="icon-admin" class="w-3 h-3 text-gray-500 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        <div id="section-admin" class="space-y-0.5 overflow-hidden transition-all duration-300 origin-top">
            <a href="{{ route('dashboard') }}#utilisateurs" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="utilisateurs">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Utilisateurs
            </a>
            <a href="{{ route('dashboard') }}#logs" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="logs">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Logs
            </a>
            <a href="{{ route('dashboard') }}#parametres" class="sidebar-link flex items-center px-8 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 border-l-4 border-transparent" data-target="parametres">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Configuration
            </a>
        </div>
        @endif
    </nav>

    <!-- User Profile Strip -->
    <div class="p-4 bg-[#142430] border-t border-[#263a4d]">
        <div class="flex items-center gap-3">
             <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 group no-underline">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-[#cb2d2d] flex items-center justify-center font-bold text-white shadow-lg ring-2 ring-[#263a4d] group-hover:ring-[#cb2d2d] transition-all duration-300">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#142430] rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate group-hover:text-[#cb2d2d] transition-colors">{{ Auth::user()->name ?? 'Utilisateur' }}</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold truncate">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-white hover:bg-[#cb2d2d] rounded-lg transition-all shadow-sm" title="Déconnexion">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    // Toggle Collapse Sections
    function toggleSection(sectionId) {
        const section = document.getElementById('section-' + sectionId);
        const icon = document.getElementById('icon-' + sectionId);
        
        if (section.style.maxHeight && section.style.maxHeight !== '0px') {
            // Close
            section.style.maxHeight = '0px';
            section.classList.remove('py-2'); // Remove padding when closed to be cleaner
            if(icon) {
                 icon.classList.remove('rotate-180');
                 icon.classList.add('rotate-0');
            }
        } else {
            // Open
            section.classList.add('py-2'); // Add subtle padding when open
            // Calculate height including py-2
            section.style.maxHeight = (section.scrollHeight + 16) + "px"; 
            if(icon) {
                icon.classList.add('rotate-180');
                icon.classList.remove('rotate-0');
            }
        }
    }

    // Active State Logic
    document.addEventListener('DOMContentLoaded', () => {
        // Init Sections
        ['gestion', 'finance', 'admin', 'rapports'].forEach(id => {
            const section = document.getElementById('section-' + id);
            if (section) {
                section.style.maxHeight = (section.scrollHeight + 16) + "px";
                section.classList.add('py-2');
            }
        });

        // Function to set active link based on Hash
        const setActiveLink = () => {
            const currentHash = window.location.hash.substring(1) || 'overview'; // Default to overview
            
            // Remove active classes from all links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                // Inactive State
                link.classList.remove('text-white', 'bg-gradient-to-r', 'from-[#cb2d2d]/20', 'to-transparent', 'border-[#cb2d2d]');
                link.classList.add('text-gray-400', 'border-transparent');
                
                // Icon opacity
                const svg = link.querySelector('svg');
                if(svg) svg.classList.add('opacity-70');
            });

            // Find target link
            // Search by data-target
            let targetLink = document.querySelector(`.sidebar-link[data-target="${currentHash}"]`);
            
            // Fallback for sub-pages or precise matches if needed
            if (!targetLink) {
                // Try to match href contains hash
                targetLink = document.querySelector(`.sidebar-link[href*="#${currentHash}"]`);
            }

            if (targetLink) {
                // Active State
                targetLink.classList.remove('text-gray-400', 'border-transparent');
                targetLink.classList.add('text-white', 'bg-gradient-to-r', 'from-[#cb2d2d]/20', 'to-transparent', 'border-[#cb2d2d]');
                
                // Icon Opacity
                const svg = targetLink.querySelector('svg');
                if(svg) svg.classList.remove('opacity-70');

                // Ensure parent section is open
                const parentSection = targetLink.closest('[id^="section-"]');
                if(parentSection) {
                    const sectionId = parentSection.id.replace('section-', '');
                    // Ensure it is open (it should be init open, but just in case)
                     const icon = document.getElementById('icon-' + sectionId);
                     if(parentSection.style.maxHeight === '0px') {
                         parentSection.style.maxHeight = (parentSection.scrollHeight + 16) + "px";
                         parentSection.classList.add('py-2');
                         if(icon) {
                             icon.classList.add('rotate-180');
                             icon.classList.remove('rotate-0');
                         }
                     }
                }
            }
        };

        // Listen for hash changes
        window.addEventListener('hashchange', setActiveLink);
        // Set on load
        setActiveLink();
    });
</script>

<style>
    /* Custom Scrollbar for Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #263a4d; 
        border-radius: 2px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: #cb2d2d;
    }
</style>
