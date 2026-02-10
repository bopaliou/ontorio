<div class="h-full flex flex-col gap-8" id="logs-section-container">
    @include('components.section-header', [
        'title' => 'Logs Système',
        'subtitle' => 'Traçabilité complète des actions effectuées sur la plateforme.',
        'icon' => 'clock',
        'actions' => '<button class="group bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-gray-50 hover:border-gray-300 hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#cb2d2d] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Exporter CSV
        </button>'
    ])

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative">
        {{-- Background Decor --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-gray-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-60 pointer-events-none"></div>

        <div class="p-8 lg:p-12 relative z-10">
            @php
                // Group logs by Date (Today, Yesterday, etc.)
                $groupedLogs = collect($data['logs_list'] ?? [])->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item['date'])->format('Y-m-d');
                });
            @endphp

            <div class="space-y-12">
                @forelse($groupedLogs as $date => $logs)
                <div class="relative">
                    {{-- Date Header --}}
                    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm py-4 mb-6 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-[#cb2d2d]"></div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">
                            @if($date == now()->format('Y-m-d'))
                                Aujourd'hui
                            @elseif($date == now()->subDay()->format('Y-m-d'))
                                Hier
                            @else
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            @endif
                        </h3>
                    </div>

                    {{-- Timeline Items --}}
                    <div class="space-y-8 relative pl-4 md:pl-0">
                        {{-- Connector Line --}}
                        <div class="absolute left-4 md:left-[8.5rem] top-4 bottom-4 w-px bg-gradient-to-b from-gray-200 via-gray-100 to-transparent hidden md:block"></div>

                        @foreach($logs as $log)
                        @php
                            $typeConfig = [
                                'create' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'plus'],
                                'update' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'pencil'],
                                'delete' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'icon' => 'trash'],
                                'report' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'icon' => 'document-text'],
                                'login' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => 'login'],
                                'payment' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'cash']
                            ];
                            $config = $typeConfig[$log['type']] ?? $typeConfig['login'];
                        @endphp

                        <div class="group relative flex flex-col md:flex-row gap-6 md:gap-10 items-start md:items-center animate-fade-in-up">

                            {{-- Time (Left Column) --}}
                            <div class="hidden md:flex flex-col items-end w-24 shrink-0 pt-1">
                                <span class="font-mono text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($log['date'])->format('H:i') }}</span>
                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Heure</span>
                            </div>

                            {{-- Icon (Center) --}}
                            <div class="relative z-10 w-12 h-12 rounded-2xl {{ $config['bg'] }} {{ $config['text'] }} border-4 border-white shadow-sm flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                                @if($config['icon'] == 'plus') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                @elseif($config['icon'] == 'trash') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                @elseif($config['icon'] == 'document-text') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($config['icon'] == 'cash') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @endif
                            </div>

                            {{-- Content (Right) --}}
                            <div class="flex-1 p-5 rounded-2xl bg-gray-50/50 border border-gray-100 group-hover:bg-white group-hover:shadow-lg group-hover:shadow-gray-100/50 group-hover:-translate-y-1 transition-all duration-300 w-full relative overflow-hidden">
                                {{-- Hover Stripe --}}
                                <div class="absolute left-0 top-0 bottom-0 w-1 {{ str_replace('bg-','bg-', $config['bg']) }} opacity-0 group-hover:opacity-100 transition-opacity"></div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-snug">{{ $log['action'] }}</p>
                                        <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                            <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center text-[9px] font-black text-gray-600">
                                                {{ strtoupper(substr($log['user'], 0, 1)) }}
                                            </div>
                                            <span class="font-medium">{{ $log['user'] }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-block md:hidden text-xs font-mono text-gray-400">{{ \Carbon\Carbon::parse($log['date'])->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-20 flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 text-gray-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900">Aucune activité récente</h3>
                    <p class="text-gray-400 mt-2 text-sm">Les actions effectuées sur la plateforme apparaîtront ici.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
