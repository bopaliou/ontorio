<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20">
    <!-- Left: Breadcrumb / Title -->
    <div class="flex items-center gap-4">
        <button class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <nav class="hidden sm:flex text-sm font-medium text-gray-500 gap-2">
            <span data-show-section="overview" class="hover:text-gray-900 cursor-pointer">Dashboard</span>
            <span class="text-gray-300">/</span>
            <span class="text-secondary-900">Overview</span>
        </nav>
    </div>

    <!-- Right: Search & Actions -->
    <div class="flex items-center gap-4">
        <!-- Search Bar -->
        <div class="relative hidden md:block group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" 
                   class="block w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all" 
                   placeholder="Rechercher une propriété, un locataire...">
        </div>

        <!-- Notifications -->
        <button class="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition">
            <span class="absolute top-2 right-2 w-2 h-2 bg-brand-500 rounded-full border border-white"></span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </button>

        <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

        <!-- Role Badge -->
        <div class="hidden sm:flex flex-col items-end">
            <span class="text-xs font-bold text-secondary-900 uppercase tracking-wide">
                {{ ucfirst(Auth::user()->role ?? 'Gestionnaire') }}
            </span>
            <span class="text-[10px] text-gray-400">Ontario Group</span>
        </div>
    </div>
</header>
