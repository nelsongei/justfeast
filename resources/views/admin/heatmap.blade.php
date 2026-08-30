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
          <p style="font-size: .75rem; color: var(--muted); margin-top: 2px;">Real-time Leaflet GIS mapping, custom section drawing, and order volume analytics</p>
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
@endsection

@section('scripts')
<script>
let adminMap = null;
let mapPolygons = {};
let mapOrderMarkers = [];
let selectedSectionKey = 'all';
let cachedStats = null;

// Drawing mode state
let isDrawingMode = false;
let draftPoints = [];
let draftPolyline = null;
let draftMarkers = [];
let customSections = {};

window.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    initAdminHeatmapMap();
    syncHeatmapData();
  }, 100);
  setInterval(syncHeatmapData, 5000);
});

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
  }).setView([-1.28817042, 36.81647301], 17);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(adminMap);

  const stageIcon = L.divIcon({
    className: 'custom-stage-pin',
    html: `<div style="background:#A31D1D; color:#FFF; font-weight:900; font-size:10px; padding:6px 12px; border-radius:20px; border:2px solid #FFC244; box-shadow:0 4px 14px rgba(0,0,0,0.3); white-space:nowrap;"><i class="fas fa-music mr-1"></i> MAIN STAGE (UHURU PARK)</div>`,
    iconSize: [170, 30],
    iconAnchor: [85, 15]
  });
  L.marker([-1.28817042, 36.81647301], { icon: stageIcon }).addTo(adminMap).bindPopup("<b>Main Stage Grounds</b><br>Uhuru Park, Cathedral Road Entrance");

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

  const basePills = `
    <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--muted); margin-right: 0.2rem;"><i class="fas fa-layer-group text-[#A31D1D] mr-1"></i> Venue Sections:</span>
    <button type="button" onclick="selectStadiumSection('all')" id="sec-pill-all" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--brand); background: var(--brand); color: #FFF;">All Sections</button>
    <button type="button" onclick="selectStadiumSection('vip_a')" id="sec-pill-vip_a" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🌟 VIP A</button>
    <button type="button" onclick="selectStadiumSection('vip_b')" id="sec-pill-vip_b" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">⭐ VIP B</button>
    <button type="button" onclick="selectStadiumSection('gen_a')" id="sec-pill-gen_a" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🎪 GEN A</button>
    <button type="button" onclick="selectStadiumSection('gen_b')" id="sec-pill-gen_b" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--text);">🎪 GEN B</button>
  `;

  let customPills = '';
  Object.keys(customSections).forEach(key => {
    const sec = customSections[key];
    customPills += `<button type="button" onclick="selectStadiumSection('${key}')" id="sec-pill-${key}" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid #FFC244; background: #FFF8E7; color: #0F172A;">📍 ${sec.name}</button>`;
  });

  const toolPills = `
    <button type="button" onclick="toggleDrawingMode()" id="btn-draw-mode" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px dashed var(--brand); background: #FFF8E7; color: var(--brand); transition: all 0.2s;"><i class="fas fa-pen-ruler mr-1"></i> ✏️ Draw Custom Section</button>
    <button type="button" onclick="resetVenueLayout()" class="sec-pill" style="padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; cursor: pointer; border: 1px solid var(--border); background: var(--surface2); color: var(--muted);"><i class="fas fa-rotate-left mr-1"></i> Reset Layout</button>
  `;

  bar.innerHTML = basePills + customPills + toolPills;
  highlightActivePill(selectedSectionKey);
}

function toggleDrawingMode() {
  isDrawingMode = !isDrawingMode;
  const btn = document.getElementById('btn-draw-mode');
  const banner = document.getElementById('drawing-banner');

  if (isDrawingMode) {
    btn.style.background = '#A31D1D';
    btn.style.color = '#FFFFFF';
    banner.style.display = 'flex';
    draftPoints = [];
    adminMap.getContainer().style.cursor = 'crosshair';
  } else {
    cancelDrawingSection();
  }
}

function handleAdminMapClick(e) {
  if (!isDrawingMode) return;

  const latlng = [e.latlng.lat, e.latlng.lng];
  draftPoints.push(latlng);

  const dot = L.circleMarker(latlng, {
    radius: 5,
    fillColor: '#A31D1D',
    color: '#FFF',
    weight: 2,
    fillOpacity: 1
  }).addTo(adminMap);
  draftMarkers.push(dot);

  if (draftPolyline) adminMap.removeLayer(draftPolyline);
  draftPolyline = L.polyline(draftPoints, { color: '#A31D1D', weight: 3, dashArray: '6, 6' }).addTo(adminMap);
}

function finishDrawingSection() {
  if (draftPoints.length < 3) {
    alert("⚠️ Please click at least 3 points on the map to define a section boundary.");
    return;
  }

  const name = prompt("Enter a name for this custom venue section (e.g. VVIP Terrace, Beer Garden, Food Court):");
  if (!name || !name.trim()) {
    cancelDrawingSection();
    return;
  }

  const key = 'custom_' + Date.now();
  const newSec = {
    name: name.trim(),
    coords: draftPoints
  };

  customSections[key] = newSec;
  localStorage.setItem('justfeast_custom_sections', JSON.stringify(customSections));

  renderSectorPolygonOnMap(key, newSec);
  cancelDrawingSection();
  renderSectionPillsToolbar();
  selectStadiumSection(key);
}

function cancelDrawingSection() {
  isDrawingMode = false;
  adminMap.getContainer().style.cursor = '';
  document.getElementById('drawing-banner').style.display = 'none';

  const btn = document.getElementById('btn-draw-mode');
  if (btn) {
    btn.style.background = '#FFF8E7';
    btn.style.color = 'var(--brand)';
  }

  if (draftPolyline) {
    adminMap.removeLayer(draftPolyline);
    draftPolyline = null;
  }
  draftMarkers.forEach(m => adminMap.removeLayer(m));
  draftMarkers = [];
  draftPoints = [];
}

function deleteCurrentSelectedSection() {
  if (!selectedSectionKey || !customSections[selectedSectionKey]) return;

  if (confirm(`Delete custom section "${customSections[selectedSectionKey].name}"?`)) {
    if (mapPolygons[selectedSectionKey]) {
      adminMap.removeLayer(mapPolygons[selectedSectionKey]);
      delete mapPolygons[selectedSectionKey];
    }
    delete customSections[selectedSectionKey];
    localStorage.setItem('justfeast_custom_sections', JSON.stringify(customSections));

    renderSectionPillsToolbar();
    selectStadiumSection('all');
  }
}

function resetVenueLayout() {
  if (confirm("Reset layout to default Uhuru Park stadium sectors?")) {
    Object.keys(customSections).forEach(key => {
      if (mapPolygons[key]) adminMap.removeLayer(mapPolygons[key]);
    });
    customSections = {};
    localStorage.removeItem('justfeast_custom_sections');
    renderSectionPillsToolbar();
    selectStadiumSection('all');
  }
}

function highlightActivePill(secKey) {
  document.querySelectorAll('.sec-pill').forEach(btn => {
    if (btn.id === `sec-pill-${secKey}`) {
      btn.style.background = '#A31D1D';
      btn.style.color = '#FFFFFF';
      btn.style.borderColor = '#A31D1D';
    } else if (btn.id !== 'btn-draw-mode') {
      btn.style.background = 'var(--surface2)';
      btn.style.color = 'var(--text)';
      btn.style.borderColor = 'var(--border)';
    }
  });
}

function updateHeatmapUI() {
  if (!cachedStats) return;
  const heat = cachedStats.section_heatmap || {};

  Object.keys(mapPolygons).forEach(secKey => {
    const count = heat[secKey] || 0;
    let color = '#64748B';
    let opacity = 0.25;
    if (count > 0 && count <= 2) { color = '#05A357'; opacity = 0.35; }
    else if (count <= 5) { color = '#FFC244'; opacity = 0.5; }
    else if (count > 5) { color = '#A31D1D'; opacity = 0.65; }

    if (mapPolygons[secKey]) {
      mapPolygons[secKey].setStyle({
        fillColor: color,
        color: color,
        fillOpacity: opacity
      });
    }
  });

  if (!adminMap) return;

  mapOrderMarkers.forEach(m => adminMap.removeLayer(m));
  mapOrderMarkers = [];

  if (cachedStats.recent_orders) {
    cachedStats.recent_orders.forEach(o => {
      let lat = -1.28817042 + (Math.random() - 0.5) * 0.002;
      let lng = 36.81647301 + (Math.random() - 0.5) * 0.002;

      if (o.seat_location && o.seat_location.type === 'gps' && o.seat_location.latitude) {
        lat = o.seat_location.latitude;
        lng = o.seat_location.longitude;
      }

      const marker = L.circleMarker([lat, lng], {
        radius: 8,
        fillColor: '#A31D1D',
        color: '#FFFFFF',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.9
      }).addTo(adminMap);

      marker.bindPopup(`
        <div style="font-family:'Outfit',sans-serif; padding:4px;">
          <strong style="color:#A31D1D;">Order #${o.id}</strong><br>
          <b>Customer:</b> ${o.user?.name || 'Guest'}<br>
          <b>Vendor:</b> ${o.vendor?.business_name || 'Stall'}<br>
          <b>Amount:</b> Ksh ${Number(o.total_amount).toLocaleString()}
        </div>
      `);

      mapOrderMarkers.push(marker);
    });
  }
}

function selectStadiumSection(secKey) {
  selectedSectionKey = secKey;
  highlightActivePill(secKey);

  const actionBox = document.getElementById('section-action-box');
  if (actionBox) {
    actionBox.style.display = customSections[secKey] ? 'block' : 'none';
  }

  if (!cachedStats) return;

  if (secKey === 'all') {
    document.getElementById('h-sec-name').textContent = "All Venue Sections";
    document.getElementById('h-sec-orders').textContent = `${cachedStats.orders_count || 0} active orders`;
    document.getElementById('h-sec-rev').textContent = `Ksh ${Number(cachedStats.total_revenue || 0).toLocaleString()}`;
    const avg = cachedStats.orders_count > 0 ? cachedStats.total_revenue / cachedStats.orders_count : 0;
    document.getElementById('h-sec-avg').textContent = `Ksh ${Math.round(avg).toLocaleString()}`;

    const ratingEl = document.getElementById('h-sec-rating');
    ratingEl.textContent = 'Consolidated Monitor';
    ratingEl.className = 'status-pill s-ready';

    if (adminMap) {
      adminMap.setView([-1.28817042, 36.81647301], 17);
    }
    return;
  }

  const names = {
    vip_a: 'VIP Section A (Northwest)',
    vip_b: 'VIP Section B (Northeast)',
    gen_a: 'General Arena A (Southwest)',
    gen_b: 'General Arena B (Southeast)'
  };

  const name = customSections[secKey] ? customSections[secKey].name : (names[secKey] || secKey.toUpperCase());
  const count = cachedStats.section_heatmap[secKey] || 0;
  const orders = (cachedStats.recent_orders || []).filter(o => {
    const sec = o.seat_location?.section?.toLowerCase().replace(/\s+/g, '_') || '';
    return sec === secKey || (secKey.startsWith('vip') && sec.includes('vip')) || (secKey.startsWith('gen') && sec.includes('gen'));
  });

  let totalRev = 0;
  orders.forEach(o => totalRev += parseFloat(o.total_amount));
  const avg = count > 0 ? totalRev / count : 0;

  document.getElementById('h-sec-name').textContent = name;
  document.getElementById('h-sec-orders').textContent = `${count} active orders`;
  document.getElementById('h-sec-rev').textContent = `Ksh ${Math.round(totalRev).toLocaleString()}`;
  document.getElementById('h-sec-avg').textContent = `Ksh ${Math.round(avg).toLocaleString()}`;

  const ratingEl = document.getElementById('h-sec-rating');
  if (count === 0) { ratingEl.textContent = 'Low Traffic'; ratingEl.className = 'status-pill s-created'; }
  else if (count <= 2) { ratingEl.textContent = 'Moderate Traffic'; ratingEl.className = 'status-pill s-ready'; }
  else if (count <= 5) { ratingEl.textContent = 'High Demand'; ratingEl.className = 'status-pill s-preparing'; }
  else { ratingEl.textContent = 'Peak Hotzone'; ratingEl.className = 'status-pill s-enroute'; }

  if (mapPolygons[secKey] && adminMap) {
    adminMap.fitBounds(mapPolygons[secKey].getBounds(), { padding: [40, 40] });
  }
}
</script>
@endsection
