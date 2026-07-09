<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast Vendor — Kitchen Preparation Portal</title>
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
                            emerald: '#00A082',
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
            background-image: linear-gradient(to right, #FFC244, #00A082) !important;
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
            color: #00A082 !important;
        }
        .bg-brand-emerald, .bg-gradient-to-r.from-brand-emerald {
            background-color: #00A082 !important;
            background-image: none !important;
            color: #FFFFFF !important;
        }
        .bg-brand-emerald\/20 {
            background-color: rgba(0, 160, 130, 0.1) !important;
        }
        
        /* Rose/Orange text elements should be changed to Glovo Green or Charcoal for readability */
        .text-brand-rose, .text-brand-orange {
            color: #00A082 !important;
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
            stroke: #00A082 !important;
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
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-[#00A082]/10 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- Main Workspace Wrapper -->
    <div class="w-full max-w-[1700px] mx-auto px-4 py-8 relative z-10">

        <!-- Auth Picker Screen (if not logged in) -->
        <div id="vendor-auth" class="glass-card rounded-3xl p-8 max-w-md mx-auto text-center space-y-6 shadow-md">
            <div class="w-16 h-16 bg-[#FFC244] rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-[#FFC244]/15 text-3xl border border-[#E0A325] flex items-center justify-center">
                <span>🏪</span>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-[#2D3748] font-sans">Kitchen Staff Portal</h2>
                <p class="text-xs text-zinc-500">Select your food stall vendor account to log in and start receiving orders from attendees.</p>
            </div>

            <div class="space-y-3 pt-4 text-left">
                <button onclick="loginAsVendor('vendor@justfeast.com')" class="w-full p-4 rounded-2xl bg-white border border-[#E2E8F0] hover:border-[#FFC244] hover:bg-[#FFFDF9] transition flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🍔</span>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-[#2D3748]">Burger World</h4>
                            <p class="text-[9px] text-zinc-500 uppercase tracking-wider font-semibold">Gourmet Smash Burgers</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-400 text-xs"></i>
                </button>

                <button onclick="loginAsVendor('taco@justfeast.com')" class="w-full p-4 rounded-2xl bg-white border border-[#E2E8F0] hover:border-[#FFC244] hover:bg-[#FFFDF9] transition flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🌮</span>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-[#2D3748]">Taco Fiesta</h4>
                            <p class="text-[9px] text-zinc-500 uppercase tracking-wider font-semibold">Mexican Tacos & Churros</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-400 text-xs"></i>
                </button>

                <button onclick="loginAsVendor('choma@justfeast.com')" class="w-full p-4 rounded-2xl bg-white border border-[#E2E8F0] hover:border-[#FFC244] hover:bg-[#FFFDF9] transition flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🥩</span>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-[#2D3748]">Choma Zone</h4>
                            <p class="text-[9px] text-zinc-500 uppercase tracking-wider font-semibold">Nyama Choma & cold Tusker</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-400 text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Vendor Dashboard Portal -->
        <div id="vendor-dashboard" class="hidden space-y-6">
            <!-- Header -->
            <header class="flex flex-col md:flex-row items-center justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                <div class="flex items-center gap-3">
                    <span id="vendor-avatar" class="text-3xl">🏪</span>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-[#2D3748] flex items-center gap-2" id="vendor-title">
                            Vendor Portal
                        </h1>
                        <p class="text-xs text-zinc-500" id="live-event-banner">Connected to concert event stalls</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs bg-zinc-900 border border-zinc-800 px-3 py-1.5 rounded-xl text-zinc-400 font-semibold" id="staff-name-pill">Loading...</span>
                    <button onclick="logoutVendor()" class="text-xs text-zinc-500 hover:text-zinc-400 font-bold"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </div>
            </header>

            <!-- Dashboard Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column (Queue & Stats) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Metrics Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass-card p-5 rounded-2xl">
                            <span class="text-[9px] uppercase tracking-wider text-zinc-500 block font-bold mb-1">Active Kitchen Queue</span>
                            <span class="text-2xl font-black text-brand-rose" id="vendor-queue-count">0 Orders</span>
                        </div>
                        <div class="glass-card p-5 rounded-2xl">
                            <span class="text-[9px] uppercase tracking-wider text-zinc-500 block font-bold mb-1">Total Sales Revenue</span>
                            <span class="text-2xl font-black text-brand-emerald" id="vendor-sales-amount">Ksh 0.00</span>
                        </div>
                    </div>

                    <!-- Kitchen Queue -->
                    <div class="glass-card p-6 rounded-2xl flex flex-col h-[650px]">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-4"><i class="fas fa-fire text-brand-orange mr-1.5"></i> Live Kitchen Prep Queue</h3>
                        
                        <div id="vendor-orders-container" class="flex-1 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4 pr-1">
                            <!-- Order cards list -->
                            <div class="col-span-full text-center py-24 text-zinc-600 space-y-2">
                                <i class="fas fa-utensils text-3xl"></i>
                                <p class="text-xs">No active paid orders in the kitchen.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Menu & Stock Controls) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="glass-card p-6 rounded-2xl flex flex-col h-[650px]">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-800 mb-4">
                            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider"><i class="fas fa-list text-[#00A082] mr-1.5"></i> Stall Menu</h3>
                            <div class="flex gap-1 bg-zinc-950 p-1 rounded-full border border-zinc-850">
                                <button type="button" onclick="switchVendorTab('stock')" id="btn-tab-stock" class="px-3 py-1 text-[9px] font-black rounded-full transition bg-[#FFC244] text-[#2D3748]">Stock</button>
                                <button type="button" onclick="switchVendorTab('menu')" id="btn-tab-menu" class="px-3 py-1 text-[9px] font-black rounded-full transition text-zinc-400">Edit Menu</button>
                            </div>
                        </div>

                        <!-- TAB: STOCK TOGGLE -->
                        <div id="vendor-tab-stock-content" class="flex-1 flex flex-col min-h-0">
                            <p class="text-[10px] text-zinc-500 mb-4">Toggle stock status to instantly mark items as "Sold Out" or "Available" on customer apps.</p>
                            <div id="vendor-stock-items" class="flex-1 overflow-y-auto space-y-3 pr-1">
                                <!-- Toggle switches checklist -->
                            </div>
                        </div>

                        <!-- TAB: EDIT MENU (ADD/EDIT/DELETE) -->
                        <div id="vendor-tab-menu-content" class="flex-1 flex flex-col min-h-0 hidden">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] text-zinc-500">Edit prices, details, or add items.</span>
                                <button type="button" onclick="openAddProductForm()" class="px-3 py-1 bg-[#00A082] text-white text-[9px] font-black rounded-full shadow-sm"><i class="fas fa-plus mr-1"></i> Add Item</button>
                            </div>
                            
                            <!-- Inline Add/Edit Form -->
                            <div id="vendor-product-form-wrap" class="bg-zinc-950 border border-zinc-850 rounded-2xl p-4 mb-4 hidden">
                                <h4 id="product-form-title" class="text-[10px] font-black text-white uppercase tracking-wider mb-3">Add Stall Item</h4>
                                <form id="vendor-product-form" onsubmit="handleSaveProduct(event)" class="space-y-3">
                                    <input type="hidden" id="form-product-id">
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase mb-1">Item Name</label>
                                        <input type="text" id="form-product-name" required placeholder="e.g. Classic Burger" class="w-full bg-[#1C1C24] border border-zinc-850 text-white rounded-xl px-2.5 py-1.5 text-[10px] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase mb-1">Price (Ksh)</label>
                                        <input type="number" id="form-product-price" required min="0" placeholder="e.g. 750" class="w-full bg-[#1C1C24] border border-zinc-850 text-white rounded-xl px-2.5 py-1.5 text-[10px] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase mb-1">Description</label>
                                        <input type="text" id="form-product-desc" placeholder="e.g. Juicy beef patty with cheddar cheese" class="w-full bg-[#1C1C24] border border-zinc-850 text-white rounded-xl px-2.5 py-1.5 text-[10px] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase mb-1">Item Image</label>
                                        <input type="file" id="form-product-image" accept="image/*" class="w-full bg-[#1C1C24] border border-zinc-850 text-white rounded-xl px-2.5 py-1.5 text-[10px] outline-none">
                                    </div>
                                    <div class="flex gap-2 pt-1.5">
                                        <button type="submit" class="flex-1 py-1.5 bg-[#FFC244] text-[#2D3748] text-[9px] font-bold rounded-full">Save Item</button>
                                        <button type="button" onclick="closeProductForm()" class="flex-1 py-1.5 bg-zinc-900 border border-zinc-800 text-zinc-400 text-[9px] font-bold rounded-full">Cancel</button>
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

        window.addEventListener('DOMContentLoaded', () => {
            loadVendors();

            // Session check
            const saved = localStorage.getItem('justfeast_vendor_user');
            if (laravelUser) {
                currentUser = laravelUser;
                localStorage.setItem('justfeast_vendor_user', JSON.stringify(currentUser));
                showDashboard();
            } else if (saved) {
                currentUser = JSON.parse(saved);
                showDashboard();
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

        async function loginAsVendor(email) {
            try {
                const res = await fetch(`${API_BASE}/auth/login-as`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                if (res.ok) {
                    playSound('success');
                    currentUser = data.user;
                    localStorage.setItem('justfeast_vendor_user', JSON.stringify(currentUser));
                    showDashboard();
                    syncQueue();
                }
            } catch(e) {}
        }

        function showDashboard() {
            document.getElementById('vendor-auth').classList.add('hidden');
            document.getElementById('vendor-dashboard').classList.remove('hidden');
            document.getElementById('staff-name-pill').textContent = currentUser.name;

            const vendorDetails = getVendorDetails();
            if (vendorDetails) {
                document.getElementById('vendor-title').textContent = vendorDetails.business_name + ' Stall';
                document.getElementById('vendor-avatar').textContent = vendorDetails.logo_url;
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

        async function syncQueue() {
            if (!currentUser) return;
            try {
                const qRes = await fetch(`${API_BASE}/orders/vendor?user_id=${currentUser.id}`);
                if (qRes.ok) {
                    const queue = await qRes.json();
                    renderQueue(queue);
                }
            } catch(e) {}
        }

        function renderQueue(orders) {
            const container = document.getElementById('vendor-orders-container');
            const queueCount = document.getElementById('vendor-queue-count');
            const totalSales = document.getElementById('vendor-sales-amount');

            let salesSum = 0;
            orders.forEach(o => salesSum += parseFloat(o.total_amount));
            totalSales.textContent = `Ksh ${salesSum.toLocaleString()}`;

            const pending = orders.filter(o => ['accepted', 'preparing', 'ready', 'runner_assigned', 'en_route'].includes(o.order_status));
            queueCount.textContent = `${pending.length} Active`;

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
                if (o.order_status === 'accepted') badgeClass = 'bg-brand-rose/20 text-brand-rose';
                else if (o.order_status === 'preparing') badgeClass = 'bg-brand-orange/20 text-brand-orange animate-pulse';
                else badgeClass = 'bg-brand-emerald/20 text-brand-emerald';

                card.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] text-zinc-500 font-bold uppercase">Order #${o.id}</span>
                            <h4 class="text-xs font-bold text-white">${o.user.name}</h4>
                        </div>
                        <span class="text-[8px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${badgeClass}">${o.order_status}</span>
                    </div>
                    <div class="text-[10px] text-zinc-400 space-y-1 py-1.5 border-y border-zinc-900">
                        ${o.items.map(item => `<div>• ${item.quantity}x ${item.product.name}</div>`).join('')}
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-zinc-500">
                        <span>Seat Coordinates: <strong class="text-white">${o.seat_location.section}, Row ${o.seat_location.row}, Seat ${o.seat_location.seat}</strong></span>
                        <span class="font-extrabold text-brand-rose">Ksh ${parseFloat(o.total_amount).toLocaleString()}</span>
                    </div>
                    <div class="pt-1">
                        ${o.order_status === 'accepted'
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
                const res = await fetch(`${API_BASE}/orders/${orderId}/status`, {
                    method: 'POST',
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

            vendor.products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center bg-zinc-900 px-3 py-2.5 rounded-xl border border-zinc-800 text-xs';
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
            playSound('beep');
            try {
                const res = await fetch(`${API_BASE}/products/${productId}/toggle-stock`, { method: 'POST' });
                if (res.ok) {
                    // Refresh vendor list context
                    const response = await fetch(`${API_BASE}/vendors`);
                    if (response.ok) {
                        vendors = await response.json();
                        renderStockControls();
                        renderMenuManagement();
                    }
                }
            } catch(e) {}
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
                btnStock.className = 'px-3 py-1 text-[9px] font-black rounded-full transition bg-[#FFC244] text-[#2D3748]';
                btnMenu.className = 'px-3 py-1 text-[9px] font-black rounded-full transition text-zinc-400';
                contentStock.classList.remove('hidden');
                contentMenu.classList.add('hidden');
            } else {
                btnStock.className = 'px-3 py-1 text-[9px] font-black rounded-full transition text-zinc-400';
                btnMenu.className = 'px-3 py-1 text-[9px] font-black rounded-full transition bg-[#FFC244] text-[#2D3748]';
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
                    <div class="text-center py-12 text-zinc-650">
                        <i class="fas fa-utensils text-2xl mb-1 block"></i>
                        <span class="text-[10px]">No items in menu. Add one!</span>
                    </div>
                `;
                return;
            }

            vendor.products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'bg-zinc-900 border border-zinc-800 rounded-xl p-3 flex flex-col gap-1.5 text-xs';
                
                const escName = p.name.replace(/'/g, "\\'");
                const escDesc = (p.description || '').replace(/'/g, "\\'");

                let visual = '';
                if (p.image_url && p.image_url.startsWith('/')) {
                    visual = `<img src="${API_BASE.replace('/api', '') + p.image_url}" class="w-10 h-10 object-cover rounded-lg border border-zinc-800 shrink-0" alt="${p.name}">`;
                } else {
                    const gradient = p.image_url || 'bg-gradient-to-br from-amber-400 to-red-500';
                    visual = `<div class="w-10 h-10 rounded-lg ${gradient} flex items-center justify-center text-white text-[10px] font-black uppercase shrink-0">${p.name.substring(0, 2)}</div>`;
                }

                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        ${visual}
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-zinc-200 truncate">${p.name}</div>
                            <div class="text-[10px] text-zinc-500 truncate">${p.description || 'No description.'}</div>
                        </div>
                        <span class="font-black text-[#FFC244] shrink-0">Ksh ${parseFloat(p.price).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-end gap-2 pt-1.5 border-t border-zinc-850">
                        <button type="button" onclick="openEditProductForm(${p.id}, '${escName}', ${p.price}, '${escDesc}')" class="px-3 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[8px] font-bold rounded-full">Edit</button>
                        <button type="button" onclick="handleDeleteProduct(${p.id})" class="px-3 py-0.5 bg-red-950/40 hover:bg-red-900/60 text-red-400 text-[8px] font-bold rounded-full">Delete</button>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        function openAddProductForm() {
            playSound('beep');
            document.getElementById('product-form-title').textContent = 'Add Stall Item';
            document.getElementById('form-product-id').value = '';
            document.getElementById('form-product-name').value = '';
            document.getElementById('form-product-price').value = '';
            document.getElementById('form-product-desc').value = '';
            document.getElementById('form-product-image').value = '';
            document.getElementById('vendor-product-form-wrap').classList.remove('hidden');
        }

        function openEditProductForm(id, name, price, description) {
            playSound('beep');
            document.getElementById('product-form-title').textContent = 'Edit Stall Item';
            document.getElementById('form-product-id').value = id;
            document.getElementById('form-product-name').value = name;
            document.getElementById('form-product-price').value = price;
            document.getElementById('form-product-desc').value = description;
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

            const vendor = getVendorDetails();
            if (!vendor) return;

            const formData = new FormData();
            formData.append('vendor_id', vendor.id);
            formData.append('name', name);
            formData.append('price', price);
            formData.append('description', description);
            formData.append('stock_status', 'in_stock');

            const imageInput = document.getElementById('form-product-image');
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            let url = `${API_BASE}/products`;
            if (id) {
                url = `${API_BASE}/products/${id}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                if (res.ok) {
                    closeProductForm();
                    // Reload vendors context
                    const response = await fetch(`${API_BASE}/vendors`);
                    if (response.ok) {
                        vendors = await response.json();
                        renderStockControls();
                        renderMenuManagement();
                    }
                }
            } catch(e) {}
        }

        async function handleDeleteProduct(id) {
            if (!confirm('Are you sure you want to delete this menu item?')) return;
            playSound('beep');

            try {
                const res = await fetch(`${API_BASE}/products/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    // Reload vendors context
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
