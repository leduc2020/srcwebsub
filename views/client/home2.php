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
                        dark: '#0a0a0a',
                        darker: '#050505',
                        neon: '#00f5ff',
                        purple: '#8b5cf6',
                        pink: '#ec4899',
                        yellow: '#fbbf24',
                    },
                    fontFamily: {
                        cyber: ['Orbitron', 'monospace'],
                        modern: ['Space Grotesk', 'sans-serif'],
                    },
                    animation: {
                        'glow-pulse': 'glow-pulse 2s ease-in-out infinite alternate',
                        'float': 'float 6s ease-in-out infinite',
                        'neon-flicker': 'neon-flicker 1.5s infinite alternate',
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
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        body { 
            font-family: 'Space Grotesk', sans-serif; 
            background: #0a0a0a;
            overflow-x: hidden;
        }
        
        /* Dark Glassmorphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Animated Background */
        .cyber-bg {
            background: 
                radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 245, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(236, 72, 153, 0.2) 0%, transparent 50%),
                linear-gradient(135deg, #0a0a0a 0%, #050505 100%);
            position: relative;
        }
        
        .cyber-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(90deg, transparent, transparent 98px, rgba(0, 245, 255, 0.03) 100px),
                repeating-linear-gradient(0deg, transparent, transparent 98px, rgba(139, 92, 246, 0.03) 100px);
            pointer-events: none;
        }
        
        /* Neon Effects */
        .neon-text {
            color: #00f5ff;
            text-shadow: 
                0 0 5px #00f5ff,
                0 0 10px #00f5ff,
                0 0 15px #00f5ff,
                0 0 20px #00f5ff;
            animation: neon-flicker 1.5s infinite alternate;
        }
        
        .neon-border {
            border: 2px solid #00f5ff;
            box-shadow: 
                0 0 10px #00f5ff,
                inset 0 0 10px rgba(0, 245, 255, 0.1);
        }
        
        /* Floating Cards */
        .float-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
            position: relative;
            overflow: hidden;
        }
        
        .float-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .float-card:hover::before {
            left: 100%;
        }
        
        .float-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 20px rgba(0, 245, 255, 0.2);
        }
        
        /* Custom Buttons */
        .btn-cyber {
            background: linear-gradient(45deg, #00f5ff, #8b5cf6);
            color: #000;
            font-weight: 700;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-cyber::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-cyber:hover::before {
            left: 100%;
        }
        
        .btn-cyber:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(0, 245, 255, 0.5);
        }
        
        .btn-outline-cyber {
            background: transparent;
            color: #00f5ff;
            border: 2px solid #00f5ff;
            font-weight: 600;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-cyber:hover {
            background: #00f5ff;
            color: #000;
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.5);
        }
        
        /* Hexagon Service Cards */
        .hex-card {
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hex-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00f5ff, #8b5cf6, #ec4899, #fbbf24);
            border-radius: 20px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .hex-card:hover::before {
            opacity: 1;
        }
        
        .hex-card:hover {
            transform: translateY(-10px) scale(1.03);
        }
        
        /* FAQ Cyber Style */
        .faq-cyber .faq-item {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .faq-cyber .faq-question {
            background: rgba(0, 245, 255, 0.1);
            color: #fff;
            font-weight: 600;
            font-family: 'Orbitron', monospace;
        }
        
        .faq-cyber .faq-item.active .faq-question {
            background: linear-gradient(45deg, #00f5ff, #8b5cf6);
            color: #000;
        }
        
        /* Custom Animations */
        @keyframes glow-pulse {
            from { box-shadow: 0 0 20px rgba(0, 245, 255, 0.2); }
            to { box-shadow: 0 0 30px rgba(0, 245, 255, 0.4); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes neon-flicker {
            0%, 100% { 
                text-shadow: 
                    0 0 5px #00f5ff,
                    0 0 10px #00f5ff,
                    0 0 15px #00f5ff,
                    0 0 20px #00f5ff;
            }
            50% { 
                text-shadow: 
                    0 0 2px #00f5ff,
                    0 0 5px #00f5ff,
                    0 0 8px #00f5ff,
                    0 0 12px #00f5ff;
            }
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .float-card:hover {
                transform: none;
            }
            
            .hex-card:hover {
                transform: none;
            }
            
            .cyber-bg::before {
                display: none;
            }
        }
        
        /* Scroll Indicator */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #00f5ff, #8b5cf6, #ec4899);
            z-index: 9999;
            transition: width 0.1s ease;
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
<body class="bg-dark text-white overflow-x-hidden">

    <!-- Scroll Progress -->
    <div class="scroll-progress"></div>

    <!-- Floating Navigation -->
    <nav class="fixed w-full top-4 z-50 px-4" id="navbar">
        <div class="glass rounded-2xl mx-auto max-w-6xl px-6 py-4">
                <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="Logo" class="h-8 w-auto">
                    </div>
                    
                    <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-gray-300 hover:text-neon transition-colors duration-300 font-medium"><?=__('Trang Chủ')?></a>
                    <a href="#services" class="text-gray-300 hover:text-neon transition-colors duration-300 font-medium"><?=__('Dịch Vụ')?></a>
                    <a href="#faq" class="text-gray-300 hover:text-neon transition-colors duration-300 font-medium"><?=__('FAQ')?></a>
                    <a href="<?=base_url('client/services')?>" class="text-gray-300 hover:text-neon transition-colors duration-300 font-medium"><?=__('Bảng Giá')?></a>
                    <a href="<?=base_url('client/contact');?>" class="text-gray-300 hover:text-neon transition-colors duration-300 font-medium"><?=__('Liên Hệ')?></a>
                    </div>
                    
                <div class="flex items-center space-x-3">
                    <a href="<?=base_url('client/login')?>" class="hidden sm:block btn-outline-cyber px-4 py-2 rounded-lg text-sm"><?=__('Đăng Nhập')?></a>
                    <a href="<?=base_url('client/register')?>" class="btn-cyber px-6 py-2 rounded-lg text-sm"><?=__('Đăng Ký')?></a>
                    <button class="lg:hidden p-2 text-neon" id="mobileMenuBtn">
                        <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
            <div class="lg:hidden mt-4 pt-4 border-t border-white/10 hidden" id="mobileMenu">
                <div class="space-y-3">
                    <a href="#home" class="block text-gray-300 hover:text-neon transition-colors py-2"><?=__('Trang Chủ')?></a>
                    <a href="#services" class="block text-gray-300 hover:text-neon transition-colors py-2"><?=__('Dịch Vụ')?></a>
                    <a href="#faq" class="block text-gray-300 hover:text-neon transition-colors py-2"><?=__('FAQ')?></a>
                    <a href="<?=base_url('client/services')?>" class="block text-gray-300 hover:text-neon transition-colors py-2"><?=__('Bảng Giá')?></a>
                    <a href="<?=base_url('client/contact');?>" class="block text-gray-300 hover:text-neon transition-colors py-2"><?=__('Liên Hệ')?></a>
                    <div class="pt-3 space-y-2">
                        <a href="<?=base_url('client/login')?>" class="block btn-outline-cyber px-4 py-2 rounded-lg text-center text-sm"><?=__('Đăng Nhập')?></a>
                        <a href="<?=base_url('client/register')?>" class="block btn-cyber px-4 py-2 rounded-lg text-center text-sm"><?=__('Đăng Ký')?></a>
                            </div>
                    </div>
                </div>
            </div>
        </nav>

    <!-- Hero Section - Cyber Style -->
    <section id="home" class="cyber-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Floating Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-4 h-4 bg-neon rounded-full animate-pulse"></div>
            <div class="absolute top-40 right-20 w-6 h-6 bg-purple rounded-full animate-float"></div>
            <div class="absolute bottom-32 left-20 w-3 h-3 bg-pink rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-40 w-5 h-5 bg-yellow rounded-full animate-float"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-20 grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-left space-y-8" data-aos="fade-right">
                <div class="inline-flex items-center space-x-2 glass px-4 py-2 rounded-full">
                    <span class="w-2 h-2 bg-neon rounded-full animate-pulse"></span>
                    <span class="text-sm font-cyber text-neon"><?=__('SMM PANEL THẾ HỆ MỚI')?></span>
                    </div>
                    
                <h1 class="font-cyber text-5xl lg:text-7xl font-black leading-tight">
                    <span class="text-white"><?=__('BOOST')?></span><br>
                    <span class="neon-text"><?=__('SOCIAL')?></span><br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple to-pink"><?=__('POWER')?></span>
                    </h1>
                    
                <p class="text-gray-300 text-xl font-modern leading-relaxed max-w-lg">
                    <?=__('Nền tảng SMM Panel tiên tiến nhất với')?> <span class="text-neon font-semibold"><?=__('AI-powered automation')?></span> 
                    <?=__('và')?> <span class="text-purple font-semibold"><?=__('3000+ dịch vụ premium')?></span> <?=__('cho mọi platform.')?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?=base_url('client/register')?>" class="btn-cyber px-8 py-4 text-lg font-bold">
                        <i class="fas fa-rocket mr-2"></i>
                        <?=__('BẮT ĐẦU NGAY')?>
                    </a>
                    <a href="<?=base_url('client/login')?>" class="btn-outline-cyber px-8 py-4 text-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <?=__('ĐĂNG NHẬP')?>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="flex space-x-8 pt-8">
                    <div class="text-center">
                        <div class="text-2xl font-cyber text-neon font-bold">15K+</div>
                        <div class="text-gray-400 text-sm"><?=__('Khách Hàng')?></div>
                        </div>
                    <div class="text-center">
                        <div class="text-2xl font-cyber text-purple font-bold">3K+</div>
                        <div class="text-gray-400 text-sm"><?=__('Dịch Vụ')?></div>
                            </div>
                    <div class="text-center">
                        <div class="text-2xl font-cyber text-pink font-bold">24/7</div>
                        <div class="text-gray-400 text-sm"><?=__('Hỗ Trợ')?></div>
                        </div>
                            </div>
                        </div>
            
            <!-- Right Visual -->
            <div class="relative" data-aos="fade-left">
                <div class="float-card p-8 relative">
                    <img src="<?=base_url('assets/img/homepage-item1.webp')?>" alt="<?=__('SMM Dashboard')?>" class="w-full rounded-xl">
                    <div class="absolute -top-4 -right-4 glass-dark p-4 rounded-xl">
                        <i class="fas fa-chart-line text-neon text-2xl"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 glass-dark p-4 rounded-xl">
                        <i class="fas fa-users text-purple text-2xl"></i>
                </div>
            </div>
            </div>
        </div>
    </section>

    <!-- Services Section - Hexagon Grid -->
    <section id="services" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 glass px-4 py-2 rounded-full mb-4">
                    <i class="fas fa-cogs text-neon"></i>
                    <span class="text-sm font-cyber text-neon"><?=__('PREMIUM SERVICES')?></span>
                </div>
                <h2 class="font-cyber text-4xl lg:text-6xl font-black mb-6">
                    <span class="text-white"><?=__('DỊCH VỤ')?></span>
                    <span class="neon-text block"><?=__('CHUYÊN NGHIỆP')?></span>
                </h2>
                <p class="text-gray-400 text-xl max-w-3xl mx-auto">
                    <?=__('Tăng tương tác mạnh mẽ với công nghệ AI và automation tiên tiến nhất thị trường')?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Facebook -->
                <div class="hex-card group" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-facebook-f text-3xl text-white"></i>
                    </div>
                    <h3 class="font-cyber text-xl font-bold mb-3 text-neon"><?=__('FACEBOOK')?></h3>
                    <p class="text-gray-400 mb-4 text-sm"><?=__('Tăng like, follow, share và comment Facebook với độ retention cao và người dùng thật 100%')?></p>
                    <ul class="text-xs space-y-2 text-gray-500">
                        <li><i class="fas fa-check text-neon mr-2"></i><?=__('Like Fanpage Premium')?></li>
                        <li><i class="fas fa-check text-neon mr-2"></i><?=__('Follow Profile Thật')?></li>
                        <li><i class="fas fa-check text-neon mr-2"></i><?=__('Comment Tương Tác')?></li>
                    </ul>
                </div>
                
                <!-- Instagram -->
                <div class="hex-card group" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-instagram text-3xl text-white"></i>
                    </div>
                    <h3 class="font-cyber text-xl font-bold mb-3 text-purple"><?=__('INSTAGRAM')?></h3>
                    <p class="text-gray-400 mb-4 text-sm"><?=__('Followers chất lượng cao, likes, views story và saves Instagram với tốc độ siêu nhanh')?></p>
                    <ul class="text-xs space-y-2 text-gray-500">
                        <li><i class="fas fa-check text-purple mr-2"></i><?=__('Followers Quality')?></li>
                        <li><i class="fas fa-check text-purple mr-2"></i><?=__('Story Views Max')?></li>
                        <li><i class="fas fa-check text-purple mr-2"></i><?=__('Reels Viral Boost')?></li>
                    </ul>
                </div>
                
                <!-- YouTube -->
                <div class="hex-card group" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-youtube text-3xl text-white"></i>
                    </div>
                    <h3 class="font-cyber text-xl font-bold mb-3 text-pink"><?=__('YOUTUBE')?></h3>
                    <p class="text-gray-400 mb-4 text-sm"><?=__('Subscriber, views và watch time để đạt điều kiện monetize và phát triển kênh bền vững')?></p>
                    <ul class="text-xs space-y-2 text-gray-500">
                        <li><i class="fas fa-check text-pink mr-2"></i><?=__('Subscriber Real')?></li>
                        <li><i class="fas fa-check text-pink mr-2"></i><?=__('4000h Watch Time')?></li>
                        <li><i class="fas fa-check text-pink mr-2"></i><?=__('Shorts Optimization')?></li>
                    </ul>
                </div>
                
                <!-- TikTok -->
                <div class="hex-card group" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-gray-800 to-black flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-tiktok text-3xl text-white"></i>
                    </div>
                    <h3 class="font-cyber text-xl font-bold mb-3 text-yellow"><?=__('TIKTOK')?></h3>
                    <p class="text-gray-400 mb-4 text-sm"><?=__('Tăng followers, likes và views TikTok để video viral trên For You Page với thuật toán mới nhất')?></p>
                    <ul class="text-xs space-y-2 text-gray-500">
                        <li><i class="fas fa-check text-yellow mr-2"></i><?=__('Followers Viral')?></li>
                        <li><i class="fas fa-check text-yellow mr-2"></i><?=__('Views Algorithm')?></li>
                        <li><i class="fas fa-check text-yellow mr-2"></i><?=__('Live Support')?></li>
                    </ul>
                        </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section - Cyber Accordion -->
    <section id="faq" class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 glass px-4 py-2 rounded-full mb-4">
                    <i class="fas fa-question-circle text-neon"></i>
                    <span class="text-sm font-cyber text-neon"><?=__('SUPPORT CENTER')?></span>
                </div>
                <h2 class="font-cyber text-4xl lg:text-6xl font-black mb-6">
                    <span class="text-white"><?=__('FAQ')?></span>
                    <span class="neon-text block"><?=__('SYSTEM')?></span>
                </h2>
            </div>

            <div class="faq-cyber space-y-4">
                <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center">
                        <span><?=__('SMM Panel hoạt động như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer px-6 pb-6 text-gray-400 hidden">
                        <?=__('SMM Panel là hệ thống tự động hóa marketing mạng xã hội. Bạn chỉ cần cung cấp link, hệ thống AI sẽ phân tích và thực hiện tăng tương tác một cách tự nhiên và an toàn nhất.')?>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center">
                        <span><?=__('Thời gian giao hàng bao lâu?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer px-6 pb-6 text-gray-400 hidden">
                        <?=__('Tùy dịch vụ: Likes/Follows 5-30 phút, Views 1-6 giờ, Comments 30 phút-2 giờ. Hệ thống AI đảm bảo tốc độ tối ưu mà vẫn giữ độ tự nhiên.')?>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center">
                        <span><?=__('Dịch vụ có an toàn không?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer px-6 pb-6 text-gray-400 hidden">
                        <?=__('100% an toàn! Công nghệ AI mô phỏng hành vi người dùng thật. Không yêu cầu password, chỉ cần link công khai. 15K+ khách hàng tin tưởng.')?>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center">
                        <span><?=__('Chính sách bảo hành như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer px-6 pb-6 text-gray-400 hidden">
                        <?=__('Bảo hành 30-90 ngày tùy dịch vụ. Auto-refill nếu giảm số lượng. Hoàn tiền 100% nếu không giao hàng sau 24h. Chính sách minh bạch.')?>
                    </div>
                </div>

                <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center">
                        <span><?=__('Phương thức thanh toán nào được hỗ trợ?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer px-6 pb-6 text-gray-400 hidden">
                        <?=__('Đa dạng: Banking, Momo, ZaloPay, ViettelPay, Card điện thoại, Bitcoin và crypto. Auto-payment 24/7, xử lý trong 1-5 phút.')?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - Dark Cyber -->
    <footer class="relative pt-20 pb-8 mt-20">
        <div class="absolute inset-0 bg-gradient-to-t from-darker to-dark"></div>
        <div class="relative max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="<?=base_url($CMSNT->site('logo_dark'));?>" alt="Logo" class="h-10">
        </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        <?=$CMSNT->site('description');?>
                    </p>
                </div>

                <div>
                    <h4 class="font-cyber text-white font-bold mb-4 text-lg"><?=__('MENU')?></h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=__('Trang Chủ')?></a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=__('Dịch Vụ')?></a></li>
                        <li><a href="#faq" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=__('FAQ')?></a></li>
                        <li><a href="<?=base_url('client/services');?>" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=__('Bảng Giá')?></a></li>
                        <li><a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=__('Liên Hệ')?></a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-cyber text-white font-bold mb-4 text-lg"><?=__('DỊCH VỤ')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC LIMIT 5") as $category):?>
                        <li><a href="<?=base_url('service/'.$category['slug']);?>" class="text-gray-400 hover:text-neon transition-colors text-sm"><?=$category['name'];?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>

                <div>
                    <h4 class="font-cyber text-white font-bold mb-4 text-lg"><?=__('LIÊN HỆ')?></h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-neon mr-3 w-4"></i>
                            <?=$CMSNT->site('address')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-neon mr-3 w-4"></i>
                            <?=$CMSNT->site('hotline')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-neon mr-3 w-4"></i>
                            <?=$CMSNT->site('email')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-neon mr-3 w-4"></i>
                            <?=__('24/7 Support')?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm mb-4 md:mb-0">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?>
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?=base_url('client/privacy');?>" class="text-gray-500 hover:text-neon transition-colors text-sm"><?=__('Quyền riêng tư')?></a>
                        <a href="<?=base_url('client/policy');?>" class="text-gray-500 hover:text-neon transition-colors text-sm"><?=__('Chính sách')?></a>
                        <a href="<?=base_url('client/contact');?>" class="text-gray-500 hover:text-neon transition-colors text-sm"><?=__('Liên hệ')?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Cyber Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 glass-dark rounded-xl text-neon hover:text-black hover:bg-neon transition-all duration-300 scale-0 z-50 flex items-center justify-center neon-border">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100
        });

        $(document).ready(function() {
            // Mobile Menu Toggle
            $('#mobileMenuBtn').on('click', function() {
                const $menu = $('#mobileMenu');
                const $icon = $(this).find('i');
                
                $menu.toggleClass('hidden');
                $icon.toggleClass('fa-bars fa-times');
            });

            // FAQ Accordion
            $('.faq-question').on('click', function() {
                const $item = $(this).closest('.faq-item');
                const $answer = $item.find('.faq-answer');
                const $icon = $(this).find('i');
                
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    $answer.slideUp(300);
                    $icon.removeClass('rotate-180');
                } else {
                    $('.faq-item.active').removeClass('active').find('.faq-answer').slideUp(300);
                    $('.faq-question i').removeClass('rotate-180');
                    
                    $item.addClass('active');
                    $answer.slideDown(300);
                    $icon.addClass('rotate-180');
                }
            });

            // Scroll Progress
            $(window).on('scroll', function() {
                const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                $('.scroll-progress').css('width', Math.min(scrolled, 100) + '%');
                
                // Back to Top Button
                if ($(window).scrollTop() > 300) {
                    $('#backToTop').removeClass('scale-0').addClass('scale-100');
                } else {
                    $('#backToTop').removeClass('scale-100').addClass('scale-0');
                }
            });

            // Back to Top Click
            $('#backToTop').on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 800);
            });

            // Smooth Scroll
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                        $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });

            // Close mobile menu on link click
            $('#mobileMenu a').on('click', function() {
                $('#mobileMenu').addClass('hidden');
                $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            });

            console.log('SMM Panel Cyber UI loaded successfully!');
        });
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html>

 

