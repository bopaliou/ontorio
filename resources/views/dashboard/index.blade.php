<x-app-layout>
    <div id="dashboard-container" class="relative">
        
        <!-- SECTION: OVERVIEW (Default) -->
        <div id="section-overview" class="section-pane transition-opacity duration-300 ease-in-out">
            {{-- On affiche le dashboard spécifique au rôle si disponible, sinon le générique --}}
            @if(isset($data['role']))
                @if($data['role'] === 'comptable')
                    @include('dashboard.roles.comptable', ['data' => $data])
                @elseif($data['role'] === 'direction')
                    @include('dashboard.roles.direction', ['data' => $data])
                @else
                    {{-- Par défaut on utilise la section Overview générique ou Gestionnaire --}}
                    @include('dashboard.sections.overview', ['data' => $data])
                @endif
            @else
                @include('dashboard.sections.overview', ['data' => $data])
            @endif
        </div>

        <!-- SECTION: BIENS -->
        <div id="section-biens" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.biens')
        </div>

        <!-- SECTION: ONTARIO GROUP (Propriétaires) -->
        <div id="section-proprietaires" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.proprietaires')
        </div>

        <!-- SECTION: LOCATAIRES -->
        <div id="section-locataires" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.locataires')
        </div>

        <!-- SECTION: CONTRATS -->
        <div id="section-contrats" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.contrats')
        </div>

        <!-- SECTION: LOYERS (Finance) -->
        <div id="section-loyers" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.loyers')
        </div>

        <!-- SECTION: PAIEMENTS (Compta) -->
        <div id="section-paiements" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.paiements')
        </div>

        <!-- SECTION: DEPENSES (Management) -->
        <div id="section-depenses" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.depenses')
        </div>

        <!-- SECTION: ADMINISTRATION -->
        @if(App\Helpers\PermissionHelper::can('users.view'))
        <div id="section-utilisateurs" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.utilisateurs')
        </div>
        <div id="section-logs" class="section-pane hidden opacity-0 transition-opacity duration-300 ease-in-out">
            @include('dashboard.sections.logs')
        </div>
        @endif

    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        /**
         * ONTARIO GROUP - DASHBOARD SPA ENGINE
         * Gestionnaire de navigation fluide avec History API
         */
        class OntarioDashboard {
            constructor() {
                this.container = document.getElementById('dashboard-container');
                this.loader = document.getElementById('page-loader-bar');
                this.currentSection = null;
                this.isNavigating = false;

                this.init();
            }

            init() {
                // Intercepter les clics sur les liens de navigation
                document.querySelectorAll('[data-show-section]').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const sectionId = link.getAttribute('data-show-section');
                        this.show(sectionId);
                    });
                });

                // Gérer les boutons Précédent/Suivant du navigateur
                window.addEventListener('popstate', (e) => {
                    const sectionId = e.state?.sectionId || this.getSectionFromHash() || 'overview';
                    this.show(sectionId, false); // false = don't push state again
                });

                // Chargement initial
                const initialSection = this.getSectionFromHash() || 'overview';
                this.show(initialSection, false);
            }

            getSectionFromHash() {
                return window.location.hash.substring(1);
            }

            /**
             * Affiche physiquement la section
             * @param {string} sectionId - L'ID de la section à afficher
             * @param {boolean} updateHistory - Si vrai (par défaut), met à jour l'historique du navigateur
             */
            show(sectionId, updateHistory = true) {
                const target = document.getElementById('section-' + sectionId);
                if (!target) {
                    if (sectionId !== 'overview') this.show('overview', updateHistory);
                    return;
                }

                if (this.currentSection === sectionId && !updateHistory) return;
                this.isNavigating = true;

                // 1. Mettre à jour l'état et l'historique
                if (updateHistory) {
                    history.pushState({ sectionId }, '', `#${sectionId}`);
                }
                this.currentSection = sectionId;

                // 2. Loader Effect
                this.loader.classList.add('loading');
                this.loader.style.opacity = '1';

                // 3. Sidebar Sync
                this.updateSidebarUI(sectionId);

                // 4. Transition des panneaux
                const activePanes = document.querySelectorAll('.section-pane:not(.hidden)');
                activePanes.forEach(pane => {
                    if (pane.id !== 'section-' + sectionId) {
                        pane.classList.add('opacity-0');
                        setTimeout(() => pane.classList.add('hidden'), 400);
                    }
                });

                // Attendre la fin du masquage de l'ancien panneau pour afficher le nouveau
                setTimeout(() => {
                    target.classList.remove('hidden');
                    
                    requestAnimationFrame(() => {
                        target.classList.remove('opacity-0');
                        
                        // Terminer le loader
                        setTimeout(() => {
                            this.loader.classList.remove('loading');
                            this.loader.style.opacity = '0';
                            this.isNavigating = false;
                        }, 400);
                    });
                }, activePanes.length > 0 ? 100 : 0);
            }

            updateSidebarUI(sectionId) {
                document.querySelectorAll('.sidebar-nav-link').forEach(link => {
                    const isTarget = link.getAttribute('data-show-section') === sectionId;
                    
                    if (isTarget) {
                        link.classList.remove('text-gray-400', 'hover:text-white', 'hover:bg-[#1e3342]');
                        link.classList.add('bg-[#cb2d2d]', 'text-white', 'shadow-md');
                        // Icône couleur
                        const svg = link.querySelector('svg');
                        if (svg) svg.classList.replace('text-gray-500', 'text-white');
                    } else {
                        link.classList.remove('bg-[#cb2d2d]', 'text-white', 'shadow-md');
                        link.classList.add('text-gray-400', 'hover:text-white', 'hover:bg-[#1e3342]');
                        const svg = link.querySelector('svg');
                        if (svg) svg.classList.replace('text-white', 'text-gray-500');
                    }
                });
            }
        }

        // Initialisation globale
        document.addEventListener('DOMContentLoaded', () => {
            window.dashboard = new OntarioDashboard();
        });
    </script>
</x-app-layout>
