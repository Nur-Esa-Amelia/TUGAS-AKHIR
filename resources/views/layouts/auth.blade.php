<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem Early Warning IKU/IKT Politeknik Sukabumi')</title>

    <!-- Favicon / Logo Poltek -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/LOGO POLTEKKKKK.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/LOGO POLTEKKKKK.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Apply theme immediately before paint to prevent flash -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <style>
        :root, html[data-theme="dark"] {
            --auth-bg: #0b0f19;
            --auth-surface: #171d2c;
            --auth-surface-2: #0f1626;
            --auth-border: #242f47;
            --auth-text: #f8fafc;
            --auth-text-soft: #cbd5e1;
            --auth-text-muted: #94a3b8;
            --auth-text-faint: #64748b;
            --auth-accent: #38bdf8;
            --auth-accent-soft: rgba(56, 189, 248, 0.08);
            --auth-input-bg: #0f1626;
            --auth-input-border: #242f47;
            --auth-card-shadow: rgba(0, 0, 0, 0.3);
        }

        html[data-theme="light"] {
            --auth-bg: #f1f5f9;
            --auth-surface: #ffffff;
            --auth-surface-2: #f8fafc;
            --auth-border: #e2e8f0;
            --auth-text: #0f172a;
            --auth-text-soft: #1e293b;
            --auth-text-muted: #475569;
            --auth-text-faint: #64748b;
            --auth-accent: #0f172a;
            --auth-accent-soft: rgba(15, 23, 42, 0.04);
            --auth-input-bg: #f8fafc;
            --auth-input-border: #cbd5e1;
            --auth-card-shadow: rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body { 
            background-color: var(--auth-bg); 
            background-image: linear-gradient(rgba(11, 15, 25, 0.7), rgba(11, 15, 25, 0.7)), url('{{ asset("images/gambar2.jpeg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--auth-text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        html[data-theme="light"] body,
        html[data-theme="light"] {
            background-color: var(--auth-bg) !important;
            background-image: linear-gradient(rgba(241, 245, 249, 0.7), rgba(241, 245, 249, 0.7)), url('{{ asset("images/gambar2.jpeg") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            color: var(--auth-text) !important;
        }
        html[data-theme="light"] .auth-card {
            background-color: var(--auth-surface) !important;
            border-color: var(--auth-border) !important;
            box-shadow: 0 20px 25px -5px rgba(15,23,42,0.08), 0 10px 10px -5px rgba(15,23,42,0.04) !important;
        }
        html[data-theme="light"] .auth-title { color: var(--auth-text) !important; }
        html[data-theme="light"] .auth-subtitle { color: var(--auth-text-muted) !important; }
        html[data-theme="light"] .form-label { color: var(--auth-text-muted) !important; }
        html[data-theme="light"] .form-input {
            background-color: var(--auth-input-bg) !important;
            border-color: var(--auth-input-border) !important;
            color: var(--auth-text) !important;
        }
        html[data-theme="light"] .checkbox-input {
            background-color: var(--auth-input-bg) !important;
            border-color: var(--auth-input-border) !important;
        }
        html[data-theme="light"] .checkbox-label { color: var(--auth-text-muted) !important; }
        html[data-theme="light"] .prodi-card {
            background-color: var(--auth-surface-2) !important;
            border-color: var(--auth-border) !important;
        }
        html[data-theme="light"] .prodi-name { color: var(--auth-text) !important; }
        html[data-theme="light"] .prodi-code { color: var(--auth-text-faint) !important; }
        html[data-theme="light"] .prodi-icon-wrapper {
            background-color: var(--auth-surface) !important;
            border-color: var(--auth-border) !important;
        }
        html[data-theme="light"] .btn-primary {
            background-color: var(--auth-accent) !important;
            color: var(--auth-surface) !important;
        }
        html[data-theme="light"] .btn-primary:hover {
            background-color: transparent !important;
            border: 1px solid var(--auth-accent) !important;
            color: var(--auth-accent) !important;
        }

        /* ==================== THEME TOGGLE (Auth) ==================== */
        .auth-theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.07);
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }
        .auth-theme-toggle:hover {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
        }
        html[data-theme="light"] .auth-theme-toggle {
            border-color: rgba(0,0,0,0.1);
            background: rgba(0,0,0,0.05);
            color: #475569;
        }
        html[data-theme="light"] .auth-theme-toggle:hover {
            background: rgba(0,0,0,0.1);
            color: #0f172a;
        }
        .auth-theme-toggle svg { width: 18px; height: 18px; }
        html[data-theme="light"] .icon-moon { display: none; }
        html[data-theme="dark"]  .icon-sun  { display: none; }

        /* Dekorasi latar belakang dengan efek cahaya lembut */ 
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, rgba(0, 0, 0, 0) 70%);
            top: -150px;
            left: -150px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, rgba(0, 0, 0, 0) 70%);
            bottom: -150px;
            right: -150px;
            z-index: 0;
        }

        /* Hover Bubble Particles styling */
        .hover-bubble-particle {
            position: absolute;
            pointer-events: none;
            border: 1px solid rgba(56, 189, 248, 0.65); /* Cyan border */
            background: rgba(56, 189, 248, 0.08); /* Soft cyan bg */
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            animation: bubble-float 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            z-index: 99;
            box-shadow: 0 0 4px rgba(56, 189, 248, 0.3);
        }

        @keyframes bubble-float {
            0% {
                transform: translate(-50%, -50%) scale(0.2);
                opacity: 0.9;
            }
            100% {
                transform: translate(calc(-50% + var(--dx)), calc(-150% + var(--dy))) scale(1.3);
                opacity: 0;
            }
        }

        /* Auth Card Layout */
        .auth-card {
            width: 100%;
            max-width: 480px;
            background-color: #171d2c;
            border: 1px solid #242f47;
            border-radius: 24px;
            padding: 40px 32px;
            z-index: 10;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #ffffff;
            line-height: 1.25;
            text-align: center;
        }

        .auth-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 32px;
            line-height: 1.5;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
        }

        .form-input {
            width: 100%;
            background-color: #0f1626;
            border: 1px solid #242f47;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.15);
        }

        .form-input::placeholder {
            color: #475569;
        }

        .form-error {
            font-size: 0.8rem;
            color: #f43f5e;
            margin-top: 4px;
            text-align: left;
        }

        /* Checkbox styling */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
            margin-bottom: 8px;
        }

        .checkbox-input {
            appearance: none;
            width: 18px;
            height: 18px;
            background-color: #0f1626;
            border: 1px solid #242f47;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .checkbox-input:checked {
            background-color: #38bdf8;
            border-color: #38bdf8;
        }

        .checkbox-input:checked::after {
            content: '✓';
            color: #0b0f19;
            font-size: 11px;
            font-weight: bold;
        }

        .checkbox-label {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* Button styles */
        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background-color: #f8fafc;
            color: #0b0f19;
        }

        .btn-primary:hover {
            background-color: transparent;
            border: 1px solid #cbd5e1;
            color: #f8fafc;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid #38bdf8;
            color: #38bdf8;
        }

        .btn-secondary:hover {
            background-color: #38bdf8;
            color: #0b0f19;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
            transform: translateY(-2px);
        }

        /* Prodi Card styles in Step 1 */
        .prodi-card {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background-color: #0f1626;
            border: 1px solid #1e293b;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            outline: none;
            color: inherit;
            margin-bottom: 4px;
        }

        .prodi-card:hover {
            border-color: #38bdf8;
            background-color: rgba(56, 189, 248, 0.04);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.1);
            transform: translateX(4px);
        }

        .prodi-icon-wrapper {
            width: 44px;
            height: 44px;
            background-color: #171d2c;
            border: 1px solid #242f47;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            transition: all 0.2s ease;
        }

        .prodi-card:hover .prodi-icon-wrapper {
            background-color: rgba(56, 189, 248, 0.1);
            border-color: rgba(56, 189, 248, 0.2);
        }

        .prodi-info {
            flex: 1;
            margin-left: 16px;
        }

        .prodi-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .prodi-code {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 500;
        }

        .prodi-arrow {
            color: #475569;
            transition: all 0.2s ease;
        }

        .prodi-card:hover .prodi-arrow {
            color: #38bdf8;
            transform: translateX(2px);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const attachBubbleEffect = (el) => {
                if (el.dataset.bubbleAttached) return;
                el.dataset.bubbleAttached = 'true';

                // Ensure element is relative-positioned
                const style = window.getComputedStyle(el);
                if (style.position === 'static') {
                    el.style.position = 'relative';
                }

                let lastSpawn = 0;
                const spawnThrottle = 50; // ms between bubbles

                el.addEventListener('mousemove', (e) => {
                    const now = Date.now();
                    if (now - lastSpawn < spawnThrottle) return;
                    lastSpawn = now;

                    const rect = el.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    createBubble(el, x, y);
                });

                // Spawn burst of bubbles on click
                el.addEventListener('click', (e) => {
                    const rect = el.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    for (let i = 0; i < 6; i++) {
                        setTimeout(() => createBubble(el, x, y), i * 45);
                    }
                });
            };

            const createBubble = (parent, x, y) => {
                const bubble = document.createElement('span');
                bubble.className = 'hover-bubble-particle';
                
                // Randomize size between 6px and 16px
                const size = Math.random() * 10 + 6;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Set coordinates
                bubble.style.left = `${x}px`;
                bubble.style.top = `${y}px`;

                // Randomize drift direction
                const dx = (Math.random() - 0.5) * 80; // horizontal drift range
                const dy = -(Math.random() * 50 + 40);  // vertical float range
                bubble.style.setProperty('--dx', `${dx}px`);
                bubble.style.setProperty('--dy', `${dy}px`);

                parent.appendChild(bubble);

                // Remove element after animation completes
                bubble.addEventListener('animationend', () => {
                    bubble.remove();
                });
            };

            // Auto-attach to all buttons, links, and select cards
            const scanAndAttach = () => {
                const targets = document.querySelectorAll('button, a, [role="button"], .cursor-pointer');
                targets.forEach(attachBubbleEffect);
            };

            scanAndAttach();

            // Observe body for dynamically added interactive elements (e.g. step changes)
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            if (node.matches('button, a, [role="button"], .cursor-pointer')) {
                                attachBubbleEffect(node);
                            }
                            const children = node.querySelectorAll('button, a, [role="button"], .cursor-pointer');
                            children.forEach(attachBubbleEffect);
                        }
                    });
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });

        // Theme Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('auth-theme-btn');
            if (btn) {
                btn.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-theme');
                    const next = current === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', next);
                    localStorage.setItem('theme', next);
                });
            }
        });
    </script>
</head>
<body class="bg-[#080c14] text-gray-100 min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-slate-900 overflow-x-hidden antialiased">
    <!-- Beautiful Glow Effects -->
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-cyan-500/5 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[120px] pointer-events-none"></div>

    <!-- Theme Toggle Button (floating) -->
    <button id="auth-theme-btn" class="auth-theme-toggle" title="Toggle Tema" aria-label="Toggle tema gelap/terang">
        <svg class="icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
        </svg>
        <svg class="icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10A5 5 0 0012 7z"/>
        </svg>
    </button>

    <div class="w-full @yield('container-class', 'max-w-[480px]') z-10 py-6">
        @yield('content')
    </div>
</body>
</html>
