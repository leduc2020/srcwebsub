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
                        luxury: {
                            gold: '#D4AF37',
                            darkgold: '#B8860B',
                            black: '#0F0F0F',
                            gray: '#1a1a1a',
                            lightgray: '#f8f9fa',
                            accent: '#FF6B35',
                        }
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'slide-in-right': 'slideInRight 1s ease-out',
                        'pulse-gold': 'pulseGold 2s ease-in-out infinite',
                        'float-gentle': 'floatGentle 4s ease-in-out infinite',
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;900&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }
        
        /* Luxury Gold Theme */
        .bg-luxury-gradient {
            background: linear-gradient(135deg, #D4AF37 0%, #B8860B 50%, #0F0F0F 100%);
        }
        
        .text-gradient-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Premium Cards */
        .card-luxury {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.1);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .card-luxury:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(212, 175, 55, 0.2);
            border-color: rgba(212, 175, 55, 0.4);
        }
        
        /* Premium Navigation */
        .nav-luxury {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        /* Elegant Buttons */
        .btn-luxury {
            background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .btn-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-luxury:hover::before {
            left: 100%;
        }
        
        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        
        .btn-outline-luxury {
            border: 2px solid #D4AF37;
            color: #D4AF37;
            background: transparent;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-luxury:hover {
            background: #D4AF37;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        
        /* Service Cards with Elegant Design */
        .service-luxury {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .service-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #D4AF37, #B8860B);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .service-luxury:hover::before {
            transform: scaleX(1);
        }
        
        .service-luxury:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }
        
        /* FAQ Luxury Style */
        .faq-luxury .faq-item {
            background: white;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-luxury .faq-item:hover {
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.1);
        }
        
        .faq-luxury .faq-question {
            background: rgba(212, 175, 55, 0.05);
            color: #0F0F0F;
            font-weight: 600;
            padding: 1.5rem 2rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .faq-luxury .faq-item.active .faq-question {
            background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
            color: white;
        }
        
        .faq-luxury .faq-answer {
            padding: 0 2rem 1.5rem 2rem;
            color: #666;
            line-height: 1.6;
            display: none;
        }
        
        /* Custom Animations */
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
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulseGold {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(212, 175, 55, 0);
            }
        }
        
        @keyframes floatGentle {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        /* Hero Background */
        .hero-luxury {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(184, 134, 11, 0.1) 100%);
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
        }
        
        /* Scroll Progress */
        .scroll-progress-luxury {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #D4AF37, #B8860B);
            z-index: 9999;
            transition: width 0.1s ease;
        }
        
        /* Footer Luxury */
        .footer-luxury {
            background: linear-gradient(135deg, #0F0F0F 0%, #1a1a1a 100%);
            color: white;
            position: relative;
        }
        
        .footer-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .card-luxury:hover,
            .service-luxury:hover {
                transform: none;
            }
            
            .hero-luxury::before {
                display: none;
            }
        }
        
        /* Typography Enhancements */
        .text-luxury-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .text-luxury-subtitle {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            line-height: 1.6;
        }
        
        /* Icon Styling */
        .icon-luxury {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        
        .service-luxury:hover .icon-luxury {
            transform: scale(1.1) rotate(5deg);
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
<body class="bg-luxury-lightgray overflow-x-hidden">

    <!-- Scroll Progress -->
    <div class="scroll-progress-luxury"></div>

    <!-- Navigation -->
    <nav class="nav-luxury fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="<?=BASE_URL($CMSNT->site('logo_light'));?>" alt="Logo" class="h-8 w-auto">
                </div>
                
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-luxury-gray hover:text-luxury-gold transition-colors duration-300 font-medium"><?=__('Trang Chủ')?></a>
                    <a href="#services" class="text-luxury-gray hover:text-luxury-gold transition-colors duration-300 font-medium"><?=__('Dịch Vụ')?></a>
                    <a href="#faq" class="text-luxury-gray hover:text-luxury-gold transition-colors duration-300 font-medium"><?=__('FAQ')?></a>
                    <a href="<?=base_url('client/services')?>" class="text-luxury-gray hover:text-luxury-gold transition-colors duration-300 font-medium"><?=__('Bảng Giá')?></a>
                    <a href="<?=base_url('client/contact');?>" class="text-luxury-gray hover:text-luxury-gold transition-colors duration-300 font-medium"><?=__('Liên Hệ')?></a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="<?=base_url('client/login')?>" class="hidden sm:block btn-outline-luxury px-6 py-2 rounded-lg"><?=__('Đăng Nhập')?></a>
                    <a href="<?=base_url('client/register')?>" class="btn-luxury px-6 py-2 rounded-lg"><?=__('Đăng Ký')?></a>
                    <button class="lg:hidden p-2 text-luxury-gold" id="mobileMenuBtn">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="lg:hidden mt-4 pb-4 border-t border-luxury-gold/20 hidden" id="mobileMenu">
                <div class="space-y-3 pt-4">
                    <a href="#home" class="block text-luxury-gray hover:text-luxury-gold transition-colors py-2"><?=__('Trang Chủ')?></a>
                    <a href="#services" class="block text-luxury-gray hover:text-luxury-gold transition-colors py-2"><?=__('Dịch Vụ')?></a>
                    <a href="#faq" class="block text-luxury-gray hover:text-luxury-gold transition-colors py-2"><?=__('FAQ')?></a>
                    <a href="<?=base_url('client/services')?>" class="block text-luxury-gray hover:text-luxury-gold transition-colors py-2"><?=__('Bảng Giá')?></a>
                    <a href="<?=base_url('client/contact');?>" class="block text-luxury-gray hover:text-luxury-gold transition-colors py-2"><?=__('Liên Hệ')?></a>
                    <div class="pt-3 space-y-2">
                        <a href="<?=base_url('client/login')?>" class="block btn-outline-luxury px-4 py-2 rounded-lg text-center"><?=__('Đăng Nhập')?></a>
                        <a href="<?=base_url('client/register')?>" class="block btn-luxury px-4 py-2 rounded-lg text-center"><?=__('Đăng Ký')?></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-luxury min-h-screen flex items-center relative pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-8" data-aos="fade-right">
                    <div class="inline-flex items-center space-x-2 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-full border border-luxury-gold/20">
                        <div class="w-2 h-2 bg-luxury-gold rounded-full animate-pulse-gold"></div>
                        <span class="text-sm font-mono text-luxury-gray font-medium"><?=__('Premium SMM Panel')?></span>
                    </div>
                    
                    <h1 class="text-luxury-title text-5xl lg:text-7xl text-luxury-black leading-tight">
                        <?=__('Nâng Tầm')?>
                        <span class="text-gradient-gold block"><?=__('Social Media')?></span>
                        <span class="text-luxury-accent"><?=__('Của Bạn')?></span>
                    </h1>
                    
                    <p class="text-luxury-subtitle text-xl text-luxury-gray leading-relaxed max-w-lg">
                        <?=__('Trải nghiệm dịch vụ SMM Panel cao cấp với')?> 
                        <span class="text-luxury-gold font-semibold"><?=__('công nghệ tiên tiến')?></span> 
                        <?=__('và')?> <span class="text-luxury-gold font-semibold"><?=__('chất lượng đảm bảo')?></span>. 
                        <?=__('Hơn 3000+ dịch vụ premium cho mọi nền tảng.')?>
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?=base_url('client/register')?>" class="btn-luxury px-8 py-4 rounded-xl text-lg inline-flex items-center justify-center">
                            <i class="fas fa-crown mr-2"></i>
                            <?=__('Bắt Đầu Ngay')?>
                        </a>
                        <a href="<?=base_url('client/login')?>" class="btn-outline-luxury px-8 py-4 rounded-xl text-lg inline-flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <?=__('Đăng Nhập')?>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex space-x-8 pt-8">
                        <div class="text-center">
                            <div class="text-3xl text-luxury-title text-luxury-black font-bold">15K+</div>
                            <div class="text-luxury-gray text-sm"><?=__('Khách Hàng VIP')?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl text-luxury-title text-luxury-black font-bold">3K+</div>
                            <div class="text-luxury-gray text-sm"><?=__('Dịch Vụ Premium')?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl text-luxury-title text-luxury-black font-bold">24/7</div>
                            <div class="text-luxury-gray text-sm"><?=__('Hỗ Trợ Cao Cấp')?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Visual -->
                <div class="relative" data-aos="fade-left">
                    <div class="card-luxury p-8 rounded-3xl relative animate-float-gentle">
                        <img src="<?=base_url('assets/img/homepage-item1.webp')?>" alt="<?=__('SMM Dashboard Premium')?>" class="w-full rounded-2xl">
                        <div class="absolute -top-6 -right-6 bg-luxury-gradient p-4 rounded-2xl text-white">
                            <i class="fas fa-chart-trending-up text-2xl"></i>
                        </div>
                        <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-2xl shadow-lg">
                            <i class="fas fa-shield-check text-2xl text-luxury-gold"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 bg-luxury-gold/10 px-4 py-2 rounded-full border border-luxury-gold/20 mb-4">
                    <i class="fas fa-gem text-luxury-gold"></i>
                    <span class="text-sm font-mono text-luxury-gold font-medium"><?=__('Dịch Vụ Cao Cấp')?></span>
                </div>
                <h2 class="text-luxury-title text-4xl lg:text-6xl text-luxury-black mb-6">
                    <?=__('Giải Pháp')?>
                    <span class="text-gradient-gold"><?=__('SMM Premium')?></span>
                </h2>
                <p class="text-luxury-subtitle text-xl text-luxury-gray max-w-3xl mx-auto">
                    <?=__('Trải nghiệm dịch vụ SMM chất lượng cao với công nghệ tiên tiến và đội ngũ chuyên gia hàng đầu')?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Facebook -->
                <div class="service-luxury" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-luxury">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h3 class="text-luxury-title text-xl text-luxury-black mb-3"><?=__('Facebook Premium')?></h3>
                    <p class="text-luxury-gray text-sm mb-4 leading-relaxed"><?=__('Tăng like, follow, share và comment Facebook với chất lượng cao và độ bền vững tuyệt đối')?></p>
                    <ul class="text-xs space-y-2 text-luxury-gray">
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Like Fanpage VIP')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Follow Profile Cao Cấp')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Comment Chất Lượng')?></li>
                    </ul>
                </div>
                
                <!-- Instagram -->
                <div class="service-luxury" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-luxury">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="text-luxury-title text-xl text-luxury-black mb-3"><?=__('Instagram Elite')?></h3>
                    <p class="text-luxury-gray text-sm mb-4 leading-relaxed"><?=__('Followers chất lượng cao, likes, views story và saves Instagram với tốc độ và độ ổn định tối ưu')?></p>
                    <ul class="text-xs space-y-2 text-luxury-gray">
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Followers Premium')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Story Views Elite')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Reels Boost Pro')?></li>
                    </ul>
                </div>
                
                <!-- YouTube -->
                <div class="service-luxury" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-luxury">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="text-luxury-title text-xl text-luxury-black mb-3"><?=__('YouTube Pro')?></h3>
                    <p class="text-luxury-gray text-sm mb-4 leading-relaxed"><?=__('Subscriber thật, views chất lượng và watch time để monetize và phát triển kênh bền vững lâu dài')?></p>
                    <ul class="text-xs space-y-2 text-luxury-gray">
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Subscriber Thật 100%')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('4000h Watch Time')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Shorts Viral Pro')?></li>
                    </ul>
                </div>
                
                <!-- TikTok -->
                <div class="service-luxury" data-aos="fade-up" data-aos-delay="400">
                    <div class="icon-luxury">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="text-luxury-title text-xl text-luxury-black mb-3"><?=__('TikTok Viral')?></h3>
                    <p class="text-luxury-gray text-sm mb-4 leading-relaxed"><?=__('Tăng followers, likes và views TikTok để video viral trên For You Page với thuật toán mới nhất')?></p>
                    <ul class="text-xs space-y-2 text-luxury-gray">
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Followers Viral')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Views Algorithm Pro')?></li>
                        <li><i class="fas fa-check text-luxury-gold mr-2"></i><?=__('Live Stream VIP')?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-luxury-lightgray">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <div class="inline-flex items-center space-x-2 bg-luxury-gold/10 px-4 py-2 rounded-full border border-luxury-gold/20 mb-4">
                    <i class="fas fa-question-circle text-luxury-gold"></i>
                    <span class="text-sm font-mono text-luxury-gold font-medium"><?=__('Hỗ Trợ Premium')?></span>
                </div>
                <h2 class="text-luxury-title text-4xl lg:text-6xl text-luxury-black mb-6">
                    <?=__('Câu Hỏi')?>
                    <span class="text-gradient-gold"><?=__('Thường Gặp')?></span>
                </h2>
            </div>
            
            <div class="faq-luxury space-y-4">
                <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-question">
                        <span><?=__('SMM Panel Premium hoạt động như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('SMM Panel Premium là hệ thống tự động hóa marketing mạng xã hội cao cấp. Chúng tôi sử dụng công nghệ AI tiên tiến và mạng lưới người dùng thật để cung cấp dịch vụ chất lượng cao nhất.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-question">
                        <span><?=__('Thời gian giao hàng dịch vụ premium?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('Dịch vụ Premium: 5-15 phút cho likes/follows, 30 phút-2 giờ cho views, comments trong 15-60 phút. Tất cả được tối ưu hóa cho chất lượng và độ bền cao.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-question">
                        <span><?=__('Dịch vụ Premium có đảm bảo an toàn?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('Hoàn toàn an toàn 100%! Chúng tôi sử dụng công nghệ AI cao cấp để mô phỏng hành vi người dùng thật. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ 15K+ khách hàng VIP.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="faq-question">
                        <span><?=__('Chính sách bảo hành Premium như thế nào?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('Bảo hành Premium 60-120 ngày tùy dịch vụ. Auto-refill tự động nếu giảm số lượng. Hoàn tiền 100% nếu không giao hàng đúng cam kết. Chính sách minh bạch, uy tín.')?>
                    </div>
                </div>
                
                <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="faq-question">
                        <span><?=__('Các phương thức thanh toán Premium?')?></span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <?=__('Hỗ trợ tất cả phương thức: Banking VIP, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào, cryptocurrency. Xử lý tự động 24/7 trong 1-3 phút.')?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-luxury pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                    <h4 class="text-luxury-title text-white font-bold mb-4 text-lg"><?=__('Menu')?></h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=__('Trang Chủ')?></a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=__('Dịch Vụ')?></a></li>
                        <li><a href="#faq" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=__('FAQ')?></a></li>
                        <li><a href="<?=base_url('client/services');?>" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=__('Bảng Giá')?></a></li>
                        <li><a href="<?=base_url('client/contact');?>" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=__('Liên Hệ')?></a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-luxury-title text-white font-bold mb-4 text-lg"><?=__('Dịch Vụ')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC LIMIT 5") as $category):?>
                        <li><a href="<?=base_url('service/'.$category['slug']);?>" class="text-gray-400 hover:text-luxury-gold transition-colors text-sm"><?=$category['name'];?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-luxury-title text-white font-bold mb-4 text-lg"><?=__('Liên Hệ')?></h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-luxury-gold mr-3 w-4"></i>
                            <?=$CMSNT->site('address')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-luxury-gold mr-3 w-4"></i>
                            <?=$CMSNT->site('hotline')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-luxury-gold mr-3 w-4"></i>
                            <?=$CMSNT->site('email')?>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-crown text-luxury-gold mr-3 w-4"></i>
                            <?=__('Hỗ Trợ Premium 24/7')?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm mb-4 md:mb-0">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?>
                    </p>
                    <div class="flex space-x-6">
                        <a href="<?=base_url('client/privacy');?>" class="text-gray-500 hover:text-luxury-gold transition-colors text-sm"><?=__('Quyền riêng tư')?></a>
                        <a href="<?=base_url('client/policy');?>" class="text-gray-500 hover:text-luxury-gold transition-colors text-sm"><?=__('Chính sách')?></a>
                        <a href="<?=base_url('client/contact');?>" class="text-gray-500 hover:text-luxury-gold transition-colors text-sm"><?=__('Liên hệ')?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-luxury-gradient rounded-xl text-white hover:scale-110 transition-all duration-300 scale-0 z-50 flex items-center justify-center shadow-lg">
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
                $('.scroll-progress-luxury').css('width', Math.min(scrolled, 100) + '%');
                
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
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });

            // Close mobile menu on link click
            $('#mobileMenu a').on('click', function() {
                $('#mobileMenu').addClass('hidden');
                $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            });

            console.log('Luxury SMM Panel UI loaded successfully!');
        });
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html>

 

