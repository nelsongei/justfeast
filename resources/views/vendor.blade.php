<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast Vendor — Kitchen Preparation Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/jm.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/jm.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: {
                            rose: '#FFC244',
                            orange: '#FFC244',
                            amber: '#FFC244',
                            emerald: '#A31D1D',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* --- Glovo Look & Feel Branding --- */
        body {
            background-color: #FFFDF9 !important;
            color: #2D3748 !important;
        }
        .bg-zinc-950 {
            background-color: #FFFDF9 !important;
        }
        .bg-zinc-900 {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
        }
        .bg-zinc-900\/40 {
            background-color: #FFFDF9 !important;
            border-color: #E2E8F0 !important;
        }
        .bg-zinc-900\/50 {
            background-color: #F7F9FA !important;
            border-color: #E2E8F0 !important;
        }
        .bg-zinc-900\/60 {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
        }
        .bg-zinc-900\/95 {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-top: 1px solid #E2E8F0 !important;
        }
        .bg-zinc-950\/80 {
            background-color: rgba(255, 253, 249, 0.8) !important;
            border-bottom: 1px solid #E2E8F0 !important;
        }
        .bg-black\/95, .bg-black\/90, .bg-black\/80 {
            background-color: rgba(0, 0, 0, 0.6) !important;
            backdrop-filter: blur(8px);
        }
        
        /* Borders */
        .border-zinc-900, .border-zinc-900\/60, .border-zinc-800, .border-zinc-800\/60, .border-zinc-800\/80, .border-zinc-850 {
            border-color: #E2E8F0 !important;
        }
        
        /* Cards */
        .glass-card {
            background: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04) !important;
            color: #2D3748 !important;
        }
        .glass-card:hover {
            border-color: #FFC244 !important;
            box-shadow: 0 12px 32px rgba(255, 194, 68, 0.15) !important;
        }
        
        /* Inputs & Selects */
        .glass-input, select {
            background: #F7F9FA !important;
            border: 1px solid #E2E8F0 !important;
            color: #2D3748 !important;
        }
        .glass-input:focus, select:focus {
            border-color: #FFC244 !important;
            box-shadow: 0 0 0 3px rgba(255, 194, 68, 0.15) !important;
            color: #2D3748 !important;
        }
        .glass-input::placeholder {
            color: #A0AEC0 !important;
        }
        
        /* Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #FFFDF9 !important; }
        ::-webkit-scrollbar-thumb { background: #FFC244 !important; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #E0A325 !important; }

        /* Typography & Text */
        h1, h2, h3, h4, h5, h6, .text-zinc-100, .text-zinc-200, .text-zinc-300 {
            color: #2D3748 !important;
        }
        .text-zinc-400, .text-zinc-500, .text-zinc-600 {
            color: #718096 !important;
        }
        
        /* Exceptions: Keep text-white for dark background badges & buttons */
        .text-white, button.text-white, .bg-brand-rose .text-white, .bg-brand-emerald .text-white,
        .bg-gradient-to-r .text-white, .bg-zinc-900 .text-white, .bg-emerald-600 .text-white,
        span.text-white, i.text-white, .bg-gradient-to-br .text-white, .text-white i {
            color: #FFFFFF !important;
        }
        
        /* Glovo Header Brand title - force gradient to Glovo Brand colors */
        h1.tracking-wider.bg-gradient-to-r {
            background-image: linear-gradient(to right, #FFC244, #A31D1D) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            color: transparent !important;
        }
        
        /* Glovo buttons & active elements text color adjustment */
        button.bg-gradient-to-r.from-brand-rose, 
        button.bg-brand-rose, 
        button.bg-gradient-to-r.from-brand-rose *, 
        button.bg-brand-rose *,
        .bg-gradient-to-r.from-brand-rose, .bg-brand-rose, .from-brand-rose, .to-brand-orange {
            color: #2D3748 !important;
        }
        .bg-gradient-to-r.from-brand-rose i, .bg-brand-rose i {
            color: #2D3748 !important;
        }
        
        /* Successful state text/badges (Glovo Green) */
        .bg-brand-emerald, .bg-gradient-to-r.from-brand-emerald, .text-brand-emerald, .bg-brand-emerald\/20 {
            color: #A31D1D !important;
        }
        .bg-brand-emerald, .bg-gradient-to-r.from-brand-emerald {
            background-color: #A31D1D !important;
            background-image: none !important;
            color: #FFFFFF !important;
        }
        .bg-brand-emerald\/20 {
            background-color: rgba(0, 160, 130, 0.1) !important;
        }
        
        /* Rose/Orange text elements should be changed to Glovo Green or Charcoal for readability */
        .text-brand-rose, .text-brand-orange {
            color: #A31D1D !important;
        }
        
        /* Stadium map overrides */
        .stadium-grid {
            background-image: radial-gradient(rgba(0, 160, 130, 0.08) 1px, transparent 1px) !important;
            background-color: #FFFDF9 !important;
        }
        polygon.spotlight {
            fill: url(#grad-spot-glovo) !important;
        }
        
        /* Active radar color adjustments */
        .radar-sweep {
            background: conic-gradient(from 0deg at 50% 50%, rgba(255, 194, 68, 0.25) 0deg, rgba(255, 194, 68, 0) 120deg) !important;
        }
        .pulse-ring {
            border-color: rgba(255, 194, 68, 0.3) !important;
        }
        
        /* SVG Stadium seats styling */
        svg path.fill-zinc-900 {
            fill: #FFFFFF !important;
            stroke: #E2E8F0 !important;
        }
        svg path.fill-zinc-900:hover {
            fill: rgba(255, 194, 68, 0.15) !important;
            stroke: #FFC244 !important;
        }
        svg path.fill-brand-rose\/20 {
            fill: rgba(255, 194, 68, 0.3) !important;
            stroke: #FFC244 !important;
        }
        svg path.fill-brand-orange\/20 {
            fill: rgba(0, 160, 130, 0.2) !important;
            stroke: #A31D1D !important;
        }
        svg rect[stroke="#f43f5e"], svg rect[stroke="#8b5cf6"] {
            fill: #FFFDF9 !important;
            stroke: #FFC244 !important;
        }
        svg text[fill="#f43f5e"], svg text[fill="#8b5cf6"] {
            fill: #E0A325 !important;
        }

        button {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 9999px !important; /* Extremely rounded/pill buttons for Glovo */
        }
        button:hover {
            transform: translateY(-1px);
        }
        button:active {
            transform: translateY(1.5px) scale(0.98);
        }
    </style>
    <script>
        const API_BASE = "{{ url('/api') }}";
    </script>
</head>
<body class="bg-[#FFFDF9] text-[#2D3748] font-sans min-h-screen relative overflow-x-hidden pb-12">

    <!-- Glowing Background blobs -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-[#FFC244]/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-[#A31D1D]/10 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- Main Workspace Wrapper -->
    <div class="w-full max-w-[1700px] mx-auto px-3 sm:px-6 py-4 sm:py-8 relative z-10">

        <!-- Auth Picker Screen (if not logged in) -->
        <div id="vendor-auth" class="glass-card rounded-3xl p-6 sm:p-8 max-w-md mx-auto text-center space-y-6 shadow-md">
            <div class="w-16 h-16 bg-[#FFC244] rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-[#FFC244]/15 text-3xl border border-[#E0A325]">
                <span>🏪</span>
            </div>
            <div class="space-y-2">
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[#2D3748] font-sans">Kitchen Staff Portal</h2>
                <p class="text-xs text-zinc-500">Enter your phone number to log in via secure SMS OTP verification.</p>
            </div>

            <!-- Phone Step -->
            <div id="vendor-auth-step-phone" class="space-y-3 pt-2">
                <input type="text" id="vendor-phone-input" placeholder="+254712345678" class="w-full p-3.5 rounded-2xl bg-[#F7F9FA] border border-[#E2E8F0] text-sm text-[#2D3748] focus:border-[#FFC244] focus:outline-none font-bold text-center">
                <button onclick="sendVendorOTP()" class="w-full p-3.5 rounded-2xl bg-[#A31D1D] hover:bg-[#841313] text-white font-extrabold text-xs transition shadow-md">
                    Send Verification Code
                </button>
            </div>

            <!-- OTP Step -->
            <div id="vendor-auth-step-otp" class="hidden space-y-3 pt-2">
                <p class="text-[11px] text-zinc-500 font-semibold" id="vendor-otp-status-text">Code sent to phone</p>
                <div id="vendor-otp-banner" class="hidden bg-[#ECFDF5] border border-[#A7F3D0] rounded-2xl p-3.5 text-center space-y-1 my-2">
                    <p class="text-[9px] uppercase tracking-widest text-[#047857] font-black">System Generated Login OTP</p>
                    <p class="text-3xl font-black tracking-widest text-[#05A357]" id="vendor-generated-otp-display">------</p>
                    <button type="button" onclick="autoFillVendorOTP()" class="text-[10px] font-extrabold text-[#047857] underline cursor-pointer inline-flex items-center gap-1">
                        <i class="fas fa-magic"></i> Auto-fill OTP Code
                    </button>
                </div>
                <input type="text" id="vendor-otp-input" placeholder="Enter 6-Digit Code" maxlength="6" class="w-full p-3.5 rounded-2xl bg-[#F7F9FA] border border-[#E2E8F0] text-base text-[#2D3748] focus:border-[#FFC244] focus:outline-none font-black text-center tracking-widest">
                <button onclick="verifyVendorOTP()" class="w-full p-3.5 rounded-2xl bg-[#05A357] hover:bg-[#048245] text-white font-extrabold text-xs transition shadow-md">
                    Verify & Access Dashboard
                </button>
                <button onclick="resetVendorAuthForm()" class="text-[10px] text-zinc-500 hover:text-zinc-700 block mx-auto font-bold pt-1">
                    ← Change Phone Number
                </button>
            </div>
        </div>

        <!-- Vendor Dashboard Portal -->
        <div id="vendor-dashboard" class="hidden space-y-4 sm:space-y-6">
            <!-- Header -->
            <header class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-10 sm:h-12 w-auto rounded-xl shadow-sm border border-black/10 shrink-0">
                    <div>
                        <h1 class="text-base sm:text-lg font-black tracking-tight text-[#0F172A] flex items-center gap-1.5" id="vendor-title">
                            Vendor Portal
                        </h1>
                        <p class="text-[10px] sm:text-xs text-[#64748B] flex items-center gap-1" id="live-event-banner">
                            <span class="w-2 h-2 rounded-full bg-[#05A357] animate-pulse"></span> Connected to Uhuru Park Stalls
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-auto sm:ml-0">
                    <div class="flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-bold text-[#0F172A]">
                        <span id="vendor-avatar" class="text-sm">🏪</span>
                        <span id="staff-name-pill" class="max-w-[110px] sm:max-w-none truncate">Staff</span>
                    </div>
                    <button onclick="logoutVendor()" class="text-[11px] sm:text-xs bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#991B1B] border border-[#FCA5A5] px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full font-bold transition flex items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </header>

            <!-- Dashboard Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

                <!-- Left Column (Queue & Metrics - 7 Cols) -->
                <div class="lg:col-span-7 space-y-4 sm:space-y-6">
                    <!-- Metrics Cards (4 Columns) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                        <div class="bg-white border border-[#E2E8F0] p-3 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-[#A31D1D]"></div>
                            <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Kitchen Queue</span>
                            <span class="text-base sm:text-xl font-black text-[#A31D1D]" id="vendor-queue-count">0 Active</span>
                        </div>
                        <div class="bg-white border border-[#E2E8F0] p-3 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-[#05A357]"></div>
                            <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Total Sales</span>
                            <span class="text-base sm:text-xl font-black text-[#05A357]" id="vendor-sales-amount">Ksh 0</span>
                        </div>
                        <div class="bg-white border border-[#E2E8F0] p-3 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-[#2563EB]"></div>
                            <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Delivered</span>
                            <span class="text-base sm:text-xl font-black text-[#2563EB]" id="vendor-completed-count">0 Delivered</span>
                        </div>
                        <div class="bg-white border border-[#E2E8F0] p-3 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-[#FFC244]"></div>
                            <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Menu Items</span>
                            <span class="text-base sm:text-xl font-black text-[#D97706]" id="vendor-menu-count">0 Items</span>
                        </div>
                    </div>

                    <!-- Kitchen Queue -->
                    <div class="bg-white border border-[#E2E8F0] p-4 sm:p-5 rounded-2xl flex flex-col min-h-[480px] sm:min-h-[580px] shadow-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0] mb-4">
                            <h3 class="text-xs font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-fire text-[#A31D1D]"></i> Live Kitchen Dispatch Board
                            </h3>
                            <span class="text-[10px] bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0] px-2.5 py-1 rounded-full font-bold">
                                <i class="fas fa-sync fa-spin mr-1 text-[8px]"></i> Auto-Syncing (2s)
                            </span>
                        </div>
                        
                        <div id="vendor-orders-container" class="flex-1 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4 pr-1">
                            <!-- Order cards list -->
                            <div class="col-span-full text-center py-16 sm:py-20 text-[#64748B] space-y-3">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl flex items-center justify-center mx-auto text-xl sm:text-2xl text-[#94A3B8]">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#0F172A]">Kitchen Queue Clear</h4>
                                    <p class="text-xs text-[#64748B]">New attendee orders will appear here automatically in real time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Menu & Stock Controls - 5 Cols) -->
                <div class="lg:col-span-5 space-y-4 sm:space-y-6">
                    <div class="bg-white border border-[#E2E8F0] p-4 sm:p-5 rounded-2xl flex flex-col min-h-[520px] sm:min-h-[660px] shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-[#E2E8F0] mb-4">
                            <h3 class="text-xs font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-store text-[#A31D1D]"></i> Stall Menu
                            </h3>
                            <div class="flex gap-1.5 items-center">
                                <div class="flex gap-1 bg-[#F8FAFC] p-1 rounded-full border border-[#E2E8F0]">
                                    <button type="button" onclick="switchVendorTab('stock')" id="btn-tab-stock" class="px-2.5 sm:px-3 py-1 text-[10px] font-extrabold rounded-full transition bg-[#A31D1D] text-white shadow-sm">Stock</button>
                                    <button type="button" onclick="switchVendorTab('menu')" id="btn-tab-menu" class="px-2.5 sm:px-3 py-1 text-[10px] font-extrabold rounded-full transition text-[#64748B]">All Items</button>
                                </div>
                                <button type="button" onclick="toggleAddProductForm()" id="btn-header-add" class="px-2.5 sm:px-3 py-1 bg-[#A31D1D] hover:bg-[#841313] text-white text-[10px] font-extrabold rounded-full shadow-sm flex items-center gap-1">
                                    <i class="fas fa-plus text-[9px]"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <!-- TAB: STOCK TOGGLE -->
                        <div id="vendor-tab-stock-content" class="flex-1 flex flex-col min-h-0">
                            <p class="text-[11px] text-[#64748B] mb-3 font-medium">Toggle switches to mark items "Sold Out" or "In Stock" on customer event apps.</p>
                            <div id="vendor-stock-items" class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                                <!-- Toggle switches checklist -->
                            </div>
                        </div>

                        <!-- TAB: EDIT MENU (ADD/EDIT/DELETE) -->
                        <div id="vendor-tab-menu-content" class="flex-1 flex flex-col min-h-0 hidden">
                            
                            <!-- Inline Add/Edit Form (Improved Light Theme Layout) -->
                            <div id="vendor-product-form-wrap" class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-2xl p-4 mb-4 shadow-sm hidden">
                                <div class="flex items-center justify-between pb-2 mb-3 border-b border-[#E2E8F0]">
                                    <h4 id="product-form-title" class="text-xs font-extrabold text-[#0F172A] uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-utensils text-[#A31D1D]"></i> Add Stall Item
                                    </h4>
                                    <button type="button" onclick="closeProductForm()" class="text-zinc-400 hover:text-zinc-600 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <form id="vendor-product-form" onsubmit="handleSaveProduct(event)" class="space-y-3">
                                    <input type="hidden" id="form-product-id">
                                    
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Item Name</label>
                                        <input type="text" id="form-product-name" required placeholder="e.g. Classic Smash Burger" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] focus:ring-1 focus:ring-[#A31D1D] text-[#0F172A] placeholder-[#94A3B8] rounded-xl px-3 py-2 text-xs font-semibold outline-none transition">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Price (Ksh)</label>
                                            <input type="number" id="form-product-price" required min="0" placeholder="e.g. 750" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] focus:ring-1 focus:ring-[#A31D1D] text-[#0F172A] placeholder-[#94A3B8] rounded-xl px-3 py-2 text-xs font-bold outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Category</label>
                                            <select id="form-product-category" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] text-[#0F172A] rounded-xl px-2.5 py-2 text-xs font-semibold outline-none">
                                                <option value="Mains">🍔 Burgers & Mains</option>
                                                <option value="Sides">🍟 Sides & Snacks</option>
                                                <option value="Drinks">🥤 Cold Drinks</option>
                                                <option value="Desserts">🍦 Desserts</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Description</label>
                                        <input type="text" id="form-product-desc" placeholder="e.g. Juicy double beef patty with cheddar cheese" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] focus:ring-1 focus:ring-[#A31D1D] text-[#0F172A] placeholder-[#94A3B8] rounded-xl px-3 py-2 text-xs font-medium outline-none transition">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Stock Status</label>
                                            <select id="form-product-stock" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] text-[#0F172A] rounded-xl px-2.5 py-2 text-xs font-semibold outline-none">
                                                <option value="in_stock">✅ In Stock</option>
                                                <option value="out_of_stock">❌ Sold Out</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Prep Time</label>
                                            <select id="form-product-preptime" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] focus:border-[#A31D1D] text-[#0F172A] rounded-xl px-2.5 py-2 text-xs font-semibold outline-none">
                                                <option value="5 mins">⚡ 5 mins (Fast)</option>
                                                <option value="10 mins">⏱️ 10 mins (Standard)</option>
                                                <option value="15 mins">🍳 15 mins (Gourmet)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-extrabold text-[#475569] uppercase mb-1">Item Photo</label>
                                        <input type="file" id="form-product-image" accept="image/*" class="w-full bg-[#FFFFFF] border border-[#CBD5E1] text-[#0F172A] rounded-xl px-2.5 py-1.5 text-[11px] outline-none">
                                    </div>

                                    <div class="flex gap-2 pt-2">
                                        <button type="submit" class="flex-1 py-2 bg-[#A31D1D] hover:bg-[#841313] text-white text-xs font-extrabold rounded-full shadow-md shadow-[#A31D1D]/20">
                                            <i class="fas fa-check mr-1"></i> Save Item
                                        </button>
                                        <button type="button" onclick="closeProductForm()" class="px-4 py-2 bg-[#E2E8F0] hover:bg-[#CBD5E1] text-[#475569] text-xs font-extrabold rounded-full">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div id="vendor-menu-items" class="flex-1 overflow-y-auto space-y-3 pr-1">
                                <!-- Products list with Edit / Delete controls -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        const laravelUser = @auth @json(Auth::user()) @else null @endauth;
        let currentUser = null;
        let vendors = [];
        let pollingInterval = null;
        let audioCtx = null;

        function playSound(type) {
            try {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain); gain.connect(audioCtx.destination);
                if (type === 'beep') {
                    osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    osc.start(); osc.stop(audioCtx.currentTime + 0.1);
                } else if (type === 'success') {
                    osc.frequency.setValueAtTime(800, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    osc.start(); osc.stop(audioCtx.currentTime + 0.25);
                }
            } catch(e) {}
        }

        // ── Auth helpers ──────────────────────────────────────────────────────
        function getToken() {
            try {
                const s = localStorage.getItem('justfeast_vendor_user');
                return s ? JSON.parse(s).__token : null;
            } catch(e) { return null; }
        }

        /**
         * Authenticated fetch — injects Authorization: Bearer <token>.
         * Clears session and reloads on 401.
         */
        async function authFetch(url, options = {}) {
            const token = getToken();
            options.headers = options.headers || {};
            if (token) options.headers['Authorization'] = `Bearer ${token}`;
            const res = await fetch(url, options);
            if (res.status === 401) {
                localStorage.removeItem('justfeast_vendor_user');
                window.location.reload();
            }
            return res;
        }

        window.addEventListener('DOMContentLoaded', () => {
            loadVendors();

            // Session check — restore from localStorage
            const saved = localStorage.getItem('justfeast_vendor_user');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (parsed && parsed.id) {
                        currentUser = parsed;
                        showDashboard();
                    }
                } catch(e) { localStorage.removeItem('justfeast_vendor_user'); }
            }

            pollingInterval = setInterval(syncQueue, 2000);
        });

        async function loadVendors() {
            try {
                const res = await fetch(`${API_BASE}/vendors`);
                if (res.ok) {
                    vendors = await res.json();
                    if (currentUser) {
                        renderStockControls();
                    }
                }
            } catch(e) {}
        }

        function showToast(msg, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full px-4';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-[#05A357]' : type === 'danger' ? 'bg-[#A31D1D]' : 'bg-[#0F172A]';
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';

            toast.className = `${bgColor} text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-bold pointer-events-auto transition-all transform translate-y-2 opacity-0`;
            toast.innerHTML = `<i class="fas ${icon} text-base"></i> <span class="flex-1">${msg}</span>`;

            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        let lastVendorOTP = '';
        function autoFillVendorOTP() {
            if (lastVendorOTP) {
                document.getElementById('vendor-otp-input').value = lastVendorOTP;
            }
        }

        async function sendVendorOTP() {
            const phone = document.getElementById('vendor-phone-input').value.trim();
            if (!phone) { showToast('Please enter your phone number.', 'danger'); return; }
            try {
                const res = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ phone })
                });
                const data = await res.json();
                if (res.ok) {
                    playSound('beep');
                    document.getElementById('vendor-otp-status-text').textContent = data.message;
                    if (data.otp) {
                        lastVendorOTP = data.otp;
                        document.getElementById('vendor-otp-input').value = data.otp;
                        const banner = document.getElementById('vendor-otp-banner');
                        if (banner) banner.classList.remove('hidden');
                        const display = document.getElementById('vendor-generated-otp-display');
                        if (display) display.textContent = data.otp;
                    }
                    document.getElementById('vendor-auth-step-phone').classList.add('hidden');
                    document.getElementById('vendor-auth-step-otp').classList.remove('hidden');
                    showToast('Verification OTP code sent to phone.', 'success');
                } else {
                    showToast(data.message || 'Error sending OTP', 'danger');
                }
            } catch(e) { showToast('Network error connecting to authentication server', 'danger'); }
        }

        async function verifyVendorOTP() {
            const phone = document.getElementById('vendor-phone-input').value.trim();
            const code = document.getElementById('vendor-otp-input').value.trim();
            if (!code || code.length < 6) { showToast('Please enter the 6-digit verification code.', 'danger'); return; }

            try {
                const res = await fetch(`${API_BASE}/auth/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ phone, code })
                });
                const data = await res.json();
                if (res.ok) {
                    playSound('success');
                    currentUser = data.user;
                    localStorage.setItem('justfeast_vendor_user', JSON.stringify({ ...currentUser, __token: data.token }));
                    showDashboard();
                    syncQueue();
                    showToast(`Welcome back, ${currentUser.name}!`, 'success');
                } else {
                    showToast(data.message || 'Verification failed', 'danger');
                }
            } catch(e) { showToast('Network error verifying code', 'danger'); }
        }

        function resetVendorAuthForm() {
            document.getElementById('vendor-auth-step-otp').classList.add('hidden');
            document.getElementById('vendor-auth-step-phone').classList.remove('hidden');
        }

        function showDashboard() {
            document.getElementById('vendor-auth').classList.add('hidden');
            document.getElementById('vendor-dashboard').classList.remove('hidden');
            document.getElementById('staff-name-pill').textContent = currentUser.name;

            const vendorDetails = getVendorDetails();
            if (vendorDetails) {
                document.getElementById('vendor-title').textContent = vendorDetails.business_name + ' Stall';
                if (vendorDetails.logo_url && (vendorDetails.logo_url.startsWith('/') || vendorDetails.logo_url.startsWith('http'))) {
                    document.getElementById('vendor-avatar').innerHTML = `<img src="${vendorDetails.logo_url}" class="w-5 h-5 rounded-full object-cover" alt="">`;
                } else {
                    document.getElementById('vendor-avatar').textContent = vendorDetails.logo_url || '🏪';
                }
            }
            renderStockControls();
            renderMenuManagement();
        }

        function logoutVendor() {
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

        function getVendorDetails() {
            if (!currentUser) return null;
            return vendors.find(v => v.user_id === currentUser.id);
        }

        let lastVendorQueueHash = '';
        async function syncQueue() {
            if (!currentUser) return;
            if (document.hidden) return;
            try {
                const qRes = await authFetch(`${API_BASE}/vendor/orders`);
                if (qRes.ok) {
                    const rawData = await qRes.json();
                    const orders = Array.isArray(rawData) ? rawData : (rawData.data || []);
                    const currentHash = JSON.stringify(orders);
                    if (currentHash !== lastVendorQueueHash) {
                        lastVendorQueueHash = currentHash;
                        renderQueue(orders);
                    }
                }
            } catch(e) {}
        }

        function renderQueue(ordersList) {
            const orders = Array.isArray(ordersList) ? ordersList : (ordersList.data || []);
            const container = document.getElementById('vendor-orders-container');
            const queueCount = document.getElementById('vendor-queue-count');
            const totalSales = document.getElementById('vendor-sales-amount');
            const completedCount = document.getElementById('vendor-completed-count');

            let salesSum = 0;
            orders.forEach(o => salesSum += parseFloat(o.total_amount || 0));
            if (totalSales) totalSales.textContent = `Ksh ${salesSum.toLocaleString()}`;

            const delivered = orders.filter(o => o.order_status === 'delivered');
            if (completedCount) completedCount.textContent = `${delivered.length} Delivered`;

            const pending = orders.filter(o => ['created', 'accepted', 'preparing', 'ready', 'runner_assigned', 'enroute', 'en_route'].includes(o.order_status));
            if (queueCount) queueCount.textContent = `${pending.length} Active`;

            if (pending.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-24 text-zinc-600 space-y-2">
                        <i class="fas fa-utensils text-3xl"></i>
                        <p class="text-xs">No active paid orders in the kitchen.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            pending.forEach(o => {
                const card = document.createElement('div');
                card.className = 'bg-zinc-900 border border-zinc-800 p-4 rounded-2xl space-y-3 relative overflow-hidden';
                
                let badgeClass = '';
                if (o.order_status === 'created' || o.order_status === 'accepted') badgeClass = 'bg-brand-rose/20 text-brand-rose';
                else if (o.order_status === 'preparing') badgeClass = 'bg-brand-orange/20 text-brand-orange animate-pulse';
                else badgeClass = 'bg-brand-emerald/20 text-brand-emerald';

                const loc = o.seat_location || {};
                const locText = (loc.type === 'gps' || loc.latitude)
                    ? `GPS Pin: ${loc.description || (parseFloat(loc.latitude).toFixed(4) + ', ' + parseFloat(loc.longitude).toFixed(4))}`
                    : `${loc.section || 'Seat'}, Row ${loc.row || ''}, Seat ${loc.seat || ''}`;

                const itemsList = (o.items || []).map(item => `<div>• ${item.quantity}x ${item.product ? item.product.name : 'Item'}</div>`).join('');

                card.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] text-zinc-500 font-bold uppercase">Order #${o.id}</span>
                            <h4 class="text-xs font-bold text-white">${o.user ? o.user.name : 'Customer'}</h4>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#05A357]/20 text-[#05A357] border border-[#05A357]/30">
                                <i class="fas fa-check-circle text-[8px] mr-0.5"></i> PAID
                            </span>
                            <span class="text-[8px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${badgeClass}">${o.order_status}</span>
                        </div>
                    </div>
                    <div class="text-[10px] text-zinc-400 space-y-1 py-1.5 border-y border-zinc-900">
                        ${itemsList}
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-zinc-500">
                        <span>Location: <strong class="text-white">${locText}</strong></span>
                        <span class="font-extrabold text-brand-rose">Ksh ${parseFloat(o.total_amount).toLocaleString()}</span>
                    </div>
                    <div class="pt-1">
                        ${(o.order_status === 'created' || o.order_status === 'accepted')
                            ? `<button onclick="updateStatus(${o.id}, 'preparing')" class="w-full py-2 bg-brand-rose hover:opacity-90 text-white rounded-lg text-xs font-bold transition">Start Preparing</button>`
                            : o.order_status === 'preparing'
                                ? `<button onclick="updateStatus(${o.id}, 'ready')" class="w-full py-2 bg-brand-emerald hover:bg-emerald-600 text-white rounded-lg text-xs font-bold transition">Mark Ready & Dispatch Runner</button>`
                                : `<div class="text-center text-[9px] text-zinc-500 font-bold"><i class="fas fa-truck mr-1"></i> Delivery Dispatch: Runner assigned & en route</div>`
                        }
                    </div>
                `;
                container.appendChild(card);
            });
        }

        async function updateStatus(orderId, status) {
            playSound('success');
            try {
                const res = await authFetch(`${API_BASE}/vendor/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                if (res.ok) {
                    syncQueue();
                }
            } catch(e) {}
        }

        function renderStockControls() {
            const container = document.getElementById('vendor-stock-items');
            if (!container) return;
            container.innerHTML = '';

            const vendor = getVendorDetails();
            if (!vendor) return;

            if (vendor.products.length === 0) {
                container.innerHTML = `<div class="text-center py-8 text-[#64748B] text-xs">No menu items listed yet</div>`;
                return;
            }

            // Update header item counts
            const menuCountEl = document.getElementById('vendor-menu-count');
            if (menuCountEl) menuCountEl.textContent = `${vendor.products.length} Items`;

            vendor.products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center bg-[#F8FAFC] p-3 rounded-2xl border border-[#E2E8F0] shadow-sm text-xs';
                const checked = p.stock_status === 'in_stock' ? 'checked' : '';
                const statusBadge = p.stock_status === 'in_stock' 
                    ? `<span class="text-[9px] bg-[#ECFDF5] text-[#047857] px-2 py-0.5 rounded-full font-bold">In Stock</span>`
                    : `<span class="text-[9px] bg-[#FEF2F2] text-[#991B1B] px-2 py-0.5 rounded-full font-bold">Sold Out</span>`;

                item.innerHTML = `
                    <div class="flex items-center gap-2.5">
                        <div class="font-bold text-[#0F172A]">${p.name}</div>
                        ${statusBadge}
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="${p.id}" onchange="toggleStock(${p.id})" class="sr-only peer" ${checked}>
                        <div class="w-8 h-4.5 bg-[#CBD5E1] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-[#05A357]"></div>
                    </label>
                `;
                container.appendChild(item);
            });
        }

        let activeVendorTab = 'stock';

        function switchVendorTab(tab) {
            playSound('beep');
            activeVendorTab = tab;
            const btnStock = document.getElementById('btn-tab-stock');
            const btnMenu = document.getElementById('btn-tab-menu');
            const contentStock = document.getElementById('vendor-tab-stock-content');
            const contentMenu = document.getElementById('vendor-tab-menu-content');

            if (tab === 'stock') {
                btnStock.className = 'px-3.5 py-1 text-[10px] font-extrabold rounded-full transition bg-[#A31D1D] text-white shadow-sm';
                btnMenu.className = 'px-3.5 py-1 text-[10px] font-extrabold rounded-full transition text-[#64748B]';
                contentStock.classList.remove('hidden');
                contentMenu.classList.add('hidden');
            } else {
                btnStock.className = 'px-3.5 py-1 text-[10px] font-extrabold rounded-full transition text-[#64748B]';
                btnMenu.className = 'px-3.5 py-1 text-[10px] font-extrabold rounded-full transition bg-[#A31D1D] text-white shadow-sm';
                contentStock.classList.add('hidden');
                contentMenu.classList.remove('hidden');
                renderMenuManagement();
            }
        }

        function renderMenuManagement() {
            const container = document.getElementById('vendor-menu-items');
            if (!container) return;
            container.innerHTML = '';

            const vendor = getVendorDetails();
            if (!vendor) return;

            if (vendor.products.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 text-[#64748B]">
                        <i class="fas fa-utensils text-3xl mb-2 block text-[#CBD5E1]"></i>
                        <span class="text-xs font-semibold">No items in menu. Click "Add Item" above!</span>
                    </div>
                `;
                return;
            }

            vendor.products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'bg-white border border-[#E2E8F0] rounded-2xl p-3.5 flex flex-col gap-2 shadow-sm hover:border-[#A31D1D]/30 transition';
                
                const escName = p.name.replace(/'/g, "\\'");
                const escDesc = (p.description || '').replace(/'/g, "\\'");

                let visual = '';
                if (p.image_url && p.image_url.startsWith('/')) {
                    visual = `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-12 h-12 object-cover rounded-xl border border-[#E2E8F0] shrink-0" alt="${p.name}">`;
                } else {
                    const gradient = p.image_url || 'bg-gradient-to-br from-[#A31D1D] to-[#FFC244]';
                    visual = `<div class="w-12 h-12 rounded-xl ${gradient} flex items-center justify-center text-white text-xs font-extrabold uppercase shrink-0 shadow-sm">${p.name.substring(0, 2)}</div>`;
                }

                const stockBadge = p.stock_status === 'in_stock' 
                    ? `<span class="bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0] px-2 py-0.5 rounded-full text-[9px] font-extrabold">In Stock</span>`
                    : `<span class="bg-[#FEF2F2] text-[#991B1B] border border-[#FCA5A5] px-2 py-0.5 rounded-full text-[9px] font-extrabold">Sold Out</span>`;

                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        ${visual}
                        <div class="flex-1 min-w-0">
                            <div class="font-extrabold text-[#0F172A] text-xs truncate">${p.name}</div>
                            <div class="text-[10px] text-[#64748B] truncate mt-0.5">${p.description || 'No description provided.'}</div>
                            <div class="mt-1 flex items-center gap-1.5">${stockBadge}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-black text-[#A31D1D] text-sm block">Ksh ${Number(p.price).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2 border-t border-[#F1F5F9]">
                        <button type="button" onclick="openEditProductForm(${p.id}, '${escName}', ${p.price}, '${escDesc}', '${p.category || 'Mains'}', '${p.stock_status || 'in_stock'}')" class="px-3.5 py-1 bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#0F172A] text-[10px] font-extrabold rounded-full transition flex items-center gap-1">
                            <i class="fas fa-pen text-[8px] text-[#A31D1D]"></i> Edit
                        </button>
                        <button type="button" onclick="handleDeleteProduct(${p.id})" class="px-3.5 py-1 bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#991B1B] text-[10px] font-extrabold rounded-full transition flex items-center gap-1">
                            <i class="fas fa-trash text-[8px]"></i> Delete
                        </button>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        function toggleAddProductForm() {
            const wrap = document.getElementById('vendor-product-form-wrap');
            if (wrap.classList.contains('hidden')) {
                switchVendorTab('menu');
                openAddProductForm();
            } else {
                closeProductForm();
            }
        }

        function openAddProductForm() {
            playSound('beep');
            document.getElementById('product-form-title').innerHTML = '<i class="fas fa-plus-circle text-[#A31D1D] mr-1.5"></i> Add Stall Item';
            document.getElementById('form-product-id').value = '';
            document.getElementById('form-product-name').value = '';
            document.getElementById('form-product-price').value = '';
            document.getElementById('form-product-desc').value = '';
            document.getElementById('form-product-category').value = 'Mains';
            document.getElementById('form-product-stock').value = 'in_stock';
            document.getElementById('form-product-preptime').value = '10 mins';
            document.getElementById('form-product-image').value = '';
            document.getElementById('vendor-product-form-wrap').classList.remove('hidden');
        }

        function openEditProductForm(id, name, price, description, category, stockStatus) {
            playSound('beep');
            document.getElementById('product-form-title').innerHTML = '<i class="fas fa-edit text-[#A31D1D] mr-1.5"></i> Edit Stall Item';
            document.getElementById('form-product-id').value = id;
            document.getElementById('form-product-name').value = name;
            document.getElementById('form-product-price').value = price;
            document.getElementById('form-product-desc').value = description || '';
            if (category) document.getElementById('form-product-category').value = category;
            if (stockStatus) document.getElementById('form-product-stock').value = stockStatus;
            document.getElementById('form-product-image').value = '';
            document.getElementById('vendor-product-form-wrap').classList.remove('hidden');
        }

        function closeProductForm() {
            playSound('beep');
            document.getElementById('vendor-product-form-wrap').classList.add('hidden');
        }

        async function handleSaveProduct(e) {
            e.preventDefault();
            playSound('success');

            const id = document.getElementById('form-product-id').value;
            const name = document.getElementById('form-product-name').value;
            const price = document.getElementById('form-product-price').value;
            const description = document.getElementById('form-product-desc').value;
            const category = document.getElementById('form-product-category').value;
            const stockStatus = document.getElementById('form-product-stock').value;

            const vendor = getVendorDetails();
            if (!vendor) return;

            const formData = new FormData();
            formData.append('vendor_id', vendor.id);
            formData.append('name', name);
            formData.append('price', price);
            formData.append('description', description);
            formData.append('category', category);
            formData.append('stock_status', stockStatus);

            const imageInput = document.getElementById('form-product-image');
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            let url = `${API_BASE}/vendor/products`;
            if (id) {
                url = `${API_BASE}/vendor/products/${id}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await authFetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (res.ok) {
                    closeProductForm();
                    showToast(id ? 'Menu item updated successfully!' : 'Menu item added successfully!', 'success');
                    const response = await fetch(`${API_BASE}/vendors`);
                    if (response.ok) {
                        vendors = await response.json();
                    }
                    renderStockControls();
                    renderMenuManagement();
                } else {
                    const data = await res.json();
                    showToast(data.message || 'Failed to save menu item', 'danger');
                }
            } catch(e) {}
        }

        async function toggleStock(productId) {
            try {
                const res = await authFetch(`${API_BASE}/vendor/products/${productId}/stock`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (res.ok) {
                    const response = await fetch(`${API_BASE}/vendors`);
                    if (response.ok) {
                        vendors = await response.json();
                    }
                    renderStockControls();
                    renderMenuManagement();
                }
            } catch(e) {}
        }

        async function handleDeleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this menu item?')) return;
            playSound('beep');

            try {
                const res = await authFetch(`${API_BASE}/vendor/products/${productId}`, {
                    method: 'DELETE'
                });

                if (res.ok) {
                    const response = await fetch(`${API_BASE}/vendors`);
                    if (response.ok) {
                        vendors = await response.json();
                        renderStockControls();
                        renderMenuManagement();
                    }
                }
            } catch(e) {}
        }
    </script>
</body>
</html>
