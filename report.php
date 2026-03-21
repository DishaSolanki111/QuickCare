<?php
/**
 * QuickCare – Reports Hub  (Level 1)
 * File:  C:\xampp\htdocs\QuickCare\QuickCare\report.php
 *
 * HOW SIDEBAR WORKS:
 *   – If  Admin_sidebar.php  exists in the same folder it is auto-included.
 *   – Otherwise a built-in fallback sidebar (identical look) is used.
 *
 * Update the four DB lines below to match your settings.
 */
session_start();

/* ── DB ──────────────────────────────────────────── */
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "quick_care";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

function qn($c,$s){ $r=$c->query($s); if(!$r)return 0; $row=$r->fetch_row(); return $row?$row[0]:0; }

/* ── COUNTS ──────────────────────────────────────── */
$total_appt   = qn($conn,"SELECT COUNT(*) FROM appointment_tbl");
$done_appt    = qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='COMPLETED'");
$sched_appt   = qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='SCHEDULED'");
$revenue      = qn($conn,"SELECT COALESCE(SUM(AMOUNT),0) FROM payment_tbl WHERE STATUS='COMPLETED'");
$done_pay     = qn($conn,"SELECT COUNT(*) FROM payment_tbl WHERE STATUS='COMPLETED'");
$pend_pay     = qn($conn,"SELECT COUNT(*) FROM payment_tbl WHERE STATUS='PENDING'");
$total_presc  = qn($conn,"SELECT COUNT(*) FROM prescription_tbl");
$presc_docs   = qn($conn,"SELECT COUNT(DISTINCT a.DOCTOR_ID) FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID=a.APPOINTMENT_ID");
$presc_meds   = qn($conn,"SELECT COUNT(*) FROM prescription_medicine_tbl");
$appt_rem     = qn($conn,"SELECT COUNT(*) FROM appointment_reminder_tbl");
$med_rem      = qn($conn,"SELECT COUNT(*) FROM medicine_reminder_tbl");
$total_fb     = qn($conn,"SELECT COUNT(*) FROM feedback_tbl");
$avg_rating   = qn($conn,"SELECT ROUND(AVG(RATING),1) FROM feedback_tbl");
$five_star    = qn($conn,"SELECT COUNT(*) FROM feedback_tbl WHERE RATING=5");
$total_docs   = qn($conn,"SELECT COUNT(*) FROM doctor_tbl");
$total_specs  = qn($conn,"SELECT COUNT(DISTINCT SPECIALISATION_ID) FROM doctor_tbl");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reports – QuickCare</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ─── RESET ─────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

/* ─── PALETTE (from your colour swatch) ─────────── */
:root{
  --c1:#072D44;   /* darkest navy  – sidebar bg, table header */
  --c2:#064469;   /* navy          – active item, buttons     */
  --c3:#5790AB;   /* blue          – accents, borders         */
  --c4:#9CCDDB;   /* light blue    – hover tint, badges       */
  --c5:#D0D7E1;   /* pale grey-blue– borders, bg stripe       */
  --white:#ffffff;
  --bg:#f0f4f8;
  --border:#dde6ed;
  --text:#1a2e3b;
  --muted:#5b7f8a;
  --green:#198754;
  --orange:#e07b19;
  --red:#c0392b;
  --gold:#b07d00;
  --sw:250px;       /* sidebar width – matches your screenshots */
}

body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
     background:var(--bg);color:var(--text);display:flex;min-height:100vh;font-size:14px}

/* ─── SIDEBAR WRAPPER ────────────────────────────── */
.sb{width:var(--sw);flex-shrink:0;position:fixed;top:0;left:0;bottom:0;
    z-index:300;background:var(--c1);overflow-y:auto}

/* ─── MAIN ───────────────────────────────────────── */
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh}

/* ─── TOPBAR  (exactly matches your screenshots) ─── */
.topbar{background:var(--white);padding:15px 26px;
        display:flex;align-items:center;justify-content:space-between;
        border-bottom:1px solid var(--border);
        box-shadow:0 1px 3px rgba(0,0,0,.06)}
.topbar-title{font-size:22px;font-weight:700;color:var(--c1)}
.admin-wrap{display:flex;align-items:center;gap:10px}
.admin-info{text-align:right}
.admin-info .aname{font-size:14px;font-weight:700;color:var(--c2)}
.admin-info .adate{font-size:11px;color:var(--muted)}
.admin-av{width:38px;height:38px;border-radius:50%;background:var(--c2);
          color:#fff;display:flex;align-items:center;justify-content:center;
          font-size:12px;font-weight:700}

/* ─── PAGE BODY ──────────────────────────────────── */
.body{padding:24px 26px;flex:1}

/* ─── PAGE HEADER (blue-left-border style) ──────── */
.ph{background:var(--white);border-radius:8px;padding:18px 22px;
    margin-bottom:22px;border-left:4px solid var(--c3);
    box-shadow:0 1px 4px rgba(0,0,0,.05)}
.ph h2{font-size:18px;font-weight:700;color:var(--c2);
       display:flex;align-items:center;gap:10px}
.ph h2 i{color:var(--c3)}
.ph p{font-size:12.5px;color:var(--muted);margin-top:5px}

/* ─── OVERVIEW STRIP ─────────────────────────────── */
.ov{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:22px}
.ov-c{background:var(--white);border:1px solid var(--border);border-radius:8px;
      padding:13px 15px;display:flex;align-items:center;gap:12px;
      box-shadow:0 1px 3px rgba(0,0,0,.04)}
.ov-ic{width:40px;height:40px;border-radius:8px;background:var(--c5);
       display:flex;align-items:center;justify-content:center;
       font-size:16px;color:var(--c2);flex-shrink:0}
.ov-v{font-size:18px;font-weight:700;color:var(--c1);line-height:1}
.ov-l{font-size:10px;color:var(--muted);margin-top:2px}

/* ─── SECTION LABEL ──────────────────────────────── */
.sl{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;
    color:var(--muted);display:flex;align-items:center;gap:10px;margin-bottom:16px}
.sl::after{content:'';flex:1;height:1px;background:var(--border)}

/* ─── CARDS GRID ─────────────────────────────────── */
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}

/* ─── REPORT CARD ────────────────────────────────── */
.card{background:var(--white);border:1px solid var(--border);border-radius:8px;
      overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);
      display:flex;flex-direction:column;
      transition:box-shadow .2s,transform .2s}
.card:hover{box-shadow:0 8px 24px rgba(7,45,68,.13);transform:translateY(-3px)}

/* top colour bar */
.bar{height:4px}

/* card inner */
.ci{padding:18px 20px 14px;flex:1}
.ci-top{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.ci-ico{width:44px;height:44px;border-radius:9px;
        display:flex;align-items:center;justify-content:center;
        font-size:19px;flex-shrink:0}
.ci-title{font-size:15px;font-weight:700;color:var(--c1)}
.ci-desc{font-size:12px;color:var(--muted);line-height:1.65}

/* mini stats row */
.msr{display:flex;border-top:1px solid var(--border)}
.ms{flex:1;padding:10px 8px;text-align:center;border-right:1px solid var(--border)}
.ms:last-child{border-right:none}
.ms-v{font-size:16px;font-weight:700;color:var(--c1);line-height:1}
.ms-l{font-size:9px;color:var(--muted);text-transform:uppercase;
      letter-spacing:.7px;margin-top:2px}

/* view button */
.vbtn{display:block;margin:12px 14px 14px;padding:9px 12px;border-radius:7px;
      font-size:13px;font-weight:600;color:#fff;text-decoration:none;
      text-align:center;background:var(--c2);transition:opacity .18s}
.vbtn:hover{opacity:.85}
.vbtn i{margin-right:6px}

/* ─── COLOUR THEMES (bar + icon bg) ─────────────── */
.t1 .bar{background:linear-gradient(90deg,var(--c1),var(--c3))}
.t1 .ci-ico{background:#deeaf2;color:var(--c2)}

.t2 .bar{background:linear-gradient(90deg,#145a32,var(--green))}
.t2 .ci-ico{background:#d5f0e3;color:var(--green)}

.t3 .bar{background:linear-gradient(90deg,var(--c2),var(--c3))}
.t3 .ci-ico{background:#daeef5;color:var(--c3)}

.t4 .bar{background:linear-gradient(90deg,#922b21,var(--red))}
.t4 .ci-ico{background:#fce4e1;color:var(--red)}

.t5 .bar{background:linear-gradient(90deg,#7d6608,var(--gold))}
.t5 .ci-ico{background:#fef9e7;color:var(--gold)}

.t6 .bar{background:linear-gradient(90deg,#1a2a35,var(--c1))}
.t6 .ci-ico{background:#dce4ea;color:var(--c1)}

/* ─── ANIMATIONS ─────────────────────────────────── */
@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.card{animation:fu .35s ease both}
.card:nth-child(1){animation-delay:.05s}.card:nth-child(2){animation-delay:.10s}
.card:nth-child(3){animation-delay:.15s}.card:nth-child(4){animation-delay:.20s}
.card:nth-child(5){animation-delay:.25s}.card:nth-child(6){animation-delay:.30s}

/* ─── RESPONSIVE ─────────────────────────────────── */
@media(max-width:1100px){.grid{grid-template-columns:1fr 1fr}.ov{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.grid{grid-template-columns:1fr}.ov{grid-template-columns:1fr 1fr}}
@media print{.sb{display:none}.main{margin-left:0}}
</style>
</head>
<body>

<!-- ════ SIDEBAR ════ -->
<div class="sb">
<?php
/* ── include your sidebar or render fallback ── */
if (file_exists('Admin_sidebar.php')) {
    include 'Admin_sidebar.php';
} else {
    /* Fallback: visually identical to your screenshots */
    $nav = [
        ['Home',           'fa-house',             'Admin_home.php'],
        ['Dashboard',      'fa-gauge',             'Admin_dashboard.php'],
        ['Appointments',   'fa-calendar-check',    'Admin_appointment.php'],
        ['Doctors',        'fa-user-doctor',       'Admin_doctor.php'],
        ['Receptionist',   'fa-headset',           'Admin_receptionist.php'],
        ['Patients',       'fa-hospital-user',     'Admin_patient.php'],
        ['Doctor Schedule','fa-calendar-days',     'Admin_schedule.php'],
        ['Prescriptions',  'fa-file-prescription', 'Admin_prescription.php'],
        ['Payments',       'fa-credit-card',       'Admin_payment.php'],
        ['Feedback',       'fa-star',              'Admin_feedback.php'],
        ['Reports',        'fa-chart-bar',         'report.php'],
    ];
    $cur = basename($_SERVER['PHP_SELF']);
    echo '<div style="display:flex;flex-direction:column;height:100%">';

    /* logo */
    echo '<div style="padding:18px 14px 16px;text-align:center;border-bottom:1px solid rgba(255,255,255,.09)">';
    echo '<div style="width:54px;height:54px;border-radius:50%;background:#064469;margin:0 auto 8px;'
        .'display:flex;align-items:center;justify-content:center;font-size:22px;color:#9CCDDB">'
        .'<i class="fas fa-kit-medical"></i></div>';
    echo '<div style="color:#fff;font-size:15px;font-weight:700">QuickCare</div></div>';

    /* nav items */
    echo '<nav style="padding:10px 8px;flex:1">';
    foreach ($nav as $n) {
        $a  = ($cur === basename($n[2]));
        $bg = $a ? 'background:rgba(87,144,171,.22);' : '';
        $cl = $a ? 'color:#9CCDDB;font-weight:600;' : 'color:rgba(255,255,255,.70);';
        echo "<a href='{$n[2]}' style='display:flex;align-items:center;gap:10px;"
            ."padding:9px 11px;border-radius:7px;text-decoration:none;"
            ."font-size:13px;margin-bottom:2px;{$bg}{$cl}'>"
            ."<i class='fas {$n[1]}' style='width:15px;text-align:center;font-size:13px'></i>"
            ."{$n[0]}</a>";
    }
    echo '</nav>';

    /* logout */
    echo '<div style="padding:10px 8px;border-top:1px solid rgba(255,255,255,.08)">';
    echo "<a href='logout.php' style='display:flex;align-items:center;justify-content:center;"
        ."gap:8px;padding:9px;border-radius:7px;background:#064469;color:#fff;"
        ."text-decoration:none;font-size:13px;font-weight:600'>"
        ."<i class='fas fa-right-from-bracket'></i> Logout</a>";
    echo '</div></div>';
}
?>
</div>

<!-- ════ MAIN CONTENT ════ -->
<div class="main">

  <!-- topbar -->
  <div class="topbar">
    <div class="topbar-title">Welcome back</div>
    <div class="admin-wrap">
      <div class="admin-info">
        <div class="aname">Admin</div>
        <div class="adate"><?= date('F d, Y') ?></div>
      </div>
      <div class="admin-av">AD</div>
    </div>
  </div>

  <div class="body">

    <!-- page header -->
    <div class="ph">
      <h2><i class="fas fa-chart-bar"></i> Reports &amp; Analytics</h2>
      <p>Select a report category below to view detailed statistics, charts and full data tables.</p>
    </div>

    <!-- overview strip -->
    <div class="ov">
      <div class="ov-c"><div class="ov-ic"><i class="fas fa-calendar-check"></i></div>
        <div><div class="ov-v"><?=$total_appt?></div><div class="ov-l">Appointments</div></div></div>
      <div class="ov-c"><div class="ov-ic"><i class="fas fa-indian-rupee-sign"></i></div>
        <div><div class="ov-v">₹<?=number_format($revenue)?></div><div class="ov-l">Revenue</div></div></div>
      <div class="ov-c"><div class="ov-ic"><i class="fas fa-file-prescription"></i></div>
        <div><div class="ov-v"><?=$total_presc?></div><div class="ov-l">Prescriptions</div></div></div>
      <div class="ov-c"><div class="ov-ic"><i class="fas fa-bell"></i></div>
        <div><div class="ov-v"><?=$appt_rem+$med_rem?></div><div class="ov-l">Reminders</div></div></div>
      <div class="ov-c"><div class="ov-ic"><i class="fas fa-star"></i></div>
        <div><div class="ov-v"><?=$avg_rating?>/5</div><div class="ov-l">Avg Rating</div></div></div>
    </div>

    <!-- report cards -->
    <div class="sl">Choose Report</div>
    <div class="grid">

      <!-- 1. APPOINTMENT -->
      <div class="card t1">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-calendar-check"></i></div>
            <div class="ci-title">Appointment Report</div>
          </div>
          <div class="ci-desc">Monthly &amp; weekly counts, Scheduled / Completed / Cancelled breakdown, trend charts and full appointment records table with patient &amp; doctor details.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v"><?=$total_appt?></div><div class="ms-l">Total</div></div>
          <div class="ms"><div class="ms-v" style="color:var(--green)"><?=$done_appt?></div><div class="ms-l">Completed</div></div>
          <div class="ms"><div class="ms-v" style="color:var(--orange)"><?=$sched_appt?></div><div class="ms-l">Scheduled</div></div>
        </div>
        <a href="http://localhost/QuickCare/QuickCare/Admin_report/ViewAppointmentReport" class="vbtn">
   <i class="fas fa-arrow-right"></i>View Appointment Report
</a></div>

      <!-- 2. PAYMENT -->
      <div class="card t2">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-credit-card"></i></div>
            <div class="ci-title">Payment Report</div>
          </div>
          <div class="ci-desc">Total revenue, payment mode breakdown (UPI / Credit Card / Net Banking / Google Pay), pending &amp; failed transactions and full transaction records.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v">₹<?=number_format($revenue)?></div><div class="ms-l">Revenue</div></div>
          <div class="ms"><div class="ms-v" style="color:var(--green)"><?=$done_pay?></div><div class="ms-l">Paid</div></div>
          <div class="ms"><div class="ms-v" style="color:var(--orange)"><?=$pend_pay?></div><div class="ms-l">Pending</div></div>
        </div>
        <a href="report_detail.php?type=payment" class="vbtn" style="background:var(--green)"><i class="fas fa-arrow-right"></i>View Payment Report</a>
      </div>

      <!-- 3. PRESCRIPTION -->
      <div class="card t3">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-file-prescription"></i></div>
            <div class="ci-title">Prescription Report</div>
          </div>
          <div class="ci-desc">Prescriptions per month, doctor-wise count, diagnosis trends, full records with symptoms, diagnosis, BP, diabetes status and doctor notes.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v"><?=$total_presc?></div><div class="ms-l">Total</div></div>
          <div class="ms"><div class="ms-v"><?=$presc_docs?></div><div class="ms-l">Doctors</div></div>
          <div class="ms"><div class="ms-v"><?=$presc_meds?></div><div class="ms-l">Medicines</div></div>
        </div>
        <a href="report_detail.php?type=prescription" class="vbtn" style="background:var(--c3)"><i class="fas fa-arrow-right"></i>View Prescription Report</a>
      </div>

      <!-- 4. REMINDER -->
      <div class="card t4">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-bell"></i></div>
            <div class="ci-title">Reminder Report</div>
          </div>
          <div class="ci-desc">Appointment reminder counts by week, medicine reminder activity per patient, creator breakdown (Receptionist / Patient) and full reminder tables.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v"><?=$appt_rem?></div><div class="ms-l">Appt Rem</div></div>
          <div class="ms"><div class="ms-v"><?=$med_rem?></div><div class="ms-l">Med Rem</div></div>
          <div class="ms"><div class="ms-v"><?=$appt_rem+$med_rem?></div><div class="ms-l">Total</div></div>
        </div>
        <a href="report_detail.php?type=reminder" class="vbtn" style="background:var(--red)"><i class="fas fa-arrow-right"></i>View Reminder Report</a>
      </div>

      <!-- 5. FEEDBACK -->
      <div class="card t5">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-star"></i></div>
            <div class="ci-title">Feedback Report</div>
          </div>
          <div class="ci-desc">Patient ratings per doctor, star distribution chart, average rating by specialisation and all patient feedback comments with appointment details.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v"><?=$total_fb?></div><div class="ms-l">Reviews</div></div>
          <div class="ms"><div class="ms-v"><?=$avg_rating?></div><div class="ms-l">Avg Stars</div></div>
          <div class="ms"><div class="ms-v" style="color:var(--green)"><?=$five_star?></div><div class="ms-l">5-Star</div></div>
        </div>
        <a href="report_detail.php?type=feedback" class="vbtn" style="background:var(--gold)"><i class="fas fa-arrow-right"></i>View Feedback Report</a>
      </div>

      <!-- 6. DOCTOR SUMMARY -->
      <div class="card t6">
        <div class="bar"></div>
        <div class="ci">
          <div class="ci-top">
            <div class="ci-ico"><i class="fas fa-user-doctor"></i></div>
            <div class="ci-title">Doctor Summary Report</div>
          </div>
          <div class="ci-desc">Per-doctor appointment load, completion rates, prescriptions issued, patient feedback ratings, education details and full performance summary table.</div>
        </div>
        <div class="msr">
          <div class="ms"><div class="ms-v"><?=$total_docs?></div><div class="ms-l">Doctors</div></div>
          <div class="ms"><div class="ms-v"><?=$total_specs?></div><div class="ms-l">Specs</div></div>
          <div class="ms"><div class="ms-v"><?=$total_appt?></div><div class="ms-l">Appts</div></div>
        </div>
        <a href="report_detail.php?type=doctor" class="vbtn"><i class="fas fa-arrow-right"></i>View Doctor Report</a>
      </div>

    </div><!-- /grid -->
  </div><!-- /body -->
</div><!-- /main -->
</body>
</html>
