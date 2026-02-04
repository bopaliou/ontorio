@props(['title', 'status' => null, 'statusColor' => 'gray', 'actions' => null])

<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative group overflow-hidden transition-all active:scale-[0.98]">
    <!-- Status Stripe optionnelle -->
    @if($status)
        <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
    @endif
    
    <div class="flex justify-between items-start pl-3">
        <div class="flex-1 pr-4">
             <!-- Titre principal (ex: Nom du bien, Locataire) -->
            <h3 class="text-base font-bold text-gray-900 line-clamp-1 mb-1">{{ $title }}</h3>
            
            <!-- Badge Statut Mobile -->
            @if($status)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 mb-2">
                    {{ $status }}
                </span>
            @endif

            <!-- Informations secondaires -->
            <div class="mt-2 space-y-1.5 text-sm text-gray-500">
                {{ $slot }}
            </div>
        </div>

        <!-- Actions (bouton menu ou edit) -->
        @if($actions)
            <div class="flex flex-col gap-2 items-end">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
