<title>{{ config('app.name', 'Ontario Group') }} - Dashboard</title>
<link rel="icon" type="image/png" href="{{ asset('images/ontorio-logo.png') }}">


<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    .main-shell {
        transition: margin-left 0.2s ease;
        margin-left: 0;
    }

    .sidebar-open .main-shell {
        margin-left: 16rem;
    }

    @media (max-width: 768px) {
        .sidebar-open .main-shell {
            margin-left: 0;
        }
    }
</style>
</head>
<body class="bg-slate-50 text-slate-900 sidebar-open">
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <div class="flex min-h-screen">
            <div id="sidebarBackdrop" class="fixed inset-0 z-20 hidden bg-black/40 md:hidden"></div>

            <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-[#1A365D] to-[#243B55] shadow-xl transition-transform duration-200 -translate-x-full md:translate-x-0">
                <!-- Logo -->
                <div class="flex h-32 items-center justify-center border-b border-white/10 px-3">
                    <div class="bg-white rounded-2xl p-3 shadow-2xl w-full">
                        <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="w-full h-auto">
                    </div>
                </div>

                @php
                    $nav = fn ($isActive) => $isActive ? 'sidebar-nav-link sidebar-nav-link-active' : 'sidebar-nav-link';
                @endphp

                <nav class="h-[calc(100vh-128px)] space-y-6 overflow-y-auto py-6">
                    <div>
                        <div class="mb-3 px-5 text-xs font-semibold uppercase tracking-wider text-blue-300/70">Navigation principale</div>
                        <button type="button" data-show-section="overview" class="sidebar-nav-link sidebar-nav-link-active w-full text-left">
                            <i class="bi bi-speedometer2 text-xl"></i>
                            <span class="font-medium">Dashboard</span>
                        </button>
                    </div>

                    @if(in_array(auth()->user()->role, ['admin', 'direction', 'gestionnaire']))
                    <div>
                        <div class="mb-3 px-5 text-xs font-semibold uppercase tracking-wider text-blue-300/70">Gestion</div>
                        <button type="button" data-show-section="proprietaires" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-people text-xl"></i>
                            <span class="font-medium">Propriétaires</span>
                        </button>
                        <button type="button" data-show-section="immeubles" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-buildings text-xl"></i>
                        <button type="button" data-show-section="logements" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-door-open text-xl"></i>
                            <span class="font-medium">Logements</span>
                        </button>
                        <button type="button" data-show-section="locataires" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-person-badge text-xl"></i>
                            <span class="font-medium">Locataires</span>
                        </button>
                        <button type="button" data-show-section="contrats" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-file-earmark-text text-xl"></i>
                            <span class="font-medium">Contrats</span>
                        </button>
                    </div>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'direction', 'gestionnaire', 'comptable']))
                    <div>
                        <div class="mb-3 px-5 text-xs font-semibold uppercase tracking-wider text-blue-300/70">Finance</div>
                        <button type="button" data-show-section="loyers" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-receipt text-xl"></i>
                            <span class="font-medium">Loyers</span>
                        </button>
                        <button type="button" data-show-section="paiements" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-cash-coin text-xl"></i>
                            <span class="font-medium">Paiements</span>
                        </button>
                    </div>
                    @endif

                    <div>
                        <div class="mb-3 px-5 text-xs font-semibold uppercase tracking-wider text-blue-300/70">Support</div>
                        <button type="button" data-show-section="support" class="sidebar-nav-link w-full text-left">
                            <i class="bi bi-question-circle text-xl"></i>
                            <span class="font-medium">Aide & Support</span>
                        </button>
                    </div>
                </nav>
            </aside>

            <div id="mainShell" class="main-shell flex flex-1 flex-col">
                <header class="sticky top-0 z-10 flex h-[70px] items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm hover:bg-slate-50">
                            <i class="bi bi-list text-xl"></i>
                        </button>
                        <div class="flex flex-col gap-1">
                            <h1 class="flex items-center gap-2 text-xl font-bold text-[var(--ontario-blue)]" id="pageTitle">
                                {!! $__env->yieldContent('page-icon', '<i class="bi bi-speedometer2"></i>') !!}
                                @yield('page-title', 'Dashboard')
                            </h1>
                            <ol id="breadcrumbList" class="flex items-center gap-2 text-sm text-slate-500">
                                <li><a href="{{ route('dashboard') }}" class="hover:text-slate-800">Dashboard</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative hidden sm:block">
                            <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Rechercher..." class="w-52 rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 focus:border-[var(--ontario-blue)] focus:outline-none focus:ring-2 focus:ring-[var(--ontario-blue)]/20">
                        </div>

                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-[var(--ontario-blue)] to-[var(--ontario-blue-light)] text-sm font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="leading-tight">
                                <div class="font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500">{{ auth()->user()->role }}</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn-ontario-outline">
                                <i class="bi bi-box-arrow-right"></i>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        function setSidebar(open) {
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                body.classList.add('sidebar-open');
                backdrop.classList.toggle('hidden', window.innerWidth >= 768);
            } else {
                sidebar.classList.add('-translate-x-full');
                body.classList.remove('sidebar-open');
                backdrop.classList.add('hidden');
            }
        }

        function isSidebarOpen() {
            return !sidebar.classList.contains('-translate-x-full');
        }

        sidebarToggle?.addEventListener('click', () => {
            setSidebar(!isSidebarOpen());
        });

        backdrop?.addEventListener('click', () => setSidebar(false));

        // État initial : ouvert sur desktop, fermé sur mobile
        function handleSidebarOnResize() {
            if (window.innerWidth < 768) {
                setSidebar(false);
            } else {
                setSidebar(true);
            }
        }
        window.addEventListener('resize', handleSidebarOnResize);
        handleSidebarOnResize();
    </script>
    <script>
        // Navigation sans rechargement complet (PJAX léger)
        const mainContent = document.getElementById('mainContent');
        const pageTitle = document.getElementById('pageTitle');
        const breadcrumbList = document.getElementById('breadcrumbList');

        async function loadPartial(url, push = true) {
            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newMain = doc.getElementById('mainContent');
                const newTitle = doc.getElementById('pageTitle');
                const newBreadcrumb = doc.getElementById('breadcrumbList');
                const newSidebar = doc.querySelector('aside');

                if (newMain && mainContent) {
                    mainContent.innerHTML = newMain.innerHTML;
                }
                if (newTitle && pageTitle) {
                    pageTitle.innerHTML = newTitle.innerHTML;
                }
                if (newBreadcrumb && breadcrumbList) {
                    breadcrumbList.innerHTML = newBreadcrumb.innerHTML;
                }

                // Mettre à jour les classes "active" de la sidebar
                if (newSidebar) {
                    const oldNavLinks = document.querySelectorAll('.sidebar-nav a');
                    const newNavLinks = newSidebar.querySelectorAll('.sidebar-nav a');
                    oldNavLinks.forEach(link => link.classList.remove('sidebar-nav-link-active'));
                    newNavLinks.forEach(link => {
                        if (link.classList.contains('sidebar-nav-link-active')) {
                            const oldLink = document.querySelector(`.sidebar-nav a[href="${link.getAttribute('href')}"]`);
                            if (oldLink) oldLink.classList.add('sidebar-nav-link-active');
                        }
                    });
                }

                if (push) {
                    history.pushState({}, '', url);
                }
            } catch (e) {
                console.error('Navigation AJAX échouée, rechargement classique', e);
                window.location.href = url;
            }
        }

        document.addEventListener('click', evt => {
            const link = evt.target.closest('.sidebar-nav a, #breadcrumbList a');
            if (!link) return;
            const url = link.getAttribute('href');
            if (!url || url.startsWith('#') || link.target === '_blank') return;
            evt.preventDefault();
                                <!-- Pas de JS Bootstrap -->
        });

        window.addEventListener('popstate', () => loadPartial(location.href, false));
    </script>
</body>
</html>
