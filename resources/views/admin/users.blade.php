@extends('admin.layout')

@section('title', 'User Directory — JustFeast Admin')
@section('page-title', 'User Directory')
@section('page-meta', 'Manage platform accounts: Customers, Vendors, Runners, and Administrators')

@section('content')
  {{-- KPI Summary Row (Scales instantly over 100,000+ accounts via single SQL query) --}}
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
            <input type="text" id="user-search" class="search-input" placeholder="Search name, phone, email..." oninput="handleSearchInput()">
          </div>
          <select id="filter-user-role" class="select-filter" style="width: 150px;" onchange="handleRoleFilterChange()">
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

      {{-- Server-Side Pagination Bar --}}
      <div id="users-pagination-wrap" style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.25rem;border-top:1px solid var(--border);background:var(--surface);">
        <span style="font-size:0.75rem;font-weight:700;color:var(--muted);" id="pagination-info-text">
          Showing 0 to 0 of 0 accounts
        </span>
        <div style="display:flex;align-items:center;gap:0.4rem;" id="pagination-btn-container">
          <!-- Rendered dynamically -->
        </div>
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

  <!-- Manage User Account Modal -->
  <div class="modal-overlay" id="manage-user-modal-overlay" onclick="if(event.target===this) closeManageUserModal()">
    <div class="modal-card" style="max-width:520px;">
      <div class="modal-header">
        <h3 style="font-size:1.1rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
          <i class="fas fa-user-gear" style="color:var(--brand)"></i>
          Manage User Account
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeManageUserModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="manage-user-form" onsubmit="handleUpdateUser(event)">
        <input type="hidden" id="edit-user-id">
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.1rem;">
          
          <div>
            <label style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Full Name *</label>
            <input type="text" id="edit-user-name" required style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
            <div>
              <label style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Email Address *</label>
              <input type="email" id="edit-user-email" required style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
            </div>
            <div>
              <label style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Phone Number</label>
              <input type="tel" id="edit-user-phone" placeholder="e.g. 0712345678" style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
            </div>
          </div>

          <div>
            <label style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Assign Account Role *</label>
            <select id="edit-user-role" required style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
              <option value="customer">Customer (Client)</option>
              <option value="runner">Runner (Courier)</option>
              <option value="vendor">Vendor (Stall Staff)</option>
              <option value="admin">Administrator</option>
            </select>
          </div>

          <div>
            <label style="display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Reset Password (Optional)</label>
            <input type="password" id="edit-user-password" placeholder="Leave blank to keep current password" style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:600;">
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;padding-top:1rem;border-top:1px solid var(--border);margin-top:0.5rem;">
            <button type="button" onclick="handleDeleteUser()" style="padding:.6rem 1rem;background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5;border-radius:10px;font-weight:800;font-size:.75rem;cursor:pointer;">
              <i class="fas fa-trash-alt mr-1"></i> Delete User Account
            </button>

            <div style="display:flex;gap:0.5rem;">
              <button type="button" class="btn-page" onclick="closeManageUserModal()">Cancel</button>
              <button type="submit" id="btn-update-user" style="padding:.6rem 1.2rem;background:#05A357;color:#FFF;border:none;border-radius:10px;font-weight:800;font-size:.78rem;cursor:pointer;">
                <i class="fas fa-save mr-1"></i> Save Changes
              </button>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
let currentSearch = '';
let currentRole = '';
let searchDebounceTimer = null;
let loadedUsers = [];

window.addEventListener('DOMContentLoaded', () => {
  loadUsersTab(1);
});

function handleSearchInput() {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    currentSearch = document.getElementById('user-search').value.trim();
    loadUsersTab(1);
  }, 300);
}

function handleRoleFilterChange() {
  currentRole = document.getElementById('filter-user-role').value;
  loadUsersTab(1);
}

async function loadUsersTab(page = 1) {
  currentPage = page;
  const tbody = document.getElementById('users-table-body');
  if (tbody) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:4rem;color:var(--muted)"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>`;
  }

  try {
    const params = new URLSearchParams({
      page: page,
      search: currentSearch,
      role: currentRole
    });

    const res = await fetch(`${API_BASE}/admin/users?${params.toString()}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (res.ok) {
      const data = await res.json();
      if (data.kpis) {
        updateUsersKPI(data.kpis);
      }
      if (data.users) {
        loadedUsers = data.users.data || [];
        renderUsersUI(loadedUsers);
        renderPagination(data.users);
      }
    }
  } catch(e) {
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:#991B1B">Failed to load user accounts. Please check your network connection.</td></tr>`;
    }
  }
}

function updateUsersKPI(kpis) {
  if (document.getElementById('user-kpi-total')) document.getElementById('user-kpi-total').textContent = (kpis.total || 0).toLocaleString();
  if (document.getElementById('user-kpi-customers')) document.getElementById('user-kpi-customers').textContent = (kpis.customers || 0).toLocaleString();
  if (document.getElementById('user-kpi-vendors')) document.getElementById('user-kpi-vendors').textContent = (kpis.vendors || 0).toLocaleString();
  if (document.getElementById('user-kpi-runners')) document.getElementById('user-kpi-runners').textContent = (kpis.runners || 0).toLocaleString();
}

function renderUsersUI(users) {
  const tbody = document.getElementById('users-table-body');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  if (!users.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted)">No user accounts found matching query</td></tr>`;
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
        <button class="btn-page" style="padding:.3rem .75rem;font-size:.72rem;font-weight:800;background:var(--surface2);border-color:var(--border);" onclick="promptManageUser(${user.id})">
          <i class="fas fa-sliders mr-1"></i> Manage
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function renderPagination(meta) {
  const info = document.getElementById('pagination-info-text');
  const btnWrap = document.getElementById('pagination-btn-container');

  if (info) {
    const from = meta.from || 0;
    const to = meta.to || 0;
    const total = meta.total || 0;
    info.textContent = `Showing ${from.toLocaleString()} to ${to.toLocaleString()} of ${total.toLocaleString()} accounts`;
  }

  if (!btnWrap) return;
  btnWrap.innerHTML = '';

  if (!meta.last_page || meta.last_page <= 1) return;

  // Previous button
  const prevBtn = document.createElement('button');
  prevBtn.type = 'button';
  prevBtn.className = 'btn-page';
  prevBtn.style.padding = '0.35rem 0.75rem';
  prevBtn.style.fontSize = '0.72rem';
  prevBtn.style.fontWeight = '800';
  prevBtn.disabled = meta.current_page <= 1;
  prevBtn.innerHTML = `<i class="fas fa-chevron-left"></i> Prev`;
  prevBtn.onclick = () => loadUsersTab(meta.current_page - 1);
  btnWrap.appendChild(prevBtn);

  // Page indicator badge
  const pageBadge = document.createElement('span');
  pageBadge.style.fontSize = '0.75rem';
  pageBadge.style.fontWeight = '800';
  pageBadge.style.padding = '0.35rem 0.75rem';
  pageBadge.style.background = 'var(--surface2)';
  pageBadge.style.border = '1px solid var(--border)';
  pageBadge.style.borderRadius = '10px';
  pageBadge.style.color = 'var(--text)';
  pageBadge.textContent = `Page ${meta.current_page.toLocaleString()} of ${meta.last_page.toLocaleString()}`;
  btnWrap.appendChild(pageBadge);

  // Next button
  const nextBtn = document.createElement('button');
  nextBtn.type = 'button';
  nextBtn.className = 'btn-page';
  nextBtn.style.padding = '0.35rem 0.75rem';
  nextBtn.style.fontSize = '0.72rem';
  nextBtn.style.fontWeight = '800';
  nextBtn.disabled = meta.current_page >= meta.last_page;
  nextBtn.innerHTML = `Next <i class="fas fa-chevron-right"></i>`;
  nextBtn.onclick = () => loadUsersTab(meta.current_page + 1);
  btnWrap.appendChild(nextBtn);
}

function promptManageUser(userId) {
  const user = loadedUsers.find(u => u.id === userId);
  if (!user) return;

  const modal = document.getElementById('manage-user-modal-overlay');
  const idInput = document.getElementById('edit-user-id');
  const nameInput = document.getElementById('edit-user-name');
  const emailInput = document.getElementById('edit-user-email');
  const phoneInput = document.getElementById('edit-user-phone');
  const roleInput = document.getElementById('edit-user-role');
  const passwordInput = document.getElementById('edit-user-password');

  if (idInput) idInput.value = user.id;
  if (nameInput) nameInput.value = user.name || '';
  if (emailInput) emailInput.value = user.email || '';
  if (phoneInput) phoneInput.value = user.phone || '';
  if (roleInput) {
    let r = (user.role || 'customer').toLowerCase();
    if (r === 'client') r = 'customer';
    roleInput.value = r;
  }
  if (passwordInput) passwordInput.value = '';

  if (modal) modal.classList.add('is-active');
}

function closeManageUserModal() {
  const modal = document.getElementById('manage-user-modal-overlay');
  if (modal) modal.classList.remove('is-active');
}

async function handleUpdateUser(event) {
  event.preventDefault();
  const userId = document.getElementById('edit-user-id').value;
  const name = document.getElementById('edit-user-name').value;
  const email = document.getElementById('edit-user-email').value;
  const phone = document.getElementById('edit-user-phone').value;
  const role = document.getElementById('edit-user-role').value;
  const password = document.getElementById('edit-user-password').value;
  const btn = document.getElementById('btn-update-user');

  if (!userId || !btn) return;

  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Saving...`;

  try {
    const res = await fetch(`${API_BASE}/admin/users/${userId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ name, email, phone, role, password })
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      alert(data.message || 'User account updated successfully!');
      closeManageUserModal();
      loadUsersTab(currentPage);
    } else {
      alert(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to update user account.'));
    }
  } catch (e) {
    alert('Network error while updating user account');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

async function handleDeleteUser() {
  const userId = document.getElementById('edit-user-id').value;
  const name = document.getElementById('edit-user-name').value;
  if (!userId) return;

  if (!confirm(`Are you sure you want to permanently delete the account for '${name}'? This action cannot be undone.`)) {
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/admin/users/${userId}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      alert(data.message || 'User account deleted successfully.');
      closeManageUserModal();
      loadUsersTab(currentPage);
    } else {
      alert(data.message || 'Failed to delete user account.');
    }
  } catch (e) {
    alert('Network error while deleting user account');
  }
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
      loadUsersTab(1);
    } else {
      alert(data.message || 'Error creating user account');
    }
  } catch(e) {
    alert('Network error while creating account');
  }
}
</script>
@endsection
