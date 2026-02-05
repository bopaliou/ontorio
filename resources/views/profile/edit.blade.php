<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- SKELETON LOADING -->
            <div id="profile-skeleton" class="space-y-6">
                <!-- Profile Info Skeleton -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl animate-pulse space-y-4">
                        <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div> <!-- Title -->
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-1/4"></div> <!-- Label -->
                            <div class="h-10 bg-gray-200 rounded w-full"></div> <!-- Input -->
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-1/4"></div> <!-- Label -->
                            <div class="h-10 bg-gray-200 rounded w-full"></div> <!-- Input -->
                        </div>
                        <div class="h-10 bg-gray-200 rounded w-32 mt-4"></div> <!-- Button -->
                    </div>
                </div>

                <!-- Password Skeleton -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl animate-pulse space-y-4">
                         <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div> <!-- Title -->
                        <div class="space-y-2">
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-10 bg-gray-200 rounded w-full"></div>
                        </div>
                        <div class="space-y-2">
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-10 bg-gray-200 rounded w-full"></div>
                        </div>
                        <div class="space-y-2">
                             <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                             <div class="h-10 bg-gray-200 rounded w-full"></div>
                        </div>
                        <div class="h-10 bg-gray-200 rounded w-32 mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- REAL CONTENT (Hidden initially) -->
            <div id="profile-content" class="hidden space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            {{-- Delete Account --}}
            @if(Auth::user()->role === 'admin')
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif
            </div> <!-- End profile-content -->

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simulate a short loading delay for visual consistency with the dashboard
            setTimeout(() => {
                const skeleton = document.getElementById('profile-skeleton');
                const content = document.getElementById('profile-content');

                if (skeleton && content) {
                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                    // Optional: Add a fade-in effect to content
                    content.classList.add('animate-fade-in-up'); // Assuming you have this or similar
                }
            }, 500); // 500ms delay
        });
    </script>
</x-app-layout>
