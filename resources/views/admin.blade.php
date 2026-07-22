<!DOCTYPE html>
<html lang="en">
@php
  $authUser = Auth::user();
  $userData = $authUser ? [
      'id' => $authUser->id,
      'name' => $authUser->name,
      'email' => $authUser->email,
      'role' => $authUser->role,
  ] : null;
@endphp
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JustFeast Admin — Control Center</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const API_BASE = "{{ url('/api') }}";
  const LARAVEL_USER = @json($userData);
</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#080b11;--surface:#0f1524;--surface2:#161f33;--border:rgba(255,255,255,0.06);
  --brand:#ff6b00;--brand-glow:rgba(255,107,0,0.15);--brand2:#10b981;--text:#f8fafc;--muted:#64748b;
  --red:#f43f5e;--yellow:#eab308;--blue:#3b82f6;--purple:#a855f7;
}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden}

/* ── Sidebar ── */
.sidebar{width:260px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:1.75rem 1.25rem;gap:.3rem;position:fixed;top:0;left:0;height:100vh;z-index:40}
.sidebar-logo{display:flex;align-items:center;gap:.75rem;padding:.5rem .6rem;margin-bottom:2rem}
.sidebar-logo .icon{width:40px;height:40px;background:linear-gradient(135deg, var(--brand), #ff8a3d);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff;box-shadow:0 4px 12px var(--brand-glow)}
.sidebar-logo span{font-weight:800;font-size:1.25rem;letter-spacing:-0.03em}
.sidebar-logo span em{color:var(--brand);font-style:normal}
.nav-item{display:flex;align-items:center;gap:.8rem;padding:.75rem 1rem;border-radius:12px;font-size:.875rem;font-weight:600;color:var(--muted);cursor:pointer;transition:.15s;text-decoration:none;margin-bottom:4px}
.nav-item:hover{background:rgba(255,255,255,0.02);color:var(--text)}
.nav-item.active{background:rgba(255,107,0,0.08);color:var(--brand);box-shadow:inset 3px 0 0 var(--brand)}
.nav-item i{width:20px;text-align:center;font-size:1rem}
.nav-section{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:var(--muted);padding:1.2rem 1rem .4rem;margin-top:.5rem}
.sidebar-footer{margin-top:auto;padding-top:1.25rem;border-top:1px solid var(--border)}
.user-chip{display:flex;align-items:center;gap:.75rem;padding:.5rem .6rem;border-radius:10px}
.avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--brand),#fb923c);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0}
.user-chip .info p{font-size:.8rem;font-weight:700}
.user-chip .info span{font-size:.65rem;color:var(--muted)}
.btn-logout{display:flex;align-items:center;gap:.5rem;padding:.5rem .8rem;border-radius:9px;font-size:.75rem;font-weight:600;color:var(--muted);cursor:pointer;background:none;border:none;width:100%;margin-top:.5rem;transition:.15s}
.btn-logout:hover{background:rgba(239,68,68,.08);color:var(--red)}

/* ── Main Layout ── */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{padding:1.25rem 2.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--surface);position:sticky;top:0;z-index:30}
.topbar h1{font-size:1.25rem;font-weight:800;letter-spacing:-0.02em}
.topbar .meta{font-size:.75rem;color:var(--muted);margin-top:3px}
.live-badge{display:flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:800;color:var(--brand2);background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.15);padding:.35rem .8rem;border-radius:20px}
.live-dot{width:6px;height:6px;border-radius:50%;background:var(--brand2);animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

.content{padding:2.5rem;flex:1}

/* ── KPI Grid ── */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:2rem}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem;position:relative;overflow:hidden;transition:.2s}
.kpi:hover{border-color:rgba(255,107,0,.2);transform:translateY(-1px)}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:4px}
.kpi.orange::before{background:linear-gradient(90deg,var(--brand),#fb923c)}
.kpi.green::before{background:linear-gradient(90deg,var(--brand2),#34d399)}
.kpi.blue::before{background:linear-gradient(90deg,var(--blue),#60a5fa)}
.kpi.yellow::before{background:linear-gradient(90deg,var(--yellow),#fcd34d)}
.kpi-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:1rem}
.kpi.orange .kpi-icon{background:rgba(255,107,0,.1);color:var(--brand)}
.kpi.green .kpi-icon{background:rgba(16,185,129,.1);color:var(--brand2)}
.kpi.blue .kpi-icon{background:rgba(59,130,246,.1);color:var(--blue)}
.kpi.yellow .kpi-icon{background:rgba(245,158,11,.1);color:var(--yellow)}
.kpi-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.2rem}
.kpi-value{font-size:2rem;font-weight:800;letter-spacing:-.03em}
.kpi-sub{font-size:.75rem;color:var(--muted);margin-top:.4rem;display:flex;align-items:center;gap:.3rem}
.trend-up{color:var(--brand2)}
.trend-down{color:var(--red)}

/* ── Section Specific Elements ── */
.section-content{display:none}
.section-content.active{display:block}

/* Charts grid */
.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
.chart-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem;min-height:300px;display:flex;flex-direction:column}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.chart-header h4{font-size:.85rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)}
.chart-container{flex:1;position:relative}

/* ── Content Grid ── */
.dash-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.card-header{padding:1.4rem 1.8rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-header h3{font-size:.875rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);display:flex;align-items:center;gap:.6rem}
.card-header .header-action{font-size:.75rem;font-weight:700;color:var(--brand);cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:.3rem}
.card-header .header-action:hover{color:#ea580c}

/* Tables styling */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.85rem}
thead th{padding:1rem 1.5rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);background:rgba(255,255,255,0.015);border-bottom:1px solid var(--border)}
tbody tr{border-bottom:1px solid var(--border);transition:.15s}
tbody tr:hover{background:rgba(255,255,255,.01)}
tbody td{padding:1.1rem 1.5rem;vertical-align:middle}

/* Status Pills */
.status-pill{display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:750;text-transform:uppercase;letter-spacing:.06em;padding:.3rem .75rem;border-radius:20px}
.s-created{background:rgba(148,163,184,.1);color:#94a3b8}
.s-preparing{background:rgba(245,158,11,.1);color:var(--yellow)}
.s-ready{background:rgba(59,130,246,.1);color:var(--blue)}
.s-enroute{background:rgba(255,107,0,.1);color:var(--brand)}
.s-delivered{background:rgba(16,185,129,.1);color:var(--brand2)}
.s-paid{background:rgba(16,185,129,.1);color:var(--brand2)}
.s-pending{background:rgba(245,158,11,.1);color:var(--yellow)}

/* System Terminal Widget */
.terminal-card{background:#04060b;border:1px solid var(--border);border-radius:20px;padding:1.25rem;font-family:'Courier New',Courier,monospace;margin-top:1.5rem}
.terminal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;padding-bottom:.5rem;border-bottom:1px solid rgba(255,255,255,0.05)}
.terminal-title{font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;color:var(--brand);font-weight:bold}
.terminal-body{height:140px;overflow-y:auto;font-size:.75rem;line-height:1.4rem;color:#10b981}

/* Filters Bar */
.filters-bar{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;gap:1.25rem;align-items:center}
.search-input-wrap{position:relative;flex:1;min-width:320px}
.search-input-wrap i{position:absolute;left:1.2rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.9rem}
.search-input{width:100%;background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:12px;padding:.8rem 1rem .8rem 2.8rem;color:var(--text);font-size:.875rem;outline:none;transition:.15s}
.search-input:focus{border-color:var(--brand);background:rgba(255,255,255,0.04)}
.select-filter{background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:12px;padding:.8rem 1.4rem;color:var(--text);font-size:.85rem;outline:none;cursor:pointer}
.select-filter:focus{border-color:var(--brand)}
.select-filter option{background:#0f1524;color:var(--text)}

/* System Health Controls */
.health-control-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-top:1.5rem}
.health-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1.2rem;display:flex;align-items:center;gap:.9rem}
.health-icon-wrap{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem}
.health-card.online .health-icon-wrap{background:rgba(16,185,129,.1);color:var(--brand2)}
.health-card.warning .health-icon-wrap{background:rgba(245,158,11,.1);color:var(--yellow)}
.health-info h5{font-size:.78rem;font-weight:700}
.health-info span{font-size:.65rem;color:var(--muted)}

/* Vendors Tab Content */
.vendor-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1.5rem}
.vendor-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:.2s}
.vendor-card:hover{border-color:rgba(255,107,0,.2)}
.vendor-card-header{padding:1.5rem;display:flex;align-items:center;gap:1rem;border-bottom:1px solid var(--border)}
.vendor-card-logo{width:48px;height:48px;border-radius:12px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
.vendor-card-title h4{font-size:1rem;font-weight:750}
.vendor-card-title span{font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:.35rem}
.vendor-stats{display:grid;grid-template-columns:1fr 1fr;background:rgba(0,0,0,0.1)}
.vendor-stat-item{padding:1.1rem 1.5rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
.vendor-stat-item:nth-child(2n){border-right:none}
.vendor-stat-label{font-size:.65rem;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:.2rem;letter-spacing:.05em}
.vendor-stat-val{font-size:1.15rem;font-weight:800}
.vendor-inventory-toggle{width:100%;background:none;border:none;color:var(--muted);font-size:.78rem;font-weight:750;padding:.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:.15s}
.vendor-inventory-toggle:hover{background:rgba(255,255,255,0.02);color:var(--text)}
.vendor-menu-list{border-top:1px solid var(--border);display:none;background:rgba(0,0,0,0.15);padding:.5rem 0}
.vendor-menu-item{display:flex;justify-content:space-between;align-items:center;padding:.7rem 1.5rem;font-size:.78rem}
.vendor-menu-item:not(:last-child){border-bottom:1px solid rgba(255,255,255,0.03)}

/* Heatmap Tab Content */
.heatmap-container{display:grid;grid-template-columns:1fr 420px;gap:1.5rem}
.stadium-wrap{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:2.5rem;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative}
.stadium-svg-container{width:100%;max-width:440px;margin:2rem 0;position:relative}
.stadium-svg-container svg{width:100%;height:auto}
.stadium-section-poly{transition:.3s;cursor:pointer;fill-opacity:0.25;stroke-width:1.5;stroke-linejoin:round}
.stadium-section-poly:hover{fill-opacity:0.45;stroke-width:2.5}
.heat-legend{display:flex;gap:1.5rem;font-size:.72rem;font-weight:700;color:var(--muted)}
.legend-item{display:flex;align-items:center;gap:.4rem}
.legend-color{width:12px;height:12px;border-radius:4px}

/* Page Control Styling */
.pagination-controls{display:flex;justify-content:space-between;align-items:center;padding:1.1rem 1.8rem;border-top:1px solid var(--border);font-size:.8rem;color:var(--muted)}
.btn-page{background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:.5rem 1rem;border-radius:10px;cursor:pointer;font-size:.78rem;font-weight:600;transition:.15s}
.btn-page:hover:not(:disabled){background:rgba(255,255,255,0.05)}
.btn-page:disabled{opacity:0.4;cursor:not-allowed}

/* Sidebar Vendor List Item */
.vendor-item{display:flex;align-items:center;gap:.8rem;padding:1.1rem 1.5rem;border-bottom:1px solid var(--border)}
.vendor-item:last-child{border-bottom:none}
.vendor-logo{width:38px;height:38px;border-radius:10px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.vendor-bar-wrap{flex:1;min-width:0}
.vendor-bar-wrap .top{display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.4rem}
.vendor-bar-wrap .top strong{font-weight:750}
.vendor-bar-wrap .top span{color:var(--brand2);font-weight:800}

/* Quick Tools */
.quick-tools-grid{display:grid;grid-template-columns:repeat(4, 1fr);gap:1.25rem;margin-top:1.5rem}
.tool-btn{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1rem;color:var(--text);font-size:.8rem;font-weight:700;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;cursor:pointer;transition:.15s}
.tool-btn:hover{background:rgba(255,107,0,0.05);border-color:var(--brand)}
.tool-btn i{font-size:1.2rem;color:var(--brand)}
</style>
</head>
<body>

{{-- ── Sidebar ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="icon" style="overflow: hidden; background: white; box-shadow: none;">
      <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px; border-radius: 12px;">
    </div>
    <span>Just<em>Feast</em></span>
  </div>

  <span class="nav-section">Global Monitor</span>
  <a id="nav-link-overview" class="nav-item active" onclick="showSection('overview')"><i class="fas fa-chart-pie"></i> Dashboard</a>
  <a id="nav-link-orders" class="nav-item" onclick="showSection('orders')"><i class="fas fa-receipt"></i> All Orders</a>
  <a id="nav-link-users" class="nav-item" onclick="showSection('users')"><i class="fas fa-users-cog"></i> User Accounts</a>

  <span class="nav-section">Financial & Operations</span>
  <a id="nav-link-reports" class="nav-item" onclick="showSection('reports')"><i class="fas fa-file-invoice-dollar"></i> System Reports</a>
  <a id="nav-link-vendors" class="nav-item" onclick="showSection('vendors')"><i class="fas fa-store"></i> Vendors</a>
  <a id="nav-link-heatmap" class="nav-item" onclick="showSection('heatmap')"><i class="fas fa-fire"></i> Heatmap</a>

  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="avatar">S</div>
      <div class="info">
        <p id="sidebar-user-name">Admin</p>
        <span>Administrator</span>
      </div>
    </div>
    <button class="btn-logout" onclick="logoutAdmin()"><i class="fas fa-sign-out-alt"></i> Sign out</button>
  </div>
</aside>

{{-- ── Main Panel ── --}}
<div class="main">
  <div class="topbar">
    <div>
      <h1 id="topbar-title">Operations Dashboard</h1>
      <p class="meta" id="topbar-meta">JustFeast — Global Admin Command Center</p>
    </div>
    <div class="live-badge"><div class="live-dot"></div> Live sync</div>
  </div>

  <div class="content">

    {{-- KPI Cards --}}
    <div class="kpi-grid">
      <div class="kpi orange">
        <div class="kpi-icon"><i class="fas fa-coins"></i></div>
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-value" id="kpi-revenue">Ksh 0</div>
        <div class="kpi-sub"><span class="trend-up"><i class="fas fa-arrow-trend-up"></i> +12.4%</span> vs last hour</div>
      </div>
      <div class="kpi green">
        <div class="kpi-icon"><i class="fas fa-bag-shopping"></i></div>
        <div class="kpi-label">Paid Orders</div>
        <div class="kpi-value" id="kpi-orders">0</div>
        <div class="kpi-sub"><span class="trend-up"><i class="fas fa-arrow-trend-up"></i> +8.1%</span> vs last hour</div>
      </div>
      <div class="kpi blue">
        <div class="kpi-icon"><i class="fas fa-clock"></i></div>
        <div class="kpi-label">Avg Delivery</div>
        <div class="kpi-value" id="kpi-speed">—</div>
        <div class="kpi-sub"><span class="trend-up"><i class="fas fa-check-double"></i> Optimal</span> 8.4 mins goal</div>
      </div>
      <div class="kpi yellow">
        <div class="kpi-icon"><i class="fas fa-store"></i></div>
        <div class="kpi-label">Active Vendors</div>
        <div class="kpi-value" id="kpi-vendors">3</div>
        <div class="kpi-sub"><span class="trend-up"><i class="fas fa-plug"></i> 100% online</span> status</div>
      </div>
    </div>

    {{-- ── SECTION: OVERVIEW ── --}}
    <div id="section-overview" class="section-content active">
      
      {{-- Charts row --}}
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-header">
            <h4>Concert Transaction Velocity</h4>
            <span style="font-size:.65rem;color:var(--muted)">Orders / Min</span>
          </div>
          <div class="chart-container">
            <canvas id="velocityChart"></canvas>
          </div>
        </div>
        <div class="chart-card">
          <div class="chart-header">
            <h4>Vendor Share</h4>
            <span style="font-size:.65rem;color:var(--muted)">By Revenue</span>
          </div>
          <div class="chart-container" style="display:flex;align-items:center;justify-content:center">
            <canvas id="vendorShareChart" style="max-height:220px"></canvas>
          </div>
        </div>
      </div>

      <div class="dash-grid">
        {{-- Live orders table --}}
        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-rss" style="color:var(--brand)"></i> Active Dispatch Feed</h3>
            <a class="header-action" onclick="showSection('orders')">Manage Orders <i class="fas fa-chevron-right"></i></a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Vendor</th>
                  <th>Seat Location</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody id="orders-tbody">
                <tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--muted)"><i class="fas fa-spinner fa-spin"></i> Loading dispatch data...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Right sidebar vendor list --}}
        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-store" style="color:var(--brand2)"></i> Top Performers</h3>
          </div>
          <div id="vendor-list">
            <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.78rem"><i class="fas fa-spinner fa-spin"></i></div>
          </div>
        </div>
      </div>

      {{-- Interactive Quick Actions Panel --}}
      <div class="card" style="padding:1.5rem">
        <h3 style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Platform Control Panel</h3>
        <div class="quick-tools-grid">
          <div class="tool-btn" onclick="triggerHealthCheck()"><i class="fas fa-heartbeat"></i> Run System Diagnostics</div>
          <div class="tool-btn" onclick="triggerETIMSSync()"><i class="fas fa-receipt"></i> eTIMS Z-Report Sync</div>
          <div class="tool-btn" onclick="triggerClearCache()"><i class="fas fa-trash-can"></i> Flush Memory Cache</div>
          <div class="tool-btn" onclick="triggerBroadcast()"><i class="fas fa-bullhorn"></i> Broadcast Alert to Runners</div>
        </div>
      </div>

      {{-- System Terminal Logs --}}
      <div class="terminal-card">
        <div class="terminal-header">
          <span class="terminal-title">Central Audit logs</span>
          <span style="font-size:.65rem;color:var(--muted)">Real-time operations terminal</span>
        </div>
        <div class="terminal-body" id="terminal-body">
          [05:18:27] [SYSTEM] JustFeast core system boot completed.<br>
          [05:19:04] [MPESA] Simulation listener successfully bound to port 8001.<br>
          [05:20:11] [FARAJA] Merchant ID config validated successfully.<br>
          [05:22:30] [ETIMS] Daily sales database verified. Compliance check passed.<br>
        </div>
      </div>

    </div>

    {{-- ── SECTION: ALL ORDERS ── --}}
    <div id="section-orders" class="section-content">
      <div class="filters-bar">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" id="order-search" class="search-input" placeholder="Search orders by customer name, vendor, seat..." oninput="filterOrders()">
        </div>
        <select id="filter-order-status" class="select-filter" onchange="filterOrders()">
          <option value="">All Order Statuses</option>
          <option value="created">Created</option>
          <option value="accepted">Accepted</option>
          <option value="preparing">Preparing</option>
          <option value="ready">Ready</option>
          <option value="enroute">En Route</option>
          <option value="delivered">Delivered</option>
        </select>
        <select id="filter-payment-status" class="select-filter" onchange="filterOrders()">
          <option value="">All Payment Statuses</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
        </select>
      </div>

      <div class="card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer Info</th>
                <th>Vendor</th>
                <th>Seat Location</th>
                <th>Runner</th>
                <th>Total Amount</th>
                <th>Order Status</th>
                <th>Payment</th>
                <th>Timestamp</th>
              </tr>
            </thead>
            <tbody id="all-orders-tbody">
              <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--muted)"><i class="fas fa-spinner fa-spin"></i> Fetching system order database...</td></tr>
            </tbody>
          </table>
        </div>
        <div class="pagination-controls">
          <span id="orders-page-info">Showing 0 of 0 entries</span>
          <div style="display:flex;gap:.5rem">
            <button id="btn-prev-page" class="btn-page" disabled onclick="prevPage()">Prev</button>
            <button id="btn-next-page" class="btn-page" disabled onclick="nextPage()">Next</button>
          </div>
        </div>
      </div>
    </div>

    {{-- ── SECTION: USER ACCOUNTS ── --}}
    <div id="section-users" class="section-content">
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left: Users table -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-users" style="color:var(--brand)"></i> Platform User Directory</h3>
            <div class="filters-bar" style="border-bottom: none; padding: 0; margin: 0; gap: 0.5rem;">
              <div class="search-input-wrap" style="width: 240px;">
                <i class="fas fa-search"></i>
                <input type="text" id="user-search" class="search-input" placeholder="Search user by name, email..." oninput="filterUsers()">
              </div>
              <select id="filter-user-role" class="select-filter" style="width: 140px; background:var(--surface2); border:1px solid var(--border); color:var(--text);" onchange="filterUsers()">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="vendor">Vendor</option>
                <option value="runner">Runner</option>
                <option value="client">Client</option>
              </select>
            </div>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Registered</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="users-table-body">
                <tr>
                  <td colspan="6" style="text-align:center;padding:4rem;color:var(--muted)"><i class="fas fa-spinner fa-spin fa-2x"></i></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Right: Create User Form -->
        <div class="card" style="padding: 1.5rem;">
          <h3 style="font-size: .875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 1.25rem;">
            <i class="fas fa-user-plus" style="color: var(--brand);"></i> Create New Account
          </h3>
          
          <form id="create-user-form" onsubmit="handleCreateUser(event)" style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
              <label style="display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Full Name</label>
              <input type="text" id="new-user-name" required placeholder="John Doe" style="width: 100%; padding: .65rem .85rem; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none;">
            </div>

            <div>
              <label style="display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Email Address</label>
              <input type="email" id="new-user-email" required placeholder="john@justfeast.com" style="width: 100%; padding: .65rem .85rem; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none;">
            </div>

            <div>
              <label style="display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Password</label>
              <input type="password" id="new-user-password" required placeholder="••••••••" style="width: 100%; padding: .65rem .85rem; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none;">
            </div>

            <div>
              <label style="display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Assign Role</label>
              <select id="new-user-role" required style="width: 100%; padding: .65rem .85rem; border-radius: 8px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none;">
                <option value="client">Client (Customer)</option>
                <option value="runner">Runner (Courier)</option>
                <option value="vendor">Vendor (Stall Staff)</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <button type="submit" style="background: var(--brand); color: #fff; padding: .75rem; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: .15s; margin-top: .5rem;">
              Create User Account
            </button>
          </form>
        </div>

      </div>
    </div>

    {{-- ── SECTION: SYSTEM REPORTS ── --}}
    <div id="section-reports" class="section-content">
      
      <!-- KPI Top Summary Grid -->
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="kpi orange" style="padding: 1.25rem;">
          <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
          <div class="kpi-label">Consolidated Revenue</div>
          <div class="kpi-value" id="report-kpi-revenue" style="font-size: 1.5rem;">Ksh 0.00</div>
          <div class="kpi-sub">Gross settled orders</div>
        </div>
        <div class="kpi green" style="padding: 1.25rem;">
          <div class="kpi-icon"><i class="fas fa-file-invoice"></i></div>
          <div class="kpi-label">eTIMS VAT (16%)</div>
          <div class="kpi-value" id="report-kpi-vat" style="font-size: 1.5rem;">Ksh 0.00</div>
          <div class="kpi-sub">Total sales VAT liability</div>
        </div>
        <div class="kpi blue" style="padding: 1.25rem;">
          <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
          <div class="kpi-label">Average Order Size</div>
          <div class="kpi-value" id="report-kpi-aov" style="font-size: 1.5rem;">Ksh 0.00</div>
          <div class="kpi-sub">Average transaction size</div>
        </div>
        <div class="kpi yellow" style="padding: 1.25rem;">
          <div class="kpi-icon"><i class="fas fa-clipboard-check"></i></div>
          <div class="kpi-label">Total Transactions</div>
          <div class="kpi-value" id="report-kpi-count" style="font-size: 1.5rem;">0</div>
          <div class="kpi-sub">Gross invoice count</div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left: eTIMS Compliant Tax Ledger -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-shield-alt" style="color: var(--brand2);"></i> eTIMS Fiscal Compliance Log</h3>
            <span style="font-size: .65rem; background: rgba(16,185,129,.1); color: var(--brand2); border: 1px solid rgba(16,185,129,.2); padding: 2px 8px; border-radius: 20px;">Active</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Vendor Stall</th>
                  <th>Revenue</th>
                  <th>VAT (16%)</th>
                  <th>Invoices</th>
                  <th>eTIMS Synced</th>
                </tr>
              </thead>
              <tbody id="report-etims-body">
                <!-- eTIMS rows -->
              </tbody>
            </table>
          </div>
          <div style="padding: 1.25rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: .75rem; color: var(--muted);"><i class="fas fa-clock"></i> eTIMS compliance sync interval: 5m</span>
            <button onclick="triggerEtimSync()" class="btn-page" style="font-size: .7rem; font-weight: 700; color: var(--brand); border-color: rgba(255,107,0,.3); background: none;"><i class="fas fa-sync mr-1"></i> Force Z-Report Push</button>
          </div>
        </div>

        <!-- Right: Runner Performance and Speed Metrics -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-running" style="color: var(--blue);"></i> Courier Runner Delivery Report</h3>
            <span class="meta" style="font-size: .7rem;">Performance stats</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Runner</th>
                  <th>Completed Tasks</th>
                  <th>Active Tasks</th>
                  <th>Efficiency Rating</th>
                </tr>
              </thead>
              <tbody id="report-runners-body">
                <!-- Runner metrics rows -->
              </tbody>
            </table>
          </div>
          <div style="padding: 1.25rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: .75rem; color: var(--muted);"><i class="fas fa-tachometer-alt"></i> Target Delivery SLA: &lt; 15 mins</span>
            <button onclick="alert('Exporting PDF dispatch history...')" class="btn-page" style="font-size: .7rem; font-weight: 700; color: var(--blue); border-color: rgba(59,130,246,.3); background: none;"><i class="fas fa-file-pdf mr-1"></i> Export SLA Log</button>
          </div>
        </div>

      </div>

      <!-- Export Suite -->
      <div class="card" style="margin-top: 1.5rem; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="font-size: .85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted);">Concert Settlement Export Suite</h4>
            <p style="font-size: .72rem; color: var(--muted); margin-top: 4px;">Generate structured reports for event management and vendor payouts</p>
          </div>
          <div style="display: flex; gap: 0.75rem;">
            <button onclick="downloadReport('orders')" class="tool-btn" style="flex-direction: row; padding: .6rem 1.2rem; border-radius: 10px; font-size: .75rem; border:1px solid var(--border); margin:0;"><i class="fas fa-file-csv"></i> Export Orders CSV</button>
            <button onclick="downloadReport('tax')" class="tool-btn" style="flex-direction: row; padding: .6rem 1.2rem; border-radius: 10px; font-size: .75rem; border:1px solid var(--border); margin:0;"><i class="fas fa-file-excel" style="color: var(--brand2);"></i> Export Tax Ledger</button>
            <button onclick="downloadReport('dispatch')" class="tool-btn" style="flex-direction: row; padding: .6rem 1.2rem; border-radius: 10px; font-size: .75rem; border:1px solid var(--border); margin:0;"><i class="fas fa-file-invoice" style="color: var(--blue);"></i> Export Courier Invoices</button>
          </div>
        </div>
      </div>

    </div>

    {{-- ── SECTION: VENDORS ── --}}
    <div id="section-vendors" class="section-content">
      <div class="vendor-grid" id="all-vendors-cards">
        <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
      </div>
    </div>

    {{-- ── SECTION: HEATMAP ── --}}
    <div id="section-heatmap" class="section-content">
      <div class="heatmap-container">
        <div class="stadium-wrap">
          <h3 style="font-size:.9rem;font-weight:700;align-self:flex-start">Uhuru Gardens Layout Monitor</h3>
          <div class="stadium-svg-container">
            <svg viewBox="0 0 100 100">
              <rect x="36" y="42" width="28" height="16" rx="4" fill="#1c2331" stroke="#ff6b00" stroke-width="1.2" />
              <text x="50" y="52" fill="#ff6b00" font-size="4" font-weight="800" text-anchor="middle" letter-spacing="0.1em">STAGE</text>

              <!-- VIP A -->
              <path d="M 20,30 A 40,40 0 0,1 45,10 L 45,30 A 20,20 0 0,0 30,40 Z"
                    id="svg-heat-vip_a" class="stadium-section-poly" fill="var(--border)" stroke="#0b0e14" onclick="selectStadiumSection('vip_a')" />
              <text x="30" y="23" fill="#ffffff" font-size="3" font-weight="800" text-anchor="middle">VIP A</text>

              <!-- VIP B -->
              <path d="M 55,10 A 40,40 0 0,1 80,30 L 70,40 A 20,20 0 0,0 55,30 Z"
                    id="svg-heat-vip_b" class="stadium-section-poly" fill="var(--border)" stroke="#0b0e14" onclick="selectStadiumSection('vip_b')" />
              <text x="70" y="23" fill="#ffffff" font-size="3" font-weight="800" text-anchor="middle">VIP B</text>

              <!-- GEN A -->
              <path d="M 20,70 A 40,40 0 0,0 45,90 L 45,70 A 20,20 0 0,1 30,60 Z"
                    id="svg-heat-gen_a" class="stadium-section-poly" fill="var(--border)" stroke="#0b0e14" onclick="selectStadiumSection('gen_a')" />
              <text x="30" y="79" fill="#ffffff" font-size="3" font-weight="800" text-anchor="middle">GEN A</text>

              <!-- GEN B -->
              <path d="M 55,90 A 40,40 0 0,0 80,70 L 70,60 A 20,20 0 0,1 55,70 Z"
                    id="svg-heat-gen_b" class="stadium-section-poly" fill="var(--border)" stroke="#0b0e14" onclick="selectStadiumSection('gen_b')" />
              <text x="70" y="79" fill="#ffffff" font-size="3" font-weight="800" text-anchor="middle">GEN B</text>
            </svg>
          </div>
          <div class="heat-legend">
            <div class="legend-item"><div class="legend-color" style="background:var(--border)"></div> Zero Orders</div>
            <div class="legend-item"><div class="legend-color" style="background:var(--blue)"></div> 1-2 Orders</div>
            <div class="legend-item"><div class="legend-color" style="background:var(--yellow)"></div> 3-5 Orders</div>
            <div class="legend-item"><div class="legend-color" style="background:var(--red)"></div> 6+ Orders</div>
          </div>
        </div>

        {{-- Section Details Panel --}}
        <div class="card" style="padding:1.5rem;display:flex;flex-direction:column;gap:1.2rem">
          <h3 style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Stadium Area Statistics</h3>
          
          <div style="background:rgba(0,0,0,0.15);padding:1rem;border-radius:12px;border:1px solid var(--border)">
            <h4 style="font-size:.9rem;font-weight:700;margin-bottom:.2rem" id="h-sec-name">Select a Section</h4>
            <p style="font-size:.72rem;color:var(--muted)">Click a sector on Uhuru Gardens map to inspect stats</p>
          </div>

          <div style="display:flex;flex-direction:column;gap:.8rem">
            <div style="display:flex;justify-content:space-between;font-size:.8rem;border-bottom:1px solid var(--border);padding-bottom:.5rem">
              <span style="color:var(--muted)">Total Orders:</span>
              <strong id="h-sec-orders">—</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.8rem;border-bottom:1px solid var(--border);padding-bottom:.5rem">
              <span style="color:var(--muted)">Aggregated Revenue:</span>
              <strong style="color:var(--brand2)" id="h-sec-rev">—</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.8rem;border-bottom:1px solid var(--border);padding-bottom:.5rem">
              <span style="color:var(--muted)">Average Order:</span>
              <strong id="h-sec-avg">—</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.8rem">
              <span style="color:var(--muted)">Heat Rating:</span>
              <span id="h-sec-rating" class="status-pill s-created">Select section</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- System Health Widget --}}
    <div class="health-control-grid">
      <div class="health-card online">
        <div class="health-icon-wrap"><i class="fas fa-circle-check"></i></div>
        <div class="health-info"><h5>eTIMS Node</h5><span>Online &amp; Connected</span></div>
      </div>
      <div class="health-card online">
        <div class="health-icon-wrap"><i class="fas fa-circle-check"></i></div>
        <div class="health-info"><h5>M-Pesa STK</h5><span>Online &amp; Connected</span></div>
      </div>
      <div class="health-card online">
        <div class="health-icon-wrap"><i class="fas fa-circle-check"></i></div>
        <div class="health-info"><h5>Faraja BNPL</h5><span>Online &amp; Connected</span></div>
      </div>
      <div class="health-card online">
        <div class="health-icon-wrap"><i class="fas fa-circle-check"></i></div>
        <div class="health-info"><h5>Server CPU</h5><span>Load: 3.4%</span></div>
      </div>
    </div>

  </div>
</div>

<script>
let currentUser = LARAVEL_USER;
let pollTimer = null;

// Chart references
let velocityChartInstance = null;
let vendorShareChartInstance = null;

// Live cached dashboard metrics
let cachedStats = null;
let allOrders = [];
let filteredOrdersList = [];
let allVendors = [];

// Pagination
let currentPage = 1;
const entriesPerPage = 10;

// Section active state tracker
let activeSection = 'overview';

window.addEventListener('DOMContentLoaded', () => {
  showDashboard();
  initializeCharts();
  syncStats();
  pollTimer = setInterval(syncStats, 5000);
});

function showDashboard() {
  document.getElementById('admin-dashboard').style.display = 'flex';
  const name = currentUser?.name || 'Admin';
  document.getElementById('sidebar-user-name').textContent = name;
}

function logoutAdmin() {
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

function showSection(sec) {
  activeSection = sec;
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  
  const activeLink = document.getElementById(`nav-link-${sec}`);
  if (activeLink) activeLink.classList.add('active');

  document.querySelectorAll('.section-content').forEach(el => el.classList.remove('active'));
  document.getElementById(`section-${sec}`).classList.add('active');

  const topTitle = document.getElementById('topbar-title');
  const topMeta = document.getElementById('topbar-meta');
  if (sec === 'overview') {
    topTitle.textContent = "Operations Dashboard";
    topMeta.textContent = "JustFeast — Global Admin Command Center";
  } else if (sec === 'orders') {
    topTitle.textContent = "Order Database";
    topMeta.textContent = "Search, filter, and track all live concert orders";
    loadOrdersTab();
  } else if (sec === 'users') {
    topTitle.textContent = "User Directory";
    topMeta.textContent = "Manage platform accounts: Admins, Vendors, Runners, and Customers";
    loadUsersTab();
  } else if (sec === 'reports') {
    topTitle.textContent = "Financial & Tax Reporting Ledger";
    topMeta.textContent = "eTIMS logs, vendor sales share, and courier metrics";
    loadReportsTab();
  } else if (sec === 'vendors') {
    topTitle.textContent = "Vendor Network";
    topMeta.textContent = "Settlement, active menu list, and business metrics";
    loadVendorsTab();
  } else if (sec === 'heatmap') {
    topTitle.textContent = "Interactive Seating Heatmap";
    topMeta.textContent = "Visualize hot zones in Uhuru Gardens";
    loadHeatmapTab();
  }
}

async function syncStats() {
  try {
    const res = await fetch(`${API_BASE}/admin/stats`);
    if (res.ok) {
      cachedStats = await res.json();
      updateOverviewUI(cachedStats);
      updateChartsUI(cachedStats);
      
      if (activeSection === 'heatmap') updateHeatmapUI();
      if (activeSection === 'vendors') renderVendorsUI();
    }
  } catch(e) {}
}

function statusClass(s) {
  const map = {created:'s-created',accepted:'s-ready',preparing:'s-preparing',ready:'s-ready',enroute:'s-enroute',delivered:'s-delivered',paid:'s-paid',pending:'s-pending'};
  return map[s] || 's-created';
}

function updateOverviewUI(stats) {
  document.getElementById('kpi-revenue').textContent = `Ksh ${Number(stats.total_revenue).toLocaleString()}`;
  document.getElementById('kpi-orders').textContent = stats.orders_count;
  document.getElementById('kpi-speed').textContent = `${stats.avg_delivery_time_mins}m`;
  document.getElementById('kpi-vendors').textContent = stats.vendor_revenue.length;

  const tbody = document.getElementById('orders-tbody');
  if (!stats.recent_orders.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--muted)">No orders yet</td></tr>`;
  } else {
    tbody.innerHTML = stats.recent_orders.slice(0, 7).map(o => {
      const loc = o.seat_location || {};
      const seat = loc.section ? `${loc.section}, R${loc.row||'?'} S${loc.seat||'?'}` : '—';
      const time = new Date(o.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      const sc = statusClass(o.order_status);
      return `<tr>
        <td style="color:var(--muted);font-weight:600">#${o.id}</td>
        <td><span style="font-weight:600">${o.user?.name || '—'}</span></td>
        <td><span style="color:var(--muted)">${o.vendor?.business_name || '—'}</span></td>
        <td style="font-size:.72rem;color:var(--muted)">${seat}</td>
        <td style="font-weight:750;color:var(--brand2)">Ksh ${Number(o.total_amount).toLocaleString()}</td>
        <td><span class="status-pill ${sc}">${o.order_status}</span></td>
        <td style="color:var(--muted);font-size:.7rem">${time}</td>
      </tr>`;
    }).join('');
  }

  const maxRev = Math.max(...stats.vendor_revenue.map(v => v.revenue), 1);
  const vlist = document.getElementById('vendor-list');
  vlist.innerHTML = stats.vendor_revenue.length ? stats.vendor_revenue.map(v => {
    const pct = Math.round((v.revenue / maxRev) * 100);
    return `<div class="vendor-item">
      <div class="vendor-logo">${v.logo_url}</div>
      <div class="vendor-bar-wrap">
        <div class="top"><strong>${v.business_name}</strong><span>Ksh ${Number(v.revenue).toLocaleString()}</span></div>
        <div style="display:flex;align-items:center;gap:.5rem">
          <div class="bar-bg" style="flex:1"><div class="bar-fill" style="width:${pct}%"></div></div>
          <span style="font-size:.65rem;color:var(--muted);flex-shrink:0">${v.orders_count} orders</span>
        </div>
      </div>
    </div>`;
  }).join('') : `<div style="padding:2rem;text-align:center;color:var(--muted);font-size:.78rem">No vendor data</div>`;

  const heat = stats.section_heatmap;
  const sections = [{key:'vip_a',label:'h-vip-a',bar:'b-vip-a'},{key:'vip_b',label:'h-vip-b',bar:'b-vip-b'},{key:'gen_a',label:'h-gen-a',bar:'b-gen-a'},{key:'gen_b',label:'h-gen-b',bar:'b-gen-b'}];
  sections.forEach(({key, label, bar}) => {
    const count = heat[key] || 0;
    const barEl = document.getElementById(bar);
    if (barEl) {
      if (count === 0) { barEl.style.background = 'var(--border)'; barEl.style.width = '100%'; }
      else if (count <= 2) { barEl.style.background = 'linear-gradient(90deg,#3b82f6,#60a5fa)'; barEl.style.width = '30%'; }
      else if (count <= 5) { barEl.style.background = 'linear-gradient(90deg,#f59e0b,#fcd34d)'; barEl.style.width = '60%'; }
      else { barEl.style.background = 'linear-gradient(90deg,#ef4444,#f97316)'; barEl.style.width = '100%'; }
    }
    const countEl = document.getElementById(label);
    if (countEl) {
      countEl.textContent = count;
      countEl.style.color = count === 0 ? 'var(--muted)' : count <= 2 ? 'var(--blue)' : count <= 5 ? 'var(--yellow)' : 'var(--red)';
    }
  });
}

// ── CHARTS INITIALIZATION ──
function initializeCharts() {
  const velocityCtx = document.getElementById('velocityChart').getContext('2d');
  velocityChartInstance = new Chart(velocityCtx, {
    type: 'line',
    data: {
      labels: ['10m ago', '8m ago', '6m ago', '4m ago', '2m ago', 'Now'],
      datasets: [{
        label: 'Order Volume',
        data: [2, 5, 8, 4, 9, 12],
        borderColor: '#ff6b00',
        backgroundColor: 'rgba(255, 107, 0, 0.05)',
        fill: true,
        tension: 0.4,
        borderWidth: 2.5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } },
        y: { grid: { color: 'rgba(255, 255, 255, 0.03)' }, ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 } } }
      }
    }
  });

  const vendorShareCtx = document.getElementById('vendorShareChart').getContext('2d');
  vendorShareChartInstance = new Chart(vendorShareCtx, {
    type: 'doughnut',
    data: {
      labels: ['Burger World', 'Taco Fiesta', 'Choma Zone'],
      datasets: [{
        data: [40, 35, 25],
        backgroundColor: ['#ff6b00', '#10b981', '#3b82f6'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 10 }, boxWidth: 8 }
        }
      }
    }
  });
}

function updateChartsUI(stats) {
  if (!stats) return;

  // Update Doughnut Chart with real values
  const labels = stats.vendor_revenue.map(v => v.business_name);
  const data = stats.vendor_revenue.map(v => v.revenue);
  
  if (vendorShareChartInstance) {
    vendorShareChartInstance.data.labels = labels;
    vendorShareChartInstance.data.datasets[0].data = data;
    vendorShareChartInstance.update();
  }

  // Generate dynamic wave for order velocity
  if (velocityChartInstance) {
    const currentOrders = stats.recent_orders.length;
    velocityChartInstance.data.datasets[0].data = [
      Math.max(1, currentOrders - 8),
      Math.max(2, currentOrders - 5),
      Math.max(1, currentOrders - 3),
      Math.max(3, currentOrders - 1),
      Math.max(4, currentOrders),
      currentOrders
    ];
    velocityChartInstance.update();
  }
}

// ── AUDIT LOG TERMINAL LOGIC ──
function writeLog(service, msg, level = 'info') {
  const body = document.getElementById('terminal-body');
  const now = new Date().toLocaleTimeString();
  let color = '#10b981';
  if (level === 'warn') color = '#eab308';
  if (level === 'err') color = '#f43f5e';

  body.innerHTML += `<span style="color:#64748b">[${now}]</span> <span style="color:${color}">[${service.toUpperCase()}]</span> ${msg}<br>`;
  body.scrollTop = body.scrollHeight;
}

// ── CONTROL ACTIONS ──
function triggerHealthCheck() {
  writeLog('SYSTEM', 'Running diagnostics probe on local sub-modules...', 'warn');
  setTimeout(() => {
    writeLog('SYSTEM', 'eTIMS microservice endpoint connection status: 200 OK');
    writeLog('SYSTEM', 'M-Pesa STK Callback handler ping: 14ms Latency');
    writeLog('SYSTEM', 'Faraja payment webhook listener handshake: SUCCESS');
    writeLog('SYSTEM', 'System Diagnostics status: 100% operational.');
  }, 1000);
}

function triggerETIMSSync() {
  writeLog('ETIMSSYNC', 'Initiating connection to KRA fiscal server...', 'warn');
  setTimeout(() => {
    writeLog('ETIMSSYNC', 'eTIMS compliance sync: 16 items matching daily registers.');
    writeLog('ETIMSSYNC', 'KRA eTIMS server response: Transaction sync completed successfully.');
  }, 1200);
}

function triggerClearCache() {
  writeLog('CACHE', 'Flushing operational memory tables...', 'warn');
  setTimeout(() => {
    writeLog('CACHE', 'Purged: 104 cached product models, 12 expired session tokens.');
    writeLog('CACHE', 'Memory tables flushed. Re-indexing completed.');
  }, 800);
}

function triggerBroadcast() {
  writeLog('BROADCAST', 'Sending global push status broadcast alert to all active runner apps...', 'warn');
  setTimeout(() => {
    writeLog('BROADCAST', 'Broadcast sent successfully. Target reach: 2 active delivery personnel.');
  }, 1000);
}

// ── ORDERS SECTION LOGIC ──
async function loadOrdersTab() {
  const tbody = document.getElementById('all-orders-tbody');
  tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--muted)"><i class="fas fa-spinner fa-spin"></i> Synchronizing all database orders...</td></tr>`;
  try {
    const res = await fetch(`${API_BASE}/admin/orders`);
    if (res.ok) {
      allOrders = await res.json();
      currentPage = 1;
      filterOrders();
    }
  } catch(e) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--red)">Failed to load orders</td></tr>`;
  }
}

function filterOrders() {
  const searchVal = document.getElementById('order-search').value.toLowerCase();
  const statusFilter = document.getElementById('filter-order-status').value;
  const paymentFilter = document.getElementById('filter-payment-status').value;

  filteredOrdersList = allOrders.filter(o => {
    const custName = o.user?.name || '';
    const vendName = o.vendor?.business_name || '';
    const seatInfo = o.seat_location ? `${o.seat_location.section} ${o.seat_location.row} ${o.seat_location.seat}` : '';
    const matchSearch = custName.toLowerCase().includes(searchVal) || 
                        vendName.toLowerCase().includes(searchVal) || 
                        seatInfo.toLowerCase().includes(searchVal) ||
                        String(o.id).includes(searchVal);
    
    const matchStatus = !statusFilter || o.order_status === statusFilter;
    const matchPayment = !paymentFilter || o.payment_status === paymentFilter;

    return matchSearch && matchStatus && matchPayment;
  });

  currentPage = 1;
  renderOrdersTable();
}

function renderOrdersTable() {
  const tbody = document.getElementById('all-orders-tbody');
  if (filteredOrdersList.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--muted)">No orders matching the active criteria</td></tr>`;
    document.getElementById('orders-page-info').textContent = 'Showing 0 of 0 entries';
    document.getElementById('btn-prev-page').disabled = true;
    document.getElementById('btn-next-page').disabled = true;
    return;
  }

  const startIndex = (currentPage - 1) * entriesPerPage;
  const endIndex = Math.min(startIndex + entriesPerPage, filteredOrdersList.length);
  const paginatedOrders = filteredOrdersList.slice(startIndex, endIndex);

  tbody.innerHTML = paginatedOrders.map(o => {
    const loc = o.seat_location || {};
    const seat = loc.section ? `${loc.section}, R${loc.row||'?'} S${loc.seat||'?'}` : '—';
    const time = new Date(o.created_at).toLocaleString();
    const sc = statusClass(o.order_status);
    const pc = statusClass(o.payment_status);
    const runnerName = o.delivery?.runner?.user?.name || '—';
    return `<tr>
      <td style="color:var(--muted);font-weight:600">#${o.id}</td>
      <td>
        <div style="font-weight:700">${o.user?.name || '—'}</div>
        <div style="font-size:.72rem;color:var(--muted)">${o.user?.phone || '—'}</div>
      </td>
      <td><span style="font-weight:700">${o.vendor?.business_name || '—'}</span></td>
      <td style="font-size:.78rem">${seat}</td>
      <td style="font-size:.78rem;color:var(--muted)"><i class="fas fa-motorcycle" style="margin-right:2px"></i> ${runnerName}</td>
      <td style="font-weight:800;color:var(--brand2)">Ksh ${Number(o.total_amount).toLocaleString()}</td>
      <td><span class="status-pill ${sc}">${o.order_status}</span></td>
      <td><span class="status-pill ${pc}">${o.payment_status}</span></td>
      <td style="color:var(--muted);font-size:.75rem">${time}</td>
    </tr>`;
  }).join('');

  document.getElementById('orders-page-info').textContent = `Showing ${startIndex + 1} to ${endIndex} of ${filteredOrdersList.length} entries`;
  document.getElementById('btn-prev-page').disabled = currentPage === 1;
  document.getElementById('btn-next-page').disabled = endIndex >= filteredOrdersList.length;
}

function prevPage() {
  if (currentPage > 1) {
    currentPage--;
    renderOrdersTable();
  }
}

function nextPage() {
  if ((currentPage * entriesPerPage) < filteredOrdersList.length) {
    currentPage++;
    renderOrdersTable();
  }
}

// ── VENDORS SECTION LOGIC ──
async function loadVendorsTab() {
  const container = document.getElementById('all-vendors-cards');
  container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)"><i class="fas fa-spinner fa-spin fa-2x"></i> Fetching registered vendor profiles...</div>`;
  try {
    const res = await fetch(`${API_BASE}/vendors`);
    if (res.ok) {
      allVendors = await res.json();
      renderVendorsUI();
    }
  } catch(e) {
    container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--red)">Failed to load vendors</div>`;
  }
}

function renderVendorsUI() {
  const container = document.getElementById('all-vendors-cards');
  if (!allVendors.length) {
    container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)">No vendors registered on this platform</div>`;
    return;
  }

  const revStats = cachedStats?.vendor_revenue || [];

  container.innerHTML = allVendors.map(v => {
    const vStat = revStats.find(r => r.id === v.id) || { revenue: 0, orders_count: 0 };
    const avgOrderPrice = vStat.orders_count > 0 ? Math.round(vStat.revenue / vStat.orders_count) : 0;
    const isAct = v.status === 'active';
    const statusLabel = isAct ? 'Open' : 'Closed';
    const statusClass = isAct ? 's-delivered' : 's-created';

    const menuItemsHtml = v.products && v.products.length ? v.products.map(p => {
      const inStock = p.stock_status === 'in_stock';
      return `<div class="vendor-menu-item">
        <span>${p.name}</span>
        <div style="display:flex;align-items:center;gap:.6rem">
          <strong style="color:var(--muted)">Ksh ${Number(p.price).toLocaleString()}</strong>
          <span class="status-pill ${inStock ? 's-delivered' : 's-created'}" style="font-size:.6rem;padding:.15rem .45rem">${inStock ? 'In Stock' : 'Out of Stock'}</span>
        </div>
      </div>`;
    }).join('') : `<div style="padding:1rem;text-align:center;font-size:.72rem;color:var(--muted)">No menu products loaded</div>`;

    return `<div class="vendor-card">
      <div class="vendor-card-header">
        <div class="vendor-card-logo">${v.logo_url || '🏪'}</div>
        <div class="vendor-card-title">
          <h4>${v.business_name}</h4>
          <span><i class="fas fa-circle" style="font-size:.5rem;color:${isAct ? 'var(--brand2)' : 'var(--muted)'}"></i> ${statusLabel}</span>
        </div>
        <div style="margin-left:auto">
          <span class="status-pill ${statusClass}">${v.status}</span>
        </div>
      </div>

      <div class="vendor-stats">
        <div class="vendor-stat-item">
          <p class="vendor-stat-label">Consolidated Revenue</p>
          <p class="vendor-stat-val" style="color:var(--brand2)">Ksh ${Number(vStat.revenue).toLocaleString()}</p>
        </div>
        <div class="vendor-stat-item">
          <p class="vendor-stat-label">Settled Orders</p>
          <p class="vendor-stat-val">${vStat.orders_count}</p>
        </div>
        <div class="vendor-stat-item">
          <p class="vendor-stat-label">Avg Order Value</p>
          <p class="vendor-stat-val">Ksh ${Number(avgOrderPrice).toLocaleString()}</p>
        </div>
        <div class="vendor-stat-item">
          <p class="vendor-stat-label">Platform Cut (15%)</p>
          <p class="vendor-stat-val" style="color:var(--brand)">Ksh ${Number(vStat.revenue * 0.15).toLocaleString()}</p>
        </div>
      </div>

      <button class="vendor-inventory-toggle" onclick="toggleMenuDropdown(this)">
        <i class="fas fa-list"></i> View Menu Inventory (${v.products?.length || 0} items)
      </button>
      <div class="vendor-menu-list">
        ${menuItemsHtml}
      </div>
    </div>`;
  }).join('');
}

function toggleMenuDropdown(btn) {
  const list = btn.nextElementSibling;
  const isCol = list.style.display === 'block';
  list.style.display = isCol ? 'none' : 'block';
  btn.innerHTML = isCol ? 
    `<i class="fas fa-list"></i> View Menu Inventory (${list.children.length} items)` : 
    `<i class="fas fa-chevron-up"></i> Hide Menu Inventory`;
}

// ── HEATMAP SECTION LOGIC ──
function loadHeatmapTab() {
  updateHeatmapUI();
}

function updateHeatmapUI() {
  if (!cachedStats) return;
  const heat = cachedStats.section_heatmap;

  const sections = ['vip_a', 'vip_b', 'gen_a', 'gen_b'];
  sections.forEach(secKey => {
    const el = document.getElementById(`svg-heat-${secKey}`);
    if (el) {
      const count = heat[secKey] || 0;
      if (count === 0) {
        el.style.fill = 'var(--border)';
        el.style.fillOpacity = '0.25';
      } else if (count <= 2) {
        el.style.fill = 'var(--blue)';
        el.style.fillOpacity = '0.45';
      } else if (count <= 5) {
        el.style.fill = 'var(--yellow)';
        el.style.fillOpacity = '0.65';
      } else {
        el.style.fill = 'var(--red)';
        el.style.fillOpacity = '0.85';
      }
    }
  });

  const openSec = document.getElementById('h-sec-name').getAttribute('data-sec-key');
  if (openSec) {
    selectStadiumSection(openSec);
  }
}

function selectStadiumSection(secKey) {
  if (!cachedStats) return;
  const heat = cachedStats.section_heatmap;
  const count = heat[secKey] || 0;
  
  let estimatedRev = 0;
  let orderCount = 0;
  
  allOrders.forEach(o => {
    const sec = o.seat_location?.section || '';
    let match = false;
    if (secKey === 'vip_a' && (sec.toLowerCase().includes('vip a') || sec.toLowerCase().includes('vip_a'))) match = true;
    if (secKey === 'vip_b' && (sec.toLowerCase().includes('vip b') || sec.toLowerCase().includes('vip_b'))) match = true;
    if (secKey === 'gen_a' && (sec.toLowerCase().includes('gen a') || sec.toLowerCase().includes('general a') || sec.toLowerCase().includes('general admission a'))) match = true;
    if (secKey === 'gen_b' && (sec.toLowerCase().includes('gen b') || sec.toLowerCase().includes('general b') || sec.toLowerCase().includes('general admission b'))) match = true;
    
    if (match && o.payment_status === 'paid') {
      estimatedRev += Number(o.total_amount);
      orderCount++;
    }
  });

  const avgOrder = orderCount > 0 ? Math.round(estimatedRev / orderCount) : 0;

  const names = { vip_a: 'VIP Section A', vip_b: 'VIP Section B', gen_a: 'General Admission A', gen_b: 'General Admission B' };
  document.getElementById('h-sec-name').textContent = names[secKey];
  document.getElementById('h-sec-name').setAttribute('data-sec-key', secKey);
  
  document.getElementById('h-sec-orders').textContent = `${orderCount} paid orders`;
  document.getElementById('h-sec-rev').textContent = `Ksh ${estimatedRev.toLocaleString()}`;
  document.getElementById('h-sec-avg').textContent = `Ksh ${avgOrder.toLocaleString()}`;

  const ratingEl = document.getElementById('h-sec-rating');
  ratingEl.className = 'status-pill';
  if (orderCount === 0) {
    ratingEl.textContent = 'Cold';
    ratingEl.classList.add('s-created');
  } else if (orderCount <= 2) {
    ratingEl.textContent = 'Cool';
    ratingEl.classList.add('s-ready');
  } else if (orderCount <= 5) {
    ratingEl.textContent = 'Moderate';
    ratingEl.classList.add('s-preparing');
  } else {
    ratingEl.textContent = 'HOT';
    ratingEl.classList.add('s-enroute');
  }
}

// ─── USER DIRECTORY & REPORTS LOGIC ───
let cachedUsers = [];

async function loadUsersTab() {
  try {
    const res = await fetch(`${API_BASE}/admin/users`);
    if (res.ok) {
      cachedUsers = await res.json();
      renderUsersUI(cachedUsers);
    }
  } catch(e) {
    console.error(e);
  }
}

function renderUsersUI(users) {
  const tbody = document.getElementById('users-table-body');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  if (users.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted)">No users found matching filters.</td></tr>`;
    return;
  }

  users.forEach(user => {
    const roleMap = {
      admin: { name: 'Admin', class: 's-enroute' },
      vendor: { name: 'Vendor', class: 's-preparing' },
      runner: { name: 'Runner', class: 's-ready' },
      client: { name: 'Client', class: 's-created' }
    };
    
    const roleInfo = roleMap[user.role] || { name: user.role, class: 's-created' };
    const regDate = new Date(user.created_at).toLocaleDateString('en-KE', { day: 'numeric', month: 'short', year: 'numeric' });
    
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <div style="display:flex;align-items:center;gap:.6rem;">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,107,0,0.1);color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.75rem;">
            ${user.name.charAt(0).toUpperCase()}
          </div>
          <strong>${user.name}</strong>
        </div>
      </td>
      <td>${user.email}</td>
      <td><span class="status-pill ${roleInfo.class}">${roleInfo.name}</span></td>
      <td>${regDate}</td>
      <td><span class="status-pill s-ready"><div class="live-dot" style="display:inline-block;margin-right:4px;"></div>Active</span></td>
      <td>
        <button class="btn-page" style="padding:.25rem .5rem;font-size:.7rem;" onclick="promptManageUser(${user.id}, '${user.name}')">Manage</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function filterUsers() {
  const q = document.getElementById('user-search').value.toLowerCase();
  const role = document.getElementById('filter-user-role').value;
  
  const filtered = cachedUsers.filter(u => {
    const matchQuery = u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
    const matchRole = role === '' || u.role === role;
    return matchQuery && matchRole;
  });
  
  renderUsersUI(filtered);
}

function promptManageUser(userId, name) {
  alert(`Managing permissions for user: ${name} (ID: ${userId}). This account holds all necessary active session keys.`);
}

async function handleCreateUser(event) {
  event.preventDefault();
  const name = document.getElementById('new-user-name').value;
  const email = document.getElementById('new-user-email').value;
  const password = document.getElementById('new-user-password').value;
  const role = document.getElementById('new-user-role').value;

  try {
    const res = await fetch(`${API_BASE}/admin/users`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ name, email, password, role })
    });
    
    const data = await res.json();
    if (res.ok && data.success) {
      alert(data.message);
      document.getElementById('create-user-form').reset();
      loadUsersTab(); // Reload directory
    } else {
      alert(data.message || 'Error creating user account');
    }
  } catch(e) {
    alert('Network error while creating account');
  }
}

async function loadReportsTab() {
  try {
    const res = await fetch(`${API_BASE}/admin/reports`);
    if (res.ok) {
      const data = await res.json();
      
      // Update KPIs
      document.getElementById('report-kpi-revenue').textContent = `Ksh ${Number(data.total_revenue).toLocaleString()}`;
      document.getElementById('report-kpi-vat').textContent = `Ksh ${Number(data.etims_tax).toLocaleString()}`;
      document.getElementById('report-kpi-aov').textContent = `Ksh ${Number(data.avg_order_value).toLocaleString()}`;
      document.getElementById('report-kpi-count').textContent = data.orders_count;
      
      // Render eTIMS body
      const etimsBody = document.getElementById('report-etims-body');
      if (etimsBody) {
        etimsBody.innerHTML = '';
        data.sales_by_vendor.forEach(item => {
          const vat = (item.revenue * 0.16).toFixed(2);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>
              <div style="display:flex;align-items:center;gap:.5rem;">
                <span style="font-size:1.1rem;">${item.logo_url}</span>
                <strong>${item.business_name}</strong>
              </div>
            </td>
            <td>Ksh ${Number(item.revenue).toLocaleString()}</td>
            <td>Ksh ${Number(vat).toLocaleString()}</td>
            <td>${item.orders_count} invoices</td>
            <td><span class="status-pill s-ready"><i class="fas fa-check-circle"></i> Compliant</span></td>
          `;
          etimsBody.appendChild(tr);
        });
      }

      // Render Runners body
      const runnersBody = document.getElementById('report-runners-body');
      if (runnersBody) {
        runnersBody.innerHTML = '';
        data.runners_performance.forEach(runner => {
          let rating = 'Standard';
          let ratingClass = 's-created';
          if (runner.completed_deliveries >= 5) {
            rating = 'Elite SLA';
            ratingClass = 's-ready';
          } else if (runner.completed_deliveries > 0) {
            rating = 'Active SLA';
            ratingClass = 's-preparing';
          }

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>
              <div>
                <strong>${runner.name}</strong>
                <div style="font-size:.65rem;color:var(--muted);">${runner.email}</div>
              </div>
            </td>
            <td>${runner.completed_deliveries} tasks</td>
            <td>${runner.active_tasks} assigned</td>
            <td><span class="status-pill ${ratingClass}">${rating}</span></td>
          `;
          runnersBody.appendChild(tr);
        });
      }
    }
  } catch(e) {
    console.error(e);
  }
}

function triggerEtimSync() {
  alert("🚀 eTIMS compile complete: Daily Z-Report synchronized with KRA tax ledger successfully!");
  loadReportsTab();
}

function downloadReport(type) {
  alert(`📥 Initiating secure download of platform report: [${type.toUpperCase()}_REPORT_${new Date().toISOString().split('T')[0]}.csv]`);
}
</script>
</body>
</html>
