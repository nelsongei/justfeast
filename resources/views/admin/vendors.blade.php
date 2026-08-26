@extends('admin.layout')

@section('title', 'Vendor Network — JustFeast Admin')
@section('page-title', 'Vendor Network')
@section('page-meta', 'Settlement, active menu list, and business metrics')

@section('content')
  <div class="vendor-grid" id="all-vendors-cards">
    <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
  </div>
@endsection

@section('scripts')
<script>
let allVendors = [];

window.addEventListener('DOMContentLoaded', () => {
  loadVendorsTab();
});

async function loadVendorsTab() {
  try {
    const res = await fetch(`${API_BASE}/admin/vendors`);
    if (res.ok) {
      allVendors = await res.json();
      renderVendorsUI(allVendors);
    }
  } catch(e) {}
}

function renderVendorsUI(vendors) {
  const container = document.getElementById('all-vendors-cards');
  if (!container) return;
  container.innerHTML = '';

  if (!vendors.length) {
    container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)">No registered food stalls found</div>`;
    return;
  }

  vendors.forEach(v => {
    const card = document.createElement('div');
    card.className = 'vendor-card';
    
    card.innerHTML = `
      <div class="vendor-card-header">
        <div class="vendor-card-logo">${v.logo_url}</div>
        <div class="vendor-card-title">
          <h4>${v.business_name}</h4>
          <span><i class="fas fa-envelope text-xs"></i> ${v.user?.email || '—'}</span>
        </div>
      </div>
      <div class="vendor-stats">
        <div class="vendor-stat-item">
          <div class="vendor-stat-label">Total Revenue</div>
          <div class="vendor-stat-val" style="color:var(--brand2)">Ksh ${Number(v.total_revenue).toLocaleString()}</div>
        </div>
        <div class="vendor-stat-item">
          <div class="vendor-stat-label">Total Orders</div>
          <div class="vendor-stat-val">${v.orders_count}</div>
        </div>
      </div>
      <button type="button" class="vendor-inventory-toggle" onclick="toggleVendorMenu(${v.id})">
        <span><i class="fas fa-utensils mr-1"></i> View Menu Items (${v.products?.length || 0})</span>
        <i class="fas fa-chevron-down" id="v-arrow-${v.id}"></i>
      </button>
      <div class="vendor-menu-list" id="v-menu-${v.id}">
        ${(v.products || []).map(p => `
          <div class="vendor-menu-item">
            <div>
              <strong>${p.name}</strong>
              <div style="font-size:.68rem;color:var(--muted);">${p.description || 'No description'}</div>
            </div>
            <strong style="color:var(--brand)">Ksh ${Number(p.price).toLocaleString()}</strong>
          </div>
        `).join('')}
      </div>
    `;
    container.appendChild(card);
  });
}

function toggleVendorMenu(vId) {
  const menu = document.getElementById(`v-menu-${vId}`);
  const arrow = document.getElementById(`v-arrow-${vId}`);
  if (menu.style.display === 'block') {
    menu.style.display = 'none';
    arrow.className = 'fas fa-chevron-down';
  } else {
    menu.style.display = 'block';
    arrow.className = 'fas fa-chevron-up';
  }
}
</script>
@endsection
