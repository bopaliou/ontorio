<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contrats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('contrats.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Nouveau Contrat
                        </a>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bien</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locataire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($contrats as $contrat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $contrat->bien->nom ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $contrat->locataire->nom ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $contrat->type_bail }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($contrat->loyer_montant, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                                        @if($contrat->date_fin)
                                            - {{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('contrats.print', $contrat) }}" class="text-green-600 hover:text-green-900 mr-3" target="_blank">Imprimer</a>
                                        <a href="{{ route('contrats.show', $contrat) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                        <a href="{{ route('contrats.edit', $contrat) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Éditer</a>
                                        <form action="{{ route('contrats.destroy', $contrat) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun contrat enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $contrats->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
