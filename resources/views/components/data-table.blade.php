@props(['headers', 'emptyMessage' => 'Aucune donnée trouvée'])

<div class="w-full">
    <!-- Desktop Table View (Hidden on Mobile) -->
    <div class="hidden md:block bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#274256]">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-white {{ $header['classes'] ?? '' }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{ $slot }}
                    
                    @if(trim($slot) === '')
                        <tr>
                            <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="font-medium">{{ $emptyMessage }}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View (Hidden on Desktop) -->
    <div class="md:hidden space-y-4">
        @if(isset($mobile))
            {{ $mobile }}
        @else
            <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-yellow-700 text-sm">
                Vue mobile non définie pour ce tableau.
            </div>
        @endif
        
        @if(trim($slot) === '' && (!isset($mobile) || trim($mobile) === ''))
             <div class="bg-white p-8 rounded-2xl border border-gray-100 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="font-medium">{{ $emptyMessage }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
