<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ontario Group') }} - Connexion</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ontorio-logo.png') }}">

        <!-- Fonts - Inter pour un look moderne -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            .gradient-bg {
                background: linear-gradient(135deg, #1A365D 0%, #243B55 50%, #2D4A6F 100%);
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
            .ontario-btn {
                background: linear-gradient(135deg, #C62828 0%, #D32F2F 100%);
                transition: all 0.3s ease;
            }
            .ontario-btn:hover {
                background: linear-gradient(135deg, #D32F2F 0%, #E53935 100%);
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(211, 47, 47, 0.4);
            }
            .input-focus:focus {
                border-color: #D32F2F;
                box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.15);
            }
            .floating-buildings {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            .text-ontario-red {
                color: #D32F2F;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Illustration & Branding -->
            <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
                <!-- Decorative circles -->
                <div class="absolute top-20 left-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
                
                <div class="flex flex-col justify-center items-center w-full px-12 z-10">
                    <!-- Logo & Title -->
                    <div class="text-center mb-8">
                        <div class="flex items-center justify-center mb-6">
                            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="h-32 w-auto bg-white rounded-2xl p-4 shadow-xl">
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-3">Ontario Group</h1>
                        <p class="text-xl text-blue-200">Gestion Immobilière</p>
                    </div>

                    <!-- Illustration -->
                    <div class="floating-buildings">
                        <img src="{{ asset('images/real-estate-illustration.png') }}" 
                             alt="Gestion Immobilière" 
                             class="max-w-md w-full drop-shadow-2xl">
                    </div>

                    <!-- Slogan -->
                    <div class="mt-12 text-center">
                        <p class="text-2xl font-light text-white italic">
                            "Bien loger dans un bon logement"
                        </p>
                        <p class="text-blue-200 mt-4 max-w-md">
                            Simplifiez la gestion de vos biens immobiliers avec notre plateforme complète et sécurisée.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
                <div class="w-full max-w-lg">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden text-center mb-8">
                        <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="h-16 w-auto mx-auto mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Ontario Group</h2>
                    </div>

                    <!-- Login Card -->
                    <div class="glass-card rounded-2xl p-12">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-8 text-gray-500 text-sm">
                        <p>© {{ date('Y') }} Ontario Group. Tous droits réservés.</p>
                        <p class="mt-1">5 Félix Faure x Colbert, Dakar Plateau</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
