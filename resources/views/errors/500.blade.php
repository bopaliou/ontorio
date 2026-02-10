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
        :root {
            --bg: #0f172a;
            --accent: #cb2d2d;
            --accent-dark: #902020;
            --muted: #9ca3af;
            --panel: rgba(255, 255, 255, 0.05);
            --panel-border: rgba(255, 255, 255, 0.1);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            background: var(--bg);
            color: #fff;
            font-family: Inter, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            position: relative;
        }

        .blob {
            position: absolute;
            width: 24rem;
            height: 24rem;
            border-radius: 9999px;
            opacity: .2;
            filter: blur(56px);
            animation: pulse 4s ease-in-out infinite;
        }

        .blob.left { background: var(--accent); top: -4rem; left: -5rem; }
        .blob.right { background: #274256; bottom: -4rem; right: -5rem; animation-delay: 2s; }

        .container {
            width: 100%;
            max-width: 34rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .icon-wrap {
            width: 6rem;
            height: 6rem;
            margin: 0 auto 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1.25rem;
            background: var(--panel);
            border: 1px solid var(--panel-border);
            backdrop-filter: blur(20px);
        }

        .code {
            font-size: clamp(4rem, 10vw, 6rem);
            font-weight: 900;
            letter-spacing: -.05em;
            opacity: .12;
            margin: 0 0 .5rem;
        }

        h2 {
            margin: 0 0 1rem;
            font-size: clamp(1.5rem, 4vw, 2rem);
            line-height: 1.2;
        }

        .gradient {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .desc {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.65;
            margin: 0 auto 2.2rem;
            max-width: 32rem;
        }

        .actions {
            display: flex;
            gap: .9rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            text-decoration: none;
            cursor: pointer;
            padding: .9rem 1.5rem;
            border-radius: 1rem;
            font-weight: 700;
            transition: .25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 12rem;
        }

        .btn-primary {
            color: #fff;
            background: var(--accent);
            box-shadow: 0 10px 30px rgba(203, 45, 45, .25);
        }

        .btn-primary:hover { background: #b02727; }

        .btn-ghost {
            color: #fff;
            background: var(--panel);
            border: 1px solid var(--panel-border);
        }

        .btn-ghost:hover { background: rgba(255, 255, 255, .1); }

        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, .08);
            color: #6b7280;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
    </style>
</head>
<body>
    <div class="blob left" aria-hidden="true"></div>
    <div class="blob right" aria-hidden="true"></div>

    <main class="container">
        <div class="icon-wrap" aria-hidden="true">
            <svg width="44" height="44" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cb2d2d">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1 class="code">500</h1>

        <h2>Oups ! Une erreur est <span class="gradient">survenue</span>.</h2>

        <p class="desc">
            Notre équipe technique a été informée et travaille à la résolution du problème.
            Veuillez nous excuser pour ce désagrément temporaire.
        </p>

        <div class="actions">
            <a href="/" class="btn btn-primary">Retour à l'accueil</a>
            <button onclick="window.location.reload()" class="btn btn-ghost" type="button">Réessayer</button>
        </div>

        <p class="footer">© 2026 Ontario Group • Gestion Immobilière Premium</p>
    </main>
</body>
</html>
