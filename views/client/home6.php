<!DOCTYPE html>
<html lang="<?=getLanguageCode();?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?=$CMSNT->site('title')?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?=$CMSNT->site('description')?>">
    <meta name="keywords" content="<?=$CMSNT->site('keywords')?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?=base_url();?>">
    <link rel="icon" type="image/png" href="<?=BASE_URL($CMSNT->site('favicon'));?>" />
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?=$CMSNT->site('title')?>">
    <meta property="og:description" content="<?=$CMSNT->site('description')?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=base_url();?>">
    <meta property="og:image" content="<?=BASE_URL($CMSNT->site('image')); // THAY THẾ BẰNG URL HÌNH ẢNH CỤ THỂ CHO FACEBOOK (1200x630px) ?>">
    <meta property="og:image:alt" content="<?=$CMSNT->site('title')?>">
    <meta property="og:site_name" content="<?=$CMSNT->site('title')?>">
    <meta property="og:locale" content="vi_VN">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?=$CMSNT->site('title')?>">
    <meta name="twitter:description" content="<?=$CMSNT->site('description')?>">
    <meta name="twitter:image" content="<?=BASE_URL($CMSNT->site('image')); // THAY THẾ BẰNG URL HÌNH ẢNH CỤ THỂ CHO TWITTER (tỷ lệ 2:1, ví dụ 600x300px) ?>">
    <meta name="twitter:image:alt" content="<?=$CMSNT->site('title')?>">
    <!-- End SEO Meta Tags -->
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00ffff',
                        secondary: '#ff0080',
                        accent: '#00ff41',
                        dark: '#0a0a0a',
                        surface: '#111111',
                        'cyber-cyan': '#00ffff',
                        'cyber-magenta': '#ff0080', 
                        'cyber-green': '#00ff41',
                        'cyber-purple': '#8a2be2',
                        'cyber-orange': '#ff6600',
                        'cyber-blue': '#0080ff',
                        'cyber-dark': '#0a0a0a',
                        'cyber-darker': '#050505',
                        'cyber-gray': '#1a1a1a',
                        'gradient-start': '#00ffff',
                        'gradient-middle': '#ff0080', 
                        'gradient-end': '#00ff41',
                    },
                    fontFamily: {
                        sans: ['Exo 2', 'Inter', 'system-ui', 'sans-serif'],
                        display: ['Audiowide', 'Exo 2', 'system-ui', 'sans-serif'],
                        cyber: ['Space Mono', 'JetBrains Mono', 'Monaco', 'Consolas', 'monospace'],
                        mono: ['Space Mono', 'JetBrains Mono', 'Monaco', 'Consolas', 'monospace'],
                    },
                    animation: {
                        'cyber-float': 'cyber-float 8s ease-in-out infinite',
                        'glitch': 'glitch 2s infinite',
                        'neon-pulse': 'neon-pulse 2s infinite',
                        'scan-line': 'scan-line 3s linear infinite',
                        'matrix-rain': 'matrix-rain 10s linear infinite',
                        'hologram': 'hologram 4s ease-in-out infinite',
                        'data-stream': 'data-stream 15s linear infinite',
                        'cyber-glow': 'cyber-glow 3s ease-in-out infinite alternate',
                        'hex-rotate': 'hex-rotate 20s linear infinite',
                        'rgb-split': 'rgb-split 0.1s ease-in-out infinite',
                        'cyber-zoom': 'cyber-zoom 0.6s ease-out',
                        'terminal-blink': 'terminal-blink 1.5s step-end infinite',
                        'energy-pulse': 'energy-pulse 2s ease-in-out infinite',
                    },
                    screens: {
                        'xs': '375px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                        '2xl': '1536px',
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    boxShadow: {
                        'neon-cyan': '0 0 20px #00ffff, 0 0 40px #00ffff, 0 0 60px #00ffff',
                        'neon-magenta': '0 0 20px #ff0080, 0 0 40px #ff0080, 0 0 60px #ff0080',
                        'neon-green': '0 0 20px #00ff41, 0 0 40px #00ff41, 0 0 60px #00ff41',
                        'cyber-glow': '0 0 30px rgba(0, 255, 255, 0.5), inset 0 0 30px rgba(0, 255, 255, 0.1)',
                        'holo': '0 0 40px rgba(255, 0, 128, 0.6), 0 0 80px rgba(0, 255, 255, 0.3)',
                        'terminal': '0 0 10px #00ff41, inset 0 0 10px rgba(0, 255, 65, 0.2)',
                        'rgb-split': '3px 0 0 #ff0080, -3px 0 0 #00ffff',
                    },
                }
            }
        }
    </script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts - Optimized for Vietnamese -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Exo+2:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Audiowide&subset=vietnamese&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        * {
            font-family: 'Exo 2', 'Inter', system-ui, sans-serif;
        }
        
        .font-cyber {
            font-family: 'Space Mono', 'JetBrains Mono', 'Monaco', 'Consolas', monospace;
        }
        
        .font-display {
            font-family: 'Audiowide', 'Exo 2', system-ui, sans-serif;
        }
        
        /* Enhanced Cyber Grid Background */
        body {
            background: #0a0a0a;
            background-image: 
                linear-gradient(rgba(0, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(rgba(255, 0, 128, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 0, 128, 0.02) 1px, transparent 1px);
            background-size: 40px 40px, 40px 40px, 80px 80px, 80px 80px;
            background-position: 0 0, 0 0, 20px 20px, 20px 20px;
            position: relative;
            font-feature-settings: "liga" 1, "kern" 1;
            text-rendering: optimizeSpeed;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(0, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 0, 128, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0, 255, 65, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }
        
        /* Cyber Animations */
        @keyframes cyber-float {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            50% { transform: translateY(-20px) rotateY(180deg); }
        }
        
        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
        }
        
        @keyframes neon-pulse {
            0%, 100% { 
                text-shadow: 
                    0 0 5px #00ffff, 
                    0 0 10px #00ffff, 
                    0 0 15px #00ffff,
                    0 0 20px #00ffff;
                filter: brightness(1) saturate(1.2);
            }
            50% { 
                text-shadow: 
                    0 0 10px #00ffff, 
                    0 0 20px #00ffff, 
                    0 0 30px #00ffff,
                    0 0 40px #00ffff,
                    0 0 50px #00ffff;
                filter: brightness(1.8) saturate(1.5);
            }
        }
        
        @keyframes scan-line {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100vw); }
        }
        
        @keyframes matrix-rain {
            0% { transform: translateY(-100vh); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
        
        @keyframes hologram {
            0%, 100% { opacity: 0.8; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }
        
        @keyframes terminal-blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        @keyframes rgb-split {
            0% { transform: translate(0); }
            33% { transform: translate(-2px, 0); }
            66% { transform: translate(2px, 0); }
            100% { transform: translate(0); }
        }
        
        @keyframes energy-pulse {
            0% { box-shadow: 0 0 20px #00ffff; }
            50% { box-shadow: 0 0 40px #ff0080, 0 0 60px #00ffff; }
            100% { box-shadow: 0 0 20px #00ffff; }
        }
        
        @keyframes hex-rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(60px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.8) rotate(-5deg); }
            to { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 30px rgba(99, 102, 241, 0.3); }
            to { box-shadow: 0 0 60px rgba(99, 102, 241, 0.6), 0 0 100px rgba(139, 92, 246, 0.3); }
        }
        
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slide-in-left {
            from { opacity: 0; transform: translateX(-50px) scale(0.9); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        
        @keyframes slide-in-right {
            from { opacity: 0; transform: translateX(50px) scale(0.9); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        
        @keyframes zoom-in {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        
        @keyframes rotate-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes particles {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
            100% { transform: translateY(-1400px) rotate(360deg); opacity: 0; }
        }
        
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 100% 0%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
        }
        
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-15px) scale(1.05); }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) skewX(-15deg); }
            100% { transform: translateX(200%) skewX(-15deg); }
        }
        
        /* Modern Glass Morphism */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }
        
        .glass-dark {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        /* Cyber Gradient Backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #00ffff 0%, #ff0080 50%, #00ff41 100%);
            background-size: 400% 400%;
            animation: gradient 8s ease infinite;
        }
        
        .gradient-secondary {
            background: linear-gradient(45deg, #ff0080 0%, #8a2be2 25%, #00ffff 50%, #ff6600 75%, #00ff41 100%);
            background-size: 400% 400%;
            animation: gradient 10s ease infinite;
        }
        
        .gradient-accent {
            background: linear-gradient(90deg, #00ff41 0%, #00ffff 25%, #ff0080 50%, #8a2be2 75%, #ff6600 100%);
            background-size: 400% 400%;
            animation: gradient 12s ease infinite;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #00ffff 0%, #ff0080 25%, #00ff41 50%, #8a2be2 75%, #ff6600 100%);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 6s ease infinite;
        }
        
        .cyber-mesh {
            background: 
                radial-gradient(circle at 20% 20%, #00ffff 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, #ff0080 0%, transparent 40%),
                radial-gradient(circle at 40% 60%, #00ff41 0%, transparent 40%),
                linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            animation: gradient 15s ease infinite;
        }
        
        /* Scanline Effect */
        .scanlines::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffff, transparent);
            animation: scan-line 3s linear infinite;
            z-index: 10;
        }
        
        /* Enhanced Cyber Card Design */
        .card-modern {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(15px);
            border: 2px solid;
            border-image: linear-gradient(45deg, #00ffff, #ff0080, #00ff41, #8a2be2) 1;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 
                0 0 30px rgba(0, 255, 255, 0.4),
                inset 0 0 30px rgba(0, 255, 255, 0.05);
            clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 15px, 100% 100%, 15px 100%, 0 calc(100% - 15px));
            font-family: 'Exo 2', sans-serif;
        }
        
        .card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.2), rgba(255, 0, 128, 0.2), transparent);
            transition: left 0.8s ease;
            z-index: 1;
        }
        
        .card-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    90deg,
                    transparent 0px,
                    rgba(0, 255, 255, 0.03) 1px,
                    transparent 2px,
                    transparent 40px
                );
            pointer-events: none;
            z-index: 2;
        }
        
        .card-modern:hover::before {
            left: 100%;
        }
        
        .card-modern:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 
                0 0 50px rgba(0, 255, 255, 0.6),
                0 0 100px rgba(255, 0, 128, 0.3),
                inset 0 0 30px rgba(0, 255, 255, 0.1);
            border-image: linear-gradient(45deg, #00ffff, #ff0080, #00ff41, #8a2be2) 1;
            animation: glitch 0.3s ease-in-out;
        }
        
        /* Button Styles */
        .btn-modern {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 16px 32px;
            font-weight: 700;
            letter-spacing: 0.02em;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.2), rgba(99, 102, 241, 0.1), rgba(255, 255, 255, 0.2));
            transition: left 0.4s ease;
        }
        
        .btn-modern::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
            border-radius: 50%;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern:hover::after {
            width: 300px;
            height: 300px;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Custom Shapes */
        .blob-modern {
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation: blob-morph 20s ease-in-out infinite;
        }
        
        @keyframes blob-morph {
            0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        
        /* Particles */
        .particles {
            position: absolute;
            width: 8px;
            height: 8px;
            background: linear-gradient(45deg, #6366f1, #8b5cf6, #06b6d4);
            border-radius: 50%;
            opacity: 0.6;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
        }
        
        /* Enhanced Cyber Typing Animation */
        .typing {
            overflow: hidden;
            border-right: 3px solid #00ffff;
            white-space: nowrap;
            animation: typing 4s steps(40, end), cyber-caret 1s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes cyber-caret {
            from, to { 
                border-color: transparent; 
                box-shadow: none;
            }
            50% { 
                border-color: #00ffff; 
                box-shadow: 0 0 5px #00ffff, 0 0 10px #00ffff;
            }
        }
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 5px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4, #10b981);
            z-index: 9999;
            transition: width 0.15s ease;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.2);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 2px 15px rgba(99, 102, 241, 0.4);
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .mobile-padding { padding: 1.25rem !important; }
            .mobile-text { font-size: 0.9rem !important; line-height: 1.6 !important; }
            .mobile-title { font-size: 2.75rem !important; line-height: 1.1 !important; }
            .mobile-hero { padding-top: 7rem !important; padding-bottom: 5rem !important; }
            .mobile-section { padding-top: 4rem !important; padding-bottom: 4rem !important; }
            .mobile-hidden { display: none !important; }
            .mobile-center { text-align: center !important; }
            .mobile-full { width: 100% !important; }
            
            /* Touch optimizations */
            .touch-button {
                min-height: 48px;
                min-width: 48px;
                padding: 14px 24px;
                border-radius: 14px;
                font-weight: 600;
            }
            
            /* Card spacing for mobile */
            .mobile-card {
                margin-bottom: 2rem;
                padding: 2rem;
                border-radius: 20px;
            }
            
            /* Typography scaling */
            h1 { font-size: 2.25rem !important; font-weight: 800 !important; }
            h2 { font-size: 2rem !important; font-weight: 700 !important; }
            h3 { font-size: 1.75rem !important; font-weight: 600 !important; }
            
            /* Animation optimizations for mobile */
            .card-modern:hover {
                transform: none;
            }
            
            /* Reduce blur effects on mobile for performance */
            .glass {
                backdrop-filter: blur(12px);
            }
            
            .glass-dark {
                backdrop-filter: blur(16px);
            }
            
            /* Mobile navigation improvements */
            .mobile-nav-item {
                padding: 1rem 1.5rem;
                border-radius: 12px;
                margin: 0.25rem 0;
                transition: all 0.2s ease;
            }
        }
        
        @media (max-width: 640px) {
            .xs-padding { padding: 1rem !important; }
            .xs-text { font-size: 0.8rem !important; }
            .xs-title { font-size: 2.25rem !important; }
            .xs-hidden { display: none !important; }
            
            /* Extra small mobile optimizations */
            .mobile-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .touch-button {
                padding: 12px 20px;
                min-height: 44px;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .retina-optimized {
                image-rendering: -webkit-optimize-contrast;
            }
        }
        
        /* Landscape mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            .landscape-adjust {
                height: auto !important;
                min-height: auto !important;
                padding-top: 4rem !important;
                padding-bottom: 2rem !important;
            }
        }
        
        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .hover-effect:hover {
                transform: none;
            }
            
            .touch-optimized {
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
            
            /* Touch-friendly button states */
            .btn-modern:active {
                transform: scale(0.98);
            }
        }
        
        /* Modern Back to Top Button */
        #backToTop {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 12px 40px rgba(99, 102, 241, 0.3);
            animation: pulse-glow 4s infinite alternate;
        }
        
        #backToTop.show {
            transform: scale(1) !important;
            opacity: 1;
        }
        
        #backToTop.hide {
            transform: scale(0) !important;
            opacity: 0;
        }
        
        @keyframes pulse-glow {
            0% { 
                box-shadow: 0 12px 40px rgba(99, 102, 241, 0.3), 0 0 0 rgba(99, 102, 241, 0.2);
                transform: scale(1);
            }
            100% { 
                box-shadow: 0 20px 60px rgba(99, 102, 241, 0.5), 0 0 40px rgba(139, 92, 246, 0.3);
                transform: scale(1.05);
            }
        }
        
        /* Mobile optimizations for back to top */
        @media (max-width: 768px) {
            #backToTop {
                width: 52px;
                height: 52px;
                bottom: 1.5rem;
                right: 1.5rem;
            }
        }
        
        /* Cyber Mobile Menu */
        .mobile-menu {
            position: relative;
            z-index: 50;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(0, 255, 255, 0.3);
            box-shadow: 
                0 0 30px rgba(0, 255, 255, 0.3),
                inset 0 0 30px rgba(0, 255, 255, 0.05),
                0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-nav-item:hover {
            background: linear-gradient(135deg, #00ffff, #ff0080);
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.4);
        }
        
        .mobile-nav-item:hover i {
            color: white !important;
            transform: scale(1.1);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }
        
        .mobile-nav-item:hover span {
            color: white !important;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        
        /* Mobile menu animation */
        @media (max-width: 768px) {
            .mobile-menu {
                animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            ::-webkit-scrollbar-track {
                background: #1a1a1a;
            }
        }
        
        /* Reduced motion accessibility */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* FAQ Smooth Animations */
        .faq-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .faq-content.hidden {
            max-height: 0;
            opacity: 0;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        
        .faq-content:not(.hidden) {
            max-height: 200px;
            opacity: 1;
        }
        
        .faq-icon {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .faq-trigger:hover .faq-icon {
            transform: scale(1.1);
        }
        
        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
        <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?=$CMSNT->site('title')?>",
      "url": "<?=base_url()?>",
      "logo": "<?=BASE_URL($CMSNT->site('logo_dark'))?>",
      "description": "<?=$CMSNT->site('description')?>",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?=$CMSNT->site('hotline')?>",
        "contactType": "Customer Support",
        "email": "<?=$CMSNT->site('email')?>"
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?=$CMSNT->site('address')?>"
      }
    }
    </script>
    <?=$CMSNT->site('javascript_header');?>
</head>
<body class="bg-cyber-dark text-cyber-cyan overflow-x-hidden touch-optimized relative">

    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator"></div>

    <!-- Modern Back to Top Button -->
    <button id="backToTop" class="fixed bottom-6 right-6 w-14 h-14 text-white rounded-2xl shadow-2xl hover:shadow-glow-lg transform scale-0 transition-all duration-300 z-50 flex items-center justify-center group hover:scale-110">
        <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
    </button>

    <!-- Enhanced Particles Background -->
    <div id="particles-container" class="fixed inset-0 pointer-events-none z-0 mobile-hidden"></div>

    <!-- Modern Header -->
    <header class="fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <nav class="border-b border-white/20">
            <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-5">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <a href="<?=base_url();?>" class="d-inline-block auth-logo group">    
                            <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=__('SMM Panel Pro Logo')?>" class="h-9 sm:h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                        </a>
                    </div>
                    
                    <div class="hidden lg:flex items-center space-x-10">
                        <a href="#home" class="nav-link relative text-white hover:text-accent transition-all duration-300 group font-medium">
                            <?=__('Trang Chủ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-accent to-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="#services" class="nav-link relative text-white hover:text-accent transition-all duration-300 group font-medium">
                            <?=__('Dịch Vụ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-accent to-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="#faq" class="nav-link relative text-white hover:text-accent transition-all duration-300 group font-medium">
                            <?=__('FAQ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-accent to-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="<?=base_url('client/services')?>" class="nav-link relative text-white hover:text-accent transition-all duration-300 group font-medium">
                            <?=__('Bảng Giá')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-accent to-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="<?=base_url('client/contact');?>" class="nav-link relative text-white hover:text-accent transition-all duration-300 group font-medium">
                            <?=__('Liên Hệ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-accent to-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <a href="<?=base_url('client/register')?>" class="hidden sm:block btn-modern px-5 sm:px-7 py-2.5 border-2 border-cyber-magenta/60 text-cyber-magenta rounded-2xl hover:bg-cyber-magenta hover:text-black transition-all duration-300 touch-button backdrop-blur-sm hover:shadow-neon-magenta">
                            <?=__('Đăng Ký')?>
                        </a>
                        <a href="<?=base_url('client/login')?>" class="hidden sm:block btn-modern px-5 sm:px-7 py-2.5 bg-cyber-cyan/20 text-cyber-cyan border-2 border-cyber-cyan/50 rounded-2xl hover:bg-cyber-cyan hover:text-black hover:shadow-neon-cyan transition-all duration-300 touch-button backdrop-blur-sm font-semibold">
                            <?=__('Đăng Nhập')?>
                        </a>
                        <button class="lg:hidden mobile-menu-btn p-3 touch-button rounded-xl bg-white/10 backdrop-blur-sm border border-white/20" id="mobileMenuBtn">
                            <i class="fas fa-bars text-xl text-white transition-transform duration-300"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Enhanced Cyber Mobile Menu -->
                <div class="lg:hidden mobile-menu hidden mt-6 pb-4 bg-cyber-dark/95 backdrop-blur-xl rounded-2xl shadow-2xl mx-4 border-2 border-cyber-cyan/30" id="mobileMenu" style="box-shadow: 0 0 30px rgba(0, 255, 255, 0.3), inset 0 0 30px rgba(0, 255, 255, 0.05);">
                    <div class="py-3">
                        <a href="#home" class="mobile-nav-item flex items-center px-6 py-4 text-white/90 hover:bg-gradient-to-r hover:from-cyber-cyan hover:to-cyber-magenta hover:text-white transition-all duration-300 rounded-xl mx-3 hover:shadow-neon-cyan">
                            <div class="w-10 h-10 rounded-lg bg-cyber-cyan/20 border border-cyber-cyan/30 flex items-center justify-center mr-4">
                                <i class="fas fa-home text-cyber-cyan transition-colors duration-300"></i>
                            </div>
                            <span class="font-semibold"><?=__('Trang Chủ')?></span>
                        </a>
                        
                        <a href="#services" class="mobile-nav-item flex items-center px-6 py-4 text-white/90 hover:bg-gradient-to-r hover:from-cyber-cyan hover:to-cyber-magenta hover:text-white transition-all duration-300 rounded-xl mx-3 hover:shadow-neon-cyan">
                            <div class="w-10 h-10 rounded-lg bg-cyber-magenta/20 border border-cyber-magenta/30 flex items-center justify-center mr-4">
                                <i class="fas fa-cogs text-cyber-magenta transition-colors duration-300"></i>
                            </div>
                            <span class="font-semibold"><?=__('Dịch Vụ')?></span>
                        </a>
                        
                        <a href="#faq" class="mobile-nav-item flex items-center px-6 py-4 text-white/90 hover:bg-gradient-to-r hover:from-cyber-cyan hover:to-cyber-magenta hover:text-white transition-all duration-300 rounded-xl mx-3 hover:shadow-neon-cyan">
                            <div class="w-10 h-10 rounded-lg bg-cyber-green/20 border border-cyber-green/30 flex items-center justify-center mr-4">
                                <i class="fas fa-question-circle text-cyber-green transition-colors duration-300"></i>
                            </div>
                            <span class="font-semibold"><?=__('FAQ')?></span>
                        </a>
                        
                        <a href="<?=base_url('client/services')?>" class="mobile-nav-item flex items-center px-6 py-4 text-white/90 hover:bg-gradient-to-r hover:from-cyber-cyan hover:to-cyber-magenta hover:text-white transition-all duration-300 rounded-xl mx-3 hover:shadow-neon-cyan">
                            <div class="w-10 h-10 rounded-lg bg-cyber-purple/20 border border-cyber-purple/30 flex items-center justify-center mr-4">
                                <i class="fas fa-tags text-cyber-purple transition-colors duration-300"></i>
                            </div>
                            <span class="font-semibold"><?=__('Bảng Giá')?></span>
                        </a>
                        
                        <a href="<?=base_url('client/contact')?>" class="mobile-nav-item flex items-center px-6 py-4 text-white/90 hover:bg-gradient-to-r hover:from-cyber-cyan hover:to-cyber-magenta hover:text-white transition-all duration-300 rounded-xl mx-3 hover:shadow-neon-cyan">
                            <div class="w-10 h-10 rounded-lg bg-cyber-orange/20 border border-cyber-orange/30 flex items-center justify-center mr-4">
                                <i class="fas fa-headset text-cyber-orange transition-colors duration-300"></i>
                            </div>
                            <span class="font-semibold"><?=__('Liên Hệ')?></span>
                        </a>
                    </div>
                    
                    <div class="p-6 border-t border-cyber-cyan/30 space-y-3">
                        <a href="<?=base_url('client/login')?>" class="w-full flex items-center justify-center px-6 py-3 border-2 border-cyber-cyan text-cyber-cyan rounded-xl hover:bg-cyber-cyan hover:text-black transition-all duration-300 font-semibold hover:shadow-neon-cyan">
                            <i class="fas fa-sign-in-alt mr-3"></i>
                            <?=__('Đăng Nhập')?>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-cyber-cyan to-cyber-magenta text-white rounded-xl hover:shadow-neon-magenta hover:scale-105 transition-all duration-300 font-semibold">
                            <i class="fas fa-user-plus mr-3"></i>
                            <?=__('Đăng Ký')?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Cyber Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center bg-cyber-dark overflow-hidden mobile-hero landscape-adjust scanlines">
        <!-- Enhanced Animated Background Elements -->
        <div class="absolute inset-0 mobile-hidden">
            <div class="absolute top-20 left-10 w-80 h-80 bg-white/8 rounded-full blur-3xl animate-float blob-modern"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent/10 rounded-full blur-3xl animate-float blob-modern" style="animation-delay: -3s;"></div>
            <div class="absolute top-1/2 left-1/2 w-72 h-72 bg-secondary/8 rounded-full blur-3xl animate-float blob-modern" style="animation-delay: -6s;"></div>
            <div class="absolute top-10 right-20 w-40 h-40 bg-primary/10 rounded-full blur-2xl animate-float" style="animation-delay: -1s;"></div>
            <div class="absolute bottom-32 left-20 w-60 h-60 bg-white/5 rounded-full blur-3xl animate-float" style="animation-delay: -4s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 py-16 sm:py-24 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="text-white mobile-center lg:text-left">
                    <div class="inline-flex items-center px-4 sm:px-6 py-3 bg-white/15 backdrop-blur-sm rounded-full text-sm sm:text-base mb-6 sm:mb-8 border border-white/20" data-aos="fade-up">
                        <span class="w-3 h-3 bg-accent rounded-full mr-3 animate-pulse-slow"></span>
                        🚀 <?=__('SMM Panel Uy Tín #1 Việt Nam')?>
                    </div>
                    
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black mb-6 sm:mb-8 leading-tight mobile-title font-display" data-aos="fade-up" data-aos-delay="100">
                        <span class="block text-cyber-cyan animate-neon-pulse" style="text-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff, 0 0 30px #00ffff;"><?=__('TĂNG TƯƠNG TÁC')?></span>
                        <span class="typing gradient-text text-cyber-magenta animate-glitch font-cyber" style="text-shadow: 0 0 10px #ff0080, 0 0 20px #ff0080, 0 0 30px #ff0080; letter-spacing: 2px;"><?=__('MẠNG XÃ HỘI')?></span>
                    </h1>
                    
                    <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-white/90 leading-relaxed mobile-text" data-aos="fade-up" data-aos-delay="200">
                        <?=__('Nền tảng SMM Panel chuyên nghiệp với')?> <span class="text-yellow-400 font-semibold"><?=__('hơn 3000+ dịch vụ')?></span> 
                        <?=__('cho tất cả các nền tảng mạng xã hội. Tăng followers, likes, views')?> <span class="text-yellow-400 font-semibold"><?=__('nhanh chóng & an toàn')?></span>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 mb-12 sm:mb-16" data-aos="fade-up" data-aos-delay="300">
                        <a href="<?=base_url('client/login')?>" class="group relative btn-modern px-8 sm:px-10 py-4 sm:py-5 bg-gradient-to-r from-cyber-cyan to-cyber-blue text-black rounded-2xl font-bold text-lg sm:text-xl hover:shadow-neon-cyan transition-all duration-300 hover:scale-105 touch-button mobile-full sm:w-auto flex items-center justify-center overflow-hidden border-2 border-cyber-cyan/50">
                            <span class="relative z-10 flex items-center font-cyber">
                                <i class="fas fa-sign-in-alt mr-3"></i> 
                                <?=__('ĐĂNG NHẬP NGAY')?>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-cyber-magenta to-cyber-purple opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="group btn-modern px-8 sm:px-10 py-4 sm:py-5 border-2 border-cyber-magenta text-cyber-magenta rounded-2xl font-bold text-lg sm:text-xl hover:bg-cyber-magenta hover:text-black transition-all duration-300 flex items-center justify-center touch-button mobile-full sm:w-auto backdrop-blur-sm hover:shadow-neon-magenta">
                            <i class="fas fa-user-plus mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="font-cyber"><?=__('ĐĂNG KÝ NGAY')?></span>
                        </a>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="glass-card rounded-2xl p-4 sm:p-6 text-center">
                                                            <div class="text-2xl sm:text-3xl font-black text-white mb-2 font-cyber"><?=__('15K+')?></div>
                                <div class="text-sm sm:text-base text-white/80"><?=__('Khách hàng')?></div>
                        </div>
                        <div class="glass-card rounded-2xl p-4 sm:p-6 text-center">
                            <div class="text-2xl sm:text-3xl font-black text-white mb-2 font-cyber"><?=__('3000+')?></div>
                            <div class="text-sm sm:text-base text-white/80"><?=__('Dịch vụ')?></div>
                        </div>
                        <div class="glass-card rounded-2xl p-4 sm:p-6 text-center col-span-2 sm:col-span-1">
                            <div class="text-2xl sm:text-3xl font-black text-white mb-2 font-cyber"><?=__('99.9%')?></div>
                            <div class="text-sm sm:text-base text-white/80"><?=__('Uptime')?></div>
                        </div>
                    </div>
                </div>
                
                <div class="relative order-first lg:order-last mb-12 lg:mb-0" data-aos="fade-left" data-aos-delay="500">
                    <!-- Enhanced Dashboard Preview -->
                    <div class="relative">
                        <div class="glass-card rounded-3xl sm:rounded-[2rem] p-2 animate-glow">
                            <img src="<?=base_url('assets/img/homepage-item1.webp')?>" 
                                 alt="<?=__('SMM Panel Dashboard')?>" 
                                 class="rounded-2xl sm:rounded-3xl shadow-2xl w-full transform hover:scale-105 transition-transform duration-700 retina-optimized">
                        </div>
                        
                        <!-- Enhanced Floating Elements -->
                        <div class="absolute -top-4 sm:-top-8 -right-4 sm:-right-8 glass-card rounded-2xl sm:rounded-3xl p-3 sm:p-6 animate-float mobile-hidden sm:block">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="w-3 sm:w-4 h-3 sm:h-4 bg-accent rounded-full animate-pulse-slow"></div>
                                <div>
                                    <div class="text-white text-sm sm:text-base font-bold"><?=__('99.9% Uptime')?></div>
                                    <div class="text-white/70 text-xs sm:text-sm"><?=__('Hệ thống ổn định')?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-4 sm:-bottom-8 -left-4 sm:-left-8 glass-card rounded-2xl sm:rounded-3xl p-3 sm:p-6 animate-float mobile-hidden sm:block" style="animation-delay: -3s;">
                            <div class="text-white">
                                <div class="text-xl sm:text-3xl font-black text-accent font-cyber"><?=__('2M+');?></div>
                                <div class="text-xs sm:text-sm text-white/80 font-medium"><?=__('Đơn hàng hoàn thành')?></div>
                            </div>
                        </div>
                        
                        <div class="absolute top-1/4 -left-6 sm:-left-12 glass-card rounded-xl sm:rounded-2xl p-2 sm:p-4 animate-float mobile-hidden" style="animation-delay: -5s;">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <i class="fas fa-bolt text-accent text-lg sm:text-xl"></i>
                                <div class="text-white text-xs sm:text-sm font-semibold"><?=__('Tự động')?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Scroll Indicator -->
        <div class="absolute bottom-6 sm:bottom-12 left-1/2 transform -translate-x-1/2 text-white animate-bounce-subtle mobile-hidden">
            <div class="flex flex-col items-center">
                <span class="text-sm sm:text-base mb-3 opacity-90 font-medium"><?=__('Scroll để khám phá')?></span>
                <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white rounded-full mt-2 animate-bounce"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cyber Services Section -->
    <section id="services" class="py-16 sm:py-20 lg:py-28 bg-cyber-dark relative overflow-hidden mobile-section scanlines">
        <div class="absolute inset-0 bg-gradient-to-br from-cyber-cyan/10 via-cyber-purple/5 to-cyber-magenta/10"></div>
        
        <div class="container mx-auto px-4 sm:px-6 relative">
            <div class="text-center mb-16 sm:mb-20 lg:mb-24">
                <div class="inline-flex items-center px-4 sm:px-6 py-3 bg-cyber-cyan/20 backdrop-blur-sm rounded-full text-cyber-cyan text-sm sm:text-base font-semibold mb-4 sm:mb-6 border border-cyber-cyan/30" data-aos="fade-up" style="box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);">
                    <i class="fas fa-star mr-3 text-lg animate-neon-pulse"></i>
                    <?=__('Dịch Vụ SMM Chuyên Nghiệp')?>
                </div>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black mb-6 sm:mb-8 gradient-text mobile-title font-display" data-aos="fade-up" data-aos-delay="100" style="letter-spacing: 1px;">
                    <?=__('GIẢI PHÁP SMM TOÀN DIỆN')?>
                </h2>
                <p class="text-lg sm:text-2xl text-white/90 max-w-4xl mx-auto mobile-text font-light leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    <?=__('Từ tăng followers, likes, views đến quản lý chiến dịch marketing - chúng tôi cung cấp mọi dịch vụ bạn cần để phát triển mạnh mẽ trên social media')?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-10">
                <!-- Facebook Service -->
                <div class="card-modern text-center group mobile-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-8 sm:mb-10 mt-4 sm:mt-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl sm:rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glow">
                        <i class="fab fa-facebook-f text-white text-3xl sm:text-4xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-4 sm:mb-6 font-display"><?=__('Facebook Marketing')?></h3>
                    <p class="text-white/80 mb-6 sm:mb-8 text-base sm:text-lg mobile-text leading-relaxed text-center"><?=__('Tăng like fanpage, like bài viết, share, comment và follow với người dùng Việt Nam thật 100%')?></p>
                    <div class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Like Fanpage & Bài Viết')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Follow & Comment Tương Tác')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Share & Reaction Đa Dạng')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Instagram Service -->
                <div class="card-modern text-center group mobile-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-8 sm:mb-10 mt-4 sm:mt-6 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl sm:rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glow">
                        <i class="fab fa-instagram text-white text-3xl sm:text-4xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-4 sm:mb-6 font-display"><?=__('Instagram Growth')?></h3>
                    <p class="text-white/80 mb-6 sm:mb-8 text-base sm:text-lg mobile-text leading-relaxed text-center"><?=__('Tăng followers, likes, views story, saves và comments Instagram với chất lượng cao và tốc độ nhanh')?></p>
                    <div class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Followers & Likes Chất Lượng')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Story Views & Saves')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Reels & IGTV Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- YouTube Service -->
                <div class="card-modern text-center group mobile-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-8 sm:mb-10 mt-4 sm:mt-6 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl sm:rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glow">
                        <i class="fab fa-youtube text-white text-3xl sm:text-4xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-4 sm:mb-6 font-display"><?=__('YouTube Optimization')?></h3>
                    <p class="text-white/80 mb-6 sm:mb-8 text-base sm:text-lg mobile-text leading-relaxed text-center"><?=__('Tăng subscriber, views, watch time và likes YouTube để đạt điều kiện kiếm tiền và phát triển kênh')?></p>
                    <div class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Subscriber & Views Thật')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Watch Time 4000 Giờ')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('YouTube Shorts Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- TikTok Service -->
                <div class="card-modern text-center group mobile-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-8 sm:mb-10 mt-4 sm:mt-6 bg-gradient-to-r from-gray-800 to-black rounded-2xl sm:rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glow">
                        <i class="fab fa-tiktok text-white text-3xl sm:text-4xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-4 sm:mb-6 font-display"><?=__('TikTok Viral')?></h3>
                    <p class="text-white/80 mb-6 sm:mb-8 text-base sm:text-lg mobile-text leading-relaxed text-center"><?=__('Tăng followers, likes, views và shares TikTok để video viral và tăng độ phủ sóng trên For You Page')?></p>
                    <div class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Followers & Likes TikTok')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Views & Shares Viral')?></span>
                        </div>
                        <div class="flex items-center justify-center text-white/90 text-sm sm:text-base">
                            <i class="fas fa-check text-cyber-green mr-3 sm:mr-4 text-lg"></i>
                            <span class="font-medium"><?=__('Live Stream Support')?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cyber FAQ Section -->
    <section id="faq" class="py-16 sm:py-20 lg:py-28 bg-cyber-darker text-white mobile-section relative overflow-hidden scanlines">
        <div class="absolute inset-0 bg-gradient-to-br from-cyber-cyan/8 via-cyber-purple/3 to-cyber-magenta/8"></div>
        
        <div class="container mx-auto px-4 sm:px-6 relative">
            <div class="text-center mb-16 sm:mb-20 lg:mb-24">
                <div class="inline-flex items-center px-4 sm:px-6 py-3 bg-cyber-magenta/20 backdrop-blur-sm rounded-full text-cyber-magenta text-sm sm:text-base font-semibold mb-4 sm:mb-6 border border-cyber-magenta/30" data-aos="fade-up" style="box-shadow: 0 0 20px rgba(255, 0, 128, 0.3);">
                    <i class="fas fa-question-circle mr-3 text-lg animate-neon-pulse"></i>
                    <?=__('Hỗ Trợ Khách Hàng')?>
                </div>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black mb-6 sm:mb-8 gradient-text mobile-title font-display" data-aos="fade-up" data-aos-delay="100" style="letter-spacing: 1px;">
                    <?=__('CÂU HỎI THƯỜNG GẶP')?>
                </h2>
                <p class="text-lg sm:text-2xl text-white/90 max-w-4xl mx-auto mobile-text font-light leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    <?=__('Tìm hiểu thêm về dịch vụ SMM Panel của chúng tôi qua những câu hỏi phổ biến nhất từ khách hàng.')?>
                </p>
            </div>

            <div class="max-w-5xl mx-auto space-y-6" data-aos="fade-up" data-aos-delay="300">
                <!-- FAQ Item 1 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq1">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('SMM Panel là gì và hoạt động như thế nào?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq1">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('SMM Panel là nền tảng cung cấp dịch vụ marketing mạng xã hội tự động. Bạn chỉ cần đặt hàng với link bài viết/profile, hệ thống sẽ tự động tăng followers, likes, views, comments... cho tài khoản của bạn một cách nhanh chóng và an toàn.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq2">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('Thời gian giao hàng bao lâu?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq2">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('Thời gian giao hàng phụ thuộc vào từng dịch vụ: Likes/Followers thường trong vòng 5-30 phút, Views trong vòng 1-6 giờ, Comments trong vòng 30 phút - 2 giờ. Tất cả đều được giao từ từ và tự nhiên để đảm bảo an toàn tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq3">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('Dịch vụ có an toàn cho tài khoản không?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq3">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('Hoàn toàn an toàn! Chúng tôi sử dụng công nghệ tiên tiến để mô phỏng tương tác tự nhiên. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ hơn 15,000+ khách hàng mà không có trường hợp nào bị khóa tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq4">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('Có chính sách bảo hành và hoàn tiền không?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq4">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('Có! Chúng tôi bảo hành 30-90 ngày tùy dịch vụ. Nếu số lượng giảm, chúng tôi sẽ refill miễn phí. Hoàn tiền 100% nếu không giao được hàng sau 24h. Chính sách rõ ràng, minh bạch, uy tín hàng đầu Việt Nam.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq5">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('Các phương thức thanh toán được hỗ trợ?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq5">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('Hỗ trợ đa dạng: Chuyển khoản ngân hàng, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào điện thoại, Bitcoin và các loại coin khác. Nạp tiền tự động 24/7, xử lý trong vòng 1-5 phút.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="card-modern overflow-hidden transition-all duration-300 faq-item">
                    <button class="w-full px-8 py-8 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq6">
                        <h3 class="text-xl font-bold text-white pr-4"><?=__('Làm sao để bắt đầu sử dụng dịch vụ?')?></h3>
                        <i class="fas fa-chevron-down text-cyber-cyan transition-transform duration-300 text-xl flex-shrink-0 faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-8" id="faq6">
                        <div class="pt-4 border-t border-cyber-cyan/30">
                            <p class="text-white/80 leading-relaxed text-lg">
                                <?=__('Rất đơn giản! 1) Đăng ký tài khoản miễn phí 2) Nạp tiền vào tài khoản 3) Chọn dịch vụ phù hợp 4) Nhập link bài viết/profile 5) Đặt hàng và chờ kết quả. Hỗ trợ 24/7 qua Telegram/Zalo nếu cần.')?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-slate-900 text-white pt-16 pb-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 mobile-hidden">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 relative">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
                <!-- Company Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?=base_url($CMSNT->site('logo_dark'));?>" alt="<?=$CMSNT->site('title');?>">
                    </div>
                    <p class="text-gray-400 text-sm">
                        <?=$CMSNT->site('description');?>
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4"><?=__('Liên Kết Nhanh')?></h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="#home" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=__('Trang Chủ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#services" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=__('Dịch Vụ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#faq" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=__('FAQ')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/services');?>" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=__('Bảng Giá')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=__('Liên Hệ')?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-lg font-semibold mb-4"><?=__('Dịch Vụ')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC") as $category):?>
                        <li>
                            <a href="<?=base_url('service/'.$category['slug']);?>" class="text-gray-400 hover:text-primary transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <?=$category['name'];?>
                            </a>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4"><?=__('Liên Hệ')?></h4>
                    <ul class="space-y-3">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <span class="text-gray-400"><?=$CMSNT->site('address')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-primary"></i>
                            <span class="text-gray-400"><?=$CMSNT->site('hotline')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <span class="text-gray-400"><?=$CMSNT->site('email')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-primary"></i>
                            <span class="text-gray-400"><?=__('Hỗ trợ 24/7 - Phản hồi trong 5 phút')?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?>
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-primary transition-colors duration-300 text-sm">
                            <?=__('Liên Hệ')?>
                        </a>
                        <a href="<?=base_url('client/policy');?>" class="text-gray-400 hover:text-primary transition-colors duration-300 text-sm">
                            <?=__('Chính Sách')?>
                        </a>
                        <a href="<?=base_url('client/privacy');?>" class="text-gray-400 hover:text-primary transition-colors duration-300 text-sm">
                            <?=__('Bảo Mật')?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS with mobile optimization
        AOS.init({
            duration: 600, // Shorter duration for mobile
            easing: 'ease-out',
            once: true,
            offset: 50, // Lower offset for mobile
            disable: function() {
                return /Mobile|Android/i.test(navigator.userAgent) && window.innerWidth < 768 ? 'mobile' : false;
            }
        });

        $(document).ready(function() {
            // Mobile detection
            const isMobile = /Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            
            // Initialize mobile menu state
            const $mobileMenu = $('#mobileMenu');
            const $mobileMenuContent = $('.mobile-menu-content');
            $mobileMenu.addClass('hidden');
            
            const $navbar = $('#navbar');
            // Set initial navbar style
            $navbar.css({
                'background': 'rgba(10, 10, 10, 0.4)', // dark with 40% opacity
                'backdrop-filter': 'blur(16px)',
                'box-shadow': 'none'
            });
            
            // Mobile-specific optimizations
            if (isMobile) {
                $('body').addClass('is-mobile');
                // Disable particles on mobile for performance
                $('#particles-container').hide();
                // Reduce animation complexity
                $('.animate-glow').removeClass('animate-glow');
                // Reduce blur effects
                $('.glass').css('backdrop-filter', 'blur(8px)');
            }

            // Mobile Menu Toggle (Simplified)
            $('#mobileMenuBtn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $menu = $('#mobileMenu');
                const $icon = $(this).find('i');
                
                if ($menu.hasClass('hidden')) {
                    // Show menu
                    $menu.removeClass('hidden').show();
                    $icon.removeClass('fa-bars').addClass('fa-times');
                } else {
                    // Hide menu
                    $menu.addClass('hidden').hide();
                    $icon.removeClass('fa-times').addClass('fa-bars');
                }
            });

            // Close mobile menu when clicking nav items
            $('.mobile-nav-item').on('click', function() {
                const $menu = $('#mobileMenu');
                const $icon = $('#mobileMenuBtn').find('i');
                
                $menu.addClass('hidden').hide();
                $icon.removeClass('fa-times').addClass('fa-bars');
            });

            // Close mobile menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#navbar').length) {
                    const $menu = $('#mobileMenu');
                    const $icon = $('#mobileMenuBtn').find('i');
                    
                    $menu.addClass('hidden').hide();
                    $icon.removeClass('fa-times').addClass('fa-bars');
                }
            });

            // Scroll Progress Indicator
            let ticking = false;
            
            function updateScrollProgress() {
                const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                $('.scroll-indicator').css('width', Math.min(scrolled, 100) + '%');
                
                // Toggle back to top button
                toggleBackToTop();
                
                // Navbar background on scroll
                const $navbar = $('#navbar');

                if ($(window).scrollTop() > 50) {
                    if (!$navbar.hasClass('scrolled')) {
                        $navbar.addClass('scrolled').css({
                            'background': 'rgba(10, 10, 10, 0.8)', // dark with 80% opacity for scrolled state
                            'backdrop-filter': 'blur(16px)',
                            'box-shadow': '0 4px 20px rgba(0, 0, 0, 0.1)'
                        });
                    }
                } else {
                    if ($navbar.hasClass('scrolled')) {
                        $navbar.removeClass('scrolled').css({
                            'background': 'rgba(10, 10, 10, 0.4)', // Back to initial dark with 40% opacity
                            'backdrop-filter': 'blur(16px)',
                            'box-shadow': 'none'
                        });
                    }
                }
                
                ticking = false;
            }

            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateScrollProgress);
                    ticking = true;
                }
            }

            $(window).on('scroll', requestTick);

            // Particles Animation (only for desktop)
            if (!isMobile) {
                let particleCount = 0;
                const maxParticles = 20;
                
                function createParticle() {
                    if (particleCount >= maxParticles) return;
                    
                    const particle = $('<div class="particles"></div>');
                    const size = Math.random() * 3 + 2;
                    const posX = Math.random() * window.innerWidth;
                    const duration = Math.random() * 4 + 3;
                    
                    particle.css({
                        left: posX + 'px',
                        width: size + 'px',
                        height: size + 'px',
                        animationDuration: duration + 's'
                    });
                    
                    $('#particles-container').append(particle);
                    particleCount++;
                    
                    setTimeout(() => {
                        particle.remove();
                        particleCount--;
                    }, duration * 1000);
                }

                // Create particles less frequently on mobile
                const particleInterval = isMobile ? 1000 : 400;
                setInterval(createParticle, particleInterval);
            }

            // FAQ Accordion functionality
            $('.faq-trigger').on('click', function(e) {
                e.preventDefault();
                const targetId = $(this).data('target');
                const content = $('#' + targetId);
                const icon = $(this).find('.faq-icon');
                const isOpen = !content.hasClass('hidden');
                
                // Close all other FAQ items
                $('.faq-content').not(content).addClass('hidden');
                $('.faq-icon').not(icon).removeClass('fa-chevron-up').addClass('fa-chevron-down').css('transform', 'rotate(0deg)');
                
                // Toggle current item
                if (isOpen) {
                    content.addClass('hidden');
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down').css('transform', 'rotate(0deg)');
                } else {
                    content.removeClass('hidden');
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up').css('transform', 'rotate(180deg)');
                }
            });

            // Smooth scrolling for navigation with mobile optimization
            $('a[href^="#"]').on('click', function(event) {
                event.preventDefault();
                const targetId = this.getAttribute('href');
                const target = $(targetId);
                
                if (target.length) {
                    const headerHeight = $('#navbar').outerHeight() || 80;
                    const targetPosition = target.offset().top - headerHeight;
                    
                    // Use native smooth scroll if supported, otherwise fallback to jQuery
                    if ('scrollBehavior' in document.documentElement.style && !isMobile) {
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    } else {
                        $('html, body').animate({
                            scrollTop: targetPosition
                        }, isMobile ? 300 : 800, 'easeInOutCubic');
                    }
                }
            });

            // Enhanced Cyber Typing effect with Vietnamese optimization
            const typingText = "<?=__('MẠNG XÃ HỘI')?>";
            let typingIndex = 0;

            function typeWriter() {
                const $typingElement = $('.typing');
                if (!$typingElement.length) return;

                if (typingIndex <= typingText.length) {
                    $typingElement.text(typingText.substring(0, typingIndex));
                    typingIndex++;
                    setTimeout(typeWriter, 100); // Faster typing for cyber effect
                } else {
                    setTimeout(() => {
                        typingIndex = 0;
                        $typingElement.text('');
                        setTimeout(typeWriter, 1000);
                    }, 2500); // Longer pause to read Vietnamese text
                }
            }
            typeWriter();

            // Touch optimization for buttons
            if (isTouch) {
                $('.touch-button').on('touchstart', function() {
                    $(this).addClass('active');
                }).on('touchend touchcancel', function() {
                    $(this).removeClass('active');
                });
            }

            // Lazy loading for images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                imageObserver.unobserve(img);
                            }
                        }
                    });
                });

                $('img[data-src]').each(function() {
                    imageObserver.observe(this);
                });
            }

            // Performance monitoring
            if (window.performance && window.performance.mark) {
                window.performance.mark('page-interactive');
            }

            // Orientation change handler
            $(window).on('orientationchange', function() {
                setTimeout(() => {
                    // Recalculate positions after orientation change
                    AOS.refresh();
                    
                    // Close mobile menu if open
                    $mobileMenu.addClass('hidden').hide();
                }, 500);
            });

            // Viewport height fix for mobile browsers
            function setVH() {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }
            
            setVH();
            $(window).on('resize orientationchange', setVH);

            // Performance optimization: Debounced resize handler
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    AOS.refresh();
                    setVH();
                }, 150);
            });

            // Add CSS for active touch state
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .touch-button.active {
                        transform: scale(0.95);
                        opacity: 0.8;
                    }
                    
                    .is-mobile .card-modern:hover {
                        transform: none;
                    }
                    
                    @media (max-width: 768px) {
                        .min-h-screen {
                            min-height: calc(var(--vh, 1vh) * 100);
                        }
                    }
                    
                    .scale-95 {
                        transform: scale(0.95) !important;
                    }
                    
                    #backToTop:hover {
                        animation-play-state: paused;
                        box-shadow: 0 0 40px rgba(0, 102, 255, 0.8), 0 0 80px rgba(0, 102, 255, 0.4) !important;
                    }
                `)
                .appendTo('head');

            // Back to Top Button functionality
            const $backToTopBtn = $('#backToTop');
            let backToTopVisible = false;

            // Show/hide back to top button based on scroll position
            function toggleBackToTop() {
                const scrollTop = $(window).scrollTop();
                const shouldShow = scrollTop > 300; // Show after scrolling 300px

                if (shouldShow && !backToTopVisible) {
                    $backToTopBtn.removeClass('hide').addClass('show');
                    backToTopVisible = true;
                } else if (!shouldShow && backToTopVisible) {
                    $backToTopBtn.removeClass('show').addClass('hide');
                    backToTopVisible = false;
                }
            }

            // Smooth scroll to top when clicked
            $backToTopBtn.on('click', function(e) {
                e.preventDefault();
                
                // Add click effect
                $(this).addClass('scale-95');
                setTimeout(() => {
                    $(this).removeClass('scale-95');
                }, 150);

                // Smooth scroll to top
                if ('scrollBehavior' in document.documentElement.style && !isMobile) {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    $('html, body').animate({
                        scrollTop: 0
                    }, isMobile ? 600 : 1000, 'easeOutCubic');
                }
            });

            console.log('<?=__('SMM Panel Pro')?> - Landing page loaded successfully!');
        });
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html>

 

