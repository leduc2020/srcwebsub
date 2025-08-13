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
                        'minimal-gray': '#1a1a1a',
                        'minimal-dark': '#ffffff',
                        'minimal-medium': '#a0a0a0',
                        'minimal-light': '#2d2d2d',
                        'minimal-accent': '#ffffff',
                        'minimal-bg': '#0f0f0f',
                        'minimal-border': '#333333',
                    },
                    fontFamily: {
                        'minimal': ['Inter', 'system-ui', 'sans-serif'],
                        'minimal-mono': ['JetBrains Mono', 'Monaco', 'monospace'],
                    },
                    animation: {
                        'fade-in': 'fade-in 0.6s ease-out',
                        'slide-up': 'slide-up 0.5s ease-out',
                        'minimal-hover': 'minimal-hover 0.3s ease',
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                        '128': '32rem',
                    },
                    boxShadow: {
                        'minimal': '0 1px 3px rgba(0, 0, 0, 0.1)',
                        'minimal-md': '0 4px 6px rgba(0, 0, 0, 0.07)',
                        'minimal-lg': '0 10px 15px rgba(0, 0, 0, 0.1)',
                        'minimal-xl': '0 20px 25px rgba(0, 0, 0, 0.1)',
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=JetBrains+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&subset=vietnamese&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        * {
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        /* Dark Minimalist Background */
        body {
            background-color: #0f0f0f;
            color: #ffffff;
            line-height: 1.6;
            font-weight: 400;
        }
        
        /* Minimalist Animations */
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes minimal-hover {
            from { transform: translateY(0); }
            to { transform: translateY(-2px); }
        }
        
        /* Dark Minimalist Components */
        .minimal-card {
            background: #1a1a1a;
            border: 1px solid #333333;
            transition: all 0.3s ease;
        }
        
        .minimal-card:hover {
            border-color: #ffffff;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        }
        
        .minimal-button {
            background: #ffffff;
            color: #0f0f0f;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .minimal-button:hover {
            background: transparent;
            color: #ffffff;
        }
        
        .minimal-button-outline {
            background: transparent;
            color: #ffffff;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .minimal-button-outline:hover {
            background: #ffffff;
            color: #0f0f0f;
        }
        
        /* Clean Typography */
        .minimal-title {
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.1;
        }
        
        .minimal-subtitle {
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        
        .minimal-text {
            font-weight: 400;
            color: #6c757d;
        }
        
        /* Clean Lines */
        .minimal-divider {
            width: 60px;
            height: 2px;
            background: #ffffff;
            margin: 0 auto;
        }
        
        /* Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 2px;
            background: #ffffff;
            z-index: 9999;
            transition: width 0.3s ease;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #ffffff;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .minimal-spacing {
                padding: 2rem 1rem;
            }
            
            .minimal-text-mobile {
                font-size: 0.95rem;
            }
        }
        
        /* Focus States */
        .minimal-focus:focus {
            outline: 2px solid #212529;
            outline-offset: 2px;
        }
        
        /* Minimal Icons */
        .minimal-icon {
            width: 48px;
            height: 48px;
            border: 2px solid #333333;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .minimal-icon:hover {
            border-color: #ffffff;
            transform: translateY(-1px);
        }
        
        /* Clean Grid */
        .minimal-grid {
            display: grid;
            gap: 2rem;
        }
        
        @media (min-width: 768px) {
            .minimal-grid {
                gap: 3rem;
            }
        }
        
        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
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
      }
    }
    </script>
    <?=$CMSNT->site('javascript_header');?>
</head>

<body class="bg-minimal-bg text-minimal-dark overflow-x-hidden">
    <!-- Progress Bar -->
    <div class="progress-bar"></div>
    
    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-minimal-light text-minimal-dark border border-minimal-border rounded-full opacity-0 transition-all duration-300 z-50 flex items-center justify-center hover:bg-minimal-border hover:text-minimal-accent minimal-focus">
        <i class="fas fa-chevron-up text-sm"></i>
    </button>

    <!-- Dark Minimalist Header -->
    <header class="fixed w-full top-0 z-50 bg-minimal-bg/90 backdrop-blur-sm border-b border-minimal-border transition-all duration-300" id="navbar">
        <nav class="container mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?=base_url();?>" class="flex items-center group">    
                        <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=$CMSNT->site('title')?>" class="h-8 w-auto transition-opacity duration-300 group-hover:opacity-80">
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-12">
                    <a href="#home" class="text-minimal-dark hover:text-minimal-medium transition-colors duration-300 font-medium minimal-focus">
                        <?=__('Trang Chủ')?>
                    </a>
                    <a href="#services" class="text-minimal-dark hover:text-minimal-medium transition-colors duration-300 font-medium minimal-focus">
                        <?=__('Dịch Vụ')?>
                    </a>
                    <a href="#faq" class="text-minimal-dark hover:text-minimal-medium transition-colors duration-300 font-medium minimal-focus">
                        <?=__('FAQ')?>
                    </a>
                    <a href="<?=base_url('client/services')?>" class="text-minimal-dark hover:text-minimal-medium transition-colors duration-300 font-medium minimal-focus">
                        <?=__('Bảng Giá')?>
                    </a>
                    <a href="<?=base_url('client/contact');?>" class="text-minimal-dark hover:text-minimal-medium transition-colors duration-300 font-medium minimal-focus">
                        <?=__('Liên Hệ')?>
                    </a>
                </div>
                
                <!-- Header Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="<?=base_url('client/register')?>" class="hidden sm:block minimal-button-outline px-6 py-2 rounded-none text-sm minimal-focus">
                        <?=__('Đăng Ký')?>
                    </a>
                    <a href="<?=base_url('client/login')?>" class="hidden sm:block minimal-button px-6 py-2 rounded-none text-sm minimal-focus">
                        <?=__('Đăng Nhập')?>
                    </a>
                    <button class="lg:hidden p-2 minimal-focus" id="mobileMenuBtn">
                        <i class="fas fa-bars text-lg text-minimal-dark"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="lg:hidden hidden border-t border-minimal-border bg-minimal-bg" id="mobileMenu">
                <div class="py-4 space-y-1">
                    <a href="#home" class="block py-3 px-4 text-minimal-dark hover:bg-minimal-gray transition-colors duration-300 minimal-focus">
                        <?=__('Trang Chủ')?>
                    </a>
                    <a href="#services" class="block py-3 px-4 text-minimal-dark hover:bg-minimal-gray transition-colors duration-300 minimal-focus">
                        <?=__('Dịch Vụ')?>
                    </a>
                    <a href="#faq" class="block py-3 px-4 text-minimal-dark hover:bg-minimal-gray transition-colors duration-300 minimal-focus">
                        <?=__('FAQ')?>
                    </a>
                    <a href="<?=base_url('client/services')?>" class="block py-3 px-4 text-minimal-dark hover:bg-minimal-gray transition-colors duration-300 minimal-focus">
                        <?=__('Bảng Giá')?>
                    </a>
                    <a href="<?=base_url('client/contact')?>" class="block py-3 px-4 text-minimal-dark hover:bg-minimal-gray transition-colors duration-300 minimal-focus">
                        <?=__('Liên Hệ')?>
                    </a>
                </div>
                
                <div class="p-4 border-t border-minimal-border space-y-3">
                    <a href="<?=base_url('client/login')?>" class="block minimal-button text-center py-3 rounded-none minimal-focus">
                        <?=__('Đăng Nhập')?>
                    </a>
                    <a href="<?=base_url('client/register')?>" class="block minimal-button-outline text-center py-3 rounded-none minimal-focus">
                        <?=__('Đăng Ký')?>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Minimalist Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center pt-16">
        <div class="container mx-auto px-4 sm:px-6 py-20">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="inline-flex items-center border border-minimal-border px-4 py-2 rounded-none mb-12 text-sm font-medium">
                        <span class="w-2 h-2 bg-minimal-accent rounded-full mr-3"></span>
                        <?=__('SMM Panel Uy Tín #1 Việt Nam')?>
                    </div>
                    
                    <!-- Main Heading -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl minimal-title mb-8">
                        <span class="block text-minimal-dark"><?=__('TĂNG TƯƠNG TÁC')?></span>
                        <span class="block text-minimal-medium"><?=__('MẠNG XÃ HỘI')?></span>
                    </h1>
                    
                    <!-- Divider -->
                    <div class="minimal-divider mb-8 lg:mx-0"></div>
                    
                    <!-- Description -->
                    <p class="text-lg text-minimal-medium mb-12 leading-relaxed max-w-2xl lg:mx-0 mx-auto">
                        <?=__('Nền tảng SMM Panel chuyên nghiệp với hơn 3000+ dịch vụ cho tất cả các nền tảng mạng xã hội. Tăng followers, likes, views nhanh chóng & an toàn.')?>
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-16 justify-center lg:justify-start">
                        <a href="<?=base_url('client/login')?>" class="minimal-button px-8 py-4 rounded-none text-base font-medium minimal-focus">
                            <?=__('ĐĂNG NHẬP NGAY')?>
                        </a>
                        <a href="<?=base_url('client/register')?>" class="minimal-button-outline px-8 py-4 rounded-none text-base font-medium minimal-focus">
                            <?=__('ĐĂNG KÝ NGAY')?>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-minimal-dark mb-2 minimal-title"><?=__('15K+')?></div>
                            <div class="text-sm text-minimal-medium uppercase tracking-wide"><?=__('Khách hàng')?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-minimal-dark mb-2 minimal-title"><?=__('3000+')?></div>
                            <div class="text-sm text-minimal-medium uppercase tracking-wide"><?=__('Dịch vụ')?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-minimal-dark mb-2 minimal-title"><?=__('99.9%')?></div>
                            <div class="text-sm text-minimal-medium uppercase tracking-wide"><?=__('Uptime')?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="relative order-first lg:order-last" data-aos="fade-left">
                    <div class="relative">
                        <!-- Main Dashboard Image -->
                        <div class="minimal-card p-2 rounded-none shadow-minimal-lg">
                            <img src="<?=base_url('assets/img/homepage-item1.webp')?>" 
                                 alt="<?=__('SMM Panel Dashboard')?>" 
                                 class="w-full shadow-minimal">
                        </div>
                        
                        <!-- Simple Info Cards -->
                        <div class="absolute -top-4 -right-4 minimal-card p-4 rounded-none shadow-minimal hidden sm:block">
                            <div class="text-minimal-dark">
                                <div class="text-xl font-bold"><?=__('99.9%')?></div>
                                <div class="text-xs text-minimal-medium uppercase tracking-wide"><?=__('Uptime')?></div>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-4 -left-4 minimal-card p-4 rounded-none shadow-minimal hidden sm:block">
                            <div class="text-minimal-dark">
                                <div class="text-xl font-bold"><?=__('2M+')?></div>
                                <div class="text-xs text-minimal-medium uppercase tracking-wide"><?=__('Orders')?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Simple Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-minimal-medium hidden lg:block">
            <div class="flex flex-col items-center">
                <span class="text-xs uppercase tracking-wide mb-3"><?=__('Scroll')?></span>
                <div class="w-px h-12 bg-minimal-border"></div>
            </div>
        </div>
    </section>

    <!-- Minimalist Services Section -->
    <section id="services" class="py-24 bg-minimal-gray">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Section Header -->
            <div class="text-center mb-20" data-aos="fade-up">
                <div class="inline-flex items-center border border-minimal-border px-4 py-2 rounded-none mb-8 text-sm font-medium">
                    <?=__('Dịch Vụ SMM Chuyên Nghiệp')?>
                </div>
                <h2 class="text-3xl sm:text-5xl minimal-title mb-6">
                    <?=__('GIẢI PHÁP SMM TOÀN DIỆN')?>
                </h2>
                <div class="minimal-divider mb-8"></div>
                <p class="text-lg text-minimal-medium max-w-2xl mx-auto leading-relaxed">
                    <?=__('Từ tăng followers, likes, views đến quản lý chiến dịch marketing - chúng tôi cung cấp mọi dịch vụ bạn cần để phát triển mạnh mẽ trên social media')?>
                </p>
            </div>
            
            <!-- Services Grid -->
            <div class="minimal-grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                <!-- Facebook Service -->
                <div class="minimal-card p-8 rounded-none group" data-aos="fade-up" data-aos-delay="100">
                    <div class="minimal-icon rounded-none mx-auto mb-6 group-hover:border-blue-500 transition-colors duration-300">
                        <i class="fab fa-facebook-f text-xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl minimal-subtitle mb-4 text-center"><?=__('Facebook Marketing')?></h3>
                    <p class="minimal-text mb-6 text-center text-sm leading-relaxed">
                        <?=__('Tăng like fanpage, like bài viết, share, comment và follow với người dùng Việt Nam thật 100%')?>
                    </p>
                                         <div class="space-y-3">
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Like Fanpage & Bài Viết')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Follow & Comment Tương Tác')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Share & Reaction Đa Dạng')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Instagram Service -->
                <div class="minimal-card p-8 rounded-none group" data-aos="fade-up" data-aos-delay="200">
                    <div class="minimal-icon rounded-none mx-auto mb-6 group-hover:border-pink-500 transition-colors duration-300">
                        <i class="fab fa-instagram text-xl text-pink-500"></i>
                    </div>
                    <h3 class="text-xl minimal-subtitle mb-4 text-center"><?=__('Instagram Growth')?></h3>
                    <p class="minimal-text mb-6 text-center text-sm leading-relaxed">
                        <?=__('Tăng followers, likes, views story, saves và comments Instagram với chất lượng cao và tốc độ nhanh')?>
                    </p>
                                         <div class="space-y-3">
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Followers & Likes Chất Lượng')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Story Views & Saves')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Reels & IGTV Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- YouTube Service -->
                <div class="minimal-card p-8 rounded-none group" data-aos="fade-up" data-aos-delay="300">
                    <div class="minimal-icon rounded-none mx-auto mb-6 group-hover:border-red-500 transition-colors duration-300">
                        <i class="fab fa-youtube text-xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl minimal-subtitle mb-4 text-center"><?=__('YouTube Optimization')?></h3>
                    <p class="minimal-text mb-6 text-center text-sm leading-relaxed">
                        <?=__('Tăng subscriber, views, watch time và likes YouTube để đạt điều kiện kiếm tiền và phát triển kênh')?>
                    </p>
                                         <div class="space-y-3">
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Subscriber & Views Thật')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Watch Time 4000 Giờ')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('YouTube Shorts Boost')?></span>
                        </div>
                    </div>
                </div>
                
                <!-- TikTok Service -->
                <div class="minimal-card p-8 rounded-none group" data-aos="fade-up" data-aos-delay="400">
                    <div class="minimal-icon rounded-none mx-auto mb-6 group-hover:border-gray-800 transition-colors duration-300">
                        <i class="fab fa-tiktok text-xl text-gray-800"></i>
                    </div>
                    <h3 class="text-xl minimal-subtitle mb-4 text-center"><?=__('TikTok Viral')?></h3>
                    <p class="minimal-text mb-6 text-center text-sm leading-relaxed">
                        <?=__('Tăng followers, likes, views và shares TikTok để video viral và tăng độ phủ sóng trên For You Page')?>
                    </p>
                                         <div class="space-y-3">
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Followers & Likes TikTok')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Views & Shares Viral')?></span>
                        </div>
                        <div class="flex items-center text-xs text-minimal-medium">
                            <div class="w-1 h-1 bg-minimal-accent rounded-full mr-3"></div>
                            <span><?=__('Live Stream Support')?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Minimalist FAQ Section -->
    <section id="faq" class="py-24">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Section Header -->
            <div class="text-center mb-20" data-aos="fade-up">
                <div class="inline-flex items-center border border-minimal-border px-4 py-2 rounded-none mb-8 text-sm font-medium">
                    <?=__('Hỗ Trợ Khách Hàng')?>
                </div>
                <h2 class="text-3xl sm:text-5xl minimal-title mb-6">
                    <?=__('CÂU HỎI THƯỜNG GẶP')?>
                </h2>
                <div class="minimal-divider mb-8"></div>
                <p class="text-lg text-minimal-medium max-w-2xl mx-auto leading-relaxed">
                    <?=__('Tìm hiểu thêm về dịch vụ SMM Panel của chúng tôi qua những câu hỏi phổ biến nhất từ khách hàng.')?>
                </p>
            </div>

            <!-- FAQ Items -->
            <div class="max-w-3xl mx-auto space-y-1" data-aos="fade-up" data-aos-delay="200">
                <!-- FAQ Item 1 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq1">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('SMM Panel là gì và hoạt động như thế nào?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                                         <div class="faq-content hidden px-8 pb-6" id="faq1">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('SMM Panel là nền tảng cung cấp dịch vụ marketing mạng xã hội tự động. Bạn chỉ cần đặt hàng với link bài viết/profile, hệ thống sẽ tự động tăng followers, likes, views, comments... cho tài khoản của bạn một cách nhanh chóng và an toàn.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq2">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('Thời gian giao hàng bao lâu?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq2">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('Thời gian giao hàng phụ thuộc vào từng dịch vụ: Likes/Followers thường trong vòng 5-30 phút, Views trong vòng 1-6 giờ, Comments trong vòng 30 phút - 2 giờ. Tất cả đều được giao từ từ và tự nhiên để đảm bảo an toàn tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq3">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('Dịch vụ có an toàn cho tài khoản không?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq3">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('Hoàn toàn an toàn! Chúng tôi sử dụng công nghệ tiên tiến để mô phỏng tương tác tự nhiên. Không yêu cầu mật khẩu, chỉ cần link công khai. Đã phục vụ hơn 15,000+ khách hàng mà không có trường hợp nào bị khóa tài khoản.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq4">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('Có chính sách bảo hành và hoàn tiền không?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq4">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('Có! Chúng tôi bảo hành 30-90 ngày tùy dịch vụ. Nếu số lượng giảm, chúng tôi sẽ refill miễn phí. Hoàn tiền 100% nếu không giao được hàng sau 24h. Chính sách rõ ràng, minh bạch, uy tín hàng đầu Việt Nam.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq5">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('Các phương thức thanh toán được hỗ trợ?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq5">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('Hỗ trợ đa dạng: Chuyển khoản ngân hàng, ví điện tử (Momo, ZaloPay, ViettelPay), thẻ cào điện thoại, Bitcoin và các loại coin khác. Nạp tiền tự động 24/7, xử lý trong vòng 1-5 phút.')?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="minimal-card rounded-none">
                    <button class="w-full px-8 py-6 text-left focus:outline-none faq-trigger flex justify-between items-center hover:bg-minimal-gray transition-colors duration-300 minimal-focus" data-target="faq6">
                        <h3 class="text-base font-medium text-minimal-dark pr-4"><?=__('Làm sao để bắt đầu sử dụng dịch vụ?')?></h3>
                        <i class="fas fa-plus text-minimal-medium transition-transform duration-300 text-sm faq-icon"></i>
                    </button>
                    <div class="faq-content hidden px-8 pb-6" id="faq6">
                        <div class="pt-4 border-t border-minimal-border">
                            <p class="text-minimal-medium leading-relaxed text-sm">
                                <?=__('Rất đơn giản! 1) Đăng ký tài khoản miễn phí 2) Nạp tiền vào tài khoản 3) Chọn dịch vụ phù hợp 4) Nhập link bài viết/profile 5) Đặt hàng và chờ kết quả. Hỗ trợ 24/7 qua Telegram/Zalo nếu cần.')?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dark Minimalist Footer -->
    <footer class="py-16 bg-minimal-gray border-t border-minimal-border">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Company Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="<?=$CMSNT->site('title');?>" class="h-8">
                    </div>
                    <p class="text-minimal-medium text-sm leading-relaxed">
                        <?=$CMSNT->site('description');?>
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-base minimal-subtitle mb-6"><?=__('Liên Kết Nhanh')?></h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="#home" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=__('Trang Chủ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#services" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=__('Dịch Vụ')?>
                            </a>
                        </li>
                        <li>
                            <a href="#faq" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=__('FAQ')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/services');?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=__('Bảng Giá')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('client/contact');?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=__('Liên Hệ')?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-base minimal-subtitle mb-6"><?=__('Dịch Vụ')?></h4>
                    <ul class="space-y-3">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC LIMIT 5") as $category):?>
                        <li>
                            <a href="<?=base_url('service/'.$category['slug']);?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                                <?=$category['name'];?>
                            </a>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-base minimal-subtitle mb-6"><?=__('Liên Hệ')?></h4>
                    <ul class="space-y-3">
                        <li class="text-minimal-medium text-sm">
                            <?=$CMSNT->site('address')?>
                        </li>
                        <li class="text-minimal-medium text-sm">
                            <?=$CMSNT->site('hotline')?>
                        </li>
                        <li class="text-minimal-medium text-sm">
                            <?=$CMSNT->site('email')?>
                        </li>
                        <li class="text-minimal-medium text-sm">
                            <?=__('Hỗ trợ 24/7')?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-minimal-border pt-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-minimal-medium text-sm">
                        © <?=date('Y')?> <?=$CMSNT->site('title')?>. <?=__('Tất cả quyền được bảo lưu.')?>
                    </p>
                    <div class="flex space-x-8">
                        <a href="<?=base_url('client/contact');?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                            <?=__('Liên Hệ')?>
                        </a>
                        <a href="<?=base_url('client/policy');?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                            <?=__('Chính Sách')?>
                        </a>
                        <a href="<?=base_url('client/privacy');?>" class="text-minimal-medium hover:text-minimal-dark transition-colors duration-300 text-sm">
                            <?=__('Bảo Mật')?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS with minimal settings
        AOS.init({
            duration: 600,
            easing: 'ease-out',
            once: true,
            offset: 50,
            disable: 'mobile' // Disable on mobile for better performance
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

            // Scroll Progress and Effects
            function updateScrollEffects() {
                const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                $('.progress-bar').css('width', Math.min(scrolled, 100) + '%');
                
                // Back to top button
                if ($(window).scrollTop() > 300) {
                    $('#backToTop').removeClass('opacity-0').addClass('opacity-100');
                } else {
                    $('#backToTop').removeClass('opacity-100').addClass('opacity-0');
                }
                
                // Navbar background on scroll
                if ($(window).scrollTop() > 50) {
                    $('#navbar').addClass('bg-minimal-bg shadow-minimal border-b border-minimal-border');
                } else {
                    $('#navbar').removeClass('bg-minimal-bg shadow-minimal border-b border-minimal-border');
                }
            }

            // Throttled scroll handler for better performance
            let scrollTimeout;
            $(window).on('scroll', function() {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(function() {
                        updateScrollEffects();
                        scrollTimeout = null;
                    }, 16); // ~60fps
                }
            });

            // Back to Top functionality
            $('#backToTop').on('click', function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 600, 'easeOutCubic');
            });

            // FAQ Accordion with minimal animations
            $('.faq-trigger').on('click', function() {
                const targetId = $(this).data('target');
                const content = $('#' + targetId);
                const icon = $(this).find('.faq-icon');
                
                // Close all other FAQ items
                $('.faq-content').not(content).addClass('hidden');
                $('.faq-icon').not(icon).removeClass('fa-minus').addClass('fa-plus');
                
                // Toggle current item
                if (content.hasClass('hidden')) {
                    content.removeClass('hidden');
                    icon.removeClass('fa-plus').addClass('fa-minus');
                } else {
                    content.addClass('hidden');
                    icon.removeClass('fa-minus').addClass('fa-plus');
                }
            });

            // Smooth Scrolling for navigation
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                
                if (target.length) {
                    const headerHeight = $('#navbar').outerHeight();
                    $('html, body').animate({
                        scrollTop: target.offset().top - headerHeight - 20
                    }, 600, 'easeOutCubic');
                }
            });

            // Keyboard navigation support
            $(document).on('keydown', function(e) {
                // ESC key closes mobile menu
                if (e.key === 'Escape') {
                    $('#mobileMenu').addClass('hidden');
                    $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
                }
            });

            // Initialize scroll effects
            updateScrollEffects();

            // Performance optimization: Preload critical images
            const criticalImages = [
                '<?=base_url('assets/img/homepage-item1.webp')?>'
            ];
            
            criticalImages.forEach(function(src) {
                const img = new Image();
                img.src = src;
            });

            // Lazy loading for non-critical elements
            if ('IntersectionObserver' in window) {
                const lazyElements = document.querySelectorAll('[data-lazy]');
                const lazyObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const element = entry.target;
                            element.classList.add('loaded');
                            lazyObserver.unobserve(element);
                        }
                    });
                });

                lazyElements.forEach(function(element) {
                    lazyObserver.observe(element);
                });
            }

            // Focus management for accessibility
            $('.minimal-focus').on('focus', function() {
                $(this).addClass('ring-2 ring-minimal-dark ring-opacity-50');
            }).on('blur', function() {
                $(this).removeClass('ring-2 ring-minimal-dark ring-opacity-50');
            });
        });

        // Add easing function for smooth scrolling
        jQuery.easing.easeOutCubic = function(x, t, b, c, d) {
            return c * ((t = t / d - 1) * t * t + 1) + b;
        };
    </script>
    <?=$CMSNT->site('javascript_footer');?>
    <?=$CMSNT->site('javascript_footer');?>
    <?php if($CMSNT->site('language_type') == 'gtranslate'):?> 
    <?=$CMSNT->site('gtranslate_script');?> 
    <?php endif?>
</body>
</html> 