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
<title>@yield('title', 'JustFeast Admin — Control Center')</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const API_BASE = "{{ url('/api') }}";
  const LARAVEL_USER = @json($userData);
</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#FFFDF9;--surface:#FFFFFF;--surface2:#F8FAFC;--border:#E2E8F0;
  --brand:#A31D1D;--brand-glow:rgba(163,29,29,0.12);--brand2:#05A357;--text:#0F172A;--muted:#64748B;
  --red:#EF4444;--yellow:#B45309;--blue:#2563EB;--purple:#7C3AED;
}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden}

/* ── Sidebar ── */
.sidebar{width:260px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:1.75rem 1.25rem;gap:.3rem;position:fixed;top:0;left:0;height:100vh;z-index:40;box-shadow:2px 0 12px rgba(15,23,42,0.03)}
.sidebar-logo{display:flex;align-items:center;gap:.75rem;padding:.5rem .6rem;margin-bottom:2rem}
.sidebar-logo .icon{width:40px;height:40px;background:#FFF;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff;border:1px solid var(--border);box-shadow:0 4px 12px rgba(163,29,29,0.1)}
.sidebar-logo span{font-weight:900;font-size:1.25rem;letter-spacing:-0.03em;color:var(--text)}
.sidebar-logo span em{color:#FFC244;font-style:normal}
.nav-item{display:flex;align-items:center;gap:.8rem;padding:.75rem 1rem;border-radius:12px;font-size:.875rem;font-weight:600;color:var(--muted);cursor:pointer;transition:.15s;text-decoration:none;margin-bottom:4px}
.nav-item:hover{background:#F8FAFC;color:var(--text)}
.nav-item.active{background:#FFF8E7;color:var(--brand);box-shadow:inset 3px 0 0 var(--brand)}
.nav-item i{width:20px;text-align:center;font-size:1rem}
.nav-section{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:var(--muted);padding:1.2rem 1rem .4rem;margin-top:.5rem}
.sidebar-footer{margin-top:auto;padding-top:1.25rem;border-top:1px solid var(--border)}
.user-chip{display:flex;align-items:center;gap:.75rem;padding:.5rem .6rem;border-radius:10px;background:#F8FAFC}
.avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--brand),#841313);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0}
.user-chip .info p{font-size:.8rem;font-weight:700;color:var(--text)}
.user-chip .info span{font-size:.65rem;color:var(--muted)}
.btn-logout{display:flex;align-items:center;gap:.5rem;padding:.5rem .8rem;border-radius:9px;font-size:.75rem;font-weight:600;color:var(--muted);cursor:pointer;background:none;border:none;width:100%;margin-top:.5rem;transition:.15s}
.btn-logout:hover{background:rgba(239,68,68,.08);color:var(--red)}

/* ── Main Layout ── */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;background:var(--bg)}
.topbar{padding:1.25rem 2.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--surface);position:sticky;top:0;z-index:30;box-shadow:0 2px 12px rgba(15,23,42,0.02)}
.topbar h1{font-size:1.25rem;font-weight:900;letter-spacing:-0.02em;color:var(--text)}
.topbar .meta{font-size:.75rem;color:var(--muted);margin-top:3px}
.live-badge{display:flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:800;color:var(--brand2);background:rgba(5,163,87,.08);border:1px solid rgba(5,163,87,.2);padding:.35rem .8rem;border-radius:20px}
.live-dot{width:6px;height:6px;border-radius:50%;background:var(--brand2);animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

.content{padding:2.5rem;flex:1}

/* ── KPI Grid ── */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:2rem}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem;position:relative;overflow:hidden;transition:.2s;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.kpi:hover{border-color:rgba(163,29,29,.25);transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,0.06)}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:4px}
.kpi.orange::before{background:linear-gradient(90deg,var(--brand),#FFC244)}
.kpi.green::before{background:linear-gradient(90deg,var(--brand2),#34d399)}
.kpi.blue::before{background:linear-gradient(90deg,var(--blue),#60a5fa)}
.kpi.yellow::before{background:linear-gradient(90deg,#D97706,#fcd34d)}
.kpi-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:1rem}
.kpi.orange .kpi-icon{background:#FFF8E7;color:var(--brand)}
.kpi.green .kpi-icon{background:#ECFDF5;color:var(--brand2)}
.kpi.blue .kpi-icon{background:#EFF6FF;color:var(--blue)}
.kpi.yellow .kpi-icon{background:#FFF8E7;color:#D97706}
.kpi-label{font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.2rem}
.kpi-value{font-size:2rem;font-weight:900;letter-spacing:-.03em;color:var(--text)}
.kpi-sub{font-size:.75rem;color:var(--muted);margin-top:.4rem;display:flex;align-items:center;gap:.3rem;font-weight:600}
.trend-up{color:var(--brand2);font-weight:700}
.trend-down{color:var(--red);font-weight:700}

/* Charts grid */
.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
.chart-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.5rem;min-height:300px;display:flex;flex-direction:column;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.chart-header h4{font-size:.85rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)}
.chart-container{flex:1;position:relative}

/* ── Content Grid ── */
.dash-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.card-header{padding:1.4rem 1.8rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#FFFFFF}
.card-header h3{font-size:.875rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);display:flex;align-items:center;gap:.6rem}
.card-header .header-action{font-size:.75rem;font-weight:800;color:var(--brand);cursor:pointer;text-decoration:none;display:flex;align-items:center;gap:.3rem}
.card-header .header-action:hover{color:#841313}

/* Tables styling */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.85rem}
thead th{padding:1rem 1.5rem;text-align:left;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);background:#F8FAFC;border-bottom:1px solid var(--border)}
tbody tr{border-bottom:1px solid var(--border);transition:.15s}
tbody tr:hover{background:#FFFDF9}
tbody td{padding:1.1rem 1.5rem;vertical-align:middle;color:var(--text)}

/* Status Pills */
.status-pill{display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:.3rem .75rem;border-radius:20px}
.s-created{background:#F1F5F9;color:#64748B}
.s-preparing{background:#FFF8E7;color:#B45309;border:1px solid #F7E5B2}
.s-ready{background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE}
.s-enroute{background:#FFF8E7;color:#A31D1D;border:1px solid #FCA5A5}
.s-delivered{background:#ECFDF5;color:#047857;border:1px solid #A7F3D0}
.s-paid{background:#ECFDF5;color:#047857;border:1px solid #A7F3D0}
.s-pending{background:#FFF8E7;color:#B45309;border:1px solid #F7E5B2}

/* System Terminal Widget */
.terminal-card{background:#0F172A;border:1px solid #1E293B;border-radius:20px;padding:1.25rem;font-family:'Courier New',Courier,monospace;margin-top:1.5rem;box-shadow:0 8px 24px rgba(15,23,42,0.1)}
.terminal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;padding-bottom:.5rem;border-bottom:1px solid rgba(255,255,255,0.1)}
.terminal-title{font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;color:#FFC244;font-weight:bold}
.terminal-body{height:140px;overflow-y:auto;font-size:.75rem;line-height:1.4rem;color:#34D399}

/* Filters Bar */
.filters-bar{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;gap:1.25rem;align-items:center;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.search-input-wrap{position:relative;flex:1;min-width:320px}
.search-input-wrap i{position:absolute;left:1.2rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.9rem}
.search-input{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:.8rem 1rem .8rem 2.8rem;color:var(--text);font-size:.875rem;outline:none;transition:.15s;font-weight:600}
.search-input:focus{border-color:var(--brand);background:#FFFFFF}
.select-filter{background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:.8rem 1.4rem;color:var(--text);font-size:.85rem;outline:none;cursor:pointer;font-weight:600}
.select-filter:focus{border-color:var(--brand)}
.select-filter option{background:#FFFFFF;color:var(--text)}

/* System Health Controls */
.health-control-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-top:1.5rem}
.health-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1.2rem;display:flex;align-items:center;gap:.9rem;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.health-icon-wrap{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem}
.health-card.online .health-icon-wrap{background:#ECFDF5;color:var(--brand2)}
.health-card.warning .health-icon-wrap{background:#FFF8E7;color:#D97706}
.health-info h5{font-size:.78rem;font-weight:800;color:var(--text)}
.health-info span{font-size:.65rem;color:var(--muted);font-weight:600}

/* Vendors Tab Content */
.vendor-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1.5rem}
.vendor-card{background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:.2s;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.vendor-card:hover{border-color:rgba(163,29,29,.25);transform:translateY(-1px)}
.vendor-card-header{padding:1.5rem;display:flex;align-items:center;gap:1rem;border-bottom:1px solid var(--border)}
.vendor-card-logo{width:48px;height:48px;border-radius:12px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;border:1px solid var(--border)}
.vendor-card-title h4{font-size:1rem;font-weight:800;color:var(--text)}
.vendor-card-title span{font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:.35rem;font-weight:600}
.vendor-stats{display:grid;grid-template-columns:1fr 1fr;background:var(--surface2)}
.vendor-stat-item{padding:1.1rem 1.5rem;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
.vendor-stat-item:nth-child(2n){border-right:none}
.vendor-stat-label{font-size:.65rem;font-weight:800;color:var(--muted);text-transform:uppercase;margin-bottom:.2rem;letter-spacing:.05em}
.vendor-stat-val{font-size:1.15rem;font-weight:900;color:var(--text)}
.vendor-inventory-toggle{width:100%;background:none;border:none;color:var(--muted);font-size:.78rem;font-weight:800;padding:.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:.15s}
.vendor-inventory-toggle:hover{background:#F8FAFC;color:var(--text)}
.vendor-menu-list{border-top:1px solid var(--border);display:none;background:#F8FAFC;padding:.5rem 0}
.vendor-menu-item{display:flex;justify-content:space-between;align-items:center;padding:.7rem 1.5rem;font-size:.78rem;color:var(--text)}
.vendor-menu-item:not(:last-child){border-bottom:1px solid var(--border)}

/* Heatmap Tab Content */
.heatmap-container{display:grid;grid-template-columns:1fr 420px;gap:1.5rem}
.stadium-wrap{background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:2.5rem;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;box-shadow:0 4px 16px rgba(15,23,42,0.03)}
.stadium-svg-container{width:100%;max-width:440px;margin:2rem 0;position:relative}
.stadium-svg-container svg{width:100%;height:auto}
.stadium-section-poly{transition:.3s;cursor:pointer;fill-opacity:0.35;stroke-width:1.5;stroke-linejoin:round}
.stadium-section-poly:hover{fill-opacity:0.6;stroke-width:2.5}
.heat-legend{display:flex;gap:1.5rem;font-size:.72rem;font-weight:800;color:var(--muted)}
.legend-item{display:flex;align-items:center;gap:.4rem}
.legend-color{width:12px;height:12px;border-radius:4px}

/* Page Control Styling */
.pagination-controls{display:flex;justify-content:space-between;align-items:center;padding:1.1rem 1.8rem;border-top:1px solid var(--border);font-size:.8rem;color:var(--muted);background:#FFFFFF}
.btn-page{background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:.5rem 1rem;border-radius:10px;cursor:pointer;font-size:.78rem;font-weight:700;transition:.15s}
.btn-page:hover:not(:disabled){background:#FFF8E7;border-color:var(--brand)}
.btn-page:disabled{opacity:0.4;cursor:not-allowed}

/* Sidebar Vendor List Item */
.vendor-item{display:flex;align-items:center;gap:.8rem;padding:1.1rem 1.5rem;border-bottom:1px solid var(--border)}
.vendor-item:last-child{border-bottom:none}
.vendor-logo{width:38px;height:38px;border-radius:10px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;border:1px solid var(--border)}
.vendor-bar-wrap{flex:1;min-width:0}
.vendor-bar-wrap .top{display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.4rem;color:var(--text)}
.vendor-bar-wrap .top strong{font-weight:800}
.vendor-bar-wrap .top span{color:var(--brand2);font-weight:800}
.bar-bg{height:6px;background:#E2E8F0;border-radius:10px;overflow:hidden}
.bar-fill{height:100%;background:linear-gradient(90deg,var(--brand),#FFC244);border-radius:10px}

/* Quick Tools */
.quick-tools-grid{display:grid;grid-template-columns:repeat(4, 1fr);gap:1.25rem;margin-top:1.5rem}
.tool-btn{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1rem;color:var(--text);font-size:.8rem;font-weight:800;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;cursor:pointer;transition:.15s;box-shadow:0 2px 8px rgba(15,23,42,0.03)}
.tool-btn:hover{background:#FFF8E7;border-color:var(--brand);box-shadow:0 6px 20px rgba(163,29,29,0.1);transform:translateY(-1px)}
.tool-btn i{font-size:1.2rem;color:var(--brand)}

/* ── Global Modal Overlay & Dialog Styling ── */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(8px);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  opacity: 0;
  visibility: hidden;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-overlay.is-active {
  opacity: 1;
  visibility: visible;
}
.modal-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 28px;
  width: 100%;
  max-width: 640px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  transform: scale(0.95) translateY(10px);
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-overlay.is-active .modal-card {
  transform: scale(1) translateY(0);
}
.modal-header {
  padding: 1.5rem 1.75rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #FFFFFF;
}
.modal-close-btn {
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--muted);
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.15s ease;
}
.modal-close-btn:hover {
  background: #FEF2F2;
  color: #991B1B;
  border-color: #FCA5A5;
}

/* ── Toast Container & Global Notifications ── */
.toast-container {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  z-index: 99999;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  pointer-events: none;
  max-width: 420px;
  width: calc(100% - 3rem);
}
.toast {
  pointer-events: auto;
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.9rem 1.25rem;
  background: var(--surface);
  color: var(--text);
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.15);
  font-size: 0.84rem;
  font-weight: 700;
  transform: translateY(0);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes toastSlideIn {
  from { opacity: 0; transform: translateY(-15px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
.toast-icon {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  flex-shrink: 0;
}
.toast-success { border-color: rgba(5, 163, 87, 0.3); }
.toast-success .toast-icon { background: #ECFDF5; color: #05A357; }
.toast-error { border-color: rgba(239, 68, 68, 0.3); }
.toast-error .toast-icon { background: #FEF2F2; color: #EF4444; }
.toast-warning { border-color: rgba(217, 119, 6, 0.3); }
.toast-warning .toast-icon { background: #FFF8E7; color: #D97706; }
.toast-info { border-color: rgba(37, 99, 235, 0.3); }
.toast-info .toast-icon { background: #EFF6FF; color: #2563EB; }
</style>
</head>
<body>

<div id="toast-container" class="toast-container"></div>

{{-- ── Sidebar Navigation ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" style="height: 44px; width: auto; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); border: 1px solid rgba(0,0,0,0.08);">
  </div>

  <span class="nav-section">Global Monitor</span>
  <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') || request()->is('admin') ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
  <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') || request()->is('admin/orders') ? 'active' : '' }}"><i class="fas fa-receipt"></i> All Orders</a>
  <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') || request()->is('admin/users') ? 'active' : '' }}"><i class="fas fa-users-cog"></i> User Accounts</a>

  <span class="nav-section">Financial & Operations</span>
  <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') || request()->is('admin/reports') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> System Reports</a>
  <a href="{{ route('admin.vendors') }}" class="nav-item {{ request()->routeIs('admin.vendors') || request()->is('admin/vendors') ? 'active' : '' }}"><i class="fas fa-store"></i> Vendors</a>
  <a href="{{ route('admin.heatmap') }}" class="nav-item {{ request()->routeIs('admin.heatmap') || request()->is('admin/heatmap') ? 'active' : '' }}"><i class="fas fa-fire"></i> Heatmap</a>

  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="avatar">S</div>
      <div class="info">
        <p id="sidebar-user-name">{{ Auth::user()->name ?? 'Admin' }}</p>
        <span>Administrator</span>
      </div>
    </div>
    <button class="btn-logout" onclick="logoutAdmin()"><i class="fas fa-sign-out-alt"></i> Sign out</button>
  </div>
</aside>

{{-- ── Main Layout View Wrapper ── --}}
<div class="main">
  <div class="topbar">
    <div>
      <h1 id="topbar-title">@yield('page-title', 'Operations Dashboard')</h1>
      <p class="meta" id="topbar-meta">@yield('page-meta', 'JustFeast — Global Admin Command Center')</p>
    </div>
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <!-- Delivery Fee Quick Settings Button -->
      <button type="button" onclick="openDeliveryFeeModal()" 
              style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem;font-weight:800;color:var(--brand);background:#FFF8E7;border:1px solid #F7E5B2;padding:0.4rem 0.9rem;border-radius:20px;cursor:pointer;transition:all 0.15s ease;"
              title="Click to adjust seat delivery fee">
        <i class="fas fa-motorcycle" style="color:var(--brand);"></i>
        <span>Delivery Fee: <strong id="admin-delivery-fee-badge">Ksh 30</strong></span>
        <i class="fas fa-edit text-xs" style="opacity:0.6;"></i>
      </button>

      <div class="live-badge"><div class="live-dot"></div> Live sync</div>
    </div>
  </div>

  <div class="content">
    @yield('content')
  </div>
</div>

<!-- Delivery Fee Settings Modal -->
<div class="modal-overlay" id="delivery-fee-modal-overlay" onclick="if(event.target===this) closeDeliveryFeeModal()">
  <div class="modal-card" style="max-width:440px;">
    <div class="modal-header">
      <h3 style="font-size:1.1rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
        <i class="fas fa-motorcycle" style="color:var(--brand)"></i>
        Adjust Seat Delivery Fee
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeDeliveryFeeModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="delivery-fee-form" onsubmit="handleSaveDeliveryFee(event)">
      <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">
        <div>
          <label style="display:block;font-size:0.78rem;font-weight:800;color:var(--text);margin-bottom:0.4rem;">
            Seat Delivery Fee (Ksh) *
          </label>
          <div style="position:relative;">
            <span style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);font-weight:800;color:var(--brand);">Ksh</span>
            <input type="number" id="setting-delivery-fee-input" required min="0" step="1" 
                   style="width:100%;padding:0.75rem 1rem 0.75rem 3.2rem;background:var(--surface2);border:1px solid var(--border);border-radius:14px;font-weight:900;font-size:1.1rem;color:var(--text);outline:none;">
          </div>
          <p style="font-size:0.72rem;color:var(--muted);margin-top:0.4rem;font-weight:600;">
            This fee is automatically added to customer orders during seat delivery checkout. Default: 30 Ksh.
          </p>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border);">
          <button type="button" class="btn-page" onclick="closeDeliveryFeeModal()">Cancel</button>
          <button type="submit" id="btn-save-fee" 
                  style="padding:0.65rem 1.4rem;background:#05A357;color:#FFF;border:none;border-radius:12px;font-weight:800;font-size:0.82rem;cursor:pointer;display:flex;align-items:center;gap:0.4rem;">
            <i class="fas fa-save"></i> Save Delivery Fee
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
  loadAdminDeliveryFee();
});

async function loadAdminDeliveryFee() {
  try {
    const res = await fetch(`${API_BASE}/admin/settings`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (res.ok) {
      const data = await res.json();
      if (data.settings && data.settings.delivery_fee !== undefined) {
        const fee = Number(data.settings.delivery_fee);
        const badge = document.getElementById('admin-delivery-fee-badge');
        const input = document.getElementById('setting-delivery-fee-input');
        if (badge) badge.textContent = `Ksh ${fee}`;
        if (input) input.value = fee;
      }
    }
  } catch (e) {
    console.error('Error loading delivery fee setting:', e);
  }
}

function openDeliveryFeeModal() {
  const modal = document.getElementById('delivery-fee-modal-overlay');
  if (modal) modal.classList.add('is-active');
}

function closeDeliveryFeeModal() {
  const modal = document.getElementById('delivery-fee-modal-overlay');
  if (modal) modal.classList.remove('is-active');
}

async function handleSaveDeliveryFee(event) {
  event.preventDefault();
  const input = document.getElementById('setting-delivery-fee-input');
  const btn = document.getElementById('btn-save-fee');
  if (!input || !btn) return;

  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Saving...`;

  try {
    const res = await fetch(`${API_BASE}/admin/settings`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ delivery_fee: Number(input.value) })
    });

    const data = await res.json();

    if (res.ok && (data.status === 'success' || data.success)) {
      const newFee = Number(data.settings.delivery_fee);
      const badge = document.getElementById('admin-delivery-fee-badge');
      if (badge) badge.textContent = `Ksh ${newFee}`;
      closeDeliveryFeeModal();
      showNotification(`System delivery fee updated to Ksh ${newFee}!`, 'success');
    } else {
      showNotification(data.message || 'Failed to update delivery fee.', 'error');
    }
  } catch (e) {
    showNotification('Network error while saving delivery fee', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

window.showNotification = function(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  
  let iconClass = 'fa-check';
  if (type === 'error') iconClass = 'fa-exclamation-triangle';
  else if (type === 'warning') iconClass = 'fa-exclamation-circle';
  else if (type === 'info') iconClass = 'fa-info-circle';

  toast.innerHTML = `
    <div class="toast-icon">
      <i class="fas ${iconClass}"></i>
    </div>
    <span style="flex:1;word-break:break-word;">${escapeHtmlGlobal(message)}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    toast.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
};

function escapeHtmlGlobal(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
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
</script>
@yield('scripts')
</body>
</html>
