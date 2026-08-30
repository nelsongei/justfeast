@extends('admin.layout')

@section('title', 'User Directory — JustFeast Admin')
@section('page-title', 'User Directory')
@section('page-meta', 'Manage platform accounts: Customers, Vendors, Runners, and Administrators')

@section('content')
  {{-- KPI Summary Row --}}
  <div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
    <div class="kpi green">
      <div class="kpi-icon"><i class="fas fa-users"></i></div>
      <div class="kpi-label">Total Accounts</div>
      <div class="kpi-value" id="user-kpi-total">0</div>
      <div class="kpi-sub"><span class="trend-up"><i class="fas fa-user-check"></i> Platform Directory</span></div>
    </div>
    <div class="kpi blue">
      <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
      <div class="kpi-label">Registered Customers</div>
      <div class="kpi-value" id="user-kpi-customers">0</div>
      <div class="kpi-sub"><span class="trend-up"><i class="fas fa-mobile-screen-button"></i> App Buyers</span></div>
    </div>
    <div class="kpi yellow">
      <div class="kpi-icon"><i class="fas fa-store"></i></div>
      <div class="kpi-label">Vendor Outlets</div>
      <div class="kpi-value" id="user-kpi-vendors">0</div>
      <div class="kpi-sub"><span class="trend-up"><i class="fas fa-utensils"></i> Stall Managers</span></div>
    </div>
    <div class="kpi orange">
      <div class="kpi-icon"><i class="fas fa-motorcycle"></i></div>
      <div class="kpi-label">Couriers & Runners</div>
      <div class="kpi-value" id="user-kpi-runners">0</div>
      <div class="kpi-sub"><span class="trend-up"><i class="fas fa-person-walking-luggage"></i> Field Dispatchers</span></div>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Left: Users table -->
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-users" style="color:var(--brand)"></i> Platform User Directory</h3>
        <div class="filters-bar" style="border-bottom: none; padding: 0; margin: 0; gap: 0.5rem; box-shadow: none;">
          <div class="search-input-wrap" style="width: 220px;">
            <i class="fas fa-search"></i>
            <input type="text" id="user-search" class="search-input" placeholder="Search name, phone..." oninput="filterUsers()">
          </div>
          <select id="filter-user-role" class="select-filter" style="width: 150px;" onchange="filterUsers()">
            <option value="">All Roles</option>
            <option value="customer">Customers (Clients)</option>
            <option value="vendor">Vendors</option>
            <option value="runner">Runners</option>
            <option value="admin">Admins</option>
          </select>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>User Name</th>
              <th>Email & Contact Phone</th>
              <th>Account Role</th>
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
      <h3 style="font-size: .875rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 1.25rem;">
        <i class="fas fa-user-plus" style="color: var(--brand);"></i> Create New Account
      </h3>
      
      <form id="create-user-form" onsubmit="handleCreateUser(event)" style="display: flex; flex-direction: column; gap: 1rem;">
        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Full Name *</label>
          <input type="text" id="new-user-name" required placeholder="Jane Doe" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Email Address *</label>
          <input type="email" id="new-user-email" required placeholder="jane@justfeast.co.ke" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Phone Number</label>
          <input type="tel" id="new-user-phone" placeholder="0712345678" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Password *</label>
          <input type="password" id="new-user-password" required placeholder="••••••••" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Assign Role *</label>
          <select id="new-user-role" required onchange="toggleVendorBizField()" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
            <option value="customer">Customer (Client)</option>
            <option value="runner">Runner (Courier)</option>
            <option value="vendor">Vendor (Stall Staff)</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <div id="vendor-biz-name-wrap" style="display: none;">
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--brand); margin-bottom: .4rem; letter-spacing: .05em;">Stall / Business Name</label>
          <input type="text" id="new-user-biz-name" placeholder="Carnivore Smokehouse" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <button type="submit" style="background: linear-gradient(135deg, #A31D1D, #841313); color: #fff; padding: .75rem; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; transition: .15s; margin-top: .5rem; box-shadow: 0 4px 14px rgba(163,29,29,0.3);">
          Create User Account
        </button>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
let cachedUsers = [];

window.addEventListener('DOMContentLoaded', () => {
  loadUsersTab();
});

async function loadUsersTab() {
  try {
    const res = await fetch(`${API_BASE}/admin/users`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (res.ok) {
      const data = await res.json();
      cachedUsers = Array.isArray(data) ? data : (data.data || []);
      updateUsersKPI(cachedUsers);
      renderUsersUI(cachedUsers);
    }
  } catch(e) {}
}

function updateUsersKPI(users) {
  let customers = 0, vendors = 0, runners = 0;
  users.forEach(u => {
    const r = u.role ? u.role.toLowerCase() : '';
    if (r === 'customer' || r === 'client') customers++;
    else if (r === 'vendor') vendors++;
    else if (r === 'runner') runners++;
  });

  if (document.getElementById('user-kpi-total')) document.getElementById('user-kpi-total').textContent = users.length;
  if (document.getElementById('user-kpi-customers')) document.getElementById('user-kpi-customers').textContent = customers;
  if (document.getElementById('user-kpi-vendors')) document.getElementById('user-kpi-vendors').textContent = vendors;
  if (document.getElementById('user-kpi-runners')) document.getElementById('user-kpi-runners').textContent = runners;
}

function renderUsersUI(users) {
  const tbody = document.getElementById('users-table-body');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  if (!users.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted)">No accounts found matching query</td></tr>`;
    return;
  }

  users.forEach(user => {
    const roleMap = {
      admin: { name: 'Admin', class: 's-enroute' },
      vendor: { name: 'Vendor', class: 's-preparing' },
      runner: { name: 'Runner', class: 's-ready' },
      customer: { name: 'Customer', class: 's-paid' },
      client: { name: 'Customer', class: 's-paid' }
    };
    
    const roleInfo = roleMap[user.role] || { name: user.role, class: 's-created' };
    const regDate = new Date(user.created_at).toLocaleDateString('en-KE', { day: 'numeric', month: 'short', year: 'numeric' });
    const phoneDisplay = user.phone ? `<br><span style="font-size:.7rem;color:var(--brand2);font-weight:700;"><i class="fas fa-phone text-xs"></i> ${user.phone}</span>` : '';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <div style="display:flex;align-items:center;gap:.6rem;">
          <div style="width:32px;height:32px;border-radius:10px;background:#FFF8E7;color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;border:1px solid var(--border);">
            ${user.name.charAt(0).toUpperCase()}
          </div>
          <strong style="color:var(--text);">${user.name}</strong>
        </div>
      </td>
      <td style="color:var(--muted);font-weight:600;">
        ${user.email}
        ${phoneDisplay}
      </td>
      <td><span class="status-pill ${roleInfo.class}">${roleInfo.name}</span></td>
      <td style="font-size:.78rem;color:var(--muted);">${regDate}</td>
      <td><span class="status-pill s-ready"><div class="live-dot" style="display:inline-block;margin-right:4px;"></div>Active</span></td>
      <td>
        <button class="btn-page" style="padding:.25rem .6rem;font-size:.7rem;" onclick="promptManageUser(${user.id}, '${user.name}')">Manage</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function filterUsers() {
  const q = document.getElementById('user-search').value.toLowerCase();
  const role = document.getElementById('filter-user-role').value.toLowerCase();
  
  const filtered = cachedUsers.filter(u => {
    const nameMatch = u.name ? u.name.toLowerCase().includes(q) : false;
    const emailMatch = u.email ? u.email.toLowerCase().includes(q) : false;
    const phoneMatch = u.phone ? u.phone.toLowerCase().includes(q) : false;
    const matchQuery = nameMatch || emailMatch || phoneMatch;

    const uRole = u.role ? u.role.toLowerCase() : '';
    let matchRole = role === '';
    if (role === 'customer') {
      matchRole = uRole === 'customer' || uRole === 'client';
    } else if (role !== '') {
      matchRole = uRole === role;
    }

    return matchQuery && matchRole;
  });
  
  renderUsersUI(filtered);
}

function promptManageUser(userId, name) {
  alert(`Managing permissions for user: ${name} (ID: ${userId}). Account status: Active.`);
}

function toggleVendorBizField() {
  const role = document.getElementById('new-user-role')?.value;
  const wrap = document.getElementById('vendor-biz-name-wrap');
  if (wrap) {
    wrap.style.display = role === 'vendor' ? 'block' : 'none';
  }
}

async function handleCreateUser(event) {
  event.preventDefault();
  const name = document.getElementById('new-user-name').value;
  const email = document.getElementById('new-user-email').value;
  const phone = document.getElementById('new-user-phone').value;
  const password = document.getElementById('new-user-password').value;
  const role = document.getElementById('new-user-role').value;
  const business_name = role === 'vendor' ? document.getElementById('new-user-biz-name')?.value : null;

  try {
    const res = await fetch(`${API_BASE}/admin/users`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ name, email, phone, password, role, business_name })
    });
    
    const data = await res.json();
    if (res.ok && data.success) {
      alert(data.message);
      document.getElementById('create-user-form').reset();
      toggleVendorBizField();
      loadUsersTab();
    } else {
      alert(data.message || 'Error creating user account');
    }
  } catch(e) {
    alert('Network error while creating account');
  }
}
</script>
@endsection
