<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">
            {{ __('Mot de passe actuel') }}
        </label>
        <input id="update_password_current_password" name="current_password" type="password" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3.5 px-4 transition-all" autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">
            {{ __('Nouveau mot de passe') }}
        </label>
        <input id="update_password_password" name="password" type="password" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3.5 px-4 transition-all" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </div>

    <div>
        <label for="update_password_password_confirmation" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">
            {{ __('Confirmer le mot de passe') }}
        </label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3.5 px-4 transition-all" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" class="bg-[#cb2d2d] text-white px-8 py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-[#a82020] hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            {{ __('Mettre à jour') }}
        </button>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-xs font-bold text-green-600 flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Enregistré.') }}
            </p>
        @endif
    </div>
</form>
