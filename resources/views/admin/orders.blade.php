@extends('admin.layout')

@section('title', 'Order Database — JustFeast Admin')
@section('page-title', 'Order Database')
@section('page-meta', 'Search, filter, and track all live concert orders')

@section('content')
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
@endsection

@section('scripts')
<script>
let allOrders = [];
let filteredOrdersList = [];
let currentPage = 1;
const entriesPerPage = 10;

window.addEventListener('DOMContentLoaded', () => {
  loadOrdersTab();
  setInterval(loadOrdersTab, 5000);
});

async function loadOrdersTab() {
  try {
    const res = await fetch(`${API_BASE}/admin/orders`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (res.ok) {
      const data = await res.json();
      allOrders = Array.isArray(data) ? data : (data.data || []);
      filterOrders();
    }
  } catch(e) {}
}

function statusClass(s) {
  const map = {created:'s-created',accepted:'s-ready',preparing:'s-preparing',ready:'s-ready',enroute:'s-enroute',delivered:'s-delivered',paid:'s-paid',pending:'s-pending'};
  return map[s] || 's-created';
}

function filterOrders() {
  const q = document.getElementById('order-search').value.toLowerCase();
  const oStatus = document.getElementById('filter-order-status').value;
  const pStatus = document.getElementById('filter-payment-status').value;

  filteredOrdersList = allOrders.filter(o => {
    const name = (o.user?.name || '').toLowerCase();
    const vendor = (o.vendor?.business_name || '').toLowerCase();
    const matchQuery = name.includes(q) || vendor.includes(q) || String(o.id).includes(q);
    const matchOStatus = oStatus === '' || o.order_status === oStatus;
    const matchPStatus = pStatus === '' || o.payment_status === pStatus;
    return matchQuery && matchOStatus && matchPStatus;
  });

  currentPage = 1;
  renderOrdersTable();
}

function renderOrdersTable() {
  const tbody = document.getElementById('all-orders-tbody');
  const start = (currentPage - 1) * entriesPerPage;
  const pageItems = filteredOrdersList.slice(start, start + entriesPerPage);

  if (!pageItems.length) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--muted)">No orders match your filter criteria</td></tr>`;
  } else {
    tbody.innerHTML = pageItems.map(o => {
      const loc = o.seat_location || {};
      const seat = loc.section ? `${loc.section}, R${loc.row||'?'} S${loc.seat||'?'}` : '—';
      const time = new Date(o.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      const sc = statusClass(o.order_status);
      const pc = o.payment_status === 'paid' ? 's-paid' : 's-pending';

      return `<tr>
        <td style="font-weight:700;color:var(--brand)">#${o.id}</td>
        <td>
          <div style="font-weight:700">${o.user?.name || 'Guest'}</div>
          <div style="font-size:.68rem;color:var(--muted)">${o.user?.email || '—'}</div>
        </td>
        <td><strong style="color:var(--text)">${o.vendor?.business_name || '—'}</strong></td>
        <td style="font-size:.75rem;color:var(--muted)">${seat}</td>
        <td><span style="font-size:.75rem;font-weight:600">${o.runner?.name || 'Unassigned'}</span></td>
        <td style="font-weight:800;color:var(--brand2)">Ksh ${Number(o.total_amount).toLocaleString()}</td>
        <td><span class="status-pill ${sc}">${o.order_status}</span></td>
        <td><span class="status-pill ${pc}">${o.payment_status}</span></td>
        <td style="color:var(--muted);font-size:.7rem">${time}</td>
      </tr>`;
    }).join('');
  }

  // Update pagination info
  document.getElementById('orders-page-info').textContent = `Showing ${filteredOrdersList.length ? start + 1 : 0} to ${Math.min(start + entriesPerPage, filteredOrdersList.length)} of ${filteredOrdersList.length} entries`;
  document.getElementById('btn-prev-page').disabled = currentPage === 1;
  document.getElementById('btn-next-page').disabled = start + entriesPerPage >= filteredOrdersList.length;
}

function prevPage() {
  if (currentPage > 1) {
    currentPage--;
    renderOrdersTable();
  }
}
function nextPage() {
  if (currentPage * entriesPerPage < filteredOrdersList.length) {
    currentPage++;
    renderOrdersTable();
  }
}
</script>
@endsection
