<div class="h-full flex flex-col gap-6" id="logs-section-container">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#274256]">Logs Système</h2>
            <p class="text-sm text-gray-500 mt-1">Traçabilité complète des actions effectuées sur la plateforme.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Exporter (.csv)
            </button>
        </div>
    </div>

    <!-- Timeline of logs -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-8">
        <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:w-0.5 before:-translate-x-px before:bg-gradient-to-b before:from-transparent before:via-gray-100 before:to-transparent">
            @forelse($data['logs_list'] ?? [] as $log)
            <div class="relative flex items-center justify-between md:justify-start">
                <!-- Dot -->
                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white shadow-sm shrink-0 md:order-1 
                    {{ $log['type'] == 'create' ? 'bg-green-500' : 
                       ($log['type'] == 'payment' ? 'bg-blue-500' : 
                       ($log['type'] == 'report' ? 'bg-purple-500' : 'bg-gray-500')) }} text-white">
                    @if($log['type'] == 'create')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    @elseif($log['type'] == 'payment')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <!-- Content -->
                <div class="w-[calc(100%-4rem)] md:w-[calc(100%-10rem)] p-4 rounded-2xl border border-gray-50 bg-gray-50/30 ml-6 hover:bg-white hover:shadow-xl hover:shadow-gray-500/5 transition duration-300 md:order-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="font-black text-[#274256] text-sm">{{ $log['action'] }}</div>
                        <time class="text-[10px] font-bold text-gray-400 uppercase">{{ \Carbon\Carbon::parse($log['date'])->translatedFormat('H:i') }}</time>
                    </div>
                    <div class="text-xs text-gray-500">
                        Effectué par <span class="font-bold text-gray-700">{{ $log['user'] }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400 italic">Aucune activité enregistrée.</div>
            @endforelse
        </div>
    </div>
</div>
