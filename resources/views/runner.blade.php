<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast Runner — Stadium Delivery Dispatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        /* --- Glovo Premium Look & Feel --- */
        body {
            background-color: #FFFDF9 !important;
            color: #2D3748 !important;
        }
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
        button {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 9999px !important;
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

    <!-- Background glowing accents -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-[#FFC244]/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-[#A31D1D]/10 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- App Container -->
    <div class="w-full max-w-[1500px] mx-auto min-h-screen flex flex-col relative z-10 px-4 md:px-6 pt-4 sm:pt-6">
        
        <!-- Auth Screen (if not logged in) -->
        <div id="runner-auth" class="glass-card rounded-3xl p-8 text-center space-y-6 max-w-md mx-auto w-full my-auto shadow-md">
            <div class="w-16 h-16 bg-[#FFC244] rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-[#FFC244]/15 text-3xl border border-[#E0A325]">
                <span>🏃</span>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-[#2D3748] font-sans">Runner Dispatch Portal</h2>
                <p class="text-xs text-zinc-500">Enter your phone number to log in and access live concert delivery dispatches.</p>
            </div>

            <!-- Phone Step -->
            <div id="runner-auth-step-phone" class="space-y-3 pt-2">
                <input type="text" id="runner-phone-input" placeholder="+254712345678" class="w-full p-3.5 rounded-2xl bg-[#F7F9FA] border border-[#E2E8F0] text-sm text-[#2D3748] focus:border-[#FFC244] focus:outline-none font-bold text-center">
                <button onclick="sendRunnerOTP()" class="w-full p-3.5 rounded-2xl bg-[#A31D1D] hover:bg-[#841313] text-white font-extrabold text-xs transition shadow-md">
                    Send Verification Code
                </button>
            </div>

            <!-- OTP Step -->
            <div id="runner-auth-step-otp" class="hidden space-y-3 pt-2">
                <p class="text-[11px] text-zinc-500 font-semibold" id="runner-otp-status-text">Code sent to phone</p>
                <div id="runner-otp-banner" class="hidden bg-[#ECFDF5] border border-[#A7F3D0] rounded-2xl p-3.5 text-center space-y-1 my-2">
                    <p class="text-[9px] uppercase tracking-widest text-[#047857] font-black">System Generated Login OTP</p>
                    <p class="text-3xl font-black tracking-widest text-[#05A357]" id="runner-generated-otp-display">------</p>
                    <button type="button" onclick="autoFillRunnerOTP()" class="text-[10px] font-extrabold text-[#047857] underline cursor-pointer inline-flex items-center gap-1">
                        <i class="fas fa-magic"></i> Auto-fill OTP Code
                    </button>
                </div>
                <input type="text" id="runner-otp-input" placeholder="Enter 6-Digit Code" maxlength="6" class="w-full p-3.5 rounded-2xl bg-[#F7F9FA] border border-[#E2E8F0] text-base text-[#2D3748] focus:border-[#FFC244] focus:outline-none font-black text-center tracking-widest">
                <button onclick="verifyRunnerOTP()" class="w-full p-3.5 rounded-2xl bg-[#05A357] hover:bg-[#048245] text-white font-extrabold text-xs transition shadow-md">
                    Verify & Access Portal
                </button>
                <button onclick="resetRunnerAuthForm()" class="text-[10px] text-zinc-500 hover:text-zinc-700 block mx-auto font-bold pt-1">
                    ← Change Phone Number
                </button>
            </div>
        </div>

        <!-- Runner Dashboard Screen -->
        <div id="runner-dashboard" class="hidden space-y-4 sm:space-y-6">
            <!-- Header -->
            <header class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" class="h-10 sm:h-12 w-auto rounded-xl shadow-sm border border-black/10 shrink-0">
                    <div>
                        <h1 class="text-base sm:text-lg font-black tracking-tight text-[#0F172A] flex items-center gap-1.5" id="runner-title">
                            Runner Dispatch Center
                        </h1>
                        <p class="text-[10px] sm:text-xs text-[#64748B] flex items-center gap-1" id="live-event-banner">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#05A357] animate-pulse"></span> Connected to Arena Dispatch
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 ml-auto sm:ml-0">
                    <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-1.5 rounded-full text-xs font-extrabold text-[#0F172A]">
                        <span class="text-base">🏃</span>
                        <span id="runner-name-label" class="max-w-[120px] sm:max-w-none truncate">Runner</span>
                        <span class="bg-[#ECFDF5] text-[#047857] text-[9px] px-2 py-0.5 rounded-full font-black border border-[#A7F3D0]">ON DUTY</span>
                    </div>
                    <button onclick="logoutRunner()" class="text-xs bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#991B1B] border border-[#FCA5A5] px-3.5 py-1.5 rounded-full font-bold transition flex items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </header>

            <!-- Stat Summary Metrics Cards (4 Columns) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white border border-[#E2E8F0] p-3.5 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[#A31D1D]"></div>
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Active Dispatches</span>
                    <span class="text-base sm:text-xl font-black text-[#A31D1D]" id="stat-active-count">0 Active</span>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-3.5 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[#05A357]"></div>
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Completed Today</span>
                    <span class="text-base sm:text-xl font-black text-[#05A357]" id="stat-completed-count">0 Delivered</span>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-3.5 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[#FFC244]"></div>
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Delivery Fees</span>
                    <span class="text-base sm:text-xl font-black text-[#D97706]" id="stat-earnings-amount">Ksh 0</span>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-3.5 sm:p-4 rounded-2xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[#2563EB]"></div>
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-[#64748B] block font-extrabold mb-0.5">Runner Score</span>
                    <span class="text-base sm:text-xl font-black text-[#2563EB]">5.0 ★</span>
                </div>
            </div>

            <!-- Workspace Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
                
                <!-- Left Column: Assigned Delivery List (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="bg-white border border-[#E2E8F0] p-4 sm:p-5 rounded-2xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                            <h3 class="text-xs font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-route text-[#A31D1D]"></i> Assigned Delivery Tasks
                            </h3>
                            <span class="text-[10px] bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0] px-2.5 py-1 rounded-full font-bold">
                                <i class="fas fa-sync fa-spin mr-1 text-[8px]"></i> Real-time Sync (2s)
                            </span>
                        </div>

                        <!-- Active tasks container -->
                        <div id="runner-active-card-container" class="space-y-4">
                            <div class="text-center py-20 text-[#64748B] space-y-3">
                                <div class="w-16 h-16 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl flex items-center justify-center mx-auto text-2xl text-[#94A3B8]">
                                    <i class="fas fa-radar"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#0F172A]">Awaiting Kitchen Dispatches</h4>
                                    <p class="text-xs text-[#64748B]">Newly prepared orders from stalls will appear here automatically.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Navigation Map Guide & Verification (5 Cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Navigation Guide -->
                    <div id="runner-map-guide" class="bg-white border border-[#E2E8F0] p-4 sm:p-5 rounded-2xl shadow-sm space-y-3">
                        <h4 class="text-xs font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-location-arrow text-[#A31D1D]"></i> Arena Seat Finder Route
                        </h4>

                        <div class="h-40 bg-[#F8FAFC] rounded-2xl border border-[#CBD5E1] flex flex-col items-center justify-center relative overflow-hidden p-3 text-center">
                            <div class="absolute top-2 bg-white border border-[#E2E8F0] px-3 py-1 rounded-full text-[9px] text-[#475569] font-extrabold shadow-sm">
                                🏪 KITCHEN STALL PICKUP POINT
                            </div>
                            
                            <svg viewBox="0 0 100 40" class="w-32 h-auto my-auto pointer-events-none">
                                <path d="M 10,10 Q 50,30 90,30" fill="none" stroke="#A31D1D" stroke-dasharray="3" stroke-width="2.5" class="animate-pulse" />
                                <circle cx="10" cy="10" r="3.5" fill="#FFC244" />
                                <circle cx="90" cy="30" r="3.5" fill="#05A357" />
                            </svg>

                            <div class="absolute bottom-2 bg-[#A31D1D] px-3 py-1 rounded-full text-[9px] text-white font-extrabold shadow-md" id="runner-target-section-tag">
                                VIP A - ROW 12 - SEAT 18
                            </div>
                        </div>
                        <p class="text-[11px] text-[#64748B] leading-relaxed font-medium">
                            <i class="fas fa-info-circle text-[#A31D1D] mr-1"></i> Proceed to stall, confirm food pickup, then follow arena tunnel markings to the seat coordinates.
                        </p>
                    </div>

                    <!-- Runner Protocol & Safety -->
                    <div class="bg-white border border-[#E2E8F0] p-4 sm:p-5 rounded-2xl shadow-sm space-y-3">
                        <h5 class="text-xs font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-shield-check text-[#05A357]"></i> Standard Runner Protocol
                        </h5>
                        <ul class="text-xs text-[#475569] space-y-2 font-medium">
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-[#ECFDF5] text-[#047857] flex items-center justify-center text-[10px] font-black shrink-0">1</span>
                                <span>Inspect item names and quantities at stall before pickup.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-[#ECFDF5] text-[#047857] flex items-center justify-center text-[10px] font-black shrink-0">2</span>
                                <span>Call customer if seat location is difficult to navigate in crowd.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-[#ECFDF5] text-[#047857] flex items-center justify-center text-[10px] font-black shrink-0">3</span>
                                <span>Input the 4/6-digit customer verification PIN to close task.</span>
                            </li>
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

        // ── Auth helpers ──────────────────────────────────────────────────────
        function getToken() {
            try {
                const s = localStorage.getItem('justfeast_runner_user');
                return s ? JSON.parse(s).__token : null;
            } catch(e) { return null; }
        }

        async function authFetch(url, options = {}) {
            const token = getToken();
            options.headers = options.headers || {};
            if (token) options.headers['Authorization'] = `Bearer ${token}`;
            const res = await fetch(url, options);
            if (res.status === 401) {
                localStorage.removeItem('justfeast_runner_user');
                window.location.reload();
            }
            return res;
        }

        window.addEventListener('DOMContentLoaded', () => {
            // Session check
            const saved = localStorage.getItem('justfeast_runner_user');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (parsed && parsed.id) {
                        currentUser = parsed;
                        showDashboard();
                    }
                } catch(e) { localStorage.removeItem('justfeast_runner_user'); }
            }

            // Smart Adaptive Polling: 3s interval, pauses when tab is hidden
            pollingInterval = setInterval(syncDeliveries, 3000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) syncDeliveries();
            });
        });

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

        let lastRunnerOTP = '';
        function autoFillRunnerOTP() {
            if (lastRunnerOTP) {
                document.getElementById('runner-otp-input').value = lastRunnerOTP;
            }
        }

        async function sendRunnerOTP() {
            const phone = document.getElementById('runner-phone-input').value.trim();
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
                    document.getElementById('runner-otp-status-text').textContent = data.message;
                    if (data.otp) {
                        lastRunnerOTP = data.otp;
                        document.getElementById('runner-otp-input').value = data.otp;
                        const banner = document.getElementById('runner-otp-banner');
                        if (banner) banner.classList.remove('hidden');
                        const display = document.getElementById('runner-generated-otp-display');
                        if (display) display.textContent = data.otp;
                    }
                    document.getElementById('runner-auth-step-phone').classList.add('hidden');
                    document.getElementById('runner-auth-step-otp').classList.remove('hidden');
                    showToast('Verification OTP code sent to phone.', 'success');
                } else {
                    showToast(data.message || 'Error sending OTP', 'danger');
                }
            } catch(e) { showToast('Network error connecting to authentication server', 'danger'); }
        }

        async function verifyRunnerOTP() {
            const phone = document.getElementById('runner-phone-input').value.trim();
            const code = document.getElementById('runner-otp-input').value.trim();
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
                    localStorage.setItem('justfeast_runner_user', JSON.stringify({ ...currentUser, __token: data.token }));
                    showDashboard();
                    syncDeliveries();
                    showToast(`Welcome back, ${currentUser.name}!`, 'success');
                } else {
                    showToast(data.message || 'Verification failed', 'danger');
                }
            } catch(e) { showToast('Network error verifying code', 'danger'); }
        }

        function resetRunnerAuthForm() {
            document.getElementById('runner-auth-step-otp').classList.add('hidden');
            document.getElementById('runner-auth-step-phone').classList.remove('hidden');
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

        let lastDeliveriesHash = '';
        async function syncDeliveries() {
            if (!currentUser) return;
            // Skip re-rendering while user is actively typing in a PIN input field!
            if (document.activeElement && document.activeElement.id && document.activeElement.id.startsWith('runner-pin-')) {
                return;
            }
            // Skip network polling if tab is hidden/backgrounded to save battery & server CPU
            if (document.hidden) return;

            try {
                const res = await authFetch(`${API_BASE}/runner/deliveries?all=1`);
                if (res.ok) {
                    const rawData = await res.json();
                    const deliveries = Array.isArray(rawData) ? rawData : (rawData.data || []);
                    
                    // Performance Optimization: Only touch the DOM if data has actually changed!
                    const currentHash = JSON.stringify(deliveries);
                    if (currentHash !== lastDeliveriesHash) {
                        lastDeliveriesHash = currentHash;
                        renderDeliveries(deliveries);
                    }
                }
            } catch(e) {}
        }

        function renderDeliveries(deliveriesList) {
            const deliveries = Array.isArray(deliveriesList) ? deliveriesList : (deliveriesList.data || []);
            const container = document.getElementById('runner-active-card-container');
            const mapGuide = document.getElementById('runner-map-guide');

            // Preserve typed PIN values before re-rendering
            const savedPinValues = {};
            document.querySelectorAll('[id^="runner-pin-"]').forEach(input => {
                if (input.value) {
                    savedPinValues[input.id] = input.value;
                }
            });

            // Update stats
            const activeTasks = deliveries.filter(d => d.status !== 'delivered');
            const completedTasks = deliveries.filter(d => d.status === 'delivered');

            document.getElementById('stat-active-count').textContent = `${activeTasks.length} Active`;
            document.getElementById('stat-completed-count').textContent = `${completedTasks.length} Delivered`;
            document.getElementById('stat-earnings-amount').textContent = `Ksh ${(completedTasks.length * 30).toLocaleString()}`;

            if (deliveries.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-20 text-[#64748B] space-y-3">
                        <div class="w-16 h-16 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl flex items-center justify-center mx-auto text-2xl text-[#94A3B8]">
                            <i class="fas fa-radar"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#0F172A]">Awaiting Kitchen Dispatches</h4>
                            <p class="text-xs text-[#64748B]">Newly prepared orders from stalls will appear here automatically.</p>
                        </div>
                    </div>
                `;
                if (mapGuide) mapGuide.classList.add('hidden');
                return;
            }

            if (mapGuide) mapGuide.classList.remove('hidden');

            // Set navigation tag for active delivery
            const activeFirst = deliveries.find(d => d.status !== 'delivered') || deliveries[0];
            if (activeFirst && activeFirst.order) {
                const loc = activeFirst.order.seat_location || {};
                const locText = (loc.type === 'gps' || loc.latitude)
                    ? `GPS Pin: ${loc.description || (parseFloat(loc.latitude).toFixed(4) + ', ' + parseFloat(loc.longitude).toFixed(4))}`
                    : `${loc.section || 'Seat'} - ROW ${loc.row || ''} - SEAT ${loc.seat || ''}`;
                const tagEl = document.getElementById('runner-target-section-tag');
                if (tagEl) tagEl.textContent = locText;
            }

            container.innerHTML = '';
            deliveries.forEach(del => {
                const o = del.order || {};
                const loc = o.seat_location || {};
                const locText = (loc.type === 'gps' || loc.latitude)
                    ? `GPS Pin: ${loc.description || (parseFloat(loc.latitude).toFixed(4) + ', ' + parseFloat(loc.longitude).toFixed(4))}`
                    : `${loc.section || 'Seat'}, Row ${loc.row || ''}, Seat ${loc.seat || ''}`;

                const itemsText = (o.items || []).map(i => `<span class="inline-flex items-center gap-1 bg-[#F8FAFC] border border-[#CBD5E1] text-[#1E293B] px-3 py-1.5 rounded-xl text-xs font-bold mr-1.5 mb-1.5 shadow-2xs"><i class="fas fa-utensils text-[10px] text-[#A31D1D]"></i> ${i.quantity}x ${i.product ? i.product.name : 'Item'}</span>`).join('');

                const card = document.createElement('div');
                
                let borderAccent = 'border-l-4 border-l-[#FFC244]';
                let badgeClass = 'bg-[#FEF3C7] text-[#92400E] border-[#FCD34D]';
                let badgeText = 'PENDING PICKUP';

                if (del.status === 'picked_up') {
                    borderAccent = 'border-l-4 border-l-[#2563EB]';
                    badgeClass = 'bg-[#DBEAFE] text-[#1E40AF] border-[#93C5FD]';
                    badgeText = 'EN ROUTE';
                } else if (del.status === 'delivered') {
                    borderAccent = 'border-l-4 border-l-[#05A357]';
                    badgeClass = 'bg-[#ECFDF5] text-[#047857] border-[#A7F3D0]';
                    badgeText = 'DELIVERED';
                }

                card.className = `bg-white border border-[#E2E8F0] ${borderAccent} p-5 rounded-3xl space-y-4 shadow-sm relative overflow-hidden hover:shadow-md transition-all`;

                const custPhone = o.user?.phone || '0700000000';
                const callBtn = `<a href="tel:${custPhone}" class="inline-flex items-center gap-1.5 bg-[#ECFDF5] text-[#047857] hover:bg-[#D1FAE5] border border-[#A7F3D0] px-3.5 py-1.5 rounded-full font-extrabold text-[11px] transition shadow-2xs">
                    <i class="fas fa-phone-alt text-[10px]"></i> Call Customer
                </a>`;

                let actionSection = '';
                if (del.status === 'pending') {
                    actionSection = `
                        <button onclick="updateDeliveryStatus(${del.id}, 'picked_up')" 
                                class="w-full py-3.5 bg-gradient-to-r from-[#A31D1D] to-[#841313] hover:opacity-95 text-white rounded-2xl text-xs font-extrabold shadow-lg shadow-[#A31D1D]/20 transition flex items-center justify-center gap-2">
                            <i class="fas fa-box-open text-sm"></i> Confirm Pickup from Kitchen Stall
                        </button>`;
                } else if (del.status === 'picked_up') {
                    actionSection = `
                        <div class="space-y-2 pt-3 border-t border-[#E2E8F0]">
                            <div class="flex items-center justify-between">
                                <label class="block text-[9px] font-black text-[#475569] uppercase tracking-wider">
                                    <i class="fas fa-key text-[#05A357] mr-1"></i> Customer Verification PIN
                                </label>
                                <span class="text-[9px] text-[#64748B] font-bold">Ask customer for PIN upon delivery</span>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="relative flex-1">
                                    <i class="fas fa-lock text-[#94A3B8] absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
                                    <input type="text" id="runner-pin-${del.id}" placeholder="Enter 4 or 6-digit PIN" maxlength="6"
                                           class="w-full pl-9 pr-3 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-2xl text-center font-black tracking-widest text-sm text-[#0F172A] outline-none focus:border-[#05A357] focus:ring-2 focus:ring-[#05A357]/20">
                                </div>
                                <button onclick="verifyRunnerDelivery(${del.id})" 
                                        class="px-6 py-2.5 bg-[#05A357] hover:bg-[#048245] text-white rounded-2xl text-xs font-extrabold shadow-md shadow-[#05A357]/20 transition flex items-center justify-center gap-1.5 shrink-0">
                                    <i class="fas fa-check-circle text-sm"></i> Verify & Handover
                                </button>
                            </div>
                        </div>`;
                } else {
                    actionSection = `
                        <div class="text-center py-2 bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0] rounded-2xl text-[11px] font-black flex items-center justify-center gap-1.5">
                            <i class="fas fa-check-circle text-xs"></i> Delivery Completed & Verified
                        </div>`;
                }

                card.innerHTML = `
                    <div class="flex flex-wrap justify-between items-center gap-2 pb-3 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center text-lg text-[#0F172A] shadow-2xs shrink-0">
                                👤
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-[#A31D1D] font-black">Delivery #${del.id}</span>
                                    <span class="text-[10px] bg-[#F1F5F9] text-[#475569] px-2 py-0.5 rounded-md font-bold">Order #${o.id || ''}</span>
                                </div>
                                <h4 class="text-sm font-black text-[#0F172A] mt-0.5">${o.user ? o.user.name : 'Customer'}</h4>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${callBtn}
                            <span class="text-[9px] font-black uppercase tracking-wider px-3 py-1 rounded-full border ${badgeClass}">
                                ${badgeText}
                            </span>
                        </div>
                    </div>

                    <!-- Step Stepper Bar -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#FFF8E7] border border-[#F7E5B2] flex items-center justify-center text-sm shrink-0">
                                🏪
                            </div>
                            <div>
                                <span class="text-[8px] font-extrabold text-[#64748B] uppercase tracking-wider block">Pickup Stall</span>
                                <strong class="text-xs font-black text-[#0F172A]">${o.vendor ? o.vendor.business_name : 'Stall'}</strong>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 border-t md:border-t-0 md:border-l border-[#E2E8F0] pt-2 md:pt-0 md:pl-3">
                            <div class="w-8 h-8 rounded-xl bg-[#FEF2F2] border border-[#FCA5A5] flex items-center justify-center text-sm shrink-0">
                                📍
                            </div>
                            <div>
                                <span class="text-[8px] font-extrabold text-[#991B1B] uppercase tracking-wider block">Destination Coordinates</span>
                                <strong class="text-xs font-black text-[#A31D1D]">${locText}</strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] uppercase font-extrabold text-[#64748B] block mb-1.5 tracking-wider">Ordered Food & Drinks</span>
                        <div class="flex flex-wrap">${itemsText}</div>
                    </div>

                    <div>
                        ${actionSection}
                    </div>
                `;

                container.appendChild(card);
            });

            // Restore saved PIN values if any
            Object.keys(savedPinValues).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = savedPinValues[id];
            });
        }

        async function updateDeliveryStatus(delId, status) {
            playSound('success');
            try {
                const res = await authFetch(`${API_BASE}/runner/deliveries/${delId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                if (res.ok) {
                    syncDeliveries();
                }
            } catch(e) {}
        }

        async function verifyRunnerDelivery(delId) {
            let pin = '';
            if (delId) {
                const el = document.getElementById(`runner-pin-${delId}`);
                if (el) pin = el.value.trim();
            }
            if (!pin) {
                const generalEl = document.getElementById('runner-pin-input');
                if (generalEl) pin = generalEl.value.trim();
            }

            if (!pin) {
                showToast("Please enter the verification PIN!", "danger");
                return;
            }

            try {
                let targetId = delId;
                if (!targetId) {
                    const res = await authFetch(`${API_BASE}/runner/deliveries?all=1`);
                    if (res.ok) {
                        const rawData = await res.json();
                        const list = Array.isArray(rawData) ? rawData : (rawData.data || []);
                        const activeDel = list.find(d => d.status === 'picked_up' || d.status === 'pending');
                        if (activeDel) targetId = activeDel.id;
                    }
                }

                if (!targetId) {
                    showToast("No active delivery found to verify.", "danger");
                    return;
                }

                const verifyRes = await authFetch(`${API_BASE}/runner/deliveries/${targetId}/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pin })
                });

                const data = await verifyRes.json();
                if (verifyRes.ok && data.status === 'success') {
                    playSound('success');
                    showToast(data.message || "🎉 Delivery verified successfully! Order completed.", 'success');
                    const generalEl = document.getElementById('runner-pin-input');
                    if (generalEl) generalEl.value = '';
                    syncDeliveries();
                } else {
                    showToast(data.message || "Invalid verification PIN.", 'danger');
                }
            } catch(e) {
                showToast("Network error verifying PIN", 'danger');
            }
        }
    </script>
</body>
</html>
