<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')

    {{-- Avatar / Name Visual --}}
    <div class="flex justify-center mb-8">
        <div class="relative group">
            <div class="w-32 h-32 rounded-[2rem] bg-gradient-to-br from-[#cb2d2d] to-[#ef4444] shadow-xl shadow-red-500/20 flex items-center justify-center text-white text-4xl font-black transform group-hover:scale-105 transition-all duration-300">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="absolute -bottom-3 -right-3 bg-white p-2 rounded-xl shadow-md border border-gray-100 text-gray-400 group-hover:text-[#cb2d2d] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
        </div>
    </div>

    <div>
        <label for="name" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">
            {{ __('Nom Complet') }}
        </label>
        <input id="name" name="name" type="text" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3.5 px-4 transition-all" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">
            {{ __('E-mail') }}
        </label>
        <input id="email" name="email" type="email" class="block w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-[#cb2d2d] focus:border-[#cb2d2d] py-3.5 px-4 transition-all" value="{{ old('email', $user->email) }}" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-4 p-4 bg-amber-50 rounded-xl text-amber-800 text-sm font-medium border border-amber-100">
                <p>
                    {{ __('Votre adresse e-mail n\'est pas vérifiée.') }}
                    <button form="send-verification" class="underline hover:text-amber-900 font-bold ml-1">
                        {{ __('Renvoyer le lien.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-green-600 font-bold">
                        {{ __('Un nouveau lien a été envoyé.') }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" class="w-full bg-[#274256] text-white px-8 py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-[#1a2e3d] hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ __('Enregistrer') }}
        </button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-xs font-bold text-green-600 flex items-center gap-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Sauvegardé.') }}
            </p>
        @endif
    </div>
</form>
