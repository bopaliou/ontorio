{{-- Widget d'alertes dynamiques --}}
<div id="alerts-widget" class="bg-gradient-to-r from-[#274256] to-[#1a2e3d] rounded-2xl p-6 text-white">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Alertes
        </h3>
        <button onclick="loadAlerts()" class="text-xs text-gray-300 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>
    <div id="alerts-container" class="space-y-3">
        {{-- Alertes chargées dynamiquement --}}
        <div class="animate-pulse flex items-center gap-3 p-3 bg-white/10 rounded-xl">
            <div class="w-8 h-8 bg-white/20 rounded-lg"></div>
            <div class="flex-1 h-4 bg-white/20 rounded"></div>
        </div>
    </div>
</div>

<script>
async function loadAlerts() {
    const container = document.getElementById('alerts-container');
    container.innerHTML = '<div class="animate-pulse flex items-center gap-3 p-3 bg-white/10 rounded-xl"><div class="w-8 h-8 bg-white/20 rounded-lg"></div><div class="flex-1 h-4 bg-white/20 rounded"></div></div>';
    
    try {
        const response = await fetch('/api/alerts');
        const alerts = await response.json();
        
        if (alerts.length === 0) {
            container.innerHTML = `
                <div class="flex items-center gap-3 p-3 bg-green-500/20 rounded-xl border border-green-500/30">
                    <div class="w-8 h-8 bg-green-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm text-green-200">Aucune alerte - Tout est à jour !</span>
                </div>
            `;
            return;
        }
        
        container.innerHTML = alerts.map(alert => {
            const colors = {
                warning: { bg: 'bg-yellow-500/20', border: 'border-yellow-500/30', icon: 'text-yellow-400' },
                info: { bg: 'bg-blue-500/20', border: 'border-blue-500/30', icon: 'text-blue-400' },
                secondary: { bg: 'bg-gray-500/20', border: 'border-gray-500/30', icon: 'text-gray-300' }
            };
            const color = colors[alert.type] || colors.secondary;
            
            const icons = {
                'exclamation-triangle': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                'calendar': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                'home': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
            };
            
            return `
                <a href="#" data-show-section="${alert.action}" onclick="dashboard.show('${alert.action}')" 
                   class="flex items-center gap-3 p-3 ${color.bg} rounded-xl border ${color.border} hover:scale-[1.02] transition-transform cursor-pointer">
                    <div class="w-8 h-8 ${color.bg} rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 ${color.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${icons[alert.icon] || icons['home']}
                        </svg>
                    </div>
                    <span class="text-sm text-white/90">${alert.message}</span>
                    <svg class="w-4 h-4 text-white/50 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            `;
        }).join('');
        
    } catch (error) {
        console.error('Erreur chargement alertes:', error);
        container.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-red-500/20 rounded-xl border border-red-500/30">
                <span class="text-sm text-red-200">Erreur de chargement</span>
            </div>
        `;
    }
}

// Charger les alertes au démarrage
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(loadAlerts, 500);
});
</script>
