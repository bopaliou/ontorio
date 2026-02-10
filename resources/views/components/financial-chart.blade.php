<div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm transition-all hover:shadow-lg">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-[#274256] uppercase tracking-wide">Tendance Financière</h3>
            <p class="text-xs text-gray-400 font-medium">Flux de trésorerie sur les 6 derniers mois</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-gray-500 uppercase">Encaissé</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <span class="text-[10px] font-bold text-gray-500 uppercase">Dépenses</span>
            </div>
        </div>
    </div>

    <div class="relative h-64 w-full">
        <canvas id="financialTrendChart"></canvas>
    </div>
</div>

<script>
async function initFinancialChart() {
    const ctx = document.getElementById('financialTrendChart').getContext('2d');

    try {
        const response = await fetch('/api/stats/charts');
        const data = await response.json();

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Encaissé',
                        data: data.encaissements,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Dépenses',
                        data: data.depenses,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: '#1a2e3d',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.raw) + ' F';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: {
                            font: { size: 10 },
                            callback: function(value) {
                                return value >= 1000000 ? (value / 1000000) + 'M' : (value / 1000) + 'k';
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Erreur initialisation graphique:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initFinancialChart, 600);
});
</script>
