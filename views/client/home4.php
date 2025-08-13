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
    <meta property="og:image" content="<?=BASE_URL($CMSNT->site('image')); // HÌNH ẢNH CHO FACEBOOK (1200x630px) ?>">
    <meta property="og:image:alt" content="<?=$CMSNT->site('title')?>">
    <meta property="og:site_name" content="<?=$CMSNT->site('title')?>">
    <meta property="og:locale" content="vi_VN">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?=$CMSNT->site('title')?>">
    <meta name="twitter:description" content="<?=$CMSNT->site('description')?>">
    <meta name="twitter:image" content="<?=BASE_URL($CMSNT->site('image')); // HÌNH ẢNH CHO TWITTER (tỷ lệ 2:1) ?>">
    <meta name="twitter:image:alt" content="<?=$CMSNT->site('title')?>">
    <!-- End SEO Meta Tags -->
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#f59e0b',
                        accent: '#10b981',
                        dark: '#0f172a',
                        light: '#f8fafc',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'gradient': 'gradient 15s ease infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'fade-in': 'fadeIn 0.8s ease-out',
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        /* Professional Color Scheme */
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #f59e0b;
            --secondary-dark: #d97706;
            --accent: #10b981;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #f1f5f9;
            --white: #ffffff;
        }
        
        /* Glassmorphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .glass-dark {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Modern Cards */
        .card-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        /* Professional Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.025em;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(59, 130, 246, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Service Cards */
        .service-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: white;
            font-size: 32px;
            transition: all 0.3s ease;
        }
        
        .service-card:hover .service-icon {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            transform: scale(1.1);
        }
        
        /* FAQ */
        .faq-item {
            background: white;
            border-radius: 16px;
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .faq-item:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .faq-question {
            background: #f8fafc;
            color: var(--dark);
            font-weight: 600;
            padding: 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .faq-item.active .faq-question {
            background: var(--primary);
            color: white;
        }
        
        .faq-answer {
            padding: 0 24px 24px 24px;
            color: var(--gray);
            line-height: 1.7;
            display: none;
        }
        
        /* Typography */
        .heading-1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.2;
            margin-bottom: 24px;
        }
        
        .heading-2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.3;
            margin-bottom: 16px;
        }
        
        .heading-3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            line-height: 1.4;
            margin-bottom: 12px;
        }
        
        .text-body {
            font-family: 'Inter', sans-serif;
            font-size: 1.125rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Stats Cards */
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            z-index: 9999;
            transition: width 0.1s ease;
        }
        
        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 56px;
            height: 56px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
            z-index: 1000;
        }
        
        .back-to-top:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .service-card:hover,
            .card-modern:hover {
                transform: none;
            }
            
            .back-to-top {
                width: 48px;
                height: 48px;
                bottom: 24px;
                right: 24px;
                font-size: 18px;
            }
            
            .heading-1 {
                font-size: 2.5rem;
            }
            
            .heading-2 {
                font-size: 2rem;
            }
        }
        
        /* Section Spacing */
        .section-padding {
            padding: 80px 0;
        }
        
        @media (max-width: 768px) {
            .section-padding {
                padding: 60px 0;
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
<body class="overflow-x-hidden">

    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator"></div>

    <!-- Navigation -->
    <nav class="navbar fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="<?=BASE_URL($CMSNT->site('logo_light'));?>" alt="Logo" class="h-8 w-auto">
                </div>
                
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-white/80 hover:text-white transition-colors duration-300 font-medium"><?=__('Trang Chủ')?></a>
                    <a href="#services" class="text-white/80 hover:text-white transition-colors duration-300 font-medium"><?=__('Dịch Vụ')?></a>
                    <a href="#faq" class="text-white/80 hover:text-white transition-colors duration-300 font-medium"><?=__('FAQ')?></a>
                    <a href="<?=base_url('client/services')?>" class="text-white/80 hover:text-white transition-colors duration-300 font-medium"><?=__('Bảng Giá')?></a>
                    <a href="<?=base_url('client/contact');?>" class="text-white/80 hover:text-white transition-colors duration-300 font-medium"><?=__('Liên Hệ')?></a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="<?=base_url('client/login')?>" class="hidden sm:block btn-outline"><?=__('Đăng Nhập')?></a>
                    <a href="<?=base_url('client/register')?>" class="btn-primary"><?=__('Đăng Ký')?></a>
                    <button class="lg:hidden p-2 text-white" id="mobileMenuBtn">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="lg:hidden hidden" id="mobileMenu">
                <div class="glass mt-4 rounded-2xl p-6">
                    <div class="space-y-4">
                        <a href="#home" class="block text-white hover:text-blue-300 transition-colors py-2 font-medium"><?=__('Trang Chủ')?></a>
                        <a href="#services" class="block text-white hover:text-blue-300 transition-colors py-2 font-medium"><?=__('Dịch Vụ')?></a>
                        <a href="#faq" class="block text-white hover:text-blue-300 transition-colors py-2 font-medium"><?=__('FAQ')?></a>
                        <a href="<?=base_url('client/services')?>" class="block text-white hover:text-blue-300 transition-colors py-2 font-medium"><?=__('Bảng Giá')?></a>
                        <a href="<?=base_url('client/contact');?>" class="block text-white hover:text-blue-300 transition-colors py-2 font-medium"><?=__('Liên Hệ')?></a>
                        <div class="pt-4 space-y-3 border-t border-white/20">
                            <a href="<?=base_url('client/login')?>" class="block btn-outline text-center"><?=__('Đăng Nhập')?></a>
                            <a href="<?=base_url('client/register')?>" class="block btn-primary text-center"><?=__('Đăng Ký')?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="min-h-screen flex items-center relative pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-8" data-aos="fade-right">
                    <div class="glass inline-flex items-center space-x-2 px-4 py-2 rounded-full">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-mono text-white font-medium"><?=__('SMM Panel #1 Việt Nam')?></span>
                    </div>
                    
                    <h1 class="heading-1 text-white">
                        <?=__('Tăng Tương Tác')?>
                        <span class="gradient-text block"><?=__('Mạng Xã Hội')?></span>
                        <span class="text-white"><?=__('Chuyên Nghiệp')?></span>
                    </h1>
                    
                    <p class="text-body max-w-lg">
                        <?=__('Nền tảng SMM Panel hàng đầu với công nghệ AI tiên tiến, cung cấp hơn')?> 
                        <span class="text-yellow-400 font-semibold"><?=__('3000+ dịch vụ')?></span> 
                        <?=__('chất lượng cao, nhanh chóng và an toàn cho mọi nền tảng mạng xã hội.')?>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?=base_url('client/register')?>" class="btn-primary">
                            <i class="fas fa-rocket mr-2"></i>
                            <?=__('Bắt Đầu Ngay')?>
                        </a>
                        <a href="<?=base_url('client/login')?>" class="btn-outline">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <?=__('Đăng Nhập')?>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8">
                        <div class="stat-card">
                            <div class="text-2xl font-bold text-white"><?=__('1000+')?></div>
                            <div class="text-white/60 text-sm"><?=__('Khách Hàng')?></div>
                        </div>
                        <div class="stat-card">
                            <div class="text-2xl font-bold text-white"><?=__('3K+')?></div>
                            <div class="text-white/60 text-sm"><?=__('Dịch Vụ')?></div>
                        </div>
                        <div class="stat-card">
                            <div class="text-2xl font-bold text-white"><?=__('24/7')?></div>
                            <div class="text-white/60 text-sm"><?=__('Hỗ Trợ')?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Visual -->
                <div class="relative" data-aos="fade-left">
                    <div class="card-modern p-8 animate-float">
                        <img src="<?=base_url('assets/img/homepage-item1.webp')?>" alt="<?=__('SMM Dashboard')?>" class="w-full rounded-xl">
                        <div class="absolute -top-4 -right-4 w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section-padding bg-white/5 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="glass inline-flex items-center space-x-2 px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-gem text-yellow-400"></i>
                    <span class="text-sm font-mono text-white font-medium"><?=__('Dịch Vụ Chuyên Nghiệp')?></span>
                </div>
                <h2 class="heading-2 text-white mb-6">
                    <?=__('Giải Pháp SMM')?>
                    <span class="gradient-text"><?=__('Toàn Diện')?></span>
                </h2>
                <p class="text-body max-w-3xl mx-auto">
                    <?=__('Trải nghiệm dịch vụ marketing mạng xã hội chất lượng cao với công nghệ tiên tiến và đội ngũ chuyên gia hàng đầu')?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Facebook -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h3 class="heading-3 text-gray-900"><?=__('Facebook Marketing')?></h3>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed"><?=__('Tăng like, follow, share và comment Facebook với chất lượng thật và độ bền vững cao nhất thị trường')?></p>
                    <ul class="text-xs space-y-2 text-gray-500 text-left">
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Like Fanpage Thật')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Follow Profile Chất Lượng')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Comment Tương Tác Cao')?></li>
                    </ul>
                </div>
                
                <!-- Instagram -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="heading-3 text-gray-900"><?=__('Instagram Growth')?></h3>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed"><?=__('Followers chất lượng cao, likes, views story và saves Instagram với tốc độ và độ ổn định tối ưu')?></p>
                    <ul class="text-xs space-y-2 text-gray-500 text-left">
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Followers Chất Lượng')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Story Views Thật')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Reels Viral Boost')?></li>
                    </ul>
                </div>
                
                <!-- YouTube -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="heading-3 text-gray-900"><?=__('YouTube Optimization')?></h3>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed"><?=__('Subscriber thật 100%, views chất lượng và watch time để monetize và phát triển kênh bền vững')?></p>
                    <ul class="text-xs space-y-2 text-gray-500 text-left">
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Subscriber Thật 100%')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('4000h Watch Time')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Shorts Viral')?></li>
                    </ul>
                </div>
                
                <!-- TikTok -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="heading-3 text-gray-900"><?=__('TikTok Viral')?></h3>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed"><?=__('Tăng followers, likes và views TikTok để video viral trên For You Page với thuật toán mới nhất')?></p>
                    <ul class="text-xs space-y-2 text-gray-500 text-left">
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Followers Viral')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Views Algorithm')?></li>
                        <li class="flex items-center"><i class="fas fa-check text-blue-500 mr-2"></i><?=__('Live Stream Boost')?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section-padding bg-black/10 backdrop-blur-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="glass inline-flex items-center space-x-2 px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-question-circle text-green-400"></i>
                    <span class="text-sm font-mono text-white font-medium"><?=__('Hỗ Trợ 24/7')?></span>
                </div>
                <h2 class="heading-2 text-white mb-6">
                    <?=__('Câu Hỏi')?>
                    <span class="gradient-text"><?=__('Thường Gặp')?></span>
                </h2>
                <p class="text-body">
                    <?=__('Giải đáp mọi thắc mắc về dịch vụ SMM Panel của chúng tôi')?>
                </p>
            </div>
            
            <div class="space-y-4">
                <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-question">
                        <span><?=__('SMM Panel hoạt động như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('SMM Panel của chúng tôi là hệ thống tự động hóa marketing mạng xã hội tiên tiến. Sử dụng công nghệ AI và mạng lưới người dùng thật để cung cấp dịch vụ chất lượng cao nhất, đảm bảo an toàn và hiệu quả.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-question">
                        <span><?=__('Thời gian giao hàng ra sao?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('⚡ Siêu nhanh: 5-15 phút cho likes/follows, 30 phút-2 giờ cho views, comments trong 15-60 phút. Tất cả được tối ưu hóa cho chất lượng và độ bền cao nhất.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-question">
                        <span><?=__('Dịch vụ có an toàn không?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('🛡️ Hoàn toàn an toàn! Chúng tôi sử dụng công nghệ AI cao cấp để mô phỏng hành vi người dùng thật. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ 15K+ khách hàng tin tưởng.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="faq-question">
                        <span><?=__('Chính sách bảo hành như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('💎 Bảo hành 60-120 ngày tùy dịch vụ. Auto-refill tự động nếu giảm số lượng. Hoàn tiền 100% nếu không giao hàng đúng cam kết. Chính sách minh bạch, uy tín.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="faq-question">
                        <span><?=__('Phương thức thanh toán nào được hỗ trợ?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('💳 Đa dạng phương thức: Banking, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào. Xử lý tự động 24/7 trong 1-3 phút, nhanh chóng tiện lợi.')?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="section-padding bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center mb-6">
                        <img src="<?=base_url($CMSNT->site('logo_dark'));?>" alt="Logo" class="h-10">
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        <?=$CMSNT->site('description');?>
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6 text-lg"><?=__('Menu')?></h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Trang Chủ')?></a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Dịch Vụ')?></a></li>
                        <li><a href="#faq" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('FAQ')?></a></li>
                        <li><a href="<?=base_url('client/services');?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Bảng Giá')?></a></li>
                        <li><a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Liên Hệ')?></a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6 text-lg"><?=__('Dịch Vụ Hot')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC LIMIT 5") as $category):?>
                        <li><a href="<?=base_url('service/'.$category['slug']);?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=$category['name'];?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6 text-lg"><?=__('Liên Hệ')?></h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-blue-400 text-xs"></i>
                            </div>
                            <span><?=$CMSNT->site('address')?></span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-phone text-green-400 text-xs"></i>
                            </div>
                            <span><?=$CMSNT->site('hotline')?></span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-envelope text-yellow-400 text-xs"></i>
                            </div>
                            <span><?=$CMSNT->site('email')?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?> - <?=__('SMM Panel Chuyên Nghiệp')?>
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?=base_url('client/privacy');?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Quyền riêng tư')?></a>
                        <a href="<?=base_url('client/policy');?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Chính sách')?></a>
                        <a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?=__('Liên hệ')?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <div class="back-to-top opacity-0 invisible transition-all duration-300" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
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

            // Scroll Progress & Back to Top
            $(window).on('scroll', function() {
                const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                $('.scroll-indicator').css('width', Math.min(scrolled, 100) + '%');
                
                // Back to Top Button
                const $backToTop = $('#backToTop');
                if ($(window).scrollTop() > 300) {
                    $backToTop.removeClass('opacity-0 invisible').addClass('opacity-100 visible');
                } else {
                    $backToTop.removeClass('opacity-100 visible').addClass('opacity-0 invisible');
                }
            });

            // Back to Top Click
            $('#backToTop').on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 600);
            });

            // Smooth Scroll
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 600);
                }
            });

            // Close mobile menu on link click
            $('#mobileMenu a').on('click', function() {
                $('#mobileMenu').addClass('hidden');
                $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            });

            console.log('✅ SMM Panel Professional UI loaded successfully!');
        });
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html>

 

