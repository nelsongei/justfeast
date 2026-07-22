<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast — Concert Seat Food Delivery Platform</title>
    <!-- Web App & PWA Metadata -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f43f5e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet Map CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            rose: '#f43f5e',
                            orange: '#f97316',
                            amber: '#f59e0b',
                            emerald: '#10b981',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Premium Aesthetics & Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(24, 24, 27, 0.5);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.6);
        }

        .glass-card {
            background: rgba(24, 24, 27, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .glass-input {
            background: rgba(9, 9, 11, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 10px rgba(139, 92, 246, 0.2);
            outline: none;
        }

        /* Ambient animations */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 0.4; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        .pulse-ring {
            animation: pulse-ring 2s infinite ease-out;
        }

        @keyframes spotlight {
            0%, 100% { transform: rotate(-5deg); opacity: 0.3; }
            50% { transform: rotate(5deg); opacity: 0.6; }
        }
        .spotlight {
            animation: spotlight 6s infinite ease-in-out;
            transform-origin: top center;
        }

        /* Shaking phone animation */
        @keyframes buzz {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            10%, 30%, 50%, 70%, 90% { transform: translate(-2px, 2px) rotate(-1deg); }
            20%, 40%, 60%, 80% { transform: translate(2px, -2px) rotate(1deg); }
        }
        .phone-buzz {
            animation: buzz 0.5s ease-in-out 3;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen overflow-x-hidden relative">

    <!-- Glowing Background blobs -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-brand-rose/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-brand-pink/10 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- PWA Installation Banner -->
    <div id="pwa-install-banner" class="hidden bg-gradient-to-r from-brand-rose to-brand-orange px-4 py-2 text-center text-sm font-semibold flex items-center justify-between shadow-lg relative z-50">
        <span>✨ Experience justFeast Seat Delivery as a full PWA app!</span>
        <div class="flex items-center gap-2">
            <button onclick="installPWA()" class="bg-white text-zinc-900 px-3 py-1 rounded-full text-xs font-bold hover:bg-zinc-100 transition">Install App</button>
            <button onclick="dismissPWABanner()" class="text-white hover:text-zinc-200"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-40 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="bg-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-brand-rose/20 overflow-hidden">
                <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-8 w-auto object-contain">
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-zinc-400 bg-clip-text text-transparent flex items-center gap-2">
                    JUSTFEAST <span class="text-xs bg-brand-rose/20 text-brand-rose px-2 py-0.5 rounded-full font-semibold border border-brand-rose/30">Concert Delivery PWA</span>
                </h1>
                <p class="text-xs text-zinc-500" id="live-event-banner">Loading active event...</p>
            </div>
        </div>

        <!-- Global Multi-Role Portal Switcher -->
        <div class="flex items-center gap-1 bg-zinc-900 p-1.5 rounded-xl border border-zinc-800 text-sm font-medium">
            <button onclick="switchPortal('all')" id="tab-all" class="px-3.5 py-1.5 rounded-lg transition-all duration-300 bg-brand-rose text-white shadow shadow-brand-rose/30">
                <i class="fas fa-cubes mr-1.5"></i>All-in-One
            </button>
            <button onclick="switchPortal('customer')" id="tab-customer" class="px-3.5 py-1.5 rounded-lg text-zinc-400 hover:text-white transition-all duration-300">
                <i class="fas fa-mobile-screen mr-1.5"></i>Attendee
            </button>
            <button onclick="switchPortal('vendor')" id="tab-vendor" class="px-3.5 py-1.5 rounded-lg text-zinc-400 hover:text-white transition-all duration-300">
                <i class="fas fa-store mr-1.5"></i>Vendor
            </button>
            <button onclick="switchPortal('runner')" id="tab-runner" class="px-3.5 py-1.5 rounded-lg text-zinc-400 hover:text-white transition-all duration-300">
                <i class="fas fa-person-running mr-1.5"></i>Runner
            </button>
            <button onclick="switchPortal('admin')" id="tab-admin" class="px-3.5 py-1.5 rounded-lg text-zinc-400 hover:text-white transition-all duration-300">
                <i class="fas fa-sliders mr-1.5"></i>Admin
            </button>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="max-w-[1700px] mx-auto p-4 md:p-6 pb-24">

        <!-- SIDE-BY-SIDE SIMULATOR VIEW (DEFAULT) -->
        <div id="portal-all-grid" class="grid grid-cols-1 xl:grid-cols-4 gap-6">

            <!-- PORTAL A: CUSTOMER PANEL -->
            <div id="col-customer" class="xl:col-span-1 glass-card p-5 rounded-3xl border border-zinc-800 flex flex-col h-[780px] relative overflow-hidden">
                <div class="pb-3 border-b border-zinc-800 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="bg-white w-7 h-7 rounded-lg flex items-center justify-center border border-zinc-200 shadow-sm overflow-hidden">
                            <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-5 w-auto object-contain">
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-white">justFeast</h2>
                            <p class="text-[8px] text-zinc-400">Seat Delivery</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div id="header-user-badge" class="hidden flex items-center gap-1 bg-zinc-950 px-2 py-0.5 rounded-full border border-zinc-800">
                            <span class="w-1 h-1 rounded-full bg-[#00A082] animate-pulse"></span>
                            <span class="text-[8px] font-bold text-zinc-300" id="header-user-name">Guest</span>
                        </div>
                        <div id="header-auth-buttons">
                            <!-- Toggle Login/Logout -->
                        </div>
                        <span class="text-[9px] text-zinc-500 font-semibold"><i class="fas fa-signal text-[#00A082]"></i> 5G</span>
                    </div>
                </div>
 
                <!-- Customer Phone Inner Content Container -->
                <div id="customer-phone-body" class="flex-1 overflow-y-auto pr-1 relative">
                        <!-- Authentication Modal Overlay (OTP Login) -->
                        <div id="auth-modal-overlay" class="hidden absolute inset-0 bg-black/85 backdrop-blur-sm z-50 flex items-center justify-center p-3">
                            <!-- Auth Screen -->
                            <div id="cust-auth" class="w-full bg-[#1C1C24] border border-zinc-800 rounded-3xl p-5 text-center space-y-5 relative shadow-2xl">
                                <button onclick="closeAuthModal()" class="absolute top-3 right-3 text-zinc-500 hover:text-zinc-300 text-xs"><i class="fas fa-times"></i></button>
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-zinc-150/15 border border-zinc-200 overflow-hidden">
                                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-9 w-auto object-contain">
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-black text-white">Concert Location Delivery</h3>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed font-medium">Get food, drinks, and snacks delivered directly to your location during the concert!</p>
                                </div>
                                <div class="space-y-3 text-left">
                                    <div>
                                        <label class="block text-[8px] font-black text-zinc-400 mb-1 uppercase tracking-wider">Enter Phone Number</label>
                                        <input type="text" id="cust-phone-input" placeholder="e.g. 0712345678" class="w-full px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-white text-xs focus:outline-none focus:border-[#00A082]" value="0712345678">
                                    </div>
                                    <button onclick="sendOTP()" class="w-full py-2.5 rounded-full bg-[#00A082] text-white font-bold text-xs hover:bg-[#008A70] transition shadow-md shadow-[#00A082]/10 border-0 cursor-pointer">
                                        Send Verification OTP
                                    </button>
                                </div>
                                <div class="border-t border-zinc-800 pt-4">
                                    <span class="text-[8px] text-zinc-500 block mb-2 font-semibold uppercase tracking-wider">Or instantly access:</span>
                                    <button onclick="quickLogin('customer@justfeast.com')" class="w-full py-2 rounded-full bg-[#FFC244] hover:bg-[#E0A325] text-[#2D3748] text-xs font-bold transition shadow-sm border border-[#E0A325] cursor-pointer">
                                        🚀 Access as John Customer
                                    </button>
                                </div>
                            </div>
 
                            <!-- OTP Screen -->
                            <div id="cust-otp" class="hidden w-full bg-[#1C1C24] border border-zinc-800 rounded-3xl p-5 text-center space-y-5 relative shadow-2xl">
                                <button onclick="closeAuthModal()" class="absolute top-3 right-3 text-zinc-500 hover:text-zinc-300 text-xs"><i class="fas fa-times"></i></button>
                                <h3 class="text-sm font-black text-white">Confirm OTP</h3>
                                <p class="text-[9px] text-zinc-400" id="otp-phone-text">We sent a verification SMS to your phone</p>
                                <div class="space-y-3 text-left">
                                    <div>
                                        <label class="block text-[8px] font-black text-zinc-400 mb-1 uppercase tracking-wider">Enter 4-Digit Code</label>
                                        <input type="text" id="cust-otp-input" placeholder="Enter 1234" class="w-full px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-white text-center text-base tracking-widest font-bold focus:outline-none focus:border-[#00A082]" value="1234">
                                    </div>
                                    <button onclick="verifyOTP()" class="w-full py-2.5 rounded-full bg-[#00A082] text-white font-bold text-xs hover:bg-[#008A70] transition shadow-md shadow-[#00A082]/10 border-0 cursor-pointer">
                                        Verify Code
                                    </button>
                                    <button onclick="showAuthScreen()" class="w-full py-1.5 rounded-full text-zinc-500 hover:text-zinc-300 text-[10px] font-bold text-center transition">
                                        Go Back
                                    </button>
                                </div>
                            </div>
                        </div>
 
                        <!-- Marketplace / Seating / Menu Portal -->
                        <div id="cust-main" class="space-y-5">
                            <!-- Glovo Hero Landing Fold inside simulator phone -->
                            <div id="glovo-hero-fold" class="text-center py-5 px-3 relative overflow-hidden bg-gradient-to-b from-[#FFC244]/15 to-transparent rounded-2xl border border-[#FFC244]/10 mb-2">
                                <h3 class="text-sm font-black text-white leading-tight">What shall we deliver to you?</h3>
                                <p class="text-[8px] text-zinc-400 mt-1 max-w-[200px] mx-auto leading-relaxed">Skip the stadium queues. Order snacks delivered directly to your location!</p>
                                
                                <!-- Address search capsule -->
                                <div onclick="openSeatModal()" class="mt-3 flex items-center bg-white rounded-full border border-[#E2E8F0] shadow-md p-1 hover:border-[#00A082] transition duration-200 cursor-pointer group">
                                    <span class="pl-2 pr-1 text-[#00A082] text-xs"><i class="fas fa-location-dot"></i></span>
                                    <div class="flex-1 text-left min-w-0">
                                        <p class="text-[7px] uppercase tracking-wider text-zinc-400 font-bold">Delivery Location</p>
                                        <p class="text-[9px] font-black text-[#2D3748] truncate" id="selected-seat-hero">Select Delivery Location...</p>
                                    </div>
                                    <span class="bg-[#00A082] text-white px-3 py-1 rounded-full text-[8px] font-black">Select</span>
                                </div>
                            </div>

                            <!-- User Welcome Segment -->
                            <div class="flex justify-between items-center bg-zinc-900/40 p-3 rounded-xl border border-zinc-900">
                                <div>
                                    <p class="text-[10px] text-zinc-500 font-semibold uppercase">Attendee</p>
                                    <h4 class="text-xs font-bold text-white" id="cust-user-name">John Customer</h4>
                                </div>
                                <button onclick="logoutCustomer()" class="text-zinc-500 hover:text-zinc-400 text-xs"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </div>

                            <!-- STEP A: LOCATION CONFIGURATION -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">Configure Delivery Location</label>
                                    <span id="seat-status-pill" class="text-[10px] bg-brand-orange/20 text-brand-orange px-2 py-0.5 rounded-full font-semibold border border-brand-pink/20">Not Set</span>
                                </div>

                                <button onclick="openSeatModal()" class="w-full py-3 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between px-4 hover:border-brand-rose transition">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-map-location-dot text-brand-rose text-sm"></i>
                                        <div class="text-left">
                                            <p class="text-xs font-bold" id="selected-seat-label">Configure Delivery Location</p>
                                            <p class="text-[10px] text-zinc-500" id="selected-seat-sub">Tap to pin location on map</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-zinc-600 text-xs"></i>
                                </button>
                            </div>

                            <!-- STEP B: VENDORS MARKETPLACE -->
                            <div class="space-y-3">
                                <h3 class="text-[10px] font-black text-[#00A082] uppercase tracking-wider">Browse Stalls</h3>
 
                                <!-- Search bar -->
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500"><i class="fas fa-search text-xs"></i></span>
                                    <input type="text" id="cust-menu-search" oninput="searchCustomerMenu()" placeholder="Search food, drinks..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-white focus:outline-none focus:border-[#00A082]">
                                </div>
 
                                <!-- Glovo-style Category Pills (Mini) -->
                                <div class="flex items-center gap-1.5 py-1 overflow-x-auto no-scrollbar scroll-smooth">
                                    <button onclick="setCustomerCategory('all')" id="cust-cat-all" class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#FFC244] text-[#2D3748] border border-[#FFC244] text-[9px] font-black uppercase tracking-wider whitespace-nowrap cursor-pointer focus:outline-none">
                                        <span>🏪</span>
                                        <span>All</span>
                                    </button>
                                    <button onclick="setCustomerCategory('food')" id="cust-cat-food" class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-zinc-950 text-zinc-400 border border-zinc-800 text-[9px] font-black uppercase tracking-wider whitespace-nowrap cursor-pointer focus:outline-none">
                                        <span>🍔</span>
                                        <span>Food</span>
                                    </button>
                                    <button onclick="setCustomerCategory('drinks')" id="cust-cat-drinks" class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-zinc-950 text-zinc-400 border border-zinc-800 text-[9px] font-black uppercase tracking-wider whitespace-nowrap cursor-pointer focus:outline-none">
                                        <span>🥤</span>
                                        <span>Drinks</span>
                                    </button>
                                    <button onclick="setCustomerCategory('snacks')" id="cust-cat-snacks" class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-zinc-950 text-zinc-400 border border-zinc-800 text-[9px] font-black uppercase tracking-wider whitespace-nowrap cursor-pointer focus:outline-none">
                                        <span>🍿</span>
                                        <span>Snacks</span>
                                    </button>
                                </div>
 
                                <!-- Vendors Listings -->
                                <div id="vendor-list-container" class="space-y-4">
                                    <!-- Dynamic vendors loaded here -->
                                </div>
                            </div>
                                              <!-- Active Order Tracking Radar (Pulses dynamically) -->
                        <div id="cust-tracker" class="hidden flex flex-col h-full justify-between py-2">
                            <div class="text-center">
                                <h3 class="text-base font-black text-[#2D3748] mb-0.5">Live Delivery Radar</h3>
                                <p class="text-[10px] text-zinc-500">Fast Location Delivery — Uhuru Gardens Event Park</p>
                            </div>
 
                            <!-- Simulated radar animation -->
                            <div class="relative w-44 h-44 mx-auto my-6 flex items-center justify-center bg-[#F7F9FA] rounded-full border border-[#E2E8F0]">
                                <!-- Pulsing concentric circles -->
                                <div class="absolute w-44 h-44 rounded-full border border-[#00A082]/10 pulse-ring"></div>
                                <div class="absolute w-32 h-32 rounded-full border border-[#FFC244]/20 pulse-ring" style="animation-delay: 0.5s"></div>
                                <div class="absolute w-20 h-20 rounded-full border border-[#00A082]/20 pulse-ring" style="animation-delay: 1s"></div>
 
                                <div class="w-14 h-14 bg-[#FFC244] border border-[#E0A325] rounded-full flex items-center justify-center shadow-lg relative z-10 animate-bounce">
                                    <i class="fas fa-person-running text-[#2D3748] text-xl"></i>
                                </div>
                            </div>
 
                            <!-- Secret Verification PIN Block -->
                            <div class="bg-[#F7F9FA] border border-[#E2E8F0] p-4 rounded-xl text-center space-y-1.5 relative overflow-hidden">
                                <span class="text-[9px] uppercase tracking-widest text-zinc-500 font-bold">Secure Delivery PIN</span>
                                <h4 class="text-3xl font-black text-[#00A082] tracking-widest" id="tracker-pin">----</h4>
                                <p class="text-[10px] text-zinc-500">Share this secret PIN with the runner when they arrive at your location to confirm delivery.</p>
                            </div>

                            <!-- QR Verification Code Block (Generates dynamically on arrival) -->
                            <div id="tracker-qr-container" class="hidden bg-white border border-[#E2E8F0] p-4 rounded-xl text-center space-y-2 flex flex-col items-center justify-center">
                                <span class="text-[9px] uppercase tracking-widest text-[#00A082] font-black"><i class="fas fa-qrcode mr-1"></i> Scan to Verify Delivery</span>
                                <canvas id="tracker-qr-canvas" class="w-32 h-32 border border-zinc-100 p-1 bg-white"></canvas>
                                <p class="text-[9px] text-zinc-500 font-medium leading-relaxed">Let the runner scan this QR code or type your PIN to complete the delivery.</p>
                            </div>
 
                            <!-- Live timeline tracker checklist -->
                            <div class="space-y-3 bg-[#F7F9FA] p-4 rounded-xl border border-[#E2E8F0]">
                                <div class="flex items-center gap-3" id="step-created">
                                    <div class="w-5 h-5 rounded-full bg-[#00A082]/15 text-[#00A082] flex items-center justify-center text-[10px] font-bold"><i class="fas fa-check"></i></div>
                                    <span class="text-xs font-bold text-[#2D3748]">Order Placed & Paid</span>
                                </div>
                                <div class="flex items-center gap-3" id="step-preparing">
                                    <div class="w-5 h-5 rounded-full bg-white border border-[#E2E8F0] text-zinc-400 flex items-center justify-center text-[10px] font-bold">2</div>
                                    <span class="text-xs font-bold text-zinc-500">Preparing in Kitchen</span>
                                </div>
                                <div class="flex items-center gap-3" id="step-ready">
                                    <div class="w-5 h-5 rounded-full bg-white border border-[#E2E8F0] text-zinc-400 flex items-center justify-center text-[10px] font-bold">3</div>
                                    <span class="text-xs font-bold text-zinc-500">Ready & Dispatched</span>
                                </div>
                                <div class="flex items-center gap-3" id="step-enroute">
                                    <div class="w-5 h-5 rounded-full bg-white border border-[#E2E8F0] text-zinc-400 flex items-center justify-center text-[10px] font-bold">4</div>
                                    <span class="text-xs font-bold text-zinc-500">Runner En-Route</span>
                                </div>
                            </div>
 
                            <div class="text-center mt-2">
                                <button onclick="resetTrackerDemo()" class="text-[10px] bg-white hover:bg-[#F7F9FA] text-[#2D3748] border border-[#E2E8F0] px-4 py-2 rounded-full transition font-bold cursor-pointer">
                                    Simulate New Order
                                </button>
                            </div>
                        </div>      </div>

                    </div>

                    <!-- Cart Tray (Glows when items added) -->
                    <div id="phone-cart-tray" class="hidden absolute bottom-0 inset-x-0 bg-zinc-900/95 border-t border-zinc-800 rounded-t-[25px] p-4 pb-6 z-40 transition-all duration-300">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-white flex items-center gap-1.5"><i class="fas fa-shopping-basket text-brand-rose"></i> Your Basket</span>
                            <button onclick="clearBasket()" class="text-[10px] text-zinc-500 hover:text-zinc-400">Clear All</button>
                        </div>
                        <div id="cart-tray-items" class="max-h-[140px] overflow-y-auto space-y-2 mb-4">
                            <!-- Items populated here -->
                        </div>
                        <div class="border-t border-zinc-800/80 pt-3 space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-400">Delivery location:</span>
                                <span class="font-bold text-white" id="cart-location-text">Not configured!</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-400">Total:</span>
                                <span class="text-sm font-extrabold text-brand-rose" id="cart-tray-total">Ksh 0.00</span>
                            </div>
                            <button onclick="checkoutOrder()" class="w-full py-2.5 bg-gradient-to-r from-brand-rose to-brand-orange text-white rounded-xl text-xs font-bold hover:from-brand-purple transition shadow-lg">
                                Order & Pay via M-Pesa
                            </button>
                        </div>
                    </div>

                    <!-- M-Pesa Smartphone Lock-Screen STK Push Simulation Overlay -->
                    <div id="mpesa-simulation-overlay" class="hidden absolute inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
                        <div class="w-full max-w-[280px] bg-white rounded-[32px] p-6 shadow-2xl border border-[#E2E8F0] phone-buzz">
                            <div class="text-center space-y-4">
                                <div class="w-12 h-12 bg-[#00A082]/10 text-[#00A082] rounded-full flex items-center justify-center mx-auto shadow-sm">
                                    <i class="fas fa-mobile-screen text-xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">M-Pesa STK Push</h4>
                                    <p class="text-[11px] text-zinc-600 px-1 leading-relaxed font-medium">
                                        Enter M-Pesa PIN to authorize payment of <br>
                                        <strong class="text-[#00A082] text-xs font-black" id="mpesa-amount">Ksh 0</strong> to <strong class="text-[#2D3748] font-bold">JUSTFEAST LTD</strong>.
                                    </p>
                                </div>
                                <div class="py-0.5">
                                    <input type="password" id="mpesa-pin-input" placeholder="••••" class="w-36 mx-auto text-center font-black tracking-widest text-xl py-2.5 rounded-xl bg-[#F7F9FA] border border-[#E2E8F0] text-[#2D3748] focus:outline-none focus:border-[#00A082] focus:ring-1 focus:ring-[#00A082] transition shadow-inner" maxlength="4" value="1234">
                                </div>
                                <div class="grid grid-cols-2 gap-3 pt-1">
                                    <button onclick="cancelMpesaSimulation()" class="py-2.5 bg-[#F7F9FA] hover:bg-[#E2E8F0] text-zinc-600 rounded-full text-[10px] font-bold transition duration-200 border border-[#E2E8F0] cursor-pointer">Cancel</button>
                                    <button onclick="confirmMpesaSimulation()" class="py-2.5 bg-[#00A082] hover:bg-[#008A70] text-white rounded-full text-[10px] font-bold transition duration-200 shadow-md shadow-[#00A082]/10 cursor-pointer">Pay Now</button>
                                </div>
                            </div>
                        </div>                    <!-- Stadium Interactive SVG Map Modal Overlay -->
                    <div id="stadium-modal-overlay" class="hidden absolute inset-0 bg-black/95 z-50 flex flex-col p-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-white"><i class="fas fa-map-marked-alt text-brand-rose mr-1.5"></i> Select Your Location</span>
                            <button onclick="closeSeatModal()" class="text-zinc-500 hover:text-zinc-300 text-sm"><i class="fas fa-times"></i></button>
                        </div>

                        <!-- VIEW 2: GPS Pin View -->
                        <div id="modal-gps-selector-view" class="flex-1 bg-zinc-950 rounded-xl border border-zinc-900 p-2.5 flex flex-col justify-between overflow-y-auto">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] text-zinc-500 uppercase font-black tracking-wider">Drop location pin on map</span>
                                    <button type="button" onclick="getCurrentGPSLocation()" class="text-[9px] bg-brand-rose/20 text-brand-rose px-2.5 py-1 rounded-full font-bold border border-brand-rose/30 hover:bg-brand-rose/30 transition">
                                        <i class="fas fa-location-crosshairs mr-1"></i> Use My GPS
                                    </button>
                                </div>

                                <!-- Leaflet Map Container -->
                                <div id="modal-leaflet-map" class="h-44 w-full rounded-xl bg-zinc-900 border border-zinc-800 z-10 relative"></div>

                                <!-- Latitude / Longitude Display -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase">Latitude</label>
                                        <input type="text" id="gps-lat-input" class="w-full px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-800 text-xs font-bold text-white" value="-1.32588000" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase">Longitude</label>
                                        <input type="text" id="gps-lng-input" class="w-full px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-800 text-xs font-bold text-white" value="36.79941000" readonly>
                                    </div>
                                </div>

                                <!-- Description Input -->
                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase mb-1">Landmarks & Clothing Description</label>
                                    <textarea id="gps-desc-input" placeholder="e.g. Near general entrance gate, wearing a yellow t-shirt and white hat." class="w-full px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-800 text-xs text-white focus:outline-none focus:border-brand-rose h-12 resize-none"></textarea>
                                </div>
                            </div>

                            <button onclick="saveGPSCoordinates()" class="w-full py-2.5 bg-brand-rose text-white rounded-lg text-[10px] font-bold hover:bg-brand-rose/95 transition shadow mt-3">
                                Confirm GPS Pin Location
                            </button>
                        </div>
                    </div>>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PORTAL B: VENDOR KITCHEN DASHBOARD -->
            <div id="col-vendor" class="xl:col-span-1 glass-card p-5 rounded-3xl border border-zinc-800 flex flex-col h-[780px]">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-zinc-900">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏪</span>
                        <div>
                            <h2 class="text-base font-bold text-white">Vendor Kitchen Portal</h2>
                            <p class="text-[10px] text-zinc-500" id="vendor-assigned-name">Connecting...</p>
                        </div>
                    </div>
                    <!-- Easy switcher to demo another vendor -->
                    <select onchange="quickSwitchVendor(this.value)" class="bg-zinc-900 border border-zinc-800 text-[10px] px-2 py-1 rounded-md text-zinc-400 focus:outline-none">
                        <option value="vendor@justfeast.com">Burger World 🍔</option>
                        <option value="taco@justfeast.com">Taco Fiesta 🌮</option>
                        <option value="choma@justfeast.com">Choma Zone 🥩</option>
                    </select>
                </div>

                <!-- Vendor Dashboard Body -->
                <div id="vendor-dashboard-content" class="flex-1 flex flex-col overflow-hidden space-y-4">
                    <!-- Metrics Row -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-zinc-900/50 border border-zinc-900 p-3 rounded-2xl">
                            <span class="text-[9px] uppercase tracking-wider text-zinc-500 block font-semibold">Active Queue</span>
                            <span class="text-xl font-black text-brand-rose" id="vendor-queue-count">0 Orders</span>
                        </div>
                        <div class="bg-zinc-900/50 border border-zinc-900 p-3 rounded-2xl">
                            <span class="text-[9px] uppercase tracking-wider text-zinc-500 block font-semibold">Total Sales</span>
                            <span class="text-xl font-black text-brand-emerald" id="vendor-sales-amount">Ksh 0.00</span>
                        </div>
                    </div>

                    <!-- Active Orders Prep Queue -->
                    <div class="flex-1 flex flex-col min-h-0">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Live Preparation Queue</h3>

                        <div id="vendor-orders-container" class="flex-1 overflow-y-auto space-y-3 pr-1">
                            <!-- Incoming order cards filled here -->
                            <div class="text-center py-12 text-zinc-600 space-y-2">
                                <i class="fas fa-utensils text-2xl"></i>
                                <p class="text-xs">No active paid orders in the kitchen.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kitchen Stock Management -->
                    <div class="bg-zinc-900/30 border border-zinc-900 p-3.5 rounded-2xl space-y-2">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Quick Stock Toggle</h3>
                        <div id="vendor-stock-items" class="space-y-2 max-h-[140px] overflow-y-auto">
                            <!-- Stock control checklist dynamically loaded -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- PORTAL C: RUNNER PANEL -->
            <div id="col-runner" class="xl:col-span-1 glass-card p-5 rounded-3xl border border-zinc-800 flex flex-col h-[780px] relative overflow-hidden">
                <div class="pb-3 border-b border-zinc-900 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏃</span>
                        <div>
                            <h2 class="text-base font-bold text-white">Runner Dispatch</h2>
                            <p class="text-[10px] text-zinc-500">Staging area routing</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-[9px] bg-brand-orange/10 text-brand-orange px-2 py-0.5 rounded border border-brand-orange/20"><i class="fas fa-signal"></i> 5G</span>
                    </div>
                </div>

                <!-- Runner Phone Inner Content -->
                <div id="runner-phone-body" class="flex-1 overflow-y-auto pr-1 relative flex flex-col">

                        <!-- Header metadata -->
                        <div class="flex justify-between items-center bg-zinc-900/40 p-3 rounded-xl border border-zinc-900 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm">🏃</span>
                                <div>
                                    <p class="text-[9px] text-zinc-500 font-bold uppercase">Delivery Runner</p>
                                    <h4 class="text-xs font-bold text-white">Mike Runner</h4>
                                </div>
                            </div>
                            <span class="text-[9px] bg-brand-emerald/20 text-brand-emerald px-2 py-0.5 rounded-full font-bold border border-brand-emerald/30">Active</span>
                        </div>

                        <!-- Main Delivery Task Dashboard -->
                        <div id="runner-delivery-task" class="flex-1 flex flex-col justify-between">

                            <!-- Delivery details section -->
                            <div class="space-y-4 flex-1">
                                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider"><i class="fas fa-route text-brand-rose mr-1.5"></i> Assigned Delivery</h3>

                                <div id="runner-active-card-container">
                                    <!-- Active delivery card loaded here -->
                                    <div class="text-center py-20 text-zinc-600 bg-zinc-900/30 border border-zinc-900 rounded-2xl p-6 space-y-3">
                                        <i class="fas fa-radar text-3xl text-zinc-700"></i>
                                        <h4 class="text-xs font-bold text-zinc-500">Awaiting Kitchen Orders</h4>
                                        <p class="text-[10px] text-zinc-600">You will automatically receive a task notification when a vendor marks an order as 'Ready'.</p>
                                    </div>
                                </div>

                                <!-- GPS Location Navigation Guide -->
                                <div id="runner-map-guide" class="hidden space-y-2 bg-zinc-900/40 p-4 rounded-xl border border-zinc-900">
                                    <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">GPS Location Pin Navigation</h4>

                                    <div class="h-28 bg-zinc-950 rounded-lg border border-zinc-800 flex items-center justify-center relative overflow-hidden">
                                        <!-- Mini stage mapping visual representation -->
                                        <div class="absolute top-1 bg-zinc-900 border border-zinc-800 px-2 py-0.5 rounded text-[8px] text-zinc-400 font-bold">UHURU GARDENS KITCHEN STALLS</div>
                                        <div class="absolute bottom-2 bg-brand-rose px-2 py-1 rounded text-[8px] text-white font-bold" id="runner-target-section-tag">GPS TARGET LOCATION</div>

                                        <!-- Dashboard dashed navigation routing path -->
                                        <svg viewBox="0 0 100 40" class="w-24 h-auto pointer-events-none">
                                            <path d="M 10,10 Q 50,30 90,30" fill="none" stroke="#8b5cf6" stroke-dasharray="3" stroke-width="2" class="animate-pulse" />
                                            <circle cx="10" cy="10" r="3" fill="#ec4899" />
                                            <circle cx="90" cy="30" r="3" fill="#10b981" />
                                        </svg>
                                    </div>
                                    <p class="text-[9px] text-zinc-500"><i class="fas fa-info-circle mr-1"></i> Proceed to Vendor kitchen, pickup order, and navigate to the location coordinates listed.</p>
                                </div>
                            </div>

                            <!-- Delivery verification secure section -->
                            <div id="runner-verification-box" class="hidden mt-4 bg-zinc-900 border border-zinc-800 p-4 rounded-2xl space-y-3">
                                <h4 class="text-xs font-bold text-white text-center">Handover Verification</h4>
                                
                                <!-- QR Scanner Trigger / View -->
                                <div class="space-y-2">
                                    <button type="button" onclick="toggleRunnerQRScanner()" class="w-full py-2 bg-brand-rose/20 text-brand-rose rounded-lg text-xs font-bold border border-brand-rose/30 hover:bg-brand-rose/30 transition flex items-center justify-center gap-1.5">
                                        <i class="fas fa-camera"></i> Scan Customer QR Code
                                    </button>
                                    <div id="runner-qr-reader-container" class="hidden rounded-xl overflow-hidden border border-zinc-800 bg-black">
                                        <div id="runner-qr-reader" class="w-full"></div>
                                    </div>
                                    
                                    <!-- Simulator Helper Button -->
                                    <button type="button" onclick="simulateQRScan()" class="w-full py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-[9px] font-bold border border-zinc-700 hover:bg-zinc-700 transition flex items-center justify-center gap-1">
                                        <i class="fas fa-wand-magic-sparkles text-brand-rose"></i> [Simulate QR Scan]
                                    </button>
                                </div>

                                <div class="relative flex py-1 items-center">
                                    <div class="flex-grow border-t border-zinc-800"></div>
                                    <span class="flex-shrink mx-2 text-[8px] text-zinc-500 font-bold uppercase">Or Enter PIN</span>
                                    <div class="flex-grow border-t border-zinc-800"></div>
                                </div>

                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Enter Customer Verification PIN</label>
                                    <input type="text" id="runner-pin-input" placeholder="Enter 4-Digit PIN" class="w-full text-center py-2.5 rounded-lg glass-input font-bold tracking-widest text-lg text-white" maxlength="4">
                                </div>
                                <button onclick="verifyRunnerDelivery()" class="w-full py-2.5 bg-brand-emerald hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-brand-emerald/10">
                                    Verify & Complete Delivery
                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- PORTAL D: PLATFORM ADMIN CONTROL CENTER -->
            <div id="col-admin" class="xl:col-span-1 glass-card p-5 rounded-3xl border border-zinc-800 flex flex-col h-[780px] overflow-y-auto">
                <div class="pb-3 border-b border-zinc-900 mb-4 flex items-center gap-2">
                    <span class="text-xl">📊</span>
                    <div>
                        <h2 class="text-base font-bold text-white">Platform Admin Portal</h2>
                        <p class="text-[10px] text-zinc-500">Global control panel & logistics monitor</p>
                    </div>
                </div>

                <!-- Admin Analytics Metrics Grid -->
                <div class="grid grid-cols-2 gap-3.5 mb-5">
                    <div class="bg-zinc-900/50 border border-zinc-900 p-3 rounded-2xl text-left">
                        <span class="text-[8px] uppercase tracking-wider text-zinc-500 block font-bold">Total Platform Revenue</span>
                        <span class="text-lg font-black text-brand-rose" id="admin-total-revenue">Ksh 0.00</span>
                    </div>
                    <div class="bg-zinc-900/50 border border-zinc-900 p-3 rounded-2xl text-left">
                        <span class="text-[8px] uppercase tracking-wider text-zinc-500 block font-bold">Consolidated Orders</span>
                        <span class="text-lg font-black text-brand-orange" id="admin-orders-count">0</span>
                    </div>
                    <div class="bg-zinc-900/50 border border-zinc-900 p-3 rounded-2xl text-left col-span-2">
                        <span class="text-[8px] uppercase tracking-wider text-zinc-500 block font-bold">Average Delivery Speed</span>
                        <span class="text-lg font-black text-brand-emerald" id="admin-delivery-speed">8.4 minutes</span>
                    </div>
                </div>

                <!-- Interactive heatmaps mapping seating quadrants (Uhuru Gardens Arena) -->
                <div class="bg-zinc-900/30 border border-zinc-900 p-4 rounded-2xl space-y-3 mb-5">
                    <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider"><i class="fas fa-fire text-brand-orange mr-1.5"></i> Stadium Order Heatmap</h3>

                    <div class="flex items-center justify-center py-2 bg-zinc-950 rounded-xl border border-zinc-900">
                        <svg viewBox="0 0 100 100" class="w-36 h-auto">
                            <!-- Center Concert Stage -->
                            <rect x="38" y="42" width="24" height="16" rx="2" fill="#18181b" stroke="#6366f1" stroke-width="0.8" />

                            <!-- heat areas sections -->
                            <!-- VIP A (Top Left) -->
                            <path d="M 20,30 A 40,40 0 0,1 45,10 L 45,30 A 20,20 0 0,0 30,40 Z"
                                  id="heat-vip-a" fill="#3f3f46" stroke="#27272a" stroke-width="0.5" class="transition-all duration-500" />
                            <text x="31" y="22" fill="#71717a" font-size="3" font-weight="bold">VIP A</text>

                            <!-- VIP B (Top Right) -->
                            <path d="M 55,10 A 40,40 0 0,1 80,30 L 70,40 A 20,20 0 0,0 55,30 Z"
                                  id="heat-vip-b" fill="#3f3f46" stroke="#27272a" stroke-width="0.5" class="transition-all duration-500" />
                            <text x="64" y="22" fill="#71717a" font-size="3" font-weight="bold">VIP B</text>

                            <!-- General A (Bottom Left) -->
                            <path d="M 20,70 A 40,40 0 0,0 45,90 L 45,70 A 20,20 0 0,1 30,60 Z"
                                  id="heat-gen-a" fill="#3f3f46" stroke="#27272a" stroke-width="0.5" class="transition-all duration-500" />
                            <text x="31" y="80" fill="#71717a" font-size="3" font-weight="bold">GEN A</text>

                            <!-- General B (Bottom Right) -->
                            <path d="M 55,90 A 40,40 0 0,0 80,70 L 70,60 A 20,20 0 0,1 55,70 Z"
                                  id="heat-gen-b" fill="#3f3f46" stroke="#27272a" stroke-width="0.5" class="transition-all duration-500" />
                            <text x="64" y="80" fill="#71717a" font-size="3" font-weight="bold">GEN B</text>
                        </svg>
                    </div>
                    <div class="flex justify-between items-center text-[9px] text-zinc-500 font-medium">
                        <span>Low Density 🟢</span>
                        <span>High Density 🔥</span>
                    </div>
                </div>

                <!-- Vendor Revenue List -->
                <div class="bg-zinc-900/30 border border-zinc-900 p-4 rounded-2xl space-y-3">
                    <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Vendor Financial Contracts</h3>

                    <div id="admin-vendors-list" class="space-y-3">
                        <!-- Filled dynamically -->
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Simulation Panel (CONGESTION CONTROL + Sound Engine) -->
    <footer class="fixed bottom-0 inset-x-0 bg-zinc-950 border-t border-zinc-900 py-3.5 px-6 flex flex-col md:flex-row items-center justify-between gap-4 z-40 text-xs">
        <div class="flex items-center gap-3">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-emerald opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-emerald"></span>
            </span>
            <span class="text-zinc-400">Simulation engine: <strong class="text-white">Active (1s smart-polling status sync)</strong></span>
        </div>

        <div class="flex items-center gap-4">
            <!-- Offline / Network Congestion Simulation Toggle -->
            <button onclick="toggleCongestionMode()" id="congestion-toggle" class="bg-zinc-900 border border-zinc-800 text-zinc-400 px-4 py-2 rounded-xl flex items-center gap-2 hover:border-brand-pink transition font-semibold">
                <i class="fas fa-wifi text-zinc-500" id="congestion-wifi-icon"></i>
                <span id="congestion-btn-text">Simulate Concert Network Congestion</span>
            </button>
        </div>
    </footer>

    <!-- JS LOGIC CODE -->
    <script>
        // Global variables
        let currentUser = null;
        let activeEvent = null;
        let vendors = [];
        let basket = [];
        let activeOrder = null;
        let hasBeepedForArrival = false;
        let selectedSeat = null;
        let congestionMode = false;
        let selectedCustomerCategory = 'all';

        // Runner movement simulation state
        let runnerMovementInterval = null;
        let runnerLat = -1.32588;
        let runnerLng = 36.79941;
        let runnerStartLat = -1.32588;
        let runnerStartLng = 36.79941;

        let pollingInterval = null;
        let audioCtx = null;

        // Initialize audio engine for alerts on user interaction
        function playSoundNotification(type) {
            try {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);

                if (type === 'beep') {
                    osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.1);
                } else if (type === 'success') {
                    osc.frequency.setValueAtTime(800, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.25);
                    gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.3);
                } else if (type === 'alert') {
                    osc.frequency.setValueAtTime(400, audioCtx.currentTime);
                    osc.frequency.setValueAtTime(300, audioCtx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.3);
                }
            } catch (e) {
                console.log("Audio not supported / initialized by guest interaction yet", e);
            }
        }

        // On Load Page Init
        window.addEventListener('DOMContentLoaded', () => {
            loadActiveEvent();
            loadVendors();
 
            // Always display simulated customer main portal
            document.getElementById('cust-main').classList.remove('hidden');
 
            // Load saved user session
            const saved = localStorage.getItem('justfeast_client_user');
            if (saved) {
                currentUser = JSON.parse(saved);
            }
            updateAuthHeader();
 
            // Recover seat coordinates
            const savedSeat = localStorage.getItem('justfeast_selected_seat');
            if (savedSeat) {
                try {
                    selectedSeat = JSON.parse(savedSeat);
                    if (selectedSeat.type === 'gps') {
                        const { latitude, longitude, description } = selectedSeat;
                        document.getElementById('selected-seat-label').textContent = "GPS Location Pin";
                        document.getElementById('selected-seat-sub').textContent = `${latitude.toFixed(5)}, ${longitude.toFixed(5)}${description ? ' — ' + description : ''}`;
                        document.getElementById('seat-status-pill').textContent = "Configured";
                        document.getElementById('seat-status-pill').className = "text-[10px] bg-brand-emerald/20 text-brand-emerald px-2 py-0.5 rounded-full font-semibold border border-brand-emerald/30";
                        
                        const locationText = `GPS Pin: ${description || (latitude.toFixed(4) + ', ' + longitude.toFixed(4))}`;
                        document.getElementById('cart-location-text').textContent = locationText;
                        document.getElementById('selected-seat-hero').textContent = locationText;
                    } else {
                        const { section, row, seat } = selectedSeat;
                        document.getElementById('selected-seat-label').textContent = section;
                        document.getElementById('selected-seat-sub').textContent = `${row} — ${seat}`;
                        document.getElementById('seat-status-pill').textContent = "Configured";
                        document.getElementById('seat-status-pill').className = "text-[10px] bg-brand-emerald/20 text-brand-emerald px-2 py-0.5 rounded-full font-semibold border border-brand-emerald/30";
                        
                        const locationText = `${section}, ${row}, ${seat}`;
                        document.getElementById('cart-location-text').textContent = locationText;
                        document.getElementById('selected-seat-hero').textContent = locationText;
                    }
                } catch (e) {}
            }
 
            // Start real-time sync polling
            pollingInterval = setInterval(syncRealTimeEngine, 2000);
 
            // Check PWA Install
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
                    userNameText.textContent = currentUser.name.split(' ')[0];
                }
                if (sidebarWelcome) {
                    sidebarWelcome.textContent = currentUser.name;
                }
                if (authButtonsContainer) {
                    authButtonsContainer.innerHTML = `
                        <button onclick="logoutCustomer()" class="px-2 py-1 text-[8px] font-bold text-zinc-400 hover:text-zinc-200 bg-zinc-900 border border-zinc-800 rounded-full transition cursor-pointer focus:outline-none">
                            Logout
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
                        <button onclick="openAuthModal()" class="px-2 py-1 text-[8px] font-black text-[#2D3748] bg-[#FFC244] hover:bg-[#E0A325] border border-[#E0A325] rounded-full transition shadow-sm cursor-pointer focus:outline-none">
                            Login
                        </button>
                    `;
                }
            }
        }

        // Fetch active event metadata
        async function loadActiveEvent() {
            try {
                const res = await fetch('/api/events/active');
                if (res.ok) {
                    activeEvent = await res.json();
                    document.getElementById('live-event-banner').innerHTML = `<i class="fas fa-ticket text-brand-rose mr-1"></i> ${activeEvent.name} — @${activeEvent.venue.name}`;
                }
            } catch (e) {
                console.error("Error loading event", e);
            }
        }

        // Fetch vendors listing
        async function loadVendors() {
            try {
                const res = await fetch('/api/vendors');
                if (res.ok) {
                    vendors = await res.json();
                    renderCustomerVendors();
                }
            } catch (e) {
                console.error("Error loading vendors", e);
            }
        }

        // Helper to get simulated product category
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
 
        // Helper to trigger customer search on input
        function searchCustomerMenu() {
            renderCustomerVendors();
        }
 
        // Helper to switch active category
        function setCustomerCategory(cat) {
            playSoundNotification('beep');
            selectedCustomerCategory = cat;
            
            const categories = ['all', 'food', 'drinks', 'snacks'];
            categories.forEach(c => {
                const btn = document.getElementById(`cust-cat-${c}`);
                if (!btn) return;
                if (c === cat) {
                    btn.classList.add('bg-[#FFC244]', 'text-[#2D3748]', 'border-[#FFC244]');
                    btn.classList.remove('bg-zinc-950', 'text-zinc-400', 'border-zinc-800');
                } else {
                    btn.classList.remove('bg-[#FFC244]', 'text-[#2D3748]', 'border-[#FFC244]');
                    btn.classList.add('bg-zinc-950', 'text-zinc-400', 'border-zinc-800');
                }
            });
            
            renderCustomerVendors();
        }
 
        // Render food marketplace lists inside simulated mobile app
        function renderCustomerVendors() {
            const container = document.getElementById('vendor-list-container');
            container.innerHTML = '';
 
            if (vendors.length === 0) {
                container.innerHTML = '<p class="text-xs text-zinc-600 text-center py-4">No active vendors found.</p>';
                return;
            }
 
            const searchVal = document.getElementById('cust-menu-search') 
                ? document.getElementById('cust-menu-search').value.toLowerCase() 
                : '';
 
            vendors.forEach(vendor => {
                // Filter products by search and category
                const matchingProducts = vendor.products.filter(p => {
                    const matchesSearch = p.name.toLowerCase().includes(searchVal) || p.description.toLowerCase().includes(searchVal);
                    const matchesCategory = selectedCustomerCategory === 'all' || getProductCategory(p) === selectedCustomerCategory;
                    return matchesSearch && matchesCategory;
                });
 
                if (matchingProducts.length === 0) return;
 
                const stallSection = document.createElement('div');
                stallSection.className = 'space-y-3 mb-6';
 
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
                    <div class="relative overflow-hidden rounded-2xl border border-zinc-800 bg-[#1C1C24] shadow-sm">
                        <div class="h-16 ${coverImg} relative z-0">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1C1C24] via-transparent to-transparent"></div>
                        </div>
                        <div class="p-3 pt-0 relative z-10 flex items-end justify-between gap-2 -mt-6">
                            <div class="flex items-end gap-2">
                                <div class="w-10 h-10 rounded-xl bg-[#1C1C24] border border-zinc-800 flex items-center justify-center text-xl filter drop-shadow-sm flex-shrink-0">
                                    ${vendor.logo_url || '🏪'}
                                </div>
                                <div class="pb-0.5">
                                    <h4 class="text-[11px] font-black text-white leading-none">${vendor.business_name}</h4>
                                    <p class="text-[8px] text-zinc-400 mt-1"><i class="far fa-clock mr-0.5"></i> ${deliveryTime}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 bg-[#FFC244] text-[#2D3748] px-2 py-0.5 rounded-full font-black text-[9px] border border-[#E0A325]">
                                <span>★</span>
                                <span>${rating}</span>
                            </div>
                        </div>
                    </div>
                `;
 
                let productsHtml = '<div class="space-y-2 mt-2">';
 
                matchingProducts.forEach(p => {
                    const out = p.stock_status !== 'in_stock';
                    let imageTag = '';
                    if (p.image_url && p.image_url.startsWith('/')) {
                        imageTag = `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-full h-full object-cover" alt="${p.name}">`;
                    } else {
                        const gradient = p.image_url || 'bg-gradient-to-br from-amber-400 to-red-500';
                        imageTag = `
                            <div class="w-full h-full ${gradient} flex items-center justify-center text-white text-[10px] font-black uppercase">
                                ${p.name.substring(0, 2)}
                            </div>
                        `;
                    }
 
                    productsHtml += `
                        <div class="bg-[#1C1C24] rounded-2xl border border-zinc-800 overflow-hidden flex items-center p-2.5 gap-3">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-zinc-900 flex-shrink-0 border border-zinc-850">
                                ${imageTag}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-[10px] font-black text-white truncate ${out ? 'text-zinc-600 line-through' : ''}">${p.name}</h5>
                                <p class="text-[8px] text-zinc-500 line-clamp-1 leading-none mt-0.5">${p.description || 'Stadium seat delivery'}</p>
                                <p class="text-[10px] font-black text-[#00A082] mt-1">Ksh ${parseFloat(p.price).toLocaleString()}</p>
                            </div>
                            <div class="flex-shrink-0">
                                ${out 
                                    ? `<span class="text-[8px] bg-zinc-900 border border-zinc-800 text-zinc-600 px-2 py-1 rounded-full font-bold">Out</span>`
                                    : (() => {
                                        const bItem = basket.find(item => item.id === p.id);
                                        if (bItem && bItem.quantity > 0) {
                                            return `
                                            <div class="flex items-center bg-[#FFC244] rounded-full h-7 px-1 border border-[#E0A325] shadow-sm">
                                                <button onclick="adjustQty(${p.id}, -1)" class="w-5 h-5 rounded-full bg-white hover:bg-zinc-100 flex items-center justify-center font-black text-[9px] text-[#2D3748] transition border-0 cursor-pointer"><i class="fas fa-minus text-[7px]"></i></button>
                                                <span class="px-2 text-[9px] font-black text-[#2D3748] min-w-[12px] text-center">${bItem.quantity}</span>
                                                <button onclick="adjustQty(${p.id}, 1)" class="w-5 h-5 rounded-full bg-white hover:bg-zinc-100 flex items-center justify-center font-black text-[9px] text-[#2D3748] transition border-0 cursor-pointer"><i class="fas fa-plus text-[7px]"></i></button>
                                            </div>
                                            `;
                                        } else {
                                            return `
                                            <button onclick="addToBasket(${p.id}, '${p.name}', ${p.price}, ${vendor.id})" class="w-7 h-7 rounded-full bg-[#FFC244] hover:bg-[#E0A325] text-[#2D3748] flex items-center justify-center font-black transition-all shadow-sm border border-[#E0A325] cursor-pointer">
                                                <i class="fas fa-plus text-[10px]"></i>
                                            </button>
                                            `;
                                        }
                                      })()
                                }
                            </div>
                        </div>
                    `;
                });
 
                productsHtml += '</div>';
 
                stallSection.innerHTML = headerCard + productsHtml;
                container.appendChild(stallSection);
            });
        }

        function showConfirmModal(title, message, confirmText, cancelText) {
            return new Promise((resolve) => {
                const phoneBody = document.getElementById('customer-phone-body');
                const overlay = document.createElement('div');
                overlay.className = "absolute inset-0 bg-black/85 backdrop-blur-sm z-[9999] flex items-center justify-center p-3 transition-all duration-300";
                
                const card = document.createElement('div');
                card.className = "bg-[#1C1C24] rounded-3xl p-4 max-w-[240px] w-full text-center space-y-4 shadow-2xl border border-zinc-800/80 transform scale-95 opacity-0 transition-all duration-300";
                
                card.innerHTML = `
                    <div class="w-10 h-10 bg-brand-orange/20 text-brand-orange rounded-2xl flex items-center justify-center mx-auto text-base border border-brand-orange/30">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xs font-black text-white">${title}</h3>
                        <p class="text-[9px] text-zinc-400 leading-relaxed">${message}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button id="confirm-modal-cancel" class="py-2 bg-zinc-900 hover:bg-zinc-800 text-zinc-450 rounded-xl text-[10px] font-bold transition border border-zinc-800 cursor-pointer">
                            ${cancelText}
                        </button>
                        <button id="confirm-modal-ok" class="py-2 bg-brand-rose hover:bg-brand-rose/90 text-white rounded-xl text-[10px] font-bold transition shadow-lg shadow-brand-rose/10 cursor-pointer border-0">
                            ${confirmText}
                        </button>
                    </div>
                `;
                
                overlay.appendChild(card);
                phoneBody.appendChild(overlay);
                
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

        // Add to basket logic
        async function addToBasket(id, name, price, vendorId) {
            playSoundNotification('beep');
            // justFeast orders must be from one vendor at a time (standard logic)
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
                basket.push({ id, name, price, quantity: 1, vendorId });
            }

            renderBasketTray();
            renderCustomerVendors();
        }

        // Render current basket drawer inside customer phone
        function renderBasketTray() {
            const tray = document.getElementById('phone-cart-tray');
            const itemsContainer = document.getElementById('cart-tray-items');

            if (basket.length === 0) {
                tray.classList.add('hidden');
                return;
            }

            tray.classList.remove('hidden');
            itemsContainer.innerHTML = '';

            let total = 0;
            basket.forEach(item => {
                const sub = item.price * item.quantity;
                total += sub;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex justify-between items-center text-xs py-1.5 border-b border-zinc-900';
                itemDiv.innerHTML = `
                    <div class="flex-1">
                        <p class="font-bold text-zinc-300">${item.name}</p>
                        <p class="text-[9px] text-zinc-500">Qty ${item.quantity} x Ksh ${item.price}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="adjustQty(${item.id}, -1)" class="w-5 h-5 bg-zinc-800 rounded flex items-center justify-center text-xs text-zinc-400 hover:bg-zinc-700">-</button>
                        <span class="text-xs font-bold text-white">${item.quantity}</span>
                        <button onclick="adjustQty(${item.id}, 1)" class="w-5 h-5 bg-zinc-800 rounded flex items-center justify-center text-xs text-zinc-400 hover:bg-zinc-700">+</button>
                    </div>
                `;
                itemsContainer.appendChild(itemDiv);
            });

            document.getElementById('cart-tray-total').textContent = `Ksh ${total.toLocaleString()}`;
        }

        function adjustQty(id, amount) {
            const item = basket.find(i => i.id === id);
            if (item) {
                item.quantity += amount;
                if (item.quantity <= 0) {
                    basket = basket.filter(i => i.id !== id);
                }
            }
            renderBasketTray();
            renderCustomerVendors();
        }

        function clearBasket() {
            basket = [];
            renderBasketTray();
            renderCustomerVendors();
        }

        // Custom simulated phone logins & Verification callbacks
        async function sendOTP() {
            const phone = document.getElementById('cust-phone-input').value;
            if (!phone) {
                alert("Please enter your phone number");
                return;
            }
 
            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone })
                });
 
                if (res.ok) {
                    playSoundNotification('beep');
                    document.getElementById('otp-phone-text').textContent = `Verification SMS sent to ${phone}`;
                    document.getElementById('cust-auth').classList.add('hidden');
                    document.getElementById('cust-otp').classList.remove('hidden');
                }
            } catch (e) {
                console.error("Auth error", e);
            }
        }
 
        async function verifyOTP() {
            const phone = document.getElementById('cust-phone-input').value;
            const code = document.getElementById('cust-otp-input').value;
 
            try {
                const res = await fetch('/api/auth/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone, code })
                });
 
                const data = await res.json();
                if (res.ok) {
                    playSoundNotification('success');
                    currentUser = data.user;
                    updateAuthHeader();
                    closeAuthModal();
                    
                    // Sync other dashboards if connected
                    syncRealTimeEngine();
 
                    // Auto checkout if basket & seat are set
                    if (basket.length > 0 && selectedSeat) {
                        checkoutOrder();
                    }
                } else {
                    alert(data.message);
                }
            } catch (e) {
                console.error(e);
            }
        }
 
        // Demo quick accounts switcher
        async function quickLogin(email) {
            try {
                const res = await fetch('/api/auth/login-as', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
 
                const data = await res.json();
                if (res.ok) {
                    playSoundNotification('success');
                    currentUser = data.user;
                    updateAuthHeader();
                    closeAuthModal();
 
                    // Sync other dashboards if connected
                    syncRealTimeEngine();
 
                    // Auto checkout if basket & seat are set
                    if (basket.length > 0 && selectedSeat) {
                        checkoutOrder();
                    }
                }
            } catch (e) {
                console.error(e);
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
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
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
 
        // SVG Stadium Seat Selector Functions
        // Seating Mode & GPS Coordinates State
        let leafletMap = null;
        let leafletMarker = null;
        let activeSeatingMode = 'gps'; // 'seat' or 'gps'

        function setSeatingMode(mode) {
            activeSeatingMode = mode;
            const tabSeat = document.getElementById('modal-tab-seat');
            const tabGps = document.getElementById('modal-tab-gps');
            const viewSeat = document.getElementById('modal-seat-selector-view');
            const viewGps = document.getElementById('modal-gps-selector-view');

            if (mode === 'seat') {
                tabSeat.className = "flex-1 py-2 rounded-lg text-center bg-brand-rose text-white shadow shadow-brand-rose/25 transition-all";
                tabGps.className = "flex-1 py-2 rounded-lg text-center text-zinc-400 hover:text-white transition-all";
                viewSeat.classList.remove('hidden');
                viewGps.classList.add('hidden');
            } else {
                tabGps.className = "flex-1 py-2 rounded-lg text-center bg-brand-rose text-white shadow shadow-brand-rose/25 transition-all";
                tabSeat.className = "flex-1 py-2 rounded-lg text-center text-zinc-400 hover:text-white transition-all";
                viewSeat.classList.add('hidden');
                viewGps.classList.remove('hidden');
                
                // Initialize map if not done yet
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

                    // Create a draggable marker
                    leafletMarker = L.marker(center, { draggable: true }).addTo(leafletMap);

                    // Update coordinates inputs when marker is dragged
                    leafletMarker.on('dragend', function (e) {
                        const position = leafletMarker.getLatLng();
                        document.getElementById('gps-lat-input').value = position.lat.toFixed(8);
                        document.getElementById('gps-lng-input').value = position.lng.toFixed(8);
                    });

                    // Update marker when map is clicked
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
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.getElementById('gps-lat-input').value = lat.toFixed(8);
                        document.getElementById('gps-lng-input').value = lng.toFixed(8);

                        if (leafletMap && leafletMarker) {
                            const newLatLng = new L.LatLng(lat, lng);
                            leafletMarker.setLatLng(newLatLng);
                            leafletMap.setView(newLatLng, 17);
                        }
                        playSoundNotification('success');
                    },
                    (error) => {
                        alert("Error getting location: " + error.message + ". Center map marker used instead.");
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
 
        function selectSectionSvg(sectionName) {
            playSoundNotification('beep');
            document.getElementById('seat-sec-input').value = sectionName;
 
            // Highlight color styles inside SVG
            const sections = ['svg-vip-a', 'svg-vip-b', 'svg-gen-a', 'svg-gen-b'];
            sections.forEach(id => {
                document.getElementById(id).classList.remove('fill-brand-rose/40', 'fill-brand-orange/40');
            });
 
            let activeId = '';
            if (sectionName.includes('VIP Section A')) activeId = 'svg-vip-a';
            else if (sectionName.includes('VIP Section B')) activeId = 'svg-vip-b';
            else if (sectionName.includes('General Admission A')) activeId = 'svg-gen-a';
            else if (sectionName.includes('General Admission B')) activeId = 'svg-gen-b';
 
            if (activeId) {
                const fillClass = activeId.includes('vip') ? 'fill-brand-rose/40' : 'fill-brand-orange/40';
                document.getElementById(activeId).classList.add(fillClass);
            }
        }
 
        function saveSeatCoordinates() {
            const section = document.getElementById('seat-sec-input').value;
            const row = document.getElementById('seat-row-input').value;
            const seat = document.getElementById('seat-num-input').value;
 
            selectedSeat = { type: 'seat', section, row, seat };
            localStorage.setItem('justfeast_selected_seat', JSON.stringify(selectedSeat));
 
            document.getElementById('selected-seat-label').textContent = `${section}`;
            document.getElementById('selected-seat-sub').textContent = `${row} — ${seat}`;
 
            document.getElementById('seat-status-pill').textContent = "Configured";
            document.getElementById('seat-status-pill').className = "text-[10px] bg-brand-emerald/20 text-brand-emerald px-2 py-0.5 rounded-full font-semibold border border-brand-emerald/30";
 
            const locationText = `${section}, ${row}, ${seat}`;
            document.getElementById('cart-location-text').textContent = locationText;
            document.getElementById('selected-seat-hero').textContent = locationText;
 
            playSoundNotification('success');
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
            document.getElementById('seat-status-pill').className = "text-[10px] bg-brand-emerald/20 text-brand-emerald px-2 py-0.5 rounded-full font-semibold border border-brand-emerald/30";

            const locationText = `GPS Pin: ${desc || (lat.toFixed(4) + ', ' + lng.toFixed(4))}`;
            document.getElementById('cart-location-text').textContent = locationText;
            document.getElementById('selected-seat-hero').textContent = locationText;

            playSoundNotification('success');
            closeSeatModal();
        }
 
        // Checkout Cart Trigger
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
 
            // Trigger M-Pesa STK Screen
            let total = 0;
            basket.forEach(i => total += (i.price * i.quantity));
 
            document.getElementById('mpesa-amount').textContent = `Ksh ${total.toLocaleString()}`;
            document.getElementById('mpesa-simulation-overlay').classList.remove('hidden');
 
            // Simulate Buzz vibration sound
            playSoundNotification('alert');
        }

        function cancelMpesaSimulation() {
            document.getElementById('mpesa-simulation-overlay').classList.add('hidden');
        }

        // Confirm STK Push PIN -> backend Order placement!
        async function confirmMpesaSimulation() {
            const pin = document.getElementById('mpesa-pin-input').value;
            if (!pin || pin.length < 4) {
                alert("Please input your 4-digit M-Pesa PIN!");
                return;
            }
 
            const modalContainer = document.querySelector('#mpesa-simulation-overlay .phone-buzz');
            const originalContentHtml = modalContainer.innerHTML;
 
            modalContainer.innerHTML = `
                <div class="text-center space-y-4 py-2">
                    <div class="relative w-12 h-12 mx-auto flex items-center justify-center">
                        <div class="absolute inset-0 border-4 border-[#00A082]/10 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-[#00A082] border-t-transparent rounded-full animate-spin"></div>
                        <i class="fas fa-fingerprint text-xl text-[#00A082]"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-[9px] font-black text-[#2D3748] tracking-wider uppercase">Authenticating...</h4>
                        <p class="text-[9px] text-zinc-500 max-w-[180px] mx-auto leading-relaxed">
                            Securing transaction. Please wait.
                        </p>
                    </div>
                </div>
            `;
 
            await new Promise(resolve => setTimeout(resolve, 2000));
 
            // Place actual order first in database
            const orderPayload = {
                user_id: currentUser.id,
                vendor_id: basket[0].vendorId,
                seat_location: selectedSeat,
                items: basket.map(i => ({ product_id: i.id, quantity: i.quantity }))
            };
 
            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderPayload)
                });
 
                const data = await res.json();
                if (res.ok) {
                    const orderId = data.order.id;
 
                    // Trigger M-Pesa simulated payment callback endpoint
                    const payRes = await fetch(`/api/orders/${orderId}/pay`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ phone: currentUser.phone })
                    });
 
                    if (payRes.ok) {
                        playSoundNotification('success');
                        activeOrder = await payRes.json();
 
                        // Clear basket
                        basket = [];
                        renderBasketTray();
 
                        // Close modal and transition customer to active radar
                        document.getElementById('mpesa-simulation-overlay').classList.add('hidden');
                        modalContainer.innerHTML = originalContentHtml;
 
                        document.getElementById('cust-main').classList.add('hidden');
                        document.getElementById('cust-tracker').classList.remove('hidden');
 
                        // Sync real-time UI components
                        syncRealTimeEngine();
                    } else {
                        alert("M-Pesa transaction validation failed.");
                        modalContainer.innerHTML = originalContentHtml;
                    }
                } else {
                    alert(data.message);
                    modalContainer.innerHTML = originalContentHtml;
                }
            } catch (e) {
                console.error(e);
                alert("An error occurred during payment processing.");
                modalContainer.innerHTML = originalContentHtml;
            }
        }

        function resetTrackerDemo() {
            activeOrder = null;
            hasBeepedForArrival = false;
            document.getElementById('tracker-qr-container').classList.add('hidden');
            document.getElementById('cust-tracker').classList.add('hidden');
            document.getElementById('cust-main').classList.remove('hidden');
        }

        // Quick login & switcher for other roles/dashboard columns
        let activeVendorUser = 'vendor@justfeast.com';
        let activeRunnerUser = 'runner@justfeast.com';

        async function quickSwitchVendor(email) {
            activeVendorUser = email;
            syncRealTimeEngine();
        }

        // REAL-TIME SYNCHRONIZATION POLLING LOGIC
        async function syncRealTimeEngine() {
            // A. Customer status tracking
            if (activeOrder && activeOrder.order) {
                const orderId = activeOrder.order.id;
                try {
                    const res = await fetch(`/api/orders/${orderId}`);
                    if (res.ok) {
                        const updated = await res.json();
                        updateCustomerRadarUI(updated);
                    }
                } catch (e) { console.log(e); }
            }

            // B. Vendor portal queue update
            try {
                // We use mock user Alex Vendor email
                const vendorUserRes = await fetch('/api/auth/login-as', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: activeVendorUser })
                });

                if (vendorUserRes.ok) {
                    const vendorData = await vendorUserRes.json();
                    const vendorUser = vendorData.user;

                    document.getElementById('vendor-assigned-name').textContent = `${vendorUser.name} (Assigned Staff)`;

                    // Load active vendor queue
                    const qRes = await fetch(`/api/orders/vendor?user_id=${vendorUser.id}`);
                    if (qRes.ok) {
                        const queue = await qRes.json();
                        renderVendorQueue(queue, vendorUser);
                    }
                }
            } catch (e) { console.log(e); }

            // C. Runner App deliveries
            try {
                const runnerUserRes = await fetch('/api/auth/login-as', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: activeRunnerUser })
                });

                if (runnerUserRes.ok) {
                    const runnerData = await runnerUserRes.json();
                    const runnerUser = runnerData.user;

                    // Fetch assigned deliveries
                    const dRes = await fetch(`/api/runner/deliveries?user_id=${runnerUser.id}`);
                    if (dRes.ok) {
                        const deliveries = await dRes.json();
                        renderRunnerDeliveries(deliveries);
                    }
                }
            } catch (e) { console.log(e); }

            // D. Admin Dashboard metrics & heatmap updates
            try {
                const adminStatsRes = await fetch('/api/admin/stats');
                if (adminStatsRes.ok) {
                    const stats = await adminStatsRes.json();
                    updateAdminDashboardUI(stats);
                }
            } catch (e) { console.log(e); }
        }

        // Live UI update modules
        function updateCustomerRadarUI(order) {
            // Update tracking timeline step checkmarks
            const status = order.order_status;

            // Secret PIN displayed
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
                        playSoundNotification('alert');
                        setTimeout(() => playSoundNotification('beep'), 300);
                        setTimeout(() => playSoundNotification('success'), 600);
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

            // Reset checklist state classes
            [stepCreated, stepPreparing, stepReady, stepEnroute].forEach(el => {
                el.children[0].className = "w-5 h-5 rounded-full bg-white border border-[#E7E8DD] text-zinc-450 flex items-center justify-center text-[10px] font-bold";
                el.children[1].className = "text-xs font-semibold text-zinc-400";
            });

            if (status === 'accepted') {
                stepCreated.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                stepCreated.children[0].innerHTML = `<i class="fas fa-check"></i>`;
                stepCreated.children[1].className = "text-xs font-black text-zinc-850";
            } else if (status === 'preparing') {
                [stepCreated, stepPreparing].forEach((el, index) => {
                    el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                    el.children[0].innerHTML = `<i class="fas fa-${index === 0 ? 'check' : 'spinner animate-spin'}"></i>`;
                    el.children[1].className = "text-xs font-black text-zinc-850";
                });
            } else if (status === 'runner_assigned' || status === 'ready') {
                [stepCreated, stepPreparing, stepReady].forEach((el, index) => {
                    el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                    el.children[0].innerHTML = `<i class="fas fa-${index < 2 ? 'check' : 'bell animate-bounce'}"></i>`;
                    el.children[1].className = "text-xs font-black text-zinc-850";
                });
            } else if (status === 'en_route') {
                [stepCreated, stepPreparing, stepReady, stepEnroute].forEach((el, index) => {
                    el.children[0].className = "w-5 h-5 rounded-full bg-[#05A357] text-white flex items-center justify-center text-[10px] font-bold";
                    el.children[0].innerHTML = `<i class="fas fa-${index < 3 ? 'check' : 'truck animate-pulse'}"></i>`;
                    el.children[1].className = "text-xs font-black text-zinc-850";
                });
            } else if (status === 'delivered') {
                playSoundNotification('success');
                alert("🎉 Order successfully delivered to your seat! Enjoy the concert!");
                resetTrackerDemo();
            }
        }

        // Render Kitchen list
        function renderVendorQueue(orders, vendorUser) {
            const container = document.getElementById('vendor-orders-container');
            const queueCount = document.getElementById('vendor-queue-count');
            const totalSales = document.getElementById('vendor-sales-amount');

            // Load menu for stock toggle controls (first load only)
            const stockContainer = document.getElementById('vendor-stock-items');
            if (stockContainer.children.length === 0) {
                renderVendorStockControls(vendorUser);
            }

            // Sum total local sales
            let salesSum = 0;
            orders.forEach(o => salesSum += parseFloat(o.total_amount));
            totalSales.textContent = `Ksh ${salesSum.toLocaleString()}`;

            const pendingOrders = orders.filter(o => ['accepted', 'preparing', 'ready', 'runner_assigned', 'en_route'].includes(o.order_status));
            queueCount.textContent = `${pendingOrders.length} Active`;

            if (pendingOrders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 text-zinc-600 space-y-2">
                        <i class="fas fa-utensils text-2xl"></i>
                        <p class="text-xs">No active paid orders in the kitchen.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            pendingOrders.forEach(o => {
                const card = document.createElement('div');
                card.className = 'bg-zinc-900 border border-zinc-800/80 p-3.5 rounded-2xl space-y-2.5 relative overflow-hidden';

                // Add status background colors
                let statusBadge = '';
                if (o.order_status === 'accepted') statusBadge = 'bg-brand-orange/20 text-brand-orange';
                else if (o.order_status === 'preparing') statusBadge = 'bg-brand-rose/20 text-brand-rose animate-pulse';
                else statusBadge = 'bg-brand-emerald/20 text-brand-emerald';

                card.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] text-zinc-500 font-bold uppercase">Order #${o.id}</span>
                            <h4 class="text-xs font-bold text-white">${o.user.name}</h4>
                        </div>
                        <span class="text-[8px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full ${statusBadge}">${o.order_status}</span>
                    </div>
                    <div class="text-[10px] text-zinc-400 space-y-1 py-1 border-y border-zinc-900">
                        ${o.items.map(item => `<div>• ${item.quantity}x ${item.product.name}</div>`).join('')}
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-zinc-500">
                        <span>Seat: <strong class="text-white">${o.seat_location.section}, ${o.seat_location.row}, ${o.seat_location.seat}</strong></span>
                        <span class="font-extrabold text-brand-rose">Ksh ${parseFloat(o.total_amount).toLocaleString()}</span>
                    </div>
                    <div class="flex items-center gap-2 pt-1.5">
                        ${o.order_status === 'accepted'
                            ? `<button onclick="updateVendorOrderStatus(${o.id}, 'preparing')" class="w-full py-1.5 bg-brand-rose hover:bg-brand-rose text-white rounded-lg text-[10px] font-bold transition">Start Preparing</button>`
                            : o.order_status === 'preparing'
                                ? `<button onclick="updateVendorOrderStatus(${o.id}, 'ready')" class="w-full py-1.5 bg-brand-emerald hover:bg-emerald-600 text-white rounded-lg text-[10px] font-bold transition">Mark Ready & Dispatch</button>`
                                : `<span class="text-[9px] text-zinc-500 block text-center w-full font-medium">Runner Assigned: ${o.delivery ? 'En Route' : 'Waiting...'}</span>`
                        }
                    </div>
                `;
                container.appendChild(card);
            });
        }

        async function updateVendorOrderStatus(orderId, status) {
            playSoundNotification('success');
            try {
                const res = await fetch(`/api/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });

                if (res.ok) {
                    syncRealTimeEngine();
                }
            } catch (e) { console.error(e); }
        }

        // Render stock toggles
        function renderVendorStockControls(vendorUser) {
            const container = document.getElementById('vendor-stock-items');
            container.innerHTML = '';

            const vendor = vendors.find(v => v.user_id === vendorUser.id);
            if (!vendor) return;

            vendor.products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center bg-zinc-950 px-3 py-2 rounded-xl border border-zinc-900 text-xs';

                const checked = p.stock_status === 'in_stock' ? 'checked' : '';
                item.innerHTML = `
                    <span class="font-semibold text-zinc-300">${p.name}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="${p.id}" onchange="toggleStock(${p.id})" class="sr-only peer" ${checked}>
                        <div class="w-7 h-4 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-brand-rose"></div>
                    </label>
                `;
                container.appendChild(item);
            });
        }

        async function toggleStock(productId) {
            playSoundNotification('beep');
            try {
                const res = await fetch(`/api/products/${productId}/toggle-stock`, { method: 'POST' });
                if (res.ok) {
                    loadVendors(); // reload menus
                }
            } catch (e) { console.error(e); }
        }

        // Render Runner tasks
        function renderRunnerDeliveries(deliveries) {
            const container = document.getElementById('runner-active-card-container');
            const verifyBox = document.getElementById('runner-verification-box');
            const mapGuide = document.getElementById('runner-map-guide');

            if (deliveries.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-20 text-zinc-600 bg-zinc-900/30 border border-zinc-900 rounded-2xl p-6 space-y-3">
                        <i class="fas fa-radar text-3xl text-zinc-700"></i>
                        <h4 class="text-xs font-bold text-zinc-500">Awaiting Kitchen Orders</h4>
                        <p class="text-[10px] text-zinc-600">You will automatically receive a task notification when a vendor marks an order as 'Ready'.</p>
                    </div>
                `;
                verifyBox.classList.add('hidden');
                mapGuide.classList.add('hidden');
                
                if (runnerMovementInterval) {
                    clearInterval(runnerMovementInterval);
                    runnerMovementInterval = null;
                }
                return;
            }

            const activeDel = deliveries[0];
            container.innerHTML = '';
            verifyBox.classList.remove('hidden');
            mapGuide.classList.remove('hidden');

            const loc = activeDel.order.seat_location;
            let destText = "";
            let guideText = "";
            
            if (loc.type === 'gps') {
                destText = `GPS: ${parseFloat(loc.latitude).toFixed(5)}, ${parseFloat(loc.longitude).toFixed(5)}`;
                guideText = `GPS Drop Pin: "${loc.description || 'No description provided'}"`;
            } else {
                destText = `${loc.section}, Row ${loc.row}, Seat ${loc.seat}`;
                guideText = `${loc.section} - ROW ${loc.row} - SEAT ${loc.seat}`;
            }

            // Render route guide coordinates text
            document.getElementById('runner-target-section-tag').textContent = guideText;

            const card = document.createElement('div');
            card.className = 'bg-zinc-900 border border-zinc-800 p-4 rounded-2xl space-y-3 shadow-lg';

            let statusBtn = '';
            if (activeDel.status === 'pending') {
                statusBtn = `<button onclick="updateRunnerDelivery(${activeDel.id}, 'picked_up')" class="w-full py-2 bg-brand-rose hover:bg-brand-rose text-white rounded-lg text-xs font-bold transition">Confirm Pickup from Kitchen</button>`;
            } else if (activeDel.status === 'picked_up') {
                statusBtn = `<button onclick="updateRunnerDelivery(${activeDel.id}, 'en_route')" class="w-full py-2 bg-brand-rose hover:bg-brand-rose text-white rounded-lg text-xs font-bold transition">Start Navigation En-Route</button>`;
            } else {
                statusBtn = `<span class="text-[10px] text-brand-emerald text-center block font-bold animate-pulse"><i class="fas fa-location-arrow mr-1"></i> Delivering — Proximity Alert Active</span>`;
            }

            let liveTrackingHtml = '';
            if (activeDel.status === 'en_route') {
                liveTrackingHtml = `
                    <div id="runner-live-tracking-panel" class="mt-3.5 p-2.5 bg-zinc-950 rounded-xl border border-zinc-900 space-y-2">
                        <div class="flex justify-between text-[9px] text-zinc-500">
                            <span>Current Lat/Lng:</span>
                            <span id="runner-current-coords-lbl" class="font-bold text-white">${runnerLat.toFixed(5)}, ${runnerLng.toFixed(5)}</span>
                        </div>
                        <div class="flex justify-between text-[9px] text-zinc-500">
                            <span>Distance Remaining:</span>
                            <span id="runner-distance-lbl" class="font-bold text-brand-orange">Calculating...</span>
                        </div>
                        <div class="w-full bg-zinc-900 rounded-full h-1.5 overflow-hidden">
                            <div id="runner-progress-bar" class="bg-brand-rose h-1.5 rounded-full transition-all" style="width: 0%"></div>
                        </div>
                    </div>
                `;
            }

            card.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-zinc-500 font-bold uppercase">Delivery ID #${activeDel.id}</span>
                    <span class="text-[9px] bg-brand-rose/20 text-brand-rose px-2 py-0.5 rounded-full font-bold uppercase">${activeDel.status}</span>
                </div>

                <div class="space-y-1.5 py-2 border-y border-zinc-900 text-xs">
                    <p class="text-zinc-500">Pickup Location:</p>
                    <p class="font-bold text-white text-xs">${activeDel.order.vendor.business_name} Stall</p>

                    <p class="text-zinc-500 pt-1">Destination coordinates:</p>
                    <p class="font-extrabold text-brand-orange text-xs">${destText}</p>
                    
                    ${liveTrackingHtml}
                </div>

                <div class="pt-1">
                    ${statusBtn}
                </div>
            `;
            container.appendChild(card);

            // Movement simulation driver
            if (activeDel.status === 'en_route') {
                let targetLat = -1.32588;
                let targetLng = 36.79941;

                if (loc.type === 'gps') {
                    targetLat = parseFloat(loc.latitude);
                    targetLng = parseFloat(loc.longitude);
                } else {
                    const sec = (loc.section || '').toLowerCase();
                    if (sec.includes('vip a')) {
                        targetLat = -1.32538; targetLng = 36.79881;
                    } else if (sec.includes('vip b')) {
                        targetLat = -1.32538; targetLng = 36.80001;
                    } else if (sec.includes('gen a') || sec.includes('general a')) {
                        targetLat = -1.32638; targetLng = 36.79881;
                    } else {
                        targetLat = -1.32638; targetLng = 36.80001;
                    }
                }

                if (!runnerMovementInterval) {
                    runnerLat = -1.32588;
                    runnerLng = 36.79941;
                    runnerStartLat = -1.32588;
                    runnerStartLng = 36.79941;

                    const totalDistance = Math.sqrt(Math.pow(targetLat - runnerStartLat, 2) + Math.pow(targetLng - runnerStartLng, 2));

                    runnerMovementInterval = setInterval(async () => {
                        // Move 20% closer on each tick
                        runnerLat += (targetLat - runnerLat) * 0.20;
                        runnerLng += (targetLng - runnerLng) * 0.20;

                        const currentRemainingDistance = Math.sqrt(Math.pow(targetLat - runnerLat, 2) + Math.pow(targetLng - runnerLng, 2));
                        const progress = totalDistance > 0 ? Math.min(100, Math.round(((totalDistance - currentRemainingDistance) / totalDistance) * 100)) : 100;

                        try {
                            const locRes = await fetch(`/api/runner/deliveries/${activeDel.id}/location`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ latitude: runnerLat, longitude: runnerLng })
                            });

                            if (locRes.ok) {
                                const locData = await locRes.json();
                                if (locData.reached || progress >= 96) {
                                    clearInterval(runnerMovementInterval);
                                    runnerMovementInterval = null;
                                    runnerLat = targetLat;
                                    runnerLng = targetLng;

                                    await fetch(`/api/runner/deliveries/${activeDel.id}/location`, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ latitude: targetLat, longitude: targetLng })
                                    });

                                    // Beep notification when runner arrives!
                                    playSoundNotification('alert');
                                    setTimeout(() => playSoundNotification('success'), 300);
                                    
                                    // Live reload UI
                                    syncRealTimeEngine();
                                }
                            }
                        } catch (e) { console.error(e); }

                        const coordsLbl = document.getElementById('runner-current-coords-lbl');
                        const distLbl = document.getElementById('runner-distance-lbl');
                        const pBar = document.getElementById('runner-progress-bar');
                        if (coordsLbl) coordsLbl.textContent = `${runnerLat.toFixed(5)}, ${runnerLng.toFixed(5)}`;
                        if (distLbl) distLbl.textContent = `${(currentRemainingDistance * 111000).toFixed(1)} meters`;
                        if (pBar) pBar.style.width = `${progress}%`;
                    }, 1500);
                }
            } else {
                if (runnerMovementInterval) {
                    clearInterval(runnerMovementInterval);
                    runnerMovementInterval = null;
                }
            }
        }

        async function updateRunnerDelivery(delId, status) {
            playSoundNotification('success');
            try {
                const res = await fetch(`/api/runner/deliveries/${delId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });

                if (res.ok) {
                    syncRealTimeEngine();
                }
            } catch (e) { console.error(e); }
        }

        async function verifyRunnerDelivery() {
            // Find active delivery ID
            const runnerUserRes = await fetch('/api/auth/login-as', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: activeRunnerUser })
            });

            if (runnerUserRes.ok) {
                const runnerData = await runnerUserRes.json();
                const runnerUser = runnerData.user;

                const dRes = await fetch(`/api/runner/deliveries?user_id=${runnerUser.id}`);
                if (dRes.ok) {
                    const deliveries = await dRes.json();
                    if (deliveries.length > 0) {
                        const delId = deliveries[0].id;
                        const pin = document.getElementById('runner-pin-input').value;

                        try {
                            const verifyRes = await fetch(`/api/runner/deliveries/${delId}/verify`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ pin })
                            });

                            if (verifyRes.ok) {
                                playSoundNotification('success');
                                alert("🎉 Delivery verified successfully! Confetti triggered!");
                                document.getElementById('runner-pin-input').value = '';
                                syncRealTimeEngine();
                            } else {
                                const err = await verifyRes.json();
                                alert(err.message);
                            }
                        } catch (e) { console.error(e); }
                    }
                }
        }

        let html5QrcodeScanner = null;

        function toggleRunnerQRScanner() {
            const container = document.getElementById('runner-qr-reader-container');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                
                html5QrcodeScanner = new Html5Qrcode("runner-qr-reader");
                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: { width: 180, height: 180 }
                    },
                    (decodedText, decodedResult) => {
                        if (decodedText.startsWith("justfeast-delivery-verify:")) {
                            const parts = decodedText.split(":");
                            const pin = parts[2];
                            document.getElementById('runner-pin-input').value = pin;
                            playSoundNotification('success');
                            
                            stopRunnerQRScanner();
                            verifyRunnerDelivery();
                        } else {
                            alert("Invalid QR Code scanned!");
                        }
                    },
                    (errorMessage) => {}
                ).catch((err) => {
                    console.error("Unable to start scanner:", err);
                    alert("Camera access failed. Please enter the PIN manually or use the Simulate QR Scan button.");
                    container.classList.add('hidden');
                });
            } else {
                stopRunnerQRScanner();
            }
        }

        function stopRunnerQRScanner() {
            const container = document.getElementById('runner-qr-reader-container');
            container.classList.add('hidden');
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner = null;
                }).catch(err => console.error(err));
            }
        }

        async function simulateQRScan() {
            playSoundNotification('beep');
            
            const runnerUserRes = await fetch('/api/auth/login-as', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: activeRunnerUser })
            });

            if (runnerUserRes.ok) {
                const runnerData = await runnerUserRes.json();
                const runnerUser = runnerData.user;

                const dRes = await fetch(`/api/runner/deliveries?user_id=${runnerUser.id}`);
                if (dRes.ok) {
                    const deliveries = await dRes.json();
                    if (deliveries.length > 0) {
                        const activeDel = deliveries[0];
                        const pin = activeDel.verification_pin;
                        document.getElementById('runner-pin-input').value = pin;
                        
                        alert(`[SIMULATION] Scanned QR Code containing payload: "justfeast-delivery-verify:${activeDel.id}:${pin}". PIN has been autofilled.`);
                        verifyRunnerDelivery();
                    } else {
                        alert("No active deliveries found to scan!");
                    }
                }
            }
        }

        // Admin Dashboard updating UI elements
        function updateAdminDashboardUI(stats) {
            document.getElementById('admin-total-revenue').textContent = `Ksh ${stats.total_revenue.toLocaleString()}`;
            document.getElementById('admin-orders-count').textContent = stats.orders_count;
            document.getElementById('admin-delivery-speed').textContent = `${stats.avg_delivery_time_mins} minutes`;

            // Render Heatmaps color intensities based on seating density count
            const heatmap = stats.section_heatmap;
            const sections = ['vip_a', 'vip_b', 'gen_a', 'gen_b'];

            sections.forEach(sec => {
                const count = heatmap[sec];
                const svgElement = document.getElementById(`heat-${sec.replace('_', '-')}`);

                if (svgElement) {
                    if (count === 0) {
                        svgElement.setAttribute('fill', '#27272a'); // default zinc dark
                    } else if (count <= 2) {
                        svgElement.setAttribute('fill', 'rgba(139, 92, 246, 0.4)'); // Light purple heat
                    } else if (count <= 5) {
                        svgElement.setAttribute('fill', 'rgba(236, 72, 153, 0.7)'); // Medium pink heat
                    } else {
                        svgElement.setAttribute('fill', 'rgba(239, 68, 68, 0.9)'); // High red heat!
                    }
                }
            });

            // Render Vendor contracts revenue splits list
            const vendorsContainer = document.getElementById('admin-vendors-list');
            vendorsContainer.innerHTML = '';

            stats.vendor_revenue.forEach(vr => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center text-xs py-2 border-b border-zinc-900';

                row.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="text-base">${vr.logo_url}</span>
                        <div>
                            <p class="font-bold text-white">${vr.business_name}</p>
                            <p class="text-[9px] text-zinc-500">${vr.orders_count} orders completed</p>
                        </div>
                    </div>
                    <span class="font-extrabold text-brand-emerald">Ksh ${parseFloat(vr.revenue).toLocaleString()}</span>
                `;
                vendorsContainer.appendChild(row);
            });
        }

        // Toggle Poor Connection Mode (Concert Network throttling)
        function toggleCongestionMode() {
            congestionMode = !congestionMode;
            playSoundNotification('alert');

            const btn = document.getElementById('congestion-toggle');
            const icon = document.getElementById('congestion-wifi-icon');
            const text = document.getElementById('congestion-btn-text');
            const signal = document.getElementById('signal-icon');

            if (congestionMode) {
                btn.className = "bg-brand-pink/20 border border-brand-pink/50 text-brand-orange px-4 py-2 rounded-xl flex items-center gap-2 hover:bg-brand-pink/30 transition font-semibold animate-pulse";
                icon.className = "fas fa-wifi-slash text-brand-orange";
                text.textContent = "Network Congested (PWA Offline Cache Active)";
                signal.innerHTML = `<i class="fas fa-exclamation-triangle text-brand-orange mr-1"></i> Poor 2G`;

                // Slow down polling to simulate poor networks
                clearInterval(pollingInterval);
                pollingInterval = setInterval(syncRealTimeEngine, 8000);

                alert("⚠️ Simulated Poor network congestion! API Polling throttled to 8 seconds. Caching policies applied from sw.js local storage.");
            } else {
                btn.className = "bg-zinc-900 border border-zinc-800 text-zinc-400 px-4 py-2 rounded-xl flex items-center gap-2 hover:border-brand-pink transition font-semibold";
                icon.className = "fas fa-wifi text-zinc-500";
                text.textContent = "Simulate Concert Network Congestion";
                signal.innerHTML = `<i class="fas fa-signal text-brand-emerald"></i> 5G`;

                // Return to high frequency polling
                clearInterval(pollingInterval);
                pollingInterval = setInterval(syncRealTimeEngine, 2000);

                alert("🟢 Signal restored! Active 5G connection established. API Polling updated back to 2 seconds.");
            }
        }

        // Role tab switcher for single layouts
        function switchPortal(portal) {
            playSoundNotification('beep');

            const cols = {
                customer: document.getElementById('col-customer'),
                vendor: document.getElementById('col-vendor'),
                runner: document.getElementById('col-runner'),
                admin: document.getElementById('col-admin')
            };

            const tabs = {
                all: document.getElementById('tab-all'),
                customer: document.getElementById('tab-customer'),
                vendor: document.getElementById('tab-vendor'),
                runner: document.getElementById('tab-runner'),
                admin: document.getElementById('tab-admin')
            };

            // Reset tab styles
            Object.keys(tabs).forEach(k => {
                tabs[k].className = "px-3.5 py-1.5 rounded-lg text-zinc-400 hover:text-white transition-all duration-300";
            });

            // Set active tab style
            tabs[portal].className = "px-3.5 py-1.5 rounded-lg transition-all duration-300 bg-brand-rose text-white shadow shadow-brand-rose/30";

            if (portal === 'all') {
                // Show all columns side-by-side
                Object.keys(cols).forEach(k => {
                    cols[k].classList.remove('hidden');
                });
            } else {
                // Hide all except active
                Object.keys(cols).forEach(k => {
                    if (k === portal) {
                        cols[k].classList.remove('hidden');
                    } else {
                        cols[k].classList.add('hidden');
                    }
                });
            }
        }

        // PWA Install Event Management
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // Display install banner
            document.getElementById('pwa-install-banner').classList.remove('hidden');
        });

        function checkPWAPrompt() {
            // Register service worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service Worker registered successfully!', reg))
                    .catch((err) => console.log('Service Worker registration failed!', err));
            }
        }

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the PWA install prompt');
                    }
                    deferredPrompt = null;
                    document.getElementById('pwa-install-banner').classList.add('hidden');
                });
            }
        }

        function dismissPWABanner() {
            document.getElementById('pwa-install-banner').classList.add('hidden');
        }
    </script>
</body>
</html>
