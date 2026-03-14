<?php
/**
 * QuickCare – Admin Analytics Report
 * PHPMaker Custom Page
 *
 * HOW TO USE IN PHPMAKER:
 * 1. Place this file in your PHPMaker project root (same folder as index.php)
 * 2. Update DB credentials in the $db section below
 * 3. Access via: yoursite.com/quickcare_admin_report.php
 * 4. Optionally wrap with PHPMaker header/footer includes
 *
 * Database: quick_care
 */

// ── DB CONNECTION ─────────────────────────────────────────
$db_host = "localhost";
$db_user = "root";       // ← change to your DB username
$db_pass = "";           // ← change to your DB password
$db_name = "quick_care"; // ← your database name

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:40px;color:#c0392b;'>
        <h2>Database Connection Failed</h2>
        <p>" . $conn->connect_error . "</p>
        <p>Please check your DB credentials at the top of this file.</p>
    </div>");
}
$conn->set_charset("utf8mb4");

// ── FILTER: Month / Year / Week ───────────────────────────
$selected_year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selected_week  = isset($_GET['week'])  ? (int)$_GET['week']  : 0; // 0 = full month

$month_start = sprintf('%04d-%02d-01', $selected_year, $selected_month);
$month_end   = date('Y-m-t', strtotime($month_start));

// Week range within selected month
if ($selected_week > 0) {
    $first_day  = strtotime($month_start);
    $week_start = date('Y-m-d', strtotime('+' . (($selected_week - 1) * 7) . ' days', $first_day));
    $week_end   = date('Y-m-d', strtotime('+6 days', strtotime($week_start)));
    if ($week_end > $month_end) $week_end = $month_end;
    $date_from = $week_start;
    $date_to   = $week_end;
} else {
    $date_from = $month_start;
    $date_to   = $month_end;
}

$month_names = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$current_period = ($selected_week > 0)
    ? "Week $selected_week of {$month_names[$selected_month]} $selected_year"
    : "{$month_names[$selected_month]} $selected_year";

// ── HELPER ───────────────────────────────────────────────
function q($conn, $sql) {
    $r = $conn->query($sql);
    if (!$r) return [];
    $rows = [];
    while ($row = $r->fetch_assoc()) $rows[] = $row;
    return $rows;
}
function q1($conn, $sql) {
    $r = $conn->query($sql);
    if (!$r) return null;
    $row = $r->fetch_row();
    return $row ? $row[0] : 0;
}

// ══════════════════════════════════════════════════════════
//  1. APPOINTMENTS
// ══════════════════════════════════════════════════════════
$appt_total      = q1($conn, "SELECT COUNT(*) FROM appointment_tbl WHERE APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$appt_scheduled  = q1($conn, "SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='SCHEDULED'  AND APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$appt_completed  = q1($conn, "SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='COMPLETED'  AND APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$appt_cancelled  = q1($conn, "SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='CANCELLED'  AND APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");

// Monthly trend (last 6 months)
$appt_trend = q($conn, "
    SELECT DATE_FORMAT(APPOINTMENT_DATE,'%b %Y') AS lbl,
           MONTHNAME(APPOINTMENT_DATE) AS mon,
           MONTH(APPOINTMENT_DATE) AS m,
           YEAR(APPOINTMENT_DATE) AS y,
           COUNT(*) AS cnt
    FROM appointment_tbl
    WHERE APPOINTMENT_DATE >= DATE_SUB('$month_end', INTERVAL 5 MONTH)
    GROUP BY YEAR(APPOINTMENT_DATE), MONTH(APPOINTMENT_DATE)
    ORDER BY y, m
");

// Weekly breakdown in selected month
$appt_weekly = q($conn, "
    SELECT WEEK(APPOINTMENT_DATE,1) - WEEK('$month_start',1) + 1 AS week_num,
           COUNT(*) AS cnt
    FROM appointment_tbl
    WHERE APPOINTMENT_DATE BETWEEN '$month_start' AND '$month_end'
    GROUP BY week_num ORDER BY week_num
");

// ══════════════════════════════════════════════════════════
//  2. PAYMENTS
// ══════════════════════════════════════════════════════════
$pay_total_revenue  = q1($conn, "SELECT COALESCE(SUM(AMOUNT),0) FROM payment_tbl WHERE PAYMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$pay_completed      = q1($conn, "SELECT COUNT(*) FROM payment_tbl WHERE STATUS='COMPLETED' AND PAYMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$pay_pending        = q1($conn, "SELECT COUNT(*) FROM payment_tbl WHERE STATUS='PENDING'   AND PAYMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$pay_failed         = q1($conn, "SELECT COUNT(*) FROM payment_tbl WHERE STATUS='FAILED'    AND PAYMENT_DATE BETWEEN '$date_from' AND '$date_to'");

$pay_by_mode = q($conn, "
    SELECT PAYMENT_MODE, COUNT(*) AS cnt, SUM(AMOUNT) AS total
    FROM payment_tbl
    WHERE PAYMENT_DATE BETWEEN '$date_from' AND '$date_to'
    GROUP BY PAYMENT_MODE ORDER BY total DESC
");

// ══════════════════════════════════════════════════════════
//  3. REMINDERS
// ══════════════════════════════════════════════════════════
$appt_rem_total = q1($conn, "
    SELECT COUNT(*) FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'
");
$med_rem_total = q1($conn, "
    SELECT COUNT(*) FROM medicine_reminder_tbl
    WHERE START_DATE <= '$date_to' AND END_DATE >= '$date_from'
");

$appt_rem_weekly = q($conn, "
    SELECT WEEK(a.APPOINTMENT_DATE,1) - WEEK('$month_start',1) + 1 AS week_num,
           COUNT(*) AS cnt
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.APPOINTMENT_DATE BETWEEN '$month_start' AND '$month_end'
    GROUP BY week_num ORDER BY week_num
");

$med_rem_by_creator = q($conn, "
    SELECT CREATOR_ROLE, COUNT(*) AS cnt
    FROM medicine_reminder_tbl
    WHERE START_DATE <= '$date_to' AND END_DATE >= '$date_from'
    GROUP BY CREATOR_ROLE
");

// ══════════════════════════════════════════════════════════
//  4. PRESCRIPTIONS
// ══════════════════════════════════════════════════════════
$presc_total = q1($conn, "SELECT COUNT(*) FROM prescription_tbl WHERE ISSUE_DATE BETWEEN '$date_from' AND '$date_to'");

$presc_monthly = q($conn, "
    SELECT DATE_FORMAT(ISSUE_DATE,'%b %Y') AS lbl,
           MONTH(ISSUE_DATE) AS m, YEAR(ISSUE_DATE) AS y,
           COUNT(*) AS cnt
    FROM prescription_tbl
    WHERE ISSUE_DATE >= DATE_SUB('$month_end', INTERVAL 5 MONTH)
    GROUP BY y, m ORDER BY y, m
");

$presc_by_doctor = q($conn, "
    SELECT CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) AS doctor,
           s.SPECIALISATION_NAME AS spec,
           COUNT(p.PRESCRIPTION_ID) AS cnt
    FROM prescription_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d      ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE p.ISSUE_DATE BETWEEN '$date_from' AND '$date_to'
    GROUP BY d.DOCTOR_ID ORDER BY cnt DESC
");

// ══════════════════════════════════════════════════════════
//  5. FEEDBACK
// ══════════════════════════════════════════════════════════
$fb_total   = q1($conn, "SELECT COUNT(*) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");
$fb_avg_all = q1($conn, "SELECT ROUND(AVG(f.RATING),1) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'");

$fb_by_doctor = q($conn, "
    SELECT CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) AS doctor,
           s.SPECIALISATION_NAME AS spec,
           COUNT(f.FEEDBACK_ID) AS cnt,
           ROUND(AVG(f.RATING),1) AS avg_rating
    FROM feedback_tbl f
    JOIN appointment_tbl a  ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d       ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'
    GROUP BY d.DOCTOR_ID ORDER BY avg_rating DESC
");

$fb_dist = q($conn, "
    SELECT f.RATING, COUNT(*) AS cnt
    FROM feedback_tbl f
    JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID
    WHERE a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'
    GROUP BY f.RATING ORDER BY f.RATING DESC
");

// ══════════════════════════════════════════════════════════
//  6. DOCTOR-WISE SUMMARY
// ══════════════════════════════════════════════════════════
$doctor_summary = q($conn, "
    SELECT d.DOCTOR_ID,
           CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) AS doctor,
           s.SPECIALISATION_NAME AS spec,
           COUNT(DISTINCT a.APPOINTMENT_ID)  AS total_appt,
           SUM(a.STATUS='COMPLETED')         AS completed,
           SUM(a.STATUS='SCHEDULED')         AS scheduled,
           SUM(a.STATUS='CANCELLED')         AS cancelled,
           COUNT(DISTINCT p.PRESCRIPTION_ID) AS prescriptions,
           ROUND(AVG(fb.RATING),1)           AS avg_rating
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    LEFT JOIN appointment_tbl a  ON d.DOCTOR_ID = a.DOCTOR_ID AND a.APPOINTMENT_DATE BETWEEN '$date_from' AND '$date_to'
    LEFT JOIN prescription_tbl p ON a.APPOINTMENT_ID = p.APPOINTMENT_ID
    LEFT JOIN feedback_tbl fb    ON a.APPOINTMENT_ID = fb.APPOINTMENT_ID
    GROUP BY d.DOCTOR_ID ORDER BY total_appt DESC
");

// ── Year options for filter ───────────────────────────────
$years = q($conn, "SELECT DISTINCT YEAR(APPOINTMENT_DATE) y FROM appointment_tbl ORDER BY y DESC");

// ── JS chart data ─────────────────────────────────────────
$trend_labels  = json_encode(array_column($appt_trend,  'lbl'));
$trend_data    = json_encode(array_column($appt_trend,  'cnt'));
$presc_labels  = json_encode(array_column($presc_monthly,'lbl'));
$presc_data    = json_encode(array_column($presc_monthly,'cnt'));
$week_labels   = json_encode(array_map(fn($r)=>"Week {$r['week_num']}", $appt_weekly));
$week_data     = json_encode(array_column($appt_weekly, 'cnt'));
$mode_labels   = json_encode(array_column($pay_by_mode, 'PAYMENT_MODE'));
$mode_data     = json_encode(array_map(fn($r)=>(float)$r['total'], $pay_by_mode));

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>QuickCare – Admin Report | <?= htmlspecialchars($current_period) ?></title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ── RESET & BASE ─────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --teal:       #028090;
  --teal-d:     #016a78;
  --teal-l:     #e0f5f7;
  --mint:       #02c39a;
  --midnight:   #0a2530;
  --charcoal:   #1a3a46;
  --slate:      #3d6470;
  --mist:       #f2fafb;
  --white:      #ffffff;
  --text:       #0f2d38;
  --muted:      #5b7f8a;
  --border:     #cce8ed;
  --red:        #e84040;
  --orange:     #f07d35;
  --green:      #22c55e;
  --gold:       #f4a118;
  --purple:     #8b5cf6;
  --sidebar:    220px;
  --radius:     14px;
}
html { scroll-behavior: smooth; }
body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--mist);
  color: var(--text);
  font-size: 14px;
  line-height: 1.6;
  display: flex;
  min-height: 100vh;
}

/* ── SIDEBAR ─────────────────────────────────── */
.sidebar {
  width: var(--sidebar);
  background: var(--midnight);
  position: fixed;
  top: 0; left: 0; bottom: 0;
  display: flex;
  flex-direction: column;
  z-index: 100;
  overflow-y: auto;
}
.sidebar-logo {
  padding: 24px 20px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.sidebar-logo .app-name {
  font-size: 20px;
  font-weight: 700;
  color: var(--white);
  letter-spacing: -0.5px;
}
.sidebar-logo .app-name span { color: var(--mint); }
.sidebar-logo .sub { font-size: 10px; color: rgba(255,255,255,0.4); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 3px; }
.nav-section { padding: 20px 12px 8px; }
.nav-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.3); padding: 0 8px; margin-bottom: 6px; }
.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 9px;
  color: rgba(255,255,255,0.55);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.18s;
  margin-bottom: 2px;
}
.nav-link:hover, .nav-link.active {
  background: rgba(2,195,154,0.12);
  color: var(--mint);
}
.nav-link .icon { font-size: 16px; width: 20px; text-align: center; }
.sidebar-footer {
  margin-top: auto;
  padding: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
  font-size: 11px;
  color: rgba(255,255,255,0.3);
}

/* ── MAIN ─────────────────────────────────────── */
.main {
  margin-left: var(--sidebar);
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ── TOPBAR ───────────────────────────────────── */
.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--border);
  padding: 14px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 50;
}
.topbar-title { font-size: 17px; font-weight: 700; color: var(--midnight); }
.topbar-title span { color: var(--teal); }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.period-badge {
  background: var(--teal-l);
  color: var(--teal-d);
  font-size: 12px;
  font-weight: 600;
  padding: 5px 14px;
  border-radius: 20px;
  border: 1px solid var(--border);
}
.print-btn {
  background: var(--midnight);
  color: var(--white);
  border: none;
  border-radius: 8px;
  padding: 7px 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s;
}
.print-btn:hover { background: var(--charcoal); }

/* ── FILTER BAR ───────────────────────────────── */
.filter-bar {
  background: var(--white);
  border-bottom: 1px solid var(--border);
  padding: 12px 28px;
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}
.filter-bar label { font-size: 12px; font-weight: 600; color: var(--muted); }
.filter-bar select {
  border: 1.5px solid var(--border);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 13px;
  font-family: inherit;
  color: var(--text);
  background: var(--mist);
  cursor: pointer;
  outline: none;
  transition: border-color 0.18s;
}
.filter-bar select:focus { border-color: var(--teal); }
.filter-btn {
  background: var(--teal);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 7px 20px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.18s;
}
.filter-btn:hover { background: var(--teal-d); }
.filter-note { font-size: 11px; color: var(--muted); margin-left: auto; }

/* ── PAGE CONTENT ─────────────────────────────── */
.content { padding: 28px; display: flex; flex-direction: column; gap: 28px; }

/* ── SECTION TITLE ────────────────────────────── */
.sec-head {
  display: flex;
  align-items: baseline;
  gap: 10px;
  margin-bottom: 16px;
}
.sec-head h2 {
  font-size: 16px;
  font-weight: 700;
  color: var(--midnight);
}
.sec-tag {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--teal);
  background: var(--teal-l);
  padding: 2px 10px;
  border-radius: 20px;
}

/* ── STAT CARDS ───────────────────────────────── */
.stat-row { display: grid; gap: 16px; }
.stat-row.cols-4 { grid-template-columns: repeat(4,1fr); }
.stat-row.cols-3 { grid-template-columns: repeat(3,1fr); }
.stat-row.cols-2 { grid-template-columns: repeat(2,1fr); }

.stat-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px 22px;
  position: relative;
  overflow: hidden;
  transition: box-shadow 0.2s;
}
.stat-card:hover { box-shadow: 0 4px 20px rgba(2,128,144,0.10); }
.stat-card::before {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
}
.stat-card.teal::before  { background: linear-gradient(90deg,var(--teal),var(--mint)); }
.stat-card.green::before { background: var(--green); }
.stat-card.red::before   { background: var(--red); }
.stat-card.orange::before{ background: var(--orange); }
.stat-card.gold::before  { background: var(--gold); }
.stat-card.purple::before{ background: var(--purple); }

.stat-label { font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
.stat-val   { font-size: 32px; font-weight: 700; color: var(--midnight); line-height: 1; }
.stat-val.sm { font-size: 24px; }
.stat-sub   { font-size: 12px; color: var(--muted); margin-top: 4px; }
.stat-icon  { position: absolute; top: 18px; right: 18px; font-size: 26px; opacity: 0.18; }

/* ── CARD ─────────────────────────────────────── */
.card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 22px 24px;
}
.card-title { font-size: 14px; font-weight: 700; color: var(--midnight); margin-bottom: 16px; }

/* ── GRID LAYOUTS ─────────────────────────────── */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
.grid-chart-table { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }

/* ── CHART CANVAS ─────────────────────────────── */
.chart-wrap { position: relative; }
canvas { max-width: 100%; }

/* ── DATA TABLE ───────────────────────────────── */
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table thead tr { background: var(--mist); }
.data-table th {
  text-align: left;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--muted);
  padding: 10px 12px;
  border-bottom: 1px solid var(--border);
}
.data-table td { padding: 10px 12px; border-bottom: 1px solid #eef5f7; color: var(--text); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: var(--mist); }
.data-table .num { font-weight: 700; color: var(--midnight); }

/* ── STATUS PILLS ─────────────────────────────── */
.pill {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 2px 10px;
  border-radius: 20px;
  text-transform: uppercase;
}
.pill-green  { background: #dcfce7; color: #15803d; }
.pill-orange { background: #fff7ed; color: #c2410c; }
.pill-red    { background: #fee2e2; color: #b91c1c; }
.pill-teal   { background: var(--teal-l); color: var(--teal-d); }
.pill-gray   { background: #f1f5f9; color: #475569; }

/* ── STAR RATING ──────────────────────────────── */
.stars { color: var(--gold); letter-spacing: 1px; }

/* ── PROGRESS BAR ─────────────────────────────── */
.prog-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.prog-label { font-size: 12px; color: var(--muted); width: 110px; flex-shrink: 0; }
.prog-bar-wrap { flex: 1; background: var(--mist); border-radius: 6px; height: 10px; overflow: hidden; }
.prog-bar { height: 10px; border-radius: 6px; transition: width 1s ease; }
.prog-val { font-size: 12px; font-weight: 700; color: var(--midnight); width: 36px; text-align: right; flex-shrink: 0; }

/* ── WEEK GRID ────────────────────────────────── */
.week-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; }
.week-cell {
  background: var(--mist);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 14px 12px;
  text-align: center;
}
.week-cell .wlbl { font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.8px; }
.week-cell .wnum { font-size: 26px; font-weight: 700; color: var(--teal); line-height: 1; }

/* ── PRINT ────────────────────────────────────── */
@media print {
  .sidebar, .filter-bar, .topbar, .print-btn { display: none !important; }
  .main { margin-left: 0; }
  .content { padding: 16px; }
  .grid-2, .grid-chart-table { grid-template-columns: 1fr 1fr; }
  .stat-row.cols-4 { grid-template-columns: repeat(4,1fr); }
  body { font-size: 12px; }
  .card, .stat-card { break-inside: avoid; box-shadow: none; border: 1px solid #ccc; }
}

/* ── RESPONSIVE ───────────────────────────────── */
@media (max-width: 900px) {
  :root { --sidebar: 0px; }
  .sidebar { display: none; }
  .stat-row.cols-4 { grid-template-columns: repeat(2,1fr); }
  .grid-2, .grid-chart-table { grid-template-columns: 1fr; }
  .week-grid { grid-template-columns: repeat(2,1fr); }
}
</style>
</head>
<body>

<!-- ════════════ SIDEBAR ════════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="app-name">Quick<span>Care</span></div>
    <div class="sub">Admin Report Panel</div>
  </div>
  <div class="nav-section">
    <div class="nav-label">Reports</div>
    <a href="#appointments" class="nav-link active"><span class="icon">📅</span> Appointments</a>
    <a href="#payments"     class="nav-link"><span class="icon">💳</span> Payments</a>
    <a href="#reminders"    class="nav-link"><span class="icon">🔔</span> Reminders</a>
    <a href="#prescriptions"class="nav-link"><span class="icon">📄</span> Prescriptions</a>
    <a href="#feedback"     class="nav-link"><span class="icon">⭐</span> Feedback</a>
    <a href="#doctors"      class="nav-link"><span class="icon">🩺</span> Doctor Summary</a>
  </div>
  <div class="sidebar-footer">
    QuickCare v1.0 · Group 16
  </div>
</aside>

<!-- ════════════ MAIN ════════════ -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-title">Admin <span>Analytics Report</span></div>
    <div class="topbar-right">
      <div class="period-badge">📆 <?= htmlspecialchars($current_period) ?></div>
      <button class="print-btn" onclick="window.print()">🖨 Print / Export PDF</button>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;width:100%">
      <label>Month</label>
      <select name="month">
        <?php for($m=1;$m<=12;$m++): ?>
          <option value="<?=$m?>" <?=$m==$selected_month?'selected':''?>><?=$month_names[$m]?></option>
        <?php endfor; ?>
      </select>

      <label>Year</label>
      <select name="year">
        <?php
        $yr_opts = array_column($years,'y');
        if (!in_array($selected_year,$yr_opts)) $yr_opts[] = $selected_year;
        rsort($yr_opts);
        foreach($yr_opts as $y): ?>
          <option value="<?=$y?>" <?=$y==$selected_year?'selected':''?>><?=$y?></option>
        <?php endforeach; ?>
      </select>

      <label>Week</label>
      <select name="week">
        <option value="0" <?=$selected_week==0?'selected':''?>>Full Month</option>
        <option value="1" <?=$selected_week==1?'selected':''?>>Week 1</option>
        <option value="2" <?=$selected_week==2?'selected':''?>>Week 2</option>
        <option value="3" <?=$selected_week==3?'selected':''?>>Week 3</option>
        <option value="4" <?=$selected_week==4?'selected':''?>>Week 4</option>
      </select>

      <button type="submit" class="filter-btn">Apply Filter</button>
      <span class="filter-note">Showing: <strong><?= htmlspecialchars($current_period) ?></strong> (<?= $date_from ?> → <?= $date_to ?>)</span>
    </form>
  </div>

  <div class="content">

    <!-- ════ 1. APPOINTMENTS ════ -->
    <section id="appointments">
      <div class="sec-head">
        <h2>Appointments</h2>
        <span class="sec-tag">📅 Section 1</span>
      </div>

      <div class="stat-row cols-4" style="margin-bottom:20px;">
        <div class="stat-card teal">
          <div class="stat-label">Total Appointments</div>
          <div class="stat-val"><?= $appt_total ?></div>
          <div class="stat-sub"><?= htmlspecialchars($current_period) ?></div>
          <div class="stat-icon">📅</div>
        </div>
        <div class="stat-card green">
          <div class="stat-label">Completed</div>
          <div class="stat-val"><?= $appt_completed ?></div>
          <div class="stat-sub">Successfully done</div>
          <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card orange">
          <div class="stat-label">Scheduled</div>
          <div class="stat-val"><?= $appt_scheduled ?></div>
          <div class="stat-sub">Upcoming</div>
          <div class="stat-icon">🕐</div>
        </div>
        <div class="stat-card red">
          <div class="stat-label">Cancelled</div>
          <div class="stat-val"><?= $appt_cancelled ?></div>
          <div class="stat-sub">Cancelled this period</div>
          <div class="stat-icon">❌</div>
        </div>
      </div>

      <div class="grid-chart-table">
        <div class="card">
          <div class="card-title">Monthly Appointment Trend (Last 6 Months)</div>
          <div class="chart-wrap"><canvas id="apptTrendChart" height="200"></canvas></div>
        </div>
        <div class="card">
          <div class="card-title">Weekly Breakdown — <?= $month_names[$selected_month] ?></div>
          <?php if(count($appt_weekly)>0): ?>
          <div class="week-grid">
            <?php foreach($appt_weekly as $w): ?>
            <div class="week-cell">
              <div class="wlbl">Week <?= $w['week_num'] ?></div>
              <div class="wnum"><?= $w['cnt'] ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No appointment data for this month.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ 2. PAYMENTS ════ -->
    <section id="payments">
      <div class="sec-head">
        <h2>Payments</h2>
        <span class="sec-tag">💳 Section 2</span>
      </div>

      <div class="stat-row cols-3" style="margin-bottom:20px;">
        <div class="stat-card teal">
          <div class="stat-label">Total Revenue</div>
          <div class="stat-val sm">₹<?= number_format($pay_total_revenue, 2) ?></div>
          <div class="stat-sub"><?= htmlspecialchars($current_period) ?></div>
          <div class="stat-icon">💰</div>
        </div>
        <div class="stat-card green">
          <div class="stat-label">Completed Payments</div>
          <div class="stat-val"><?= $pay_completed ?></div>
          <div class="stat-sub">Transactions settled</div>
          <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card orange">
          <div class="stat-label">Pending / Failed</div>
          <div class="stat-val"><?= $pay_pending + $pay_failed ?></div>
          <div class="stat-sub"><?= $pay_pending ?> Pending · <?= $pay_failed ?> Failed</div>
          <div class="stat-icon">⚠️</div>
        </div>
      </div>

      <div class="grid-chart-table">
        <div class="card">
          <div class="card-title">Revenue by Payment Mode</div>
          <div class="chart-wrap"><canvas id="payModeChart" height="200"></canvas></div>
        </div>
        <div class="card">
          <div class="card-title">Mode-wise Breakdown</div>
          <?php if(count($pay_by_mode)>0): ?>
          <table class="data-table">
            <thead><tr><th>Payment Mode</th><th>Count</th><th>Revenue</th></tr></thead>
            <tbody>
            <?php foreach($pay_by_mode as $pm): ?>
            <tr>
              <td><?= htmlspecialchars($pm['PAYMENT_MODE']) ?></td>
              <td class="num"><?= $pm['cnt'] ?></td>
              <td class="num">₹<?= number_format($pm['total'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No payment data for this period.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ 3. REMINDERS ════ -->
    <section id="reminders">
      <div class="sec-head">
        <h2>Reminders</h2>
        <span class="sec-tag">🔔 Section 3</span>
      </div>

      <div class="stat-row cols-2" style="margin-bottom:20px;">
        <div class="stat-card teal">
          <div class="stat-label">Appointment Reminders Sent</div>
          <div class="stat-val"><?= $appt_rem_total ?></div>
          <div class="stat-sub">For appointments in this period</div>
          <div class="stat-icon">📩</div>
        </div>
        <div class="stat-card purple">
          <div class="stat-label">Medicine Reminders Active</div>
          <div class="stat-val"><?= $med_rem_total ?></div>
          <div class="stat-sub">Overlapping this period</div>
          <div class="stat-icon">💊</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="card">
          <div class="card-title">Appointment Reminders — Weekly (<?= $month_names[$selected_month] ?>)</div>
          <?php if(count($appt_rem_weekly)>0): ?>
          <div class="week-grid">
            <?php foreach($appt_rem_weekly as $w): ?>
            <div class="week-cell">
              <div class="wlbl">Week <?= $w['week_num'] ?></div>
              <div class="wnum" style="color:var(--purple);"><?= $w['cnt'] ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No reminder data for this month.</p>
          <?php endif; ?>
        </div>
        <div class="card">
          <div class="card-title">Medicine Reminders by Creator</div>
          <?php if(count($med_rem_by_creator)>0):
            $med_rem_sum = array_sum(array_column($med_rem_by_creator,'cnt'));
          ?>
          <?php foreach($med_rem_by_creator as $cr):
            $pct = $med_rem_sum > 0 ? round($cr['cnt']/$med_rem_sum*100) : 0;
            $col = $cr['CREATOR_ROLE']=='RECEPTIONIST' ? 'var(--teal)' : 'var(--mint)';
          ?>
          <div class="prog-row">
            <div class="prog-label"><?= htmlspecialchars($cr['CREATOR_ROLE']) ?></div>
            <div class="prog-bar-wrap">
              <div class="prog-bar" style="width:<?=$pct?>%;background:<?=$col?>;"></div>
            </div>
            <div class="prog-val"><?= $cr['cnt'] ?></div>
          </div>
          <?php endforeach; ?>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No medicine reminder data for this period.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ 4. PRESCRIPTIONS ════ -->
    <section id="prescriptions">
      <div class="sec-head">
        <h2>Prescriptions</h2>
        <span class="sec-tag">📄 Section 4</span>
      </div>

      <div class="stat-row cols-2" style="margin-bottom:20px;">
        <div class="stat-card teal">
          <div class="stat-label">Prescriptions Issued</div>
          <div class="stat-val"><?= $presc_total ?></div>
          <div class="stat-sub"><?= htmlspecialchars($current_period) ?></div>
          <div class="stat-icon">📄</div>
        </div>
        <div class="stat-card gold">
          <div class="stat-label">Avg per Month (Last 6)</div>
          <?php $avg_presc = count($presc_monthly)>0 ? round(array_sum(array_column($presc_monthly,'cnt'))/count($presc_monthly),1) : 0; ?>
          <div class="stat-val"><?= $avg_presc ?></div>
          <div class="stat-sub">Based on trend data</div>
          <div class="stat-icon">📊</div>
        </div>
      </div>

      <div class="grid-chart-table">
        <div class="card">
          <div class="card-title">Monthly Prescription Trend (Last 6 Months)</div>
          <div class="chart-wrap"><canvas id="prescTrendChart" height="200"></canvas></div>
        </div>
        <div class="card">
          <div class="card-title">Prescriptions by Doctor</div>
          <?php if(count($presc_by_doctor)>0): ?>
          <table class="data-table">
            <thead><tr><th>Doctor</th><th>Specialisation</th><th>Prescriptions</th></tr></thead>
            <tbody>
            <?php foreach($presc_by_doctor as $pd): ?>
            <tr>
              <td>Dr. <?= htmlspecialchars($pd['doctor']) ?></td>
              <td><span class="pill pill-teal"><?= htmlspecialchars($pd['spec']) ?></span></td>
              <td class="num"><?= $pd['cnt'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No prescription data for this period.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ 5. FEEDBACK ════ -->
    <section id="feedback">
      <div class="sec-head">
        <h2>Feedback & Ratings</h2>
        <span class="sec-tag">⭐ Section 5</span>
      </div>

      <div class="stat-row cols-2" style="margin-bottom:20px;">
        <div class="stat-card teal">
          <div class="stat-label">Total Feedback Received</div>
          <div class="stat-val"><?= $fb_total ?></div>
          <div class="stat-sub"><?= htmlspecialchars($current_period) ?></div>
          <div class="stat-icon">💬</div>
        </div>
        <div class="stat-card gold">
          <div class="stat-label">Overall Avg Rating</div>
          <div class="stat-val"><?= $fb_avg_all ?: 'N/A' ?></div>
          <div class="stat-sub">
            <?php if($fb_avg_all): ?>
            <span class="stars"><?= str_repeat('★', round($fb_avg_all)) . str_repeat('☆', 5-round($fb_avg_all)) ?></span>
            <?php else: echo 'No ratings yet'; endif; ?>
          </div>
          <div class="stat-icon">⭐</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="card">
          <div class="card-title">Rating Distribution</div>
          <?php
          $all_stars = [5,4,3,2,1];
          $fb_map = [];
          foreach($fb_dist as $fd) $fb_map[$fd['RATING']] = $fd['cnt'];
          $fb_max = $fb_total > 0 ? $fb_total : 1;
          $star_colors = [5=>'#22c55e',4=>'#86efac',3=>'#f4a118',2=>'#f97316',1=>'#ef4444'];
          foreach($all_stars as $s):
            $cnt = $fb_map[$s] ?? 0;
            $pct = round($cnt/$fb_max*100);
          ?>
          <div class="prog-row">
            <div class="prog-label"><span class="stars"><?= str_repeat('★',$s) ?></span></div>
            <div class="prog-bar-wrap">
              <div class="prog-bar" style="width:<?=$pct?>%;background:<?=$star_colors[$s]?>;"></div>
            </div>
            <div class="prog-val"><?= $cnt ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="card">
          <div class="card-title">Feedback per Doctor</div>
          <?php if(count($fb_by_doctor)>0): ?>
          <table class="data-table">
            <thead><tr><th>Doctor</th><th>Specialisation</th><th>Reviews</th><th>Avg Rating</th></tr></thead>
            <tbody>
            <?php foreach($fb_by_doctor as $fb): ?>
            <tr>
              <td>Dr. <?= htmlspecialchars($fb['doctor']) ?></td>
              <td><span class="pill pill-teal"><?= htmlspecialchars($fb['spec']) ?></span></td>
              <td class="num"><?= $fb['cnt'] ?></td>
              <td>
                <span class="stars" style="font-size:13px;"><?= str_repeat('★',round($fb['avg_rating'])) ?></span>
                <span style="font-size:12px;color:var(--muted);margin-left:4px;"><?= $fb['avg_rating'] ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
          <p style="color:var(--muted);font-size:13px;">No feedback data for this period.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ 6. DOCTOR SUMMARY ════ -->
    <section id="doctors">
      <div class="sec-head">
        <h2>Doctor-wise Summary</h2>
        <span class="sec-tag">🩺 Section 6</span>
      </div>

      <div class="card">
        <div class="card-title">All Doctors — Appointment, Prescription & Feedback Summary</div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Doctor</th>
              <th>Specialisation</th>
              <th>Total Appts</th>
              <th>Completed</th>
              <th>Scheduled</th>
              <th>Cancelled</th>
              <th>Prescriptions</th>
              <th>Avg Rating</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($doctor_summary as $ds): ?>
          <tr>
            <td style="font-weight:600;">Dr. <?= htmlspecialchars($ds['doctor']) ?></td>
            <td><span class="pill pill-teal"><?= htmlspecialchars($ds['spec']) ?></span></td>
            <td class="num"><?= $ds['total_appt'] ?></td>
            <td><span class="pill pill-green"><?= $ds['completed'] ?></span></td>
            <td><span class="pill pill-orange"><?= $ds['scheduled'] ?></span></td>
            <td><span class="pill <?= $ds['cancelled']>0?'pill-red':'pill-gray' ?>"><?= $ds['cancelled'] ?></span></td>
            <td class="num"><?= $ds['prescriptions'] ?></td>
            <td>
              <?php if($ds['avg_rating']): ?>
                <span class="stars"><?= str_repeat('★',round($ds['avg_rating'])) ?></span>
                <span style="font-size:11px;color:var(--muted);margin-left:3px;"><?= $ds['avg_rating'] ?></span>
              <?php else: ?>
                <span style="color:var(--muted);font-size:12px;">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </div><!-- /content -->
</div><!-- /main -->

<!-- ════════════ CHARTS ════════════ -->
<script>
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.color = '#5b7f8a';

const teal   = '#028090';
const mint   = '#02c39a';
const orange = '#f07d35';
const red    = '#e84040';
const gold   = '#f4a118';
const purple = '#8b5cf6';

// 1. Appointment Trend
new Chart(document.getElementById('apptTrendChart'), {
  type: 'bar',
  data: {
    labels: <?= $trend_labels ?>,
    datasets: [{
      label: 'Appointments',
      data: <?= $trend_data ?>,
      backgroundColor: 'rgba(2,128,144,0.15)',
      borderColor: teal,
      borderWidth: 2,
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#eef5f7' } },
      x: { grid: { display: false } }
    }
  }
});

// 2. Payment Mode (Doughnut)
const payLabels = <?= $mode_labels ?>;
const payData   = <?= $mode_data ?>;
if (payLabels.length > 0) {
  new Chart(document.getElementById('payModeChart'), {
    type: 'doughnut',
    data: {
      labels: payLabels,
      datasets: [{
        data: payData,
        backgroundColor: [teal, mint, orange, gold],
        borderColor: '#fff',
        borderWidth: 3,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      cutout: '62%',
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } },
        tooltip: {
          callbacks: {
            label: ctx => ` ₹${ctx.parsed.toLocaleString('en-IN', {minimumFractionDigits:2})}`
          }
        }
      }
    }
  });
}

// 3. Prescription Trend
new Chart(document.getElementById('prescTrendChart'), {
  type: 'line',
  data: {
    labels: <?= $presc_labels ?>,
    datasets: [{
      label: 'Prescriptions',
      data: <?= $presc_data ?>,
      borderColor: gold,
      backgroundColor: 'rgba(244,161,24,0.08)',
      borderWidth: 2.5,
      pointBackgroundColor: gold,
      pointRadius: 5,
      tension: 0.35,
      fill: true
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#eef5f7' } },
      x: { grid: { display: false } }
    }
  }
});

// Smooth sidebar active highlight on scroll
const sections = document.querySelectorAll('section[id]');
const navLinks  = document.querySelectorAll('.nav-link');
window.addEventListener('scroll', () => {
  let cur = '';
  sections.forEach(s => { if (window.scrollY >= s.offsetTop - 120) cur = s.id; });
  navLinks.forEach(l => {
    l.classList.toggle('active', l.getAttribute('href') === '#' + cur);
  });
});
</script>
</body>
</html>
