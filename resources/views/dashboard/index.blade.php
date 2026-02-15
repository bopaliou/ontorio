<x-app-layout>
    <div id="dashboard-container" class="relative">

        <!-- SECTION: OVERVIEW (Default) -->
        <div id="section-overview" role="tabpanel" aria-labelledby="nav-link-overview" class="section-pane">
            <div class="section-skeleton h-full">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex-1"><x-skeleton variant="rect" height="h-20" width="w-3/4" /></div>
                        <div class="flex gap-3"><x-skeleton variant="rect" height="h-12" width="w-40" /><x-skeleton variant="rect" height="h-12" width="w-40" /></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <x-skeleton variant="rect" height="h-32" />
                        <x-skeleton variant="rect" height="h-32" />
                        <x-skeleton variant="rect" height="h-32" />
                        <x-skeleton variant="rect" height="h-32" />
                    </div>
                </div>
            </div>
            <div class="section-content hidden">
                {{-- On affiche le dashboard spécifique au rôle si disponible, sinon le générique --}}
                @if(isset($data['role']))
                    @if($data['role'] === 'comptable')
                        @include('dashboard.roles.comptable', ['data' => $data])
                    @elseif($data['role'] === 'direction')
                        @include('dashboard.roles.direction', ['data' => $data])
                    @elseif($data['role'] === 'proprietaire')
                        @include('dashboard.roles.proprietaire', ['data' => $data])
                    @else
                        {{-- Par défaut on utilise la section Overview générique ou Gestionnaire --}}
                        @include('dashboard.sections.overview', ['data' => $data])
                    @endif
                @else
                    @include('dashboard.sections.overview', ['data' => $data])
                @endif
            </div>
        </div>

        <!-- SECTION: BIENS -->
        <div id="section-biens" role="tabpanel" aria-labelledby="nav-link-biens" class="section-pane hidden">
            <div class="section-skeleton h-full">
                <div class="flex flex-col gap-6">
                   <div class="flex justify-between items-center"><x-skeleton variant="rect" height="h-16" width="w-1/3" /><x-skeleton variant="rect" height="h-12" width="w-48" /></div>
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-6"><x-skeleton variant="rect" height="h-64" /><x-skeleton variant="rect" height="h-64" /><x-skeleton variant="rect" height="h-64" /></div>
                </div>
            </div>
            <div class="section-content hidden">
                @include('dashboard.sections.biens')
            </div>
        </div>

        <!-- SECTION: ONTARIO GROUP (Propriétaires) -->
        <div id="section-proprietaires" role="tabpanel" aria-labelledby="nav-link-proprietaires" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-proprietaires start\n", FILE_APPEND); ?>
                @include('dashboard.sections.proprietaires')
                <?php file_put_contents('debug_test.log', "VIEW: section-proprietaires done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: LOCATAIRES -->
        <div id="section-locataires" role="tabpanel" aria-labelledby="nav-link-locataires" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                @include('dashboard.sections.locataires')
            </div>
        </div>

        <!-- SECTION: CONTRATS -->
        <div id="section-contrats" role="tabpanel" aria-labelledby="nav-link-contrats" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-contrats start\n", FILE_APPEND); ?>
                @include('dashboard.sections.contrats')
                <?php file_put_contents('debug_test.log', "VIEW: section-contrats done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: LOYERS (Finance) -->
        <div id="section-loyers" role="tabpanel" aria-labelledby="nav-link-loyers" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-loyers start\n", FILE_APPEND); ?>
                @include('dashboard.sections.loyers')
                <?php file_put_contents('debug_test.log', "VIEW: section-loyers done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: PAIEMENTS (Compta) -->
        <div id="section-paiements" role="tabpanel" aria-labelledby="nav-link-paiements" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-paiements start\n", FILE_APPEND); ?>
                @include('dashboard.sections.paiements')
                <?php file_put_contents('debug_test.log', "VIEW: section-paiements done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: DEPENSES (Management) -->
        <div id="section-depenses" role="tabpanel" aria-labelledby="nav-link-depenses" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-depenses start\n", FILE_APPEND); ?>
                @include('dashboard.sections.depenses')
                <?php file_put_contents('debug_test.log', "VIEW: section-depenses done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: RELANCES (Communication) -->
        <div id="section-relances" role="tabpanel" aria-labelledby="nav-link-relances" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-relances start\n", FILE_APPEND); ?>
                @include('dashboard.sections.relances')
                <?php file_put_contents('debug_test.log', "VIEW: section-relances done\n", FILE_APPEND); ?>
            </div>
        </div>

        <!-- SECTION: ADMINISTRATION -->
        @if(Auth::user()->role === 'admin')
        <div id="section-utilisateurs" role="tabpanel" aria-labelledby="nav-link-utilisateurs" class="section-pane hidden">
            <div class="section-skeleton"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-utilisateurs start\n", FILE_APPEND); ?>
                @include('dashboard.sections.utilisateurs')
                <?php file_put_contents('debug_test.log', "VIEW: section-utilisateurs done\n", FILE_APPEND); ?>
            </div>
        </div>
        <div id="section-logs" role="tabpanel" aria-labelledby="nav-link-logs" class="section-pane hidden">
            <div class="section-skeleton"> ... </div>
            <div class="section-content hidden">
                @include('dashboard.sections.logs')
            </div>
        </div>
        @endif

        <!-- SECTION: PARAMÈTRES -->
        <div id="section-parametres" role="tabpanel" aria-labelledby="nav-link-parametres" class="section-pane hidden">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden">
                <?php file_put_contents('debug_test.log', "VIEW: section-parametres start\n", FILE_APPEND); ?>
                @include('dashboard.sections.parametres')
                <?php file_put_contents('debug_test.log', "VIEW: section-parametres done\n", FILE_APPEND); ?>
            </div>
        </div>

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
                // Singleton access
                window.dashboard = this;

                // Intercepter les clics sur les liens de navigation (Dashboard & Sidebar)
                document.querySelectorAll('[data-show-section], [data-target]').forEach(link => {
                    link.addEventListener('click', (e) => {
                        const sectionId = link.getAttribute('data-show-section') || link.getAttribute('data-target');
                        if (!sectionId) return;

                        e.preventDefault();
                        this.show(sectionId);

                        // Fermer la sidebar sur mobile si elle est ouverte
                        const sidebar = document.getElementById('main-sidebar');
                        if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                            window.toggleSidebar();
                        }
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

                // Forcer le skeleton sur l'overview au premier chargement si c'est la section active
                if (initialSection === 'overview') {
                    const overview = document.getElementById('section-overview');
                    if (overview) {
                        overview.classList.remove('hidden');
                        overview.classList.remove('opacity-0');
                    }
                }
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

                // 2. Loader Bar Effect
                this.loader.style.width = '30%';
                this.loader.style.opacity = '1';

                // 3. Sidebar Sync
                this.updateSidebarUI(sectionId);

                // 4. Transition des panneaux
                const activePanes = document.querySelectorAll('.section-pane:not(.hidden)');
                activePanes.forEach(pane => {
                    if (pane.id !== 'section-' + sectionId) {
                        pane.classList.add('opacity-0', 'translate-y-4'); // Reset position for exit
                        setTimeout(() => pane.classList.add('hidden'), 500);
                    }
                });

                // 5. Gestion spécifique du contenu (Skeleton -> Real Content)
                const skeleton = target.querySelector('.section-skeleton');
                const content = target.querySelector('.section-content');

                if (skeleton && content) {
                    skeleton.classList.remove('hidden');
                    content.classList.add('hidden');
                }

                // Attendre un peu pour donner un effet de chargement premium
                setTimeout(() => {
                    target.classList.remove('hidden');
                    this.loader.style.width = '70%';

                    requestAnimationFrame(() => {
                        target.classList.remove('opacity-0', 'translate-y-4'); // Ensure clean stacking context

                        // Simulation d'un chargement de données (0.8s) pour apprécier le skeleton
                        setTimeout(() => {
                            if (skeleton && content) {
                                skeleton.classList.add('hidden');
                                content.classList.remove('hidden');
                            }

                            this.loader.style.width = '100%';

                            // Terminer le loader avec un léger délai pour stabiliser le rendu
                            setTimeout(() => {
                                this.loader.style.opacity = '0';
                                setTimeout(() => this.loader.style.width = '0', 400);
                                this.isNavigating = false;
                            }, 500);
                        }, 800);
                    });
                }, activePanes.length > 0 ? 150 : 0);
            }

            updateSidebarUI(sectionId) {
                document.querySelectorAll('.sidebar-link').forEach(link => {
                    const isTarget = link.getAttribute('data-target') === sectionId || link.getAttribute('data-show-section') === sectionId;
                    link.setAttribute('aria-selected', isTarget ? 'true' : 'false');

                    if (isTarget) {
                        link.classList.remove('text-gray-400', 'hover:text-white', 'hover:bg-white/5');
                        link.classList.add('text-white', 'bg-gradient-to-r', 'from-[#cb2d2d]/20', 'to-transparent', 'border-[#cb2d2d]');
                        // Icône couleur
                        const svg = link.querySelector('svg');
                        if (svg) svg.classList.remove('opacity-70');
                    } else {
                        link.classList.add('text-gray-400', 'hover:text-white', 'hover:bg-white/5');
                        link.classList.remove('text-white', 'bg-gradient-to-r', 'from-[#cb2d2d]/20', 'to-transparent', 'border-[#cb2d2d]');
                        const svg = link.querySelector('svg');
                        if (svg) svg.classList.add('opacity-70');
                    }
                });
            }

            /**
             * Recharge la section actuelle en récupérant les nouvelles données du serveur
             */
            async refresh() {
                if (!this.currentSection) return;

                // 1. Afficher le skeleton et le loader
                this.loader.style.width = '20%';
                this.loader.style.opacity = '1';

                const target = document.getElementById('section-' + this.currentSection);
                const skeleton = target.querySelector('.section-skeleton');
                const content = target.querySelector('.section-content');

                if (skeleton && content) {
                    skeleton.classList.remove('hidden');
                    content.classList.add('hidden');
                }

                try {
                    // 2. Récupérer la page complète en tâche de fond (Cache-busting enabled)
                    const url = new URL(window.location.href);
                    url.searchParams.set('t', Date.now());
                    const response = await fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await response.text();

                    // 3. Parser et extraire le nouveau contenu
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#section-' + this.currentSection + ' .section-content');

                    if (newContent && content) {
                        // Utiliser createContextualFragment pour forcer l'exécution des scripts <script>
                        const range = document.createRange();
                        range.selectNode(content);
                        const fragment = range.createContextualFragment(newContent.innerHTML);

                        content.innerHTML = '';
                        content.appendChild(fragment);
                    }

                    this.loader.style.width = '100%';

                    // 4. Masquer le skeleton et afficher le contenu frais
                    setTimeout(() => {
                        if (skeleton && content) {
                            skeleton.classList.add('hidden');
                            content.classList.remove('hidden');
                        }
                        this.loader.style.opacity = '0';
                        setTimeout(() => this.loader.style.width = '0', 400);
                    }, 500);

                } catch (error) {
                    console.error('Erreur lors du rafraîchissement:', error);
                    window.location.reload(); // Fallback
                }
            }
        }

        // Initialisation globale
        document.addEventListener('DOMContentLoaded', () => {
            window.dashboard = new OntarioDashboard();
        });
    </script>
</x-app-layout>
