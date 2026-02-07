<x-guest-layout>
    <div class="space-y-10">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Header -->
        <div class="text-center animate-fade-in-up">
            <h2 class="text-4xl font-black text-gray-900 font-poppins tracking-tight">Espace Pro</h2>
            <p class="text-gray-400 mt-3 font-medium text-lg">Gérez votre patrimoine avec sérénité.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-12 space-y-7">
            @csrf

            <!-- Email Address -->
            <div class="animate-fade-in-up delay-100">
                <div class="flex items-center justify-between mb-2">
                    <label for="email" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">
                        Identifiant
                    </label>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center transition-colors group-focus-within:bg-red-50">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                    </div>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="votre@email.com"
                           class="input-focus block w-full input-with-icon-left pr-4 py-4 border border-gray-100 rounded-[1.5rem] text-sm font-bold text-gray-900 placeholder-gray-300 transition-all duration-300 bg-gray-50/30">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 ml-1" />
            </div>

            <!-- Password -->
            <div class="animate-fade-in-up delay-200">
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">
                        Mot de passe
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-[10px] text-gray-400 hover:text-red-600 font-black uppercase tracking-widest transition-colors duration-200"
                           href="{{ route('password.request') }}">
                            Oublié ?
                        </a>
                    @endif
                </div>
                <div class="relative group" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center transition-colors group-focus-within:bg-red-50">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <input id="password"
                           :type="show ? 'text' : 'password'"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="input-focus block w-full input-with-icon-left pr-12 py-4 border border-gray-100 rounded-[1.5rem] text-sm font-bold text-gray-900 placeholder-gray-300 transition-all duration-300 bg-gray-50/30">
                    <button type="button" 
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-gray-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" x-show="!show" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="h-5 w-5" fill="none" x-show="show" x-cloak stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 ml-1" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center animate-fade-in-up delay-300">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="peer opacity-0 absolute h-5 w-5 cursor-pointer">
                        <div class="h-5 w-5 border-2 border-gray-200 rounded-lg group-hover:border-red-400 peer-checked:bg-red-600 peer-checked:border-red-600 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="ml-3 text-xs font-bold text-gray-500 group-hover:text-gray-900 transition-colors uppercase tracking-widest">Rester connecté</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 animate-fade-in-up delay-300">
                <button type="submit"
                        class="ontario-btn w-full py-5 px-6 rounded-[1.5rem] text-white font-black text-sm uppercase tracking-[0.2em] focus:outline-none focus:ring-4 focus:ring-red-500/20 shadow-[0_20px_40px_-15px_rgba(203,45,45,0.3)] group">
                    <span class="flex items-center justify-center">
                        <span>Accéder au Dashboard</span>
                        <svg class="w-4 h-4 ml-3 transition-transform group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
