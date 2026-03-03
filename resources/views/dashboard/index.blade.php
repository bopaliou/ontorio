<x-app-layout>
    <div id="dashboard-container" class="relative">

        <!-- SECTION: OVERVIEW (Default — Server-Side Rendered) -->
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
                {{-- Overview is always server-rendered --}}
                @if(isset($data['role']))
                    @if($data['role'] === 'comptable')
                        @include('dashboard.roles.comptable', ['data' => $data])
                    @elseif($data['role'] === 'direction')
                        @include('dashboard.roles.direction', ['data' => $data])
                    @elseif($data['role'] === 'proprietaire')
                        @include('dashboard.roles.proprietaire', ['data' => $data])
                    @else
                        @include('dashboard.sections.overview', ['data' => $data])
                    @endif
                @else
                    @include('dashboard.sections.overview', ['data' => $data])
                @endif
            </div>
        </div>

        <!-- SECTION: BIENS (Lazy Loaded) -->
        <div id="section-biens" role="tabpanel" aria-labelledby="nav-link-biens" class="section-pane hidden" data-lazy="true" data-section="biens">
            <div class="section-skeleton h-full">
                <div class="flex flex-col gap-6">
                   <div class="flex justify-between items-center"><x-skeleton variant="rect" height="h-16" width="w-1/3" /><x-skeleton variant="rect" height="h-12" width="w-48" /></div>
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-6"><x-skeleton variant="rect" height="h-64" /><x-skeleton variant="rect" height="h-64" /><x-skeleton variant="rect" height="h-64" /></div>
                </div>
            </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: ONTARIO GROUP (Propriétaires — Lazy) -->
        <div id="section-proprietaires" role="tabpanel" aria-labelledby="nav-link-proprietaires" class="section-pane hidden" data-lazy="true" data-section="proprietaires">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: LOCATAIRES (Lazy) -->
        <div id="section-locataires" role="tabpanel" aria-labelledby="nav-link-locataires" class="section-pane hidden" data-lazy="true" data-section="locataires">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: CONTRATS (Lazy) -->
        <div id="section-contrats" role="tabpanel" aria-labelledby="nav-link-contrats" class="section-pane hidden" data-lazy="true" data-section="contrats">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: LOYERS (Finance — Lazy) -->
        <div id="section-loyers" role="tabpanel" aria-labelledby="nav-link-loyers" class="section-pane hidden" data-lazy="true" data-section="loyers">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: PAIEMENTS (Compta — Lazy) -->
        <div id="section-paiements" role="tabpanel" aria-labelledby="nav-link-paiements" class="section-pane hidden" data-lazy="true" data-section="paiements">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: DEPENSES (Management — Lazy) -->
        <div id="section-depenses" role="tabpanel" aria-labelledby="nav-link-depenses" class="section-pane hidden" data-lazy="true" data-section="depenses">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: RELANCES (Communication — Lazy) -->
        <div id="section-relances" role="tabpanel" aria-labelledby="nav-link-relances" class="section-pane hidden" data-lazy="true" data-section="relances">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

        <!-- SECTION: ADMINISTRATION (Lazy, Admin-only) -->
        @if(Auth::user()->role === 'admin')
        <div id="section-utilisateurs" role="tabpanel" aria-labelledby="nav-link-utilisateurs" class="section-pane hidden" data-lazy="true" data-section="utilisateurs">
            <div class="section-skeleton"> ... </div>
            <div class="section-content hidden"></div>
        </div>
        <div id="section-logs" role="tabpanel" aria-labelledby="nav-link-logs" class="section-pane hidden" data-lazy="true" data-section="logs">
            <div class="section-skeleton"> ... </div>
            <div class="section-content hidden"></div>
        </div>
        @endif

        <!-- SECTION: PARAMÈTRES (Lazy) -->
        <div id="section-parametres" role="tabpanel" aria-labelledby="nav-link-parametres" class="section-pane hidden" data-lazy="true" data-section="parametres">
            <div class="section-skeleton h-full"> ... </div>
            <div class="section-content hidden"></div>
        </div>

    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        /**
         * ONTARIO GROUP - DASHBOARD SPA ENGINE v2
         * Lazy-loading navigation with History API
         */
        class OntarioDashboard {
            constructor() {
                this.container = document.getElementById('dashboard-container');
                this.loader = document.getElementById('page-loader-bar');
                this.currentSection = null;
                this.isNavigating = false;
                this.loadedSections = new Set(); // Track which sections have been fetched
                this.loadingPromises = {};       // Prevent duplicate fetches

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

                // Overview is always SSR — mark as loaded
                this.loadedSections.add('overview');

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
            async show(sectionId, updateHistory = true) {
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

                // 1b. Update topbar breadcrumb
                if (typeof window.updateBreadcrumb === 'function') {
                    window.updateBreadcrumb(sectionId);
                }

                // 2. Loader Bar Effect
                this.loader.style.width = '30%';
                this.loader.style.opacity = '1';

                // 3. Sidebar Sync
                this.updateSidebarUI(sectionId);

                // 4. Transition des panneaux
                const activePanes = document.querySelectorAll('.section-pane:not(.hidden)');
                activePanes.forEach(pane => {
                    if (pane.id !== 'section-' + sectionId) {
                        pane.classList.add('opacity-0', 'translate-y-4');
                        setTimeout(() => pane.classList.add('hidden'), 500);
                    }
                });

                // 5. Show target pane with skeleton
                const skeleton = target.querySelector('.section-skeleton');
                const content = target.querySelector('.section-content');
                const isLazy = target.hasAttribute('data-lazy');
                const isLoaded = this.loadedSections.has(sectionId);

                if (skeleton && content) {
                    if (!isLoaded) {
                        skeleton.classList.remove('hidden');
                        content.classList.add('hidden');
                    }
                }

                // 6. Show target panel
                setTimeout(async () => {
                    target.classList.remove('hidden');
                    this.loader.style.width = '70%';

                    requestAnimationFrame(async () => {
                        target.classList.remove('opacity-0', 'translate-y-4');

                        // 7. Lazy Loading: fetch section content if needed
                        if (isLazy && !isLoaded) {
                            await this.fetchSection(sectionId, target, skeleton, content);
                        } else {
                            // Already loaded — just reveal
                            if (skeleton && content) {
                                skeleton.classList.add('hidden');
                                content.classList.remove('hidden');
                            }
                        }

                        this.loader.style.width = '100%';

                        // 8. Terminer le loader
                        setTimeout(() => {
                            this.loader.style.opacity = '0';
                            setTimeout(() => this.loader.style.width = '0', 400);
                            this.isNavigating = false;
                        }, 300);
                    });
                }, activePanes.length > 0 ? 150 : 0);
            }

            /**
             * Fetch section HTML from server and inject into the DOM
             */
            async fetchSection(sectionId, target, skeleton, content) {
                // Prevent duplicate parallel fetches for the same section
                if (this.loadingPromises[sectionId]) {
                    return this.loadingPromises[sectionId];
                }

                const sectionName = target.getAttribute('data-section') || sectionId;
                const url = `/dashboard/section/${sectionName}`;

                this.loadingPromises[sectionId] = (async () => {
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'text/html',
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const html = await response.text();

                        // Inject using createContextualFragment to execute inline <script> tags
                        const range = document.createRange();
                        range.selectNode(content);
                        const fragment = range.createContextualFragment(html);

                        content.innerHTML = '';
                        content.appendChild(fragment);

                        // Mark as loaded
                        this.loadedSections.add(sectionId);

                        // Transition: hide skeleton, show content
                        if (skeleton) skeleton.classList.add('hidden');
                        content.classList.remove('hidden');

                    } catch (error) {
                        console.error(`[Dashboard] Erreur chargement section "${sectionId}":`, error);

                        // Show error state in skeleton area
                        if (skeleton) {
                            skeleton.innerHTML = `
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Erreur de chargement</h3>
                                    <p class="text-sm text-gray-500 mb-6">Impossible de charger cette section. Vérifiez votre connexion.</p>
                                    <button onclick="dashboard.retrySection('${sectionId}')" class="px-6 py-2.5 bg-[#cb2d2d] text-white font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-[#a82020] transition shadow-lg">
                                        Réessayer
                                    </button>
                                </div>
                            `;
                        }
                    } finally {
                        delete this.loadingPromises[sectionId];
                    }
                })();

                return this.loadingPromises[sectionId];
            }

            /**
             * Retry loading a failed section
             */
            retrySection(sectionId) {
                this.loadedSections.delete(sectionId);
                const target = document.getElementById('section-' + sectionId);
                if (!target) return;

                const skeleton = target.querySelector('.section-skeleton');
                const content = target.querySelector('.section-content');

                // Reset skeleton to loading state
                if (skeleton) {
                    skeleton.innerHTML = '<div class="flex items-center justify-center py-16"><div class="animate-spin w-8 h-8 border-4 border-[#cb2d2d] border-t-transparent rounded-full"></div></div>';
                    skeleton.classList.remove('hidden');
                }
                if (content) content.classList.add('hidden');

                this.fetchSection(sectionId, target, skeleton, content);
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

                const sectionId = this.currentSection;
                const target = document.getElementById('section-' + sectionId);
                if (!target) return;

                const skeleton = target.querySelector('.section-skeleton');
                const content = target.querySelector('.section-content');
                const isLazy = target.hasAttribute('data-lazy');

                // 1. Afficher le skeleton et le loader
                this.loader.style.width = '20%';
                this.loader.style.opacity = '1';

                if (skeleton && content) {
                    skeleton.innerHTML = '<div class="flex items-center justify-center py-16"><div class="animate-spin w-8 h-8 border-4 border-[#cb2d2d] border-t-transparent rounded-full"></div></div>';
                    skeleton.classList.remove('hidden');
                    content.classList.add('hidden');
                }

                // 2. Force re-fetch
                this.loadedSections.delete(sectionId);

                if (isLazy) {
                    // For lazy sections, use the dedicated section endpoint
                    await this.fetchSection(sectionId, target, skeleton, content);
                } else {
                    // For overview (SSR), fetch the entire page and extract
                    try {
                        const url = new URL(window.location.href);
                        url.searchParams.set('t', Date.now());
                        const response = await fetch(url.toString(), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const html = await response.text();

                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('#section-' + sectionId + ' .section-content');

                        if (newContent && content) {
                            const range = document.createRange();
                            range.selectNode(content);
                            const fragment = range.createContextualFragment(newContent.innerHTML);

                            content.innerHTML = '';
                            content.appendChild(fragment);
                        }

                        this.loadedSections.add(sectionId);

                        if (skeleton) skeleton.classList.add('hidden');
                        if (content) content.classList.remove('hidden');

                    } catch (error) {
                        console.error('Erreur lors du rafraîchissement:', error);
                        window.location.reload();
                    }
                }

                this.loader.style.width = '100%';
                setTimeout(() => {
                    this.loader.style.opacity = '0';
                    setTimeout(() => this.loader.style.width = '0', 400);
                }, 300);
            }
        }

        // Initialisation globale
        document.addEventListener('DOMContentLoaded', () => {
            window.dashboard = new OntarioDashboard();
        });
    </script>
</x-app-layout>
