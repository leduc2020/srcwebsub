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
    <meta property="og:image" content="<?=BASE_URL($CMSNT->site('image'));?>">
    <meta property="og:site_name" content="<?=$CMSNT->site('title')?>">
    <meta property="og:locale" content="vi_VN">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?=$CMSNT->site('title')?>">
    <meta name="twitter:description" content="<?=$CMSNT->site('description')?>">
    <meta name="twitter:image" content="<?=BASE_URL($CMSNT->site('image'));?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'glass-primary': '#667eea',
                        'glass-secondary': '#764ba2',
                        'glass-accent': '#f093fb',
                        'glass-cyan': '#4facfe',
                        'glass-violet': '#a8edea',
                        'glass-orange': '#ffa726',
                        'glass-green': '#66bb6a',
                        'glass-pink': '#f093fb',
                        'glass-blue': '#667eea',
                        'glass-purple': '#764ba2',
                    },
                    fontFamily: {
                        'glass': ['Inter', 'Poppins', 'system-ui', 'sans-serif'],
                        'display': ['Poppins', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 3s ease-in-out infinite alternate',
                        'shimmer': 'shimmer 2.5s infinite linear',
                        'fade-in': 'fade-in 0.8s ease-out',
                        'slide-up': 'slide-up 0.6s ease-out',
                        'scale-in': 'scale-in 0.5s ease-out',
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px rgba(31, 38, 135, 0.37)',
                        'glass-lg': '0 20px 40px rgba(31, 38, 135, 0.4)',
                        'glass-xl': '0 25px 50px rgba(31, 38, 135, 0.5)',
                        'soft': '0 4px 20px rgba(102, 126, 234, 0.25)',
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&subset=vietnamese&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        * {
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
        }
        
        /* Glassmorphism Background */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(240, 147, 251, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 50% 20%, rgba(168, 237, 234, 0.15) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }
        
        /* Glassmorphism Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes glow {
            0% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.3); }
            100% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.6); }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* Glass Components */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        
        .glass-strong {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Glassmorphism Buttons */
        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(31, 38, 135, 0.4);
        }
        
        /* Scroll Progress */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #f093fb);
            z-index: 9999;
            transition: width 0.3s ease;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .glass {
                backdrop-filter: blur(12px);
            }
            
            .glass-card {
                backdrop-filter: blur(16px);
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #f093fb);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
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
      }
    }
    </script>
    <?=$CMSNT->site('javascript_header');?>
</head>
<body class="text-white overflow-x-hidden relative">
    <!-- Scroll Progress -->
    <div class="scroll-progress"></div>
    
    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 btn-glass rounded-full opacity-0 transition-all duration-300 z-50 flex items-center justify-center hover:scale-110">
        <i class="fas fa-chevron-up text-lg"></i>
    </button>

    <!-- Glassmorphism Header -->
    <header class="fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <nav class="glass border-b border-white/10">
            <div class="container mx-auto px-4 sm:px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="<?=base_url();?>" class="flex items-center group">    
                            <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=$CMSNT->site('title')?>" class="h-10 w-auto transition-transform duration-300 group-hover:scale-105">
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="#home" class="nav-link relative text-white/90 hover:text-white transition-all duration-300 group font-medium">
                            <?=__('Trang Chủ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-glass-accent to-glass-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="#services" class="nav-link relative text-white/90 hover:text-white transition-all duration-300 group font-medium">
                            <?=__('Dịch Vụ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-glass-accent to-glass-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="#faq" class="nav-link relative text-white/90 hover:text-white transition-all duration-300 group font-medium">
                            <?=__('FAQ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-glass-accent to-glass-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="<?=base_url('client/services')?>" class="nav-link relative text-white/90 hover:text-white transition-all duration-300 group font-medium">
                            <?=__('Bảng Giá')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-glass-accent to-glass-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="<?=base_url('client/contact');?>" class="nav-link relative text-white/90 hover:text-white transition-all duration-300 group font-medium">
                            <?=__('Liên Hệ')?>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-glass-accent to-glass-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                    </div>
                    
                    <!-- Header Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="<?=base_url('client/register')?>" class="hidden sm:block btn-glass px-6 py-2.5 rounded-xl text-white font-medium hover:scale-105 transition-all duration-300">
                            <?=__('Đăng Ký')?>
                        </a>
                        <a href="<?=base_url('client/login')?>" class="hidden sm:block btn-glass px-6 py-2.5 rounded-xl text-white font-semibold border-2 border-white/30 hover:scale-105 transition-all duration-300">
                            <?=__('Đăng Nhập')?>
                        </a>
                        <button class="lg:hidden p-3 btn-glass rounded-xl" id="mobileMenuBtn">
                            <i class="fas fa-bars text-lg text-white"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div class="lg:hidden hidden mt-4 glass-card rounded-xl p-4" id="mobileMenu">
                    <div class="space-y-3">
                        <a href="#home" class="block py-3 px-4 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                            <i class="fas fa-home w-5 text-glass-cyan mr-3"></i>
                            <?=__('Trang Chủ')?>
                        </a>
                        <a href="#services" class="block py-3 px-4 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                            <i class="fas fa-cogs w-5 text-glass-pink mr-3"></i>
                            <?=__('Dịch Vụ')?>
                        </a>
                        <a href="#faq" class="block py-3 px-4 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                            <i class="fas fa-question-circle w-5 text-glass-green mr-3"></i>
                            <?=__('FAQ')?>
                        </a>
                        <a href="<?=base_url('client/services')?>" class="block py-3 px-4 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                            <i class="fas fa-tags w-5 text-glass-violet mr-3"></i>
                            <?=__('Bảng Giá')?>
                        </a>
                        <a href="<?=base_url('client/contact')?>" class="block py-3 px-4 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                            <i class="fas fa-headset w-5 text-glass-orange mr-3"></i>
                            <?=__('Liên Hệ')?>
                        </a>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-white/20 space-y-3">
                        <a href="<?=base_url('client/login')?>" class="block btn-glass text-center py-3 rounded-lg text-white font-semibold">
                            <?=__('Đăng Nhập')?>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="block bg-gradient-to-r from-glass-primary to-glass-accent text-center py-3 rounded-lg text-white font-semibold">
                            <?=__('Đăng Ký')?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Glassmorphism Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden pt-20">
        <!-- Floating Glass Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-72 h-72 glass-card rounded-full animate-float opacity-30"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 glass-card rounded-full animate-float opacity-20" style="animation-delay: -2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 glass-card rounded-full animate-float opacity-25" style="animation-delay: -4s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 py-16 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="inline-flex items-center glass-card px-6 py-3 rounded-full mb-8 text-sm font-medium">
                        <span class="w-2 h-2 bg-glass-accent rounded-full mr-3 animate-pulse"></span>
                        🚀 <?=__('SMM Panel Uy Tín #1 Việt Nam')?>
                    </div>
                    
                    <!-- Main Heading -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold mb-6 leading-tight font-display">
                        <span class="block text-white"><?=__('TĂNG TƯƠNG TÁC')?></span>
                        <span class="block bg-gradient-to-r from-glass-accent to-glass-cyan bg-clip-text text-transparent"><?=__('MẠNG XÃ HỘI')?></span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-xl text-white/80 mb-8 leading-relaxed max-w-2xl">
                        <?=__('Nền tảng SMM Panel chuyên nghiệp với')?> <span class="text-glass-accent font-semibold"><?=__('hơn 3000+ dịch vụ')?></span> 
                        <?=__('cho tất cả các nền tảng mạng xã hội. Tăng followers, likes, views')?> <span class="text-glass-accent font-semibold"><?=__('nhanh chóng & an toàn')?></span>
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="<?=base_url('client/login')?>" class="btn-glass px-8 py-4 rounded-xl text-white font-semibold text-lg hover:scale-105 transition-all duration-300 flex items-center justify-center border-2 border-white/30">
                            <i class="fas fa-sign-in-alt mr-3"></i>
                            <?=__('ĐĂNG NHẬP NGAY')?>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="bg-gradient-to-r from-glass-primary to-glass-accent px-8 py-4 rounded-xl text-white font-semibold text-lg hover:scale-105 transition-all duration-300 flex items-center justify-center shadow-glass">
                            <i class="fas fa-user-plus mr-3"></i>
                            <?=__('ĐĂNG KÝ NGAY')?>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6">
                        <div class="glass-card rounded-xl p-6 text-center" data-aos="fade-up" data-aos-delay="100">
                            <div class="text-3xl font-bold text-white mb-2"><?=__('15K+')?></div>
                            <div class="text-white/70"><?=__('Khách hàng')?></div>
                        </div>
                        <div class="glass-card rounded-xl p-6 text-center" data-aos="fade-up" data-aos-delay="200">
                            <div class="text-3xl font-bold text-white mb-2"><?=__('3000+')?></div>
                            <div class="text-white/70"><?=__('Dịch vụ')?></div>
                        </div>
                        <div class="glass-card rounded-xl p-6 text-center" data-aos="fade-up" data-aos-delay="300">
                            <div class="text-3xl font-bold text-white mb-2"><?=__('99.9%')?></div>
                            <div class="text-white/70"><?=__('Uptime')?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="relative order-first lg:order-last" data-aos="fade-left">
                    <div class="relative">
                        <!-- Main Dashboard Image -->
                        <div class="glass-card rounded-3xl p-4 animate-glow">
                            <img src="<?=base_url('assets/img/homepage-item1.webp')?>" 
                                 alt="<?=__('SMM Panel Dashboard')?>" 
                                 class="rounded-2xl w-full shadow-glass-lg">
                        </div>
                        
                        <!-- Floating Info Cards -->
                        <div class="absolute -top-4 -right-4 glass-card rounded-xl p-4 animate-float hidden sm:block">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-glass-green rounded-full animate-pulse"></div>
                                <div>
                                    <div class="text-white font-semibold text-sm"><?=__('99.9% Uptime')?></div>
                                    <div class="text-white/70 text-xs"><?=__('Hệ thống ổn định')?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-4 -left-4 glass-card rounded-xl p-4 animate-float hidden sm:block" style="animation-delay: -3s;">
                            <div class="text-white">
                                <div class="text-2xl font-bold text-glass-accent"><?=__('2M+')?></div>
                                <div class="text-xs text-white/70"><?=__('Đơn hàng hoàn thành')?></div>
                            </div>
                        </div>
                        
                        <div class="absolute top-1/4 -left-8 glass-card rounded-lg p-3 animate-float hidden lg:block" style="animation-delay: -5s;">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-bolt text-glass-orange"></i>
                                <div class="text-white text-sm font-medium"><?=__('Tự động')?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce hidden lg:block">
            <div class="flex flex-col items-center">
                <span class="text-sm mb-3 opacity-80"><?=__('Scroll để khám phá')?></span>
                <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Glassmorphism Services Section -->
    <section id="services" class="py-20 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-72 h-72 glass-card rounded-full opacity-20 animate-float"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 glass-card rounded-full opacity-15 animate-float" style="animation-delay: -3s;"></div>
        </div>
        
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center glass-card px-6 py-3 rounded-full mb-6 text-sm font-medium">
                    <i class="fas fa-star text-glass-accent mr-3 animate-pulse"></i>
                    <?=__('Dịch Vụ SMM Chuyên Nghiệp')?>
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold mb-6 font-display">
                    <span class="text-white"><?=__('GIẢI PHÁP SMM')?></span>
                    <span class="block bg-gradient-to-r from-glass-accent to-glass-cyan bg-clip-text text-transparent"><?=__('TOÀN DIỆN')?></span>
                </h2>
                <p class="text-xl text-white/80 max-w-3xl mx-auto leading-relaxed">
                    <?=__('Từ tăng followers, likes, views đến quản lý chiến dịch marketing - chúng tôi cung cấp mọi dịch vụ bạn cần để phát triển mạnh mẽ trên social media')?>
                </p>
            </div>
            
            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Facebook Service -->
                <div class="glass-card rounded-2xl p-8 text-center group hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glass">
                        <i class="fab fa-facebook-f text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4 font-display"><?=__('Facebook Marketing')?></h3>
                    <p class="text-white/70 mb-6 leading-relaxed"><?=__('Tăng like fanpage, like bài viết, share, comment và follow với người dùng Việt Nam thật 100%')?></p>
                    <div class="space-y-3">
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Like Fanpage & Bài Viết')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Follow & Comment Tương Tác')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Share & Reaction Đa Dạng')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Instagram Service -->
                <div class="glass-card rounded-2xl p-8 text-center group hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glass">
                        <i class="fab fa-instagram text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4 font-display"><?=__('Instagram Growth')?></h3>
                    <p class="text-white/70 mb-6 leading-relaxed"><?=__('Tăng followers, likes, views story, saves và comments Instagram với chất lượng cao và tốc độ nhanh')?></p>
                    <div class="space-y-3">
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Followers & Likes Chất Lượng')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Story Views & Saves')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Reels & IGTV Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- YouTube Service -->
                <div class="glass-card rounded-2xl p-8 text-center group hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glass">
                        <i class="fab fa-youtube text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4 font-display"><?=__('YouTube Optimization')?></h3>
                    <p class="text-white/70 mb-6 leading-relaxed"><?=__('Tăng subscriber, views, watch time và likes YouTube để đạt điều kiện kiếm tiền và phát triển kênh')?></p>
                    <div class="space-y-3">
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Subscriber & Views Thật')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Watch Time 4000 Giờ')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('YouTube Shorts Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- TikTok Service -->
                <div class="glass-card rounded-2xl p-8 text-center group hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-gray-800 to-black rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-glass">
                        <i class="fab fa-tiktok text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4 font-display"><?=__('TikTok Viral')?></h3>
                    <p class="text-white/70 mb-6 leading-relaxed"><?=__('Tăng followers, likes, views và shares TikTok để video viral và tăng độ phủ sóng trên For You Page')?></p>
                    <div class="space-y-3">
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Followers & Likes TikTok')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Views & Shares Viral')?></span>
                        </div>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-check text-glass-green mr-3"></i>
                            <span><?=__('Live Stream Support')?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Glassmorphism FAQ Section -->
    <section id="faq" class="py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center glass-card px-6 py-3 rounded-full mb-6 text-sm font-medium">
                    <i class="fas fa-question-circle text-glass-accent mr-3 animate-pulse"></i>
                    <?=__('Hỗ Trợ Khách Hàng')?>
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold mb-6 font-display">
                    <span class="text-white"><?=__('CÂU HỎI')?></span>
                    <span class="block bg-gradient-to-r from-glass-accent to-glass-cyan bg-clip-text text-transparent"><?=__('THƯỜNG GẶP')?></span>
                </h2>
                <p class="text-xl text-white/80 max-w-3xl mx-auto leading-relaxed">
                    <?=__('Tìm hiểu thêm về dịch vụ SMM Panel của chúng tôi qua những câu hỏi phổ biến nhất từ khách hàng.')?>
                </p>
            </div>

            <!-- FAQ Items -->
            <div class="max-w-4xl mx-auto space-y-6" data-aos="fade-up" data-aos-delay="200">
                <!-- FAQ Item 1 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq1">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('SMM Panel là gì và hoạt động như thế nào?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq1">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('SMM Panel là nền tảng cung cấp dịch vụ marketing mạng xã hội tự động. Bạn chỉ cần đặt hàng với link bài viết/profile, hệ thống sẽ tự động tăng followers, likes, views, comments... cho tài khoản của bạn một cách nhanh chóng và an toàn.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq2">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('Thời gian giao hàng bao lâu?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq2">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('Thời gian giao hàng phụ thuộc vào từng dịch vụ: Likes/Followers thường trong vòng 5-30 phút, Views trong vòng 1-6 giờ, Comments trong vòng 30 phút - 2 giờ. Tất cả đều được giao từ từ và tự nhiên để đảm bảo an toàn tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq3">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('Dịch vụ có an toàn cho tài khoản không?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq3">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('Hoàn toàn an toàn! Chúng tôi sử dụng công nghệ tiên tiến để mô phỏng tương tác tự nhiên. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ hơn 15,000+ khách hàng mà không có trường hợp nào bị khóa tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq4">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('Có chính sách bảo hành và hoàn tiền không?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq4">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('Có! Chúng tôi bảo hành 30-90 ngày tùy dịch vụ. Nếu số lượng giảm, chúng tôi sẽ refill miễn phí. Hoàn tiền 100% nếu không giao được hàng sau 24h. Chính sách rõ ràng, minh bạch, uy tín hàng đầu Việt Nam.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq5">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('Các phương thức thanh toán được hỗ trợ?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq5">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('Hỗ trợ đa dạng: Chuyển khoản ngân hàng, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào điện thoại, Bitcoin và các loại coin khác. Nạp tiền tự động 24/7, xử lý trong vòng 1-5 phút.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="glass-card rounded-xl overflow-hidden">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-white/5 transition-colors duration-300" data-target="faq6">
                        <h3 class="text-lg font-semibold text-white pr-4"><?=__('Làm sao để bắt đầu sử dụng dịch vụ?')?></h3>
                        <i class="fas fa-chevron-down text-glass-accent transition-transform duration-300 text-lg faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq6">
                        <div class="pt-4 border-t border-white/20">
                            <p class="text-white/80 leading-relaxed">
                                <?=__('Rất đơn giản! 1) Đăng ký tài khoản miễn phí 2) Nạp tiền vào tài khoản 3) Chọn dịch vụ phù hợp 4) Nhập link bài viết/profile 5) Đặt hàng và chờ kết quả. Hỗ trợ 24/7 qua Telegram/Zalo nếu cần.')?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Glassmorphism Footer -->
    <footer class="relative py-16 overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0 glass-card"></div>
        
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=$CMSNT->site('title');?>" class="h-10">
                    </div>
                    <p class="text-white/70 text-sm leading-relaxed">
                        <?=$CMSNT->site('description');?>
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white"><?=__('Liên Kết Nhanh')?></h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="#home" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=__('Trang Chủ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#services" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=__('Dịch Vụ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#faq" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=__('FAQ')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/services');?>" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=__('Bảng Giá')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/contact');?>" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=__('Liên Hệ')?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white"><?=__('Dịch Vụ')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC LIMIT 5") as $category):?>
                        <li>
                            <a href="<?=base_url('service/'.$category['slug']);?>" class="text-white/70 hover:text-white transition-colors duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-glass-accent"></i>
                                <?=$category['name'];?>
                            </a>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white"><?=__('Liên Hệ')?></h4>
                    <ul class="space-y-3">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-glass-accent mt-1"></i>
                            <span class="text-white/70"><?=$CMSNT->site('address')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-glass-accent"></i>
                            <span class="text-white/70"><?=$CMSNT->site('hotline')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-glass-accent"></i>
                            <span class="text-white/70"><?=$CMSNT->site('email')?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-glass-accent"></i>
                            <span class="text-white/70"><?=__('Hỗ trợ 24/7')?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/20 pt-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-white/70 text-sm">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?>. <?=__('Tất cả quyền được bảo lưu.')?>
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?=base_url('client/contact');?>" class="text-white/70 hover:text-white transition-colors duration-300 text-sm">
                            <?=__('Liên Hệ')?>
                        </a>
                        <a href="<?=base_url('client/policy');?>" class="text-white/70 hover:text-white transition-colors duration-300 text-sm">
                            <?=__('Chính Sách')?>
                        </a>
                        <a href="<?=base_url('client/privacy');?>" class="text-white/70 hover:text-white transition-colors duration-300 text-sm">
                            <?=__('Bảo Mật')?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            offset: 100
        });

        $(document).ready(function() {
            // Mobile Menu Toggle
            $('#mobileMenuBtn').on('click', function() {
                const $menu = $('#mobileMenu');
                const $icon = $(this).find('i');
                
                if ($menu.hasClass('hidden')) {
                    $menu.removeClass('hidden');
                    $icon.removeClass('fa-bars').addClass('fa-times');
                } else {
                    $menu.addClass('hidden');
                    $icon.removeClass('fa-times').addClass('fa-bars');
                }
            });

            // Close mobile menu when clicking nav items
            $('#mobileMenu a').on('click', function() {
                $('#mobileMenu').addClass('hidden');
                $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            });

            // Close mobile menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#navbar').length) {
                    $('#mobileMenu').addClass('hidden');
                    $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
                }
            });

            // Scroll Progress
            function updateScrollProgress() {
                const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                $('.scroll-progress').css('width', Math.min(scrolled, 100) + '%');
                
                // Back to top button
                if ($(window).scrollTop() > 300) {
                    $('#backToTop').removeClass('opacity-0').addClass('opacity-100');
                } else {
                    $('#backToTop').removeClass('opacity-100').addClass('opacity-0');
                }
                
                // Navbar background
                if ($(window).scrollTop() > 50) {
                    $('#navbar nav').addClass('backdrop-blur-xl bg-white/10 border-white/20');
                } else {
                    $('#navbar nav').removeClass('backdrop-blur-xl bg-white/10 border-white/20');
                }
            }

            $(window).on('scroll', updateScrollProgress);

            // Back to Top
            $('#backToTop').on('click', function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
            });

            // FAQ Accordion
            $('.faq-trigger').on('click', function() {
                const targetId = $(this).data('target');
                const content = $('#' + targetId);
                const icon = $(this).find('.faq-icon');
                
                // Close all other FAQ items
                $('.faq-content').not(content).addClass('hidden');
                $('.faq-icon').not(icon).removeClass('fa-chevron-up').addClass('fa-chevron-down');
                
                // Toggle current item
                if (content.hasClass('hidden')) {
                    content.removeClass('hidden');
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    content.addClass('hidden');
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Smooth Scrolling
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                
                if (target.length) {
                    const headerHeight = $('#navbar').outerHeight();
                    $('html, body').animate({
                        scrollTop: target.offset().top - headerHeight
                    }, 800);
                }
            });

            // Initialize scroll progress on load
            updateScrollProgress();
        });
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html> 