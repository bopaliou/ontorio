<section class="space-y-6">
    <header>
        <p class="text-sm text-red-700 font-medium leading-relaxed">
            {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-white border-2 border-red-100 text-[#cb2d2d] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-50 hover:border-red-200 transition-all flex items-center gap-2"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        {{ __('Supprimer le compte') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 space-y-6 bg-white">
            @csrf
            @method('delete')

            <div class="text-center">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
                    <svg class="w-8 h-8 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-xl font-black text-gray-900 tracking-tight">
                    {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                </h2>
                <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                    {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
                </p>
            </div>

            <div class="max-w-xs mx-auto">
                <label for="password" class="sr-only">{{ __('Mot de passe') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3 px-4 text-center placeholder-gray-400"
                    placeholder="{{ __('Mot de passe') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-center" />
            </div>

            <div class="flex justify-center gap-4 pt-4 border-t border-gray-50">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-xs uppercase tracking-widest">
                    {{ __('Annuler') }}
                </button>

                <button type="submit" class="px-6 py-3 bg-[#cb2d2d] text-white font-bold rounded-xl hover:bg-[#a82020] transition shadow-lg shadow-red-900/20 text-xs uppercase tracking-widest">
                    {{ __('Supprimer définitivement') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
