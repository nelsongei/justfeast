@extends('admin.layout')

@section('title', 'Vendor Network — JustFeast Admin')
@section('page-title', 'Vendor Network & Account Management')
@section('page-meta', 'Monitor active stalls, gross revenue, product catalog, and account status controls')

@section('content')
  <style>
    /* Custom Modern UI Styling for Vendors View */
    .vendors-page-wrap {
      display: flex;
      flex-direction: column;
      gap: 1.75rem;
    }

    .toast-container {
      position: fixed;
      top: 1.5rem;
      right: 1.5rem;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      pointer-events: none;
    }

    .toast {
      pointer-events: auto;
      min-width: 300px;
      padding: 1rem 1.25rem;
      border-radius: 16px;
      background: #0F172A;
      color: #FFFFFF;
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
      display: flex;
      align-items: center;
      gap: 0.85rem;
      font-size: 0.85rem;
      font-weight: 700;
      animation: slideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    .toast-icon {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .toast-success .toast-icon { background: rgba(5, 163, 87, 0.2); color: #34D399; }
    .toast-error .toast-icon   { background: rgba(239, 68, 68, 0.2); color: #FCA5A5; }

    /* Summary KPIs */
    .vendor-kpis {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.25rem;
    }

    @media (max-width: 1024px) {
      .vendor-kpis { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
      .vendor-kpis { grid-template-columns: 1fr; }
    }

    .vkpi-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1.35rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
      transition: all 0.2s ease;
    }

    .vkpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .vkpi-info h6 {
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-bottom: 0.35rem;
    }

    .vkpi-info .val {
      font-size: 1.75rem;
      font-weight: 900;
      letter-spacing: -0.03em;
      color: var(--text);
    }

    .vkpi-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    /* Filter Bar */
    .vendor-filter-bar {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }

    .filter-group {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      flex: 1;
      min-width: 280px;
    }

    .btn-sync {
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--text);
      padding: 0.75rem 1.2rem;
      border-radius: 12px;
      font-size: 0.82rem;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }

    .btn-sync:hover {
      background: #FFF8E7;
      border-color: var(--brand);
      color: var(--brand);
    }

    /* Enhanced Vendor Grid Cards */
    .vendor-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
      gap: 1.5rem;
    }

    .vendor-vcard {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .vendor-vcard:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
      border-color: rgba(163, 29, 29, 0.25);
    }

    .vendor-vcard.is-inactive {
      opacity: 0.88;
      background: #FAFBFD;
      border-style: dashed;
    }

    .vcard-head {
      padding: 1.5rem;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      border-bottom: 1px solid var(--border);
      background: #FFFFFF;
    }

    .vcard-logo-meta {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .vcard-logo {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
      overflow: hidden;
    }

    .vcard-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .vcard-title h4 {
      font-size: 1.05rem;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.02em;
      margin-bottom: 0.25rem;
    }

    .vcard-title .v-sub {
      font-size: 0.76rem;
      color: var(--muted);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .vcard-body-stats {
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: var(--surface2);
      border-bottom: 1px solid var(--border);
    }

    .vstat-box {
      padding: 1.1rem 1.4rem;
      border-right: 1px solid var(--border);
    }

    .vstat-box:last-child {
      border-right: none;
    }

    .vstat-lbl {
      font-size: 0.65rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-bottom: 0.25rem;
    }

    .vstat-num {
      font-size: 1.25rem;
      font-weight: 900;
      color: var(--text);
    }

    .vcard-event-tag {
      padding: 0.75rem 1.5rem;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--muted);
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      background: #FFFFFF;
    }

    .vcard-event-tag span i {
      color: var(--brand);
      margin-right: 0.4rem;
    }

    /* Status Pill Variants */
    .pill-active {
      background: rgba(5, 163, 87, 0.1);
      color: #047857;
      border: 1px solid rgba(5, 163, 87, 0.25);
    }

    .pill-inactive {
      background: rgba(239, 68, 68, 0.1);
      color: #B91C1C;
      border: 1px solid rgba(239, 68, 68, 0.25);
    }

    /* Menu Drawer */
    .vcard-drawer-btn {
      width: 100%;
      background: #FFFFFF;
      border: none;
      border-bottom: 1px solid var(--border);
      color: var(--muted);
      font-size: 0.8rem;
      font-weight: 800;
      padding: 0.85rem 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background 0.15s ease;
    }

    .vcard-drawer-btn:hover {
      background: var(--surface2);
      color: var(--text);
    }

    .vcard-menu-drawer {
      display: none;
      background: #F8FAFC;
      border-bottom: 1px solid var(--border);
      max-height: 240px;
      overflow-y: auto;
    }

    .menu-drawer-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 1.5rem;
      font-size: 0.78rem;
      border-bottom: 1px solid var(--border);
    }

    .menu-drawer-item:last-child {
      border-bottom: none;
    }

    /* Action Buttons */
    .vcard-actions {
      padding: 1rem 1.5rem;
      background: #FFFFFF;
      margin-top: auto;
      display: flex;
      gap: 0.75rem;
    }

    .btn-toggle-status {
      flex: 1;
      padding: 0.75rem 1rem;
      border-radius: 14px;
      font-size: 0.82rem;
      font-weight: 900;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      border: none;
    }

    .btn-toggle-status.deactivate {
      background: #FEF2F2;
      color: #991B1B;
      border: 1px solid #FCA5A5;
    }

    .btn-toggle-status.deactivate:hover {
      background: #FEE2E2;
      color: #7F1D1D;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    .btn-toggle-status.activate {
      background: #ECFDF5;
      color: #047857;
      border: 1px solid #A7F3D0;
    }

    .btn-toggle-status.activate:hover {
      background: #D1FAE5;
      color: #065F46;
      box-shadow: 0 4px 12px rgba(5, 163, 87, 0.15);
    }

    .btn-toggle-status:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    /* Register Vendor Modal */
    .modal-overlay {
      position: fixed;
      inset: 0;
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

    .modal-header h3 {
      font-size: 1.1rem;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.02em;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin: 0;
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

    .modal-body {
      padding: 1.75rem;
    }

    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
    }

    @media (max-width: 640px) {
      .form-grid-2 { grid-template-columns: 1fr; }
    }

    .form-field-group {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }

    .form-field-group label {
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--muted);
    }

    .form-input-ctrl {
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--text);
      font-size: 0.85rem;
      font-weight: 600;
      outline: none;
      transition: all 0.2s ease;
    }

    .form-input-ctrl:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(163, 29, 29, 0.1);
      background: #FFFFFF;
    }

    .btn-register-vendor {
      background: linear-gradient(135deg, #A31D1D, #841313);
      color: #FFFFFF;
      padding: 0.75rem 1.4rem;
      border-radius: 12px;
      font-size: 0.82rem;
      font-weight: 900;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: 0 4px 14px rgba(163, 29, 29, 0.25);
      transition: all 0.2s ease;
    }

    .btn-register-vendor:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(163, 29, 29, 0.35);
    }
  </style>

  <div id="toast-container" class="toast-container"></div>

  <div class="vendors-page-wrap">

    {{-- ── 1. Top Metric Cards ── --}}
    <div class="vendor-kpis">
      <div class="vkpi-card">
        <div class="vkpi-info">
          <h6>Total Vendors</h6>
          <div class="val" id="kpi-total-vendors">0</div>
        </div>
        <div class="vkpi-icon" style="background:#FFF8E7; color:var(--brand);">
          <i class="fas fa-store"></i>
        </div>
      </div>

      <div class="vkpi-card">
        <div class="vkpi-info">
          <h6>Active Outlets</h6>
          <div class="val" id="kpi-active-vendors" style="color:var(--brand2)">0</div>
        </div>
        <div class="vkpi-icon" style="background:#ECFDF5; color:var(--brand2);">
          <i class="fas fa-check-circle"></i>
        </div>
      </div>

      <div class="vkpi-card">
        <div class="vkpi-info">
          <h6>Inactive / Suspended</h6>
          <div class="val" id="kpi-inactive-vendors" style="color:var(--red)">0</div>
        </div>
        <div class="vkpi-icon" style="background:#FEF2F2; color:var(--red);">
          <i class="fas fa-ban"></i>
        </div>
      </div>

      <div class="vkpi-card">
        <div class="vkpi-info">
          <h6>Network Revenue</h6>
          <div class="val" id="kpi-network-revenue" style="color:var(--brand2)">Ksh 0</div>
        </div>
        <div class="vkpi-icon" style="background:#EFF6FF; color:var(--blue);">
          <i class="fas fa-coins"></i>
        </div>
      </div>
    </div>

    {{-- ── 2. Filters & Search Bar ── --}}
    <div class="vendor-filter-bar">
      <div class="filter-group">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input 
            type="text" 
            id="vendor-search" 
            class="search-input" 
            placeholder="Search vendor by business name, email..."
            oninput="applyVendorFilters()"
          >
        </div>

        <select id="filter-status" class="select-filter" onchange="applyVendorFilters()">
          <option value="">All Statuses</option>
          <option value="active">Active Only</option>
          <option value="inactive">Inactive Only</option>
        </select>
      </div>

      <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
        <button type="button" class="btn-register-vendor" onclick="openRegisterVendorModal()">
          <i class="fas fa-plus-circle"></i>
          <span>Register New Vendor</span>
        </button>

        <button type="button" class="btn-sync" onclick="loadVendorsTab(true)">
          <i class="fas fa-sync-alt" id="sync-icon"></i>
          <span>Refresh Vendors</span>
        </button>
      </div>
    </div>

    {{-- ── 3. Vendor Cards Grid Container ── --}}
    <div class="vendor-cards-grid" id="all-vendors-cards">
      <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted)">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p style="margin-top:0.75rem;font-weight:700;">Loading vendor network...</p>
      </div>
    </div>

  </div>

  {{-- ── 4. Register Vendor Modal ── --}}
  <div class="modal-overlay" id="vendor-modal-overlay">
    <div class="modal-card">
      <div class="modal-header">
        <h3>
          <i class="fas fa-store" style="color:var(--brand)"></i>
          Register New Vendor Account
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeRegisterVendorModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="register-vendor-form" onsubmit="handleRegisterVendor(event)" enctype="multipart/form-data">
        <div class="modal-body" style="display:flex;flex-direction:column;gap:1.25rem;">
          
          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:var(--brand);border-bottom:1px solid var(--border);padding-bottom:0.4rem;">
            <i class="fas fa-user-shield mr-1"></i> Account Owner & Credentials
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="v-owner-name">Owner Full Name *</label>
              <input type="text" id="v-owner-name" name="name" required class="form-input-ctrl" placeholder="e.g. Jane Doe">
            </div>

            <div class="form-field-group">
              <label for="v-owner-email">Email Address *</label>
              <input type="email" id="v-owner-email" name="email" required class="form-input-ctrl" placeholder="jane@stall.co.ke">
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="v-owner-phone">Phone Number</label>
              <input type="tel" id="v-owner-phone" name="phone" class="form-input-ctrl" placeholder="+254 700 000 000">
            </div>

            <div class="form-field-group">
              <label for="v-password">Account Password *</label>
              <input type="password" id="v-password" name="password" required class="form-input-ctrl" placeholder="••••••••" minlength="6">
            </div>
          </div>

          <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:var(--brand);border-bottom:1px solid var(--border);padding-bottom:0.4rem;margin-top:0.5rem;">
            <i class="fas fa-store-alt mr-1"></i> Vendor Business & Outlet Profile
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="v-biz-name">Stall / Business Name *</label>
              <input type="text" id="v-biz-name" name="business_name" required class="form-input-ctrl" placeholder="e.g. Carnivore Smokehouse">
            </div>

            <div class="form-field-group">
              <label for="v-event-id">Assigned Event *</label>
              <select id="v-event-id" name="event_id" required class="form-input-ctrl">
                <option value="">Loading events...</option>
              </select>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="v-status">Initial Account Status</label>
              <select id="v-status" name="status" class="form-input-ctrl">
                <option value="active">Active (Ready to trade)</option>
                <option value="inactive">Inactive / Suspended</option>
              </select>
            </div>

            <div class="form-field-group">
              <label for="v-logo-file">Stall Logo File</label>
              <input type="file" id="v-logo-file" name="logo" accept="image/*" class="form-input-ctrl" style="padding:0.5rem;">
            </div>
          </div>

          <div class="form-field-group">
            <label for="v-logo-url">Or Image / Emoji URL (Optional)</label>
            <input type="text" id="v-logo-url" name="logo_url" class="form-input-ctrl" placeholder="https://example.com/logo.png or 🍔">
          </div>

          <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
            <button type="button" class="btn-sync" onclick="closeRegisterVendorModal()">Cancel</button>
            <button type="submit" class="btn-register-vendor" id="btn-submit-vendor">
              <i class="fas fa-check-circle"></i>
              <span>Create Vendor Account</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>

  <!-- Add Vendor Menu Product Modal -->
  <div class="modal-overlay" id="product-modal-overlay" onclick="if(event.target===this) closeAddProductModal()">
    <div class="modal-card" style="max-width:540px;">
      <div class="modal-header">
        <h3 style="font-size:1.1rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
          <i class="fas fa-utensils" style="color:var(--brand)"></i>
          Add Stall Menu Item
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeAddProductModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="add-product-form" onsubmit="handleRegisterProduct(event)" enctype="multipart/form-data">
        <input type="hidden" id="p-vendor-id" name="vendor_id">
        <div class="modal-body" style="display:flex;flex-direction:column;gap:1.25rem;">
          
          <div class="form-field-group">
            <label for="p-vendor-name">Stall / Business Name</label>
            <input type="text" id="p-vendor-name" readonly class="form-input-ctrl" style="background:var(--surface2);cursor:not-allowed;font-weight:800;">
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="p-name">Item Name *</label>
              <input type="text" id="p-name" name="name" required class="form-input-ctrl" placeholder="e.g. Samosa Special, Latte Coffee">
            </div>

            <div class="form-field-group">
              <label for="p-price">Price (Ksh) *</label>
              <input type="number" id="p-price" name="price" required min="0" step="0.01" class="form-input-ctrl" placeholder="e.g. 150">
            </div>
          </div>

          <div class="form-field-group">
            <label for="p-desc">Description</label>
            <textarea id="p-desc" name="description" rows="2" class="form-input-ctrl" placeholder="e.g. Fresh spicy beef samosa with dipping sauce"></textarea>
          </div>

          <div class="form-grid-2">
            <div class="form-field-group">
              <label for="p-stock">Stock Availability</label>
              <select id="p-stock" name="stock_status" class="form-input-ctrl">
                <option value="in_stock">✅ In Stock</option>
                <option value="out_of_stock">❌ Sold Out / Out of Stock</option>
              </select>
            </div>

            <div class="form-field-group">
              <label for="p-image-file">Item Photo File</label>
              <input type="file" id="p-image-file" name="image" accept="image/*" class="form-input-ctrl" style="padding:0.5rem;">
            </div>
          </div>

          <div class="form-field-group">
            <label for="p-image-url">Or Image URL / Gradient Class (Optional)</label>
            <input type="text" id="p-image-url" name="image_url" class="form-input-ctrl" placeholder="https://example.com/item.png or bg-gradient-to-br...">
          </div>

          <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
            <button type="button" class="btn-sync" onclick="closeAddProductModal()">Cancel</button>
            <button type="submit" class="btn-register-vendor" id="btn-submit-product" style="background:#05A357;">
              <i class="fas fa-check-circle"></i>
              <span>Add Menu Item</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
let allVendorsCache = [];

window.addEventListener('DOMContentLoaded', () => {
  loadVendorsTab();
});

async function loadVendorsTab(showToast = false) {
  const syncIcon = document.getElementById('sync-icon');
  if (syncIcon) syncIcon.classList.add('fa-spin');

  try {
    const res = await fetch(`${API_BASE}/admin/vendors`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await res.json();

    if (res.ok && Array.isArray(data)) {
      allVendorsCache = data;
      updateKPISummary(allVendorsCache);
      applyVendorFilters();
      if (showToast) showNotification('Vendor directory synchronized', 'success');
    } else {
      const msg = (data && data.message) ? data.message : 'Failed to fetch vendors data';
      showNotification(msg, 'error');
    }
  } catch (e) {
    showNotification('Network error while loading vendors: ' + (e.message || 'Server connection failed'), 'error');
  } finally {
    if (syncIcon) syncIcon.classList.remove('fa-spin');
  }
}

function updateKPISummary(vendors) {
  const total = vendors.length;
  const activeCount = vendors.filter(v => v.status === 'active').length;
  const inactiveCount = vendors.filter(v => v.status === 'inactive').length;
  const totalRevenue = vendors.reduce((acc, v) => acc + (Number(v.total_revenue) || 0), 0);

  document.getElementById('kpi-total-vendors').textContent = total;
  document.getElementById('kpi-active-vendors').textContent = activeCount;
  document.getElementById('kpi-inactive-vendors').textContent = inactiveCount;
  document.getElementById('kpi-network-revenue').textContent = `Ksh ${totalRevenue.toLocaleString()}`;
}

function applyVendorFilters() {
  const query = (document.getElementById('vendor-search')?.value || '').toLowerCase().trim();
  const statusFilter = document.getElementById('filter-status')?.value || '';

  const filtered = allVendorsCache.filter(v => {
    const nameMatch = (v.business_name || '').toLowerCase().includes(query) || 
                      (v.user?.name || '').toLowerCase().includes(query) ||
                      (v.user?.email || '').toLowerCase().includes(query);
    const statusMatch = statusFilter === '' || v.status === statusFilter;
    return nameMatch && statusMatch;
  });

  renderVendorsUI(filtered);
}

function renderVendorsUI(vendors) {
  const container = document.getElementById('all-vendors-cards');
  if (!container) return;
  container.innerHTML = '';

  if (!vendors.length) {
    container.innerHTML = `
      <div style="grid-column:1/-1;text-align:center;padding:4rem;background:var(--surface);border:1px dashed var(--border);border-radius:24px;color:var(--muted)">
        <i class="fas fa-store-slash fa-3x" style="margin-bottom:1rem;color:var(--muted);"></i>
        <h4 style="font-weight:900;color:var(--text);margin-bottom:0.25rem;">No Vendors Found</h4>
        <p style="font-size:0.85rem;font-weight:600;">No vendor records match your search or filter options.</p>
      </div>
    `;
    return;
  }

  vendors.forEach(v => {
    const isInactive = v.status === 'inactive';
    const card = document.createElement('div');
    card.className = `vendor-vcard ${isInactive ? 'is-inactive' : ''}`;
    card.id = `vendor-card-${v.id}`;
    
    const productsCount = v.products?.length || 0;
    const eventName = v.event?.name || 'Default Event';

    let logoTag = '🍔';
    if (v.logo_url && (v.logo_url.startsWith('/') || v.logo_url.startsWith('http'))) {
      logoTag = `<img src="${v.logo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;" alt="${escapeHtml(v.business_name)}">`;
    } else if (v.logo_url) {
      logoTag = v.logo_url;
    }

    card.innerHTML = `
      {{-- Card Header --}}
      <div class="vcard-head">
        <div class="vcard-logo-meta">
          <div class="vcard-logo" style="overflow:hidden;display:flex;align-items:center;justify-content:center;">${logoTag}</div>
          <div class="vcard-title">
            <h4>${escapeHtml(v.business_name)}</h4>
            <div class="v-sub">
              <i class="fas fa-envelope text-xs"></i> ${escapeHtml(v.user?.email || '—')}
            </div>
            ${v.user?.phone ? `<div class="v-sub" style="margin-top:2px;"><i class="fas fa-phone text-xs"></i> ${escapeHtml(v.user.phone)}</div>` : ''}
          </div>
        </div>

        <span class="status-pill ${isInactive ? 'pill-inactive' : 'pill-active'}" id="status-pill-${v.id}">
          ${isInactive 
            ? '<i class="fas fa-ban mr-1"></i> INACTIVE' 
            : '<div class="live-dot" style="display:inline-block;margin-right:4px;"></div> ACTIVE'
          }
        </span>
      </div>

      {{-- Metrics Grid --}}
      <div class="vcard-body-stats">
        <div class="vstat-box">
          <div class="vstat-lbl">Total Revenue</div>
          <div class="vstat-num" style="color:var(--brand2);">Ksh ${Number(v.total_revenue).toLocaleString()}</div>
        </div>
        <div class="vstat-box">
          <div class="vstat-lbl">Total Orders</div>
          <div class="vstat-num">${v.orders_count}</div>
        </div>
      </div>

      {{-- Event Info --}}
      <div class="vcard-event-tag">
        <span><i class="fas fa-calendar-alt"></i> Event: <strong>${escapeHtml(eventName)}</strong></span>
        <span style="font-size:0.7rem;color:var(--muted);"><i class="fas fa-utensils"></i> ${productsCount} Items</span>
      </div>

      {{-- Collapsible Menu items --}}
      <button type="button" class="vcard-drawer-btn" onclick="toggleVendorDrawer(${v.id})">
        <span><i class="fas fa-list-ul mr-1"></i> View Menu Catalog (${productsCount})</span>
        <i class="fas fa-chevron-down" id="v-arrow-${v.id}"></i>
      </button>

      <div class="vcard-menu-drawer" id="v-menu-${v.id}">
        ${productsCount > 0 
          ? v.products.map(p => {
              let itemImg = '';
              if (p.image_url && (p.image_url.startsWith('/') || p.image_url.startsWith('http'))) {
                itemImg = `<img src="${p.image_url}" style="width:36px;height:36px;object-fit:cover;border-radius:8px;" alt="${escapeHtml(p.name)}">`;
              } else {
                itemImg = `<div style="width:36px;height:36px;border-radius:8px;background:var(--brand2);color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.75rem;">🍽️</div>`;
              }
              return `
              <div class="menu-drawer-item" style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
                <div style="display:flex;align-items:center;gap:0.6rem;min-width:0;">
                  ${itemImg}
                  <div style="min-width:0;">
                    <strong style="color:var(--text);font-size:0.8rem;display:block;" class="truncate">${escapeHtml(p.name)}</strong>
                    <div style="font-size:0.68rem;color:var(--muted);" class="truncate">${escapeHtml(p.description || 'No description')}</div>
                  </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                  <strong style="color:var(--brand);font-size:0.8rem;">Ksh ${Number(p.price).toLocaleString()}</strong>
                  <div style="display:flex;align-items:center;gap:0.3rem;justify-content:flex-end;margin-top:2px;">
                    <span class="status-pill ${p.stock_status === 'out_of_stock' ? 'pill-inactive' : 'pill-active'}" style="padding:1px 6px;font-size:0.58rem;">
                      ${p.stock_status === 'out_of_stock' ? 'Sold Out' : 'In Stock'}
                    </span>
                    <button type="button" onclick="deleteVendorProduct(${p.id})" title="Delete Menu Item" style="padding:2px 6px;font-size:0.65rem;background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5;border-radius:6px;cursor:pointer;font-weight:700;">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            `}).join('')
          : `<div style="padding:1.25rem 1rem;text-align:center;color:var(--muted);font-weight:600;display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
              <span>No menu items in catalog yet.</span>
              <button type="button" style="padding:5px 14px;background:#05A357;color:#FFF;border:none;border-radius:20px;font-size:0.72rem;font-weight:800;cursor:pointer;" onclick="openAddProductModal(${v.id}, '${escapeJs(v.business_name)}')">
                <i class="fas fa-plus mr-1"></i> Add First Menu Item
              </button>
             </div>`
        }
      </div>

      {{-- Action Buttons --}}
      <div class="vcard-actions" style="display:flex;gap:0.5rem;">
        <button 
          type="button" 
          class="btn-toggle-status" 
          style="background:#05A357;color:#FFF;border:none;flex:1;"
          onclick="openAddProductModal(${v.id}, '${escapeJs(v.business_name)}')"
        >
          <i class="fas fa-plus"></i> Add Menu Item
        </button>
        ${isInactive 
          ? `<button 
              type="button" 
              class="btn-toggle-status activate" 
              style="flex:1;"
              id="btn-status-${v.id}" 
              onclick="toggleVendorStatus(${v.id}, 'active', '${escapeJs(v.business_name)}')"
             >
              <i class="fas fa-check-circle"></i> Activate Account
             </button>`
          : `<button 
              type="button" 
              class="btn-toggle-status deactivate" 
              style="flex:1;"
              id="btn-status-${v.id}" 
              onclick="toggleVendorStatus(${v.id}, 'inactive', '${escapeJs(v.business_name)}')"
             >
              <i class="fas fa-power-off"></i> Deactivate Account
             </button>`
        }
      </div>
    `;

    container.appendChild(card);
  });
}

function toggleVendorDrawer(vId) {
  const menu = document.getElementById(`v-menu-${vId}`);
  const arrow = document.getElementById(`v-arrow-${vId}`);
  if (!menu || !arrow) return;

  if (menu.style.display === 'block') {
    menu.style.display = 'none';
    arrow.className = 'fas fa-chevron-down';
  } else {
    menu.style.display = 'block';
    arrow.className = 'fas fa-chevron-up';
  }
}

async function toggleVendorStatus(vendorId, newStatus, businessName) {
  const btn = document.getElementById(`btn-status-${vendorId}`);
  if (!btn) return;

  const originalContent = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Updating Status...`;

  try {
    const res = await fetch(`${API_BASE}/admin/vendors/${vendorId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ status: newStatus })
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      // Update local cache
      const vendorObj = allVendorsCache.find(v => v.id === vendorId);
      if (vendorObj) {
        vendorObj.status = newStatus;
      }

      updateKPISummary(allVendorsCache);
      applyVendorFilters();

      const actionText = newStatus === 'active' ? 'activated' : 'deactivated';
      showNotification(`Vendor "${businessName}" has been successfully ${actionText}.`, 'success');
    } else {
      showNotification(data.message || 'Failed to update vendor status', 'error');
      btn.disabled = false;
      btn.innerHTML = originalContent;
    }
  } catch (e) {
    showNotification('Network error while updating vendor status', 'error');
    btn.disabled = false;
    btn.innerHTML = originalContent;
  }
}

function showNotification(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  
  const icon = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
  
  toast.innerHTML = `
    <div class="toast-icon">
      <i class="fas ${icon}"></i>
    </div>
    <span>${escapeHtml(message)}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function escapeJs(str) {
  if (!str) return '';
  return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
}

async function openRegisterVendorModal() {
  const modal = document.getElementById('vendor-modal-overlay');
  if (modal) modal.classList.add('is-active');
  await loadEventsForVendorModal();
}

function closeRegisterVendorModal() {
  const modal = document.getElementById('vendor-modal-overlay');
  if (modal) modal.classList.remove('is-active');
  const form = document.getElementById('register-vendor-form');
  if (form) form.reset();
}

async function loadEventsForVendorModal() {
  const select = document.getElementById('v-event-id');
  if (!select) return;

  try {
    const res = await fetch(`${API_BASE}/admin/events`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (res.ok) {
      const events = await res.json();
      select.innerHTML = Array.isArray(events) && events.length
        ? events.map(e => `<option value="${e.id}">${escapeHtml(e.name)} (${(e.status || 'active').toUpperCase()})</option>`).join('')
        : '<option value="">Default Main Event</option>';
    } else {
      select.innerHTML = '<option value="">Default Main Event</option>';
    }
  } catch (e) {
    select.innerHTML = '<option value="">Default Main Event</option>';
  }
}

async function handleRegisterVendor(event) {
  event.preventDefault();
  const form = document.getElementById('register-vendor-form');
  const submitBtn = document.getElementById('btn-submit-vendor');
  if (!form || !submitBtn) return;

  const originalHtml = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Registering Vendor...`;

  const formData = new FormData(form);

  try {
    const res = await fetch(`${API_BASE}/admin/vendors`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: formData
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      showNotification(data.message || 'Vendor account registered successfully!', 'success');
      closeRegisterVendorModal();
      loadVendorsTab();
    } else {
      const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to register vendor account.');
      showNotification(errMsg, 'error');
    }
  } catch (e) {
    showNotification('Network error while registering vendor', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
  }
}

function openAddProductModal(vendorId, businessName) {
  const modal = document.getElementById('product-modal-overlay');
  const vendorIdInput = document.getElementById('p-vendor-id');
  const vendorNameInput = document.getElementById('p-vendor-name');
  const form = document.getElementById('add-product-form');
  
  if (form) form.reset();
  if (vendorIdInput) vendorIdInput.value = vendorId;
  if (vendorNameInput) vendorNameInput.value = businessName;
  if (modal) modal.classList.add('is-active');
}

function closeAddProductModal() {
  const modal = document.getElementById('product-modal-overlay');
  if (modal) modal.classList.remove('is-active');
}

async function handleRegisterProduct(event) {
  event.preventDefault();
  const form = document.getElementById('add-product-form');
  const submitBtn = document.getElementById('btn-submit-product');
  const vendorId = document.getElementById('p-vendor-id').value;
  if (!form || !submitBtn || !vendorId) return;

  const originalHtml = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Adding Menu Item...`;

  const formData = new FormData(form);

  try {
    const res = await fetch(`${API_BASE}/admin/vendors/${vendorId}/products`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: formData
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      showNotification(data.message || 'Menu item added successfully!', 'success');
      closeAddProductModal();
      await loadVendorsTab();
      // Expand vendor's drawer automatically
      const menu = document.getElementById(`v-menu-${vendorId}`);
      const arrow = document.getElementById(`v-arrow-${vendorId}`);
      if (menu) menu.style.display = 'block';
      if (arrow) arrow.className = 'fas fa-chevron-up';
    } else {
      const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to add menu item.');
      showNotification(errMsg, 'error');
    }
  } catch (e) {
    showNotification('Network error while adding menu item', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
  }
}

async function deleteVendorProduct(productId) {
  if (!confirm('Are you sure you want to delete this menu item from the vendor catalog?')) return;

  try {
    const res = await fetch(`${API_BASE}/admin/products/${productId}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });

    const data = await res.json();

    if (res.ok && (data.success || data.status === 'success')) {
      showNotification(data.message || 'Menu item deleted successfully!', 'success');
      loadVendorsTab();
    } else {
      showNotification(data.message || 'Failed to delete menu item', 'error');
    }
  } catch (e) {
    showNotification('Network error while deleting menu item', 'error');
  }
}
</script>
@endsection
