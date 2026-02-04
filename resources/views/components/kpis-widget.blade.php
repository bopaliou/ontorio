{{-- Widget KPIs dynamiques avec refresh AJAX --}}
<div id="kpis-widget" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 animate-stagger">
    {{-- KPI 1: Loyers Facturés --}}
    <div class="kpi-card bg-white p-5 rounded-2xl shadow-sm border border-gray-200 ontario-card-lift group cursor-pointer"
         data-show-section="loyers" onclick="dashboard.show('loyers')">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Loyers Facturés</p>
                <p id="kpi-factures" class="text-2xl font-bold text-[#274256] mt-2">
                    <span class="animate-pulse bg-gray-200 rounded w-24 h-8 inline-block"></span>
                </p>
                <p class="text-xs text-gray-400 mt-1">Ce mois</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 2: Encaissé --}}
    <div class="kpi-card bg-white p-5 rounded-2xl shadow-sm border border-gray-200 border-l-4 border-l-green-500 ontario-card-lift group cursor-pointer"
         data-show-section="paiements" onclick="dashboard.show('paiements')">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Montant Encaissé</p>
                <p id="kpi-encaisses" class="text-2xl font-bold text-green-600 mt-2">
                    <span class="animate-pulse bg-gray-200 rounded w-24 h-8 inline-block"></span>
                </p>
                <p id="kpi-taux" class="text-xs text-green-600 mt-1 font-medium">--% recouvré</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
            <div id="kpi-progress" class="bg-green-500 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
    </div>

    {{-- KPI 3: Impayés --}}
    <div class="kpi-card bg-white p-5 rounded-2xl shadow-sm border border-gray-200 border-l-4 border-l-red-500 ontario-card-lift group cursor-pointer"
         data-show-section="loyers" onclick="dashboard.show('loyers')">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Arriérés Total</p>
                <p id="kpi-arrieres" class="text-2xl font-bold text-red-600 mt-2">
                    <span class="animate-pulse bg-gray-200 rounded w-24 h-8 inline-block"></span>
                </p>
                <p id="kpi-impayes-count" class="text-xs text-red-500 mt-1 font-medium">-- loyers impayés</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 4: Solde Net --}}
    <div class="kpi-card bg-gradient-to-br from-[#274256] to-[#1a2e3d] p-5 rounded-2xl shadow-lg text-white group hover:shadow-xl transition-all duration-300 cursor-pointer"
         data-show-section="depenses" onclick="dashboard.show('depenses')">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-300 uppercase tracking-wider">Solde Net</p>
                <p id="kpi-solde" class="text-2xl font-bold mt-2">
                    <span class="animate-pulse bg-white/20 rounded w-24 h-8 inline-block"></span>
                </p>
                <p class="text-xs text-gray-300 mt-1">Encaissements - Dépenses</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<script>
async function loadKPIs() {
    try {
        const response = await fetch('/api/stats/kpis');
        const data = await response.json();

        // Formateur
        const fmt = (n) => new Intl.NumberFormat('fr-FR').format(Math.round(n));

        // Mise à jour avec animation
        document.getElementById('kpi-factures').innerHTML = fmt(data.loyers_factures) + ' <span class="text-sm font-normal text-gray-400">F</span>';
        document.getElementById('kpi-encaisses').innerHTML = fmt(data.loyers_encaisses) + ' <span class="text-sm font-normal text-gray-400">F</span>';
        document.getElementById('kpi-arrieres').innerHTML = fmt(data.arrieres_total) + ' <span class="text-sm font-normal text-gray-400">F</span>';
        document.getElementById('kpi-solde').innerHTML = fmt(data.solde_net) + ' <span class="text-sm font-normal">F</span>';

        document.getElementById('kpi-taux').textContent = data.taux_recouvrement + '% recouvré';
        document.getElementById('kpi-progress').style.width = data.taux_recouvrement + '%';
        document.getElementById('kpi-impayes-count').textContent = data.nb_impayes + ' loyers impayés';

        // Colorer le solde
        const soldeEl = document.getElementById('kpi-solde');
        if (data.solde_net < 0) {
            soldeEl.classList.add('text-red-300');
        }

    } catch (error) {
        console.error('Erreur chargement KPIs:', error);
    }
}

// Auto-load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(loadKPIs, 300);
});
</script>
