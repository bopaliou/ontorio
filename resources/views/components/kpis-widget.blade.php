{{-- Widget KPIs dynamiques avec refresh AJAX (Refactorisé Modern Widget Style) --}}
<div id="kpis-widget" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 animate-stagger">
    
    {{-- KPI 1: Loyers Facturés --}}
    <div class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-blue-100 shadow-sm cursor-pointer"
         data-show-section="loyers" onclick="dashboard.show('loyers')">
        {{-- Watermark --}}
        <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
            <svg class="w-32 h-32 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        
        <div class="relative z-10 flex flex-col h-full justify-between">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Loyers Facturés</h3>
            <div>
                <div class="flex items-baseline gap-1">
                    <p id="kpi-factures" class="text-4xl font-black tracking-tighter text-gray-900 tabular-nums">
                        <span class="animate-pulse bg-gray-100 rounded w-24 h-8 inline-block"></span>
                    </p>
                </div>
                <p class="mt-1 text-xs font-medium text-blue-600">Ce mois</p>
            </div>
        </div>
    </div>

    {{-- KPI 2: Encaissé --}}
    <div class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-emerald-100 shadow-sm cursor-pointer"
         data-show-section="paiements" onclick="dashboard.show('paiements')">
        {{-- Watermark --}}
        <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
            <svg class="w-32 h-32 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <div class="relative z-10 flex flex-col h-full justify-between">
            <div class="flex items-start justify-between">
                <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Montant Encaissé</h3>
                <span id="kpi-taux" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                    --%
                </span>
            </div>
            
            <div>
                <div class="flex items-baseline gap-1">
                     <p id="kpi-encaisses" class="text-4xl font-black tracking-tighter text-emerald-600 tabular-nums">
                        <span class="animate-pulse bg-emerald-50 rounded w-24 h-8 inline-block"></span>
                    </p>
                </div>
                {{-- Progress Bar --}}
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div id="kpi-progress" class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI 3: Impayés --}}
    <div class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-white border border-red-100 shadow-sm cursor-pointer"
         data-show-section="loyers" onclick="dashboard.show('loyers')">
        {{-- Watermark --}}
        <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
             <svg class="w-32 h-32 text-[#cb2d2d]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>

        <div class="relative z-10 flex flex-col h-full justify-between">
            <div class="flex items-start justify-between">
                 <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Arriérés Total</h3>
                 <span id="kpi-impayes-count" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-[#cb2d2d] border border-red-100">
                    -- retards
                </span>
            </div>
           
            <div>
                <div class="flex items-baseline gap-1">
                    <p id="kpi-arrieres" class="text-4xl font-black tracking-tighter text-[#cb2d2d] tabular-nums">
                        <span class="animate-pulse bg-red-50 rounded w-24 h-8 inline-block"></span>
                    </p>
                </div>
                {{-- Aging Mini Breakdown --}}
                <div id="kpi-aging" class="mt-2 flex gap-2 text-[10px] text-gray-400 font-mono hidden">
                    <!-- JS will populate -->
                </div>
            </div>
        </div>
    </div>

    {{-- KPI 4: Solde Net --}}
    <div class="relative overflow-hidden rounded-3xl p-6 transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl bg-gradient-to-br from-[#274256] to-[#1a2e3d] text-white shadow-lg shadow-blue-900/20 cursor-pointer"
         data-show-section="depenses" onclick="dashboard.show('depenses')">
        {{-- Watermark --}}
        <div class="absolute -bottom-6 -right-6 opacity-[0.08] transform rotate-[-15deg] group-hover:scale-110 group-hover:rotate-0 transition-all duration-500">
             <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <div class="relative z-10 flex flex-col h-full justify-between">
             <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-blue-200">Solde Net</h3>
            <div>
                <div class="flex items-baseline gap-1">
                     <p id="kpi-solde" class="text-4xl font-black tracking-tighter text-white tabular-nums">
                        <span class="animate-pulse bg-white/10 rounded w-24 h-8 inline-block"></span>
                    </p>
                </div>
                <p class="mt-1 text-xs font-medium text-blue-300">Encaissements - Dépenses</p>
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
        const updateVal = (id, val, suffix = 'F') => {
             const el = document.getElementById(id);
             if(el) el.innerHTML = `${val} <span class="text-lg font-bold opacity-60">${suffix}</span>`;
        };

        updateVal('kpi-factures', fmt(data.loyers_factures));
        updateVal('kpi-encaisses', fmt(data.loyers_encaisses));
        updateVal('kpi-arrieres', fmt(data.arrieres_total));
        updateVal('kpi-depenses', fmt(data.depenses_mois));
        updateVal('kpi-solde', fmt(data.solde_net));

        const tauxEl = document.getElementById('kpi-taux');
        if(tauxEl) tauxEl.textContent = data.taux_recouvrement + '% recouvré';

        const progEl = document.getElementById('kpi-progress');
        if(progEl) progEl.style.width = data.taux_recouvrement + '%';

        const impayesEl = document.getElementById('kpi-impayes-count');
        if(impayesEl) impayesEl.textContent = data.nb_impayes + ' retards';

        // Colorer le solde si négatif
        const soldeEl = document.getElementById('kpi-solde');
        if (data.solde_net < 0 && soldeEl) {
            soldeEl.classList.remove('text-white');
            soldeEl.classList.add('text-red-400');
        }

        // Aging Logic
        if (data.kpis_modern && data.kpis_modern.arrears_aging) {
            const agingEl = document.getElementById('kpi-aging');
            const ag = data.kpis_modern.arrears_aging;
            
            // Format short: "30j: 100k | 60j: 50k"
            let html = '';
            // Only show significant buckets
            if(ag['0-30'] > 0) html += `<span class="text-red-300">30j:${fmt(ag['0-30']/1000)}k</span>`;
            if(ag['31-60'] > 0) html += `<span class="text-red-400 font-bold ml-1">60j:${fmt(ag['31-60']/1000)}k</span>`;
            if(ag['90+'] > 0) html += `<span class="text-red-600 font-black ml-1">90j+:${fmt(ag['90+']/1000)}k</span>`;
            
            if (html && agingEl) {
                agingEl.innerHTML = html;
                agingEl.classList.remove('hidden');
            }
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
