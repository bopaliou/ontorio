@php
    $reminderStats = [
        ['label' => 'Relances ce mois', 'value' => '124', 'icon' => 'chat-alt-2', 'color' => 'blue'],
        ['label' => 'Taux de réponse', 'value' => '42%', 'icon' => 'trending-up', 'color' => 'emerald'],
        ['label' => 'Paiements suite relance', 'value' => '1.2M', 'icon' => 'cash', 'color' => 'amber'],
    ];

    $templates = [
        [
            'id' => 'anticipation',
            'title' => 'J-3 : Anticipation',
            'description' => 'Rappel amical avant l\'échéance du 5 du mois.',
            'status' => 'actif',
            'message' => "Bonjour {locataire}, nous vous rappelons amicalement que votre loyer Ontario Group pour {mois} d'un montant de {montant} F arrive à échéance dans 3 jours. Merci pour votre confiance.",
            'color' => 'blue'
        ],
        [
            'id' => 'echeance',
            'title' => 'Jour J : Échéance',
            'description' => 'Message envoyé le 5 du mois.',
            'status' => 'actif',
            'message' => "Cher {locataire}, votre loyer de {mois} est exigible aujourd'hui ({montant} F). Vous pouvez effectuer votre paiement via l'un de nos canaux habituels. Merci.",
            'color' => 'emerald'
        ],
        [
            'id' => 'retard',
            'title' => 'J+5 : Mise en demeure',
            'description' => 'Alerte de retard après le délai gracieux.',
            'status' => 'brouillon',
            'message' => "ALERTE : Monsieur/Madame {locataire}, nous n'avons pas encore reçu votre paiement pour {mois}. Veuillez régulariser votre situation immédiatement pour éviter des pénalités de retard. {reste_a_payer} F attendus.",
            'color' => 'red'
        ]
    ];
@endphp

<div class="h-full flex flex-col gap-8" id="relances-section-container" x-data="{ activeTab: 'templates', editingTemplate: null }">

    @include('components.section-header', [
        'title' => 'Centre de Relances',
        'subtitle' => 'Automatisez vos communications pour un recouvrement pro-actif.',
        'icon' => 'bell',
        'actions' => '<div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl border border-emerald-100">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-bold uppercase tracking-widest">Bot Automatisé Actif</span>
        </div>'
    ])

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-stagger">
        @foreach($reminderStats as $stat)
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 group hover:shadow-md transition-all">
            <div class="w-14 h-14 rounded-2xl bg-{{ $stat['color'] }}-50 flex items-center justify-center text-{{ $stat['color'] }}-600 group-hover:scale-110 transition-transform">
                @if($stat['icon'] === 'chat-alt-2')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                @elseif($stat['icon'] === 'trending-up')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @endif
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                <p class="text-2xl font-black text-gray-900 tracking-tighter">{{ $stat['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Tabs -->
    <div class="flex flex-col gap-6">
        <div class="flex items-center gap-1 bg-gray-100/50 p-1.5 rounded-2xl self-start">
            <button @click="activeTab = 'templates'" :class="activeTab === 'templates' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Templates</button>
            <button @click="activeTab = 'historique'" :class="activeTab === 'historique' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Historique</button>
            <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Configuration</button>
        </div>

        {{-- TAB: TEMPLATES --}}
        <div x-show="activeTab === 'templates'" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($templates as $tmpl)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-500">
                <div class="p-8 pb-4">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $tmpl['color'] }}-50 flex items-center justify-center text-{{ $tmpl['color'] }}-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $tmpl['status'] === 'actif' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                            {{ $tmpl['status'] }}
                        </span>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 tracking-tight mb-1">{{ $tmpl['title'] }}</h4>
                    <p class="text-xs text-gray-400 font-medium mb-6">{{ $tmpl['description'] }}</p>
                    
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 relative group-hover:bg-white transition-colors duration-500">
                        <p class="text-[13px] leading-relaxed text-gray-600 font-medium italic">"{{ $tmpl['message'] }}"</p>
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @php $tags = ['locataire', 'montant', 'mois', 'reste_a_payer']; @endphp
                            @foreach($tags as $tag)
                            <span class="px-2 py-0.5 bg-{{ $tmpl['color'] }}-50 text-{{ $tmpl['color'] }}-600 rounded-md text-[9px] font-bold border border-{{ $tmpl['color'] }}-100 lowercase">#{!! $tag !!}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-auto p-4 bg-gray-50/50 border-t border-gray-100 flex gap-2">
                    <button class="flex-1 py-3 bg-white border border-gray-200 text-gray-700 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Modifier
                    </button>
                    <button class="w-12 h-12 bg-white border border-gray-200 text-gray-400 rounded-xl hover:text-emerald-600 hover:border-emerald-200 transition-all flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- TAB: HISTORIQUE --}}
        <div x-show="activeTab === 'historique'" x-transition class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date / Heure</th>
                            <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Locataire</th>
                            <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                            <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Canal</th>
                            <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @for($i=1; $i<=8; $i++)
                        <tr class="group hover:bg-gray-50/80 transition-all">
                            <td class="py-4 px-8">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">{{ Carbon\Carbon::now()->subHours($i*2)->format('d M Y') }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ Carbon\Carbon::now()->subHours($i*2)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500">L{{ $i }}</div>
                                    <span class="text-xs font-bold text-gray-700">Locataire Prototype {{ $i }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-8">
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider bg-gray-100 px-2 py-1 rounded-md">J-3 Anticipation</span>
                            </td>
                            <td class="py-4 px-8">
                                <div class="flex items-center gap-2 text-emerald-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.984 11.984 0 001.586 6.03L0 24l6.096-1.6c1.854 1.011 3.948 1.545 6.083 1.545 6.635 0 12.032-5.396 12.035-12.031a11.824 11.824 0 00-3.417-8.461z"/></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">WhatsApp</span>
                                </div>
                            </td>
                            <td class="py-4 px-8">
                                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-bold text-[10px] uppercase tracking-widest">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Délivré
                                </span>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Affichage de 8 sur 1,240 enregistrements</p>
                <div class="flex gap-2">
                    <button class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB: SETTINGS --}}
        <div x-show="activeTab === 'settings'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm space-y-8">
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest">Contrôle Automatique</h4>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-800">Envoi Automatique</p>
                            <p class="text-[11px] text-gray-400 font-medium">Lancer les relances sans validation manuelle.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-800">Canal Prioritaire</p>
                            <p class="text-[11px] text-gray-400 font-medium">WhatsApp est recommandé pour le Sénégal.</p>
                        </div>
                        <select class="bg-gray-50 border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest py-2 px-4 focus:ring-blue-500 focus:border-blue-500">
                            <option>WhatsApp</option>
                            <option>SMS</option>
                            <option>Email</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-[#1a2e3d] rounded-3xl p-8 border border-white/5 shadow-xl text-white relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>
                <h4 class="text-sm font-black text-blue-300 uppercase tracking-widest mb-6">Aide à la configuration</h4>
                <div class="space-y-4">
                    <p class="text-xs text-gray-300 leading-relaxed font-medium">Les relances sont basées sur la date d'échéance fixée au <span class="text-white font-bold">5 de chaque mois</span> dans vos contrats.</p>
                    <div class="flex gap-3 pt-4">
                        <div class="flex flex-col items-center">
                            <div class="w-px h-full bg-gray-700 absolute mt-4 -z-10"></div>
                            <div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center text-[8px] font-bold">1</div>
                        </div>
                        <div class="pb-6">
                            <p class="text-[11px] font-bold text-blue-200 uppercase">Étape 1 : Le 2 du mois</p>
                            <p class="text-[10px] text-gray-400">Envoi de l'anticipation (J-3).</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                         <div class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center text-[8px] font-bold">2</div>
                         <div class="pb-6">
                            <p class="text-[11px] font-bold text-emerald-200 uppercase">Étape 2 : Le 5 du mois</p>
                            <p class="text-[10px] text-gray-400">Relance le jour de l'échéance.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                         <div class="w-4 h-4 rounded-full bg-red-500 flex items-center justify-center text-[8px] font-bold">3</div>
                         <div>
                            <p class="text-[11px] font-bold text-red-200 uppercase">Étape 3 : Le 10 du mois</p>
                            <p class="text-[10px] text-gray-400">Mise en demeure pour les retardataires.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
