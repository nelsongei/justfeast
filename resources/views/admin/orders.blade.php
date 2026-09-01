@extends('admin.layout')

@section('title', 'Order Database — JustFeast Admin')
@section('page-title', 'Order Database')
@section('page-meta', 'Search, filter, manage status, and trigger M-Pesa payments for all live concert orders')

@section('content')
  <div class="filters-bar">
    <div class="search-input-wrap">
      <i class="fas fa-search"></i>
      <input type="text" id="order-search" class="search-input" placeholder="Search orders by customer name, phone, vendor, seat, or #ID..." oninput="filterOrders()">
    </div>
    <select id="filter-order-status" class="select-filter" onchange="filterOrders()">
      <option value="">All Order Statuses</option>
      <option value="created">Created</option>
      <option value="accepted">Accepted</option>
      <option value="preparing">Preparing</option>
      <option value="ready">Ready</option>
      <option value="runner_assigned">Runner Assigned</option>
      <option value="enroute">En Route</option>
      <option value="delivered">Delivered</option>
      <option value="cancelled">Cancelled</option>
    </select>
    <select id="filter-payment-status" class="select-filter" onchange="filterOrders()">
      <option value="">All Payment Statuses</option>
      <option value="paid">Paid</option>
      <option value="pending">Pending</option>
      <option value="failed">Failed</option>
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
            <th style="text-align:center">Actions</th>
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

  <!-- Admin Trigger M-Pesa STK Push Payment Modal -->
  <div class="modal-overlay" id="admin-pay-modal-overlay" onclick="if(event.target===this) closeAdminPayModal()">
    <div class="modal-card" style="max-width:440px;">
      <div class="modal-header">
        <h3 style="font-size:1.05rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
          <span style="background:#ECFDF5;color:#05A357;padding:0.35rem 0.6rem;border-radius:10px;font-size:0.9rem;border:1px solid rgba(5,163,87,0.2);">
            <i class="fas fa-mobile-screen-button"></i>
          </span>
          Trigger M-Pesa Payment
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeAdminPayModal()">&times;</button>
      </div>
      <form id="admin-pay-form" onsubmit="handleAdminTriggerPay(event)">
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">
          <div style="background:#F8FAFC;border:1px solid var(--border);border-radius:16px;padding:1rem;display:flex;flex-direction:column;gap:0.4rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <span style="font-size:0.72rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Target Order</span>
              <strong style="font-size:0.9rem;font-weight:900;color:var(--brand)" id="pay-modal-order-id">#0</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <span style="font-size:0.72rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Total Payable</span>
              <strong style="font-size:1.15rem;font-weight:900;color:#05A357" id="pay-modal-amount">Ksh 0</strong>
            </div>
          </div>

          <div>
            <label style="display:block;font-size:0.78rem;font-weight:800;color:var(--text);margin-bottom:0.4rem;">
              Customer M-Pesa Phone Number *
            </label>
            <div style="position:relative;">
              <span style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);font-weight:800;color:#05A357;">
                <i class="fas fa-phone"></i>
              </span>
              <input type="tel" id="pay-modal-phone-input" required placeholder="e.g. 0712345678"
                     style="width:100%;padding:0.75rem 1rem 0.75rem 2.8rem;background:var(--surface2);border:1px solid var(--border);border-radius:14px;font-weight:800;font-size:0.95rem;color:var(--text);outline:none;">
            </div>
            <p style="font-size:0.72rem;color:var(--muted);margin-top:0.4rem;font-weight:600;">
              An instant Safaricom M-Pesa STK prompt will be dispatched to this phone.
            </p>
          </div>

          <div id="pay-modal-status-banner" style="display:none;padding:0.85rem;border-radius:12px;font-size:0.8rem;font-weight:700;"></div>

          <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:0.75rem;border-top:1px solid var(--border);">
            <button type="button" class="btn-page" onclick="closeAdminPayModal()">Cancel</button>
            <button type="submit" id="btn-trigger-stk"
                    style="padding:0.75rem 1.4rem;background:#05A357;color:#FFF;border:none;border-radius:14px;font-weight:800;font-size:0.84rem;cursor:pointer;display:flex;align-items:center;gap:0.5rem;box-shadow:0 4px 14px rgba(5,163,87,0.25);">
              <i class="fas fa-paper-plane"></i> Send STK Push
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Order Details Inspection Modal -->
  <div class="modal-overlay" id="order-details-modal-overlay" onclick="if(event.target===this) closeOrderDetailsModal()">
    <div class="modal-card" style="max-width:620px;">
      <div class="modal-header">
        <h3 style="font-size:1.1rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
          <i class="fas fa-receipt" style="color:var(--brand)"></i>
          Order Breakdown & Delivery PIN
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeOrderDetailsModal()">&times;</button>
      </div>

      <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">
        <!-- Header details -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;background:#F8FAFC;border:1px solid var(--border);border-radius:16px;padding:1rem;">
          <div>
            <span style="font-size:0.65rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Order Reference</span>
            <p style="font-size:1rem;font-weight:900;color:var(--brand)" id="details-order-id">#0</p>
            <span style="font-size:0.7rem;color:var(--muted);font-weight:600" id="details-order-date">—</span>
          </div>
          <div>
            <span style="font-size:0.65rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Handover Verification PIN</span>
            <p style="font-size:1.3rem;font-weight:900;color:#05A357;letter-spacing:0.1em" id="details-verification-pin">----</p>
            <span style="font-size:0.68rem;color:var(--muted);font-weight:600" id="details-runner-name">Runner: Unassigned</span>
          </div>
        </div>

        <!-- Customer & Location info -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:0.8rem;">
          <div style="background:#FFF;border:1px solid var(--border);border-radius:14px;padding:0.9rem;">
            <strong style="display:block;font-size:0.7rem;font-weight:800;color:var(--muted);text-transform:uppercase;margin-bottom:0.3rem;">Customer Info</strong>
            <p style="font-weight:800;color:var(--text)" id="details-cust-name">Guest</p>
            <p style="color:var(--muted);font-size:0.75rem" id="details-cust-email">—</p>
            <p style="color:#05A357;font-weight:700;font-size:0.75rem;margin-top:0.2rem" id="details-cust-phone">—</p>
          </div>
          <div style="background:#FFF;border:1px solid var(--border);border-radius:14px;padding:0.9rem;">
            <strong style="display:block;font-size:0.7rem;font-weight:800;color:var(--muted);text-transform:uppercase;margin-bottom:0.3rem;">Delivery Location Pin</strong>
            <p style="font-weight:800;color:var(--text)" id="details-seat-location">Not Configured</p>
            <p style="color:var(--muted);font-size:0.72rem;margin-top:0.2rem" id="details-vendor-name">Stall: —</p>
          </div>
        </div>

        <!-- Items Table -->
        <div>
          <strong style="display:block;font-size:0.75rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Ordered Food & Drinks</strong>
          <div style="border:1px solid var(--border);border-radius:14px;overflow:hidden;">
            <table style="width:100%;font-size:0.8rem;border-collapse:collapse;">
              <thead style="background:#F8FAFC;">
                <tr>
                  <th style="padding:0.6rem 0.9rem;text-align:left;font-size:0.68rem;">Item Name</th>
                  <th style="padding:0.6rem 0.9rem;text-align:center;font-size:0.68rem;">Qty</th>
                  <th style="padding:0.6rem 0.9rem;text-align:right;font-size:0.68rem;">Price</th>
                  <th style="padding:0.6rem 0.9rem;text-align:right;font-size:0.68rem;">Subtotal</th>
                </tr>
              </thead>
              <tbody id="details-items-tbody">
                <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--muted)">No items found</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Financial Summary -->
        <div style="display:flex;flex-direction:column;gap:0.3rem;padding:0.8rem 1rem;background:#FFF8E7;border:1px solid #F7E5B2;border-radius:14px;font-size:0.82rem;">
          <div style="display:flex;justify-between;align-items:center;">
            <span style="color:var(--muted);font-weight:700">Total Amount Payable</span>
            <strong style="font-size:1.1rem;font-weight:900;color:var(--brand)" id="details-total-payable">Ksh 0</strong>
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;padding-top:0.5rem;">
          <button type="button" class="btn-page" onclick="closeOrderDetailsModal()">Close Window</button>
        </div>
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
let activePayOrder = null;

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

function filterOrders() {
  const q = document.getElementById('order-search').value.toLowerCase().trim();
  const oStatus = document.getElementById('filter-order-status').value;
  const pStatus = document.getElementById('filter-payment-status').value;

  filteredOrdersList = allOrders.filter(o => {
    const name = (o.user?.name || '').toLowerCase();
    const email = (o.user?.email || '').toLowerCase();
    const phone = (o.user?.phone || '').toLowerCase();
    const vendor = (o.vendor?.business_name || '').toLowerCase();
    const matchQuery = name.includes(q) || email.includes(q) || phone.includes(q) || vendor.includes(q) || String(o.id).includes(q);
    const matchOStatus = oStatus === '' || o.order_status === oStatus;
    const matchPStatus = pStatus === '' || o.payment_status === pStatus;
    return matchQuery && matchOStatus && matchPStatus;
  });

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
      let seat = '—';
      if (loc.type === 'gps' || loc.latitude) {
        seat = `GPS: ${loc.description || (Number(loc.latitude).toFixed(4) + ', ' + Number(loc.longitude).toFixed(4))}`;
      } else if (loc.section) {
        seat = `${loc.section}, R${loc.row||'?'} S${loc.seat||'?'}`;
      }

      const time = new Date(o.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      const dateStr = new Date(o.created_at).toLocaleDateString([], {month:'short',day:'numeric'});

      const runnerName = o.runner?.name || o.delivery?.runner?.name || 'Unassigned';
      const custContact = o.user?.phone || (isGenuineEmail(o.user?.email) ? o.user.email : '—');

      return `<tr>
        <td style="font-weight:800;color:var(--brand)">
          #${o.id}
          <div style="font-size:0.65rem;color:var(--muted);font-weight:600;">${dateStr} ${time}</div>
        </td>
        <td>
          <div style="font-weight:800;color:var(--text)">${o.user?.name || 'Guest'}</div>
          <div style="font-size:.68rem;color:var(--muted);">${custContact}</div>
        </td>
        <td><strong style="color:var(--text)">${o.vendor?.business_name || '—'}</strong></td>
        <td style="font-size:.75rem;color:var(--muted);max-width:140px;word-break:break-word;">${seat}</td>
        <td><span style="font-size:.75rem;font-weight:700;color:var(--text)">${runnerName}</span></td>
        <td style="font-weight:900;color:#05A357">Ksh ${Number(o.total_amount).toLocaleString()}</td>
        
        <!-- Editable Order Status -->
        <td>
          <select onchange="updateOrderStatus(${o.id}, 'order_status', this.value)" 
                  style="padding:0.3rem 0.5rem;border-radius:10px;font-size:0.72rem;font-weight:800;border:1px solid var(--border);background:var(--surface2);color:var(--text);cursor:pointer;">
            <option value="created" ${o.order_status==='created'?'selected':''}>Created</option>
            <option value="accepted" ${o.order_status==='accepted'?'selected':''}>Accepted</option>
            <option value="preparing" ${o.order_status==='preparing'?'selected':''}>Preparing</option>
            <option value="ready" ${o.order_status==='ready'?'selected':''}>Ready</option>
            <option value="runner_assigned" ${o.order_status==='runner_assigned'?'selected':''}>Runner Assigned</option>
            <option value="enroute" ${o.order_status==='enroute'?'selected':''}>En Route</option>
            <option value="delivered" ${o.order_status==='delivered'?'selected':''}>Delivered</option>
            <option value="cancelled" ${o.order_status==='cancelled'?'selected':''}>Cancelled</option>
          </select>
        </td>

        <!-- Editable Payment Status -->
        <td>
          <select onchange="updateOrderStatus(${o.id}, 'payment_status', this.value)" 
                  style="padding:0.3rem 0.5rem;border-radius:10px;font-size:0.72rem;font-weight:800;border:1px solid var(--border);background:${o.payment_status==='paid'?'#ECFDF5':'#FFF8E7'};color:${o.payment_status==='paid'?'#047857':'#B45309'};cursor:pointer;">
            <option value="pending" ${o.payment_status==='pending'?'selected':''}>Pending</option>
            <option value="paid" ${o.payment_status==='paid'?'selected':''}>Paid</option>
            <option value="failed" ${o.payment_status==='failed'?'selected':''}>Failed</option>
          </select>
        </td>

        <!-- Actions -->
        <td style="text-align:center;">
          <div style="display:flex;align-items:center;justify-content:center;gap:0.4rem;">
            <button onclick="openAdminPayModal(${o.id})" title="Trigger M-Pesa STK Push Payment"
                    style="padding:0.35rem 0.6rem;background:#05A357;color:#FFF;border:none;border-radius:8px;font-size:0.72rem;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:0.3rem;box-shadow:0 2px 6px rgba(5,163,87,0.2);">
              <i class="fas fa-paper-plane"></i> Pay
            </button>
            <button onclick="openOrderDetailsModal(${o.id})" title="View Details & Handover PIN"
                    style="padding:0.35rem 0.6rem;background:var(--surface2);color:var(--text);border:1px solid var(--border);border-radius:8px;font-size:0.72rem;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:0.3rem;">
              <i class="fas fa-eye" style="color:var(--brand)"></i> View
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');
  }

  // Update pagination info
  document.getElementById('orders-page-info').textContent = `Showing ${filteredOrdersList.length ? start + 1 : 0} to ${Math.min(start + entriesPerPage, filteredOrdersList.length)} of ${filteredOrdersList.length} entries`;
  document.getElementById('btn-prev-page').disabled = currentPage === 1;
  document.getElementById('btn-next-page').disabled = start + entriesPerPage >= filteredOrdersList.length;
}

async function updateOrderStatus(orderId, field, value) {
  try {
    const payload = {};
    payload[field] = value;

    const res = await fetch(`${API_BASE}/admin/orders/${orderId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (res.ok && data.status === 'success') {
      showNotification(`Order #${orderId} updated successfully!`, 'success');
      loadOrdersTab();
    } else {
      showNotification(data.message || 'Failed to update order status.', 'error');
    }
  } catch (e) {
    showNotification('Network error updating order status.', 'error');
  }
}

function openAdminPayModal(orderId) {
  const order = allOrders.find(o => o.id === orderId);
  if (!order) return;
  activePayOrder = order;

  document.getElementById('pay-modal-order-id').textContent = `#${order.id}`;
  document.getElementById('pay-modal-amount').textContent = `Ksh ${Number(order.total_amount).toLocaleString()}`;
  document.getElementById('pay-modal-phone-input').value = order.user?.phone || '';

  const banner = document.getElementById('pay-modal-status-banner');
  if (banner) banner.style.display = 'none';

  const modal = document.getElementById('admin-pay-modal-overlay');
  if (modal) modal.classList.add('is-active');
}

function closeAdminPayModal() {
  const modal = document.getElementById('admin-pay-modal-overlay');
  if (modal) modal.classList.remove('is-active');
  activePayOrder = null;
}

async function handleAdminTriggerPay(event) {
  event.preventDefault();
  if (!activePayOrder) return;

  const phoneInput = document.getElementById('pay-modal-phone-input');
  const btn = document.getElementById('btn-trigger-stk');
  const banner = document.getElementById('pay-modal-status-banner');
  if (!phoneInput || !btn) return;

  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Triggering...`;
  if (banner) banner.style.display = 'none';

  try {
    const res = await fetch(`${API_BASE}/admin/orders/${activePayOrder.id}/pay`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ phone: phoneInput.value.trim() })
    });

    const data = await res.json();

    if (res.ok && data.status === 'success') {
      showNotification(`M-Pesa STK Push sent to ${data.phone || phoneInput.value}!`, 'success');
      if (banner) {
        banner.style.display = 'block';
        banner.style.background = '#ECFDF5';
        banner.style.color = '#047857';
        banner.style.border = '1px solid #A7F3D0';
        banner.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message || 'STK Push prompt dispatched to customer.'}`;
      }
      setTimeout(() => {
        closeAdminPayModal();
        loadOrdersTab();
      }, 1800);
    } else {
      showNotification(data.message || 'Failed to trigger M-Pesa STK Push.', 'error');
      if (banner) {
        banner.style.display = 'block';
        banner.style.background = '#FEF2F2';
        banner.style.color = '#991B1B';
        banner.style.border = '1px solid #FCA5A5';
        banner.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${data.message || 'STK Push initiation failed.'}`;
      }
    }
  } catch (e) {
    showNotification('Network error triggering M-Pesa payment.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

function isGenuineEmail(email) {
  if (!email) return false;
  const str = String(email).trim().toLowerCase();
  if (str.startsWith('customer_') || str.startsWith('vendor_') || str.startsWith('runner_')) return false;
  if (str.endsWith('@justfeast.co.ke') || str.endsWith('@justfeast.com')) return false;
  return true;
}

function openOrderDetailsModal(orderId) {
  const order = allOrders.find(o => o.id === orderId);
  if (!order) return;

  document.getElementById('details-order-id').textContent = `#${order.id}`;
  document.getElementById('details-order-date').textContent = new Date(order.created_at).toLocaleString();
  document.getElementById('details-verification-pin').textContent = order.delivery?.verification_pin || 'Pending';
  document.getElementById('details-runner-name').textContent = `Runner: ${order.runner?.name || order.delivery?.runner?.name || 'Unassigned'}`;
  document.getElementById('details-cust-name').textContent = order.user?.name || 'Guest';
  
  const emailEl = document.getElementById('details-cust-email');
  if (isGenuineEmail(order.user?.email)) {
    emailEl.textContent = order.user.email;
    emailEl.style.display = 'block';
  } else {
    emailEl.textContent = '';
    emailEl.style.display = 'none';
  }

  document.getElementById('details-cust-phone').textContent = order.user?.phone || '—';
  document.getElementById('details-vendor-name').textContent = `Stall: ${order.vendor?.business_name || '—'}`;

  const loc = order.seat_location || {};
  let locText = 'Not Configured';
  if (loc.type === 'gps' || loc.latitude) {
    locText = `GPS Pin: ${loc.description || (Number(loc.latitude).toFixed(5) + ', ' + Number(loc.longitude).toFixed(5))}`;
  } else if (loc.section) {
    locText = `${loc.section}, Row ${loc.row||'?'}, Seat ${loc.seat||'?'}`;
  }
  document.getElementById('details-seat-location').textContent = locText;
  document.getElementById('details-total-payable').textContent = `Ksh ${Number(order.total_amount).toLocaleString()}`;

  const tbody = document.getElementById('details-items-tbody');
  const items = order.items || [];
  if (!items.length) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--muted)">No item records found</td></tr>`;
  } else {
    tbody.innerHTML = items.map(i => {
      const pName = i.product?.name || `Product #${i.product_id}`;
      const price = Number(i.price || 0);
      const qty = Number(i.quantity || 1);
      const subtotal = price * qty;
      return `<tr>
        <td style="padding:0.6rem 0.9rem;font-weight:700;color:var(--text)">${pName}</td>
        <td style="padding:0.6rem 0.9rem;text-align:center;font-weight:800;">${qty}</td>
        <td style="padding:0.6rem 0.9rem;text-align:right;color:var(--muted)">Ksh ${price.toLocaleString()}</td>
        <td style="padding:0.6rem 0.9rem;text-align:right;font-weight:900;color:var(--brand)">Ksh ${subtotal.toLocaleString()}</td>
      </tr>`;
    }).join('');
  }

  const modal = document.getElementById('order-details-modal-overlay');
  if (modal) modal.classList.add('is-active');
}

function closeOrderDetailsModal() {
  const modal = document.getElementById('order-details-modal-overlay');
  if (modal) modal.classList.remove('is-active');
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
