@extends('admin.layout')

@section('title', 'User Directory — JustFeast Admin')
@section('page-title', 'User Directory')
@section('page-meta', 'Manage platform accounts: Admins, Vendors, Runners, and Customers')

@section('content')
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Left: Users table -->
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-users" style="color:var(--brand)"></i> Platform User Directory</h3>
        <div class="filters-bar" style="border-bottom: none; padding: 0; margin: 0; gap: 0.5rem; box-shadow: none;">
          <div class="search-input-wrap" style="width: 240px;">
            <i class="fas fa-search"></i>
            <input type="text" id="user-search" class="search-input" placeholder="Search user by name, email..." oninput="filterUsers()">
          </div>
          <select id="filter-user-role" class="select-filter" style="width: 140px;" onchange="filterUsers()">
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
      <h3 style="font-size: .875rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: 1.25rem;">
        <i class="fas fa-user-plus" style="color: var(--brand);"></i> Create New Account
      </h3>
      
      <form id="create-user-form" onsubmit="handleCreateUser(event)" style="display: flex; flex-direction: column; gap: 1rem;">
        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Full Name</label>
          <input type="text" id="new-user-name" required placeholder="John Doe" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Email Address</label>
          <input type="email" id="new-user-email" required placeholder="john@justfeast.com" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Password</label>
          <input type="password" id="new-user-password" required placeholder="••••••••" style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
        </div>

        <div>
          <label style="display: block; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; letter-spacing: .05em;">Assign Role</label>
          <select id="new-user-role" required style="width: 100%; padding: .65rem .85rem; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); outline: none; font-weight: 600;">
            <option value="client">Client (Customer)</option>
            <option value="runner">Runner (Courier)</option>
            <option value="vendor">Vendor (Stall Staff)</option>
            <option value="admin">Administrator</option>
          </select>
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
    const res = await fetch(`${API_BASE}/admin/users`);
    if (res.ok) {
      cachedUsers = await res.json();
      renderUsersUI(cachedUsers);
    }
  } catch(e) {}
}

function renderUsersUI(users) {
  const tbody = document.getElementById('users-table-body');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  if (!users.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted)">No users found matching query</td></tr>`;
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
          <div style="width:32px;height:32px;border-radius:10px;background:#FFF8E7;color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;border:1px solid var(--border);">
            ${user.name.charAt(0).toUpperCase()}
          </div>
          <strong style="color:var(--text);">${user.name}</strong>
        </div>
      </td>
      <td style="color:var(--muted);font-weight:600;">${user.email}</td>
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
  const role = document.getElementById('filter-user-role').value;
  
  const filtered = cachedUsers.filter(u => {
    const matchQuery = u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
    const matchRole = role === '' || u.role === role;
    return matchQuery && matchRole;
  });
  
  renderUsersUI(filtered);
}

function promptManageUser(userId, name) {
  alert(`Managing permissions for user: ${name} (ID: ${userId}). Account status: Active.`);
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
