<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ontario Group') }} - Connexion</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ontorio-logo.png') }}">

        <!-- Fonts - Modern Typography -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --ontario-blue: #274256;
                --ontario-blue-dark: #1a2e3d;
                --ontario-red: #cb2d2d;
                --ontario-red-light: #ef4444;
                --glass-bg: rgba(255, 255, 255, 0.85);
                --glass-border: rgba(255, 255, 255, 0.3);
            }

            [x-cloak] { display: none !important; }

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8fafc;
            }

            h1, h2, h3, .font-poppins {
                font-family: 'Poppins', sans-serif;
            }

            .gradient-bg {
                background: linear-gradient(135deg, var(--ontario-blue) 0%, var(--ontario-blue-dark) 100%);
                position: relative;
            }

            .gradient-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 20% 30%, rgba(203, 45, 45, 0.15) 0%, transparent 50%),
                            radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
                pointer-events: none;
            }

            .glass-card {
                background: var(--glass-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
                border: 1px solid var(--glass-border);
            }

            .ontario-btn {
                background: linear-gradient(135deg, var(--ontario-red) 0%, var(--ontario-red-light) 100%);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .ontario-btn:hover {
                background: linear-gradient(135deg, var(--ontario-red-light) 0%, #f87171 100%);
                transform: translateY(-2px);
                box-shadow: 0 12px 24px -6px rgba(203, 45, 45, 0.4);
            }

            .ontario-btn::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: 0.5s;
            }

            .ontario-btn:hover::after {
                left: 100%;
            }

            .input-focus:focus {
                border-color: var(--ontario-red);
                box-shadow: 0 0 0 4px rgba(203, 45, 45, 0.1);
                background-color: white;
            }

            /* Animations */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes fadeInLeft {
                from { opacity: 0; transform: translateX(-30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.8s ease-out forwards;
            }

            .animate-fade-in-left {
                animation: fadeInLeft 1s ease-out forwards;
            }

            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }

            .floating-brand {
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
        </style>
    </head>
    <body class="antialiased font-inter">
        <div class="min-h-screen flex overflow-hidden">
            <!-- Left Side - Illustration & Branding -->
            <div class="hidden lg:flex lg:w-3/5 gradient-bg relative items-center justify-center overflow-hidden">
                <!-- Decorative elements -->
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-white/5 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-red-500/10 rounded-full blur-[120px]"></div>

                <div class="px-20 z-10 text-center lg:text-left">
                    <div class="animate-fade-in-left mb-10">
                        <div class="inline-block p-4 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 mb-8 floating-brand">
                            <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="h-28 w-auto filter drop-shadow-xl brightness-110">
                        </div>
                        <h1 class="text-6xl font-bold text-white leading-tight mb-4 tracking-tight">
                            Gérez vos biens <br>
                            <span class="text-red-400">en toute sérénité.</span>
                        </h1>
                        <div class="w-24 h-1.5 bg-red-500 rounded-full mb-8"></div>
                    </div>

                    <div class="animate-fade-in-left delay-200">
                        <p class="text-2xl font-light text-blue-50/80 italic max-w-xl">
                            "Bien loger dans un bon logement"
                        </p>
                        <p class="text-blue-100/60 mt-6 text-lg max-w-lg leading-relaxed">
                            La plateforme de référence pour la gestion immobilière moderne au Sénégal. Efficacité, transparence et sécurité au cœur de votre patrimoine.
                        </p>
                    </div>

                    <!-- Stats or trust indicators -->
                    <div class="flex gap-12 mt-16 animate-fade-in-left delay-300">
                        <div class="flex flex-col">
                            <span class="text-3xl font-bold text-white">2000+</span>
                            <span class="text-blue-200/60 text-sm uppercase tracking-wider font-semibold">Logements</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-3xl font-bold text-white">98%</span>
                            <span class="text-blue-200/60 text-sm uppercase tracking-wider font-semibold">Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full lg:w-2/5 flex items-center justify-center p-6 bg-slate-50 relative">
                <!-- Background decorative shapes for right side -->
                <div class="absolute top-20 right-20 w-32 h-32 bg-red-100 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute bottom-20 left-20 w-40 h-40 bg-blue-100 rounded-full blur-3xl opacity-50"></div>

                <div class="w-full max-w-md z-10 transition-all duration-500">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden text-center mb-10">
                        <img src="{{ asset('images/ontorio-logo.png') }}" alt="Ontario Group" class="h-20 w-auto mx-auto mb-4">
                        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Ontario Group</h2>
                    </div>

                    <!-- Login Card -->
                    <div class="glass-card rounded-3xl p-10 animate-fade-in-up">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-12 text-gray-400 text-sm animate-fade-in-up delay-300">
                        <p class="font-medium tracking-wide">© {{ date('Y') }} ONTARIO GROUP</p>
                        <p class="mt-2 opacity-75">5 Félix Faure x Colbert, Dakar Plateau</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
