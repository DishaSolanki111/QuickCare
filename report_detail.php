<?php
/**
 * QuickCare – Report Detail Page  (Level 2)
 * File:  C:\xampp\htdocs\QuickCare\QuickCare\report_detail.php
 * Called from report.php via: report_detail.php?type=appointment|payment|prescription|reminder|feedback|doctor
 */
session_start();

/* ── DB ─────────────────────────────────────── */
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "quick_care";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

function qr($c,$s){$r=$c->query($s);if(!$r)return[];$d=[];while($row=$r->fetch_assoc())$d[]=$row;return $d;}
function qn($c,$s){$r=$c->query($s);if(!$r)return 0;$row=$r->fetch_row();return $row?$row[0]:0;}

/* ── FILTERS ─────────────────────────────────── */
$type  = in_array($_GET['type']??'',['appointment','payment','prescription','reminder','feedback','doctor'])
         ? $_GET['type'] : 'appointment';
$yr    = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
$mo    = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$wk    = isset($_GET['week'])  ? (int)$_GET['week']  : 0;

$ms    = sprintf('%04d-%02d-01',$yr,$mo);
$me    = date('Y-m-t',strtotime($ms));

if ($wk > 0) {
    $fp  = strtotime($ms);
    $ws  = date('Y-m-d',strtotime('+'.( ($wk-1)*7).' days',$fp));
    $we  = date('Y-m-d',strtotime('+6 days',strtotime($ws)));
    if ($we > $me) $we = $me;
    $df  = $ws; $dt = $we;
} else { $df = $ms; $dt = $me; }

$MN = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$period = $wk>0 ? "Week $wk of {$MN[$mo]} $yr" : "{$MN[$mo]} $yr";
$years  = qr($conn,"SELECT DISTINCT YEAR(APPOINTMENT_DATE) y FROM appointment_tbl ORDER BY y DESC");

/* ── REPORT META ─────────────────────────────── */
$M = [
  'appointment' =>['icon'=>'fa-calendar-check','title'=>'Appointment Report',  'ac'=>'#064469'],
  'payment'     =>['icon'=>'fa-credit-card',   'title'=>'Payment Report',       'ac'=>'#198754'],
  'prescription'=>['icon'=>'fa-file-prescription','title'=>'Prescription Report','ac'=>'#5790AB'],
  'reminder'    =>['icon'=>'fa-bell',          'title'=>'Reminder Report',      'ac'=>'#c0392b'],
  'feedback'    =>['icon'=>'fa-star',          'title'=>'Feedback Report',      'ac'=>'#b07d00'],
  'doctor'      =>['icon'=>'fa-user-doctor',   'title'=>'Doctor Summary Report','ac'=>'#072D44'],
][$type];

/* ══════════════════════════════════════════════
   DATA QUERIES
══════════════════════════════════════════════ */

/* ── APPOINTMENT ── */
if ($type==='appointment') {
    $s=[
        'total'   =>qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'done'    =>qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='COMPLETED' AND APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'sched'   =>qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='SCHEDULED' AND APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'cancel'  =>qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE STATUS='CANCELLED'  AND APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
    ];
    $trend  = qr($conn,"SELECT DATE_FORMAT(APPOINTMENT_DATE,'%b %Y') lbl,MONTH(APPOINTMENT_DATE) m,YEAR(APPOINTMENT_DATE) y,COUNT(*) cnt FROM appointment_tbl WHERE APPOINTMENT_DATE>=DATE_SUB('$me',INTERVAL 5 MONTH) GROUP BY y,m ORDER BY y,m");
    $weekly = qr($conn,"SELECT WEEK(APPOINTMENT_DATE,1)-WEEK('$ms',1)+1 wn,COUNT(*) cnt,SUM(STATUS='COMPLETED') dn,SUM(STATUS='SCHEDULED') sc FROM appointment_tbl WHERE APPOINTMENT_DATE BETWEEN '$ms' AND '$me' GROUP BY wn ORDER BY wn");
    $rows   = qr($conn,"SELECT a.APPOINTMENT_ID,CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) pat,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,s.SPECIALISATION_NAME sp,a.APPOINTMENT_DATE,a.APPOINTMENT_TIME,a.STATUS,a.CREATED_AT FROM appointment_tbl a JOIN patient_tbl p ON a.PATIENT_ID=p.PATIENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID JOIN specialisation_tbl s ON d.SPECIALISATION_ID=s.SPECIALISATION_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' ORDER BY a.APPOINTMENT_DATE DESC");
}

/* ── PAYMENT ── */
if ($type==='payment') {
    $s=[
        'rev'   =>qn($conn,"SELECT COALESCE(SUM(AMOUNT),0) FROM payment_tbl WHERE PAYMENT_DATE BETWEEN '$df' AND '$dt'"),
        'done'  =>qn($conn,"SELECT COUNT(*) FROM payment_tbl WHERE STATUS='COMPLETED' AND PAYMENT_DATE BETWEEN '$df' AND '$dt'"),
        'pend'  =>qn($conn,"SELECT COUNT(*) FROM payment_tbl WHERE STATUS='PENDING'   AND PAYMENT_DATE BETWEEN '$df' AND '$dt'"),
        'fail'  =>qn($conn,"SELECT COUNT(*) FROM payment_tbl WHERE STATUS='FAILED'    AND PAYMENT_DATE BETWEEN '$df' AND '$dt'"),
    ];
    $bymode = qr($conn,"SELECT PAYMENT_MODE,COUNT(*) cnt,SUM(AMOUNT) tot FROM payment_tbl WHERE PAYMENT_DATE BETWEEN '$df' AND '$dt' GROUP BY PAYMENT_MODE ORDER BY tot DESC");
    $trend  = qr($conn,"SELECT DATE_FORMAT(PAYMENT_DATE,'%b %Y') lbl,MONTH(PAYMENT_DATE) m,YEAR(PAYMENT_DATE) y,SUM(AMOUNT) amt FROM payment_tbl WHERE STATUS='COMPLETED' AND PAYMENT_DATE>=DATE_SUB('$me',INTERVAL 5 MONTH) GROUP BY y,m ORDER BY y,m");
    $rows   = qr($conn,"SELECT py.PAYMENT_ID,py.TRANSACTION_ID,CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) pat,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,py.AMOUNT,py.PAYMENT_MODE,py.STATUS,py.PAYMENT_DATE FROM payment_tbl py JOIN appointment_tbl a ON py.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN patient_tbl p ON a.PATIENT_ID=p.PATIENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID WHERE py.PAYMENT_DATE BETWEEN '$df' AND '$dt' ORDER BY py.PAYMENT_DATE DESC");
}

/* ── PRESCRIPTION ── */
if ($type==='prescription') {
    $s=[
        'total' =>qn($conn,"SELECT COUNT(*) FROM prescription_tbl WHERE ISSUE_DATE BETWEEN '$df' AND '$dt'"),
        'docs'  =>qn($conn,"SELECT COUNT(DISTINCT a.DOCTOR_ID) FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE p.ISSUE_DATE BETWEEN '$df' AND '$dt'"),
        'meds'  =>qn($conn,"SELECT COUNT(*) FROM prescription_medicine_tbl pm JOIN prescription_tbl p ON pm.PRESCRIPTION_ID=p.PRESCRIPTION_ID WHERE p.ISSUE_DATE BETWEEN '$df' AND '$dt'"),
        'diab'  =>qn($conn,"SELECT COUNT(*) FROM prescription_tbl WHERE DIABETES!='NO' AND ISSUE_DATE BETWEEN '$df' AND '$dt'"),
    ];
    $trend    = qr($conn,"SELECT DATE_FORMAT(ISSUE_DATE,'%b %Y') lbl,MONTH(ISSUE_DATE) m,YEAR(ISSUE_DATE) y,COUNT(*) cnt FROM prescription_tbl WHERE ISSUE_DATE>=DATE_SUB('$me',INTERVAL 5 MONTH) GROUP BY y,m ORDER BY y,m");
    $bydoc    = qr($conn,"SELECT CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,s.SPECIALISATION_NAME sp,COUNT(p.PRESCRIPTION_ID) cnt FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID JOIN specialisation_tbl s ON d.SPECIALISATION_ID=s.SPECIALISATION_ID WHERE p.ISSUE_DATE BETWEEN '$df' AND '$dt' GROUP BY d.DOCTOR_ID ORDER BY cnt DESC");
    $rows     = qr($conn,"SELECT p.PRESCRIPTION_ID,p.ISSUE_DATE,CONCAT(pt.FIRST_NAME,' ',pt.LAST_NAME) pat,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,p.SYMPTOMS,p.DIAGNOSIS,p.DIABETES,p.BLOOD_PRESSURE,p.ADDITIONAL_NOTES FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN patient_tbl pt ON a.PATIENT_ID=pt.PATIENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID WHERE p.ISSUE_DATE BETWEEN '$df' AND '$dt' ORDER BY p.ISSUE_DATE DESC");
}

/* ── REMINDER ── */
if ($type==='reminder') {
    $s=[
        'ar'   =>qn($conn,"SELECT COUNT(*) FROM appointment_reminder_tbl ar JOIN appointment_tbl a ON ar.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'mr'   =>qn($conn,"SELECT COUNT(*) FROM medicine_reminder_tbl WHERE START_DATE<='$dt' AND END_DATE>='$df'"),
        'pts'  =>qn($conn,"SELECT COUNT(DISTINCT PATIENT_ID) FROM medicine_reminder_tbl WHERE START_DATE<='$dt' AND END_DATE>='$df'"),
        'rec'  =>qn($conn,"SELECT COUNT(*) FROM medicine_reminder_tbl WHERE CREATOR_ROLE='RECEPTIONIST' AND START_DATE<='$dt' AND END_DATE>='$df'"),
    ];
    $wkrem  = qr($conn,"SELECT WEEK(a.APPOINTMENT_DATE,1)-WEEK('$ms',1)+1 wn,COUNT(*) cnt FROM appointment_reminder_tbl ar JOIN appointment_tbl a ON ar.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$ms' AND '$me' GROUP BY wn ORDER BY wn");
    $rows_a = qr($conn,"SELECT ar.APPOINTMENT_REMINDER_ID,CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) pat,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,a.APPOINTMENT_DATE,ar.REMINDER_TIME,ar.REMARKS FROM appointment_reminder_tbl ar JOIN appointment_tbl a ON ar.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN patient_tbl p ON a.PATIENT_ID=p.PATIENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' ORDER BY a.APPOINTMENT_DATE DESC LIMIT 60");
    $rows_m = qr($conn,"SELECT mr.MEDICINE_REMINDER_ID,m.MED_NAME med,CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) pat,mr.CREATOR_ROLE,mr.REMINDER_TIME,mr.START_DATE,mr.END_DATE,mr.REMARKS FROM medicine_reminder_tbl mr JOIN medicine_tbl m ON mr.MEDICINE_ID=m.MEDICINE_ID JOIN patient_tbl p ON mr.PATIENT_ID=p.PATIENT_ID WHERE mr.START_DATE<='$dt' AND mr.END_DATE>='$df' ORDER BY mr.START_DATE DESC LIMIT 60");
}

/* ── FEEDBACK ── */
if ($type==='feedback') {
    $s=[
        'total' =>qn($conn,"SELECT COUNT(*) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'avg'   =>qn($conn,"SELECT ROUND(AVG(f.RATING),1) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'five'  =>qn($conn,"SELECT COUNT(*) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE f.RATING=5 AND a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'one'   =>qn($conn,"SELECT COUNT(*) FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE f.RATING=1 AND a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
    ];
    $dist   = qr($conn,"SELECT f.RATING,COUNT(*) cnt FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' GROUP BY f.RATING ORDER BY f.RATING DESC");
    $bydoc  = qr($conn,"SELECT CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,s.SPECIALISATION_NAME sp,COUNT(f.FEEDBACK_ID) cnt,ROUND(AVG(f.RATING),1) avg_r FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID JOIN specialisation_tbl s ON d.SPECIALISATION_ID=s.SPECIALISATION_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' GROUP BY d.DOCTOR_ID ORDER BY avg_r DESC");
    $rows   = qr($conn,"SELECT f.FEEDBACK_ID,CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) pat,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,s.SPECIALISATION_NAME sp,f.RATING,f.COMMENTS,a.APPOINTMENT_DATE FROM feedback_tbl f JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID JOIN patient_tbl p ON a.PATIENT_ID=p.PATIENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID JOIN specialisation_tbl s ON d.SPECIALISATION_ID=s.SPECIALISATION_ID WHERE a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' ORDER BY a.APPOINTMENT_DATE DESC");
}

/* ── DOCTOR ── */
if ($type==='doctor') {
    $s=[
        'docs'  =>qn($conn,"SELECT COUNT(*) FROM doctor_tbl"),
        'specs' =>qn($conn,"SELECT COUNT(DISTINCT SPECIALISATION_ID) FROM doctor_tbl"),
        'appts' =>qn($conn,"SELECT COUNT(*) FROM appointment_tbl WHERE APPOINTMENT_DATE BETWEEN '$df' AND '$dt'"),
        'presc' =>qn($conn,"SELECT COUNT(*) FROM prescription_tbl WHERE ISSUE_DATE BETWEEN '$df' AND '$dt'"),
    ];
    $byspec = qr($conn,"SELECT s.SPECIALISATION_NAME sp,COUNT(DISTINCT d.DOCTOR_ID) docs,COUNT(DISTINCT a.APPOINTMENT_ID) appts FROM specialisation_tbl s LEFT JOIN doctor_tbl d ON s.SPECIALISATION_ID=d.SPECIALISATION_ID LEFT JOIN appointment_tbl a ON d.DOCTOR_ID=a.DOCTOR_ID AND a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' GROUP BY s.SPECIALISATION_ID ORDER BY appts DESC");
    $rows   = qr($conn,"SELECT d.DOCTOR_ID,CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) doc,s.SPECIALISATION_NAME sp,d.EDUCATION,d.STATUS,COUNT(DISTINCT a.APPOINTMENT_ID) ta,SUM(a.STATUS='COMPLETED') dn,SUM(a.STATUS='SCHEDULED') sc,SUM(a.STATUS='CANCELLED') cx,COUNT(DISTINCT p.PRESCRIPTION_ID) pr,ROUND(AVG(fb.RATING),1) ar FROM doctor_tbl d JOIN specialisation_tbl s ON d.SPECIALISATION_ID=s.SPECIALISATION_ID LEFT JOIN appointment_tbl a ON d.DOCTOR_ID=a.DOCTOR_ID AND a.APPOINTMENT_DATE BETWEEN '$df' AND '$dt' LEFT JOIN prescription_tbl p ON a.APPOINTMENT_ID=p.APPOINTMENT_ID LEFT JOIN feedback_tbl fb ON a.APPOINTMENT_ID=fb.APPOINTMENT_ID GROUP BY d.DOCTOR_ID ORDER BY ta DESC");
}

$conn->close();

/* ── CHART JSON ─────────────────────────────── */
$c1l=$c1d=$c2l=$c2d='[]';
if ($type==='appointment'){
    $c1l=json_encode(array_column($trend,'lbl')); $c1d=json_encode(array_column($trend,'cnt'));
    $c2l=json_encode(array_map(fn($r)=>"Wk {$r['wn']}",$weekly)); $c2d=json_encode(array_column($weekly,'cnt'));
}
if ($type==='payment'){
    $c1l=json_encode(array_column($trend,'lbl')); $c1d=json_encode(array_map(fn($r)=>(float)$r['amt'],$trend));
    $c2l=json_encode(array_column($bymode,'PAYMENT_MODE')); $c2d=json_encode(array_map(fn($r)=>(float)$r['tot'],$bymode));
}
if ($type==='prescription'){
    $c1l=json_encode(array_column($trend,'lbl')); $c1d=json_encode(array_column($trend,'cnt'));
    $c2l=json_encode(array_map(fn($r)=>"Dr.{$r['doc']}",$bydoc)); $c2d=json_encode(array_column($bydoc,'cnt'));
}
if ($type==='reminder'){
    $c1l=json_encode(array_map(fn($r)=>"Week {$r['wn']}",$wkrem)); $c1d=json_encode(array_column($wkrem,'cnt'));
}
if ($type==='feedback'){
    $fm=[]; foreach($dist as $d) $fm[$d['RATING']]=$d['cnt'];
    $c1l=json_encode(['5★','4★','3★','2★','1★']); $c1d=json_encode([($fm[5]??0),($fm[4]??0),($fm[3]??0),($fm[2]??0),($fm[1]??0)]);
    $c2l=json_encode(array_map(fn($r)=>"Dr.{$r['doc']}",$bydoc)); $c2d=json_encode(array_column($bydoc,'avg_r'));
}
if ($type==='doctor'){
    $c1l=json_encode(array_column($byspec,'sp')); $c1d=json_encode(array_column($byspec,'appts'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?=htmlspecialchars($M['title'])?> – QuickCare</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --c1:#072D44; --c2:#064469; --c3:#5790AB; --c4:#9CCDDB; --c5:#D0D7E1;
  --white:#fff; --bg:#f0f4f8; --border:#dde6ed;
  --text:#1a2e3b; --muted:#5b7f8a;
  --green:#198754; --orange:#e07b19; --red:#c0392b; --gold:#b07d00;
  --ac:<?=$M['ac']?>;
  --sw:250px;
}
body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
     background:var(--bg);color:var(--text);display:flex;min-height:100vh;font-size:14px}



/* main */
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh}

/* topbar */
.topbar{background:var(--white);padding:14px 26px;display:flex;align-items:center;
        justify-content:space-between;border-bottom:1px solid var(--border);
        box-shadow:0 1px 3px rgba(0,0,0,.06)}
.topbar-l{display:flex;align-items:center;gap:18px}
.topbar-title{font-size:20px;font-weight:700;color:var(--c1)}
.back{color:var(--c2);font-size:13px;text-decoration:none;font-weight:600;
      display:flex;align-items:center;gap:5px;border-left:1px solid var(--border);padding-left:16px}
.back:hover{color:var(--c3)}
.admin-wrap{display:flex;align-items:center;gap:10px}
.admin-av{width:36px;height:36px;border-radius:50%;background:var(--c2);
          color:#fff;display:flex;align-items:center;justify-content:center;
          font-size:12px;font-weight:700}

/* filter bar */
.fb{background:var(--white);padding:9px 26px;display:flex;align-items:center;
    gap:12px;flex-wrap:wrap;border-bottom:1px solid var(--border)}
.fb label{font-size:11px;font-weight:700;color:var(--muted);letter-spacing:.4px}
.fb select{border:1.5px solid var(--border);border-radius:7px;padding:5px 10px;
           font-size:13px;font-family:inherit;color:var(--text);background:var(--bg);outline:none;cursor:pointer}
.fb select:focus{border-color:var(--ac)}
.fb-apply{background:var(--ac);color:#fff;border:none;border-radius:7px;
          padding:6px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit}
.fb-apply:hover{opacity:.87}
.fb-print{background:var(--c1);color:#fff;border:none;border-radius:7px;
          padding:6px 13px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.fb-period{font-size:11.5px;color:var(--muted);margin-left:auto;
           border-left:1px solid var(--border);padding-left:12px}

/* page body */
.body{padding:22px 26px;flex:1}

/* page header – blue left border like your prescription page */
.ph{background:var(--white);border-radius:8px;padding:16px 20px;margin-bottom:20px;
    border-left:4px solid var(--ac);box-shadow:0 1px 4px rgba(0,0,0,.05);
    display:flex;align-items:center;gap:14px}
.ph-ico{width:46px;height:46px;border-radius:9px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        font-size:20px;color:var(--ac)}
.ph-title{font-size:17px;font-weight:700;color:var(--c1)}
.ph-sub{font-size:12px;color:var(--muted);margin-top:3px}

/* section label */
.sl{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;
    color:var(--muted);display:flex;align-items:center;gap:10px;margin-bottom:14px;margin-top:4px}
.sl::after{content:'';flex:1;height:1px;background:var(--border)}

/* stat cards */
.sr{display:grid;gap:14px;margin-bottom:20px}
.c4{grid-template-columns:repeat(4,1fr)}
.c3{grid-template-columns:repeat(3,1fr)}
.c2{grid-template-columns:repeat(2,1fr)}
.sc{background:var(--white);border:1px solid var(--border);border-radius:8px;
    padding:15px 17px;position:relative;overflow:hidden;
    box-shadow:0 1px 3px rgba(0,0,0,.04)}
/* coloured bottom bar on each stat card */
.sc::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--ac)}
.sc-l{font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;
      color:var(--muted);margin-bottom:5px}
.sc-v{font-size:26px;font-weight:700;color:var(--c1);line-height:1}
.sc-s{font-size:11px;color:var(--muted);margin-top:4px}
.sc-i{position:absolute;top:13px;right:13px;font-size:22px;opacity:.11;color:var(--c1)}

/* chart cards */
.cg{display:grid;gap:14px;margin-bottom:20px}
.cg2{grid-template-columns:1fr 1fr}
.cg1{grid-template-columns:1fr}
.cc{background:var(--white);border:1px solid var(--border);border-radius:8px;
    padding:17px 20px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.cc h3{font-size:13.5px;font-weight:700;color:var(--c1);margin-bottom:14px;
       display:flex;align-items:center;gap:8px}
.cc h3 i{font-size:12px;color:var(--ac)}

/* week mini-grid inside chart card */
.wg{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.wc{background:var(--bg);border:1px solid var(--border);border-radius:7px;
    padding:12px;text-align:center}
.wc-l{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
      color:var(--muted);margin-bottom:3px}
.wc-v{font-size:22px;font-weight:700;color:var(--ac);line-height:1}
.wc-s{font-size:10px;color:var(--muted);margin-top:3px}

/* progress bars */
.pr{display:flex;align-items:center;gap:10px;margin-bottom:9px}
.pr-l{font-size:12px;color:var(--muted);min-width:120px}
.pr-bg{flex:1;background:#ecf2f6;border-radius:5px;height:9px;overflow:hidden}
.pr-f{height:9px;border-radius:5px;background:var(--ac)}
.pr-v{font-size:12px;font-weight:700;color:var(--c1);min-width:80px;text-align:right}

/* ─── TABLE CARD ─────────────────────────────────────────────
   Table header is dark navy (#072D44) with white text
   exactly like your Manage Patients screenshot.
─────────────────────────────────────────────────────────── */
.tc{background:var(--white);border:1px solid var(--border);
    border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);
    margin-bottom:20px}
.tc-top{padding:13px 18px;display:flex;align-items:center;
        justify-content:space-between;border-bottom:1px solid var(--border)}
.tc-top h3{font-size:14px;font-weight:700;color:var(--c1);
           display:flex;align-items:center;gap:8px}
.tc-top h3 i{color:var(--ac);font-size:13px}
.rec{background:var(--bg);border:1px solid var(--border);border-radius:20px;
     padding:3px 12px;font-size:11px;color:var(--muted);font-weight:600}
.tw{overflow-x:auto}

/* THE TABLE – dark navy header exactly like your screenshots */
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--c1)}                       /* ← #072D44 dark header */
th{text-align:left;font-size:11.5px;font-weight:600;
   color:var(--white);padding:11px 14px;
   white-space:nowrap;letter-spacing:.3px}
td{padding:10px 14px;border-bottom:1px solid #edf2f6;
   color:var(--text);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f6fafb}
.tn{font-weight:700;color:var(--c1)}
.tm{font-family:'Courier New',monospace;font-size:11px;color:var(--muted)}
.ts{font-size:12px;color:var(--muted)}

/* status pills  */
.p{display:inline-flex;align-items:center;justify-content:center;
   font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;
   text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
.p-done  {background:#d5f0e3;color:#145a32}
.p-sched {background:#fdedc1;color:#7d5900}
.p-cancel{background:#fce4e1;color:#922b21}
.p-blue  {background:#d6eaf8;color:#1b4f72}
.p-gray  {background:#edf2f6;color:var(--muted)}
.p-green {background:#d5f0e3;color:#145a32}
.p-red   {background:#fce4e1;color:#922b21}

/* stars */
.stars{color:#b07d00;letter-spacing:1px}

/* ── PRINT ── */
@media print{
  .sb,.fb,.fb-print,.back{display:none!important}
  .main{margin-left:0}
  .sc,.cc,.tc{break-inside:avoid;box-shadow:none}
  .cg2,.c4{grid-template-columns:1fr 1fr}
}
@media(max-width:1000px){.cg2,.c4{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.cg2,.c4,.c3,.c2,.wg{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>

<!-- ════ SIDEBAR ════ -->

<?php include 'Admin_sidebar.php';?>


<!-- ════ MAIN ════ -->
<div class="main">

  <!-- topbar -->
  <div class="topbar">
    <div class="topbar-l">
      <div class="topbar-title">Welcome back</div>
      <a href="report.php" class="back"><i class="fas fa-arrow-left"></i> Back to Reports</a>
    </div>
    <div class="admin-wrap">
      <div style="text-align:right">
        <div style="font-size:14px;font-weight:700;color:var(--c2)">Admin</div>
        <div style="font-size:11px;color:var(--muted)"><?=date('F d, Y')?></div>
      </div>
      <div class="admin-av">AD</div>
    </div>
  </div>

  <!-- filter bar -->
  <div class="fb">
    <form method="GET" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;width:100%">
      <input type="hidden" name="type" value="<?=htmlspecialchars($type)?>">
      <label>Month</label>
      <select name="month">
        <?php for($m=1;$m<=12;$m++): ?>
        <option value="<?=$m?>" <?=$m==$mo?'selected':''?>><?=$MN[$m]?></option>
        <?php endfor; ?>
      </select>
      <label>Year</label>
      <select name="year">
        <?php $yrs=array_column($years,'y'); if(!in_array($yr,$yrs))$yrs[]=$yr; rsort($yrs);
        foreach($yrs as $y): ?><option value="<?=$y?>" <?=$y==$yr?'selected':''?>><?=$y?></option><?php endforeach; ?>
      </select>
      <label>Week</label>
      <select name="week">
        <option value="0" <?=$wk==0?'selected':''?>>Full Month</option>
        <?php for($w=1;$w<=4;$w++): ?><option value="<?=$w?>" <?=$wk==$w?'selected':''?>>Week <?=$w?></option><?php endfor; ?>
      </select>
      <button type="submit" class="fb-apply"><i class="fas fa-filter" style="margin-right:5px"></i>Apply</button>
      <button type="button" class="fb-print" onclick="window.print()"><i class="fas fa-print" style="margin-right:5px"></i>Print</button>
      <span class="fb-period"><i class="fas fa-calendar-days" style="margin-right:5px;color:var(--ac)"></i><?=htmlspecialchars($period)?> &nbsp;(<?=$df?> → <?=$dt?>)</span>
    </form>
  </div>

  <div class="body">

    <!-- page header -->
    <div class="ph">
      <div class="ph-ico" style="background:color-mix(in srgb,var(--ac) 12%,#fff)">
        <i class="fas <?=$M['icon']?>"></i>
      </div>
      <div>
        <div class="ph-title"><?=htmlspecialchars($M['title'])?></div>
        <div class="ph-sub">Period: <?=htmlspecialchars($period)?> &nbsp;·&nbsp; <?=$df?> to <?=$dt?></div>
      </div>
    </div>

<?php /* ══════════ APPOINTMENT ══════════ */ if($type==='appointment'): ?>

    <!-- STAT CARDS -->
    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Total Appointments</div><div class="sc-v"><?=$s['total']?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-calendar-check"></i></div></div>
      <div class="sc" style="--ac:#198754"><div class="sc-l">Completed</div><div class="sc-v" style="color:#198754"><?=$s['done']?></div><div class="sc-s">Done</div><div class="sc-i"><i class="fas fa-circle-check"></i></div></div>
      <div class="sc" style="--ac:#e07b19"><div class="sc-l">Scheduled</div><div class="sc-v" style="color:#e07b19"><?=$s['sched']?></div><div class="sc-s">Upcoming</div><div class="sc-i"><i class="fas fa-clock"></i></div></div>
      <div class="sc" style="--ac:#c0392b"><div class="sc-l">Cancelled</div><div class="sc-v" style="color:#c0392b"><?=$s['cancel']?></div><div class="sc-s">Cancelled</div><div class="sc-i"><i class="fas fa-xmark"></i></div></div>
    </div>

    <!-- CHARTS -->
    <div class="sl">Charts &amp; Trends</div>
    <div class="cg cg2">
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Monthly Trend (Last 6 Months)</h3><canvas id="c1" height="200"></canvas></div>
      <div class="cc"><h3><i class="fas fa-chart-column"></i>Weekly Breakdown — <?=$MN[$mo]?></h3>
        <?php if(count($weekly)>0): ?>
        <div class="wg">
          <?php foreach($weekly as $w): ?>
          <div class="wc"><div class="wc-l">Week <?=$w['wn']?></div><div class="wc-v"><?=$w['cnt']?></div><div class="wc-s"><?=$w['dn']?> done · <?=$w['sc']?> sch</div></div>
          <?php endforeach; ?>
        </div>
        <?php else: ?><p style="color:var(--muted);font-size:13px">No data for this month.</p><?php endif; ?>
      </div>
    </div>

    <!-- TABLE -->
    <div class="sl">Appointment Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-table-list"></i>All Appointments</h3><span class="rec"><?=count($rows)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Specialisation</th><th>Date</th><th>Time</th><th>Status</th><th>Booked On</th></tr></thead>
        <tbody>
        <?php if(!count($rows)): ?><tr><td colspan="8" style="text-align:center;padding:28px;color:var(--muted)">No records found for this period.</td></tr>
        <?php else: foreach($rows as $r): ?>
        <tr>
          <td class="tn"><?=$r['APPOINTMENT_ID']?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td>Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td><span class="p p-blue"><?=htmlspecialchars($r['sp'])?></span></td>
          <td><?=htmlspecialchars($r['APPOINTMENT_DATE'])?></td>
          <td><?=substr($r['APPOINTMENT_TIME'],0,5)?></td>
          <td><span class="p <?=$r['STATUS']==='COMPLETED'?'p-done':($r['STATUS']==='SCHEDULED'?'p-sched':'p-cancel')?>"><?=$r['STATUS']?></span></td>
          <td class="ts"><?=substr($r['CREATED_AT'],0,16)?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

<?php /* ══════════ PAYMENT ══════════ */ elseif($type==='payment'): ?>

    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Total Revenue</div><div class="sc-v" style="font-size:20px">₹<?=number_format($s['rev'],2)?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-indian-rupee-sign"></i></div></div>
      <div class="sc" style="--ac:#198754"><div class="sc-l">Completed</div><div class="sc-v" style="color:#198754"><?=$s['done']?></div><div class="sc-s">Settled</div><div class="sc-i"><i class="fas fa-circle-check"></i></div></div>
      <div class="sc" style="--ac:#e07b19"><div class="sc-l">Pending</div><div class="sc-v" style="color:#e07b19"><?=$s['pend']?></div><div class="sc-s">Awaiting</div><div class="sc-i"><i class="fas fa-hourglass-half"></i></div></div>
      <div class="sc" style="--ac:#c0392b"><div class="sc-l">Failed</div><div class="sc-v" style="color:#c0392b"><?=$s['fail']?></div><div class="sc-s">Failed</div><div class="sc-i"><i class="fas fa-triangle-exclamation"></i></div></div>
    </div>

    <div class="sl">Charts</div>
    <div class="cg cg2">
      <div class="cc"><h3><i class="fas fa-chart-line"></i>Monthly Revenue Trend</h3><canvas id="c1" height="200"></canvas></div>
      <div class="cc"><h3><i class="fas fa-chart-pie"></i>Revenue by Payment Mode</h3><canvas id="c2" height="200"></canvas></div>
    </div>
    <?php if(count($bymode)>0): $mx=max(array_column($bymode,'tot'))?:1; ?>
    <div class="cc" style="margin-bottom:20px"><h3><i class="fas fa-bars"></i>Mode-wise Breakdown</h3>
      <?php foreach($bymode as $b): $pct=round($b['tot']/$mx*100); ?>
      <div class="pr"><div class="pr-l"><?=htmlspecialchars($b['PAYMENT_MODE'])?></div><div class="pr-bg"><div class="pr-f" style="width:<?=$pct?>%"></div></div><div class="pr-v">₹<?=number_format($b['tot'],0)?> (<?=$b['cnt']?>)</div></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="sl">Payment Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-table-list"></i>All Payments</h3><span class="rec"><?=count($rows)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Transaction ID</th><th>Patient</th><th>Doctor</th><th>Amount</th><th>Mode</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php if(!count($rows)): ?><tr><td colspan="8" style="text-align:center;padding:28px;color:var(--muted)">No records found.</td></tr>
        <?php else: foreach($rows as $r): ?>
        <tr>
          <td class="tn"><?=$r['PAYMENT_ID']?></td>
          <td class="tm"><?=htmlspecialchars($r['TRANSACTION_ID'])?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td>Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td class="tn">₹<?=number_format($r['AMOUNT'],2)?></td>
          <td><span class="p p-blue"><?=htmlspecialchars($r['PAYMENT_MODE'])?></span></td>
          <td><span class="p <?=$r['STATUS']==='COMPLETED'?'p-done':($r['STATUS']==='PENDING'?'p-sched':'p-cancel')?>"><?=$r['STATUS']?></span></td>
          <td><?=htmlspecialchars($r['PAYMENT_DATE'])?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

<?php /* ══════════ PRESCRIPTION ══════════ */ elseif($type==='prescription'): ?>

    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Prescriptions Issued</div><div class="sc-v"><?=$s['total']?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-file-prescription"></i></div></div>
      <div class="sc"><div class="sc-l">Doctors Involved</div><div class="sc-v"><?=$s['docs']?></div><div class="sc-s">Active prescribers</div><div class="sc-i"><i class="fas fa-user-doctor"></i></div></div>
      <div class="sc"><div class="sc-l">Medicines Prescribed</div><div class="sc-v"><?=$s['meds']?></div><div class="sc-s">Total entries</div><div class="sc-i"><i class="fas fa-pills"></i></div></div>
      <div class="sc" style="--ac:#c0392b"><div class="sc-l">Diabetic Cases</div><div class="sc-v" style="color:#c0392b"><?=$s['diab']?></div><div class="sc-s">Type 1/2 or Pre-diabetic</div><div class="sc-i"><i class="fas fa-heart-pulse"></i></div></div>
    </div>

    <div class="sl">Charts</div>
    <div class="cg cg2">
      <div class="cc"><h3><i class="fas fa-chart-line"></i>Monthly Prescription Trend</h3><canvas id="c1" height="200"></canvas></div>
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Prescriptions per Doctor</h3><canvas id="c2" height="200"></canvas></div>
    </div>

    <div class="sl">Prescription Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-table-list"></i>All Prescriptions</h3><span class="rec"><?=count($rows)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Issue Date</th><th>Patient</th><th>Doctor</th><th>Symptoms</th><th>Diagnosis</th><th>Diabetes</th><th>BP</th><th>Notes</th></tr></thead>
        <tbody>
        <?php if(!count($rows)): ?><tr><td colspan="9" style="text-align:center;padding:28px;color:var(--muted)">No records found.</td></tr>
        <?php else: foreach($rows as $r): ?>
        <tr>
          <td class="tn"><?=$r['PRESCRIPTION_ID']?></td>
          <td><?=htmlspecialchars($r['ISSUE_DATE'])?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td>Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td class="ts" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?=htmlspecialchars($r['SYMPTOMS'])?>"><?=htmlspecialchars($r['SYMPTOMS'])?></td>
          <td class="ts" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?=htmlspecialchars($r['DIAGNOSIS'])?>"><?=htmlspecialchars($r['DIAGNOSIS'])?></td>
          <td><span class="p <?=$r['DIABETES']==='NO'?'p-done':'p-cancel'?>"><?=htmlspecialchars($r['DIABETES'])?></span></td>
          <td class="tn"><?=htmlspecialchars($r['BLOOD_PRESSURE'])?></td>
          <td class="ts" style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?=htmlspecialchars($r['ADDITIONAL_NOTES'])?>"><?=htmlspecialchars($r['ADDITIONAL_NOTES'])?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

<?php /* ══════════ REMINDER ══════════ */ elseif($type==='reminder'): ?>

    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Appointment Reminders</div><div class="sc-v"><?=$s['ar']?></div><div class="sc-s">Sent this period</div><div class="sc-i"><i class="fas fa-envelope"></i></div></div>
      <div class="sc"><div class="sc-l">Medicine Reminders</div><div class="sc-v"><?=$s['mr']?></div><div class="sc-s">Active this period</div><div class="sc-i"><i class="fas fa-pills"></i></div></div>
      <div class="sc"><div class="sc-l">Patients (Med Rem)</div><div class="sc-v"><?=$s['pts']?></div><div class="sc-s">Unique patients</div><div class="sc-i"><i class="fas fa-hospital-user"></i></div></div>
      <div class="sc"><div class="sc-l">Set by Receptionist</div><div class="sc-v"><?=$s['rec']?></div><div class="sc-s">Receptionist-created</div><div class="sc-i"><i class="fas fa-headset"></i></div></div>
    </div>

    <div class="sl">Weekly Chart</div>
    <div class="cg cg1">
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Appointment Reminders — Weekly (<?=$MN[$mo]?>)</h3>
        <?php if(count($wkrem)>0): ?><canvas id="c1" height="110"></canvas>
        <?php else: ?><p style="color:var(--muted);font-size:13px">No reminder data for this month.</p><?php endif; ?>
      </div>
    </div>

    <div class="sl">Appointment Reminder Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-bell"></i>Appointment Reminders</h3><span class="rec"><?=count($rows_a)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Appointment Date</th><th>Reminder Time</th><th>Remarks</th></tr></thead>
        <tbody>
        <?php if(!count($rows_a)): ?><tr><td colspan="6" style="text-align:center;padding:28px;color:var(--muted)">No records found.</td></tr>
        <?php else: foreach($rows_a as $r): ?>
        <tr>
          <td class="tn"><?=$r['APPOINTMENT_REMINDER_ID']?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td>Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td><?=htmlspecialchars($r['APPOINTMENT_DATE'])?></td>
          <td><?=substr($r['REMINDER_TIME'],0,5)?></td>
          <td class="ts"><?=htmlspecialchars($r['REMARKS'])?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

    <div class="sl">Medicine Reminder Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-pills"></i>Medicine Reminders</h3><span class="rec"><?=count($rows_m)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Medicine</th><th>Patient</th><th>Created By</th><th>Time</th><th>Start Date</th><th>End Date</th><th>Remarks</th></tr></thead>
        <tbody>
        <?php if(!count($rows_m)): ?><tr><td colspan="8" style="text-align:center;padding:28px;color:var(--muted)">No records found.</td></tr>
        <?php else: foreach($rows_m as $r): ?>
        <tr>
          <td class="tn"><?=$r['MEDICINE_REMINDER_ID']?></td>
          <td><?=htmlspecialchars($r['med'])?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td><span class="p p-blue"><?=htmlspecialchars($r['CREATOR_ROLE'])?></span></td>
          <td><?=substr($r['REMINDER_TIME'],0,5)?></td>
          <td><?=htmlspecialchars($r['START_DATE'])?></td>
          <td><?=htmlspecialchars($r['END_DATE'])?></td>
          <td class="ts"><?=htmlspecialchars($r['REMARKS'])?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

<?php /* ══════════ FEEDBACK ══════════ */ elseif($type==='feedback'): ?>

    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Total Feedback</div><div class="sc-v"><?=$s['total']?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-comments"></i></div></div>
      <div class="sc"><div class="sc-l">Average Rating</div><div class="sc-v"><?=$s['avg']?:'-'?></div><div class="sc-s"><span class="stars"><?=str_repeat('★',round($s['avg']??0))?></span></div><div class="sc-i"><i class="fas fa-star"></i></div></div>
      <div class="sc" style="--ac:#198754"><div class="sc-l">5-Star Reviews</div><div class="sc-v" style="color:#198754"><?=$s['five']?></div><div class="sc-s">Excellent</div><div class="sc-i"><i class="fas fa-thumbs-up"></i></div></div>
      <div class="sc" style="--ac:#c0392b"><div class="sc-l">1-Star Reviews</div><div class="sc-v" style="color:#c0392b"><?=$s['one']?></div><div class="sc-s">Needs work</div><div class="sc-i"><i class="fas fa-thumbs-down"></i></div></div>
    </div>

    <div class="sl">Charts</div>
    <div class="cg cg2">
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Rating Distribution</h3>
        <?php $tot=$s['total']?:1; $sc=[5=>'#198754',4=>'#5790AB',3=>'#b07d00',2=>'#e07b19',1=>'#c0392b'];
        $fm=[]; foreach($dist as $d) $fm[$d['RATING']]=$d['cnt'];
        foreach([5,4,3,2,1] as $st): $c=$fm[$st]??0; $pct=round($c/$tot*100); ?>
        <div class="pr">
          <div class="pr-l"><span class="stars" style="font-size:12px"><?=str_repeat('★',$st)?></span></div>
          <div class="pr-bg"><div class="pr-f" style="width:<?=$pct?>%;background:<?=$sc[$st]?>"></div></div>
          <div class="pr-v"><?=$c?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Avg Rating per Doctor</h3><canvas id="c2" height="220"></canvas></div>
    </div>

    <div class="sl">Feedback Records</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-table-list"></i>All Feedback</h3><span class="rec"><?=count($rows)?> records</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Specialisation</th><th>Rating</th><th>Comments</th><th>Appt Date</th></tr></thead>
        <tbody>
        <?php if(!count($rows)): ?><tr><td colspan="7" style="text-align:center;padding:28px;color:var(--muted)">No records found.</td></tr>
        <?php else: foreach($rows as $r): ?>
        <tr>
          <td class="tn"><?=$r['FEEDBACK_ID']?></td>
          <td><?=htmlspecialchars($r['pat'])?></td>
          <td>Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td><span class="p p-blue"><?=htmlspecialchars($r['sp'])?></span></td>
          <td><span class="stars"><?=str_repeat('★',$r['RATING'])?></span> <span class="ts"><?=$r['RATING']?>/5</span></td>
          <td class="ts" style="max-width:200px"><?=htmlspecialchars($r['COMMENTS']??'—')?></td>
          <td><?=htmlspecialchars($r['APPOINTMENT_DATE'])?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table></div>
    </div>

<?php /* ══════════ DOCTOR ══════════ */ elseif($type==='doctor'): ?>

    <div class="sl">Summary Statistics</div>
    <div class="sr c4">
      <div class="sc"><div class="sc-l">Total Doctors</div><div class="sc-v"><?=$s['docs']?></div><div class="sc-s">Registered</div><div class="sc-i"><i class="fas fa-user-doctor"></i></div></div>
      <div class="sc"><div class="sc-l">Specialisations</div><div class="sc-v"><?=$s['specs']?></div><div class="sc-s">Departments</div><div class="sc-i"><i class="fas fa-stethoscope"></i></div></div>
      <div class="sc"><div class="sc-l">Total Appointments</div><div class="sc-v"><?=$s['appts']?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-calendar-check"></i></div></div>
      <div class="sc"><div class="sc-l">Prescriptions Issued</div><div class="sc-v"><?=$s['presc']?></div><div class="sc-s"><?=htmlspecialchars($period)?></div><div class="sc-i"><i class="fas fa-file-prescription"></i></div></div>
    </div>

    <div class="sl">Charts</div>
    <div class="cg cg1">
      <div class="cc"><h3><i class="fas fa-chart-bar"></i>Appointments by Specialisation</h3><canvas id="c1" height="110"></canvas></div>
    </div>

    <div class="sl">Doctor Performance Summary</div>
    <div class="tc">
      <div class="tc-top"><h3><i class="fas fa-table-list"></i>All Doctors</h3><span class="rec"><?=count($rows)?> doctors</span></div>
      <div class="tw"><table>
        <thead><tr><th>ID</th><th>Doctor</th><th>Specialisation</th><th>Education</th><th>Status</th><th>Total Appts</th><th>Completed</th><th>Scheduled</th><th>Cancelled</th><th>Prescriptions</th><th>Avg Rating</th></tr></thead>
        <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
          <td class="tn"><?=$r['DOCTOR_ID']?></td>
          <td style="font-weight:600">Dr. <?=htmlspecialchars($r['doc'])?></td>
          <td><span class="p p-blue"><?=htmlspecialchars($r['sp'])?></span></td>
          <td class="ts" style="font-size:12px"><?=htmlspecialchars($r['EDUCATION'])?></td>
          <td><span class="p <?=$r['STATUS']==='approved'?'p-done':'p-sched'?>"><?=htmlspecialchars($r['STATUS'])?></span></td>
          <td class="tn"><?=$r['ta']?></td>
          <td><span class="p p-done"><?=$r['dn']?></span></td>
          <td><span class="p p-sched"><?=$r['sc']?></span></td>
          <td><span class="p <?=$r['cx']>0?'p-cancel':'p-gray'?>"><?=$r['cx']?></span></td>
          <td class="tn"><?=$r['pr']?></td>
          <td><?php if($r['ar']): ?><span class="stars" style="font-size:12px"><?=str_repeat('★',round($r['ar']))?></span> <span class="ts"><?=$r['ar']?></span><?php else: ?>—<?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>

<?php endif; ?>

  </div><!-- /body -->
</div><!-- /main -->

<!-- ════ CHARTS ════ -->
<script>
Chart.defaults.font.family="'Segoe UI',Tahoma,Geneva,Verdana,sans-serif";
Chart.defaults.color='#5b7f8a';
Chart.defaults.plugins.legend.labels.boxWidth=11;
const AC='<?=$M['ac']?>', AL=AC+'25';

<?php if($type==='appointment'): ?>
if(document.getElementById('c1'))new Chart(document.getElementById('c1'),{type:'bar',data:{labels:<?=$c1l?>,datasets:[{label:'Appointments',data:<?=$c1d?>,backgroundColor:AL,borderColor:AC,borderWidth:2,borderRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#edf2f6'}},x:{grid:{display:false}}}}});

<?php elseif($type==='payment'): ?>
if(document.getElementById('c1'))new Chart(document.getElementById('c1'),{type:'line',data:{labels:<?=$c1l?>,datasets:[{label:'Revenue ₹',data:<?=$c1d?>,borderColor:AC,backgroundColor:AL,borderWidth:2.5,pointBackgroundColor:AC,pointRadius:5,tension:.35,fill:true}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#edf2f6'}},x:{grid:{display:false}}}}});
if(document.getElementById('c2'))new Chart(document.getElementById('c2'),{type:'doughnut',data:{labels:<?=$c2l?>,datasets:[{data:<?=$c2d?>,backgroundColor:['#064469','#198754','#5790AB','#b07d00'],borderColor:'#fff',borderWidth:3,hoverOffset:5}]},options:{responsive:true,cutout:'60%',plugins:{legend:{position:'bottom'}}}});

<?php elseif($type==='prescription'): ?>
if(document.getElementById('c1'))new Chart(document.getElementById('c1'),{type:'line',data:{labels:<?=$c1l?>,datasets:[{label:'Prescriptions',data:<?=$c1d?>,borderColor:AC,backgroundColor:AL,borderWidth:2.5,pointBackgroundColor:AC,pointRadius:5,tension:.35,fill:true}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#edf2f6'}},x:{grid:{display:false}}}}});
if(document.getElementById('c2'))new Chart(document.getElementById('c2'),{type:'bar',data:{labels:<?=$c2l?>,datasets:[{label:'Prescriptions',data:<?=$c2d?>,backgroundColor:AL,borderColor:AC,borderWidth:2,borderRadius:5}]},options:{responsive:true,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{stepSize:1}},y:{grid:{display:false}}}}});

<?php elseif($type==='reminder'&&count($wkrem)>0): ?>
if(document.getElementById('c1'))new Chart(document.getElementById('c1'),{type:'bar',data:{labels:<?=$c1l?>,datasets:[{label:'Reminders',data:<?=$c1d?>,backgroundColor:AL,borderColor:AC,borderWidth:2,borderRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#edf2f6'}},x:{grid:{display:false}}}}});

<?php elseif($type==='feedback'): ?>
if(document.getElementById('c2'))new Chart(document.getElementById('c2'),{type:'bar',data:{labels:<?=$c2l?>,datasets:[{label:'Avg Rating',data:<?=$c2d?>,backgroundColor:AL,borderColor:AC,borderWidth:2,borderRadius:5}]},options:{responsive:true,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{min:0,max:5,ticks:{stepSize:1}},y:{grid:{display:false}}}}});

<?php elseif($type==='doctor'): ?>
if(document.getElementById('c1'))new Chart(document.getElementById('c1'),{type:'bar',data:{labels:<?=$c1l?>,datasets:[{label:'Appointments',data:<?=$c1d?>,backgroundColor:AL,borderColor:AC,borderWidth:2,borderRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'#edf2f6'}},x:{grid:{display:false}}}}});
<?php endif; ?>
</script>
</body>
</html>
