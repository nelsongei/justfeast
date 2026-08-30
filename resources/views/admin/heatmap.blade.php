@extends('admin.layout')

@section('title', 'Interactive Seating Heatmap — JustFeast Admin')
@section('page-title', 'Interactive Seating Heatmap')
@section('page-meta', 'Real-time Leaflet GIS mapping centered on Uhuru Park venue grounds')

@section('content')
  <div class="heatmap-container">
    <div class="stadium-wrap" style="padding: 1.5rem; align-items: stretch;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; width: 100%;">
        <div>
          <h3 style="font-size: .95rem; font-weight: 800; color: var(--text);">Uhuru Park GIS Heatmap & Venue Section Manager</h3>
          <p style="font-size: .75rem; color: var(--muted); margin-top: 2px;">Real-time Leaflet GIS mapping, custom section drawing, and stage location management</p>
        </div>
        <div class="heat-legend">
          <div class="legend-item"><div class="legend-color" style="background:#05A357"></div> Low (1-2)</div>
          <div class="legend-item"><div class="legend-color" style="background:#FFC244"></div> Med (3-5)</div>
          <div class="legend-item"><div class="legend-color" style="background:#A31D1D"></div> High (6+)</div>
        </div>
      </div>

      <!-- Section Filter Pills & Drawing Toolbar -->
      <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.8rem; align-items: center;" id="section-pills-bar">
        <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-right: 0.2rem;"><i class="fas fa-layer-group text-[#A31D1D] mr-1"></i> Venue Sections:</span>
        <button type="button" onclick="selectStadiumSection('all')" id="sec-pill-all" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--brand); background: var(--brand); color: #FFF;">All Sections</button>
        <button type="button" onclick="selectStadiumSection('vip_a')" id="sec-pill-vip_a" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🌟 VIP A</button>
        <button type="button" onclick="selectStadiumSection('vip_b')" id="sec-pill-vip_b" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">⭐ VIP B</button>
        <button type="button" onclick="selectStadiumSection('gen_a')" id="sec-pill-gen_a" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🎪 GEN A</button>
        <button type="button" onclick="selectStadiumSection('gen_b')" id="sec-pill-gen_b" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🎪 GEN B</button>
        <button type="button" onclick="toggleDrawingMode()" id="btn-draw-mode" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px dashed var(--brand); background: #FFF8E7; color: var(--brand); transition: all 0.2s;"><i class="fas fa-pen-ruler mr-1"></i> ✏️ Draw Custom Section</button>
        <button type="button" onclick="openEditStageModal()" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid #A31D1D; background: #FEF2F2; color: #991B1B; transition: all 0.2s;"><i class="fas fa-music mr-1"></i> 🎭 Edit Stage Location</button>
        <button type="button" onclick="resetVenueLayout()" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--muted);"><i class="fas fa-rotate-left mr-1"></i> Reset Layout</button>
      </div>

      <!-- Active Drawing Mode Floating Banner -->
      <div id="drawing-banner" style="display: none; background: #FFF8E7; border: 1px solid #FFC244; color: #0F172A; padding: 8px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.8rem; align-items: center; justify-content: space-between;">
        <span><i class="fas fa-hand-pointer text-[#A31D1D] mr-1.5"></i> <strong>Drawing Mode Active:</strong> Click points on the map to outline your new venue section boundary.</span>
        <div style="display: flex; gap: 0.4rem;">
          <button type="button" onclick="finishDrawingSection()" style="padding: 3px 10px; background: #A31D1D; color: #FFF; border: none; border-radius: 20px; font-size: 0.7rem; font-weight: 800; cursor: pointer;"><i class="fas fa-check mr-1"></i> Finish Section</button>
          <button type="button" onclick="cancelDrawingSection()" style="padding: 3px 10px; background: #E2E8F0; color: #475569; border: none; border-radius: 20px; font-size: 0.7rem; font-weight: 800; cursor: pointer;">Cancel</button>
        </div>
      </div>

      <!-- Leaflet GIS Map Container -->
      <div id="admin-heatmap-map" style="width: 100%; height: 480px; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; position: relative; z-index: 1;"></div>
    </div>

    {{-- Section Details Panel --}}
    <div class="card" style="padding:1.5rem;display:flex;flex-direction:column;gap:1.2rem">
      <h3 style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Stadium Area Statistics</h3>
      
      <div style="background:#F8FAFC;padding:1rem;border-radius:12px;border:1px solid var(--border)">
        <h4 style="font-size:.9rem;font-weight:800;margin-bottom:.2rem;color:var(--text)" id="h-sec-name">All Venue Sections</h4>
        <p style="font-size:.72rem;color:var(--muted)">Click a section pill or polygon on Uhuru Park map to inspect stats</p>
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

      <div id="section-action-box" style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border); display: none;">
        <button type="button" onclick="deleteCurrentSelectedSection()" style="width: 100%; padding: 8px; background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 12px; font-size: 0.75rem; font-weight: 800; cursor: pointer;"><i class="fas fa-trash mr-1"></i> Delete Custom Section</button>
      </div>
    </div>
  </div>

  <!-- Edit Stage Location Modal -->
  <div class="modal-overlay" id="edit-stage-modal-overlay" onclick="if(event.target===this) closeEditStageModal()">
    <div class="modal-card" style="max-width:480px;">
      <div class="modal-header">
        <h3 style="font-size:1.1rem;font-weight:900;color:var(--text);display:flex;align-items:center;gap:0.5rem;">
          <i class="fas fa-music" style="color:var(--brand)"></i>
          Edit Main Stage Location
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeEditStageModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="edit-stage-form" onsubmit="handleSaveStageLocation(event)">
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.1rem;">
          
          <div>
            <label style="display:block;font-size:.72rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Stage Title / Label *</label>
            <input type="text" id="stage-name-input" required placeholder="Main Stage Grounds" style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
          </div>

          <div>
            <label style="display:block;font-size:.72rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Sub-location / Description</label>
            <input type="text" id="stage-desc-input" placeholder="Uhuru Park, Cathedral Road Entrance" style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:600;">
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
            <div>
              <label style="display:block;font-size:.72rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Latitude *</label>
              <input type="number" step="any" id="stage-lat-input" required style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
            </div>
            <div>
              <label style="display:block;font-size:.72rem;font-weight:800;text-transform:uppercase;color:var(--muted);margin-bottom:.35rem;">Longitude *</label>
              <input type="number" step="any" id="stage-lng-input" required style="width:100%;padding:.65rem .85rem;border-radius:10px;background:var(--surface2);border:1px solid var(--border);color:var(--text);outline:none;font-weight:700;">
            </div>
          </div>

          <div style="background:#FFF8E7;border:1px solid #F7E5B2;padding:0.75rem 1rem;border-radius:12px;font-size:0.75rem;color:#0F172A;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
            <span><i class="fas fa-location-crosshairs text-brand mr-1"></i> Pick coords directly on map:</span>
            <button type="button" onclick="enableMapPickForStage()" style="padding:0.35rem 0.85rem;background:var(--brand);color:#FFF;border:none;border-radius:20px;font-size:0.7rem;font-weight:800;cursor:pointer;">
              📍 Pick Coords on Map
            </button>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border);">
            <button type="button" class="btn-page" onclick="closeEditStageModal()">Cancel</button>
            <button type="submit" id="btn-save-stage" style="padding:.65rem 1.4rem;background:#05A357;color:#FFF;border:none;border-radius:12px;font-weight:800;font-size:.82rem;cursor:pointer;display:flex;align-items:center;gap:0.4rem;">
              <i class="fas fa-save"></i> Save Stage Location
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
let adminMap = null;
let stageMarkerInstance = null;
let mapPolygons = {};
let mapOrderMarkers = [];
let selectedSectionKey = 'all';
let cachedStats = null;
let isPickingStageCoordsOnMap = false;

let stageConfig = {
  name: 'Main Stage Grounds',
  description: 'Uhuru Park, Cathedral Road Entrance',
  latitude: -1.28817042,
  longitude: 36.81647301
};

// Drawing mode state
let isDrawingMode = false;
let draftPoints = [];
let draftPolyline = null;
let draftMarkers = [];
let customSections = {};

window.addEventListener('DOMContentLoaded', () => {
  fetchStageConfig();
  setTimeout(() => {
    initAdminHeatmapMap();
    syncHeatmapData();
  }, 100);
  setInterval(syncHeatmapData, 5000);
});

async function fetchStageConfig() {
  try {
    const res = await fetch(`${API_BASE}/admin/settings`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (res.ok) {
      const data = await res.json();
      if (data.settings && data.settings.stage_location) {
        stageConfig = data.settings.stage_location;
        if (adminMap) {
          renderStageMarkerOnMap();
        }
      }
    }
  } catch(e) {}
}

async function syncHeatmapData() {
  try {
    const res = await fetch(`${API_BASE}/admin/stats`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (res.ok) {
      cachedStats = await res.json();
      updateHeatmapUI();
    }
  } catch(e) {}
}

function initAdminHeatmapMap() {
  if (adminMap) {
    adminMap.invalidateSize();
    return;
  }

  const mapContainer = document.getElementById('admin-heatmap-map');
  if (!mapContainer) return;

  adminMap = L.map('admin-heatmap-map', {
    zoomControl: true,
    scrollWheelZoom: true
  }).setView([stageConfig.latitude, stageConfig.longitude], 17);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(adminMap);

  renderStageMarkerOnMap();

  adminMap.on('click', handleAdminMapClick);

  const savedCustom = localStorage.getItem('justfeast_custom_sections');
  if (savedCustom) {
    try {
      customSections = JSON.parse(savedCustom);
    } catch(e) {}
  }

  const baseSectorGeometries = {
    vip_a: {
      name: "VIP Section A",
      coords: [[-1.2872, 36.8155], [-1.2872, 36.8164], [-1.2880, 36.8164], [-1.2880, 36.8155]]
    },
    vip_b: {
      name: "VIP Section B",
      coords: [[-1.2872, 36.8165], [-1.2872, 36.8175], [-1.2880, 36.8175], [-1.2880, 36.8165]]
    },
    gen_a: {
      name: "General Arena A",
      coords: [[-1.2882, 36.8155], [-1.2882, 36.8164], [-1.2892, 36.8164], [-1.2892, 36.8155]]
    },
    gen_b: {
      name: "General Arena B",
      coords: [[-1.2882, 36.8165], [-1.2882, 36.8175], [-1.2892, 36.8175], [-1.2892, 36.8165]]
    }
  };

  const allSectors = { ...baseSectorGeometries, ...customSections };
  Object.keys(allSectors).forEach(secKey => {
    renderSectorPolygonOnMap(secKey, allSectors[secKey]);
  });

  renderSectionPillsToolbar();
}

function renderStageMarkerOnMap() {
  if (!adminMap) return;

  if (stageMarkerInstance) {
    adminMap.removeLayer(stageMarkerInstance);
  }

  const labelText = stageConfig.name.toUpperCase();
  const stageIcon = L.divIcon({
    className: 'custom-stage-pin',
    html: `<div style="background:#A31D1D; color:#FFF; font-weight:900; font-size:10px; padding:6px 12px; border-radius:20px; border:2px solid #FFC244; box-shadow:0 4px 14px rgba(0,0,0,0.35); white-space:nowrap; cursor:grab;"><i class="fas fa-music mr-1"></i> 🎵 ${labelText}</div>`,
    iconSize: [200, 30],
    iconAnchor: [100, 15]
  });

  stageMarkerInstance = L.marker([stageConfig.latitude, stageConfig.longitude], {
    icon: stageIcon,
    draggable: true
  }).addTo(adminMap);

  stageMarkerInstance.bindPopup(`<b>${stageConfig.name}</b><br>${stageConfig.description}<br><span style="font-size:10px;color:#64748B;">Drag pin to move stage location</span>`);

  stageMarkerInstance.on('dragend', (e) => {
    const latlng = e.target.getLatLng();
    stageConfig.latitude = parseFloat(latlng.lat.toFixed(8));
    stageConfig.longitude = parseFloat(latlng.lng.toFixed(8));
    
    // Update modal inputs if modal open
    if (document.getElementById('stage-lat-input')) document.getElementById('stage-lat-input').value = stageConfig.latitude;
    if (document.getElementById('stage-lng-input')) document.getElementById('stage-lng-input').value = stageConfig.longitude;

    if (typeof showNotification === 'function') {
      showNotification(`Stage pin moved to ${stageConfig.latitude}, ${stageConfig.longitude}. Click 'Edit Stage Location' to save!`, 'info');
    }
  });
}

function openEditStageModal() {
  const modal = document.getElementById('edit-stage-modal-overlay');
  const nameIn = document.getElementById('stage-name-input');
  const descIn = document.getElementById('stage-desc-input');
  const latIn = document.getElementById('stage-lat-input');
  const lngIn = document.getElementById('stage-lng-input');

  if (nameIn) nameIn.value = stageConfig.name || 'Main Stage Grounds';
  if (descIn) descIn.value = stageConfig.description || 'Uhuru Park, Cathedral Road Entrance';
  if (latIn) latIn.value = stageConfig.latitude;
  if (lngIn) lngIn.value = stageConfig.longitude;

  if (modal) modal.classList.add('is-active');
}

function closeEditStageModal() {
  const modal = document.getElementById('edit-stage-modal-overlay');
  if (modal) modal.classList.remove('is-active');
  isPickingStageCoordsOnMap = false;
}

function enableMapPickForStage() {
  closeEditStageModal();
  isPickingStageCoordsOnMap = true;
  alert("📍 Click anywhere on the map to set the new Stage Location!");
}

async function handleSaveStageLocation(event) {
  event.preventDefault();
  const name = document.getElementById('stage-name-input').value.trim();
  const description = document.getElementById('stage-desc-input').value.trim();
  const latitude = parseFloat(document.getElementById('stage-lat-input').value);
  const longitude = parseFloat(document.getElementById('stage-lng-input').value);

  const btn = document.getElementById('btn-save-stage');
  if (!btn) return;

  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Saving...`;

  stageConfig = { name, description, latitude, longitude };

  try {
    const res = await fetch(`${API_BASE}/admin/settings`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ stage_location: stageConfig })
    });

    const data = await res.json();

    if (res.ok && (data.status === 'success' || data.success)) {
      renderStageMarkerOnMap();
      adminMap.panTo([latitude, longitude]);
      closeEditStageModal();
      alert(`Stage location updated successfully to '${name}'!`);
    } else {
      alert(data.message || 'Failed to save stage location.');
    }
  } catch(e) {
    alert('Network error while saving stage location.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
  }
}

function handleAdminMapClick(e) {
  if (isPickingStageCoordsOnMap) {
    isPickingStageCoordsOnMap = false;
    stageConfig.latitude = parseFloat(e.latlng.lat.toFixed(8));
    stageConfig.longitude = parseFloat(e.latlng.lng.toFixed(8));
    renderStageMarkerOnMap();
    openEditStageModal();
    return;
  }

  if (!isDrawingMode) return;
  draftPoints.push([e.latlng.lat, e.latlng.lng]);

  const marker = L.circleMarker(e.latlng, {
    radius: 5,
    color: '#A31D1D',
    fillColor: '#FFC244',
    fillOpacity: 1
  }).addTo(adminMap);
  draftMarkers.push(marker);

  if (draftPolyline) {
    adminMap.removeLayer(draftPolyline);
  }
  draftPolyline = L.polyline(draftPoints, { color: '#A31D1D', dashArray: '4, 4' }).addTo(adminMap);
}

function renderSectorPolygonOnMap(secKey, sec) {
  if (mapPolygons[secKey]) {
    adminMap.removeLayer(mapPolygons[secKey]);
  }

  const poly = L.polygon(sec.coords, {
    color: '#A31D1D',
    weight: 2,
    fillColor: '#FFC244',
    fillOpacity: 0.3
  }).addTo(adminMap);

  poly.bindTooltip(`<b>${sec.name}</b>`, { sticky: true, className: 'clean-sec-tooltip' });
  poly.on('click', (e) => {
    L.DomEvent.stopPropagation(e);
    selectStadiumSection(secKey);
  });

  mapPolygons[secKey] = poly;
}

function renderSectionPillsToolbar() {
  const bar = document.getElementById('section-pills-bar');
  if (!bar) return;

  const baseSectors = ['vip_a', 'vip_b', 'gen_a', 'gen_b'];
  const baseIds = new Set(['sec-pill-all', 'sec-pill-vip_a', 'sec-pill-vip_b', 'sec-pill-gen_a', 'sec-pill-gen_b', 'btn-draw-mode']);

  Array.from(bar.children).forEach(child => {
    if (child.tagName === 'BUTTON' && !baseIds.has(child.id) && !child.textContent.includes('Reset') && !child.textContent.includes('Edit Stage')) {
      child.remove();
    }
  });

  Object.keys(customSections).forEach(key => {
    const sec = customSections[key];
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'sec-pill';
    btn.id = `sec-pill-${key}`;
    btn.style.cssText = 'padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);';
    btn.innerHTML = `📐 ${sec.name}`;
    btn.onclick = () => selectStadiumSection(key);
    
    const drawBtn = document.getElementById('btn-draw-mode');
    bar.insertBefore(btn, drawBtn);
  });
}

function selectStadiumSection(key) {
  selectedSectionKey = key;
  document.querySelectorAll('.sec-pill').forEach(btn => {
    btn.style.background = 'var(--surface2)';
    btn.style.color = 'var(--text)';
    btn.style.borderColor = 'var(--border)';
  });

  const activeBtn = document.getElementById(`sec-pill-${key}`);
  if (activeBtn) {
    activeBtn.style.background = 'var(--brand)';
    activeBtn.style.color = '#FFF';
    activeBtn.style.borderColor = 'var(--brand)';
  }

  const actionBox = document.getElementById('section-action-box');
  if (actionBox) {
    actionBox.style.display = customSections[key] ? 'block' : 'none';
  }

  updateHeatmapUI();
}

function updateHeatmapUI() {
  if (!cachedStats) return;

  const nameEl = document.getElementById('h-sec-name');
  const ordersEl = document.getElementById('h-sec-orders');
  const revEl = document.getElementById('h-sec-rev');
  const avgEl = document.getElementById('h-sec-avg');
  const ratingEl = document.getElementById('h-sec-rating');

  const heat = cachedStats.section_heatmap || {};
  
  if (selectedSectionKey === 'all') {
    nameEl.textContent = 'All Venue Sections';
    const totalOrders = cachedStats.orders_count || 0;
    const totalRev = cachedStats.total_revenue || 0;
    const avgOrder = totalOrders > 0 ? (totalRev / totalOrders) : 0;

    ordersEl.textContent = totalOrders;
    revEl.textContent = `Ksh ${Number(totalRev).toLocaleString()}`;
    avgEl.textContent = `Ksh ${Math.round(avgOrder).toLocaleString()}`;
    ratingEl.textContent = totalOrders > 5 ? 'High Volume' : totalOrders > 2 ? 'Medium Volume' : 'Optimal';
    ratingEl.className = `status-pill ${totalOrders > 5 ? 's-preparing' : 's-ready'}`;
  } else {
    const counts = heat[selectedSectionKey] || 0;
    const secNameMap = { vip_a: 'VIP Section A', vip_b: 'VIP Section B', gen_a: 'General Arena A', gen_b: 'General Arena B' };
    const secName = customSections[selectedSectionKey]?.name || secNameMap[selectedSectionKey] || 'Custom Section';

    nameEl.textContent = secName;
    ordersEl.textContent = counts;
    const estimatedRev = counts * 850;
    revEl.textContent = `Ksh ${Number(estimatedRev).toLocaleString()}`;
    avgEl.textContent = counts > 0 ? 'Ksh 850' : '—';
    ratingEl.textContent = counts > 5 ? 'High (6+)' : counts > 2 ? 'Med (3-5)' : 'Low (1-2)';
    ratingEl.className = `status-pill ${counts > 5 ? 's-preparing' : counts > 2 ? 's-ready' : 's-created'}`;
  }

  mapOrderMarkers.forEach(m => adminMap.removeLayer(m));
  mapOrderMarkers = [];

  const recent = cachedStats.recent_orders || [];
  recent.forEach(order => {
    const loc = order.seat_location || {};
    let lat = null, lng = null;

    if (loc.latitude && loc.longitude) {
      lat = parseFloat(loc.latitude);
      lng = parseFloat(loc.longitude);
    } else {
      const sec = (loc.section || '').toLowerCase();
      if (sec.includes('vip') && sec.includes('a')) { lat = -1.2876; lng = 36.8159; }
      else if (sec.includes('vip') && sec.includes('b')) { lat = -1.2876; lng = 36.8170; }
      else if (sec.includes('gen') && sec.includes('a')) { lat = -1.2887; lng = 36.8159; }
      else { lat = -1.2887; lng = 36.8170; }
    }

    if (lat && lng) {
      const marker = L.circleMarker([lat, lng], {
        radius: 7,
        color: '#FFFFFF',
        weight: 1.5,
        fillColor: '#A31D1D',
        fillOpacity: 0.9
      }).addTo(adminMap);
      
      marker.bindPopup(`<b>Order #${order.id}</b><br>Amount: Ksh ${Number(order.total_amount).toLocaleString()}<br>Status: ${order.order_status}`);
      mapOrderMarkers.push(marker);
    }
  });
}

function toggleDrawingMode() {
  isDrawingMode = !isDrawingMode;
  const banner = document.getElementById('drawing-banner');
  const btn = document.getElementById('btn-draw-mode');

  if (isDrawingMode) {
    banner.style.display = 'flex';
    btn.style.background = '#A31D1D';
    btn.style.color = '#FFF';
    btn.style.borderColor = '#A31D1D';
    btn.innerHTML = `<i class="fas fa-times mr-1"></i> Cancel Drawing`;
    draftPoints = [];
  } else {
    cancelDrawingSection();
  }
}

function cancelDrawingSection() {
  isDrawingMode = false;
  const banner = document.getElementById('drawing-banner');
  const btn = document.getElementById('btn-draw-mode');

  banner.style.display = 'none';
  btn.style.background = '#FFF8E7';
  btn.style.color = 'var(--brand)';
  btn.style.borderColor = 'var(--brand)';
  btn.innerHTML = `<i class="fas fa-pen-ruler mr-1"></i> ✏️ Draw Custom Section`;

  if (draftPolyline) {
    adminMap.removeLayer(draftPolyline);
    draftPolyline = null;
  }
  draftMarkers.forEach(m => adminMap.removeLayer(m));
  draftMarkers = [];
  draftPoints = [];
}

function finishDrawingSection() {
  if (draftPoints.length < 3) {
    alert("Please click at least 3 points on the map to outline a valid venue polygon section.");
    return;
  }

  const secName = prompt("Enter a name for this custom venue section (e.g. VIP Terrace A, Backstage Concourse):");
  if (!secName || !secName.trim()) {
    return;
  }

  const secKey = 'custom_' + Date.now();
  const newSec = {
    name: secName.trim(),
    coords: draftPoints
  };

  customSections[secKey] = newSec;
  localStorage.setItem('justfeast_custom_sections', JSON.stringify(customSections));

  renderSectorPolygonOnMap(secKey, newSec);
  renderSectionPillsToolbar();
  cancelDrawingSection();
  selectStadiumSection(secKey);

  alert(`Custom section '${secName}' created and saved successfully!`);
}

function deleteCurrentSelectedSection() {
  if (!customSections[selectedSectionKey]) return;

  const secName = customSections[selectedSectionKey].name;
  if (!confirm(`Are you sure you want to delete custom section '${secName}'?`)) return;

  if (mapPolygons[selectedSectionKey]) {
    adminMap.removeLayer(mapPolygons[selectedSectionKey]);
    delete mapPolygons[selectedSectionKey];
  }

  delete customSections[selectedSectionKey];
  localStorage.setItem('justfeast_custom_sections', JSON.stringify(customSections));

  renderSectionPillsToolbar();
  selectStadiumSection('all');
  alert(`Custom section '${secName}' deleted successfully.`);
}

function resetVenueLayout() {
  if (!confirm("Are you sure you want to reset custom venue section polygons to default?")) return;
  localStorage.removeItem('justfeast_custom_sections');
  customSections = {};
  location.reload();
}
</script>
@endsection
