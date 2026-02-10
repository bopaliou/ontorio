<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur Serveur - Ontario Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.15" integrity="sha384-9J/eie52OVscsZkst4qvkkOvH3804cvot2wKJLuZ6Hc3C77tNxZeqj3oRcpchvwN" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #cb2d2d 0%, #902020 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#0f172a] text-white min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 -left-20 w-96 h-96 bg-[#cb2d2d] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="absolute bottom-0 -right-20 w-96 h-96 bg-[#274256] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>

    <div class="relative w-full max-w-lg text-center">
        <!-- Logo / Icon -->
        <div class="mb-8 inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-glass shadow-2xl">
            <svg class="w-12 h-12 text-[#cb2d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <!-- 500 Header -->
        <h1 class="text-8xl font-black mb-4 tracking-tighter opacity-10">500</h1>
        
        <h2 class="text-3xl font-extrabold mb-4 leading-tight">
            Oups ! Une erreur est <span class="text-gradient">survenue</span>.
        </h2>
        
        <p class="text-gray-400 text-lg mb-10 leading-relaxed px-4">
            Notre équipe technique a été informée et travaille à la résolution du problème. 
            Veuillez nous excuser pour ce désagrément temporaire.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="w-full sm:w-auto px-8 py-4 bg-[#cb2d2d] hover:bg-[#b02727] text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-[#cb2d2d]/30 flex items-center justify-center group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à l'accueil
            </a>
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-8 py-4 bg-glass hover:bg-white/10 text-white font-bold rounded-2xl transition-all duration-300 flex items-center justify-center">
                Réessayer
            </button>
        </div>

        <!-- Footer -->
        <div class="mt-16 pt-8 border-t border-white/5">
            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">
                © 2026 Ontario Group • Gestion Immobilière Premium
            </p>
        </div>
    </div>
</body>
</html>
