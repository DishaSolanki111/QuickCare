<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
header("Location: login_for_all.php");
exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Get selected specialization from query parameters
 $selected_specialization = isset($_POST['specialization']) ? mysqli_real_escape_string($conn, $_POST['specialization']) : (isset($_GET['specialization']) ? mysqli_real_escape_string($conn, $_GET['specialization']) : '');

// Fetch specializations for filter
 $specializations_query = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");

// Build doctors query
 $doctors_query = "
SELECT d.*, s.SPECIALISATION_NAME
FROM doctor_tbl d
JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE 1=1
";

if (!empty($selected_specialization)) {
 $doctors_query .= " AND d.SPECIALISATION_ID = '$selected_specialization'";
}

 $doctors_query .= " ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME";

 $doctors_result = mysqli_query($conn, $doctors_query);

// Get doctor ID from URL if viewing a specific doctor
 $doctor_id = isset($_POST['doctor']) ? mysqli_real_escape_string($conn, $_POST['doctor']) : (isset($_GET['doctor']) ? mysqli_real_escape_string($conn, $_GET['doctor']) : '');

// If doctor ID is provided, get doctor details
 $doctor_details = null;
if (!empty($doctor_id)) {
 $doctor_details_query = mysqli_query($conn, "
SELECT d.*, s.SPECIALISATION_NAME
FROM doctor_tbl d
JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE d.DOCTOR_ID = '$doctor_id'
");

if (mysqli_num_rows($doctor_details_query) > 0) {
 $doctor_details = mysqli_fetch_assoc($doctor_details_query);

// Get doctor's schedule
 $schedule_query = mysqli_query($conn, "
SELECT ds.*, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME
FROM doctor_schedule_tbl ds
JOIN doctor_tbl d ON ds.DOCTOR_ID = d.DOCTOR_ID
JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE ds.DOCTOR_ID = '$doctor_id'
ORDER BY ds.AVAILABLE_DAY
");
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Profiles - QuickCare</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
--primary-color: #1a3a5f;
--secondary-color: #3498db;
--accent-color: #2ecc71;
--light-color: #f8f9fa;
--dark-color: #343a40;
--danger-color: #e74c3c;
--warning-color: #f39c12;
--info-color: #17a2b8;
--dark-blue: #072D44;
--mid-blue: #064469;
--soft-blue: #5790AB;
--light-blue: #9CCDD8;
--gray-blue: #D0D7E1;
--white: #ffffff;
--card-bg: #F6F9FB;
--sidebar-width: 250px;
}

* {
margin: 0;
padding: 0;
box-sizing: border-box;
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

        body {
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Container for the entire layout */
        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            height: 100vh;
            overflow-y: auto;
        }
        /* Custom scrollbar for main content */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #5790AB;
            border-radius: 4px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #064469;
        }


/* Page Layout */
.page-wrapper {
display: flex;
min-height: 100vh;
width: 100%;
}

.header {
display: flex;
justify-content: space-between;
align-items: center;
padding: 15px 20px;
background-color: white;
border-radius: 8px;
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
margin-bottom: 25px;
}

.welcome-msg {
font-size: 24px;
font-weight: 600;
color: var(--primary-color);
}

.user-actions {
display: flex;
align-items: center;
}

.user-avatar {
width: 40px;
height: 40px;
border-radius: 50%;
background-color: var(--secondary-color);
color: white;
display: flex;
align-items: center;
justify-content: center;
margin-right: 10px;
font-weight: bold;
}

.filter-section {
background-color: white;
border-radius: 10px;
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
padding: 20px;
margin-bottom: 25px;
}

.filter-form {
display: flex;
gap: 15px;
flex-wrap: wrap;
}

.form-group {
flex: 1;
min-width: 200px;
}

.form-group label {
display: block;
margin-bottom: 8px;
font-weight: 600;
color: var(--dark-color);
}

.form-control {
width: 100%;
padding: 10px 15px;
border: 1px solid #ddd;
border-radius: 5px;
font-size: 16px;
transition: border-color 0.3s;
}

.form-control:focus {
border-color: var(--secondary-color);
outline: none;
box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.btn {
padding: 10px 20px;
border: none;
border-radius: 5px;
cursor: pointer;
font-weight: 600;
transition: all 0.3s ease;
display: inline-flex;
align-items: center;
justify-content: center;
}

.btn-primary {
background-color: var(--secondary-color);
color: white;
}

.btn-primary:hover {
background-color: #2980b9;
}

.btn-success {
background-color: var(--accent-color);
color: white;
}

.btn-success:hover {
background-color: #27ae60;
}

.doctors-container {
max-height: 70vh;

padding-right: 10px;
}

.doctor-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
gap: 20px;
}

.doctor-card {
background-color: white;
border-radius: 10px;
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
overflow: hidden;
transition: transform 0.3s, box-shadow 0.3s;
}

.doctor-card:hover {
transform: translateY(-5px);
box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.doctor-image {
height: 200px;
background-color: #f8f9fa;
display: flex;
align-items: center;
justify-content: center;
overflow: hidden;
}

.doctor-image img {
width: 100%;
height: 100%;
object-fit: contain;
}

.doctor-avatar-placeholder {
width: 100px;
height: 100px;
border-radius: 50%;
background-color: var(--secondary-color);
color: white;
display: flex;
align-items: center;
justify-content: center;
font-size: 36px;
font-weight: bold;
}

.doctor-info {
padding: 20px;
}

.doctor-name {
font-size: 20px;
font-weight: 600;
color: var(--primary-color);
margin-bottom: 5px;
text-align: center;
}

.doctor-specialization {
color: var(--secondary-color);
margin-bottom: 15px;
text-align: center;
}

.doctor-details {
margin-bottom: 15px;
}

.doctor-detail {
display: flex;
align-items: center;
margin-bottom: 8px;
color: #666;
}

.doctor-detail i {
margin-right: 10px;
color: var(--secondary-color);
width: 20px;
text-align: center;
}

.doctor-actions {
display: flex;
gap: 10px;
justify-content: center;
}

.empty-state {
text-align: center;
padding: 40px;
color: #777;
}

.empty-state i {
font-size: 48px;
margin-bottom: 15px;
color: #ddd;
}

/* Completely redesigned Doctor Detail View */
.doctor-detail-container {
width: 100%;
padding: 0;
margin: 0;
}

.doctor-detail-view {
display: flex;
flex-direction: column;
width: 100%;
gap: 25px;
}

.doctor-detail-card {
background-color: white;
border-radius: 10px;
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
padding: 25px;
width: 100%;
box-sizing: border-box;
}

.doctor-detail-header {
display: flex;
align-items: center;
margin-bottom: 25px;
padding-bottom: 15px;
border-bottom: 1px solid #eee;
flex-wrap: wrap;
}

.doctor-detail-avatar {
width: 120px;
height: 120px;
border-radius: 50%;
background-color: var(--secondary-color);
color: white;
display: flex;
align-items: center;
justify-content: center;
font-size: 42px;
font-weight: bold;
margin-right: 20px;
overflow: hidden;
flex-shrink: 0;
}

.doctor-detail-avatar img {
width: 100%;
height: 100%;
object-fit: cover;
}

.doctor-detail-title {
flex: 1;
}

.doctor-detail-title h2 {
font-size: 28px;
color: var(--primary-color);
margin-bottom: 5px;
}

.doctor-detail-title p {
color: #777;
font-size: 16px;
}

.info-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
gap: 20px;
}

.info-item {
margin-bottom: 15px;
}

.info-label {
font-weight: 600;
color: var(--dark-color);
margin-bottom: 5px;
display: block;
}

.info-value {
color: #555;
}

.schedule-container {
margin-top: 20px;
}

.schedule-header {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 15px;
padding-bottom: 10px;
border-bottom: 1px solid #eee;
}

.schedule-header h3 {
color: var(--primary-color);
}

.schedule-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
gap: 15px;
}

.schedule-card {
background-color: #f8f9fa;
border-radius: 8px;
padding: 15px;
border: 1px solid #e9ecef;
}

.schedule-item {
display: flex;
align-items: center;
color: #666;
margin-bottom: 10px;
}

.schedule-item:last-child {
margin-bottom: 0;
}

.schedule-item i {
margin-right: 10px;
color: var(--secondary-color);
font-size: 18px;
width: 20px;
text-align: center;
}

.day-badge {
display: inline-block;
padding: 5px 10px;
border-radius: 20px;
font-size: 14px;
font-weight: 600;
background-color: rgba(52, 152, 219, 0.1);
color: var(--secondary-color);
}

.btn-group {
display: flex;
gap: 10px;
flex-wrap: wrap;
margin-top: 15px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
.main-content {
padding-left: calc(70px + 20px);
}

.filter-form {
flex-direction: column;
}

.form-group {
width: 100%;
}

.info-grid {
grid-template-columns: 1fr;
}

.schedule-grid {
grid-template-columns: 1fr;
}
}

@media (max-width: 768px) {
.main-content {
padding-left: 20px;
}

.doctor-grid {
grid-template-columns: 1fr;
}

.doctor-detail-header {
flex-direction: column;
text-align: center;
}

.doctor-detail-avatar {
margin-right: 0;
margin-bottom: 15px;
}
}
</style>
</head>
<body>
<div class="page-wrapper">
<!-- Import Patient Sidebar -->
<?php include 'patient_sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
<!-- Header -->
<div class="header">
<div class="welcome-msg">Doctor Profiles</div>
<div class="user-actions">
<div class="user-dropdown">
<div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
<span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
<i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
</div>
</div>
</div>

<?php if ($doctor_details): ?>
<!-- Doctor Detail View -->
<div class="doctor-detail-container">
<div class="doctor-detail-view">
<div class="doctor-detail-card">
<div class="doctor-detail-header">
<div class="doctor-detail-avatar">
<?php if (!empty($doctor_details['PROFILE_IMAGE'])): ?>
<img src="<?php echo htmlspecialchars($doctor['PROFILE_IMAGE']); ?>">
<?php else: ?>
<?php echo strtoupper(substr($doctor_details['FIRST_NAME'], 0, 1) . substr($doctor_details['LAST_NAME'], 0, 1)); ?>
<?php endif; ?>
</div>
<div class="doctor-detail-title">
<h2>Dr. <?php echo htmlspecialchars($doctor_details['FIRST_NAME'] . ' ' . $doctor_details['LAST_NAME']); ?></h2>
<p><?php echo htmlspecialchars($doctor_details['SPECIALISATION_NAME']); ?></p>
</div>
</div>

<div class="info-grid">
<div class="info-item">
<span class="info-label">Date of Birth</span>
<span class="info-value"><?php echo date('F d, Y', strtotime($doctor_details['DOB'])); ?></span>
</div>
<div class="info-item">
<span class="info-label">Gender</span>
<span class="info-value"><?php echo htmlspecialchars($doctor_details['GENDER']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Date of Joining</span>
<span class="info-value"><?php echo date('F d, Y', strtotime($doctor_details['DOJ'])); ?></span>
</div>
<div class="info-item">
<span class="info-label">Education</span>
<span class="info-value"><?php echo htmlspecialchars($doctor_details['EDUCATION']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Phone Number</span>
<span class="info-value"><?php echo htmlspecialchars($doctor_details['PHONE']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Email Address</span>
<span class="info-value"><?php echo htmlspecialchars($doctor_details['EMAIL']); ?></span>
</div>
</div>

<div class="btn-group">
<button class="btn btn-success" onclick="bookAppointment(<?php echo $doctor_details['DOCTOR_ID']; ?>)">
<i class="fas fa-calendar-plus"></i> Book Appointment
</button>
<a href="doctor_profiles.php" class="btn" style="background-color: #6c757d; color: white;">
<i class="fas fa-arrow-left"></i> Back to Doctors
</a>
</div>
</div>

<div class="doctor-detail-card">
<div class="schedule-container">
<div class="schedule-header">
<h3>Doctor's Schedule</h3>
</div>

<?php
if (mysqli_num_rows($schedule_query) > 0) {
echo '<div class="schedule-grid">';
while ($schedule = mysqli_fetch_assoc($schedule_query)) {
 $day_name = '';
switch($schedule['AVAILABLE_DAY']) {
case 'MON': $day_name = 'Monday'; break;
case 'TUE': $day_name = 'Tuesday'; break;
case 'WED': $day_name = 'Wednesday'; break;
case 'THUR': $day_name = 'Thursday'; break;
case 'FRI': $day_name = 'Friday'; break;
case 'SAT': $day_name = 'Saturday'; break;
case 'SUN': $day_name = 'Sunday'; break;
}
?>
<div class="schedule-card">
<div class="schedule-item">
<i class="far fa-calendar"></i>
<span class="day-badge"><?php echo $day_name; ?></span>
</div>

<div class="schedule-item">
<i class="far fa-clock"></i>
<span><?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?></span>
</div>

<div class="btn-group">
<button class="btn btn-primary" onclick="bookAppointment(<?php echo $schedule['DOCTOR_ID']; ?>, '<?php echo $schedule['AVAILABLE_DAY']; ?>')">
<i class="fas fa-calendar-plus"></i> Book
</button>
</div>
</div>
<?php
}
echo '</div>';
} else {
echo '<div class="empty-state">
<i class="far fa-calendar-times"></i>
<p>No schedule information available</p>
</div>';
}
?>
</div>
</div>
</div>
</div>
<?php else: ?>
<!-- Filter Section -->
<div class="filter-section">
<form method="POST" action="view_doctor_patient.php" class="filter-form">
<div class="form-group">
<label for="specialization">Filter by Specialization</label>
<select class="form-control" id="specialization" name="specialization">
<option value="">All Specializations</option>
<?php
if (mysqli_num_rows($specializations_query) > 0) {
while ($specialization = mysqli_fetch_assoc($specializations_query)) {
 $selected = ($selected_specialization == $specialization['SPECIALISATION_ID']) ? 'selected' : '';
echo '<option value="' . $specialization['SPECIALISATION_ID'] . '" ' . $selected . '>' .
htmlspecialchars($specialization['SPECIALISATION_NAME']) . '</option>';
}
// Reset the result pointer
mysqli_data_seek($specializations_query, 0);
}
?>
</select>
</div>

<div class="form-group" style="display: flex; align-items: flex-end;">
<button type="submit" class="btn btn-primary">
<i class="fas fa-filter"></i> Apply Filters
</button>
<a href="view_doctor_patient.php" class="btn" style="margin-left: 10px; background-color: #6c757d; color: white;">
<i class="fas fa-redo"></i> Reset
</a>
</div>
</form>
</div>

<!-- Doctors Grid with Scrollbar -->
<div class="doctors-container">
<div class="doctor-grid">
<?php
if (mysqli_num_rows($doctors_result) > 0) {
while ($doctor = mysqli_fetch_assoc($doctors_result)) {
?>
<div class="doctor-card">
<div class="doctor-image">
<?php if (!empty(trim($doctor['PROFILE_IMAGE']))): ?>
<img src="<?php echo htmlspecialchars($doctor['PROFILE_IMAGE']); ?>">
<?php else: ?>
<div class="doctor-avatar-placeholder">
<?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
</div>
<?php endif; ?>
</div>
<div class="doctor-info">
<h3 class="doctor-name">Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
<p class="doctor-specialization"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
<div class="doctor-details">
<div class="doctor-detail">
<i class="fas fa-graduation-cap"></i>
<span><?php echo htmlspecialchars($doctor['EDUCATION']); ?></span>
</div>
<div class="doctor-detail">
<i class="far fa-calendar"></i>
<span>Joined: <?php echo date('Y', strtotime($doctor['DOJ'])); ?></span>
</div>
</div>
<div class="doctor-actions">
<form method="POST" action="d_profile.php" style="display:inline">
<input type="hidden" name="id" value="<?php echo $doctor['DOCTOR_ID']; ?>">
<button type="submit" class="btn btn-primary"><i class="fas fa-user-md"></i> View Profile</button>
</form>
<form method="POST" action="book_appointment_date.php" style="display:inline">
<input type="hidden" name="doctor_id" value="<?php echo $doctor['DOCTOR_ID']; ?>">
<button type="submit" class="btn btn-success">
<i class="fas fa-calendar-plus"></i> Book
</button>
</form>
</div>
</div>
</div>
<?php
}
} else {
echo '<div class="empty-state" style="grid-column: 1 / -1;">
<i class="fas fa-user-md"></i>
<p>No doctors found matching your criteria</p>
</div>';
}
?>
</div>
</div>
<?php endif; ?>
</div>
</div>

<script>
function bookAppointment(doctorId, day) {
var f = document.createElement('form');
f.method = 'POST';
f.action = 'manage_appointments.php';
var d = document.createElement('input');
d.type = 'hidden';
d.name = 'doctor';
d.value = doctorId;
f.appendChild(d);
if (day) {
var dayInput = document.createElement('input');
dayInput.type = 'hidden';
dayInput.name = 'day';
dayInput.value = day;
f.appendChild(dayInput);
}
document.body.appendChild(f);
f.submit();
}
</script>
</body>
</html>