<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast Runner — Stadium Delivery Dispatch</title>
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

    <!-- App Container -->
    <div class="w-full max-w-[1400px] mx-auto min-h-screen flex flex-col relative z-10 px-4 md:px-6 pt-6">
        
        <!-- Auth Selector Screen (if not logged in) -->
        <div id="runner-auth" class="glass-card rounded-3xl p-8 text-center space-y-6 max-w-md mx-auto w-full my-auto shadow-md">
            <div class="w-16 h-16 bg-[#FFC244] rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-[#FFC244]/15 text-3xl border border-[#E0A325] flex items-center justify-center">
                <span>🏃</span>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-[#2D3748] font-sans">Runner Dispatch Portal</h2>
                <p class="text-xs text-zinc-500">Select your active runner profile to log in and start receiving delivery dispatch notifications.</p>
            </div>

            <div class="space-y-3 pt-4 text-left">
                <button onclick="loginAsRunner('runner@justfeast.com')" class="w-full p-4 rounded-2xl bg-white border border-[#E2E8F0] hover:border-[#FFC244] hover:bg-[#FFFDF9] transition flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🏃‍♂️</span>
                        <div>
                            <h4 class="text-xs font-bold text-[#2D3748]">Mike Runner</h4>
                            <p class="text-[9px] text-zinc-500 uppercase font-semibold">Stall Staging Area A</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-400 text-xs"></i>
                </button>

                <button onclick="loginAsRunner('runner2@justfeast.com')" class="w-full p-4 rounded-2xl bg-white border border-[#E2E8F0] hover:border-[#FFC244] hover:bg-[#FFFDF9] transition flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🏃‍♀️</span>
                        <div>
                            <h4 class="text-xs font-bold text-[#2D3748]">Jane Runner</h4>
                            <p class="text-[9px] text-zinc-500 uppercase font-semibold">Stall Staging Area B</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-400 text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Runner Dashboard Screen -->
        <div id="runner-dashboard" class="hidden flex-1 flex flex-col justify-between">
            <!-- Header -->
            <header class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🏃</span>
                    <div>
                        <h4 class="text-xs font-bold text-[#2D3748]" id="runner-name-label">Mike Runner</h4>
                        <p class="text-[9px] text-zinc-500 uppercase font-bold" id="live-event-banner">Rhema Feast 2026</p>
                    </div>
                </div>
                <button onclick="logoutRunner()" class="text-xs text-zinc-500 hover:text-zinc-700 font-bold"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </header>

            <!-- Main Deliveries Workspace -->
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start w-full pb-12">
                <!-- Left Column: Active task card & Verification Block (e.g. 2 columns) -->
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider"><i class="fas fa-route text-brand-rose mr-1.5"></i> Assigned Delivery</h3>

                    <div id="runner-active-card-container">
                        <!-- Active task status card -->
                        <div class="text-center py-20 text-zinc-600 bg-zinc-900/30 border border-zinc-900 rounded-2xl p-6 space-y-3">
                            <i class="fas fa-radar text-3xl text-zinc-700"></i>
                            <h4 class="text-xs font-bold text-zinc-500">Awaiting Kitchen Orders</h4>
                            <p class="text-[10px] text-zinc-600">You will automatically receive a task notification when a vendor kitchen marks an order as 'Ready'.</p>
                        </div>
                    </div>

                    <!-- Verification Block -->
                    <div id="runner-verification-box" class="hidden bg-zinc-900/40 border border-zinc-800 p-6 rounded-2xl space-y-4">
                        <h4 class="text-xs font-bold text-white text-center">Handover Verification</h4>
                        <div class="max-w-xs mx-auto">
                            <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-wider mb-1 text-center">Enter Customer Verification PIN</label>
                            <input type="text" id="runner-pin-input" placeholder="Enter 4-Digit PIN" class="w-full text-center py-2 rounded-lg glass-input font-bold tracking-widest text-lg text-white" maxlength="4">
                        </div>
                        <button onclick="verifyRunnerDelivery()" class="w-full py-2.5 bg-brand-emerald hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-brand-emerald/10">
                            Verify & Complete Delivery
                        </button>
                    </div>
                </div>

                <!-- Right Column: Navigation Guide (e.g. 1 column) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Seat Navigation Guide -->
                    <div id="runner-map-guide" class="hidden space-y-2 bg-zinc-900/40 p-4 rounded-2xl border border-zinc-900/60">
                        <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Stadium Seat Finder Navigation</h4>

                        <div class="h-32 bg-zinc-950 rounded-lg border border-zinc-800/80 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute top-1 bg-zinc-900 border border-zinc-800 px-2 py-0.5 rounded text-[8px] text-zinc-400 font-bold">UHURU GARDENS KITCHEN STALLS</div>
                            <div class="absolute bottom-2 bg-brand-rose px-2 py-1 rounded text-[8px] text-white font-bold" id="runner-target-section-tag">VIP A - ROW 12 - SEAT 18</div>

                            <svg viewBox="0 0 100 40" class="w-24 h-auto pointer-events-none">
                                <path d="M 10,10 Q 50,30 90,30" fill="none" stroke="#f43f5e" stroke-dasharray="3" stroke-width="2" class="animate-pulse" />
                                <circle cx="10" cy="10" r="3" fill="#f97316" />
                                <circle cx="90" cy="30" r="3" fill="#10b981" />
                            </svg>
                        </div>
                        <p class="text-[9px] text-zinc-500"><i class="fas fa-info-circle mr-1"></i> Proceed to vendor stall, pickup order, and navigate via arena tunnels to the seat coordinates.</p>
                    </div>

                    <!-- General Info Card -->
                    <div class="bg-zinc-900/20 border border-zinc-850 p-4 rounded-2xl text-xs text-zinc-500 space-y-2">
                        <h5 class="font-bold text-zinc-400 uppercase tracking-wider">Runner Protocol</h5>
                        <ul class="list-disc pl-4 space-y-1">
                            <li>Check items carefully before leaving the kitchen.</li>
                            <li>Navigate safely through concert crowds.</li>
                            <li>Input the customer PIN correctly to close tasks.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        const laravelUser = @auth @json(Auth::user()) @else null @endauth;
        let currentUser = null;
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
            // Session check
            const saved = localStorage.getItem('justfeast_runner_user');
            if (laravelUser) {
                currentUser = laravelUser;
                localStorage.setItem('justfeast_runner_user', JSON.stringify(currentUser));
                showDashboard();
            } else if (saved) {
                currentUser = JSON.parse(saved);
                showDashboard();
            }

            pollingInterval = setInterval(syncDeliveries, 2000);
        });

        async function loginAsRunner(email) {
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
                    localStorage.setItem('justfeast_runner_user', JSON.stringify(currentUser));
                    showDashboard();
                    syncDeliveries();
                }
            } catch(e) {}
        }

        function showDashboard() {
            document.getElementById('runner-auth').classList.add('hidden');
            document.getElementById('runner-dashboard').classList.remove('hidden');
            document.getElementById('runner-name-label').textContent = currentUser.name;
        }

        function logoutRunner() {
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

        async function syncDeliveries() {
            if (!currentUser) return;
            try {
                const res = await fetch(`${API_BASE}/runner/deliveries?user_id=${currentUser.id}`);
                if (res.ok) {
                    const deliveries = await res.json();
                    renderDeliveries(deliveries);
                }
            } catch(e) {}
        }

        function renderDeliveries(deliveries) {
            const container = document.getElementById('runner-active-card-container');
            const verifyBox = document.getElementById('runner-verification-box');
            const mapGuide = document.getElementById('runner-map-guide');

            if (deliveries.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-20 text-zinc-600 bg-zinc-900/30 border border-zinc-900 rounded-2xl p-6 space-y-3">
                        <i class="fas fa-radar text-3xl text-zinc-700"></i>
                        <h4 class="text-xs font-bold text-zinc-500 font-sans">Awaiting Kitchen Orders</h4>
                        <p class="text-[10px] text-zinc-600">You will automatically receive a task notification when a vendor kitchen marks an order as 'Ready'.</p>
                    </div>
                `;
                verifyBox.classList.add('hidden');
                mapGuide.classList.add('hidden');
                return;
            }

            const activeDel = deliveries[0];
            container.innerHTML = '';
            verifyBox.classList.remove('hidden');
            mapGuide.classList.remove('hidden');

            document.getElementById('runner-target-section-tag').textContent = `${activeDel.order.seat_location.section} - ROW ${activeDel.order.seat_location.row} - SEAT ${activeDel.order.seat_location.seat}`;

            const card = document.createElement('div');
            card.className = 'bg-zinc-900 border border-zinc-800 p-4 rounded-2xl space-y-3 shadow-lg';

            let statusBtn = '';
            if (activeDel.status === 'pending') {
                statusBtn = `<button onclick="updateDeliveryStatus(${activeDel.id}, 'picked_up')" class="w-full py-2 bg-brand-rose hover:opacity-95 text-white rounded-lg text-xs font-bold transition">Confirm Pickup from Kitchen</button>`;
            } else if (activeDel.status === 'picked_up') {
                statusBtn = `<button onclick="updateDeliveryStatus(${activeDel.id}, 'en_route')" class="w-full py-2 bg-brand-orange hover:opacity-95 text-white rounded-lg text-xs font-bold transition">Start Navigation En-Route</button>`;
            } else {
                statusBtn = `<span class="text-[10px] text-brand-emerald text-center block font-bold"><i class="fas fa-truck mr-1"></i> Navigation Active - Handover PIN</span>`;
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
                    <p class="font-extrabold text-brand-orange text-xs">${activeDel.order.seat_location.section}, Row ${activeDel.order.seat_location.row}, Seat ${activeDel.order.seat_location.seat}</p>
                </div>

                <div class="pt-1.5">
                    ${statusBtn}
                </div>
            `;
            container.appendChild(card);
        }

        async function updateDeliveryStatus(delId, status) {
            playSound('success');
            try {
                const res = await fetch(`${API_BASE}/runner/deliveries/${delId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                if (res.ok) {
                    syncDeliveries();
                }
            } catch(e) {}
        }

        async function verifyRunnerDelivery() {
            try {
                const res = await fetch(`${API_BASE}/runner/deliveries?user_id=${currentUser.id}`);
                if (res.ok) {
                    const deliveries = await res.json();
                    if (deliveries.length > 0) {
                        const delId = deliveries[0].id;
                        const pin = document.getElementById('runner-pin-input').value;
                        const verifyRes = await fetch(`${API_BASE}/runner/deliveries/${delId}/verify`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ pin })
                        });
                        if (verifyRes.ok) {
                            playSound('success');
                            alert("🎉 Delivery verified successfully! Order completed.");
                            document.getElementById('runner-pin-input').value = '';
                            syncDeliveries();
                        } else {
                            const err = await verifyRes.json();
                            alert(err.message);
                        }
                    }
                }
            } catch(e) {}
        }
    </script>
</body>
</html>
