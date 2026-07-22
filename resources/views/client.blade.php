<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast — Event Seat Delivery</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#05A357">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet.js and QRious libraries for GPS map pinning and secure QR codes -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {sans: ['Outfit', 'sans-serif']},
                    colors: {
                        brand: {rose: '#FFC244', orange: '#FFC244', amber: '#FFC244', emerald: '#05A357'},
                        jf: {
                            green: '#05A357',
                            greenDark: '#047A43',
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
                        glow: '0 20px 50px rgba(5, 163, 87, 0.22)'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --jf-green: #05A357;
            --jf-green-dark: #047A43;
            --jf-yellow: #FFC244;
            --jf-ink: #111827;
            --jf-muted: #667085;
            --jf-border: #E7E8DD;
            --jf-cream: #FFF8E7;
            --jf-cloud: #F6F7F2;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, rgba(255, 194, 68, .35), transparent 32rem),
            radial-gradient(circle at 85% 18%, rgba(5, 163, 87, .18), transparent 32rem),
            linear-gradient(180deg, #FFFDF4 0%, #F8FAF4 48%, #FFFFFF 100%);
            color: var(--jf-ink);
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-track {
            background: #F8FAF4;
        }

        ::-webkit-scrollbar-thumb {
            background: #C9D1BE;
            border-radius: 100px;
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
            transform: scale(.98);
        }

        .app-shell {
            max-width: 1480px;
            margin: 0 auto;
            padding: 18px;
        }

        .jf-card {
            background: rgba(255, 255, 255, .86);
            border: 1px solid rgba(231, 232, 221, .95);
            box-shadow: 0 10px 30px rgba(17, 24, 39, .07);
            backdrop-filter: blur(14px);
        }

        .hero-panel {
            background: linear-gradient(135deg, rgba(17, 24, 39, .96), rgba(4, 122, 67, .92)),
            radial-gradient(circle at 75% 25%, rgba(255, 194, 68, .45), transparent 18rem);
            position: relative;
            overflow: hidden;
        }

        .hero-panel:before {
            content: '';
            position: absolute;
            inset: -20%;
            background: radial-gradient(circle at 76% 32%, rgba(255, 194, 68, .34), transparent 15rem),
            linear-gradient(115deg, transparent 0 47%, rgba(255, 255, 255, .06) 47% 49%, transparent 49% 100%);
            pointer-events: none;
        }

        .floating-food {
            animation: floaty 5s ease-in-out infinite;
        }

        .floating-food:nth-child(2) {
            animation-delay: .7s;
        }

        .floating-food:nth-child(3) {
            animation-delay: 1.25s;
        }

        @keyframes floaty {
            0%, 100% {
                transform: translateY(0) rotate(-2deg);
            }
            50% {
                transform: translateY(-13px) rotate(3deg);
            }
        }

        .stadium-bowl {
            background: radial-gradient(ellipse at center, rgba(255, 194, 68, .96) 0 18%, transparent 19%),
            repeating-radial-gradient(ellipse at center, rgba(255, 255, 255, .20) 0 9px, rgba(255, 255, 255, .05) 10px 18px, transparent 19px 28px);
        }

        .search-dock {
            box-shadow: 0 18px 50px rgba(17, 24, 39, .12);
        }

        .category-active {
            background: #111827 !important;
            color: #fff !important;
            border-color: #111827 !important;
            box-shadow: 0 10px 24px rgba(17, 24, 39, .16);
        }

        .category-pill {
            background: #fff;
            border: 1px solid var(--jf-border);
            color: #111827;
        }

        .category-pill:hover {
            border-color: var(--jf-green);
            transform: translateY(-1px);
        }

        .seat-map-mini {
            background-image: linear-gradient(rgba(17, 24, 39, .045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(17, 24, 39, .045) 1px, transparent 1px);
            background-size: 18px 18px;
            background-color: #FBFDF8;
        }

        .glass-card {
            background: rgba(255, 255, 255, .9) !important;
            border: 1px solid var(--jf-border) !important;
            box-shadow: 0 10px 30px rgba(17, 24, 39, .08) !important;
            color: #111827 !important;
        }

        .radar-sweep {
            position: absolute;
            inset: 0;
            background: conic-gradient(from 0deg at 50% 50%, rgba(5, 163, 87, .22), transparent 125deg);
            animation: spin 2.2s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(.8);
                opacity: .7;
            }
            100% {
                transform: scale(1.35);
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
                transform: translate(-2px, 1px) rotate(-1deg);
            }
            40%, 80% {
                transform: translate(2px, -1px) rotate(1deg);
            }
        }

        .phone-buzz {
            animation: buzz .5s ease-in-out 3;
        }

        .stadium-grid {
            background-image: radial-gradient(rgba(5, 163, 87, .12) 1px, transparent 1px);
            background-size: 14px 14px;
        }

        .vendor-showcase-card {
            transform: translateZ(0);
        }

        .vendor-showcase-card:hover {
            transform: translateY(-2px);
        }

        .vendor-showcase-card button {
            border-radius: 999px !important;
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
    <header class="jf-card rounded-[28px] px-4 md:px-6 py-4 mb-5 flex items-center justify-between sticky top-3 z-40">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-white flex items-center justify-center shadow-card overflow-hidden">
                <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-9 w-auto object-contain">
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tight leading-none">just<span class="text-[#05A357]">Feast</span>
                </h1>
                <p class="text-[10px] text-zinc-500 font-bold mt-1" id="live-event-banner">Loading active event...</p>
            </div>
        </div>

        <nav class="hidden lg:flex items-center gap-2 bg-[#F6F7F2] p-1.5 rounded-full border border-[#E7E8DD]">
            <a href="#vendors"
               class="px-4 py-2 rounded-full text-xs font-black text-zinc-700 hover:bg-white">Vendors</a>
            <button onclick="openSeatModal()"
                    class="px-4 py-2 rounded-full text-xs font-black text-zinc-700 hover:bg-white">Delivery map
            </button>
            <a href="#how-it-works" class="px-4 py-2 rounded-full text-xs font-black text-zinc-700 hover:bg-white">How
                it works</a>
        </nav>

        <div class="flex items-center gap-3">
            <div id="header-user-badge"
                 class="hidden md:flex items-center gap-2 bg-[#F6F7F2] border border-[#E7E8DD] px-3 py-2 rounded-full">
                <span class="w-2 h-2 rounded-full bg-[#05A357] animate-pulse"></span>
                <span class="text-[10px] font-black text-[#111827]" id="header-user-name">Guest</span>
            </div>
            <div id="header-auth-buttons"></div>
        </div>
    </header>

    <!-- Customer Main Wrapper -->
    <main class="flex-1">
        <div id="cust-main" class="hidden space-y-6 pb-10">
            <!-- Hero -->
            <section id="glovo-hero-fold"
                     class="hero-panel rounded-[38px] md:rounded-[48px] p-5 md:p-8 lg:p-10 text-white">
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-7">
                        <div
                            class="inline-flex items-center gap-2 bg-white/10 border border-white/15 backdrop-blur px-3.5 py-2 rounded-full text-[10px] font-black uppercase tracking-[.18em] text-white">
                            <span class="relative flex h-2 w-2"><span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC244] opacity-75"></span><span
                                    class="relative inline-flex rounded-full h-2 w-2 bg-[#FFC244]"></span></span>
                            Event food delivery, without leaving the crowd
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-[.92]">
                                Food that <span class="text-[#FFC244]">finds</span><br class="hidden md:block"> your
                                location.
                            </h2>
                            <p class="text-white/75 text-sm md:text-base leading-relaxed max-w-xl">
                                Connect event attendees with approved food vendors. Drop your location pin on the map,
                                order from nearby stalls, pay by M-Pesa, then track your runner in real time.
                            </p>
                        </div>

                        <div
                            class="search-dock bg-white rounded-[28px] md:rounded-full p-2 md:p-2.5 max-w-3xl grid grid-cols-1 md:grid-cols-[1fr_auto] gap-2 text-[#111827]">
                            <button onclick="openSeatModal()"
                                    class="flex items-center gap-3 px-4 py-3 rounded-2xl md:rounded-full hover:bg-[#F6F7F2] text-left group">
                                <span
                                    class="w-11 h-11 rounded-full bg-[#05A357]/10 text-[#05A357] flex items-center justify-center group-hover:bg-[#05A357] group-hover:text-white"><i
                                        class="fas fa-location-dot"></i></span>
                                <span class="min-w-0">
                                        <span
                                            class="block text-[9px] uppercase tracking-wider font-black text-zinc-400">Deliver to</span>
                                        <span class="block text-sm font-black truncate" id="selected-seat-hero">Set delivery location</span>
                                    </span>
                            </button>
                            <a href="#vendors"
                               class="inline-flex justify-center items-center gap-2 bg-[#05A357] hover:bg-[#047A43] text-white px-7 py-4 rounded-2xl md:rounded-full text-sm font-black shadow-glow">
                                Explore vendors <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-3 max-w-xl">
                            <div class="bg-white/10 border border-white/10 rounded-3xl p-4 backdrop-blur">
                                <p class="text-2xl font-black text-[#FFC244]">8-15</p>
                                <p class="text-[10px] uppercase tracking-wider text-white/60 font-black">Min
                                    delivery</p>
                            </div>
                            <div class="bg-white/10 border border-white/10 rounded-3xl p-4 backdrop-blur">
                                <p class="text-2xl font-black text-[#FFC244]">M-Pesa</p>
                                <p class="text-[10px] uppercase tracking-wider text-white/60 font-black">Fast
                                    payment</p>
                            </div>
                            <div class="bg-white/10 border border-white/10 rounded-3xl p-4 backdrop-blur">
                                <p class="text-2xl font-black text-[#FFC244]">PIN</p>
                                <p class="text-[10px] uppercase tracking-wider text-white/60 font-black">Safe
                                    handover</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="relative min-h-[430px]">
                            <div
                                class="absolute inset-0 stadium-bowl rounded-full border border-white/15 opacity-90"></div>
                            <div
                                class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 rounded-full bg-[#FFC244] shadow-2xl flex items-center justify-center text-[#111827] text-center p-6">
                                <div>
                                    <i class="fas fa-music text-3xl mb-2"></i>
                                    <p class="text-xs font-black uppercase tracking-widest">Live Stage</p>
                                    <p class="text-[10px] font-bold opacity-70">Uhuru Gardens Arena</p>
                                </div>
                            </div>
                            <div
                                class="floating-food absolute left-3 top-10 bg-white text-[#111827] rounded-[28px] p-3 shadow-soft flex items-center gap-3">
                                <span class="text-3xl">🍔</span>
                                <div><p class="text-xs font-black">Burgers</p>
                                    <p class="text-[10px] text-zinc-500 font-bold">from Ksh 450</p></div>
                            </div>
                            <div
                                class="floating-food absolute right-0 top-28 bg-white text-[#111827] rounded-[28px] p-3 shadow-soft flex items-center gap-3">
                                <span class="text-3xl">🥤</span>
                                <div><p class="text-xs font-black">Cold drinks</p>
                                    <p class="text-[10px] text-zinc-500 font-bold">ice cold</p></div>
                            </div>
                            <div
                                class="floating-food absolute bottom-10 left-10 bg-white text-[#111827] rounded-[28px] p-3 shadow-soft flex items-center gap-3">
                                <span class="text-3xl">🍿</span>
                                <div><p class="text-xs font-black">Snacks</p>
                                    <p class="text-[10px] text-zinc-500 font-bold">queue-free</p></div>
                            </div>
                            <div
                                class="absolute bottom-4 right-6 bg-[#05A357] text-white rounded-[30px] p-4 shadow-glow w-52">
                                <p class="text-[10px] uppercase tracking-widest font-black text-white/70">Runner
                                    status</p>
                                <div class="flex items-center gap-2 mt-2"><span
                                        class="w-2 h-2 bg-[#FFC244] rounded-full animate-pulse"></span>
                                    <p class="text-sm font-black">Ready near Gate B</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How it works / controls -->
            <section id="how-it-works" class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <div class="lg:col-span-8 jf-card rounded-[32px] p-4 md:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-[#FFF8E7] rounded-[26px] p-4 border border-[#F7E5B2]">
                            <span
                                class="w-10 h-10 rounded-2xl bg-[#FFC244] flex items-center justify-center text-[#111827] mb-3"><i
                                    class="fas fa-map-location-dot"></i></span>
                            <h3 class="text-sm font-black">1. Pin location</h3>
                            <p class="text-[11px] text-zinc-500 leading-relaxed mt-1">Drop your location pin on the map
                                before checkout.</p>
                        </div>
                        <div class="bg-white rounded-[26px] p-4 border border-[#E7E8DD]">
                            <span
                                class="w-10 h-10 rounded-2xl bg-[#05A357]/10 text-[#05A357] flex items-center justify-center mb-3"><i
                                    class="fas fa-store"></i></span>
                            <h3 class="text-sm font-black">2. Pick a vendor</h3>
                            <p class="text-[11px] text-zinc-500 leading-relaxed mt-1">Browse approved stalls serving
                                your zone.</p>
                        </div>
                        <div class="bg-[#111827] rounded-[26px] p-4 border border-[#111827] text-white">
                            <span
                                class="w-10 h-10 rounded-2xl bg-white/10 text-[#FFC244] flex items-center justify-center mb-3"><i
                                    class="fas fa-person-running"></i></span>
                            <h3 class="text-sm font-black">3. Track delivery</h3>
                            <p class="text-[11px] text-white/60 leading-relaxed mt-1">Runner delivers using your secure
                                PIN.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 jf-card rounded-[32px] p-4 md:p-5 seat-map-mini">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest font-black text-zinc-400">Your location</p>
                            <h3 class="text-sm font-black mt-1" id="selected-seat-label">Configure Delivery
                                Location</h3>
                            <p class="text-[11px] text-zinc-500 mt-1" id="selected-seat-sub">Tap to pin your location on
                                map</p>
                        </div>
                        <span id="seat-status-pill"
                              class="text-[9px] bg-zinc-100 text-zinc-500 px-2.5 py-1 rounded-full font-black border border-zinc-200">Not Set</span>
                    </div>
                    <button onclick="openSeatModal()"
                            class="mt-4 w-full bg-[#111827] text-white rounded-full py-3 text-xs font-black hover:bg-[#05A357] flex items-center justify-center gap-2">
                        <i class="fas fa-map-location-dot"></i> Set delivery pin
                    </button>
                </div>
            </section>

            <!-- Marketplace -->
            <section id="vendors" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-8 xl:col-span-9 space-y-5 min-w-0">
                    <div class="jf-card rounded-[32px] p-4 md:p-5 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-[10px] uppercase tracking-[.2em] text-[#05A357] font-black">Event
                                    marketplace</p>
                                <h2 class="text-2xl md:text-3xl font-black tracking-tight">Order from vendors serving
                                    your zone</h2>
                            </div>
                            <div
                                class="flex items-center gap-2 text-[11px] font-black text-zinc-500 bg-[#F6F7F2] border border-[#E7E8DD] rounded-full px-3 py-2 self-start md:self-auto">
                                <i class="fas fa-shield-halved text-[#05A357]"></i> Verified event vendors
                            </div>
                        </div>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#05A357]"><i
                                    class="fas fa-search text-sm"></i></span>
                            <input type="text" id="menu-search" oninput="searchMenu()"
                                   placeholder="Search burgers, fries, soda, water, snacks..."
                                   class="w-full pl-11 pr-4 py-4 rounded-2xl md:rounded-full bg-[#F6F7F2] border border-[#E7E8DD] focus:border-[#05A357] focus:ring-4 focus:ring-[#05A357]/10 text-[#111827] text-sm font-bold shadow-inner focus:outline-none">
                        </div>
                        <div class="flex items-center gap-2.5 py-1 overflow-x-auto no-scrollbar scroll-smooth">
                            <button onclick="setCategory('all')" id="cat-all"
                                    class="category-pill category-active flex items-center gap-2 px-4 py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-base">🏟️</span><span
                                    class="text-[11px] font-black uppercase tracking-wide">All vendors</span>
                            </button>
                            <button onclick="setCategory('food')" id="cat-food"
                                    class="category-pill flex items-center gap-2 px-4 py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-base">🍔</span><span
                                    class="text-[11px] font-black uppercase tracking-wide">Meals</span>
                            </button>
                            <button onclick="setCategory('drinks')" id="cat-drinks"
                                    class="category-pill flex items-center gap-2 px-4 py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-base">🥤</span><span
                                    class="text-[11px] font-black uppercase tracking-wide">Drinks</span>
                            </button>
                            <button onclick="setCategory('snacks')" id="cat-snacks"
                                    class="category-pill flex items-center gap-2 px-4 py-3 rounded-full shadow-sm whitespace-nowrap cursor-pointer focus:outline-none">
                                <span class="text-base">🍿</span><span
                                    class="text-[11px] font-black uppercase tracking-wide">Snacks</span>
                            </button>
                        </div>
                    </div>

                    <div
                        class="relative overflow-hidden rounded-[34px] bg-[#05A357] p-5 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 text-white shadow-glow">
                        <div class="absolute -right-8 -top-10 text-[170px] opacity-10">🎤</div>
                        <div class="relative z-10">
                            <span
                                class="inline-flex items-center gap-1.5 bg-[#FFC244] text-[#111827] text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full"><i
                                    class="fas fa-bolt"></i> Live event offer</span>
                            <h3 class="font-black text-2xl tracking-tight mt-3">Don’t miss the headliner.</h3>
                            <p class="text-white/70 text-sm mt-1 max-w-xl">Order before the next set starts and get
                                priority runner dispatch to your location zone.</p>
                        </div>
                        <button onclick="openSeatModal()"
                                class="relative z-10 bg-white text-[#111827] px-5 py-3 rounded-full text-xs font-black hover:bg-[#FFC244] self-start md:self-auto">
                            Set delivery location
                        </button>
                    </div>

                    <div id="vendor-list-container" class="space-y-8"></div>
                </div>

                <aside class="lg:col-span-4 xl:col-span-3 space-y-4 lg:sticky lg:top-28">
                    <div class="jf-card rounded-[30px] p-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-11 h-11 rounded-2xl bg-[#FFC244] flex items-center justify-center text-[#111827]">
                                <i class="fas fa-user"></i></div>
                            <div>
                                <p class="text-[10px] text-zinc-400 font-black uppercase tracking-wider">Attendee</p>
                                <h4 class="text-sm font-black" id="cust-user-name">Guest</h4>
                            </div>
                        </div>
                        <button onclick="logoutCustomer()"
                                class="text-zinc-400 hover:text-red-500 text-[10px] font-black bg-[#F6F7F2] px-3 py-2 rounded-full">
                            Logout
                        </button>
                    </div>

                    <div id="desktop-cart-tray" class="hidden lg:block jf-card rounded-[32px] overflow-hidden">
                        <div class="px-5 pt-5 pb-4 border-b border-[#EEF0E6] flex justify-between items-center">
                            <span class="text-sm font-black flex items-center gap-2"><i
                                    class="fas fa-basket-shopping text-[#05A357]"></i> Basket</span>
                            <button onclick="clearBasket()"
                                    class="text-[10px] text-zinc-400 hover:text-red-500 font-black">Clear
                            </button>
                        </div>
                        <div id="desktop-cart-tray-items" class="max-h-[260px] overflow-y-auto space-y-2 p-4">
                            <div class="text-center py-7 space-y-2">
                                <div class="text-4xl">🛒</div>
                                <p class="text-xs text-zinc-400 font-bold">Your basket is empty.<br>Add food from
                                    vendors.</p>
                            </div>
                        </div>
                        <div class="border-t border-[#EEF0E6] p-5 space-y-3 bg-[#FBFCF8]">
                            <div class="flex justify-between gap-3 text-xs">
                                <span class="text-zinc-500 font-black flex items-center gap-1.5"><i
                                        class="fas fa-location-dot text-[#05A357]"></i> Location</span>
                                <span class="font-black text-right truncate max-w-[150px]"
                                      id="desktop-cart-location-text">Not configured</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-xs text-zinc-500 font-black">Total</span>
                                <span class="text-2xl font-black text-[#05A357]"
                                      id="desktop-cart-tray-total">Ksh 0</span>
                            </div>
                            <button onclick="checkoutOrder()"
                                    class="w-full py-4 bg-[#111827] hover:bg-[#05A357] text-white rounded-full text-xs font-black shadow-card flex items-center justify-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa"
                                         class="h-6 w-auto object-contain scale-110">
                                </span>
                                <span class="text-white/30 text-xs">|</span>
                                <span
                                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                                    <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja"
                                         class="h-6 w-auto object-contain scale-110">
                                </span>
                                <span>Order &amp; Pay</span>
                            </button>
                        </div>
                    </div>
                </aside>
            </section>
        </div>

        <!-- Active Order Tracking -->
        <div id="cust-tracker" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch pb-10">
            <div class="lg:col-span-7 glass-card rounded-[34px] p-6 flex flex-col justify-between space-y-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[.2em] text-[#05A357] font-black">Live order</p>
                    <h3 class="text-2xl font-black tracking-tight">Delivery timeline</h3>
                    <p class="text-xs text-zinc-500 font-bold">Track your vendor-to-location delivery in real time.</p>
                </div>
                <div class="space-y-4 bg-[#F6F7F2] p-5 rounded-[26px] border border-[#E7E8DD] text-left">
                    <div class="flex items-center gap-3" id="step-created">
                        <div
                            class="w-6 h-6 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold">
                            <i class="fas fa-check"></i></div>
                        <span class="text-sm font-black text-[#111827]">Order placed & paid</span></div>
                    <div class="flex items-center gap-3" id="step-preparing">
                        <div
                            class="w-6 h-6 rounded-full bg-white border border-[#E7E8DD] text-zinc-400 flex items-center justify-center text-[10px] font-bold">
                            2
                        </div>
                        <span class="text-sm font-black text-zinc-400">Vendor preparing</span></div>
                    <div class="flex items-center gap-3" id="step-ready">
                        <div
                            class="w-6 h-6 rounded-full bg-white border border-[#E7E8DD] text-zinc-400 flex items-center justify-center text-[10px] font-bold">
                            3
                        </div>
                        <span class="text-sm font-black text-zinc-400">Ready for runner</span></div>
                    <div class="flex items-center gap-3" id="step-enroute">
                        <div
                            class="w-6 h-6 rounded-full bg-white border border-[#E7E8DD] text-zinc-400 flex items-center justify-center text-[10px] font-bold">
                            4
                        </div>
                        <span class="text-sm font-black text-zinc-400">Runner en-route</span></div>
                </div>
                <div class="bg-[#05A357]/8 border border-[#05A357]/15 p-4 rounded-2xl text-xs text-zinc-600 font-bold">
                    <i class="fas fa-circle-info mr-1 text-[#05A357]"></i> Stay near your pinned location. The runner
                    will
                    ask for your delivery PIN before handover.
                </div>
            </div>
            <div
                class="lg:col-span-5 glass-card rounded-[34px] p-6 text-center flex flex-col justify-between space-y-6">
                <div><p class="text-[10px] uppercase tracking-[.2em] text-[#05A357] font-black">Runner radar</p>
                    <h3 class="text-2xl font-black tracking-tight">Location delivery active</h3>
                    <p class="text-xs text-zinc-500 font-bold">Uhuru Gardens Event Park</p></div>
                <div
                    class="relative w-48 h-48 mx-auto flex items-center justify-center bg-[#F6F7F2] rounded-full border border-[#E7E8DD] overflow-hidden">
                    <div class="radar-sweep"></div>
                    <div class="absolute w-48 h-48 rounded-full border border-[#05A357]/15 pulse-ring"></div>
                    <div class="absolute w-32 h-32 rounded-full border border-[#FFC244]/25 pulse-ring"
                         style="animation-delay:.55s"></div>
                    <div
                        class="w-14 h-14 bg-[#FFC244] border border-[#e6a920] rounded-full flex items-center justify-center shadow-lg relative z-10 animate-bounce">
                        <i class="fas fa-person-running text-[#111827] text-xl"></i></div>
                </div>
                <div class="bg-[#111827] text-white p-5 rounded-[26px] space-y-1"><span
                        class="text-[9px] uppercase tracking-widest text-white/50 font-black">Secure handover PIN</span>
                    <h4 class="text-4xl font-black text-[#FFC244] tracking-widest" id="tracker-pin">----</h4>
                    <p class="text-[10px] text-white/50 font-bold">Share this PIN only when the runner arrives.</p>
                </div>

                <!-- QR Verification Code Block (Generates dynamically on arrival) -->
                <div id="tracker-qr-container"
                     class="hidden bg-white border border-[#E7E8DD] p-4 rounded-[26px] text-center space-y-2 flex flex-col items-center justify-center">
                    <span class="text-[9px] uppercase tracking-widest text-[#05A357] font-black"><i
                            class="fas fa-qrcode mr-1"></i> Scan to Verify Delivery</span>
                    <canvas id="tracker-qr-canvas" class="w-32 h-32 border border-zinc-100 p-1 bg-white"></canvas>
                    <p class="text-[9.5px] text-zinc-500 font-medium leading-relaxed">Let the runner scan this QR code
                        or type your PIN to complete the delivery.</p>
                </div>
                <button onclick="resetTrackerDemo()"
                        class="text-xs bg-[#F6F7F2] hover:bg-[#E7E8DD] text-[#111827] px-4 py-3 rounded-full font-black mx-auto">
                    Order something else
                </button>
            </div>
        </div>
    </main>

    <!-- Mobile Basket Tray -->
    <div id="phone-cart-tray"
         class="hidden lg:hidden fixed bottom-0 inset-x-0 max-w-md mx-auto bg-white border-t border-[#E7E8DD] rounded-t-[30px] p-5 pb-6 z-40 shadow-2xl">
        <div class="flex justify-between items-center mb-3"><span
                class="text-sm font-black flex items-center gap-1.5"><i
                    class="fas fa-basket-shopping text-[#05A357]"></i> Basket</span>
            <button onclick="clearBasket()" class="text-[10px] text-zinc-400 hover:text-red-500 font-black">Clear
            </button>
        </div>
        <div id="cart-tray-items" class="max-h-[120px] overflow-y-auto space-y-2 mb-4"></div>
        <div class="border-t border-[#E7E8DD] pt-3 space-y-3">
            <div class="flex justify-between items-center text-xs"><span
                    class="text-zinc-500 font-black">Delivery location:</span><span class="font-black"
                                                                                    id="cart-location-text">Not configured</span>
            </div>
            <div class="flex justify-between items-center"><span
                    class="text-xs text-zinc-500 font-black">Total:</span><span
                    class="text-xl font-black text-[#05A357]" id="cart-tray-total">Ksh 0</span></div>
            <button onclick="checkoutOrder()"
                    class="w-full py-4 bg-[#111827] hover:bg-[#05A357] text-white rounded-full text-xs font-black flex items-center justify-center gap-2">
                <span
                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa"
                         class="h-6 w-auto object-contain scale-110">
                </span>
                <span class="text-white/30 text-xs">|</span>
                <span
                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                    <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja"
                         class="h-6 w-auto object-contain scale-110">
                </span>
                <span>Order &amp; Pay</span>
            </button>
        </div>
    </div>
</div>

<footer class="mt-10 bg-[#0B1117] text-white relative overflow-hidden">
    <div class="h-1 bg-gradient-to-r from-[#FFC244] via-[#05A357] to-[#FFC244]"></div>
    <div class="max-w-[1480px] mx-auto px-5 md:px-8 py-12 grid grid-cols-1 md:grid-cols-12 gap-8">
        <div class="md:col-span-5 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-9 w-auto object-contain">
                </div>
                <h2 class="text-2xl font-black">just<span class="text-[#05A357]">Feast</span></h2></div>
            <p class="text-sm text-white/55 leading-relaxed max-w-md">Live event food ordering for concerts, stadiums
                and festivals. Vendors sell more, attendees miss less, runners deliver to the exact location.</p>
        </div>
        <div class="md:col-span-2 space-y-3"><h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">
                For attendees</h4><a href="#vendors" class="block text-sm text-white/65 hover:text-[#FFC244]">Browse
                vendors</a>
            <button onclick="openSeatModal()" class="block text-sm text-white/65 hover:text-[#FFC244]">Delivery map
            </button>
            <a href="#how-it-works" class="block text-sm text-white/65 hover:text-[#FFC244]">How it works</a></div>
        <div class="md:col-span-2 space-y-3"><h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">
                For events</h4><a href="#" class="block text-sm text-white/65 hover:text-[#FFC244]">Vendor
                onboarding</a><a href="#" class="block text-sm text-white/65 hover:text-[#FFC244]">Runner ops</a><a
                href="#" class="block text-sm text-white/65 hover:text-[#FFC244]">Support</a></div>
        <div class="md:col-span-3 space-y-3"><h4 class="text-[11px] font-black uppercase tracking-widest text-white/40">
                Payments</h4>
            <div class="bg-white/5 border border-white/10 rounded-3xl p-4 space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-7 w-auto object-contain"
                         onerror="this.style.display='none'">
                    <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja" class="h-7 w-auto object-contain"
                         onerror="this.style.display='none'">
                </div>
                <p class="text-xs text-white/50 leading-relaxed">Secure STK Push checkout and delivery PIN
                    verification for safer handovers.</p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div
            class="max-w-[1480px] mx-auto px-5 md:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-[11px] text-white/35 font-bold">© 2026 justFeast. All rights reserved.</p>
            <div class="flex items-center gap-2 text-[11px] text-white/45 font-black uppercase tracking-wider">
                <span>Powered by</span>
                <img src="{{asset('/images/logo/basemathai.jpeg')}}"
                     onerror="this.style.display='none'; document.getElementById('basemath-wordmark').classList.remove('hidden');"
                     alt="Basemath" class="h-6 w-auto object-contain">
                <span id="basemath-wordmark" class="hidden text-white">Basemath</span>
            </div>
        </div>
    </div>
</footer>
<!-- M-Pesa STK Simulator Overlay -->
<div id="mpesa-simulation-overlay"
     class="hidden fixed inset-0 bg-[#2D3748]/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div
        class="w-full max-w-[380px] bg-white rounded-[32px] shadow-2xl border border-[#E2E8F0] phone-buzz overflow-hidden">

        {{-- Modal header with M-Pesa + Faraja branding --}}
        <div class="bg-[#00A082] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa"
                         class="h-6 w-auto object-contain scale-110"
                         onerror="this.outerHTML='<span class=\'text-[#00A082] font-black text-[9px]\'>M-PESA</span>'">
                </span>
                <span class="text-white/30">|</span>
                <span
                    class="inline-flex items-center justify-center bg-white px-2 rounded-full h-8 w-14 overflow-hidden">
                    <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja"
                         class="h-6 w-auto object-contain scale-110"
                         onerror="this.outerHTML='<span class=\'text-[#00A082] font-black text-[9px]\'>Faraja</span>'">
                </span>
            </div>
            <span class="text-white/70 text-[10px] font-black uppercase tracking-widest">Secure Pay</span>
        </div>

        <div class="p-7 text-center space-y-5">
            <div class="space-y-1.5">
                <p class="text-xs text-zinc-500 font-medium">Authorize payment of</p>
                <strong class="text-[#00A082] text-2xl font-black block" id="mpesa-amount">Ksh 0</strong>
                <p class="text-xs text-zinc-500">to <strong class="text-[#2D3748] font-bold">JUSTFEAST LTD</strong></p>
            </div>

            {{-- Payment method selector --}}
            <div class="flex gap-2 justify-center">
                <button id="pay-mpesa-tab" onclick="selectPayTab('mpesa')"
                        class="flex items-center gap-2 px-4 py-2 rounded-full border-2 border-[#00A082] bg-[#00A082]/5 text-xs font-black transition">
                    <img src="{{ asset('images/logo/mpesa.png') }}" alt="M-Pesa" class="h-4 w-auto object-contain">
                </button>
                <button id="pay-faraja-tab" onclick="selectPayTab('faraja')"
                        class="flex items-center gap-2 px-4 py-2 rounded-full border-2 border-transparent bg-zinc-100 text-xs font-black transition">
                    <img src="{{ asset('images/logo/Faraja.png') }}" alt="Faraja" class="h-4 w-auto object-contain">
                </button>
            </div>

            <div class="py-1">
                <input type="password" id="mpesa-pin-input" placeholder="Enter PIN"
                       class="w-48 mx-auto text-center font-black tracking-widest text-2xl py-3 rounded-2xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] focus:outline-none focus:border-[#00A082] focus:ring-1 focus:ring-[#00A082] transition shadow-inner"
                       maxlength="4" value="1234">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="cancelMpesaSimulation()"
                        class="py-3.5 bg-[#F7F9FA] hover:bg-[#E2E8F0] text-zinc-600 rounded-full text-xs font-bold transition border border-[#E2E8F0] cursor-pointer">
                    Cancel
                </button>
                <button onclick="confirmMpesaSimulation()"
                        class="py-3.5 bg-[#00A082] hover:bg-[#008A70] text-white rounded-full text-xs font-bold transition shadow-md cursor-pointer">
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Authentication Modal Overlay (OTP Login) -->
<div id="auth-modal-overlay"
     class="hidden fixed inset-0 bg-[#2D3748]/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <!-- Auth Screen -->
    <div id="cust-auth"
         class="w-full max-w-[360px] bg-white rounded-[32px] p-8 shadow-2xl border border-[#E2E8F0] relative">
        <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600"><i
                class="fas fa-times"></i></button>
        <div class="text-center space-y-6">
            <div
                class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-zinc-100/15 border border-zinc-200 overflow-hidden">
                <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-12 w-auto object-contain">
            </div>
            <div class="space-y-2">
                <h2 class="text-xl font-black text-[#2D3748]">Concert Seat Delivery</h2>
                <p class="text-xs text-zinc-500 px-2 leading-relaxed font-medium">Get food, drinks, and snacks delivered
                    directly to your stadium seat during the concert!</p>
            </div>
            <div class="space-y-4 text-left">
                <div>
                    <label class="block text-[9px] font-black text-zinc-500 mb-1.5 uppercase tracking-wider">Enter Phone
                        Number</label>
                    <input type="text" id="cust-phone-input" placeholder="e.g. 0712345678"
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-sm focus:outline-none focus:border-[#00A082]"
                           value="0712345678">
                </div>
                <button onclick="sendOTP()"
                        class="w-full py-3.5 rounded-full bg-[#00A082] text-white font-bold text-xs hover:bg-[#008A70] transition shadow-md shadow-[#00A082]/10 border-0 cursor-pointer">
                    Send Verification OTP
                </button>
            </div>
            <div class="border-t border-[#E2E8F0] pt-5">
                <span class="text-[10px] text-zinc-400 block mb-3 font-semibold uppercase tracking-wider">Or instantly access:</span>
                <button onclick="quickLogin('customer@justfeast.com')"
                        class="w-full py-3 rounded-full bg-[#FFC244] hover:bg-[#E0A325] text-[#2D3748] text-xs font-bold transition shadow-sm border border-[#E0A325] cursor-pointer">
                    🚀 Access as John Customer
                </button>
            </div>
        </div>
    </div>

    <!-- OTP Screen -->
    <div id="cust-otp"
         class="hidden w-full max-w-[360px] bg-white rounded-[32px] p-8 shadow-2xl border border-[#E2E8F0] relative">
        <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600"><i
                class="fas fa-times"></i></button>
        <div class="text-center space-y-6">
            <h3 class="text-xl font-black text-[#2D3748]">Confirm OTP</h3>
            <p class="text-xs text-zinc-500" id="otp-phone-text">We sent a verification SMS to your phone</p>
            <div class="space-y-4 text-left">
                <div>
                    <label class="block text-[9px] font-black text-zinc-500 mb-1.5 uppercase tracking-wider">Enter
                        4-Digit Code</label>
                    <input type="text" id="cust-otp-input" placeholder="Enter 1234"
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] text-center text-lg tracking-widest font-bold focus:outline-none focus:border-[#00A082]"
                           value="1234">
                </div>
                <button onclick="verifyOTP()"
                        class="w-full py-3.5 rounded-full bg-[#00A082] text-white font-bold text-xs hover:bg-[#008A70] transition shadow-md shadow-[#00A082]/10 border-0 cursor-pointer">
                    Verify Code
                </button>
                <button onclick="showAuthScreen()"
                        class="w-full py-2 rounded-full text-zinc-400 hover:text-zinc-600 text-xs font-bold text-center transition">
                    Go Back
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
                               readonly value="-1.32588000">
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-zinc-500 uppercase">Longitude</label>
                        <input type="text" id="gps-lng-input"
                               class="w-full px-2 py-1.5 rounded bg-white border border-[#E2E8F0] text-[10px] font-mono font-bold text-zinc-700"
                               readonly value="36.79941000">
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
    let basket = [];
    let activeOrder = null;
    let hasBeepedForArrival = false;
    let selectedSeat = null;
    let leafletMap = null;
    let leafletMarker = null;
    let activeSeatingMode = 'gps';
    let pollingInterval = null;
    let audioCtx = null;

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

        // Always show the marketplace main wrapper
        document.getElementById('cust-main').classList.remove('hidden');

        // Session check
        const saved = localStorage.getItem('justfeast_client_user');
        if (laravelUser) {
            currentUser = laravelUser;
            localStorage.setItem('justfeast_client_user', JSON.stringify(currentUser));
        } else if (saved) {
            currentUser = JSON.parse(saved);
        }
        updateAuthHeader();

        // Location recovery
        const savedSeat = localStorage.getItem('justfeast_selected_seat');
        if (savedSeat) {
            try {
                selectedSeat = JSON.parse(savedSeat);
                if (selectedSeat.type === 'gps') {
                    const lat = selectedSeat.latitude;
                    const lng = selectedSeat.longitude;
                    const desc = selectedSeat.description || '';
                    document.getElementById('selected-seat-label').textContent = "GPS Location Pin";
                    document.getElementById('selected-seat-sub').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}${desc ? ' — ' + desc : ''}`;

                    const locationText = `GPS Pin: ${desc || (lat.toFixed(4) + ', ' + lng.toFixed(4))}`;
                    document.getElementById('cart-location-text').textContent = locationText;
                    document.getElementById('desktop-cart-location-text').textContent = locationText;
                    document.getElementById('selected-seat-hero').textContent = locationText;
                } else {
                    const {section, row, seat} = selectedSeat;
                    document.getElementById('selected-seat-label').textContent = section || 'Stadium Seat';
                    document.getElementById('selected-seat-sub').textContent = `${row || ''} — ${seat || ''}`;
                    const locationText = `${section || ''}, ${row || ''}, ${seat || ''}`;
                    document.getElementById('cart-location-text').textContent = locationText;
                    document.getElementById('desktop-cart-location-text').textContent = locationText;
                    document.getElementById('selected-seat-hero').textContent = locationText;
                }
                document.getElementById('seat-status-pill').textContent = "Configured";
                document.getElementById('seat-status-pill').className = "text-[9px] bg-brand-emerald/20 text-brand-emerald px-2.5 py-0.5 rounded-full font-bold border border-brand-emerald/30";
            } catch (e) {
                console.error("Error recovering seat/location:", e);
            }
        }

        pollingInterval = setInterval(syncActiveOrder, 2000);
        checkPWAPrompt();
    });

    function updateAuthHeader() {
        const authButtonsContainer = document.getElementById('header-auth-buttons');
        const userBadge = document.getElementById('header-user-badge');
        const userNameText = document.getElementById('header-user-name');
        const sidebarWelcome = document.getElementById('cust-user-name');

        if (currentUser) {
            if (userBadge) {
                userBadge.classList.remove('hidden');
                userNameText.textContent = currentUser.name;
            }
            if (sidebarWelcome) {
                sidebarWelcome.textContent = currentUser.name;
            }
            if (authButtonsContainer) {
                authButtonsContainer.innerHTML = `
                        <button onclick="logoutCustomer()" class="px-4 py-2 text-[10px] font-bold text-zinc-500 hover:text-zinc-700 bg-white border border-[#E2E8F0] rounded-full transition cursor-pointer focus:outline-none">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
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
            if (authButtonsContainer) {
                authButtonsContainer.innerHTML = `
                        <button onclick="openAuthModal()" class="px-5 py-2.5 text-[10px] font-black text-[#2D3748] bg-[#FFC244] hover:bg-[#E0A325] border border-[#E0A325] rounded-full transition shadow-sm cursor-pointer focus:outline-none">
                            Log In
                        </button>
                    `;
            }
        }
    }

    async function loadActiveEvent() {
        try {
            const res = await fetch(`${API_BASE}/events/active`);
            if (res.ok) {
                activeEvent = await res.json();
                document.getElementById('live-event-banner').innerHTML = `<i class="fas fa-ticket text-brand-rose mr-1"></i> ${activeEvent.name} — @${activeEvent.venue.name}`;
            }
        } catch (e) {
        }
    }

    async function loadVendors() {
        try {
            const res = await fetch(`${API_BASE}/vendors`);
            if (res.ok) {
                vendors = await res.json();
                renderVendors();
            }
        } catch (e) {
        }
    }

    let selectedCategory = 'all';

    function getProductCategory(p) {
        const name = p.name.toLowerCase();
        if (name.includes('beer') || name.includes('lager') || name.includes('coca') || name.includes('coke') || name.includes('soda') || name.includes('drink') || name.includes('water') || name.includes('juice')) {
            return 'drinks';
        }
        if (name.includes('fries') || name.includes('churros') || name.includes('nachos') || name.includes('chips') || name.includes('onion rings')) {
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
                // Active pill: dark filled
                btn.classList.add('bg-[#2D3748]', 'text-white', 'border-[#2D3748]', 'border-2');
                btn.classList.remove('bg-white', 'text-[#2D3748]', 'border-[#E2E8F0]', 'border');
            } else {
                // Inactive pill: light
                btn.classList.remove('bg-[#2D3748]', 'text-white', 'border-[#2D3748]', 'border-2');
                btn.classList.add('bg-white', 'text-[#2D3748]', 'border-[#E2E8F0]', 'border');
            }
        });

        renderVendors();
    }

    function renderVendors() {
        const container = document.getElementById('vendor-list-container');
        container.innerHTML = '';

        const searchVal = document.getElementById('menu-search').value.toLowerCase();

        vendors.forEach(vendor => {
            const matchingProducts = vendor.products.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(searchVal) || p.description.toLowerCase().includes(searchVal);
                const matchesCategory = selectedCategory === 'all' || getProductCategory(p) === selectedCategory;
                return matchesSearch && matchesCategory;
            });

            if (matchingProducts.length === 0) return;

            const stallSection = document.createElement('div');
            stallSection.className = 'space-y-4 mb-8';

            let rating = "4.8";
            let deliveryTime = "10-15 min";
            let coverImg = 'bg-gradient-to-r from-[#FFC244]/20 to-[#FFD885]/10';

            if (vendor.id === 1) {
                rating = "4.9";
                deliveryTime = "8-12 min";
                coverImg = 'bg-gradient-to-r from-[#FFC244]/20 to-[#E0A325]/20';
            } else if (vendor.id === 2) {
                rating = "4.7";
                deliveryTime = "12-18 min";
                coverImg = 'bg-gradient-to-r from-[#00A082]/10 to-[#008A70]/20';
            }

            const headerCard = `
    <div class="vendor-showcase-card group relative overflow-hidden rounded-[28px] bg-white border border-[#E8EDF0] shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_18px_45px_rgba(15,23,42,0.10)] transition-all duration-300">

        <!-- Top visual area -->
        <div class="relative h-[150px] md:h-[170px] overflow-hidden bg-[#F6F7F2]">

            <!-- Soft food/event background -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#071827] via-[#065F46] to-[#00A86B]"></div>

            <!-- Pattern overlay -->
            <div class="absolute inset-0 opacity-[0.14]" style="background-image: radial-gradient(circle at 20% 20%, #ffffff 0 2px, transparent 2px), radial-gradient(circle at 80% 50%, #ffffff 0 2px, transparent 2px); background-size: 34px 34px;"></div>

            <!-- Large faded food mark -->
            <div class="absolute right-7 top-4 text-[90px] opacity-20 blur-[0.2px] scale-110 group-hover:scale-125 transition duration-500">
                ${vendor.logo_url || '🍔'}
            </div>

            <!-- Vendor status pills -->
            <div class="absolute top-4 left-4 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 bg-[#FFC244] text-[#071827] px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wide shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00A082]"></span>
                    Open now
                </span>

                <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-md text-white px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wide border border-white/15">
                    Near Gate B
                </span>
            </div>

            <!-- CTA -->
            <button onclick="openSeatModal()" class="absolute top-4 right-4 bg-white text-[#071827] hover:bg-[#FFC244] px-4 py-2 rounded-full text-[10px] font-black shadow-lg transition flex items-center gap-1.5">
                <i class="fas fa-location-dot text-[#00A082]"></i>
                Seat zone
            </button>
        </div>

        <!-- Bottom vendor info -->
        <div class="relative px-5 pb-5 pt-0">

            <!-- Logo -->
            <div class="-mt-10 flex items-end justify-between gap-4">
                <div class="flex items-end gap-4">
                    <div class="w-[82px] h-[82px] rounded-[24px] bg-white border border-[#E8EDF0] shadow-[0_12px_30px_rgba(15,23,42,0.12)] flex items-center justify-center text-4xl">
                        ${vendor.logo_url || '🍔'}
                    </div>

                    <div class="pb-2">
                        <h4 class="text-[20px] md:text-[22px] font-black text-[#071827] tracking-tight leading-tight">
                            ${vendor.business_name}
                        </h4>

                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-[#071827]">
                                <i class="fas fa-star text-[#FFC244]"></i>
                                ${rating}
                            </span>

                            <span class="w-1 h-1 rounded-full bg-zinc-300"></span>

                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#64748B]">
                                <i class="far fa-clock"></i>
                                ${deliveryTime}
                            </span>

                            <span class="w-1 h-1 rounded-full bg-zinc-300"></span>

                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#00A082]">
                                <i class="fas fa-person-running"></i>
                                Seat runners ready
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini info strip -->
            <div class="mt-5 grid grid-cols-3 gap-2">
                <div class="rounded-2xl bg-[#F8FAFC] border border-[#EEF2F6] px-3 py-3">
                    <p class="text-[8px] uppercase tracking-wider text-[#94A3B8] font-black">Minimum</p>
                    <p class="text-[11px] font-black text-[#071827]">Ksh 300</p>
                </div>

                <div class="rounded-2xl bg-[#F8FAFC] border border-[#EEF2F6] px-3 py-3">
                    <p class="text-[8px] uppercase tracking-wider text-[#94A3B8] font-black">Delivery</p>
                    <p class="text-[11px] font-black text-[#071827]">To seat</p>
                </div>

                <div class="rounded-2xl bg-[#F8FAFC] border border-[#EEF2F6] px-3 py-3">
                    <p class="text-[8px] uppercase tracking-wider text-[#94A3B8] font-black">Payment</p>
                    <p class="text-[11px] font-black text-[#071827]">M-Pesa</p>
                </div>
            </div>
        </div>
    </div>
`;

            let productsHtml = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">';

            matchingProducts.forEach(p => {
                const out = p.stock_status !== 'in_stock';
                let imageTag = '';
                if (p.image_url && p.image_url.startsWith('/')) {
                    imageTag = `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">`;
                } else {
                    const gradient = p.image_url || 'bg-gradient-to-br from-amber-400 to-red-500';
                    imageTag = `
                            <div class="w-full h-full ${gradient} flex flex-col items-center justify-center text-white p-3 text-center">
                                <span class="text-xl font-black uppercase tracking-wider">${p.name.substring(0, 2)}</span>
                            </div>
                        `;
                }

                productsHtml += `
                        <div class="group bg-white rounded-[24px] border border-[#E2E8F0] overflow-hidden shadow-sm hover:shadow-md transition duration-300 flex flex-col h-full">
                            <div class="h-36 relative overflow-hidden bg-zinc-100 flex-shrink-0">
                                ${imageTag}
                                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-[9px] font-black text-[#2D3748] px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm border border-zinc-150">
                                    ${getProductCategory(p)}
                                </span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <h5 class="text-xs font-black text-[#2D3748] tracking-tight group-hover:text-[#00A082] transition duration-200 ${out ? 'text-zinc-400 line-through' : ''}">${p.name}</h5>
                                    <p class="text-[10px] text-zinc-500 line-clamp-2 leading-relaxed mt-1 font-medium">${p.description || 'Delivered fresh and hot to your exact stadium seat location coordinates.'}</p>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div>
                                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 font-bold">Price</p>
                                        <p class="text-xs font-black text-[#00A082]">Ksh ${parseFloat(p.price).toLocaleString()}</p>
                                    </div>
                                    <div>
                                        ${out
                    ? `<span class="text-[8px] bg-zinc-100 border border-zinc-200 text-zinc-400 px-3 py-1.5 rounded-full font-bold">Out of stock</span>`
                    : `<button onclick="addToBasket(${p.id}, '${p.name}', ${p.price}, ${vendor.id})" class="w-9 h-9 rounded-full bg-[#FFC244] hover:bg-[#E0A325] text-[#2D3748] flex items-center justify-center font-black transition-all shadow-md shadow-[#FFC244]/20 border border-[#E0A325] cursor-pointer group-hover:scale-105">
                                                    <i class="fas fa-plus text-xs"></i>
                                               </button>`
                }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            });

            productsHtml += '</div>';

            stallSection.innerHTML = headerCard + productsHtml;
            container.appendChild(stallSection);
        });
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
        if (basket.length > 0 && basket[0].vendorId !== vendorId) {
            const confirm = await showConfirmModal(
                "Switch Vendor?",
                "To keep delivery fast, you can only order from one vendor stall at a time. Adding this item will clear your current basket.",
                "Clear & Add",
                "Cancel"
            );
            if (!confirm) return;
            basket = [];
        }
        const existing = basket.find(item => item.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            basket.push({id, name, price, quantity: 1, vendorId});
        }
        renderBasket();
    }

    function renderBasket() {
        // Mobile Basket Tray
        const mobileTray = document.getElementById('phone-cart-tray');
        const mobileContainer = document.getElementById('cart-tray-items');

        // Desktop Basket Card
        const desktopContainer = document.getElementById('desktop-cart-tray-items');

        const hasItems = basket.length > 0;

        // Mobile Tray visibility
        if (!hasItems) {
            mobileTray.classList.add('hidden');
            mobileContainer.innerHTML = '';
            desktopContainer.innerHTML = '<p class="text-xs text-zinc-500 text-center py-6">Your basket is empty. Add food from stalls to get started!</p>';
            document.getElementById('cart-tray-total').textContent = 'Ksh 0.00';
            document.getElementById('desktop-cart-tray-total').textContent = 'Ksh 0.00';
            return;
        }

        // Show mobile tray if on mobile
        mobileTray.classList.remove('hidden');

        mobileContainer.innerHTML = '';
        desktopContainer.innerHTML = '';

        let total = 0;

        basket.forEach(item => {
            total += item.price * item.quantity;

            // Create item for mobile tray
            const mDiv = document.createElement('div');
            mDiv.className = 'flex justify-between items-center text-xs py-2 border-b border-[#E2E8F0]';
            mDiv.innerHTML = `
                    <div class="flex-1">
                        <p class="font-bold text-[#2D3748]">${item.name}</p>
                        <p class="text-[9px] text-zinc-500">Ksh ${item.price.toLocaleString()} each</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="adjustQty(${item.id}, -1)" class="w-6 h-6 rounded-full bg-[#FFC244]/15 hover:bg-[#FFC244]/30 flex items-center justify-center text-xs text-[#2D3748] font-extrabold transition">-</button>
                        <span class="text-xs font-black text-[#2D3748]">${item.quantity}</span>
                        <button onclick="adjustQty(${item.id}, 1)" class="w-6 h-6 rounded-full bg-[#FFC244]/15 hover:bg-[#FFC244]/30 flex items-center justify-center text-xs text-[#2D3748] font-extrabold transition">+</button>
                    </div>
                `;
            mobileContainer.appendChild(mDiv);

            // Create item for desktop card
            const dDiv = document.createElement('div');
            dDiv.className = 'flex justify-between items-center text-xs py-2 border-b border-[#E2E8F0]';
            dDiv.innerHTML = `
                    <div class="flex-1">
                        <p class="font-bold text-[#2D3748]">${item.name}</p>
                        <p class="text-[9px] text-zinc-500">Ksh ${item.price.toLocaleString()} each</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="adjustQty(${item.id}, -1)" class="w-6 h-6 rounded-full bg-[#FFC244]/15 hover:bg-[#FFC244]/30 flex items-center justify-center text-xs text-[#2D3748] font-extrabold transition">-</button>
                        <span class="text-xs font-black text-[#2D3748]">${item.quantity}</span>
                        <button onclick="adjustQty(${item.id}, 1)" class="w-6 h-6 rounded-full bg-[#FFC244]/15 hover:bg-[#FFC244]/30 flex items-center justify-center text-[#2D3748] font-extrabold transition">+</button>
                    </div>
                `;
            desktopContainer.appendChild(dDiv);
        });

        const totalText = `Ksh ${total.toLocaleString()}`;
        document.getElementById('cart-tray-total').textContent = totalText;
        document.getElementById('desktop-cart-tray-total').textContent = totalText;
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

    async function sendOTP() {
        const phone = document.getElementById('cust-phone-input').value;
        if (!phone) {
            alert("Please enter phone number!");
            return;
        }
        try {
            const res = await fetch(`${API_BASE}/auth/login`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({phone})
            });
            if (res.ok) {
                playSound('beep');
                document.getElementById('otp-phone-text').textContent = `Verification SMS sent to ${phone}`;
                document.getElementById('cust-auth').classList.add('hidden');
                document.getElementById('cust-otp').classList.remove('hidden');
            }
        } catch (e) {
        }
    }

    async function verifyOTP() {
        const phone = document.getElementById('cust-phone-input').value;
        const code = document.getElementById('cust-otp-input').value;
        try {
            const res = await fetch(`${API_BASE}/auth/verify`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({phone, code})
            });
            const data = await res.json();
            if (res.ok) {
                playSound('success');
                currentUser = data.user;
                localStorage.setItem('justfeast_client_user', JSON.stringify(currentUser));
                updateAuthHeader();
                closeAuthModal();
                checkActiveOrderOnLogin();

                // Auto checkout if basket & seat are set
                if (basket.length > 0 && selectedSeat) {
                    checkoutOrder();
                }
            } else {
                alert(data.message);
            }
        } catch (e) {
        }
    }

    async function quickLogin(email) {
        try {
            const res = await fetch(`${API_BASE}/auth/login-as`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email})
            });
            const data = await res.json();
            if (res.ok) {
                playSound('success');
                currentUser = data.user;
                localStorage.setItem('justfeast_client_user', JSON.stringify(currentUser));
                updateAuthHeader();
                closeAuthModal();
                checkActiveOrderOnLogin();

                // Auto checkout if basket & seat are set
                if (basket.length > 0 && selectedSeat) {
                    checkoutOrder();
                }
            }
        } catch (e) {
        }
    }

    function logoutCustomer() {
        currentUser = null;
        localStorage.removeItem('justfeast_admin_user');
        localStorage.removeItem('justfeast_vendor_user');
        localStorage.removeItem('justfeast_runner_user');
        localStorage.removeItem('justfeast_client_user');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    function openAuthModal() {
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
            const center = [-1.32588, 36.79941];
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
                    alert("Error getting location: " + err.message + ". Center map marker used instead.");
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
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

    function checkoutOrder() {
        if (basket.length === 0) {
            alert("Please add items to your basket first!");
            return;
        }
        if (!selectedSeat) {
            alert("Please configure your delivery location on the map first!");
            openSeatModal();
            return;
        }
        if (!currentUser) {
            openAuthModal();
            return;
        }

        let total = 0;
        basket.forEach(i => total += i.price * i.quantity);

        document.getElementById('mpesa-amount').textContent = `Ksh ${total.toLocaleString()}`;
        document.getElementById('mpesa-simulation-overlay').classList.remove('hidden');
    }

    function selectPayTab(method) {
        const mpesaTab = document.getElementById('pay-mpesa-tab');
        const farajaTab = document.getElementById('pay-faraja-tab');
        if (method === 'mpesa') {
            mpesaTab.classList.add('border-[#00A082]', 'bg-[#00A082]/5');
            mpesaTab.classList.remove('border-transparent', 'bg-zinc-100');
            farajaTab.classList.add('border-transparent', 'bg-zinc-100');
            farajaTab.classList.remove('border-[#00A082]', 'bg-[#00A082]/5');
        } else {
            farajaTab.classList.add('border-[#00A082]', 'bg-[#00A082]/5');
            farajaTab.classList.remove('border-transparent', 'bg-zinc-100');
            mpesaTab.classList.add('border-transparent', 'bg-zinc-100');
            mpesaTab.classList.remove('border-[#00A082]', 'bg-[#00A082]/5');
        }
    }

    function cancelMpesaSimulation() {
        document.getElementById('mpesa-simulation-overlay').classList.add('hidden');
    }

    async function confirmMpesaSimulation() {
        const pin = document.getElementById('mpesa-pin-input').value;
        if (!pin || pin.length < 4) {
            alert("Please enter 4-digit PIN");
            return;
        }

        const modalContainer = document.querySelector('#mpesa-simulation-overlay .phone-buzz');
        const originalContentHtml = modalContainer.innerHTML;

        modalContainer.innerHTML = `
                <div class="text-center space-y-6 py-4">
                    <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
                        <div class="absolute inset-0 border-4 border-[#00A082]/10 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-[#00A082] border-t-transparent rounded-full animate-spin"></div>
                        <i class="fas fa-fingerprint text-2xl text-[#00A082]"></i>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-xs font-black text-[#2D3748] tracking-wider uppercase">Authenticating PIN...</h4>
                        <p class="text-[10px] text-zinc-500 max-w-[220px] mx-auto leading-relaxed">
                            Securing transaction with Safaricom Daraja. Please wait while we process your request.
                        </p>
                    </div>
                </div>
            `;

        await new Promise(resolve => setTimeout(resolve, 2000));

        const payload = {
            user_id: currentUser.id,
            vendor_id: basket[0].vendorId,
            seat_location: selectedSeat,
            items: basket.map(i => ({product_id: i.id, quantity: i.quantity}))
        };

        try {
            const res = await fetch(`${API_BASE}/orders`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok) {
                const payRes = await fetch(`${API_BASE}/orders/${data.order.id}/pay`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({phone: currentUser.phone})
                });
                if (payRes.ok) {
                    playSound('success');
                    activeOrder = await payRes.json();
                    basket = [];
                    renderBasket();

                    document.getElementById('mpesa-simulation-overlay').classList.add('hidden');
                    modalContainer.innerHTML = originalContentHtml;

                    document.getElementById('cust-main').classList.add('hidden');
                    document.getElementById('cust-tracker').classList.remove('hidden');
                    syncActiveOrder();
                } else {
                    alert("M-Pesa transaction validation failed.");
                    modalContainer.innerHTML = originalContentHtml;
                }
            } else {
                alert(data.message);
                modalContainer.innerHTML = originalContentHtml;
            }
        } catch (e) {
            alert("An error occurred during checkout processing.");
            modalContainer.innerHTML = originalContentHtml;
        }
    }

    async function checkActiveOrderOnLogin() {
        if (!currentUser) return;
        try {
            const res = await fetch(`${API_BASE}/orders/active?user_id=${currentUser.id}`);
            if (res.ok) {
                const order = await res.json();
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
            const res = await fetch(`${API_BASE}/orders/${activeOrder.order.id}`);
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
            alert("🎉 Order successfully delivered to your seat! Enjoy the concert!");
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

    function renderVendors() {
        const container = document.getElementById('vendor-list-container');
        if (!container) return;
        container.innerHTML = '';
        const searchEl = document.getElementById('menu-search');
        const searchVal = searchEl ? searchEl.value.toLowerCase() : '';
        let renderedCount = 0;

        vendors.forEach((vendor, index) => {
            const products = (vendor.products || []).filter(p => {
                const desc = (p.description || '').toLowerCase();
                const name = (p.name || '').toLowerCase();
                const matchesSearch = name.includes(searchVal) || desc.includes(searchVal) || (vendor.business_name || '').toLowerCase().includes(searchVal);
                const matchesCategory = selectedCategory === 'all' || getProductCategory(p) === selectedCategory;
                return matchesSearch && matchesCategory;
            });
            if (!products.length) return;
            renderedCount++;

            const vendorEmoji = vendor.logo_url || ['🍔', '🥤', '🍿', '🌮', '🍟'][index % 5];
            const rating = (4.9 - (index * 0.08)).toFixed(1);
            const etaMin = 7 + (index % 3) * 2;
            const etaMax = etaMin + 6;
            const zone = index % 2 === 0 ? 'Near Gate B' : 'Near VIP concourse';

            const section = document.createElement('section');
            section.className = 'space-y-4';

            const header = `
                    <div class="group relative overflow-hidden rounded-[34px] bg-white border border-[#E7E8DD] shadow-card">
                        <div class="absolute inset-x-0 top-0 h-28 bg-gradient-to-r from-[#111827] via-[#047A43] to-[#05A357]"></div>
                        <div class="absolute right-8 top-5 text-7xl opacity-10 text-white">${vendorEmoji}</div>
                        <div class="relative p-5 md:p-6 pt-16 flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <div class="flex items-end gap-4">
                                <div class="w-20 h-20 rounded-[28px] bg-white shadow-soft border border-[#E7E8DD] flex items-center justify-center text-4xl flex-shrink-0">${vendorEmoji}</div>
                                <div class="pb-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span class="bg-[#FFC244] text-[#111827] text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full">Open now</span>
                                        <span class="bg-[#05A357]/10 text-[#05A357] border border-[#05A357]/20 text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full">${zone}</span>
                                    </div>
                                    <h3 class="text-xl md:text-2xl font-black tracking-tight text-[#111827]">${vendor.business_name}</h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] font-black text-zinc-500">
                                        <span><i class="fas fa-star text-[#FFC244]"></i> ${rating}</span>
                                        <span><i class="far fa-clock text-[#05A357]"></i> ${etaMin}-${etaMax} min</span>
                                        <span><i class="fas fa-person-running text-[#05A357]"></i> Seat runners ready</span>
                                    </div>
                                </div>
                            </div>
                            <button onclick="openSeatModal()" class="bg-[#111827] hover:bg-[#05A357] text-white rounded-full px-5 py-3 text-xs font-black self-start md:self-end">Check seat zone</button>
                        </div>
                    </div>`;

            let productGrid = '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">';
            products.forEach((p, pIndex) => {
                const out = p.stock_status !== 'in_stock';
                let visual = '';
                if (p.image_url && p.image_url.startsWith('/')) {
                    visual = `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${p.name}">`;
                } else {
                    const emoji = getProductCategory(p) === 'drinks' ? '🥤' : getProductCategory(p) === 'snacks' ? '🍿' : ['🍔', '🌮', '🍟', '🍗'][pIndex % 4];
                    visual = `<div class="w-full h-full bg-gradient-to-br from-[#FFF8E7] via-white to-[#E9F7EE] flex items-center justify-center"><span class="text-7xl drop-shadow-sm">${emoji}</span></div>`;
                }
                const safeName = String(p.name || '').replace(/'/g, "\\'");
                const category = getProductCategory(p);
                productGrid += `
                        <article class="group bg-white rounded-[30px] border border-[#E7E8DD] overflow-hidden shadow-card hover:shadow-soft hover:-translate-y-1 transition duration-300 flex flex-col">
                            <div class="relative h-44 overflow-hidden bg-[#F6F7F2]">
                                ${visual}
                                <span class="absolute top-3 left-3 bg-white/95 backdrop-blur text-[9px] font-black text-[#111827] px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm border border-white/70">${category}</span>
                                <span class="absolute bottom-3 right-3 bg-[#111827] text-white text-[10px] font-black px-3 py-1.5 rounded-full">Fast seat drop</span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-black tracking-tight text-[#111827] group-hover:text-[#05A357] ${out ? 'line-through text-zinc-400' : ''}">${p.name}</h4>
                                    <p class="text-[11px] text-zinc-500 leading-relaxed mt-1 line-clamp-2 font-medium">${p.description || 'Prepared fresh by an approved event vendor and delivered directly to your selected seat.'}</p>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div><p class="text-[9px] uppercase tracking-wider text-zinc-400 font-black">Price</p><p class="text-lg font-black text-[#05A357]">Ksh ${parseFloat(p.price).toLocaleString()}</p></div>
                                    ${out
                    ? `<span class="text-[9px] bg-zinc-100 border border-zinc-200 text-zinc-400 px-3 py-2 rounded-full font-black">Out of stock</span>`
                    : (() => {
                        const bItem = basket.find(item => item.id === p.id);
                        if (bItem && bItem.quantity > 0) {
                            return `
                            <div class="flex items-center bg-[#FFC244] rounded-full h-11 px-1.5 border border-[#efb52e] shadow-card">
                                <button onclick="adjustQty(${p.id}, -1)" class="w-8 h-8 rounded-full bg-white hover:bg-zinc-100 flex items-center justify-center font-black text-xs text-[#111827] transition border-0 cursor-pointer"><i class="fas fa-minus"></i></button>
                                <span class="px-3 text-xs font-black text-[#111827] min-w-[20px] text-center">${bItem.quantity}</span>
                                <button onclick="adjustQty(${p.id}, 1)" class="w-8 h-8 rounded-full bg-white hover:bg-zinc-100 flex items-center justify-center font-black text-xs text-[#111827] transition border-0 cursor-pointer"><i class="fas fa-plus"></i></button>
                            </div>
                            `;
                        } else {
                            return `<button onclick="addToBasket(${p.id}, '${safeName}', ${p.price}, ${vendor.id})" class="h-11 px-4 rounded-full bg-[#FFC244] hover:bg-[#111827] text-[#111827] hover:text-white flex items-center justify-center font-black transition-all shadow-card border border-[#efb52e] text-xs gap-2 cursor-pointer"><i class="fas fa-plus"></i> Add</button>`;
                        }
                    })()}
                                </div>
                            </div>
                        </article>`;
            });
            productGrid += '</div>';
            section.innerHTML = header + productGrid;
            container.appendChild(section);
        });

        if (!renderedCount) {
            container.innerHTML = `<div class="bg-white border border-[#E7E8DD] rounded-[32px] p-10 text-center shadow-card"><div class="text-5xl mb-3">🔎</div><h3 class="text-xl font-black">No matching vendors</h3><p class="text-sm text-zinc-500 font-bold mt-1">Try another search or category.</p></div>`;
        }
    }

    function renderBasket() {
        const mobileTray = document.getElementById('phone-cart-tray');
        const mobileContainer = document.getElementById('cart-tray-items');
        const desktopContainer = document.getElementById('desktop-cart-tray-items');
        const hasItems = basket.length > 0;
        if (!hasItems) {
            mobileTray.classList.add('hidden');
            if (mobileContainer) mobileContainer.innerHTML = '';
            if (desktopContainer) desktopContainer.innerHTML = '<div class="text-center py-7 space-y-2"><div class="text-4xl">🛒</div><p class="text-xs text-zinc-400 font-bold">Your basket is empty.<br>Add food from vendors.</p></div>';
            document.getElementById('cart-tray-total').textContent = 'Ksh 0';
            document.getElementById('desktop-cart-tray-total').textContent = 'Ksh 0';
            return;
        }
        mobileTray.classList.remove('hidden');
        mobileContainer.innerHTML = '';
        desktopContainer.innerHTML = '';
        let total = 0;
        basket.forEach(item => {
            total += item.price * item.quantity;
            const row = `
                    <div class="flex justify-between items-center gap-3 text-xs py-3 border-b border-[#E7E8DD] last:border-b-0">
                        <div class="flex-1 min-w-0"><p class="font-black text-[#111827] truncate">${item.name}</p><p class="text-[10px] text-zinc-500 font-bold">Ksh ${item.price.toLocaleString()} each</p></div>
                        <div class="flex items-center gap-2 bg-[#F6F7F2] rounded-full p-1">
                            <button onclick="adjustQty(${item.id}, -1)" class="w-7 h-7 rounded-full bg-white hover:bg-[#FFC244] flex items-center justify-center text-xs text-[#111827] font-black shadow-sm">-</button>
                            <span class="w-5 text-center text-xs font-black">${item.quantity}</span>
                            <button onclick="adjustQty(${item.id}, 1)" class="w-7 h-7 rounded-full bg-white hover:bg-[#FFC244] flex items-center justify-center text-xs text-[#111827] font-black shadow-sm">+</button>
                        </div>
                    </div>`;
            mobileContainer.insertAdjacentHTML('beforeend', row);
            desktopContainer.insertAdjacentHTML('beforeend', row);
        });
        const totalText = `Ksh ${total.toLocaleString()}`;
        document.getElementById('cart-tray-total').textContent = totalText;
        document.getElementById('desktop-cart-tray-total').textContent = totalText;
    }
</script>
</body>
</html>
