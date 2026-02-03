<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails du propriétaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Informations</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Nom</p>
                                <p class="font-semibold">{{ $proprietaire->nom }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-semibold">{{ $proprietaire->email ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Téléphone</p>
                                <p class="font-semibold">{{ $proprietaire->telephone ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Adresse</p>
                                <p class="font-semibold">{{ $proprietaire->adresse ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Immeubles ({{ $proprietaire->immeubles->count() }})
                        </h3>

                        @if($proprietaire->immeubles->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adresse</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ville</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Logements</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($proprietaire->immeubles as $immeuble)
                                        <tr>
                                            <td class="px-6 py-4">{{ $immeuble->nom }}</td>
                                            <td class="px-6 py-4">{{ $immeuble->adresse }}</td>
                                            <td class="px-6 py-4">{{ $immeuble->ville }}</td>
                                            <td class="px-6 py-4">{{ $immeuble->logements->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">Aucun immeuble associé.</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('proprietaires.edit', $proprietaire) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Éditer
                        </a>
                        <a href="{{ route('proprietaires.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
