<x-app-layout>
    {{-- Header Content --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('Mon Espace') }}
                </h2>
                <p class="text-sm text-gray-500 font-medium mt-1">Gérez vos informations personnelles et la sécurité de votre compte.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                {{-- LEFT COLUMN: IDENTITY (5 cols) --}}
                <div class="lg:col-span-5 space-y-8">
                    {{-- Identity Card --}}
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relaitve overflow-hidden">
                        <section>
                            <header class="mb-8">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#cb2d2d]"></span>
                                    {{ __('Identité') }}
                                </h2>
                                <p class="mt-2 text-sm text-gray-500">
                                    {{ __("Vos informations de profil publiques.") }}
                                </p>
                            </header>

                            @include('profile.partials.update-profile-information-form')
                        </section>
                    </div>
                </div>

                {{-- RIGHT COLUMN: SECURITY (7 cols) --}}
                <div class="lg:col-span-7 space-y-8">
                    {{-- Password Card --}}
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <section>
                            <header class="mb-8">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#274256]"></span>
                                    {{ __('Sécurité') }}
                                </h2>
                                <p class="mt-2 text-sm text-gray-500">
                                    {{ __("Assurez la protection de votre compte avec un mot de passe fort.") }}
                                </p>
                            </header>

                            @include('profile.partials.update-password-form')
                        </section>
                    </div>

                    {{-- Danger Zone --}}
                    @if(Auth::user()->role === 'admin')
                    <div class="bg-red-50/50 p-8 rounded-[2.5rem] border border-red-100">
                        <section>
                            <header class="mb-6">
                                <h2 class="text-lg font-black text-[#cb2d2d] uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    {{ __('Zone de Danger') }}
                                </h2>
                            </header>

                            @include('profile.partials.delete-user-form')
                        </section>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
