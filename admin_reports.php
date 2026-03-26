<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Quick Care — Analytics & Reports</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
/* ─── RESET & VARS ─────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:       #0f2044;
  --navy-mid:   #162d5e;
  --navy-light: #1e3a78;
  --accent:     #3b82f6;
  --accent2:    #10b981;
  --bg:         #f0f4f8;
  --surface:    #ffffff;
  --border:     #e2e8f0;
  --text:       #1e293b;
  --muted:      #64748b;
  --pink:       #f9a8d4;
  --teal:       #5eead4;
  --coral:      #fb7185;
  --amber:      #fbbf24;
  --sidebar-w:  260px;
  --header-h:   72px;
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden}

/* ─── SIDEBAR ──────────────────────────────────────────────── */
#sidebar{
  width:var(--sidebar-w);min-height:100vh;background:var(--navy);
  display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;
  z-index:100;transition:transform .3s;
}
.sidebar-brand{padding:28px 24px 20px;border-bottom:1px solid rgba(255,255,255,.1)}
.brand-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--accent),var(--accent2));
  border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;
  font-size:20px}
.brand-title{font-family:'DM Serif Display',serif;font-size:22px;color:#fff;line-height:1}
.brand-sub{font-size:11px;color:rgba(255,255,255,.5);letter-spacing:.08em;margin-top:4px;text-transform:uppercase}
.nav-section{padding:20px 0;flex:1}
.nav-label{font-size:10px;font-weight:600;color:rgba(255,255,255,.35);letter-spacing:.12em;
  text-transform:uppercase;padding:0 24px 8px}
.nav-item{display:flex;align-items:center;gap:12px;padding:11px 24px;cursor:pointer;
  color:rgba(255,255,255,.65);font-size:14px;font-weight:500;transition:all .2s;
  border-left:3px solid transparent;text-decoration:none}
.nav-item:hover{color:#fff;background:rgba(255,255,255,.07)}
.nav-item.active{color:#fff;background:rgba(59,130,246,.18);border-left-color:var(--accent)}
.nav-item svg{width:18px;height:18px;flex-shrink:0}
.sidebar-footer{padding:20px 24px;border-top:1px solid rgba(255,255,255,.1)}
.sidebar-footer p{font-size:11px;color:rgba(255,255,255,.35)}

/* ─── MAIN ─────────────────────────────────────────────────── */
#main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh}

/* ─── HEADER ──────────────────────────────────────────────── */
#header{
  height:var(--header-h);background:var(--surface);border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 32px;position:sticky;top:0;z-index:50;gap:16px;
}
.page-title{font-family:'DM Serif Display',serif;font-size:24px;color:var(--navy);white-space:nowrap}
.header-controls{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.filter-group{display:flex;align-items:center;gap:6px}
.filter-group label{font-size:12px;color:var(--muted);font-weight:500;white-space:nowrap}
select,input[type=date]{
  padding:6px 10px;border:1px solid var(--border);border-radius:8px;
  font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);
  background:var(--bg);cursor:pointer;outline:none;transition:border .2s}
select:hover,input[type=date]:hover{border-color:var(--accent)}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:none;
  border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;
  cursor:pointer;transition:all .2s;white-space:nowrap}
.btn-primary{background:var(--navy);color:#fff}
.btn-primary:hover{background:var(--navy-mid)}
.btn-pdf{background:#ef4444;color:#fff}
.btn-pdf:hover{background:#dc2626}
.btn-excel{background:#16a34a;color:#fff}
.btn-excel:hover{background:#15803d}
.btn-outline{background:transparent;color:var(--navy);border:1px solid var(--border)}
.btn-outline:hover{background:var(--bg)}

/* ─── CONTENT ─────────────────────────────────────────────── */
#content{padding:28px 32px;flex:1}

/* ─── KPI CARDS ───────────────────────────────────────────── */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px}
.kpi-card{
  background:var(--surface);border-radius:14px;padding:22px;
  border:1px solid var(--border);position:relative;overflow:hidden;
}
.kpi-card::before{content:'';position:absolute;top:0;right:0;width:80px;height:80px;
  border-radius:0 0 0 100%;opacity:.08}
.kpi-card.blue::before{background:var(--accent)}
.kpi-card.green::before{background:var(--accent2)}
.kpi-card.red::before{background:var(--coral)}
.kpi-card.amber::before{background:var(--amber)}
.kpi-label{font-size:12px;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px}
.kpi-value{font-family:'DM Serif Display',serif;font-size:32px;color:var(--navy);line-height:1}
.kpi-sub{font-size:12px;color:var(--muted);margin-top:6px}
.kpi-icon{position:absolute;top:20px;right:20px;width:36px;height:36px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:18px}
.kpi-card.blue .kpi-icon{background:rgba(59,130,246,.12);color:var(--accent)}
.kpi-card.green .kpi-icon{background:rgba(16,185,129,.12);color:var(--accent2)}
.kpi-card.red .kpi-icon{background:rgba(251,113,133,.12);color:var(--coral)}
.kpi-card.amber .kpi-icon{background:rgba(251,191,36,.12);color:var(--amber)}

/* ─── CHARTS ROW ──────────────────────────────────────────── */
.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:28px}
.chart-card{background:var(--surface);border-radius:14px;padding:22px;border:1px solid var(--border)}
.chart-title{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:16px;display:flex;align-items:center;gap:8px}
.chart-title span{font-size:11px;font-weight:500;color:var(--muted);background:var(--bg);padding:3px 8px;border-radius:20px}
.chart-wrap{position:relative;height:220px}

/* ─── TABLE ───────────────────────────────────────────────── */
.table-card{background:var(--surface);border-radius:14px;border:1px solid var(--border);overflow:hidden}
.table-toolbar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.table-toolbar-title{font-size:14px;font-weight:600;color:var(--navy)}
.table-search{display:flex;align-items:center;gap:8px;background:var(--bg);border-radius:8px;padding:7px 12px;flex:1;max-width:280px}
.table-search input{border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;width:100%}
.table-search svg{width:15px;height:15px;color:var(--muted);flex-shrink:0}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{background:var(--navy)}
thead th{padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:#fff;white-space:nowrap;letter-spacing:.03em}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:#f8fafc}
tbody td{padding:12px 16px;font-size:13px;color:var(--text)}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-completed{background:rgba(16,185,129,.12);color:#059669}
.badge-scheduled{background:rgba(59,130,246,.12);color:#2563eb}
.badge-cancelled{background:rgba(251,113,133,.12);color:#e11d48}
.badge-failed{background:rgba(251,113,133,.12);color:#e11d48}
.pagination{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.page-btns{display:flex;gap:4px}
.page-btn{width:30px;height:30px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all .15s}
.page-btn:hover,.page-btn.active{background:var(--navy);color:#fff;border-color:var(--navy)}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state svg{width:48px;height:48px;margin:0 auto 12px;opacity:.3}

/* ─── LOADING ─────────────────────────────────────────────── */
.loading-overlay{position:fixed;inset:0;background:rgba(255,255,255,.8);
  display:flex;align-items:center;justify-content:center;z-index:999;opacity:0;pointer-events:none;transition:opacity .2s}
.loading-overlay.show{opacity:1;pointer-events:all}
.spinner{width:40px;height:40px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* ─── FOOTER ──────────────────────────────────────────────── */
#footer{padding:16px 32px;border-top:1px solid var(--border);background:var(--surface);
  display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--muted)}

/* ─── RESPONSIVE ───────────────────────────────────────────── */
@media(max-width:900px){
  .charts-grid{grid-template-columns:1fr}
  #header{flex-wrap:wrap;height:auto;padding:12px 16px}
  #content{padding:16px}
}
@media(max-width:700px){
  #sidebar{transform:translateX(-100%)}
  #sidebar.open{transform:translateX(0)}
  #main{margin-left:0}
}
</style>
</head>
<body>

<!-- ─── SIDEBAR ──────────────────────────────────────────────── -->
<nav id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon" style="background:none;padding:0;overflow:hidden"><img src="uploads/logo.jpg" alt="Quick Care Logo" style="width:40px;height:40px;object-fit:cover;border-radius:10px;display:block"/></div>
    <div class="brand-title">Quick Care</div>
    <div class="brand-sub">Analytics &amp; Reports</div>
  </div>
  <div class="nav-section">
    <div class="nav-label">Reports</div>
    <a class="nav-item active" data-report="appointment" onclick="switchReport('appointment')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
      Appointment Report
    </a>
    <a class="nav-item" data-report="doctor" onclick="switchReport('doctor')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Doctor Report
    </a>
    <a class="nav-item" data-report="payment" onclick="switchReport('payment')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
      Payment Report
    </a>
    <a class="nav-item" data-report="patient" onclick="switchReport('patient')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Patient Report
    </a>
  </div>
  <div class="sidebar-footer"><p>Quick Care PMS v2.0</p></div>
</nav>

<!-- ─── MAIN ──────────────────────────────────────────────────── -->
<div id="main">
  <!-- HEADER -->
  <header id="header">
    <div class="page-title" id="pageTitle">Appointment Report</div>
    <div class="header-controls">
      <div class="filter-group">
        <label>Period</label>
        <select id="periodFilter" onchange="applyFilters()">
          <option value="daily">Today</option>
          <option value="weekly">This Week</option>
          <option value="monthly" selected>This Month</option>
          <option value="custom">Custom</option>
        </select>
      </div>
      <div class="filter-group" id="customDates" style="display:none">
        <input type="date" id="dateFrom" onchange="applyFilters()"/>
        <span style="font-size:12px;color:var(--muted)">—</span>
        <input type="date" id="dateTo" onchange="applyFilters()"/>
      </div>
      <div class="filter-group" id="doctorFilterWrap">
        <label>Doctor</label>
        <select id="doctorFilter" onchange="applyFilters()">
          <option value="">All Doctors</option>
        </select>
      </div>
      <div class="filter-group" id="statusFilterWrap">
        <label>Status</label>
        <select id="statusFilter" onchange="applyFilters()">
          <option value="">All</option>
          <option value="SCHEDULED">Scheduled</option>
          <option value="COMPLETED">Completed</option>
          <option value="CANCELLED">Cancelled</option>
        </select>
      </div>
      <button class="btn btn-pdf" onclick="exportPDF()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        PDF
      </button>
      <button class="btn btn-excel" onclick="exportExcel()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Excel
      </button>
    </div>
  </header>

  <!-- CONTENT -->
  <main id="content">
    <!-- KPI Cards -->
    <div class="kpi-grid" id="kpiGrid"></div>
    <!-- Charts -->
    <div class="charts-grid" id="chartsGrid">
      <div class="chart-card">
        <div class="chart-title">Trend <span id="chartPeriodLabel">Monthly</span></div>
        <div class="chart-wrap"><canvas id="chartMain"></canvas></div>
      </div>
      <div class="chart-card">
        <div class="chart-title">Distribution</div>
        <div class="chart-wrap"><canvas id="chartPie"></canvas></div>
      </div>
    </div>
    <!-- Table -->
    <div class="table-card">
      <div class="table-toolbar">
        <span class="table-toolbar-title" id="tableTitle">Appointments</span>
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="tableSearch" placeholder="Search table…" oninput="filterTable()"/>
        </div>
      </div>
      <div class="tbl-wrap"><table id="dataTable"><thead id="tableHead"></thead><tbody id="tableBody"></tbody></table></div>
      <div class="pagination">
        <span id="pageInfo">Showing 0 records</span>
        <div class="page-btns" id="pageBtns"></div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer id="footer">
    <span>Quick Care — Patient Management System</span>
    <span>Report Generated: <strong id="footerDate"></strong></span>
  </footer>
</div>

<div class="loading-overlay" id="loader"><div class="spinner"></div></div>

<script>
// ─── CONFIG ───────────────────────────────────────────────────
const API = 'api/reports.php';
let currentReport = 'appointment';
let reportData    = {};
let allRows       = [];
let filteredRows  = [];
let currentPage   = 1;
const PAGE_SIZE   = 15;
let chartMain, chartPie;

// ─── INIT ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  document.getElementById('footerDate').textContent = new Date().toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
  document.getElementById('dateFrom').value = new Date(new Date().setDate(1)).toISOString().split('T')[0];
  document.getElementById('dateTo').value   = new Date().toISOString().split('T')[0];
  await loadDoctors();
  loadReport();
  document.getElementById('periodFilter').addEventListener('change', e => {
    document.getElementById('customDates').style.display = e.target.value === 'custom' ? 'flex' : 'none';
  });
});

async function loadDoctors() {
  const r = await fetch(`${API}?type=doctors_list`);
  const docs = await r.json();
  const sel = document.getElementById('doctorFilter');
  docs.forEach(d => sel.innerHTML += `<option value="${d.DOCTOR_ID}">${d.name}</option>`);
}

function switchReport(type) {
  currentReport = type;
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  document.querySelector(`[data-report="${type}"]`).classList.add('active');

  const titles = {appointment:'Appointment Report', doctor:'Doctor Report', payment:'Payment Report', patient:'Patient Report'};
  document.getElementById('pageTitle').textContent = titles[type];

  const showDoc    = ['appointment','patient'].includes(type);
  const showStatus = type === 'appointment';
  document.getElementById('doctorFilterWrap').style.display = showDoc    ? '' : 'none';
  document.getElementById('statusFilterWrap').style.display = showStatus ? '' : 'none';

  loadReport();
}

function applyFilters() { loadReport(); }

async function loadReport() {
  showLoader(true);
  const period  = document.getElementById('periodFilter').value;
  const from    = document.getElementById('dateFrom').value;
  const to      = document.getElementById('dateTo').value;
  const doctor  = document.getElementById('doctorFilter').value;
  const status  = document.getElementById('statusFilter').value;

  let url = `${API}?type=${currentReport}&period=${period}`;
  if (period === 'custom') url += `&date_from=${from}&date_to=${to}`;
  if (doctor) url += `&doctor_id=${doctor}`;
  if (status) url += `&status=${status}`;

  try {
    const r = await fetch(url);
    reportData = await r.json();
    renderReport();
  } catch(e) {
    document.getElementById('tableBody').innerHTML = `<tr><td colspan="10" style="text-align:center;padding:40px;color:#ef4444">⚠️ Failed to load. Check PHP API &amp; DB connection.</td></tr>`;
  }
  showLoader(false);
}

// ─── RENDER DISPATCHER ────────────────────────────────────────
function renderReport() {
  switch(currentReport) {
    case 'appointment': renderAppointment(); break;
    case 'doctor':      renderDoctor();      break;
    case 'payment':     renderPayment();     break;
    case 'patient':     renderPatient();     break;
  }
}

// ─── APPOINTMENT ──────────────────────────────────────────────
function renderAppointment() {
  const d = reportData;
  const s = d.summary || {};
  renderKPIs([
    {label:'Total Appointments', value: s.total||0,     icon:'📅', color:'blue'},
    {label:'Completed',          value: s.completed||0, icon:'✅', color:'green'},
    {label:'Scheduled',          value: s.scheduled||0, icon:'🗓️', color:'amber'},
    {label:'Cancelled',          value: s.cancelled||0, icon:'❌', color:'red'},
  ]);
  const trend = d.trend||[];
  renderBarChart(trend.map(r=>r.appt_date), trend.map(r=>r.count), 'Appointments');
  const dd = d.by_doctor||[];
  renderPieChart(dd.map(r=>r.doctor_name), dd.map(r=>r.total), ['#f9a8d4','#5eead4','#fb7185','#fbbf24','#a78bfa','#34d399','#60a5fa']);

  document.getElementById('tableTitle').textContent = 'All Appointments';
  renderTable(
    ['#','Patient','Doctor','Specialisation','Date','Time','Status'],
    (d.rows||[]).map((r,i)=>[i+1, r.patient_name, r.doctor_name, r.specialisation,
      fmtDate(r.APPOINTMENT_DATE), r.APPOINTMENT_TIME,
      `<span class="badge badge-${r.STATUS.toLowerCase()}">${r.STATUS}</span>`
    ])
  );
}

// ─── DOCTOR ───────────────────────────────────────────────────
function renderDoctor() {
  const rows = reportData.rows||[];
  const totalDocs  = rows.length;
  const totalAppts = rows.reduce((a,r)=>a+parseInt(r.total_appointments),0);
  const totalPats  = rows.reduce((a,r)=>a+parseInt(r.total_patients),0);
  const totalComp  = rows.reduce((a,r)=>a+parseInt(r.completed),0);
  renderKPIs([
    {label:'Total Doctors',      value: totalDocs,  icon:'👨‍⚕️', color:'blue'},
    {label:'Total Appointments', value: totalAppts, icon:'📅',  color:'green'},
    {label:'Total Patients',     value: totalPats,  icon:'🧑',  color:'amber'},
    {label:'Completed',          value: totalComp,  icon:'✅',  color:'green'},
  ]);
  renderBarChart(rows.map(r=>r.doctor_name.split(' ').pop()), rows.map(r=>r.total_appointments), 'Appointments', '#f9a8d4');
  const spec = reportData.by_specialisation||[];
  renderPieChart(spec.map(r=>r.specialisation), spec.map(r=>r.total_appointments), ['#5eead4','#fb7185','#fbbf24','#a78bfa']);

  document.getElementById('tableTitle').textContent = 'Doctor Summary';
  renderTable(
    ['#','Doctor','Specialisation','Education','Total Appts','Patients','Completed','Scheduled','Cancelled'],
    rows.map((r,i)=>[i+1, r.doctor_name, r.specialisation, r.EDUCATION,
      r.total_appointments, r.total_patients,
      `<span class="badge badge-completed">${r.completed}</span>`,
      `<span class="badge badge-scheduled">${r.scheduled}</span>`,
      `<span class="badge badge-cancelled">${r.cancelled}</span>`,
    ])
  );
}

// ─── PAYMENT ──────────────────────────────────────────────────
function renderPayment() {
  const s = reportData.summary||{};
  renderKPIs([
    {label:'Total Revenue',  value:`₹${parseFloat(s.total_revenue||0).toLocaleString('en-IN')}`, icon:'💰', color:'green'},
    {label:'Transactions',   value: s.total_transactions||0,  icon:'🧾', color:'blue'},
    {label:'Avg. Amount',    value:`₹${parseFloat(s.avg_amount||0).toFixed(0)}`, icon:'📊', color:'amber'},
  ]);
  const trend = reportData.trend||[];
  renderBarChart(trend.map(r=>r.pay_date), trend.map(r=>parseFloat(r.revenue)), 'Revenue (₹)', '#5eead4');
  const modes = reportData.by_mode||[];
  renderPieChart(modes.map(r=>r.PAYMENT_MODE), modes.map(r=>parseFloat(r.revenue)), ['#5eead4','#fb7185','#fbbf24','#a78bfa','#60a5fa']);

  document.getElementById('tableTitle').textContent = 'Payment Transactions';
  renderTable(
    ['#','Transaction ID','Patient','Doctor','Amount','Date','Mode','Status'],
    (reportData.rows||[]).map((r,i)=>[i+1,
      `<span style="font-family:monospace;font-size:11px">${r.TRANSACTION_ID}</span>`,
      r.patient_name, r.doctor_name,
      `<strong>₹${parseFloat(r.AMOUNT).toFixed(2)}</strong>`,
      fmtDate(r.PAYMENT_DATE), r.PAYMENT_MODE,
      `<span class="badge badge-${r.STATUS.toLowerCase()}">${r.STATUS}</span>`
    ])
  );
}

// ─── PATIENT ──────────────────────────────────────────────────
function renderPatient() {
  const s = reportData.summary||{};
  renderKPIs([
    {label:'Total Patients',           value: s.total_patients||0,         icon:'🧑', color:'blue'},
    {label:'Total Appointments',       value: s.total_appointments||0,     icon:'📅', color:'green'},
    {label:'Avg Appointments/Patient', value: s.avg_visits_per_patient||0, icon:'📊', color:'amber'},
  ]);
  const bd = reportData.by_doctor||[];
  renderBarChart(bd.map(r=>r.doctor_name.split(' ').pop()), bd.map(r=>r.unique_patients), 'Patients', '#f9a8d4');
  const gd = reportData.gender_dist||[];
  renderPieChart(gd.map(r=>r.GENDER), gd.map(r=>r.count), ['#60a5fa','#f9a8d4','#a78bfa']);

  document.getElementById('tableTitle').textContent = 'Patient Appointment Summary';
  renderTable(
    ['#','Patient','Gender','Blood Group','Total Appointments','Completed','Upcoming','Cancelled','Last Visit','Next Visit','Doctors'],
    (reportData.rows||[]).map((r,i) => {

      const lastVisitCell = r.last_visit
        ? `<span style="color:#059669;font-weight:500">${fmtDate(r.last_visit)}</span>`
        : '<span style="color:#94a3b8">—</span>';

      const nextVisitCell = r.next_visit
        ? `<span style="color:#2563eb;font-weight:500">${fmtDate(r.next_visit)}</span>`
        : '<span style="color:#94a3b8">—</span>';

      return [i+1, r.patient_name, r.GENDER, r.BLOOD_GROUP,
        `<strong>${r.visit_count}</strong>`,
        `<span class="badge badge-completed">${r.completed}</span>`,
        `<span class="badge badge-scheduled">${r.scheduled||0}</span>`,
        `<span class="badge badge-cancelled">${r.cancelled}</span>`,
        lastVisitCell, nextVisitCell, r.doctors_visited
      ];
    })
  );
}

// ─── KPI RENDERER ─────────────────────────────────────────────
function renderKPIs(cards) {
  document.getElementById('kpiGrid').innerHTML = cards.map(c=>`
    <div class="kpi-card ${c.color}">
      <div class="kpi-icon">${c.icon}</div>
      <div class="kpi-label">${c.label}</div>
      <div class="kpi-value">${c.value}</div>
    </div>`).join('');
}

// ─── CHART RENDERERS ──────────────────────────────────────────
function renderBarChart(labels, data, label, color='#3b82f6') {
  const ctx = document.getElementById('chartMain').getContext('2d');
  if (chartMain) chartMain.destroy();
  chartMain = new Chart(ctx, {
    type:'bar',
    data:{labels, datasets:[{label, data, backgroundColor: color+'cc', borderRadius:6, borderSkipped:false}]},
    options:{responsive:true, maintainAspectRatio:false,
      plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false},ticks:{font:{family:'DM Sans',size:11}}},
              y:{grid:{color:'#f1f5f9'},ticks:{font:{family:'DM Sans',size:11}}}}}
  });
}

function renderPieChart(labels, data, colors) {
  const ctx = document.getElementById('chartPie').getContext('2d');
  if (chartPie) chartPie.destroy();
  chartPie = new Chart(ctx, {
    type:'doughnut',
    data:{labels, datasets:[{data, backgroundColor:colors, borderWidth:2, borderColor:'#fff'}]},
    options:{responsive:true, maintainAspectRatio:false,
      plugins:{legend:{position:'bottom', labels:{font:{family:'DM Sans',size:11}, boxWidth:12, padding:10}}}}
  });
}

// ─── TABLE RENDERER ───────────────────────────────────────────
function renderTable(headers, rows) {
  allRows = rows;
  document.getElementById('tableHead').innerHTML =
    `<tr>${headers.map(h=>`<th>${h}</th>`).join('')}</tr>`;
  filterTable();
}

function filterTable() {
  const q = document.getElementById('tableSearch').value.toLowerCase();
  filteredRows = q ? allRows.filter(r => r.some(c => String(c).toLowerCase().includes(q))) : [...allRows];
  currentPage = 1;
  renderPage();
}

function renderPage() {
  const start = (currentPage-1)*PAGE_SIZE;
  const page  = filteredRows.slice(start, start+PAGE_SIZE);
  const tbody = document.getElementById('tableBody');
  if (!page.length) {
    tbody.innerHTML = `<tr><td colspan="20" class="empty-state"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 15s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg><p>No records found</p></td></tr>`;
  } else {
    tbody.innerHTML = page.map(r=>`<tr>${r.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('');
  }
  const total = filteredRows.length;
  const pages = Math.ceil(total/PAGE_SIZE);
  document.getElementById('pageInfo').textContent =
    `Showing ${Math.min(start+1,total)}–${Math.min(start+PAGE_SIZE,total)} of ${total} records`;
  const pb = document.getElementById('pageBtns');
  pb.innerHTML = '';
  for(let i=1;i<=Math.min(pages,7);i++){
    const b=document.createElement('button');
    b.className='page-btn'+(i===currentPage?' active':'');
    b.textContent=i; b.onclick=()=>{currentPage=i;renderPage()};
    pb.appendChild(b);
  }
}

// ─── EXPORT PDF ───────────────────────────────────────────────
async function exportPDF() {
  const {jsPDF} = window.jspdf;
  const doc = new jsPDF({orientation:'landscape',unit:'mm',format:'a4'});
  const period  = document.getElementById('periodFilter').value;
  const today   = new Date().toLocaleDateString('en-IN');
  const titles  = {appointment:'Appointment Report',doctor:'Doctor Report',payment:'Payment Report',patient:'Patient Report'};
  const W = doc.internal.pageSize.getWidth();

  // Header bar
  doc.setFillColor(15,32,68);
  doc.rect(0,0,W,22,'F');
  doc.setFont('helvetica','bold');
  doc.setFontSize(16); doc.setTextColor(255,255,255);
  doc.text('Quick Care — '+titles[currentReport], 14, 13);
  doc.setFontSize(9); doc.setFont('helvetica','normal');
  doc.text(`Period: ${period.toUpperCase()}  |  Generated: ${today}`, W-14, 13, {align:'right'});

  // KPI summary
  doc.setTextColor(30,41,59);
  doc.setFontSize(10); doc.setFont('helvetica','bold');
  doc.text('Summary', 14, 32);
  const kpiCards = document.querySelectorAll('.kpi-card');
  let kx = 14;
  kpiCards.forEach(card => {
    const label = card.querySelector('.kpi-label').textContent;
    const value = card.querySelector('.kpi-value').textContent;
    doc.setFillColor(240,244,248); doc.roundedRect(kx,35,60,18,2,2,'F');
    doc.setFontSize(8); doc.setFont('helvetica','normal'); doc.setTextColor(100,116,139);
    doc.text(label, kx+4, 41);
    doc.setFontSize(12); doc.setFont('helvetica','bold'); doc.setTextColor(15,32,68);
    doc.text(String(value), kx+4, 49);
    kx += 63;
  });

  // Chart image
  try {
    const chartCanvas = document.getElementById('chartMain');
    const img = chartCanvas.toDataURL('image/png',0.8);
    doc.addImage(img, 'PNG', 14, 58, 120, 50);
    const pie = document.getElementById('chartPie');
    const img2 = pie.toDataURL('image/png',0.8);
    doc.addImage(img2, 'PNG', 140, 58, 70, 50);
  } catch(e){}

  // Table
  const headers = Array.from(document.querySelectorAll('#tableHead th')).map(th=>th.textContent);
  const tableRows = filteredRows.map(r=>r.map(c=>{
    const tmp=document.createElement('div'); tmp.innerHTML=String(c);
    return tmp.textContent.trim();
  }));
  doc.autoTable({
    head:[headers], body:tableRows,
    startY:115, margin:{left:14,right:14},
    styles:{fontSize:8, cellPadding:3, font:'helvetica'},
    headStyles:{fillColor:[15,32,68], textColor:255, fontStyle:'bold'},
    alternateRowStyles:{fillColor:[248,250,252]},
    tableLineColor:[226,232,240], tableLineWidth:0.2
  });

  // Footer
  const pCount = doc.internal.getNumberOfPages();
  for(let i=1;i<=pCount;i++){
    doc.setPage(i);
    doc.setFillColor(240,244,248);
    doc.rect(0,doc.internal.pageSize.getHeight()-10,W,10,'F');
    doc.setFontSize(8); doc.setTextColor(100,116,139); doc.setFont('helvetica','normal');
    doc.text(`Quick Care – Report Generated: ${today}`, 14, doc.internal.pageSize.getHeight()-3);
    doc.text(`Page ${i} of ${pCount}`, W-14, doc.internal.pageSize.getHeight()-3, {align:'right'});
  }

  doc.save(`QuickCare_${titles[currentReport].replace(/ /g,'_')}_${today.replace(/\//g,'-')}.pdf`);
}

// ─── EXPORT EXCEL ─────────────────────────────────────────────
function exportExcel() {
  const headers = Array.from(document.querySelectorAll('#tableHead th')).map(th=>th.textContent);
  const rows = filteredRows.map(r=>r.map(c=>{
    const tmp=document.createElement('div'); tmp.innerHTML=String(c); return tmp.textContent.trim();
  }));
  const ws = XLSX.utils.aoa_to_sheet([headers,...rows]);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, currentReport.charAt(0).toUpperCase()+currentReport.slice(1));
  const today = new Date().toLocaleDateString('en-IN').replace(/\//g,'-');
  XLSX.writeFile(wb, `QuickCare_${currentReport}_${today}.xlsx`);
}

// ─── HELPERS ──────────────────────────────────────────────────
function fmtDate(d) {
  if(!d) return '—';
  return new Date(d).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
}
function showLoader(s) {
  document.getElementById('loader').classList.toggle('show',s);
}
</script>
</body>
</html>
