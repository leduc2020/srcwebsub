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
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#f59e0b',
                        dark: '#0f172a',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slide-up 0.6s ease-out',
                        'scale-in': 'scale-in 0.5s ease-out',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'particles': 'particles 20s linear infinite',
                    },
                    screens: {
                        'xs': '375px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                        '2xl': '1536px',
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        * {
            font-family: 'Inter', 'Poppins', sans-serif;
        }
        
        /* Custom Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(-10px) rotate(-1deg); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(60px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
            to { box-shadow: 0 0 40px rgba(99, 102, 241, 0.8), 0 0 60px rgba(139, 92, 246, 0.4); }
        }
        
        @keyframes particles {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }
        
        /* Glass Morphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-dark {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Gradient Backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .gradient-secondary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom Shapes */
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob-spin 20s linear infinite;
        }
        
        @keyframes blob-spin {
            0% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        
        /* Particles */
        .particles {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
        }
        
        /* Hover Effects */
        .card-premium {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
        }
        
        .card-premium:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(99, 102, 241, 0.3);
        }
        
        /* Loading Animation */
        .typing {
            overflow: hidden;
            border-right: 3px solid #667eea;
            white-space: nowrap;
            animation: typing 3.5s steps(30, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: #667eea; }
        }
        
        /* Progress Bar */
        .progress-bar {
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 1.5s ease-in-out;
        }
        
        .progress-bar.animate {
            transform: scaleX(1);
        }
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            z-index: 9999;
            transition: width 0.1s ease;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .mobile-padding { padding: 1rem !important; }
            .mobile-text { font-size: 0.875rem !important; }
            .mobile-title { font-size: 2.5rem !important; line-height: 1.2 !important; }
            .mobile-hero { padding-top: 6rem !important; padding-bottom: 4rem !important; }
            .mobile-section { padding-top: 3rem !important; padding-bottom: 3rem !important; }
            .mobile-hidden { display: none !important; }
            .mobile-center { text-align: center !important; }
            .mobile-full { width: 100% !important; }
            
            /* Touch optimizations */
            .touch-button {
                min-height: 44px;
                min-width: 44px;
                padding: 12px 20px;
            }
            
            /* Card spacing for mobile */
            .mobile-card {
                margin-bottom: 1.5rem;
                padding: 1.5rem;
            }
            
            /* Typography scaling */
            h1 { font-size: 2rem !important; }
            h2 { font-size: 1.75rem !important; }
            h3 { font-size: 1.5rem !important; }
            
            /* Animation optimizations for mobile */
            .card-premium:hover {
                transform: none;
            }
            
            /* Reduce blur effects on mobile for performance */
            .glass {
                backdrop-filter: blur(10px);
            }
        }
        
        @media (max-width: 640px) {
            .xs-padding { padding: 0.75rem !important; }
            .xs-text { font-size: 0.75rem !important; }
            .xs-title { font-size: 2rem !important; }
            .xs-hidden { display: none !important; }
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
        }
        
        /* Back to Top Button Styles */
        #backToTop {
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation: pulse-glow 2s infinite alternate;
        }
        
        #backToTop.show {
            transform: scale(1) !important;
        }
        
        #backToTop.hide {
            transform: scale(0) !important;
        }
        
        @keyframes pulse-glow {
            0% { 
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.4), 0 0 40px rgba(139, 92, 246, 0.2);
            }
            100% { 
                box-shadow: 0 0 30px rgba(99, 102, 241, 0.6), 0 0 60px rgba(139, 92, 246, 0.3);
            }
        }
        
        /* Mobile optimizations for back to top */
        @media (max-width: 768px) {
            #backToTop {
                width: 48px;
                height: 48px;
                bottom: 1rem;
                right: 1rem;
            }
        }
        
        /* Simple Mobile Menu Styles */
        .mobile-menu {
            position: relative;
            z-index: 50;
        }
        
        .mobile-nav-item:hover i {
            color: white !important;
        }
        
        /* Remove complex animations and keep it simple */
        @media (max-width: 768px) {
            .mobile-menu {
                animation: slideDown 0.3s ease-out;
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
<body class="bg-slate-50 text-gray-800 overflow-x-hidden touch-optimized">

    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator"></div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-r from-primary to-secondary text-white rounded-full shadow-xl hover:shadow-2xl transform scale-0 transition-all duration-300 z-50 flex items-center justify-center group hover:scale-110">
        <i class="fas fa-chevron-up text-lg group-hover:scale-110 transition-transform duration-300"></i>
    </button>

    <!-- Particles Background -->
    <div id="particles-container" class="fixed inset-0 pointer-events-none z-0 mobile-hidden"></div>

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <nav class="border-b border-white/10">
            <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <a href="<?=base_url();?>" class="d-inline-block auth-logo">    
                            <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=__('SMM Panel Pro Logo')?>" class="h-8 sm:h-10 w-auto">
                        </a>
                    </div>
                    
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="#home" class="nav-link relative text-white hover:text-accent transition-all duration-300 group">
                            <?=__('Trang Chủ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="#services" class="nav-link relative text-white hover:text-accent transition-all duration-300 group">
                            <?=__('Dịch Vụ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="#faq" class="nav-link relative text-white hover:text-accent transition-all duration-300 group">
                            <?=__('FAQ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="<?=base_url('client/services')?>" class="nav-link relative text-white hover:text-accent transition-all duration-300 group">
                            <?=__('Bảng Giá')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="<?=base_url('client/contact');?>" class="nav-link relative text-white hover:text-accent transition-all duration-300 group">
                            <?=__('Liên Hệ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <a href="<?=base_url('client/register')?>" class="hidden sm:block px-4 sm:px-6 py-2 border border-primary text-primary rounded-xl hover:bg-primary hover:text-white transition-all duration-300 touch-button">
                            <?=__('Đăng Ký')?>
                        </a>
                        <a href="<?=base_url('client/login')?>" class="hidden sm:block px-4 sm:px-6 py-2 gradient-primary text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 touch-button text-sm sm:text-base">
                            <?=__('Đăng Nhập')?>
                        </a>
                        <button class="lg:hidden mobile-menu-btn p-2 touch-button" id="mobileMenuBtn">
                            <i class="fas fa-bars text-xl text-white"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div class="lg:hidden mobile-menu hidden mt-4 pb-4 bg-white rounded-lg shadow-lg mx-4" id="mobileMenu">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="Logo" class="h-6 w-auto">
                            <div>
                                <h3 class="font-semibold text-gray-800"><?=$CMSNT->site('title')?></h3>
                                <p class="text-xs text-gray-500"><?=__('SMM Panel Pro')?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="py-2">
                        <a href="#home" class="mobile-nav-item flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-home w-5 mr-3 text-primary"></i>
                            <span class="font-medium"><?=__('Trang Chủ')?></span>
                        </a>
                        
                        <a href="#services" class="mobile-nav-item flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-cogs w-5 mr-3 text-blue-500"></i>
                            <span class="font-medium"><?=__('Dịch Vụ')?></span>
                        </a>
                        
                        <a href="#faq" class="mobile-nav-item flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-question-circle w-5 mr-3 text-green-500"></i>
                            <span class="font-medium"><?=__('FAQ')?></span>
                        </a>
                        
                        <a href="<?=base_url('client/services')?>" class="mobile-nav-item flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-tags w-5 mr-3 text-purple-500"></i>
                            <span class="font-medium"><?=__('Bảng Giá')?></span>
                        </a>
                        
                        <a href="<?=base_url('client/contact')?>" class="mobile-nav-item flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-headset w-5 mr-3 text-orange-500"></i>
                            <span class="font-medium"><?=__('Liên Hệ')?></span>
                        </a>
                    </div>
                    
                    <div class="p-4 border-t border-gray-200 space-y-2">
                        <a href="<?=base_url('client/login')?>" class="w-full flex items-center justify-center px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <?=__('Đăng Nhập')?>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="w-full flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200">
                            <i class="fas fa-user-plus mr-2"></i>
                            <?=__('Đăng Ký')?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center gradient-primary overflow-hidden mobile-hero landscape-adjust">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 mobile-hidden">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl animate-float" style="animation-delay: -4s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 py-12 sm:py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-white mobile-center lg:text-left">
                    <div class="inline-flex items-center px-3 sm:px-4 py-2 bg-white/20 rounded-full text-xs sm:text-sm mb-4 sm:mb-6" data-aos="fade-up">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        🚀 <?=__('SMM Panel Uy Tín #1 Việt Nam')?>
                    </div>
                    
                    <h1 class="text-3xl sm:text-5xl lg:text-7xl font-black mb-4 sm:mb-6 leading-tight mobile-title" data-aos="fade-up" data-aos-delay="100">
                        <?=__('Tăng Tương Tác')?>
                        <br>
                        <span class="typing gradient-text bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text"><?=__('Mạng Xã Hội')?></span>
                    </h1>
                    
                    <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-white/90 leading-relaxed mobile-text" data-aos="fade-up" data-aos-delay="200">
                        <?=__('Nền tảng SMM Panel chuyên nghiệp với')?> <span class="text-yellow-400 font-semibold"><?=__('hơn 3000+ dịch vụ')?></span> 
                        <?=__('cho tất cả các nền tảng mạng xã hội. Tăng followers, likes, views')?> <span class="text-yellow-400 font-semibold"><?=__('nhanh chóng & an toàn')?></span>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8 sm:mb-12" data-aos="fade-up" data-aos-delay="300">
                        <a href="<?=base_url('client/login')?>" class="group relative px-6 sm:px-8 py-3 sm:py-4 bg-white text-primary rounded-2xl font-semibold text-base sm:text-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 touch-button mobile-full sm:w-auto flex items-center justify-center">
                            <span class="relative z-10"><i class="fas fa-sign-in-alt mr-2"></i> <?=__('Đăng Nhập Ngay')?></span>
                            <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="group px-6 sm:px-8 py-3 sm:py-4 border-2 border-white text-white rounded-2xl font-semibold text-base sm:text-lg hover:bg-white hover:text-primary transition-all duration-300 flex items-center justify-center touch-button mobile-full sm:w-auto">
                            <i class="fas fa-user-plus mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            <?=__('Đăng Ký Ngay')?>
                        </a>
                    </div>
                </div>
                
                <div class="relative order-first lg:order-last mb-8 lg:mb-0" data-aos="fade-left" data-aos-delay="500">
                    <!-- Dashboard Preview -->
                    <div class="relative">
                        <div class="glass rounded-2xl sm:rounded-3xl p-1 animate-glow">
                            <img src="<?=base_url('assets/img/homepage-item1.webp')?>" 
                                 alt="<?=__('SMM Panel Dashboard')?>" 
                                 class="rounded-2xl sm:rounded-3xl shadow-2xl w-full transform hover:scale-105 transition-transform duration-500 retina-optimized">
                        </div>
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-3 sm:-top-6 -right-3 sm:-right-6 glass rounded-xl sm:rounded-2xl p-2 sm:p-4 animate-float mobile-hidden sm:block">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-2 sm:w-3 h-2 sm:h-3 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-white text-xs sm:text-sm font-medium"><?=__('99.9% Uptime')?></span>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-3 sm:-bottom-6 -left-3 sm:-left-6 glass rounded-xl sm:rounded-2xl p-2 sm:p-4 animate-float mobile-hidden sm:block" style="animation-delay: -2s;">
                            <div class="text-white">
                                <div class="text-lg sm:text-2xl font-bold"><?=__('2M+');?></div>
                                <div class="text-xs sm:text-sm opacity-80"><?=__('Orders Completed')?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-4 sm:bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce mobile-hidden">
            <div class="flex flex-col items-center">
                <span class="text-xs sm:text-sm mb-2 opacity-80"><?=__('Scroll để khám phá')?></span>
                <i class="fas fa-chevron-down text-lg sm:text-xl"></i>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-12 sm:py-16 lg:py-24 bg-white relative overflow-hidden mobile-section">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50"></div>
        
        <div class="container mx-auto px-4 sm:px-6 relative">
            <div class="text-center mb-12 sm:mb-16 lg:mb-20">
                <div class="inline-flex items-center px-3 sm:px-4 py-2 bg-primary/10 rounded-full text-primary text-xs sm:text-sm font-medium mb-3 sm:mb-4" data-aos="fade-up">
                    <i class="fas fa-star mr-2"></i>
                    <?=__('Dịch Vụ SMM Chuyên Nghiệp')?>
                </div>
                <h2 class="text-2xl sm:text-4xl lg:text-6xl font-black mb-4 sm:mb-6 gradient-text mobile-title" data-aos="fade-up" data-aos-delay="100">
                    <?=__('Giải Pháp SMM Toàn Diện')?>
                </h2>
                <p class="text-base sm:text-xl text-gray-600 max-w-3xl mx-auto mobile-text" data-aos="fade-up" data-aos-delay="200">
                    <?=__('Từ tăng followers, likes, views đến quản lý chiến dịch marketing - chúng tôi cung cấp mọi dịch vụ bạn cần để phát triển mạnh mẽ trên social media')?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <!-- Facebook Service -->
                <div class="card-premium glass-dark rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-center group mobile-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 sm:mb-6 gradient-primary rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-facebook-f text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-3 sm:mb-4"><?=__('Facebook Marketing')?></h3>
                    <p class="text-gray-300 mb-4 sm:mb-6 text-sm sm:text-base mobile-text"><?=__('Tăng like fanpage, like bài viết, share, comment và follow với người dùng Việt Nam thật 100%')?></p>
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Like Fanpage & Bài Viết')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Follow & Comment Tương Tác')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Share & Reaction Đa Dạng')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Instagram Service -->
                <div class="card-premium glass-dark rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-center group mobile-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-instagram text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-3 sm:mb-4"><?=__('Instagram Growth')?></h3>
                    <p class="text-gray-300 mb-4 sm:mb-6 text-sm sm:text-base mobile-text"><?=__('Tăng followers, likes, views story, saves và comments Instagram với chất lượng cao và tốc độ nhanh')?></p>
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Followers & Likes Chất Lượng')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Story Views & Saves')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Reels & IGTV Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- YouTube Service -->
                <div class="card-premium glass-dark rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-center group mobile-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-red-500 to-red-600 rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-youtube text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-3 sm:mb-4"><?=__('YouTube Optimization')?></h3>
                    <p class="text-gray-300 mb-4 sm:mb-6 text-sm sm:text-base mobile-text"><?=__('Tăng subscriber, views, watch time và likes YouTube để đạt điều kiện kiếm tiền và phát triển kênh')?></p>
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Subscriber & Views Thật')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Watch Time 4000 Giờ')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('YouTube Shorts Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- TikTok Service -->
                <div class="card-premium glass-dark rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 text-center group mobile-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 sm:mb-6 bg-gradient-to-r from-gray-800 to-black rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-tiktok text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-3 sm:mb-4"><?=__('TikTok Viral')?></h3>
                    <p class="text-gray-300 mb-4 sm:mb-6 text-sm sm:text-base mobile-text"><?=__('Tăng followers, likes, views và shares TikTok để video viral và tăng độ phủ sóng trên For You Page')?></p>
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Followers & Likes TikTok')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Views & Shares Viral')?></span>
                        </div>
                        <div class="flex items-center text-gray-300 text-sm sm:text-base">
                            <i class="fas fa-check text-green-400 mr-2 sm:mr-3"></i>
                            <span><?=__('Live Stream Support')?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-12 sm:py-16 lg:py-24 bg-slate-100 text-gray-800 mobile-section">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16 lg:mb-20">
                <div class="inline-flex items-center px-3 sm:px-4 py-2 bg-primary/10 rounded-full text-primary text-xs sm:text-sm font-medium mb-3 sm:mb-4" data-aos="fade-up">
                    <i class="fas fa-question-circle mr-2"></i>
                    <?=__('Hỗ Trợ Khách Hàng')?>
                </div>
                <h2 class="text-xl sm:text-3xl lg:text-5xl font-black mb-4 sm:mb-6 gradient-text mobile-title" data-aos="fade-up" data-aos-delay="100">
                    <?=__('Câu Hỏi Thường Gặp')?>
                </h2>
                <p class="text-base sm:text-xl text-gray-600 max-w-3xl mx-auto mobile-text" data-aos="fade-up" data-aos-delay="200">
                    <?=__('Tìm hiểu thêm về dịch vụ SMM Panel của chúng tôi qua những câu hỏi phổ biến nhất từ khách hàng.')?>
                </p>
            </div>

            <div class="max-w-4xl mx-auto space-y-4" data-aos="fade-up" data-aos-delay="300">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq1">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('SMM Panel là gì và hoạt động như thế nào?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('SMM Panel là nền tảng cung cấp dịch vụ marketing mạng xã hội tự động. Bạn chỉ cần đặt hàng với link bài viết/profile, hệ thống sẽ tự động tăng followers, likes, views, comments... cho tài khoản của bạn một cách nhanh chóng và an toàn.')?>
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq2">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('Thời gian giao hàng bao lâu?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('Thời gian giao hàng phụ thuộc vào từng dịch vụ: Likes/Followers thường trong vòng 5-30 phút, Views trong vòng 1-6 giờ, Comments trong vòng 30 phút - 2 giờ. Tất cả đều được giao từ từ và tự nhiên để đảm bảo an toàn tài khoản.')?>
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq3">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('Dịch vụ có an toàn cho tài khoản không?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('Hoàn toàn an toàn! Chúng tôi sử dụng công nghệ tiên tiến để mô phỏng tương tác tự nhiên. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ hơn 15,000+ khách hàng mà không có trường hợp nào bị khóa tài khoản.')?>
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq4">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('Có chính sách bảo hành và hoàn tiền không?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('Có! Chúng tôi bảo hành 30-90 ngày tùy dịch vụ. Nếu số lượng giảm, chúng tôi sẽ refill miễn phí. Hoàn tiền 100% nếu không giao được hàng sau 24h. Chính sách rõ ràng, minh bạch, uy tín hàng đầu Việt Nam.')?>
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq5">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('Các phương thức thanh toán được hỗ trợ?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('Hỗ trợ đa dạng: Chuyển khoản ngân hàng, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào điện thoại, Bitcoin và các loại coin khác. Nạp tiền tự động 24/7, xử lý trong vòng 1-5 phút.')?>
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <button class="w-full px-6 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-gray-50 transition-colors duration-300" data-target="faq6">
                        <h3 class="text-lg font-semibold text-gray-800"><?=__('Làm sao để bắt đầu sử dụng dịch vụ?')?></h3>
                        <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            <?=__('Rất đơn giản! 1) Đăng ký tài khoản miễn phí 2) Nạp tiền vào tài khoản 3) Chọn dịch vụ phù hợp 4) Nhập link bài viết/profile 5) Đặt hàng và chờ kết quả. Hỗ trợ 24/7 qua Telegram/Zalo nếu cần.')?>
                        </p>
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
                'background': 'rgba(15, 23, 42, 0.5)', // slate-900 with 50% opacity
                'backdrop-filter': 'blur(10px)',
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
                            'background': 'rgba(15, 23, 42, 0.7)', // slate-900 with 70% opacity for scrolled state
                            'backdrop-filter': 'blur(10px)',
                            'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.15)'
                        });
                    }
                } else {
                    if ($navbar.hasClass('scrolled')) {
                        $navbar.removeClass('scrolled').css({
                            'background': 'rgba(15, 23, 42, 0.5)', // Back to initial slate-900 with 50% opacity
                            'backdrop-filter': 'blur(10px)',
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
            $('.faq-trigger').on('click', function() {
                const targetId = $(this).data('target');
                const content = $(this).siblings('.faq-content');
                const icon = $(this).find('i');
                const isOpen = !content.hasClass('hidden');
                
                // Close all other FAQ items
                $('.faq-content').not(content).addClass('hidden');
                $('.faq-trigger i').not(icon).removeClass('fa-chevron-up').addClass('fa-chevron-down');
                
                // Toggle current item
                if (isOpen) {
                    content.addClass('hidden');
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                } else {
                    content.removeClass('hidden');
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
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

            // Typing effect with mobile optimization
            const typingText = "<?=__('Mạng Xã Hội')?>";
            let typingIndex = 0;

            function typeWriter() {
                const $typingElement = $('.typing');
                if (!$typingElement.length) return;

                if (typingIndex <= typingText.length) {
                    $typingElement.text(typingText.substring(0, typingIndex));
                    typingIndex++;
                    setTimeout(typeWriter, 120);
                } else {
                    setTimeout(() => {
                        typingIndex = 0;
                        $typingElement.text('');
                        setTimeout(typeWriter, 1200);
                    }, 2000);
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
                    
                    .is-mobile .card-premium:hover {
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
                        box-shadow: 0 0 40px rgba(99, 102, 241, 0.8), 0 0 80px rgba(139, 92, 246, 0.4) !important;
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

 

