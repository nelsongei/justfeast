<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast — Event Seat Delivery</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/jm.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/jm.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#A31D1D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Safaricom M-Pesa Express Payment Integration -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet.js and QRious libraries for GPS map pinning and secure QR codes -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9250RBEY90"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-9250RBEY90');
    </script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {sans: ['Outfit', 'sans-serif']},
                    colors: {
                        brand: {rose: '#FFC244', orange: '#FFC244', amber: '#FFC244', emerald: '#A31D1D', red: '#A31D1D'},
                        jf: {
                            red: '#A31D1D',
                            redDark: '#841313',
                            green: '#A31D1D',
                            greenDark: '#841313',
                            yellow: '#FFC244',
                            ink: '#111827',
                            cloud: '#F6F7F2',
                            cream: '#FFF8E7',
                            orange: '#FF7A1A'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 50px rgba(17, 24, 39, 0.10)',
                        card: '0 10px 30px rgba(17, 24, 39, 0.08)',
                        glow: '0 20px 50px rgba(163, 29, 29, 0.22)'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --jf-red: #A31D1D;
            --jf-red-dark: #841313;
            --jf-green: #A31D1D;
            --jf-green-dark: #841313;
            --jf-yellow: #FFC244;
            --jf-ink: #0F172A;
            --jf-muted: #64748B;
            --jf-border: #E2E8F0;
            --jf-cream: #FFF8E7;
            --jf-cloud: #F8FAFC;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #F8FAF4;
            color: #111827;
            min-height: 100vh;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        button, a, input, select {
            transition: all .24s cubic-bezier(.4, 0, .2, 1);
        }

        button:active {
            transform: scale(.97);
        }

        .app-shell {
            max-width: 1480px;
            margin: 0 auto;
            padding: 12px;
        }

        @media (min-width: 640px) {
            .app-shell {
                padding: 20px;
            }
        }

        .jf-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.05);
        }

        .hero-panel {
            background: #A31D1D;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px -10px rgba(163, 29, 29, 0.35);
        }

        .floating-food {
            animation: floaty 4.8s ease-in-out infinite;
            backdrop-filter: blur(10px);
        }

        .floating-food:nth-child(2) {
            animation-delay: .8s;
        }

        .floating-food:nth-child(3) {
            animation-delay: 1.6s;
        }

        .floating-food:nth-child(4) {
            animation-delay: 2.4s;
        }

        @keyframes floaty {
            0%, 100% {
                transform: translateY(0) rotate(-1.5deg);
            }
            50% {
                transform: translateY(-14px) rotate(2.5deg);
            }
        }

        .stadium-bowl {
            background:
                radial-gradient(ellipse at center, rgba(255, 194, 68, 0.95) 0 20%, transparent 21%),
                repeating-radial-gradient(ellipse at center, rgba(255, 255, 255, 0.22) 0 10px, rgba(255, 255, 255, 0.06) 11px 20px, transparent 21px 30px);
        }

        .search-dock {
            box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.18);
        }

        .category-active {
            background: #A31D1D !important;
            color: #FFFFFF !important;
            border-color: #A31D1D !important;
            box-shadow: 0 8px 24px -4px rgba(163, 29, 29, 0.35);
            transform: translateY(-2px);
        }

        .category-pill {
            background: #FFFFFF;
            border: 1px solid var(--jf-border);
            color: var(--jf-ink);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-pill:hover {
            border-color: #A31D1D;
            color: #A31D1D;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px -4px rgba(163, 29, 29, 0.15);
        }

        .product-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12), 0 0 0 2px rgba(5, 163, 87, 0.2);
        }

        .seat-map-mini {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            background-color: #FDFEFA;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid var(--jf-border) !important;
            box-shadow: 0 14px 35px -8px rgba(15, 23, 42, 0.09) !important;
            color: #0F172A !important;
            backdrop-filter: blur(14px);
        }

        .radar-sweep {
            position: absolute;
            inset: 0;
            background: conic-gradient(from 0deg at 50% 50%, rgba(5, 163, 87, 0.28), transparent 130deg);
            animation: spin 2.2s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.85);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .pulse-ring {
            animation: pulse-ring 2.4s infinite ease-out;
        }

        @keyframes buzz {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            20%, 60% {
                transform: translate(-3px, 2px) rotate(-1.5deg);
            }
            40%, 80% {
                transform: translate(3px, -2px) rotate(1.5deg);
            }
        }

        .phone-buzz {
            animation: buzz .5s ease-in-out 3;
        }

        .stadium-grid {
            background-image: radial-gradient(rgba(5, 163, 87, .15) 1px, transparent 1px);
            background-size: 16px 16px;
        }

        .vendor-showcase-card {
            transform: translateZ(0);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .vendor-showcase-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.12);
        }

        .vendor-showcase-card button {
            border-radius: 999px !important;
        }

        .btn-glow {
            box-shadow: 0 10px 25px -5px rgba(5, 163, 87, 0.4);
        }

        .btn-glow:hover {
            box-shadow: 0 14px 30px -4px rgba(5, 163, 87, 0.55);
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .vendor-showcase-card h4 {
                font-size: 18px;
            }

            .vendor-showcase-card .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        const API_BASE = "{{ url('/api') }}";
    </script>
</head>
<body class="min-h-screen overflow-x-hidden">

<!-- PWA Install Banner -->
<div id="pwa-install-banner"
     class="hidden bg-[#111827] px-4 py-2 text-center text-sm font-semibold items-center justify-between shadow-lg relative z-50 text-white">
    <span>Install justFeast for faster event-seat ordering.</span>
    <div class="flex items-center gap-2">
        <button onclick="installPWA()"
                class="bg-[#FFC244] text-[#111827] px-3 py-1 rounded-full text-xs font-black hover:bg-[#ffd56f]">Install
            App
        </button>
        <button onclick="dismissPWABanner()" class="text-white/80 hover:text-white"><i class="fas fa-times"></i>
        </button>
    </div>
</div>

<div class="app-shell relative z-10">
    <!-- App Header -->
    <header class="jf-card rounded-[24px] sm:rounded-[32px] px-3 sm:px-5 md:px-7 py-3 md:py-4 mb-6 flex items-center justify-between sticky top-3 z-40 border border-white/40 shadow-2xl backdrop-blur-xl bg-white/80">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-9 sm:h-12 w-auto rounded-2xl shadow-sm border border-black/10 shrink-0">
            <div class="min-w-0 flex flex-col justify-center gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="bg-[#A31D1D]/10 text-[#A31D1D] text-[8px] sm:text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full border border-[#A31D1D]/20 whitespace-nowrap">Live Event</span>
                </div>
                <p class="text-[10px] sm:text-xs text-slate-600 font-extrabold flex items-center gap-1.5 truncate max-w-[150px] xs:max-w-[200px] sm:max-w-xs md:max-w-none" id="live-event-banner">
                    <span class="w-2 h-2 rounded-full bg-[#05A357] animate-pulse shrink-0"></span>
                    <span class="truncate">Loading active event...</span>
                </p>
            </div>
        </div>

        <nav class="hidden lg:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-full border border-slate-200/80 backdrop-blur">
            <a href="#vendors"
               class="px-5 py-2 rounded-full text-xs font-black text-slate-700 hover:text-[#A31D1D] hover:bg-white transition-all shadow-none hover:shadow-sm">Vendors</a>
            <button onclick="openSeatModal()"
                    class="px-5 py-2 rounded-full text-xs font-black text-slate-700 hover:text-[#A31D1D] hover:bg-white transition-all shadow-none hover:shadow-sm">
                <i class="fas fa-map-location-dot mr-1 text-[#A31D1D]"></i> Delivery map
            </button>
            <a href="#how-it-works" class="px-5 py-2 rounded-full text-xs font-black text-slate-700 hover:text-[#A31D1D] hover:bg-white transition-all shadow-none hover:shadow-sm">How it works</a>
        </nav>

        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
            <div id="header-user-badge"
                 class="max-sm:hidden sm:flex items-center gap-1.5 sm:gap-2.5 bg-slate-100 border border-slate-200 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-full shadow-inner whitespace-nowrap">
                <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-[#A31D1D] animate-pulse shrink-0"></span>
                <span class="text-[11px] sm:text-xs font-black text-[#0F172A] truncate max-w-[80px] xs:max-w-[110px] sm:max-w-[150px]" id="header-user-name">Guest</span>
            </div>
            <div id="header-auth-buttons" class="shrink-0"></div>
        </div>
    </header>

    <!-- Customer Main Wrapper -->
    <main class="flex-1">
        <div id="cust-main" class="hidden space-y-8 pb-12">
            <!-- Hero Fold -->
            <section id="glovo-hero-fold"
                     class="hero-panel rounded-[28px] sm:rounded-[40px] md:rounded-[52px] p-4 sm:p-6 md:p-10 lg:p-12 text-white border border-white/10 relative">
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center">
                    <div class="lg:col-span-7 space-y-5 sm:space-y-7">
                        <div
                            class="inline-flex items-center gap-2 sm:gap-2.5 bg-white/10 border border-white/20 backdrop-blur-md px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[9.5px] sm:text-[11px] font-black uppercase tracking-[0.1em] sm:tracking-[0.16em] text-white shadow-xl max-w-full">
                            <span class="relative flex h-2 w-2 sm:h-2.5 sm:w-2.5 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC244] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 sm:h-2.5 sm:w-2.5 bg-[#FFC244]"></span>
                            </span>
                            <span class="sm:hidden truncate">Live Event Seat Delivery</span>
                            <span class="hidden sm:inline truncate">Live Event Food Delivery — Delivered to your spot</span>
                        </div>

                        <div class="space-y-3 sm:space-y-4">
                            <h2 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-black tracking-tight leading-[0.98] sm:leading-[0.95] text-white">
                                Never miss a beat.<br class="hidden md:block">
                                Food <span class="text-[#FFC244]">finds your seat.</span>
                            </h2>
                            <p class="text-slate-200/90 text-xs sm:text-sm md:text-base leading-relaxed max-w-xl font-medium">
                                Pin your precise location on the map, choose your favorite concert meals, pay instantly with M-Pesa, and track your runner in real-time.
                            </p>
                        </div>

                        <div class="search-dock bg-white rounded-[24px] sm:rounded-[32px] md:rounded-full p-2 md:p-3 max-w-3xl grid grid-cols-1 md:grid-cols-[1fr_auto] gap-2.5 sm:gap-3 text-[#0F172A] border border-white/40 shadow-2xl">
                            <button onclick="openSeatModal()"
                                    class="flex items-center gap-3 sm:gap-4 px-3.5 sm:px-5 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl md:rounded-full hover:bg-slate-50 text-left group transition-all min-w-0">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#A31D1D]/10 text-[#A31D1D] flex items-center justify-center group-hover:bg-[#A31D1D] group-hover:text-white transition-all shadow-sm shrink-0">
                                    <i class="fas fa-location-dot text-base sm:text-lg"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[8.5px] sm:text-[9.5px] uppercase tracking-wider font-black text-slate-400 truncate">Delivery Spot Coordinates</span>
                                    <span class="block text-xs sm:text-sm font-black truncate text-[#0F172A]" id="selected-seat-hero">Set delivery location</span>
                                </span>
                            </button>
                            <a href="#vendors"
                               class="btn-glow inline-flex justify-center items-center gap-2 sm:gap-2.5 bg-[#FFC244] hover:bg-[#E0A325] text-[#0F172A] px-5 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl md:rounded-full text-xs sm:text-sm font-black transition-all shadow-lg">
                                Explore Vendors <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-2 sm:gap-3.5 max-w-xl">
                            <div class="bg-white/10 border border-white/15 rounded-2xl sm:rounded-3xl p-2 sm:p-4 backdrop-blur-md hover:bg-white/15 transition min-w-0 text-center sm:text-left">
                                <p class="text-xs xs:text-sm sm:text-2xl font-black text-[#FFC244] truncate">8-15 min</p>
                                <p class="text-[8px] xs:text-[9px] sm:text-[10px] uppercase tracking-wider text-white/70 font-black mt-0.5 truncate">Average Drop</p>
                            </div>
                            <div class="bg-white/10 border border-white/15 rounded-2xl sm:rounded-3xl p-2 sm:p-4 backdrop-blur-md hover:bg-white/15 transition min-w-0 text-center sm:text-left">
                                <p class="text-xs xs:text-sm sm:text-2xl font-black text-[#FFC244] truncate">M-Pesa</p>
                                <p class="text-[8px] xs:text-[9px] sm:text-[10px] uppercase tracking-wider text-white/70 font-black mt-0.5 truncate">
                                    <span class="sm:hidden">Instant Pay</span>
                                    <span class="hidden sm:inline">Instant Checkout</span>
                                </p>
                            </div>
                            <div class="bg-white/10 border border-white/15 rounded-2xl sm:rounded-3xl p-2 sm:p-4 backdrop-blur-md hover:bg-white/15 transition min-w-0 text-center sm:text-left">
                                <p class="text-xs xs:text-sm sm:text-2xl font-black text-[#FFC244] truncate">Safe PIN</p>
                                <p class="text-[8px] xs:text-[9px] sm:text-[10px] uppercase tracking-wider text-white/70 font-black mt-0.5 truncate">
                                    <span class="sm:hidden">Secure Drop</span>
                                    <span class="hidden sm:inline">Secure Handover</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 mt-4 lg:mt-0">
                        <div class="relative min-h-[300px] sm:min-h-[440px] flex items-center justify-center">
                            <!-- Stadium concentric visual radar -->
                            <div class="absolute inset-0 stadium-bowl rounded-full border border-white/20 opacity-80 shadow-2xl"></div>

                            <!-- Center stage node -->
                            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 sm:w-52 sm:h-52 rounded-full bg-[#FFC244] shadow-2xl flex items-center justify-center text-[#0F172A] text-center p-4 sm:p-6 border-4 border-white/30 z-10">
                                <div>
                                    <div class="w-9 h-9 sm:w-12 sm:h-12 mx-auto bg-[#0F172A] text-[#FFC244] rounded-full flex items-center justify-center mb-1.5 sm:mb-2 shadow-lg">
                                        <i class="fas fa-music text-sm sm:text-xl"></i>
                                    </div>
                                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-[#0F172A]">Main Stage</p>
                                    <p class="text-[9px] sm:text-[10px] font-extrabold text-[#0F172A]/70">Uhuru Park Arena</p>
                                </div>
                            </div>

                            <!-- Floating interactive items (4 Dynamic Vendor Adverts) -->
                            <div onclick="selectHeroVendorAd(0)" class="floating-food absolute left-1 sm:left-2 top-2 sm:top-6 bg-white/95 text-[#0F172A] rounded-2xl sm:rounded-[26px] p-2.5 sm:p-3.5 shadow-2xl flex items-center gap-2 sm:gap-3 border border-white/60 z-20 cursor-pointer hover:scale-105 transition-all" id="hero-ad-card-0">
                                <span class="text-xl sm:text-3xl" id="hero-ad-logo-0">🥟</span>
                                <div>
                                    <p class="text-[11px] sm:text-xs font-black truncate max-w-[110px] sm:max-w-[130px]" id="hero-ad-name-0">Samosa World</p>
                                    <p class="text-[9px] sm:text-[10px] text-[#05A357] font-extrabold" id="hero-ad-sub-0">from Ksh 40</p>
                                </div>
                            </div>

                            <div onclick="selectHeroVendorAd(1)" class="floating-food absolute right-1 sm:right-2 top-2 sm:top-6 bg-white/95 text-[#0F172A] rounded-2xl sm:rounded-[26px] p-2.5 sm:p-3.5 shadow-2xl flex items-center gap-2 sm:gap-3 border border-white/60 z-20 cursor-pointer hover:scale-105 transition-all" id="hero-ad-card-1">
                                <span class="text-xl sm:text-3xl" id="hero-ad-logo-1">☕</span>
                                <div>
                                    <p class="text-[11px] sm:text-xs font-black truncate max-w-[110px] sm:max-w-[130px]" id="hero-ad-name-1">Tai Coffee</p>
                                    <p class="text-[9px] sm:text-[10px] text-[#05A357] font-extrabold" id="hero-ad-sub-1">from Ksh 150</p>
                                </div>
                            </div>

                            <div onclick="selectHeroVendorAd(2)" class="floating-food absolute bottom-2 sm:bottom-6 left-1 sm:left-2 bg-white/95 text-[#0F172A] rounded-2xl sm:rounded-[26px] p-2.5 sm:p-3.5 shadow-2xl flex items-center gap-2 sm:gap-3 border border-white/60 z-20 cursor-pointer hover:scale-105 transition-all" id="hero-ad-card-2">
                                <span class="text-xl sm:text-3xl" id="hero-ad-logo-2">🐟</span>
                                <div>
                                    <p class="text-[11px] sm:text-xs font-black truncate max-w-[110px] sm:max-w-[130px]" id="hero-ad-name-2">Global Tilapia</p>
                                    <p class="text-[9px] sm:text-[10px] text-[#05A357] font-extrabold" id="hero-ad-sub-2">from Ksh 200</p>
                                </div>
                            </div>

                            <div onclick="selectHeroVendorAd(3)" class="floating-food absolute bottom-2 sm:bottom-6 right-1 sm:right-2 bg-white/95 text-[#0F172A] rounded-2xl sm:rounded-[26px] p-2.5 sm:p-3.5 shadow-2xl flex items-center gap-2 sm:gap-3 border border-white/60 z-20 cursor-pointer hover:scale-105 transition-all" id="hero-ad-card-3">
                                <span class="text-xl sm:text-3xl" id="hero-ad-logo-3">🍖</span>
                                <div>
                                    <p class="text-[11px] sm:text-xs font-black truncate max-w-[110px] sm:max-w-[130px]" id="hero-ad-name-3">Carnivore Smokehouse</p>
                                    <p class="text-[9px] sm:text-[10px] text-[#05A357] font-extrabold" id="hero-ad-sub-3">from Ksh 350</p>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </section>

            <!-- How it works & Delivery Location Dock -->
            <section id="how-it-works" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 jf-card rounded-[36px] p-5 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#05A357]">Simple 3-Step Process</span>
                        <span class="text-xs text-slate-400 font-bold">Fast seat delivery</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-[#FFF8E7] rounded-[28px] p-5 border border-[#F7E5B2] hover:shadow-md transition">
                            <div class="w-11 h-11 rounded-2xl bg-[#FFC244] text-[#0F172A] flex items-center justify-center mb-3 shadow-md shadow-[#FFC244]/30">
                                <i class="fas fa-map-location-dot text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black text-[#0F172A]">1. Pin Location</h3>
                            <p class="text-[11.5px] text-slate-600 leading-relaxed mt-1 font-medium">Drop your GPS pin on our event map so runners know where to go.</p>
                        </div>

                        <div class="bg-white rounded-[28px] p-5 border border-slate-200 hover:shadow-md transition">
                            <div class="w-11 h-11 rounded-2xl bg-[#05A357]/15 text-[#05A357] flex items-center justify-center mb-3 shadow-sm">
                                <i class="fas fa-store text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black text-[#0F172A]">2. Pick Vendors</h3>
                            <p class="text-[11.5px] text-slate-600 leading-relaxed mt-1 font-medium">Browse verified event food stalls and select your meals.</p>
                        </div>

                        <div class="bg-[#0F172A] rounded-[28px] p-5 border border-[#0F172A] text-white hover:shadow-xl transition">
                            <div class="w-11 h-11 rounded-2xl bg-white/15 text-[#FFC244] flex items-center justify-center mb-3">
                                <i class="fas fa-person-running text-lg"></i>
                            </div>
                            <h3 class="text-sm font-black text-white">3. Track Delivery</h3>
                            <p class="text-[11.5px] text-white/70 leading-relaxed mt-1 font-medium">Track your runner in real-time and hand over your secret PIN.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 jf-card rounded-[36px] p-5 md:p-6 seat-map-mini flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <p class="text-[10px] uppercase tracking-widest font-black text-slate-400">Current Target</p>
                                <h3 class="text-base font-black mt-1 text-[#0F172A]" id="selected-seat-label">Configure Location</h3>
                                <p class="text-[11.5px] text-slate-500 font-medium mt-1" id="selected-seat-sub">Tap button below to set GPS delivery pin</p>
                            </div>
                            <span id="seat-status-pill"
                                  class="text-[9.5px] bg-slate-100 text-slate-500 px-3 py-1 rounded-full font-black border border-slate-200">Not Set</span>
                        </div>
                    </div>
                    <button onclick="openSeatModal()"
                            class="mt-5 w-full bg-[#0F172A] hover:bg-[#05A357] text-white rounded-full py-3.5 text-xs font-black transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="fas fa-map-location-dot text-[#FFC244]"></i> Pin Delivery Location
                    </button>
                </div>
            </section>

            <!-- Marketplace Section -->
            <section id="vendors" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-8 xl:col-span-9 space-y-6 min-w-0">
                    <!-- Dynamic Vendors List (Renders Top restaurants & stalls + filter chips) -->
                    <div id="vendor-list-container" class="space-y-8"></div>

                    <!-- Live Event Promo Banner -->
                    <div class="relative overflow-hidden rounded-[36px] bg-[#A31D1D] p-6 md:p-7 flex flex-col md:flex-row md:items-center justify-between gap-5 text-white shadow-xl shadow-[#A31D1D]/20 border border-white/10">
                        <div class="absolute -right-8 -top-10 text-[180px] opacity-10 pointer-events-none">🎤</div>
                        <div class="relative z-10 space-y-2">
                            <span class="inline-flex items-center gap-1.5 bg-[#FFC244] text-[#0F172A] text-[9.5px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                                <i class="fas fa-bolt text-[10px]"></i> Live Event Special
                            </span>
                            <h3 class="font-black text-2xl md:text-3xl tracking-tight text-white">Don’t miss the headliner set!</h3>
                            <p class="text-white/80 text-xs md:text-sm max-w-xl font-medium">Order before the next artist performance for express runner dispatch.</p>
                        </div>
                        <button onclick="openSeatModal()"
                                class="relative z-10 bg-white hover:bg-[#FFC244] text-[#0F172A] px-6 py-3.5 rounded-full text-xs font-black transition-all shadow-lg self-start md:self-auto">
                            Set Delivery Location
                        </button>
                    </div>

                    <!-- Event Marketplace Card (Positioned below Top restaurants and stalls) -->
                    <div class="jf-card rounded-[28px] sm:rounded-[36px] p-4 sm:p-6 space-y-4 sm:space-y-5">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 sm:gap-4">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-[#A31D1D] font-black">Event Marketplace</p>
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight text-[#0F172A]">Order from stalls serving your zone</h2>
                            </div>
                            <div class="flex items-center gap-2 text-[10.5px] sm:text-[11px] font-black text-slate-600 bg-slate-100/90 border border-slate-200/90 rounded-full px-3 sm:px-3.5 py-1.5 sm:py-2 self-start md:self-auto shadow-sm whitespace-nowrap">
                                <i class="fas fa-shield-halved text-[#A31D1D]"></i> Verified Festival Vendors
                            </div>
                        </div>

                        <!-- Search dock -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 sm:pl-4.5 flex items-center pointer-events-none text-[#A31D1D]">
                                <i class="fas fa-search text-sm sm:text-base"></i>
                            </span>
                            <input type="text" id="menu-search" oninput="searchMenu()"
                                   placeholder="Search meals, drinks, snacks..."
                                   class="w-full pl-10 sm:pl-12 pr-4 sm:pr-5 py-3 sm:py-4 rounded-xl sm:rounded-2xl md:rounded-full bg-slate-100/70 border border-slate-200 focus:border-[#A31D1D] focus:ring-4 focus:ring-[#A31D1D]/15 text-[#0F172A] text-xs sm:text-sm font-extrabold shadow-inner focus:outline-none placeholder-slate-400">
                        </div>

                        <!-- Category Selector Pills -->
                        <div class="flex items-center gap-2 sm:gap-3 py-1 overflow-x-auto no-scrollbar scroll-smooth">
                            <button onclick="setCategory('all')" id="cat-all"
                                    class="category-pill category-active flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-sm sm:text-base">🏟️</span>
                                <span class="text-[11px] sm:text-xs font-black uppercase tracking-wide">All Vendors</span>
                            </button>
                            <button onclick="setCategory('food')" id="cat-food"
                                    class="category-pill flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-sm sm:text-base">🍔</span>
                                <span class="text-[11px] sm:text-xs font-black uppercase tracking-wide">Meals</span>
                            </button>
                            <button onclick="setCategory('drinks')" id="cat-drinks"
                                    class="category-pill flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-sm sm:text-base">🥤</span>
                                <span class="text-[11px] sm:text-xs font-black uppercase tracking-wide">Drinks</span>
                            </button>
                            <button onclick="setCategory('snacks')" id="cat-snacks"
                                    class="category-pill flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-sm sm:text-base">🍿</span>
                                <span class="text-[11px] sm:text-xs font-black uppercase tracking-wide">Snacks</span>
                            </button>
                        </div>

                        <!-- Featured Food & Drinks Container (Positioned inside Event Marketplace card) -->
                        <div id="featured-food-container" class="mt-3 pt-3 border-t border-slate-100/90"></div>
                    </div>
                </div>

                <!-- Right Sticky Cart Sidebar -->
                <aside class="lg:col-span-4 xl:col-span-3 space-y-5 lg:sticky lg:top-28">
                    <!-- User Profile Card -->
                    <div class="jf-card rounded-[32px] p-4 flex items-center justify-between gap-2 border border-slate-200/90 shadow-sm bg-white hover:shadow-md transition">
                        <div class="flex items-center gap-3 min-w-0 pr-1">
                            <div class="w-11 h-11 rounded-2xl bg-[#A31D1D] p-0.5 shadow-md shadow-[#A31D1D]/15 flex items-center justify-center overflow-hidden shrink-0">
                                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center text-[#0F172A] font-black text-sm">
                                    <i class="fas fa-user-tag text-[#A31D1D]"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-sm font-black text-[#0F172A] truncate" id="cust-user-name">Guest</h4>
                                    <span class="bg-slate-100 text-slate-500 text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full border border-slate-200 shrink-0" id="cust-user-pass-badge">Guest</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-bold truncate mt-0.5" id="cust-user-status-text">
                                    Tap Register / Log In
                                </p>
                            </div>
                        </div>
                        <button onclick="openAuthModal()" class="px-4 py-2 bg-[#FFC244] hover:bg-[#E0A325] active:scale-95 text-[#0F172A] rounded-full text-xs font-black transition-all border border-[#E0A325] shadow-xs cursor-pointer whitespace-nowrap shrink-0">
                            Register / Log In
                        </button>
                    </div>

                    <!-- Desktop Cart Basket Tray -->
                    <div id="desktop-cart-tray" class="hidden lg:block jf-card rounded-[36px] overflow-hidden border border-slate-200/90 shadow-xl bg-white">
                        <!-- Basket Top Header -->
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/70">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-[#A31D1D]/10 text-[#A31D1D] flex items-center justify-center text-xs">
                                    <i class="fas fa-basket-shopping"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-black text-[#0F172A]">Order Basket</span>
                                    <span id="basket-count-badge" class="ml-1 text-[9.5px] bg-slate-200/80 text-slate-600 font-black px-2 py-0.5 rounded-full">0 items</span>
                                </div>
                            </div>
                            <button onclick="clearBasket()"
                                    class="text-[10.5px] text-slate-400 hover:text-red-500 font-extrabold flex items-center gap-1 transition cursor-pointer">
                                <i class="far fa-trash-can"></i> Clear
                            </button>
                        </div>

                        <!-- Basket Items Scroll Container -->
                        <div id="desktop-cart-tray-items" class="max-h-[290px] overflow-y-auto space-y-2 p-4">
                            <div class="text-center py-8 px-4 space-y-3 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                <div class="w-14 h-14 mx-auto rounded-full bg-[#FFF8E7] text-3xl flex items-center justify-center shadow-inner">
                                    🛒
                                </div>
                                <div>
                                    <p class="text-xs font-black text-[#0F172A]">Your basket is empty</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Select meals or drinks from stalls to order.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Basket Footer / Location & Checkout -->
                        <div class="border-t border-slate-100 p-5 space-y-4 bg-slate-50/80">
                            <!-- Delivery Location Box -->
                            <div class="bg-white p-3 rounded-2xl border border-slate-200/80 flex items-center justify-between gap-2 shadow-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-[#A31D1D]/15 text-[#A31D1D] flex items-center justify-center shrink-0">
                                        <i class="fas fa-location-dot text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[9px] uppercase tracking-wider text-slate-400 font-black">Delivery Location</p>
                                        <p class="text-xs font-black text-[#0F172A] truncate" id="desktop-cart-location-text">Not configured</p>
                                    </div>
                                </div>
                                <button onclick="openSeatModal()" class="text-[10px] text-[#A31D1D] hover:underline font-black shrink-0">
                                    Pin Map
                                </button>
                            </div>

                            <!-- Total breakdown -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex justify-between text-xs text-slate-500 font-medium">
                                    <span>Subtotal</span>
                                    <span id="desktop-cart-subtotal" class="font-bold text-slate-700">Ksh 0</span>
                                </div>
                                <div class="flex justify-between text-xs text-slate-500 font-medium">
                                    <span>Seat Delivery Fee</span>
                                    <span class="font-black text-[#A31D1D]" id="desktop-cart-delivery-fee">Ksh 30</span>
                                </div>
                                <div class="flex justify-between items-end pt-2 border-t border-slate-200/60">
                                    <span class="text-xs text-slate-500 font-black">Total Payable</span>
                                    <span class="text-2xl font-black text-[#A31D1D]" id="desktop-cart-tray-total">Ksh 0</span>
                                </div>
                            </div>

                            <!-- Checkout Action Button -->
                            <button onclick="checkoutOrder()"
                                    class="w-full py-4 bg-[#A31D1D] hover:bg-[#841313] text-white rounded-full text-xs font-black shadow-xl shadow-[#A31D1D]/25 hover:shadow-2xl transition-all flex items-center justify-center gap-2.5 cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                                <span class="inline-flex items-center justify-center bg-white px-2.5 py-0.5 rounded-full h-7 overflow-hidden shadow-xs">
                                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-5 w-auto object-contain scale-110">
                                </span>
                                <span class="text-white/40 text-xs">·</span>
                                <span class="font-black tracking-tight text-white">Pay via M-Pesa STK Push</span>
                                <i class="fas fa-arrow-right text-xs text-[#FFC244]"></i>
                            </button>
                        </div>
                    </div>
                </aside>
            </section>
        </div>

        <!-- Active Order Tracking View -->
        <div id="cust-tracker" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch pb-12">
            <div class="lg:col-span-7 glass-card rounded-[40px] p-7 flex flex-col justify-between space-y-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-[#A31D1D] font-black">Live Order Status</p>
                    <h3 class="text-2xl font-black tracking-tight text-[#0F172A]">Delivery Timeline</h3>
                    <p class="text-xs text-slate-500 font-extrabold mt-1">Real-time status updates from vendor to your seat.</p>
                </div>

                <div class="space-y-4 bg-slate-50 p-6 rounded-[30px] border border-slate-200/90 text-left shadow-inner">
                    <div class="flex items-center gap-3.5" id="step-created">
                        <div class="w-7 h-7 rounded-full bg-[#A31D1D] text-white flex items-center justify-center text-xs font-bold shadow-md shadow-[#A31D1D]/30">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="text-sm font-black text-[#0F172A]">Order Placed & Paid</span>
                    </div>

                    <div class="flex items-center gap-3.5" id="step-preparing">
                        <div class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-400 flex items-center justify-center text-xs font-bold">
                            2
                        </div>
                        <span class="text-sm font-black text-slate-400">Vendor Preparing Food</span>
                    </div>

                    <div class="flex items-center gap-3.5" id="step-ready">
                        <div class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-400 flex items-center justify-center text-xs font-bold">
                            3
                        </div>
                        <span class="text-sm font-black text-slate-400">Ready for Runner Dispatch</span>
                    </div>

                    <div class="flex items-center gap-3.5" id="step-enroute">
                        <div class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-400 flex items-center justify-center text-xs font-bold">
                            4
                        </div>
                        <span class="text-sm font-black text-slate-400">Runner En-Route to Pin</span>
                    </div>
                </div>

                <div class="bg-[#A31D1D]/10 border border-[#A31D1D]/20 p-4 rounded-2xl text-xs text-slate-700 font-extrabold flex items-center gap-2">
                    <i class="fas fa-circle-info text-base text-[#A31D1D]"></i>
                    <span>Remain near your location pin. The runner will request your delivery PIN upon arrival.</span>
                </div>
            </div>

            <!-- Radar display container -->
            <div class="lg:col-span-5 glass-card rounded-[40px] p-7 text-center flex flex-col justify-between space-y-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-[#A31D1D] font-black">Live Runner Radar</p>
                    <h3 class="text-2xl font-black tracking-tight text-[#0F172A]">Location Active</h3>
                    <p class="text-xs text-slate-500 font-bold">Uhuru Park Event Arena</p>
                </div>

                <div class="relative w-52 h-52 mx-auto flex items-center justify-center bg-slate-100 rounded-full border border-slate-200 overflow-hidden shadow-inner">
                    <div class="radar-sweep"></div>
                    <div class="absolute w-52 h-52 rounded-full border border-[#A31D1D]/20 pulse-ring"></div>
                    <div class="absolute w-36 h-36 rounded-full border border-[#FFC244]/30 pulse-ring" style="animation-delay:.55s"></div>
                    <div class="w-16 h-16 bg-[#FFC244] border-2 border-white rounded-full flex items-center justify-center shadow-2xl relative z-10 animate-bounce">
                        <i class="fas fa-person-running text-[#0F172A] text-2xl"></i>
                    </div>
                </div>

                <div class="bg-[#0F172A] text-white p-5 rounded-[28px] space-y-1 shadow-2xl">
                    <span class="text-[9.5px] uppercase tracking-widest text-white/50 font-black">Secure Handover PIN</span>
                    <h4 class="text-4xl font-black text-[#FFC244] tracking-widest" id="tracker-pin">----</h4>
                    <p class="text-[10px] text-white/50 font-bold">Show this PIN only when runner arrives.</p>
                </div>

                <div id="tracker-qr-container"
                     class="hidden bg-white border border-slate-200 p-4 rounded-[28px] text-center space-y-2 flex flex-col items-center justify-center shadow-lg">
                    <span class="text-[9.5px] uppercase tracking-widest text-[#A31D1D] font-black flex items-center gap-1">
                        <i class="fas fa-qrcode"></i> Scan to Verify Handover
                    </span>
                    <canvas id="tracker-qr-canvas" class="w-32 h-32 border border-slate-100 p-1 bg-white"></canvas>
                    <p class="text-[9.5px] text-slate-500 font-medium leading-relaxed">Runner scans QR code or enters PIN to confirm delivery.</p>
                </div>

                <button onclick="resetTrackerDemo()"
                        class="text-xs bg-slate-100 hover:bg-slate-200 text-[#0F172A] px-5 py-3 rounded-full font-black mx-auto transition">
                    Order Something Else
                </button>
            </div>
        </div>
    </main>

    <!-- Mobile Basket Tray Overlay -->
    <div id="phone-cart-tray"
         class="hidden lg:hidden fixed bottom-0 inset-x-0 max-w-md mx-auto bg-white border-t border-slate-200 rounded-t-[32px] p-5 pb-6 z-40 shadow-2xl">
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-black text-[#0F172A] flex items-center gap-2">
                <i class="fas fa-basket-shopping text-[#A31D1D]"></i> Basket
            </span>
            <button onclick="clearBasket()" class="text-[10px] text-slate-400 hover:text-red-500 font-black">Clear</button>
        </div>
        <div id="cart-tray-items" class="max-h-[130px] overflow-y-auto space-y-2 mb-4"></div>
        <div class="border-t border-slate-200 pt-3 space-y-3">
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500 font-black">Location:</span>
                <span class="font-black text-[#0F172A]" id="cart-location-text">Not configured</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500 font-medium">Subtotal:</span>
                <span class="font-bold text-slate-700" id="cart-subtotal">Ksh 0</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500 font-medium">Seat Delivery Fee:</span>
                <span class="font-black text-[#A31D1D]" id="cart-delivery-fee">Ksh 30</span>
            </div>
            <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                <span class="text-xs text-slate-500 font-black">Total Amount:</span>
                <span class="text-xl font-black text-[#A31D1D]" id="cart-tray-total">Ksh 0</span>
            </div>
            <button onclick="checkoutOrder()"
                    class="w-full py-4 bg-[#0F172A] hover:bg-[#A31D1D] text-white rounded-full text-xs font-black flex items-center justify-center gap-2 shadow-xl">
                <span class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 overflow-hidden">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-6 w-auto object-contain scale-110">
                </span>
                <span class="text-white/30 text-xs">·</span>
                <span class="font-black tracking-tight">M-Pesa STK Push</span>
                <i class="fas fa-arrow-right ml-1 text-xs text-[#FFC244]"></i>
            </button>
        </div>
    </div>
</div>

<footer class="mt-10 bg-[#0B1117] text-white relative overflow-hidden">
    <div class="h-1 bg-[#A31D1D]"></div>
    <div class="max-w-[1480px] mx-auto px-5 md:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-8">
        <div class="md:col-span-4 space-y-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-10 w-auto rounded-2xl shadow-sm border border-white/10 shrink-0">
                <h2 class="text-2xl font-black">just<span class="text-[#FFC244]">Feast</span></h2>
            </div>
            <p class="text-sm text-white/55 leading-relaxed max-w-md">Live event food ordering for concerts, stadiums and festivals. Vendors sell more, attendees miss less, runners deliver to the exact location.</p>
        </div>
        <div class="md:col-span-2 space-y-3">
            <h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">For attendees</h4>
            <a href="#vendors" class="block text-sm text-white/65 hover:text-[#FFC244]">Browse vendors</a>
            <button onclick="openSeatModal()" class="block text-sm text-white/65 hover:text-[#FFC244]">Delivery map</button>
            <a href="#how-it-works" class="block text-sm text-white/65 hover:text-[#FFC244]">How it works</a>
        </div>
        <div class="md:col-span-3 space-y-3">
            <h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">Legal & Policies</h4>
            <button onclick="openPolicyModal('privacy')" class="block text-sm text-white/65 hover:text-[#FFC244] text-left cursor-pointer transition">Privacy Policy</button>
            <button onclick="openPolicyModal('terms')" class="block text-sm text-white/65 hover:text-[#FFC244] text-left cursor-pointer transition">Terms & Conditions</button>
            <button onclick="openPolicyModal('returns')" class="block text-sm text-white/65 hover:text-[#FFC244] text-left cursor-pointer transition">Return Policy</button>
        </div>
        <div class="md:col-span-3 space-y-3">
            <h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">Payments</h4>
            <div class="bg-white/5 border border-white/10 rounded-3xl p-4 space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-10 px-3.5 bg-white rounded-2xl flex items-center justify-center border border-white/20 shadow-xs shrink-0 overflow-hidden">
                        <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-7 max-h-7 w-auto object-contain scale-125" onerror="this.parentElement.style.display='none'">
                    </div>
                    <div class="h-10 px-3 bg-white rounded-2xl flex items-center justify-center border border-white/20 shadow-xs shrink-0 overflow-hidden">
                        <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja" class="h-7 max-h-7 w-auto object-contain scale-125" onerror="this.parentElement.style.display='none'">
                    </div>
                </div>
                <p class="text-xs text-white/50 leading-relaxed">Secure STK Push checkout and delivery PIN verification for safer handovers.</p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-[1480px] mx-auto px-5 md:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-[11px] text-white/35 font-bold">© 2026 justFeast. All rights reserved.</p>
            <div class="flex flex-wrap items-center justify-center gap-4 text-[11px] text-white/50 font-bold">
                <button onclick="openPolicyModal('privacy')" class="hover:text-white transition cursor-pointer">Privacy Policy</button>
                <span>•</span>
                <button onclick="openPolicyModal('terms')" class="hover:text-white transition cursor-pointer">Terms & Conditions</button>
                <span>•</span>
                <button onclick="openPolicyModal('returns')" class="hover:text-white transition cursor-pointer">Return Policy</button>
            </div>
            <div class="flex items-center gap-2.5 text-[11px] text-white/60 font-black uppercase tracking-wider bg-white/5 border border-white/10 px-3.5 py-1.5 rounded-full">
                <span>Powered by</span>
                <img src="{{ asset('images/logo/basemathai.jpeg') }}" alt="Basemath Logo" class="h-6 w-auto object-contain rounded-md border border-white/20 shadow-xs">
            </div>
        </div>
    </div>
</footer>

<!-- Policy Documents Modal Overlay -->
<div id="policy-modal-overlay" class="fixed inset-0 bg-slate-900/75 backdrop-blur-md z-[120] hidden flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="bg-white rounded-[32px] max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 my-auto flex flex-col max-h-[85vh]">
        <!-- Header -->
        <div class="p-6 bg-gradient-to-r from-[#0F172A] to-[#1E293B] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/10 p-1 flex items-center justify-center border border-white/15 shadow-sm shrink-0">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="w-full h-full object-contain rounded-xl">
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight" id="policy-title">Privacy Policy</h3>
                    <p class="text-[11px] text-slate-400 font-medium">justFeast RheamFeast Legal Notice</p>
                </div>
            </div>
            <button onclick="closePolicyModal()" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                <i class="fas fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Body Scroll Content -->
        <div class="p-6 overflow-y-auto space-y-5 text-slate-600 text-xs leading-relaxed font-medium" id="policy-content">
            <!-- Dynamic Copy Populated Here -->
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">RheamFeast Live Events</span>
            <button onclick="closePolicyModal()" class="px-5 py-2.5 rounded-full bg-[#0F172A] hover:bg-[#05A357] text-white text-xs font-black transition cursor-pointer">
                I Understand
            </button>
        </div>
    </div>
</div>
<!-- Multi-Option Payment Overlay (M-Pesa + LOOP Paybill) -->
<div id="mpesa-payment-overlay"
     class="hidden fixed inset-0 bg-[#111827]/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-[440px] bg-white rounded-[32px] shadow-2xl border border-[#E2E8F0] overflow-hidden">

        <!-- Modal header -->
        <div class="bg-[#0F172A] px-6 py-4 flex items-center justify-between text-white" id="payment-modal-header">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center bg-white px-2.5 py-1 rounded-full h-7 overflow-hidden shadow-xs border border-slate-200 shrink-0" id="payment-modal-logo-wrap">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="Payment" class="h-4 max-h-5 max-w-[60px] object-contain" id="payment-modal-logo">
                </span>
                <span class="text-white/40 text-xs font-bold">·</span>
                <span class="text-white font-black text-sm tracking-tight leading-none" id="payment-modal-title">Secure Checkout</span>
            </div>
            <button onclick="closeMpesaOverlay()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg font-bold leading-none cursor-pointer transition">&times;</button>
        </div>

        <!-- Order summary & Payment Method Switcher -->
        <div class="px-6 py-5 space-y-4">
            <div class="text-center space-y-2 py-1">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black">Total Payment</p>
                <p class="text-3xl font-black text-[#A31D1D]" id="mpesa-amount-display">Ksh 0</p>

                <!-- Payment Method Selector Tabs -->
                <div class="grid grid-cols-1 gap-1.5 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80 mt-2">
                    <button type="button" id="pay-tab-mpesa" onclick="switchPaymentMethodTab('mpesa')"
                            class="py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 bg-white text-[#0F172A] shadow-xs cursor-pointer truncate">
                        <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-4 w-auto object-contain shrink-0">
                        <span class="truncate">M-Pesa Express (STK Push)</span>
                    </button>
{{--                    <button type="button" id="pay-tab-intasend" onclick="switchPaymentMethodTab('intasend')"--}}
{{--                            class="py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 text-slate-500 hover:text-slate-800 cursor-pointer truncate">--}}
{{--                        <span class="bg-[#05A357] text-white text-[8px] px-1 py-0.5 rounded font-black shrink-0">INTASEND</span>--}}
{{--                        <span class="truncate">IntaSend</span>--}}
{{--                    </button>--}}
                </div>
            </div>

            <!-- Option 1: M-Pesa Input State -->
            <div id="mpesa-input-state" class="space-y-4 pt-1">
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-1.5">M-Pesa Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#05A357] text-sm"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="mpesa-phone-input" placeholder="e.g. 0712345678"
                               class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-[#05A357] focus:bg-white transition shadow-inner">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">You will receive an M-Pesa PIN prompt on this phone.</p>
                </div>

                <button onclick="triggerMpesaStkPush()"
                        class="w-full py-3.5 px-4 bg-[#05A357] hover:bg-[#047A43] active:scale-[0.98] text-white rounded-2xl text-sm font-black shadow-lg shadow-[#05A357]/25 hover:shadow-xl transition-all flex items-center justify-center gap-3 cursor-pointer">
                    <span class="bg-white px-2.5 py-1 rounded-full inline-flex items-center justify-center shadow-xs shrink-0">
                        <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-4 max-h-5 max-w-[60px] object-contain"
                             onerror="this.outerHTML='<span class=\'text-[#05A357] font-black text-[9px]\'>M-PESA</span>'">
                    </span>
                    <span class="font-black text-white tracking-tight">Pay with M-Pesa Express</span>
                    <i class="fas fa-arrow-right text-xs text-[#FFC244]"></i>
                </button>
            </div>

            <!-- Option 2: IntaSend STK Push State -->
            <div id="intasend-input-state" class="hidden space-y-4 pt-1">
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-1.5">IntaSend M-Pesa Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#05A357] text-sm"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="intasend-phone-input" placeholder="e.g. 0712345678"
                               class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-[#05A357] focus:bg-white transition shadow-inner">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">STK Push prompt will be sent via IntaSend Payment Gateway.</p>
                </div>

                <button onclick="triggerIntaSendPayment()"
                        class="w-full py-3.5 px-4 bg-[#05A357] hover:bg-[#047A43] active:scale-[0.98] text-white rounded-2xl text-sm font-black shadow-lg shadow-[#05A357]/25 hover:shadow-xl transition-all flex items-center justify-center gap-3 cursor-pointer">
                    <span class="bg-white px-2 py-0.5 rounded-full inline-flex items-center justify-center shadow-xs shrink-0">
                        <span class="text-[#05A357] font-black text-[9px]">INTASEND</span>
                    </span>
                    <span class="font-black text-white tracking-tight">Pay via IntaSend STK</span>
                    <i class="fas fa-arrow-right text-xs text-[#FFC244]"></i>
                </button>
            </div>

            <!-- Option 2: LOOP Paybill Collection State -->
            <div id="loop-input-state" class="hidden space-y-3 pt-1">
                <div class="bg-purple-950/5 border border-purple-500/20 p-3.5 rounded-2xl space-y-2.5">
                    <div class="flex items-center justify-between border-b border-purple-100/80 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#5B21B6] text-white flex items-center justify-center text-xs font-black shadow-xs">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-[#5B21B6]">LOOP M-Pesa Paybill</h4>
                                <p class="text-[10px] text-slate-500 font-medium">Lipa na M-Pesa → Pay Bill</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                            <i class="fas fa-clock text-[9px] mr-1"></i> Expires in 30m
                        </span>
                    </div>

                    <!-- Paybill Credentials with Copy buttons -->
                    <div class="grid grid-cols-3 gap-2 text-left">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-xs relative">
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Paybill No.</span>
                            <span id="loop-display-paybill" class="font-black text-xs text-slate-800 tracking-tight">600100</span>
                            <button onclick="copyToClipboard('600100', 'btn-copy-paybill')" id="btn-copy-paybill"
                                    class="mt-1 text-[9px] font-bold text-[#5B21B6] hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-copy text-[9px]"></i> Copy
                            </button>
                        </div>

                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-xs relative">
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Account No.</span>
                            <span id="loop-display-acc-ref" class="font-black text-xs text-[#5B21B6] tracking-tight">...</span>
                            <button onclick="copyToClipboard(document.getElementById('loop-display-acc-ref').textContent, 'btn-copy-ref')" id="btn-copy-ref"
                                    class="mt-1 text-[9px] font-bold text-[#5B21B6] hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-copy text-[9px]"></i> Copy
                            </button>
                        </div>

                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-xs relative">
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Amount</span>
                            <span id="loop-display-amount" class="font-black text-xs text-emerald-700 tracking-tight">KES 0</span>
                            <button onclick="copyToClipboard(document.getElementById('loop-display-amount').textContent.replace('KES ', '').replace(/,/g, ''), 'btn-copy-amt')" id="btn-copy-amt"
                                    class="mt-1 text-[9px] font-bold text-[#5B21B6] hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-copy text-[9px]"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Step-by-step instructions -->
                    <ol class="text-[10px] text-slate-600 font-bold space-y-1 pl-4 list-decimal marker:text-[#5B21B6] marker:font-black">
                        <li>Open M-Pesa menu & select <span class="text-slate-800 font-black">Lipa na M-Pesa</span> → <span class="text-slate-800 font-black">Pay Bill</span></li>
                        <li>Enter Business No. <span class="font-black text-slate-800">600100</span> & Account No. <span class="font-black text-[#5B21B6]" id="loop-step-acc-ref">...</span></li>
                        <li>Enter exact amount & your M-Pesa PIN to complete payment</li>
                    </ol>
                </div>

                <!-- Toggle Claim Box Button -->
                <button onclick="toggleLoopClaimBox()"
                        class="w-full py-3 px-4 bg-[#5B21B6] hover:bg-[#4C1D95] active:scale-[0.98] text-white rounded-2xl text-sm font-black shadow-lg shadow-[#5B21B6]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fas fa-check-circle text-xs text-[#FFC244]"></i>
                    <span class="font-black text-white tracking-tight">I Have Paid</span>
                    <i class="fas fa-chevron-down text-xs text-white/60 transition-transform" id="loop-claim-chevron"></i>
                </button>

                <!-- Hidden Claim Input Box -->
                <div id="loop-claim-box" class="hidden space-y-2.5 bg-slate-50 border border-slate-200 p-3.5 rounded-2xl animate-fade-in">
                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1">M-Pesa Receipt Code</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs"><i class="fas fa-receipt"></i></span>
                            <input type="text" id="loop-receipt-input" placeholder="e.g. QGH82910X"
                                   class="w-full pl-8 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-800 tracking-wider focus:outline-none focus:border-[#5B21B6]">
                        </div>
                        <p class="text-[9px] text-slate-400 mt-1 font-medium">Enter the M-Pesa confirmation code sent to your phone (e.g. QGH82910X).</p>
                    </div>

                    <button onclick="submitLoopPaymentClaim()"
                            class="w-full py-3 px-3 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-black shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-shield-halved text-xs"></i>
                        <span>Submit Receipt & Verify Payment</span>
                    </button>
                </div>
            </div>

            <!-- Loading state (Creating order & sending API request) -->
            <div id="mpesa-loading-state" class="hidden text-center py-6 space-y-3">
                <div class="relative w-14 h-14 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 border-4 border-[#0F172A]/20 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-[#0F172A] border-t-transparent rounded-full animate-spin"></div>
                    <i class="fas fa-credit-card text-[#0F172A] text-lg"></i>
                </div>
                <p class="text-xs text-zinc-600 font-bold" id="mpesa-loading-msg">Processing payment request...</p>
            </div>

            <!-- Pending / Prompt Sent state -->
            <div id="mpesa-pending-state" class="hidden text-center py-4 space-y-3">
                <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center animate-bounce border border-slate-200 p-2 text-[#0F172A]">
                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                </div>
                <div>
                    <p class="text-base font-black text-[#111827]">Payment Processing!</p>
                    <p class="text-xs text-zinc-500 mt-1" id="mpesa-prompt-phone-label">Authorizing transaction with provider...</p>
                </div>
                <div class="h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0F172A] rounded-full animate-pulse" style="width:75%"></div>
                </div>
                <p class="text-[10px] text-zinc-400 font-medium">Waiting for transaction confirmation...</p>
            </div>

            <!-- Error state -->
            <div id="mpesa-error-state" class="hidden text-center py-4 space-y-3">
                <div class="w-12 h-12 mx-auto bg-red-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-500 text-xl"></i>
                </div>
                <p class="text-sm font-black text-red-600" id="mpesa-error-msg">Payment failed</p>
                <button onclick="showMpesaState('input')" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold rounded-xl transition cursor-pointer">Try Again</button>
            </div>
        </div>
    </div>
</div>



<!-- Authentication Modal Overlay (OTP Login & Registration) -->
<div id="auth-modal-overlay"
     class="hidden fixed inset-0 bg-[#2D3748]/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <!-- Auth / Registration Screen -->
    <div id="cust-auth"
         class="w-full max-w-[400px] bg-white rounded-[32px] p-6 sm:p-7 shadow-2xl border border-[#E2E8F0] relative">
        <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 cursor-pointer p-1"><i class="fas fa-times"></i></button>
        <div class="text-center space-y-4">
            <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-12 w-auto mx-auto rounded-2xl shadow-md border border-black/10 shrink-0 block">

            <!-- Tab Mode Switcher: Log In vs Register -->
            <div class="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200/80 gap-1 text-xs font-black">
                <button type="button" id="auth-tab-login" onclick="switchAuthTab('login')"
                        class="flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 bg-white text-[#0F172A] shadow-xs font-black">
                    <i class="fas fa-right-to-bracket text-[#05A357]"></i>
                    <span>Log In</span>
                </button>
                <button type="button" id="auth-tab-register" onclick="switchAuthTab('register')"
                        class="flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold">
                    <i class="fas fa-user-plus text-[#A31D1D]"></i>
                    <span>Register</span>
                </button>
            </div>

            <div class="space-y-1">
                <h2 class="text-xl font-black text-[#2D3748]" id="auth-modal-title">Log In to your Account</h2>
                <p class="text-xs text-zinc-500 font-medium" id="auth-modal-subtitle">Enter your phone number to receive your instant verification OTP code.</p>
            </div>

            <!-- Inline Auth Alert / Error Banner -->
            <div id="auth-error-banner" class="hidden rounded-2xl p-3.5 text-xs font-bold transition-all border">
                <div class="flex items-start gap-2.5">
                    <i id="auth-error-icon" class="fas fa-circle-exclamation text-base shrink-0 mt-0.5"></i>
                    <div class="space-y-1 flex-1 text-left">
                        <p id="auth-error-msg" class="leading-snug"></p>
                        <button type="button" id="auth-error-action" class="hidden text-[11px] font-black underline underline-offset-2 cursor-pointer mt-1 block"></button>
                    </div>
                    <button type="button" onclick="hideAuthError()" class="text-xs opacity-60 hover:opacity-100 cursor-pointer p-0.5"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="space-y-3.5 text-left">
                <!-- Name field (shown only in Register mode) -->
                <div id="field-wrap-name" class="hidden">
                    <label class="block text-[9.5px] font-black text-zinc-600 mb-1 uppercase tracking-wider">Full Name *</label>
                    <input type="text" id="cust-name-input" placeholder="e.g. John Doe"
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-xs font-bold focus:outline-none focus:border-[#05A357] transition"
                           value="">
                </div>

                <!-- Phone number field (always required) -->
                <div>
                    <label class="block text-[9.5px] font-black text-zinc-600 mb-1 uppercase tracking-wider">Phone Number *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-zinc-400 text-xs font-bold"><i class="fas fa-phone text-[#05A357]"></i></span>
                        <input type="tel" id="cust-phone-input" placeholder="e.g. 0712345678" required
                               class="w-full pl-9 pr-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-xs font-bold focus:outline-none focus:border-[#05A357] transition"
                               value="">
                    </div>
                </div>

                <!-- Email field (shown only in Register mode) -->
                <div id="field-wrap-email" class="hidden">
                    <label class="block text-[9.5px] font-black text-zinc-600 mb-1 uppercase tracking-wider">Email Address (Optional)</label>
                    <input type="email" id="cust-email-input" placeholder="e.g. john@example.com"
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-xs font-bold focus:outline-none focus:border-[#05A357] transition"
                           value="">
                </div>

                <button onclick="sendOTP()" type="button" id="auth-submit-btn"
                        class="w-full py-3.5 rounded-full bg-[#05A357] hover:bg-[#047A43] text-white font-black text-xs transition shadow-md shadow-[#05A357]/20 border-0 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-key text-xs"></i>
                    <span id="auth-submit-btn-text">Generate Login OTP</span>
                </button>
            </div>

            <!-- Bottom Links -->
            <div class="pt-3 border-t border-slate-100 flex flex-col items-center gap-2 text-xs">
                <span class="text-slate-400 font-medium" id="auth-footer-prompt">First time ordering? <a href="javascript:void(0)" onclick="switchAuthTab('register')" class="text-[#05A357] font-bold hover:underline">Create an Account</a></span>
            </div>
        </div>
    </div>

    <!-- OTP Screen -->
    <div id="cust-otp"
         class="hidden w-full max-w-[380px] bg-white rounded-[32px] p-7 shadow-2xl border border-[#E2E8F0] relative">
        <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 cursor-pointer"><i class="fas fa-times"></i></button>
        <div class="text-center space-y-5">
            <h3 class="text-xl font-black text-[#2D3748]">Confirm OTP Code</h3>

            <!-- System OTP Display Banner -->
            <div class="bg-[#05A357]/10 border border-[#05A357]/30 rounded-2xl p-4 text-center space-y-1">
                <p class="text-[9px] uppercase tracking-widest text-[#05A357] font-black">System Generated OTP Code</p>
                <p class="text-3xl font-black tracking-widest text-[#05A357]" id="generated-otp-display">------</p>
                <button onclick="autoFillOTP()" class="mt-1 text-[11px] font-bold text-[#05A357] underline hover:text-[#047A43] cursor-pointer inline-flex items-center gap-1">
                    <i class="fas fa-magic"></i> Auto-fill Code
                </button>
            </div>

            <p class="text-xs text-zinc-500 font-medium" id="otp-phone-text">Verification code generated for your account.</p>

            <!-- Inline OTP Error Banner -->
            <div id="otp-error-banner" class="hidden rounded-2xl p-3 text-xs font-bold transition-all border bg-red-50 text-red-700 border-red-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-circle-exclamation text-red-600 shrink-0"></i>
                    <span id="otp-error-msg" class="flex-1 text-left"></span>
                    <button type="button" onclick="hideOtpError()" class="opacity-60 hover:opacity-100 cursor-pointer p-0.5"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="space-y-3.5 text-left">
                <div>
                    <label class="block text-[9px] font-black text-zinc-500 mb-1 uppercase tracking-wider">Enter 6-Digit Code</label>
                    <input type="text" id="cust-otp-input" placeholder="e.g. 123456" maxlength="6"
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-center text-xl tracking-widest font-black focus:outline-none focus:border-[#05A357] transition"
                           value="">
                </div>
                <button onclick="verifyOTP()" id="cust-otp-verify-btn"
                        class="w-full py-3.5 rounded-full bg-[#05A357] hover:bg-[#047A43] text-white font-black text-xs transition shadow-md shadow-[#05A357]/20 border-0 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle text-xs"></i>
                    <span>Verify Code & Login</span>
                </button>
                <button onclick="showAuthScreen()"
                        class="w-full py-2 rounded-full text-zinc-400 hover:text-zinc-700 text-xs font-bold text-center transition cursor-pointer">
                    <i class="fas fa-arrow-left mr-1"></i> Edit Phone / Details
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stadium Interactive SVG Map Modal -->
<div id="stadium-modal-overlay"
     class="hidden fixed inset-0 bg-white/95 z-50 flex flex-col p-4 max-w-md mx-auto shadow-2xl border border-[#E2E8F0]">
    <div class="flex justify-between items-center mb-3">
        <span class="text-xs font-bold text-[#2D3748]"><i class="fas fa-map-marked-alt text-[#00A082] mr-1.5"></i> Select Delivery Location</span>
        <button onclick="closeSeatModal()" class="text-zinc-500 hover:text-zinc-800 text-sm"><i
                class="fas fa-times"></i></button>
    </div>

    <!-- View B: GPS Selector Map -->
    <div id="modal-gps-selector-view" class="flex-grow flex flex-col justify-between">
        <div class="space-y-3 flex-grow flex flex-col">
            <button type="button" onclick="getCurrentGPSLocation()"
                    class="w-full py-2 bg-[#05A357] hover:bg-[#047A43] text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow shadow-[#05A357]/20">
                <i class="fas fa-location-crosshairs"></i> Use Current GPS Location
            </button>

            <!-- Map Container -->
            <div id="modal-leaflet-map"
                 class="w-full flex-grow min-h-[220px] rounded-2xl border border-[#E2E8F0] overflow-hidden bg-zinc-100 z-10"></div>

            <div class="space-y-2 bg-[#F7F9FA] p-3 rounded-2xl border border-[#E2E8F0]">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[8px] font-bold text-zinc-500 uppercase">Latitude</label>
                        <input type="text" id="gps-lat-input"
                               class="w-full px-2 py-1.5 rounded bg-white border border-[#E2E8F0] text-[10px] font-mono font-bold text-zinc-700"
                               readonly value="-1.28817042">
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-zinc-500 uppercase">Longitude</label>
                        <input type="text" id="gps-lng-input"
                               class="w-full px-2 py-1.5 rounded bg-white border border-[#E2E8F0] text-[10px] font-mono font-bold text-zinc-700"
                               readonly value="36.81647301">
                    </div>
                </div>
                <div>
                    <label class="block text-[8px] font-bold text-zinc-500 uppercase">Landmark / Clothing
                        Description</label>
                    <input type="text" id="gps-desc-input" placeholder="e.g. Red jacket, near Gate 4 entrance"
                           class="w-full px-2 py-1.5 rounded bg-white border border-[#E2E8F0] text-[10px] font-semibold text-zinc-700 placeholder-zinc-400 focus:outline-none focus:border-[#00A082]">
                </div>
            </div>
            <button onclick="saveGPSCoordinates()"
                    class="w-full py-3 bg-[#FFC244] hover:bg-[#E0A325] text-[#2D3748] rounded-full text-xs font-black shadow-md shadow-[#FFC244]/15 border border-[#E0A325] transition mt-2">
                Save Location Pin
            </button>
        </div>
    </div>
</div>

<!-- JS Logic -->
<script>
    const laravelUser = @auth @json(Auth::user()) @else null @endauth;
    let currentUser = null;
    let activeEvent = null;
    let vendors = [];
    let selectedCategory = 'all';
    let basket = [];
    let activeOrder = null;
    let hasBeepedForArrival = false;
    const defaultSeat = {
        type: 'gps',
        latitude: -1.28817042,
        longitude: 36.81647301,
        description: 'Uhuru Park, Main Stage Grounds'
    };
    let selectedSeat = defaultSeat;
    let leafletMap = null;
    let leafletMarker = null;
    let activeSeatingMode = 'gps';
    let pollingInterval = null;
    let audioCtx = null;

    function applySeatToUI(seatObj) {
        if (!seatObj) return;
        if (seatObj.type === 'gps') {
            const lat = seatObj.latitude;
            const lng = seatObj.longitude;
            const desc = seatObj.description || '';
            const labelEl = document.getElementById('selected-seat-label');
            const subEl = document.getElementById('selected-seat-sub');
            if (labelEl) labelEl.textContent = "GPS Location Pin";
            if (subEl) subEl.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}${desc ? ' — ' + desc : ''}`;

            const locationText = `GPS Pin: ${desc || (lat.toFixed(4) + ', ' + lng.toFixed(4))}`;
            const cartLoc = document.getElementById('cart-location-text');
            const desktopLoc = document.getElementById('desktop-cart-location-text');
            const heroLoc = document.getElementById('selected-seat-hero');
            if (cartLoc) cartLoc.textContent = locationText;
            if (desktopLoc) desktopLoc.textContent = locationText;
            if (heroLoc) heroLoc.textContent = locationText;

            const latInput = document.getElementById('gps-lat-input');
            const lngInput = document.getElementById('gps-lng-input');
            const descInput = document.getElementById('gps-desc-input');
            if (latInput) latInput.value = lat.toFixed(8);
            if (lngInput) lngInput.value = lng.toFixed(8);
            if (descInput && desc) descInput.value = desc;
        } else {
            const {section, row, seat} = seatObj;
            const labelEl = document.getElementById('selected-seat-label');
            const subEl = document.getElementById('selected-seat-sub');
            if (labelEl) labelEl.textContent = section || 'Stadium Seat';
            if (subEl) subEl.textContent = `${row || ''} — ${seat || ''}`;
            const locationText = `${section || ''}, ${row || ''}, ${seat || ''}`;
            const cartLoc = document.getElementById('cart-location-text');
            const desktopLoc = document.getElementById('desktop-cart-location-text');
            const heroLoc = document.getElementById('selected-seat-hero');
            if (cartLoc) cartLoc.textContent = locationText;
            if (desktopLoc) desktopLoc.textContent = locationText;
            if (heroLoc) heroLoc.textContent = locationText;
        }
        const statusPill = document.getElementById('seat-status-pill');
        if (statusPill) {
            statusPill.textContent = "Configured";
            statusPill.className = "text-[9px] bg-brand-emerald/20 text-brand-emerald px-2.5 py-0.5 rounded-full font-bold border border-brand-emerald/30";
        }
    }

    function playSound(type) {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            if (type === 'beep') {
                osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.1);
            } else if (type === 'success') {
                osc.frequency.setValueAtTime(800, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.2);
                gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.25);
            } else if (type === 'alert') {
                osc.frequency.setValueAtTime(400, audioCtx.currentTime);
                osc.frequency.setValueAtTime(300, audioCtx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.3);
            }
        } catch (e) {
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        loadActiveEvent();
        loadVendors();
        fetchSystemSettings();

        // Always show the marketplace main wrapper
        document.getElementById('cust-main').classList.remove('hidden');

        // Purge stale mock-token sessions (had no real token)
        const saved = localStorage.getItem('justfeast_client_user');
        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                if (parsed && (parsed.name === 'John Customer' || parsed.email === 'customer@justfeast.com' || !parsed.__token)) {
                    // Old mock-token sessions have no __token — clear them
                    localStorage.removeItem('justfeast_client_user');
                    currentUser = null;
                } else {
                    currentUser = parsed;
                }
            } catch (e) {
                localStorage.removeItem('justfeast_client_user');
            }
        }
        updateAuthHeader();

        // Location recovery or default setup
        const savedSeat = localStorage.getItem('justfeast_selected_seat');
        if (savedSeat) {
            try {
                selectedSeat = JSON.parse(savedSeat);
            } catch (e) {
                selectedSeat = defaultSeat;
            }
        } else {
            selectedSeat = defaultSeat;
        }
        applySeatToUI(selectedSeat);

        pollingInterval = setInterval(syncActiveOrder, 2000);
        checkPWAPrompt();
    });

    function updateAuthHeader() {
        const authButtonsContainer = document.getElementById('header-auth-buttons');
        const userBadge = document.getElementById('header-user-badge');
        const userNameText = document.getElementById('header-user-name');
        const sidebarWelcome = document.getElementById('cust-user-name');
        const sidebarPassBadge = document.getElementById('cust-user-pass-badge');
        const sidebarStatusText = document.getElementById('cust-user-status-text');
        const sidebarAction = document.getElementById('cust-profile-action');

        if (currentUser) {
            if (userBadge) {
                userBadge.classList.remove('hidden');
                userNameText.textContent = currentUser.name;
            }
            if (sidebarWelcome) {
                sidebarWelcome.textContent = currentUser.name;
            }
            if (sidebarPassBadge) {
                sidebarPassBadge.textContent = 'Pass';
                sidebarPassBadge.className = 'bg-[#FFC244]/25 text-[#0F172A] text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full shrink-0';
            }
            if (sidebarStatusText) {
                sidebarStatusText.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-[#05A357]"></span> Verified Attendee`;
            }
            if (sidebarAction) {
                sidebarAction.innerHTML = `
                        <button onclick="logoutCustomer()" title="Logout" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center text-xs transition border border-slate-200 cursor-pointer">
                            <i class="fas fa-right-from-bracket"></i>
                        </button>
                    `;
            }
            if (authButtonsContainer) {
                authButtonsContainer.innerHTML = `
                        <button onclick="logoutCustomer()" class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-full transition cursor-pointer focus:outline-none flex items-center gap-1 whitespace-nowrap shadow-xs">
                            <i class="fas fa-sign-out-alt text-slate-400"></i> Logout
                        </button>
                    `;
            }
        } else {
            if (userBadge) {
                userBadge.classList.add('hidden');
            }
            if (sidebarWelcome) {
                sidebarWelcome.textContent = 'Guest';
            }
            if (sidebarPassBadge) {
                sidebarPassBadge.textContent = 'Guest';
                sidebarPassBadge.className = 'bg-slate-100 text-slate-500 text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full shrink-0 border border-slate-200';
            }
            if (sidebarStatusText) {
                sidebarStatusText.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Tap Register / Log In to order`;
            }
            if (sidebarAction) {
                sidebarAction.innerHTML = `
                        <div class="flex items-center gap-1.5">
                            <button onclick="openAuthModal('login')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#0F172A] rounded-full text-xs font-bold transition border border-slate-200 shadow-xs cursor-pointer">
                                Log In
                            </button>
                            <button onclick="openAuthModal('register')" class="px-3.5 py-1.5 bg-[#FFC244] hover:bg-[#E0A325] text-[#0F172A] rounded-full text-xs font-black transition border border-[#E0A325] shadow-xs cursor-pointer">
                                Register
                            </button>
                        </div>
                    `;
            }
            if (authButtonsContainer) {
                authButtonsContainer.innerHTML = `
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <button onclick="openAuthModal('login')" class="px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-black text-slate-700 hover:text-[#0F172A] bg-white hover:bg-slate-50 border border-slate-200 rounded-full transition shadow-xs cursor-pointer focus:outline-none whitespace-nowrap">
                                Log In
                            </button>
                            <button onclick="openAuthModal('register')" class="px-3.5 sm:px-5 py-1.5 sm:py-2.5 text-[10px] sm:text-xs font-black text-[#2D3748] bg-[#FFC244] hover:bg-[#E0A325] border border-[#E0A325] rounded-full transition shadow-sm cursor-pointer focus:outline-none whitespace-nowrap">
                                Register
                            </button>
                        </div>
                    `;
            }
        }
    }

    async function loadActiveEvent() {
        try {
            const res = await fetch(`${API_BASE}/events/active`);
            if (res.ok) {
                activeEvent = await res.json();
                document.getElementById('live-event-banner').innerHTML = `<span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-[#05A357] animate-ping shrink-0"></span> <span class="truncate">${activeEvent.name} <span class="hidden sm:inline">— @${activeEvent.venue.name}</span></span>`;
            }
        } catch (e) {
        }
    }

    let heroVendorAdIds = [];

    function renderHeroVendorAdverts() {
        if (!vendors || !vendors.length) return;

        heroVendorAdIds = [];
        vendors.slice(0, 4).forEach((v, index) => {
            heroVendorAdIds[index] = v.id;

            const nameEl = document.getElementById(`hero-ad-name-${index}`);
            const subEl = document.getElementById(`hero-ad-sub-${index}`);
            const logoEl = document.getElementById(`hero-ad-logo-${index}`);

            if (nameEl) nameEl.textContent = v.business_name;

            let subText = 'Verified Stall';
            if (v.products && v.products.length) {
                const prices = v.products.map(p => parseFloat(p.price)).filter(pr => !isNaN(pr) && pr > 0);
                if (prices.length) {
                    const minPrice = Math.min(...prices);
                    subText = `from Ksh ${Math.round(minPrice).toLocaleString()}`;
                } else {
                    subText = `${v.products.length} Items Menu`;
                }
            }
            if (subEl) subEl.textContent = subText;

            if (logoEl) {
                if (v.logo_url && (v.logo_url.startsWith('/') || v.logo_url.startsWith('http'))) {
                    logoEl.innerHTML = `<img src="${v.logo_url}" class="w-6 h-6 sm:w-8 sm:h-8 object-cover rounded-xl shrink-0" alt="${v.business_name}">`;
                } else {
                    const fallbackEmojis = ['🍔', '🥤', '🍿', '🌮', '🍖'];
                    const emoji = (v.logo_url && v.logo_url.length <= 4) ? v.logo_url : fallbackEmojis[index % fallbackEmojis.length];
                    logoEl.textContent = emoji;
                }
            }
        });
    }

    function selectHeroVendorAd(index) {
        if (heroVendorAdIds[index]) {
            selectVendor(heroVendorAdIds[index]);
        }
    }

    async function loadVendors() {
        try {
            const res = await fetch(`${API_BASE}/vendors`);
            if (res.ok) {
                const data = await res.json();
                vendors = Array.isArray(data) ? data : (data.data || []);
                renderVendors();
                renderHeroVendorAdverts();
            }
        } catch (e) {
            console.error("Error loading marketplace vendors:", e);
        }
    }
    // ── Auth helpers ───────────────────────────────────────────────────
    function getToken() {
        try {
            const s = localStorage.getItem('justfeast_client_user');
            return s ? JSON.parse(s).__token : null;
        } catch(e) { return null; }
    }

    /**
     * Authenticated fetch — injects Authorization: Bearer <token>.
     * On 401, clears session and reloads.
     */
    async function authFetch(url, options = {}) {
        const token = getToken();
        options.headers = options.headers || {};
        if (token) options.headers['Authorization'] = `Bearer ${token}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (csrfToken && !options.headers['X-CSRF-TOKEN']) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        if (!options.headers['Accept']) options.headers['Accept'] = 'application/json';
        if (!options.headers['X-Requested-With']) options.headers['X-Requested-With'] = 'XMLHttpRequest';
        const res = await fetch(url, options);
        if (res.status === 401) {
            currentUser = null;
            localStorage.removeItem('justfeast_client_user');
            updateAuthHeader();
        }
        return res;
    }

    function getProductCategory(p) {
        if (!p) return 'food';
        const name = ((p.name || '') + ' ' + (p.category || '') + ' ' + (p.description || '')).toLowerCase();
        if (name.includes('beer') || name.includes('lager') || name.includes('coca') || name.includes('coke') || name.includes('soda') || name.includes('drink') || name.includes('water') || name.includes('juice') || name.includes('beverage') || name.includes('wine') || name.includes('whiskey') || name.includes('spirit') || name.includes('cocktail') || name.includes('fanta') || name.includes('sprite') || name.includes('pepsi') || name.includes('tea') || name.includes('coffee') || name.includes('mocktail')) {
            return 'drinks';
        }
        if (name.includes('fries') || name.includes('churros') || name.includes('nachos') || name.includes('chips') || name.includes('onion rings') || name.includes('snack') || name.includes('popcorn') || name.includes('nuts') || name.includes('crisps') || name.includes('biscuit') || name.includes('cookie') || name.includes('candy') || name.includes('sweets')) {
            return 'snacks';
        }
        return 'food';
    }

    function setCategory(cat) {
        playSound('beep');
        selectedCategory = cat;

        const categories = ['all', 'food', 'drinks', 'snacks'];
        categories.forEach(c => {
            const btn = document.getElementById(`cat-${c}`);
            if (!btn) return;
            if (c === cat) {
                btn.classList.add('category-active');
            } else {
                btn.classList.remove('category-active');
            }
        });

        renderVendors();
    }

    function searchMenu() {
        renderVendors();
    }

    function searchMenu() {
        renderVendors();
    }

    function showConfirmModal(title, message, confirmText, cancelText) {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = "fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 transition-all duration-300";

            const card = document.createElement('div');
            card.className = "bg-white rounded-[32px] p-6 max-w-sm w-full text-center space-y-5 shadow-2xl border border-zinc-100 transform scale-95 opacity-0 transition-all duration-300";

            card.innerHTML = `
                <div class="w-12 h-12 bg-[#FFC244]/15 text-[#e6a920] rounded-full flex items-center justify-center mx-auto text-xl">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-base font-black text-zinc-900">${title}</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">${message}</p>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button id="confirm-modal-cancel" class="py-3 bg-[#F6F7F2] hover:bg-[#E7E8DD] text-zinc-700 rounded-full text-xs font-black transition cursor-pointer border-0">
                        ${cancelText}
                    </button>
                    <button id="confirm-modal-ok" class="py-3 bg-[#05A357] hover:bg-[#047A43] text-white rounded-full text-xs font-black transition shadow-md shadow-[#05A357]/10 cursor-pointer border-0">
                        ${confirmText}
                    </button>
                </div>
            `;

            overlay.appendChild(card);
            document.body.appendChild(overlay);

            // Trigger animation
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);

            const cleanup = (value) => {
                card.classList.remove('scale-100', 'opacity-100');
                card.classList.add('scale-95', 'opacity-0');
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.remove();
                }, 300);
                resolve(value);
            };

            overlay.querySelector('#confirm-modal-cancel').addEventListener('click', () => cleanup(false));
            overlay.querySelector('#confirm-modal-ok').addEventListener('click', () => cleanup(true));
        });
    }

    async function addToBasket(id, name, price, vendorId) {
        playSound('beep');
        const existing = basket.find(item => item.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            basket.push({id, name, price, quantity: 1, vendorId});
        }
        renderBasket();
    }



    function adjustQty(id, amt) {
        const item = basket.find(i => i.id === id);
        if (item) {
            item.quantity += amt;
            if (item.quantity <= 0) {
                basket = basket.filter(i => i.id !== id);
            }
        }
        renderBasket();
        renderVendors();
    }

    function clearBasket() {
        basket = [];
        renderBasket();
        renderVendors();
    }

    let currentAuthMode = 'login';
    let lastGeneratedOTP = '';

    function showAuthError(msg, type = 'danger', actionText = null, actionCallback = null) {
        const banner    = document.getElementById('auth-error-banner');
        const icon      = document.getElementById('auth-error-icon');
        const msgEl     = document.getElementById('auth-error-msg');
        const actionBtn = document.getElementById('auth-error-action');

        if (!banner || !msgEl) return;

        msgEl.textContent = msg;

        if (type === 'danger') {
            banner.className = "rounded-2xl p-3.5 text-xs font-bold transition-all border bg-red-50 text-red-700 border-red-200 mb-2";
            icon.className = "fas fa-circle-exclamation text-base text-red-600 shrink-0 mt-0.5";
        } else if (type === 'warning') {
            banner.className = "rounded-2xl p-3.5 text-xs font-bold transition-all border bg-amber-50 text-amber-800 border-amber-200 mb-2";
            icon.className = "fas fa-triangle-exclamation text-base text-amber-600 shrink-0 mt-0.5";
        } else {
            banner.className = "rounded-2xl p-3.5 text-xs font-bold transition-all border bg-emerald-50 text-emerald-800 border-emerald-200 mb-2";
            icon.className = "fas fa-circle-check text-base text-emerald-600 shrink-0 mt-0.5";
        }

        if (actionText && actionCallback) {
            actionBtn.textContent = actionText;
            actionBtn.classList.remove('hidden');
            actionBtn.onclick = () => {
                actionCallback();
                hideAuthError();
            };
        } else {
            actionBtn.classList.add('hidden');
            actionBtn.onclick = null;
        }

        banner.classList.remove('hidden');
    }

    function hideAuthError() {
        const banner = document.getElementById('auth-error-banner');
        if (banner) banner.classList.add('hidden');
    }

    function showOtpError(msg) {
        const banner = document.getElementById('otp-error-banner');
        const msgEl  = document.getElementById('otp-error-msg');
        if (!banner || !msgEl) return;
        msgEl.textContent = msg;
        banner.classList.remove('hidden');
    }

    function hideOtpError() {
        const banner = document.getElementById('otp-error-banner');
        if (banner) banner.classList.add('hidden');
    }

    function showToast(msg, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-[#05A357]' : type === 'danger' ? 'bg-[#A31D1D]' : 'bg-[#0F172A]';
        const icon = type === 'success' ? 'fa-circle-check' : type === 'danger' ? 'fa-circle-exclamation' : 'fa-info-circle';

        toast.className = `${bgColor} text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-bold pointer-events-auto transition-all transform translate-y-2 opacity-0`;
        toast.innerHTML = `<i class="fas ${icon} text-base"></i> <span class="flex-1">${msg}</span>`;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    function switchAuthTab(mode) {
        hideAuthError();
        hideOtpError();
        currentAuthMode = mode;
        const tabLogin = document.getElementById('auth-tab-login');
        const tabRegister = document.getElementById('auth-tab-register');
        const fieldName = document.getElementById('field-wrap-name');
        const fieldEmail = document.getElementById('field-wrap-email');
        const modalTitle = document.getElementById('auth-modal-title');
        const modalSubtitle = document.getElementById('auth-modal-subtitle');
        const btnText = document.getElementById('auth-submit-btn-text');
        const footerPrompt = document.getElementById('auth-footer-prompt');

        if (!tabLogin || !tabRegister) return;

        if (mode === 'login') {
            tabLogin.className = "flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 bg-white text-[#0F172A] shadow-xs font-black";
            tabRegister.className = "flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold";
            if (fieldName) fieldName.classList.add('hidden');
            if (fieldEmail) fieldEmail.classList.add('hidden');
            if (modalTitle) modalTitle.textContent = "Customer Log In";
            if (modalSubtitle) modalSubtitle.textContent = "Enter your phone number to receive your instant verification OTP.";
            if (btnText) btnText.textContent = "Generate Login OTP";
            if (footerPrompt) footerPrompt.innerHTML = `First time ordering? <a href="javascript:void(0)" onclick="switchAuthTab('register')" class="text-[#05A357] font-bold hover:underline">Create an Account</a>`;
        } else {
            tabRegister.className = "flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 bg-white text-[#0F172A] shadow-xs font-black";
            tabLogin.className = "flex-1 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold";
            if (fieldName) fieldName.classList.remove('hidden');
            if (fieldEmail) fieldEmail.classList.remove('hidden');
            if (modalTitle) modalTitle.textContent = "Customer Registration";
            if (modalSubtitle) modalSubtitle.textContent = "Set up your details for stadium seat delivery & order tracking.";
            if (btnText) btnText.textContent = "Generate Registration OTP";
            if (footerPrompt) footerPrompt.innerHTML = `Already registered? <a href="javascript:void(0)" onclick="switchAuthTab('login')" class="text-[#05A357] font-bold hover:underline">Log In</a>`;
        }
    }

    async function sendOTP() {
        hideAuthError();

        const name  = document.getElementById('cust-name-input').value.trim();
        const phone = document.getElementById('cust-phone-input').value.trim();
        const email = document.getElementById('cust-email-input').value.trim();

        if (!phone) {
            showAuthError("Please enter your phone number to proceed.", "warning");
            document.getElementById('cust-phone-input').focus();
            return;
        }

        if (currentAuthMode === 'register' && !name) {
            showAuthError("Please enter your full name to create an account.", "warning");
            document.getElementById('cust-name-input').focus();
            return;
        }

        const btn = document.getElementById('auth-submit-btn');
        const btnText = document.getElementById('auth-submit-btn-text');
        if (btn) {
            btn.disabled = true;
            if (btnText) btnText.textContent = "Generating OTP...";
        }

        try {
            const payload = { phone, mode: currentAuthMode };
            if (name) payload.name = name;
            if (email) payload.email = email;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const res = await fetch(`${API_BASE}/auth/login`, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body:    JSON.stringify(payload)
            });

            let data;
            try {
                data = await res.json();
            } catch (jsonErr) {
                data = { status: 'error', message: `Server error (${res.status}). Please try again.` };
            }

            if (res.ok && data.status === 'success') {
                playSound('beep');
                lastGeneratedOTP = data.otp || '';

                // Display system-generated OTP on screen
                document.getElementById('generated-otp-display').textContent = lastGeneratedOTP || '------';
                document.getElementById('otp-phone-text').textContent = `Verification code generated for ${phone}`;

                // Auto-fill OTP input field for 1-click verification
                document.getElementById('cust-otp-input').value = lastGeneratedOTP;

                document.getElementById('cust-auth').classList.add('hidden');
                document.getElementById('cust-otp').classList.remove('hidden');
                hideOtpError();
            } else if (res.status === 404 || data.code === 'ACCOUNT_NOT_FOUND') {
                showAuthError(
                    data.message || `No registered account found for ${phone}. Please click Register below.`,
                    'danger',
                    'Switch to Registration →',
                    () => {
                        switchAuthTab('register');
                        const phoneInput = document.getElementById('cust-phone-input');
                        if (phoneInput) phoneInput.value = phone;
                        const nameInput = document.getElementById('cust-name-input');
                        if (nameInput) nameInput.focus();
                    }
                );
            } else if (res.status === 422 && data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                const firstMsg = data.errors[firstKey][0];
                showAuthError(firstMsg || data.message || 'Validation failed. Please check input values.', 'danger');
            } else {
                showAuthError(data.message || 'Failed to generate verification code. Please check your details.', 'danger');
            }
        } catch (e) {
            console.error('Auth sendOTP exception:', e);
            showAuthError('Unable to connect to authentication server. Please check your network connection.', 'danger');
        } finally {
            if (btn) {
                btn.disabled = false;
                if (btnText) {
                    btnText.textContent = currentAuthMode === 'login' ? 'Generate Login OTP' : 'Generate Registration OTP';
                }
            }
        }
    }

    async function verifyOTP() {
        hideOtpError();

        const phone = document.getElementById('cust-phone-input').value.trim();
        const code  = document.getElementById('cust-otp-input').value.trim();
        const name  = document.getElementById('cust-name-input').value.trim();
        const email = document.getElementById('cust-email-input').value.trim();

        if (!code || code.length < 6) {
            showOtpError("Please enter the complete 6-digit verification code.");
            return;
        }

        const btn = document.getElementById('cust-otp-verify-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin text-xs"></i> <span>Verifying...</span>`;
        }

        try {
            const payload = { phone, code };
            if (name) payload.name = name;
            if (email) payload.email = email;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const res = await fetch(`${API_BASE}/auth/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            let data;
            try {
                data = await res.json();
            } catch (jsonErr) {
                data = { status: 'error', message: `Server error (${res.status}). Please try again.` };
            }

            if (res.ok && data.status === 'success') {
                playSound('success');
                currentUser = data.user;
                localStorage.setItem('justfeast_client_user', JSON.stringify({ ...currentUser, __token: data.token }));

                updateAuthHeader();
                closeAuthModal();

                showToast(`Welcome back, ${currentUser.name}! You are signed in.`, 'success');
            } else if (res.status === 422 && data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                const firstMsg = data.errors[firstKey][0];
                showOtpError(firstMsg || data.message || 'Verification failed.');
            } else {
                showOtpError(data.message || 'Verification failed. Please check the code.');
            }
        } catch (e) {
            console.error('Auth verifyOTP exception:', e);
            showOtpError('Network error connecting to authentication server.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="fas fa-check-circle text-xs"></i> <span>Verify Code & Login</span>`;
            }
        }
    }

    function autoFillOTP() {
        const input = document.getElementById('cust-otp-input');
        if (input && lastGeneratedOTP) {
            input.value = lastGeneratedOTP;
            playSound('beep');
        }
    }

    function openAuthModal(defaultMode = 'login') {
        switchAuthTab(defaultMode);
        document.getElementById('auth-modal-overlay').classList.remove('hidden');
        document.getElementById('cust-auth').classList.remove('hidden');
        document.getElementById('cust-otp').classList.add('hidden');
    }

    function closeAuthModal() {
        document.getElementById('auth-modal-overlay').classList.add('hidden');
    }

    function showAuthScreen() {
        document.getElementById('cust-otp').classList.add('hidden');
        document.getElementById('cust-auth').classList.remove('hidden');
    }

    function setSeatingMode(mode) {
        activeSeatingMode = mode;
        const tabSeat = document.getElementById('modal-tab-seat');
        const tabGps = document.getElementById('modal-tab-gps');
        const viewSeat = document.getElementById('modal-seat-selector-view');
        const viewGps = document.getElementById('modal-gps-selector-view');

        if (mode === 'seat') {
            tabSeat.className = "flex-1 py-2 rounded-lg text-center bg-brand-rose text-white shadow shadow-brand-rose/25 transition-all";
            tabGps.className = "flex-1 py-2 rounded-lg text-center text-zinc-450 hover:text-zinc-800 transition-all";
            viewSeat.classList.remove('hidden');
            viewGps.classList.add('hidden');
        } else {
            tabGps.className = "flex-1 py-2 rounded-lg text-center bg-brand-rose text-white shadow shadow-brand-rose/25 transition-all";
            tabSeat.className = "flex-1 py-2 rounded-lg text-center text-zinc-450 hover:text-zinc-800 transition-all";
            viewSeat.classList.add('hidden');
            viewGps.classList.remove('hidden');
            initLeafletMap();
        }
    }

    function initLeafletMap() {
        setTimeout(() => {
            const center = (selectedSeat && selectedSeat.type === 'gps' && selectedSeat.latitude)
                ? [selectedSeat.latitude, selectedSeat.longitude]
                : [-1.28817042, 36.81647301];

            if (selectedSeat && selectedSeat.type === 'gps') {
                const latInput = document.getElementById('gps-lat-input');
                const lngInput = document.getElementById('gps-lng-input');
                const descInput = document.getElementById('gps-desc-input');
                if (latInput) latInput.value = selectedSeat.latitude.toFixed(8);
                if (lngInput) lngInput.value = selectedSeat.longitude.toFixed(8);
                if (descInput && selectedSeat.description) descInput.value = selectedSeat.description;
            }

            if (!leafletMap) {
                leafletMap = L.map('modal-leaflet-map').setView(center, 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(leafletMap);

                leafletMarker = L.marker(center, {draggable: true}).addTo(leafletMap);

                leafletMarker.on('dragend', function (e) {
                    const pos = leafletMarker.getLatLng();
                    document.getElementById('gps-lat-input').value = pos.lat.toFixed(8);
                    document.getElementById('gps-lng-input').value = pos.lng.toFixed(8);
                });

                leafletMap.on('click', function (e) {
                    leafletMarker.setLatLng(e.latlng);
                    document.getElementById('gps-lat-input').value = e.latlng.lat.toFixed(8);
                    document.getElementById('gps-lng-input').value = e.latlng.lng.toFixed(8);
                });
            } else {
                leafletMarker.setLatLng(center);
                leafletMap.setView(center, 16);
                leafletMap.invalidateSize();
            }
        }, 150);
    }

    function getCurrentGPSLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    document.getElementById('gps-lat-input').value = lat.toFixed(8);
                    document.getElementById('gps-lng-input').value = lng.toFixed(8);

                    if (leafletMap && leafletMarker) {
                        const newLatLng = new L.LatLng(lat, lng);
                        leafletMarker.setLatLng(newLatLng);
                        leafletMap.setView(newLatLng, 17);
                    }
                    playSound('success');
                },
                (err) => {
                    showToast("Error getting location: " + err.message + ". Center map marker used instead.", "warning");
                }
            );
        } else {
            showToast("Geolocation is not supported by this browser.", "warning");
        }
    }

    function openSeatModal() {
        document.getElementById('stadium-modal-overlay').classList.remove('hidden');
        if (activeSeatingMode === 'gps') {
            initLeafletMap();
        }
    }

    function closeSeatModal() {
        document.getElementById('stadium-modal-overlay').classList.add('hidden');
    }

    function selectSectionSvg(sec) {
        playSound('beep');
        document.getElementById('seat-sec-input').value = sec;
        const ids = ['svg-vip-a', 'svg-vip-b', 'svg-gen-a', 'svg-gen-b'];
        ids.forEach(id => document.getElementById(id).classList.remove('fill-brand-rose/40', 'fill-brand-orange/40'));

        let active = '';
        if (sec.includes('VIP Section A')) active = 'svg-vip-a';
        else if (sec.includes('VIP Section B')) active = 'svg-vip-b';
        else if (sec.includes('General Admission A')) active = 'svg-gen-a';
        else if (sec.includes('General Admission B')) active = 'svg-gen-b';

        if (active) {
            const color = active.includes('vip') ? 'fill-brand-rose/40' : 'fill-brand-orange/40';
            document.getElementById(active).classList.add(color);
        }
    }

    function saveSeatCoordinates() {
        const sec = document.getElementById('seat-sec-input').value;
        const row = document.getElementById('seat-row-input').value;
        const seat = document.getElementById('seat-num-input').value;
        selectedSeat = {type: 'seat', section: sec, row, seat};
        localStorage.setItem('justfeast_selected_seat', JSON.stringify(selectedSeat));

        document.getElementById('selected-seat-label').textContent = sec;
        document.getElementById('selected-seat-sub').textContent = `${row} — ${seat}`;
        document.getElementById('seat-status-pill').textContent = "Configured";
        document.getElementById('seat-status-pill').className = "text-[9px] bg-brand-emerald/20 text-brand-emerald px-2.5 py-0.5 rounded-full font-bold border border-brand-emerald/30";

        const locationText = `${sec}, ${row}, ${seat}`;
        document.getElementById('cart-location-text').textContent = locationText;
        document.getElementById('desktop-cart-location-text').textContent = locationText;
        document.getElementById('selected-seat-hero').textContent = locationText;
        playSound('success');
        closeSeatModal();
    }

    function saveGPSCoordinates() {
        const lat = parseFloat(document.getElementById('gps-lat-input').value);
        const lng = parseFloat(document.getElementById('gps-lng-input').value);
        const desc = document.getElementById('gps-desc-input').value.trim();

        selectedSeat = {
            type: 'gps',
            latitude: lat,
            longitude: lng,
            description: desc
        };

        localStorage.setItem('justfeast_selected_seat', JSON.stringify(selectedSeat));

        document.getElementById('selected-seat-label').textContent = "GPS Location Pin";
        document.getElementById('selected-seat-sub').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}${desc ? ' — ' + desc : ''}`;
        document.getElementById('seat-status-pill').textContent = "Configured";
        document.getElementById('seat-status-pill').className = "text-[9px] bg-brand-emerald/20 text-brand-emerald px-2.5 py-0.5 rounded-full font-bold border border-brand-emerald/30";

        const locationText = `GPS Pin: ${desc || (lat.toFixed(4) + ', ' + lng.toFixed(4))}`;
        document.getElementById('cart-location-text').textContent = locationText;
        document.getElementById('desktop-cart-location-text').textContent = locationText;
        document.getElementById('selected-seat-hero').textContent = locationText;

        playSound('success');
        closeSeatModal();
    }

    // ─── Payment Integration Flow (M-Pesa STK Push & LOOP Paybill) ────────────
    let currentOrderId = null;       // order ID pending payment
    let paymentPollTimer = null;     // polling interval for payment confirmation
    let activePaymentMethod = 'mpesa'; // 'mpesa', 'intasend', or 'loop'

    function switchPaymentMethodTab(method) {
        activePaymentMethod = method;
        const mpesaTab    = document.getElementById('pay-tab-mpesa');
        const intasendTab = document.getElementById('pay-tab-intasend');
        const loopTab     = document.getElementById('pay-tab-loop');
        const mpesaState    = document.getElementById('mpesa-input-state');
        const intasendState = document.getElementById('intasend-input-state');
        const loopState     = document.getElementById('loop-input-state');

        // Reset all tab styling
        if (mpesaTab)    mpesaTab.className    = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 text-slate-500 hover:text-slate-800 cursor-pointer truncate';
        if (intasendTab) intasendTab.className = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 text-slate-500 hover:text-slate-800 cursor-pointer truncate';
        if (loopTab)     loopTab.className     = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 text-slate-500 hover:text-slate-800 cursor-pointer truncate';

        // Reset state visibility
        if (mpesaState)    mpesaState.classList.add('hidden');
        if (intasendState) intasendState.classList.add('hidden');
        if (loopState)     loopState.classList.add('hidden');

        if (method === 'loop') {
            if (loopTab)   loopTab.className = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 bg-[#5B21B6] text-white shadow-xs cursor-pointer truncate';
            if (loopState) loopState.classList.remove('hidden');
            fetchLoopPaybillInstructions();
        } else if (method === 'intasend') {
            if (intasendTab)   intasendTab.className = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 bg-[#05A357] text-white shadow-xs cursor-pointer truncate';
            if (intasendState) intasendState.classList.remove('hidden');
        } else {
            if (mpesaTab)   mpesaTab.className = 'py-2 px-2 rounded-xl text-[11px] font-black transition-all flex items-center justify-center gap-1 bg-white text-[#0F172A] shadow-xs cursor-pointer truncate';
            if (mpesaState) mpesaState.classList.remove('hidden');
        }
    }

    function copyToClipboard(text, btnId) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById(btnId);
            if (btn) {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-emerald-600 text-[9px]"></i> Copied!';
                setTimeout(() => { btn.innerHTML = originalHTML; }, 2000);
            }
            showToast("Copied to clipboard!", "info");
        }).catch(err => {
            showToast("Copied!", "info");
        });
    }

    function checkoutOrder() {
        if (basket.length === 0) {
            showToast("Please add items to your basket first!", "warning");
            return;
        }
        if (!selectedSeat) {
            showToast("Please configure your delivery location on the map first!", "warning");
            openSeatModal();
            return;
        }
        if (!currentUser) {
            openAuthModal();
            return;
        }

        let subtotal = 0;
        basket.forEach(i => subtotal += i.price * i.quantity);

        const uniqueVendors = new Set(basket.map(i => i.vendorId).filter(Boolean));
        const vendorCount   = Math.max(1, uniqueVendors.size);
        const baseFee       = typeof systemDeliveryFee !== 'undefined' ? systemDeliveryFee : 30;
        const extraFee      = baseFee * 0.5;
        const effectiveDeliveryFee = baseFee + (vendorCount - 1) * extraFee;
        const grandTotal     = subtotal + effectiveDeliveryFee;

        document.getElementById('mpesa-amount-display').textContent = `Ksh ${grandTotal.toLocaleString()}`;

        // Pre-fill user's registered phone number if present
        const phoneInput = document.getElementById('mpesa-phone-input');
        if (phoneInput) {
            phoneInput.value = currentUser.phone || '';
        }
        const intasendPhoneInput = document.getElementById('intasend-phone-input');
        if (intasendPhoneInput) {
            intasendPhoneInput.value = currentUser.phone || '';
        }
        const loopPhoneInput = document.getElementById('loop-phone-input');
        if (loopPhoneInput) {
            loopPhoneInput.value = currentUser.phone || '';
        }

        switchPaymentMethodTab('mpesa');
        showMpesaState('input');
        document.getElementById('mpesa-payment-overlay').classList.remove('hidden');
    }

    function showMpesaState(state) {
        ['input', 'loading', 'pending', 'error'].forEach(s => {
            const el = document.getElementById(`mpesa-${s}-state`);
            if (el) el.classList.add('hidden');
        });
        const loopInput = document.getElementById('loop-input-state');
        if (loopInput) loopInput.classList.add('hidden');
        const intasendInput = document.getElementById('intasend-input-state');
        if (intasendInput) intasendInput.classList.add('hidden');

        const target = document.getElementById(`mpesa-${state}-state`);
        if (target && state !== 'input') {
            target.classList.remove('hidden');
        } else if (state === 'input') {
            switchPaymentMethodTab(activePaymentMethod);
        }

        if (state === 'input' || state === 'error') {
            currentOrderId = null;
        }
    }

    function closeMpesaOverlay() {
        document.getElementById('mpesa-payment-overlay').classList.add('hidden');
        if (paymentPollTimer) clearInterval(paymentPollTimer);
        paymentPollTimer = null;
        currentOrderId = null;
    }

    async function triggerMpesaStkPush() {
        const phone = document.getElementById('mpesa-phone-input').value.trim();
        if (!phone) {
            showToast("Please enter a valid M-Pesa phone number!", "warning");
            return;
        }

        showMpesaState('loading');
        document.getElementById('mpesa-loading-msg').textContent = "Sending STK Push prompt to your phone...";

        try {
            // Step 1: Create Order if not already created
            if (!currentOrderId) {
                const payload = {
                    vendor_id:     basket[0].vendorId,
                    seat_location: selectedSeat,
                    items:         basket.map(i => ({product_id: i.id, quantity: i.quantity}))
                };

                const orderRes = await authFetch(`${API_BASE}/orders`, {
                    method:  'POST',
                    headers: {'Content-Type': 'application/json'},
                    body:    JSON.stringify(payload)
                });
                const orderData = await orderRes.json();

                if (!orderRes.ok) {
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent = orderData.message || 'Failed to create order.';
                    return;
                }
                currentOrderId = orderData.order.id;
            }

            // Step 2: Trigger STK Push API call
            const payRes = await authFetch(`${API_BASE}/orders/${currentOrderId}/pay`, {
                method:  'POST',
                headers: {'Content-Type': 'application/json'},
                body:    JSON.stringify({ phone })
            });
            const payData = await payRes.json();

            if (!payRes.ok || payData.status === 'error') {
                currentOrderId = null;
                showMpesaState('error');
                document.getElementById('mpesa-error-msg').textContent = payData.message || 'Failed to trigger M-Pesa STK Push.';
                return;
            }

            // Step 3: STK Push initiated successfully -> Transition to pending prompt state
            playSound('beep');
            showMpesaState('pending');
            document.getElementById('mpesa-prompt-phone-label').textContent = `M-Pesa PIN prompt sent to ${phone}. Enter PIN to confirm.`;

            // Step 4: Poll payment status until confirmed
            confirmOrderFromMpesa(currentOrderId);

        } catch (e) {
            currentOrderId = null;
            showMpesaState('error');
            document.getElementById('mpesa-error-msg').textContent = 'Network error during payment initiation. Please try again.';
        }
    }

    async function confirmOrderFromMpesa(orderId) {
        if (paymentPollTimer) clearInterval(paymentPollTimer);

        let attempts = 0;
        const maxAttempts = 25; // 25 × 3s = 75s max poll duration

        paymentPollTimer = setInterval(async () => {
            attempts++;
            try {
                const res = await authFetch(`${API_BASE}/orders/${orderId}/payment-status?attempt=${attempts}`);
                const data = await res.json();

                if (data.payment_status === 'paid') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    playSound('success');

                    // Fetch complete order details and transition to radar tracker
                    const orderRes = await authFetch(`${API_BASE}/orders/${orderId}`);
                    const orderData = await orderRes.json();
                    activeOrder = { order: orderData };
                    basket = [];
                    renderBasket();

                    closeMpesaOverlay();
                    currentOrderId = null;
                    document.getElementById('cust-main').classList.add('hidden');
                    document.getElementById('cust-tracker').classList.remove('hidden');
                    updateRadarUI(orderData);
                    syncActiveOrder();

                } else if (data.payment_status === 'failed') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');

                    let errMsg = data.mpesa_result_desc || 'M-Pesa payment was cancelled or failed.';
                    const codeStr = String(data.mpesa_result_code || '');

                    if (codeStr === '17') {
                        errMsg = 'M-Pesa Error (Code 17): The phone number entered is invalid or not an active M-Pesa registered line. Please check the phone number and try again.';
                    } else if (codeStr === '1032') {
                        errMsg = 'M-Pesa payment prompt was cancelled on phone.';
                    } else if (codeStr === '1031') {
                        errMsg = 'M-Pesa payment prompt timed out without PIN entry.';
                    } else if (codeStr === '2001') {
                        errMsg = 'Invalid M-Pesa PIN entered.';
                    }

                    document.getElementById('mpesa-error-msg').textContent = errMsg;

                } else if (attempts >= maxAttempts) {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent =
                        'M-Pesa confirmation timed out. If you entered your PIN, your order will update automatically.';
                }
            } catch (e) {
                // Ignore transient network hiccups while polling
            }
        }, 3000);
    }

    async function triggerIntaSendPayment() {
        const phone = document.getElementById('intasend-phone-input').value.trim();
        if (!phone) {
            showToast("Please enter a valid M-Pesa phone number for IntaSend!", "warning");
            return;
        }

        showMpesaState('loading');
        document.getElementById('mpesa-loading-msg').textContent = "Initiating IntaSend STK Push prompt...";

        try {
            // Step 1: Create Order if not already created
            if (!currentOrderId) {
                const payload = {
                    vendor_id:      basket[0].vendorId,
                    seat_location:  selectedSeat,
                    payment_method: 'intasend',
                    items:          basket.map(i => ({product_id: i.id, quantity: i.quantity}))
                };

                const orderRes = await authFetch(`${API_BASE}/orders`, {
                    method:  'POST',
                    headers: {'Content-Type': 'application/json'},
                    body:    JSON.stringify(payload)
                });
                const orderData = await orderRes.json();

                if (!orderRes.ok) {
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent = orderData.message || 'Failed to create order.';
                    return;
                }
                currentOrderId = orderData.order.id;
            }

            // Step 2: Trigger IntaSend STK Push API call
            const payRes = await authFetch(`${API_BASE}/orders/${currentOrderId}/pay/intasend`, {
                method:  'POST',
                headers: {'Content-Type': 'application/json'},
                body:    JSON.stringify({ phone, payment_method: 'intasend' })
            });
            const payData = await payRes.json();

            if (!payRes.ok || payData.status === 'error') {
                currentOrderId = null;
                showMpesaState('error');
                document.getElementById('mpesa-error-msg').textContent = payData.message || 'Failed to trigger IntaSend payment.';
                return;
            }

            // Step 3: IntaSend STK Push initiated successfully -> Transition to pending prompt state
            playSound('beep');
            showMpesaState('pending');
            document.getElementById('mpesa-prompt-phone-label').textContent = `IntaSend STK prompt sent to ${phone}. Enter PIN to confirm.`;

            // Step 4: Poll IntaSend payment status until confirmed
            confirmOrderFromIntaSend(currentOrderId);

        } catch (e) {
            currentOrderId = null;
            showMpesaState('error');
            document.getElementById('mpesa-error-msg').textContent = 'Network error during IntaSend payment initiation. Please try again.';
        }
    }

    async function confirmOrderFromIntaSend(orderId) {
        if (paymentPollTimer) clearInterval(paymentPollTimer);

        let attempts = 0;
        const maxAttempts = 25; // 25 × 3s = 75s max poll duration

        paymentPollTimer = setInterval(async () => {
            attempts++;
            try {
                const res = await authFetch(`${API_BASE}/orders/${orderId}/intasend-status?attempt=${attempts}`);
                const data = await res.json();

                if (data.payment_status === 'paid') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    playSound('success');

                    const orderRes = await authFetch(`${API_BASE}/orders/${orderId}`);
                    const orderData = await orderRes.json();
                    activeOrder = { order: orderData };
                    basket = [];
                    renderBasket();

                    closeMpesaOverlay();
                    currentOrderId = null;
                    document.getElementById('cust-main').classList.add('hidden');
                    document.getElementById('cust-tracker').classList.remove('hidden');
                    updateRadarUI(orderData);
                    syncActiveOrder();

                } else if (data.payment_status === 'failed') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent = 'IntaSend payment was cancelled or failed.';

                } else if (attempts >= maxAttempts) {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent =
                        'IntaSend confirmation timed out. If you completed payment, your order will update automatically.';
                }
            } catch (e) {
                // Ignore transient network errors during polling
            }
        }, 3000);
    }

    function toggleLoopClaimBox() {
        const box = document.getElementById('loop-claim-box');
        const chevron = document.getElementById('loop-claim-chevron');
        if (box) {
            box.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }
    }

    async function fetchLoopPaybillInstructions() {
        if (basket.length === 0) return;

        try {
            // Step 1: Create Order if not created yet
            if (!currentOrderId) {
                const payload = {
                    vendor_id:     basket[0].vendorId,
                    seat_location: selectedSeat,
                    items:         basket.map(i => ({product_id: i.id, quantity: i.quantity}))
                };

                const orderRes = await authFetch(`${API_BASE}/orders`, {
                    method:  'POST',
                    headers: {'Content-Type': 'application/json'},
                    body:    JSON.stringify(payload)
                });
                const orderData = await orderRes.json();

                if (!orderRes.ok) {
                    currentOrderId = null;
                    showToast(orderData.message || 'Failed to create order.', 'error');
                    return;
                }
                currentOrderId = orderData.order.id;
            }

            // Step 2: Fetch Paybill instructions & account reference for currentOrderId
            const res = await authFetch(`${API_BASE}/orders/${currentOrderId}/pay/loop`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'}
            });
            const data = await res.json();

            if (res.ok && data.status === 'success') {
                if (document.getElementById('loop-display-paybill')) document.getElementById('loop-display-paybill').textContent = data.paybill_number || '600100';
                if (document.getElementById('loop-display-acc-ref')) document.getElementById('loop-display-acc-ref').textContent = data.account_reference || '...';
                if (document.getElementById('loop-step-acc-ref')) document.getElementById('loop-step-acc-ref').textContent = data.account_reference || '...';

                let total = 0;
                basket.forEach(i => total += i.price * i.quantity);
                const baseFee = typeof systemDeliveryFee !== 'undefined' ? systemDeliveryFee : 30;
                const uniqueVendors = new Set(basket.map(i => i.vendorId).filter(Boolean));
                const grandTotal = total + (baseFee + (Math.max(1, uniqueVendors.size) - 1) * (baseFee * 0.5));

                if (document.getElementById('loop-display-amount')) document.getElementById('loop-display-amount').textContent = `KES ${grandTotal.toLocaleString()}`;
            }
        } catch (e) {
            console.error("LOOP Instructions Error:", e);
        }
    }

    async function submitLoopPaymentClaim() {
        const receipt = document.getElementById('loop-receipt-input').value.trim();
        if (!receipt) {
            showToast("Please enter your M-Pesa receipt code!", "warning");
            return;
        }

        showMpesaState('loading');
        document.getElementById('mpesa-loading-msg').textContent = "Submitting receipt for verification with LOOP logs...";

        try {
            const res = await authFetch(`${API_BASE}/orders/${currentOrderId}/pay/loop/claim`, {
                method:  'POST',
                headers: {'Content-Type': 'application/json'},
                body:    JSON.stringify({ mpesa_receipt: receipt })
            });
            const data = await res.json();

            if (!res.ok || data.status === 'error') {
                showMpesaState('error');
                document.getElementById('mpesa-error-msg').textContent = data.message || 'Failed to submit payment claim.';
                return;
            }

            playSound('beep');
            showMpesaState('pending');
            document.getElementById('mpesa-prompt-phone-label').textContent = `Receipt ${receipt.toUpperCase()} submitted! Verifying transaction against LOOP transaction log...`;

            confirmOrderFromLoop(currentOrderId);
        } catch (e) {
            showMpesaState('error');
            document.getElementById('mpesa-error-msg').textContent = 'Network error while submitting payment claim.';
        }
    }

    async function confirmOrderFromLoop(orderId) {
        if (paymentPollTimer) clearInterval(paymentPollTimer);

        let attempts = 0;
        const maxAttempts = 20;

        paymentPollTimer = setInterval(async () => {
            attempts++;
            try {
                const res = await authFetch(`${API_BASE}/orders/${orderId}/loop-status?attempt=${attempts}`);
                const data = await res.json();

                if (data.payment_status === 'paid') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    playSound('success');

                    const orderRes = await authFetch(`${API_BASE}/orders/${orderId}`);
                    const orderData = await orderRes.json();
                    activeOrder = { order: orderData };
                    basket = [];
                    renderBasket();

                    closeMpesaOverlay();
                    currentOrderId = null;
                    document.getElementById('cust-main').classList.add('hidden');
                    document.getElementById('cust-tracker').classList.remove('hidden');
                    updateRadarUI(orderData);
                    syncActiveOrder();

                } else if (data.payment_status === 'failed') {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent = data.provider_message || 'LOOP Paybill payment was declined or failed.';

                } else if (attempts >= maxAttempts) {
                    clearInterval(paymentPollTimer);
                    paymentPollTimer = null;
                    currentOrderId = null;
                    showMpesaState('error');
                    document.getElementById('mpesa-error-msg').textContent =
                        'LOOP Paybill status confirmation timed out. Your order status will update once completed.';
                }
            } catch (e) {
                // Ignore transient network hiccups
            }
        }, 3000);
    }

    async function checkActiveOrderOnLogin() {
        if (!currentUser) return;
        try {
            const res = await authFetch(`${API_BASE}/orders/active`);
            if (res.ok) {
                const data = await res.json();
                const order = data.order;
                if (order && order.id) {
                    activeOrder = {order: order};
                    document.getElementById('cust-main').classList.add('hidden');
                    document.getElementById('cust-tracker').classList.remove('hidden');
                    updateRadarUI(order);
                }
            }
        } catch (e) {
        }
    }

    async function syncActiveOrder() {
        if (!activeOrder || !activeOrder.order) return;
        try {
            const res = await authFetch(`${API_BASE}/orders/${activeOrder.order.id}`);
            if (res.ok) {
                const updated = await res.json();
                updateRadarUI(updated);
            }
        } catch (e) {
        }
    }

    function updateRadarUI(order) {
        const status = order.order_status;
        if (order.delivery) {
            document.getElementById('tracker-pin').textContent = order.delivery.verification_pin;

            if (order.delivery.arrived_at) {
                // Show QR Code container
                const qrContainer = document.getElementById('tracker-qr-container');
                if (qrContainer.classList.contains('hidden')) {
                    qrContainer.classList.remove('hidden');
                    // Generate QR Code dynamically
                    const canvas = document.getElementById('tracker-qr-canvas');
                    new QRious({
                        element: canvas,
                        value: `justfeast-delivery-verify:${order.delivery.id}:${order.delivery.verification_pin}`,
                        size: 128
                    });
                }

                // Play beep alert if not beeped yet
                if (!hasBeepedForArrival) {
                    hasBeepedForArrival = true;
                    playSound('alert');
                    setTimeout(() => playSound('beep'), 300);
                    setTimeout(() => playSound('success'), 600);
                }
            } else {
                document.getElementById('tracker-qr-container').classList.add('hidden');
            }
        } else {
            document.getElementById('tracker-qr-container').classList.add('hidden');
        }

        const stepCreated = document.getElementById('step-created');
        const stepPreparing = document.getElementById('step-preparing');
        const stepReady = document.getElementById('step-ready');
        const stepEnroute = document.getElementById('step-enroute');

        [stepCreated, stepPreparing, stepReady, stepEnroute].forEach(el => {
            el.children[0].className = "w-5 h-5 rounded-full bg-white border border-[#E7E8DD] text-zinc-450 flex items-center justify-center text-[10px] font-bold";
            el.children[1].className = "text-xs font-semibold text-zinc-400";
        });

        if (status === 'accepted') {
            stepCreated.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
            stepCreated.children[0].innerHTML = `<i class="fas fa-check"></i>`;
            stepCreated.children[1].className = "text-xs font-black text-zinc-850";
        } else if (status === 'preparing') {
            [stepCreated, stepPreparing].forEach((el, idx) => {
                el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                el.children[0].innerHTML = `<i class="fas fa-${idx === 0 ? 'check' : 'spinner animate-spin'}"></i>`;
                el.children[1].className = "text-xs font-black text-zinc-850";
            });
        } else if (status === 'runner_assigned' || status === 'ready') {
            [stepCreated, stepPreparing, stepReady].forEach((el, idx) => {
                el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                el.children[0].innerHTML = `<i class="fas fa-${idx < 2 ? 'check' : 'bell animate-bounce'}"></i>`;
                el.children[1].className = "text-xs font-black text-zinc-850";
            });
        } else if (status === 'en_route') {
            [stepCreated, stepPreparing, stepReady, stepEnroute].forEach((el, idx) => {
                el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                el.children[0].innerHTML = `<i class="fas fa-${idx < 3 ? 'check' : 'truck animate-pulse'}"></i>`;
                el.children[1].className = "text-xs font-black text-zinc-850";
            });
        } else if (status === 'delivered') {
            playSound('success');
            showToast("🎉 Order successfully delivered to your seat! Enjoy the concert!", "success");
            resetTrackerDemo();
        }
    }

    function resetTrackerDemo() {
        activeOrder = null;
        hasBeepedForArrival = false;
        document.getElementById('tracker-qr-container').classList.add('hidden');
        document.getElementById('cust-tracker').classList.add('hidden');
        document.getElementById('cust-main').classList.remove('hidden');
    }

    // PWA Setup
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        document.getElementById('pwa-install-banner').classList.remove('hidden');
    });

    function checkPWAPrompt() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => console.log('SW Registered', reg))
                .catch((err) => console.log('SW Registration Failed', err));
        }
    }

    function installPWA() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((res) => {
                deferredPrompt = null;
                document.getElementById('pwa-install-banner').classList.add('hidden');
            });
        }
    }

    function dismissPWABanner() {
        document.getElementById('pwa-install-banner').classList.add('hidden');
    }
</script>

<script>
    // Premium marketplace card rendering override. Keeps the original API and basket logic intact.
    function setCategory(cat) {
        playSound('beep');
        selectedCategory = cat;
        ['all', 'food', 'drinks', 'snacks'].forEach(c => {
            const btn = document.getElementById(`cat-${c}`);
            if (!btn) return;
            btn.classList.toggle('category-active', c === cat);
        });
        renderVendors();
    }

    let activeVendorId = null;

    function selectVendor(vendorId) {
        playSound('beep');
        activeVendorId = vendorId;
        renderVendors();
        const el = document.getElementById('vendors');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    }

    function renderVendors() {
        const container = document.getElementById('vendor-list-container');
        if (!container) return;
        container.innerHTML = '';

        const searchEl = document.getElementById('menu-search');
        const searchVal = searchEl ? searchEl.value.trim().toLowerCase() : '';
        const isSearching = searchVal.length > 0;
        const featContainer = document.getElementById('featured-food-container');

        if (featContainer) {
            featContainer.style.display = isSearching ? 'none' : 'block';
        }

        // 1. Render Vendor Filter Chips Bar at top of marketplace
        let chipsHtml = `
            <div class="flex items-center gap-2.5 pb-2 overflow-x-auto no-scrollbar scroll-smooth">
                <button onclick="selectVendor(null)"
                        class="px-4 py-2 rounded-full text-xs font-black transition-all cursor-pointer whitespace-nowrap border ${activeVendorId === null ? 'bg-[#05A357] text-white border-[#05A357] shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:border-[#05A357]'}">
                    <i class="fas fa-store mr-1"></i> All Vendors (${vendors.length})
                </button>
        `;

        vendors.forEach((v, idx) => {
            let chipIcon = '';
            if (v.logo_url && (v.logo_url.startsWith('/') || v.logo_url.startsWith('http'))) {
                chipIcon = `<img src="${v.logo_url}" class="w-4 h-4 rounded-full object-cover inline-block shrink-0" alt="${v.business_name}">`;
            } else {
                const emoji = v.logo_url || ['🍔', '🥤', '🍿', '🌮', '🍟'][idx % 5];
                chipIcon = `<span class="text-sm">${emoji}</span>`;
            }

            const isSelected = activeVendorId === v.id;
            chipsHtml += `
                <button onclick="selectVendor(${v.id})"
                        class="px-4 py-2 rounded-full text-xs font-black transition-all cursor-pointer whitespace-nowrap border inline-flex items-center gap-1.5 ${isSelected ? 'bg-[#05A357] text-white border-[#05A357] shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:border-[#05A357]'}">
                    ${chipIcon} <span>${v.business_name}</span>
                </button>
            `;
        });
        chipsHtml += `</div>`;

        // 2. SEARCH MODE: Show product matches directly with vendor badges
        if (isSearching) {
            container.innerHTML = chipsHtml;
            let totalFound = 0;
            let productGrid = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 sm:gap-4 mt-4">';

            vendors.forEach((v, vIdx) => {
                (v.products || []).forEach((p, pIdx) => {
                    const desc = (p.description || '').toLowerCase();
                    const name = (p.name || '').toLowerCase();
                    const category = getProductCategory(p).toLowerCase();
                    const dbCategory = (p.category || '').toLowerCase();
                    const vName = (v.business_name || '').toLowerCase();

                    const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

                    const matchesSearch = name.includes(searchVal) ||
                                          desc.includes(searchVal) ||
                                          category.includes(searchVal) ||
                                          dbCategory.includes(searchVal) ||
                                          vName.includes(searchVal) ||
                                          (searchVal === 'meals' && category === 'food') ||
                                          (searchVal === 'meal' && category === 'food') ||
                                          (searchVal === 'drinks' && category === 'drinks') ||
                                          (searchVal === 'drink' && category === 'drinks') ||
                                          (searchVal === 'snacks' && category === 'snacks') ||
                                          (searchVal === 'snack' && category === 'snacks');

                    if (matchesSearch && matchesCategory) {
                        totalFound++;
                        const out = p.stock_status !== 'in_stock';
                        const safeName = String(p.name || '').replace(/'/g, "\\'");
                        let visual = p.image_url && p.image_url.startsWith('/')
                            ? `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">`
                            : `<div class="w-full h-full bg-gradient-to-br from-[#FFF8E7] via-white to-[#E9F7EE] flex items-center justify-center"><span class="text-6xl drop-shadow-sm">${category === 'drinks' ? '🥤' : category === 'snacks' ? '🍿' : '🍔'}</span></div>`;

                        productGrid += `
                            <article class="group bg-white rounded-[30px] border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition duration-300 flex flex-col">
                                <div class="relative h-44 overflow-hidden bg-slate-50">
                                    ${visual}
                                    <span class="absolute top-3 left-3 bg-white/95 backdrop-blur text-[9px] font-black text-[#0F172A] px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200">${category}</span>
                                    <span class="absolute bottom-3 left-3 bg-[#05A357] text-white text-[9px] font-black px-2.5 py-1 rounded-full shadow-sm"><i class="fas fa-shop mr-1"></i> ${v.business_name}</span>
                                </div>
                                <div class="p-4 flex-1 flex flex-col justify-between gap-4">
                                    <div>
                                        <h4 class="text-sm font-black tracking-tight text-[#0F172A] group-hover:text-[#05A357] ${out ? 'line-through text-slate-400' : ''}">${p.name}</h4>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <div><p class="text-[9px] uppercase tracking-wider text-slate-400 font-black">Price</p><p class="text-lg font-black text-[#05A357]">Ksh ${parseFloat(p.price).toLocaleString()}</p></div>
                                        ${out ? `<span class="text-[9px] bg-slate-100 border border-slate-200 text-slate-400 px-3 py-2 rounded-full font-black">Out of stock</span>` : `<button onclick="addToBasket(${p.id}, '${safeName}', ${p.price}, ${v.id})" class="h-10 px-4 rounded-full bg-[#FFC244] hover:bg-[#05A357] text-[#0F172A] hover:text-white flex items-center justify-center font-black transition-all shadow-sm border border-[#efb52e] text-xs gap-1.5 cursor-pointer"><i class="fas fa-plus text-[10px]"></i> Add</button>`}
                                    </div>
                                </div>
                            </article>
                        `;
                    }
                });
            });

            productGrid += '</div>';

            if (totalFound > 0) {
                container.innerHTML += `<div class="mt-4 mb-2 text-xs font-black text-slate-500 uppercase tracking-wider">Search Results (${totalFound} item${totalFound === 1 ? '' : 's'} matching "${searchVal}")</div>` + productGrid;
            } else {
                container.innerHTML += `<div class="bg-white border border-slate-200 rounded-[32px] p-10 text-center shadow-sm mt-4"><div class="text-5xl mb-3">🔎</div><h3 class="text-xl font-black text-[#0F172A]">No matching items found</h3><p class="text-sm text-slate-500 font-bold mt-1">Try another search keyword or select a category.</p></div>`;
            }
            return;
        }

        // 3. VENDOR FOCUS MODE: If a specific vendor is selected
        if (activeVendorId !== null) {
            const targetVendor = vendors.find(v => v.id === activeVendorId);
            if (targetVendor) {
                const idx = vendors.findIndex(v => v.id === activeVendorId);
                let headerLogo = '';
                if (targetVendor.logo_url && (targetVendor.logo_url.startsWith('/') || targetVendor.logo_url.startsWith('http'))) {
                    headerLogo = `<img src="${targetVendor.logo_url}" class="w-full h-full object-cover rounded-[20px]" alt="${targetVendor.business_name}">`;
                } else {
                    const emoji = targetVendor.logo_url || ['🍔', '🥤', '🍿', '🌮', '🍟'][idx % 5];
                    headerLogo = `<span class="text-4xl">${emoji}</span>`;
                }

                const rating = (4.9 - (idx * 0.08)).toFixed(1);
                const etaMin = 7 + (idx % 3) * 2;
                const etaMax = etaMin + 6;
                const zone = idx % 2 === 0 ? 'Gate B' : 'VIP concourse';
                const products = (targetVendor.products || []).filter(p => selectedCategory === 'all' || getProductCategory(p) === selectedCategory);

                let headerHtml = `
                    <div class="mb-4">
                        <button onclick="selectVendor(null)" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-[#0F172A] px-4 py-2 rounded-full text-xs font-black transition mb-4 cursor-pointer">
                            <i class="fas fa-arrow-left"></i> Back to all vendors
                        </button>
                        <div class="rounded-[32px] bg-white border border-slate-200 p-5 md:p-6 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-5">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-[22px] bg-emerald-50 border border-emerald-200 shadow-inner flex items-center justify-center text-4xl shrink-0 overflow-hidden">${headerLogo}</div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-[#FFC244] text-[#0F172A] text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full">Open Now</span>
                                    </div>
                                    <h3 class="text-2xl font-black tracking-tight text-[#0F172A]">${targetVendor.business_name}</h3>
                                    <div class="flex items-center gap-3 mt-1.5 text-xs font-bold text-slate-500">
                                        <span><i class="fas fa-star text-[#FFC244]"></i> ${rating}</span>
                                        <span><i class="far fa-clock text-[#05A357]"></i> ${etaMin}-${etaMax} min</span>
                                        <span><i class="fas fa-box text-[#05A357]"></i> ${products.length} Items</span>
                                    </div>
                                </div>
                            </div>
                            <button onclick="openSeatModal()" class="bg-[#05A357] hover:bg-[#047A43] text-white rounded-full px-6 py-3 text-xs font-black shadow-md self-start md:self-auto">
                                <i class="fas fa-map-location-dot mr-1"></i> Check Seat Zone
                            </button>
                        </div>
                    </div>
                `;

                let productGrid = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 sm:gap-4">';
                products.forEach((p, pIndex) => {
                    const out = p.stock_status !== 'in_stock';
                    const safeName = String(p.name || '').replace(/'/g, "\\'");
                    const category = getProductCategory(p);
                    let visual = p.image_url && p.image_url.startsWith('/')
                        ? `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">`
                        : `<div class="w-full h-full bg-[#FFF8E7] flex items-center justify-center"><span class="text-7xl drop-shadow-sm">${category === 'drinks' ? '🥤' : category === 'snacks' ? '🍿' : '🍔'}</span></div>`;

                    productGrid += `
                        <article class="group bg-white rounded-[30px] border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition duration-300 flex flex-col">
                            <div class="relative h-44 overflow-hidden bg-slate-50">
                                ${visual}
                                <span class="absolute top-3 left-3 bg-white/95 backdrop-blur text-[9px] font-black text-[#0F172A] px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200">${category}</span>
                                <span class="absolute bottom-3 right-3 bg-[#0F172A] text-[#FFC244] text-[9.5px] font-extrabold px-3 py-1 rounded-full">Fast seat drop</span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-black tracking-tight text-[#0F172A] group-hover:text-[#05A357] ${out ? 'line-through text-slate-400' : ''}">${p.name}</h4>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div><p class="text-[9px] uppercase tracking-wider text-slate-400 font-black">Price</p><p class="text-lg font-black text-[#05A357]">Ksh ${parseFloat(p.price).toLocaleString()}</p></div>
                                    ${out ? `<span class="text-[9px] bg-slate-100 border border-slate-200 text-slate-400 px-3 py-2 rounded-full font-black">Out of stock</span>` : `<button onclick="addToBasket(${p.id}, '${safeName}', ${p.price}, ${targetVendor.id})" class="h-10 px-4 rounded-full bg-[#FFC244] hover:bg-[#05A357] text-[#0F172A] hover:text-white flex items-center justify-center font-black transition-all shadow-sm border border-[#efb52e] text-xs gap-1.5 cursor-pointer"><i class="fas fa-plus text-[10px]"></i> Add</button>`}
                                </div>
                            </div>
                        </article>
                    `;
                });
                productGrid += '</div>';

                container.innerHTML = chipsHtml + headerHtml + productGrid;
                return;
            }
        }

        // 4. OVERVIEW MODE: Render Glovo-Style Circular Bubble Cards Grid!
        const eventName = (typeof activeEvent !== 'undefined' && activeEvent && activeEvent.name) ? activeEvent.name : 'RheamFeast';
        let vendorGridHtml = `
            <div class="space-y-6 pt-4 border-t border-slate-200/80 mt-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-lg sm:text-xl md:text-2xl font-black text-[#0F172A] tracking-tight">Top restaurants and stalls in ${eventName}</h3>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-bold mt-0.5">Tap any stall bubble to explore their full menu</p>
                    </div>
                    <span class="bg-[#FFF8E7] text-[#0F172A] border border-[#F7E5B2] text-[10px] font-black uppercase px-3 py-1 rounded-full self-start sm:self-auto">
                        🏪 ${vendors.length} Verified Stalls
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6 justify-items-start py-1">
        `;

        vendors.forEach((vendor, index) => {
            const currentCat = (typeof selectedCategory !== 'undefined') ? selectedCategory : 'all';
            const products = (vendor.products || []).filter(p => currentCat === 'all' || getProductCategory(p) === currentCat);
            if (currentCat !== 'all' && !products.length) return;

            let logoContent = '';
            if (vendor.logo_url && (vendor.logo_url.startsWith('/') || vendor.logo_url.startsWith('http'))) {
                logoContent = `<img src="${vendor.logo_url}" class="w-full h-full object-cover rounded-full" alt="${vendor.business_name}">`;
            } else {
                const emoji = vendor.logo_url || ['🍔', '🥤', '🍿', '🌮', '🍟'][index % 5];
                logoContent = `<span class="text-4xl sm:text-5xl md:text-6xl drop-shadow-sm group-hover:scale-110 transition-transform duration-300">${emoji}</span>`;
            }

            const rating = (4.9 - (index * 0.08)).toFixed(1);
            const etaMin = 7 + (index % 3) * 2;
            const etaMax = etaMin + 6;

            vendorGridHtml += `
                <div onclick="selectVendor(${vendor.id})"
                     class="flex flex-col items-center group cursor-pointer text-center transition-transform">

                    <!-- Circular Glovo Bubble Avatar -->
                    <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full bg-white border-2 border-slate-200 group-hover:border-[#05A357] shadow-md group-hover:shadow-2xl transition-all duration-300 flex items-center justify-center overflow-hidden relative group-hover:scale-105">
                        <div class="w-full h-full bg-[#FFF8E7] flex items-center justify-center p-2">
                            ${logoContent}
                        </div>
                        <span class="absolute bottom-1.5 sm:bottom-2 bg-white/90 backdrop-blur text-[#0F172A] text-[8.5px] sm:text-[9.5px] font-black px-2 py-0.5 rounded-full shadow-sm border border-slate-200">
                            <i class="fas fa-star text-[#FFC244] text-[8px] sm:text-[8.5px]"></i> ${rating}
                        </span>
                    </div>

                    <!-- Label Pill underneath -->
                    <span class="mt-2.5 sm:mt-3 px-3 sm:px-3.5 py-1 bg-[#FFF8E7] group-hover:bg-[#05A357] text-[#0F172A] group-hover:text-white rounded-full text-[11px] sm:text-xs font-black transition-all shadow-sm border border-[#F7E5B2] group-hover:border-[#05A357] truncate max-w-[120px] sm:max-w-[140px]">
                        ${vendor.business_name}
                    </span>

                    <span class="text-[9.5px] sm:text-[10px] text-slate-400 font-bold mt-1">
                        <i class="far fa-clock text-[#05A357] mr-0.5"></i> ${etaMin}-${etaMax} min
                    </span>
                </div>
            `;
        });

        vendorGridHtml += `</div></div>`;
        container.innerHTML = chipsHtml + vendorGridHtml;

        // Render Featured Food & Drinks grid inside top Event Marketplace card
        renderFeaturedFoodGrid();
    }

    function renderFeaturedFoodGrid() {
        const featContainer = document.getElementById('featured-food-container');
        if (!featContainer) return;

        let foodItemsHtml = `
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-[#0F172A] tracking-tight flex items-center gap-2">
                            <span>🍱</span> Featured Food & Drinks from Vendors
                        </h3>
                        <p class="text-[11px] text-slate-500 font-bold">Popular concert meals & drinks served direct to your seat (Top 20)</p>
                    </div>
                    <span class="bg-[#FFF8E7] text-[#0F172A] border border-[#F7E5B2] text-[9.5px] font-black uppercase px-3 py-1 rounded-full hidden sm:inline-block">
                        ⚡ Express Seat Delivery
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        `;

        let featuredCount = 0;
        const MAX_ITEMS = 20;

        for (let i = 0; i < vendors.length; i++) {
            if (featuredCount >= MAX_ITEMS) break;
            const v = vendors[i];
            const prods = v.products || [];

            for (let j = 0; j < prods.length; j++) {
                if (featuredCount >= MAX_ITEMS) break;
                const p = prods[j];

                const currentCat = (typeof selectedCategory !== 'undefined') ? selectedCategory : 'all';
                const pCat = getProductCategory(p);
                if (currentCat !== 'all' && pCat !== currentCat) continue;

                featuredCount++;
                const out = p.stock_status !== 'in_stock';
                const safeName = String(p.name || '').replace(/'/g, "\\'");
                let visual = p.image_url && p.image_url.startsWith('/')
                    ? `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">`
                    : `<div class="w-full h-full bg-gradient-to-br from-[#FFF8E7] via-white to-[#E9F7EE] flex items-center justify-center"><span class="text-4xl sm:text-5xl drop-shadow-sm">${pCat === 'drinks' ? '🥤' : pCat === 'snacks' ? '🍿' : '🍔'}</span></div>`;

                foodItemsHtml += `
                    <article class="group bg-white rounded-[22px] sm:rounded-[26px] border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition duration-300 flex flex-col">
                        <div class="relative h-32 sm:h-36 md:h-40 overflow-hidden bg-slate-50">
                            ${visual}
                            <span class="absolute top-2 left-2 bg-white/95 backdrop-blur text-[8px] sm:text-[8.5px] font-black text-[#0F172A] px-2 py-0.5 rounded-full uppercase tracking-wider border border-slate-200">${pCat}</span>
                            <span class="absolute bottom-2 left-2 bg-[#05A357] text-white text-[8px] sm:text-[8.5px] font-black px-2 py-0.5 rounded-full shadow-xs truncate max-w-[85%]"><i class="fas fa-shop mr-1"></i> ${v.business_name}</span>
                        </div>
                        <div class="p-3 sm:p-3.5 flex-1 flex flex-col justify-between gap-2.5">
                            <div>
                                <h4 class="text-xs font-black tracking-tight text-[#0F172A] group-hover:text-[#05A357] line-clamp-2 ${out ? 'line-through text-slate-400' : ''}">${p.name}</h4>
                            </div>
                            <div class="flex items-center justify-between gap-1.5 pt-1">
                                <div><p class="text-[8px] uppercase tracking-wider text-slate-400 font-black">Price</p><p class="text-sm sm:text-base font-black text-[#05A357]">Ksh ${parseFloat(p.price).toLocaleString()}</p></div>
                                ${out ? `<span class="text-[8px] bg-slate-100 border border-slate-200 text-slate-400 px-2 py-1 rounded-full font-black">Out of stock</span>` : `<button onclick="addToBasket(${p.id}, '${safeName}', ${p.price}, ${v.id})" class="h-8 sm:h-9 px-3 rounded-full bg-[#FFC244] hover:bg-[#05A357] text-[#0F172A] hover:text-white flex items-center justify-center font-black transition-all shadow-xs border border-[#efb52e] text-[10px] sm:text-[11px] gap-1 cursor-pointer whitespace-nowrap"><i class="fas fa-plus text-[8px]"></i> Add</button>`}
                            </div>
                        </div>
                    </article>
                `;
            }
        }

        foodItemsHtml += `</div></div>`;

        if (featuredCount > 0) {
            featContainer.style.display = 'block';
            featContainer.innerHTML = foodItemsHtml;
        } else {
            featContainer.style.display = 'none';
        }
    }

    let systemDeliveryFee = 30;
    let systemStageConfig = {
        name: 'Main Stage Grounds',
        description: 'Uhuru Park, Cathedral Road Entrance',
        latitude: -1.28817042,
        longitude: 36.81647301
    };

    async function fetchSystemSettings() {
        try {
            const res = await fetch('/api/settings');
            if (res.ok) {
                const data = await res.json();
                if (data.delivery_fee !== undefined) {
                    systemDeliveryFee = Number(data.delivery_fee);
                    renderBasket();
                }
                if (data.stage_location) {
                    systemStageConfig = data.stage_location;
                }
            }
        } catch (e) {}
    }

    function renderBasket() {
        const mobileTray = document.getElementById('phone-cart-tray');
        const mobileContainer = document.getElementById('cart-tray-items');
        const desktopContainer = document.getElementById('desktop-cart-tray-items');
        const badgeEl = document.getElementById('basket-count-badge');
        const subtotalEl = document.getElementById('desktop-cart-subtotal');
        const feeEl = document.getElementById('desktop-cart-delivery-fee');
        const mobileSubtotalEl = document.getElementById('cart-subtotal');
        const mobileFeeEl = document.getElementById('cart-delivery-fee');

        let totalQty = 0;
        basket.forEach(i => totalQty += i.quantity);
        if (badgeEl) badgeEl.textContent = `${totalQty} item${totalQty === 1 ? '' : 's'}`;

        const hasItems = basket.length > 0;
        if (!hasItems) {
            if (mobileTray) mobileTray.classList.add('hidden');
            if (mobileContainer) mobileContainer.innerHTML = '';
            if (desktopContainer) desktopContainer.innerHTML = `
                <div class="text-center py-8 px-4 space-y-3 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <div class="w-14 h-14 mx-auto rounded-full bg-[#FFF8E7] text-3xl flex items-center justify-center shadow-inner">🛒</div>
                    <div>
                        <p class="text-xs font-black text-[#0F172A]">Your basket is empty</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Select meals or drinks from stalls to order.</p>
                    </div>
                </div>
            `;
            if (document.getElementById('cart-tray-total')) document.getElementById('cart-tray-total').textContent = 'Ksh 0';
            if (document.getElementById('desktop-cart-tray-total')) document.getElementById('desktop-cart-tray-total').textContent = 'Ksh 0';
            if (subtotalEl) subtotalEl.textContent = 'Ksh 0';
            if (mobileSubtotalEl) mobileSubtotalEl.textContent = 'Ksh 0';
            const defaultFeeText = systemDeliveryFee > 0 ? `Ksh ${systemDeliveryFee.toLocaleString()}` : 'FREE';
            if (feeEl) feeEl.textContent = defaultFeeText;
            if (mobileFeeEl) mobileFeeEl.textContent = defaultFeeText;
            return;
        }

        if (mobileTray) mobileTray.classList.remove('hidden');
        if (mobileContainer) mobileContainer.innerHTML = '';
        if (desktopContainer) desktopContainer.innerHTML = '';

        let subtotal = 0;
        basket.forEach(item => {
            subtotal += item.price * item.quantity;
            const row = `
                    <div class="flex justify-between items-center gap-3 text-xs py-2.5 border-b border-slate-100 last:border-b-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-[#0F172A] truncate">${item.name}</p>
                            <p class="text-[10px] text-[#05A357] font-bold mt-0.5">Ksh ${item.price.toLocaleString()} each</p>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-100 rounded-full p-1 border border-slate-200/80">
                            <button onclick="adjustQty(${item.id}, -1)" class="w-6 h-6 rounded-full bg-white hover:bg-slate-200 flex items-center justify-center text-xs text-[#0F172A] font-black shadow-xs cursor-pointer">-</button>
                            <span class="w-5 text-center text-xs font-black text-[#0F172A]">${item.quantity}</span>
                            <button onclick="adjustQty(${item.id}, 1)" class="w-6 h-6 rounded-full bg-white hover:bg-slate-200 flex items-center justify-center text-xs text-[#0F172A] font-black shadow-xs cursor-pointer">+</button>
                        </div>
                    </div>`;
            if (mobileContainer) mobileContainer.insertAdjacentHTML('beforeend', row);
            if (desktopContainer) desktopContainer.insertAdjacentHTML('beforeend', row);
        });

        // Multi-Vendor Delivery Fee: Base fee + 50% extra for each additional unique vendor stall
        const uniqueVendors = new Set(basket.map(i => i.vendorId).filter(Boolean));
        const vendorCount   = Math.max(1, uniqueVendors.size);
        const baseFee       = systemDeliveryFee;
        const extraFee      = baseFee * 0.5; // +50% of base fee per extra vendor
        const effectiveDeliveryFee = baseFee + (vendorCount - 1) * extraFee;

        const grandTotal     = subtotal + effectiveDeliveryFee;
        const subtotalText   = `Ksh ${subtotal.toLocaleString()}`;
        const feeText        = vendorCount > 1
            ? `Ksh ${effectiveDeliveryFee.toLocaleString()} (${vendorCount} Vendors)`
            : (effectiveDeliveryFee > 0 ? `Ksh ${effectiveDeliveryFee.toLocaleString()}` : 'FREE');
        const grandTotalText = `Ksh ${grandTotal.toLocaleString()}`;

        if (subtotalEl) subtotalEl.textContent = subtotalText;
        if (mobileSubtotalEl) mobileSubtotalEl.textContent = subtotalText;
        if (feeEl) feeEl.textContent = feeText;
        if (mobileFeeEl) mobileFeeEl.textContent = feeText;
        if (document.getElementById('cart-tray-total')) document.getElementById('cart-tray-total').textContent = grandTotalText;
        if (document.getElementById('desktop-cart-tray-total')) document.getElementById('desktop-cart-tray-total').textContent = grandTotalText;
    }

    // Policy Documents Modal Handlers
    const policyTexts = {
        privacy: {
            title: "Privacy Policy",
            icon: "🔒",
            content: `
                <div class="space-y-4">
                    <p class="text-xs font-bold text-[#0F172A]">Effective Date: August 30, 2026</p>
                    <p>At <strong>justFeast</strong> ("we", "our", or "us"), we are committed to protecting your privacy and ensuring complete transparency in how your personal data is collected and utilized during RheamFeast and affiliated live events.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">1. Information We Collect</h4>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li><strong>Contact & Payment Details:</strong> M-Pesa phone number used for instant STK Push transaction processing.</li>
                        <li><strong>Delivery Location:</strong> Festival seat coordinates, section/row numbers, or live GPS pins provided for runner fulfillment.</li>
                        <li><strong>Order Activity:</strong> Purchased food & drink items, vendor stall preferences, and delivery timestamps.</li>
                    </ul>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">2. How We Use Your Data</h4>
                    <p>Your information is used strictly to fulfill food & drink orders directly to your festival seat, issue order status notifications, generate delivery verification PINs, and process secure payments via M-Pesa.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">3. Security & Data Protection</h4>
                    <p>All sensitive transactions are encrypted via HTTPS. Delivery verification OTPs are securely hashed. We do not sell or rent your personal information to third-party advertisers.</p>
                </div>
            `
        },
        terms: {
            title: "Terms & Conditions",
            icon: "📜",
            content: `
                <div class="space-y-4">
                    <p class="text-xs font-bold text-[#0F172A]">RheamFeast Platform Terms of Service</p>
                    <p>By placing an order on <strong>justFeast</strong> during RheamFeast events, you agree to comply with and be bound by the following terms and conditions.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">1. Ordering & Seat Delivery</h4>
                    <p>Orders are dispatched directly to the seat location or GPS coordinates specified at checkout. Customers are responsible for providing accurate seat details (Section, Row, Seat Number) to prevent runner delays.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">2. Delivery Verification OTP</h4>
                    <p>Every order generates a unique 4-digit verification PIN. The customer must provide this PIN to the delivery runner upon arrival to confirm handover and complete the delivery contract.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">3. Multi-Vendor Pricing & Delivery Fees</h4>
                    <p>Orders incur a standard base delivery fee. When ordering from multiple vendor stalls in a single cart, an additional delivery surcharge (+50% of base fee per extra vendor) is applied to cover multi-stall runner dispatch.</p>
                </div>
            `
        },
        returns: {
            title: "Return & Refund Policy",
            icon: "🔄",
            content: `
                <div class="space-y-4">
                    <p class="text-xs font-bold text-[#0F172A]">Event Food & Beverage Refund Policy</p>
                    <p>Due to the perishable and fresh-preparation nature of concert food and drinks, standard returns are governed by the following guidelines:</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">1. Order Cancellations</h4>
                    <p>Once an order has been accepted and preparation has commenced by a festival vendor, orders cannot be cancelled or returned for a change of mind.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">2. Missing or Incorrect Items</h4>
                    <p>If your delivered order is incomplete, incorrect, or spoiled, please notify our customer support team immediately. Verified claims will be eligible for an instant M-Pesa refund or vendor re-dispatch.</p>

                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0F172A] pt-3 border-t border-slate-200">3. Non-Delivery & Unfulfilled Orders</h4>
                    <p>In the rare event that a runner cannot fulfill delivery due to vendor inventory depletion or event operational constraints, full refunds are automatically credited back to your M-Pesa account within 15 minutes.</p>
                </div>
            `
        }
    };

    function openPolicyModal(type) {
        const modal = document.getElementById('policy-modal-overlay');
        const policy = policyTexts[type] || policyTexts.privacy;
        document.getElementById('policy-title').textContent = policy.title;
        document.getElementById('policy-content').innerHTML = policy.content;
        modal.classList.remove('hidden');
    }

    function closePolicyModal() {
        document.getElementById('policy-modal-overlay').classList.add('hidden');
    }
</script>
</body>
</html>
