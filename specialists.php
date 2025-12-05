<?php
@include 'config.php';

 $spec_id = $_GET['spec_id'] ?? 0;

// Use prepared statements to prevent SQL injection
 $stmt = mysqli_prepare($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.SPECIALISATION_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $spec_id);
mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Specialists</title>
<link rel="stylesheet" href="specialists.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== MODAL STYLING ===== */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 40px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    background: rgba(0,0,0,0.6);
}

.modal-content {
    background: #fff;
    margin: auto;
    padding: 20px;
    width: 90%;
    max-width: 700px;
    border-radius: 10px;
    animation: popup 0.3s ease;
}

@keyframes popup {
    from { transform: scale(0.8); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
    font-weight: bold;
}
.close:hover { color: red; }

/* Calendar styles */
.calendar-container {
    margin: 20px 0;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.calendar-nav {
    background: #4a90e2;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.calendar-nav:hover {
    background: #3a7bc8;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.calendar-day-header {
    text-align: center;
    font-weight: bold;
    padding: 10px 0;
    color: #4a90e2;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.calendar-day:hover {
    background: #f0f8ff;
}

.calendar-day.other-month {
    color: #ccc;
}

.calendar-day.today {
    background: #e6f2ff;
    font-weight: bold;
}

.calendar-day.selected {
    background: #4a90e2;
    color: white;
}

.calendar-day.unavailable {
    color: #ccc;
    cursor: not-allowed;
    text-decoration: line-through;
}

.calendar-day.available {
    background: #e8f5e9;
}

.calendar-day.available:hover {
    background: #c8e6c9;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 20px;
}

.time-slot {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.time-slot:hover {
    background: #f0f8ff;
}

.time-slot.selected {
    background: #4a90e2;
    color: white;
    border-color: #4a90e2;
}

.time-slot.unavailable {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
}

.booking-form {
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.btn-book {
    background: #4a90e2;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.btn-book:hover {
    background: #3a7bc8;
}

.notification {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
}

.notification.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.notification.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
// OPEN MODAL + LOAD book.php via AJAX
function openBookingPopup(doctorId) {
    document.getElementById('bookingModal').style.display = 'block';
    document.getElementById('modal-body').innerHTML = "Loading…";

    fetch("book.php?doctor_id=" + doctorId)
    .then(res => res.text())
    .then(html => {
        document.getElementById('modal-body').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('modal-body').innerHTML = "Error loading booking form.";
        console.error('Error:', error);
    });
}

// CLOSE MODAL
function closeModal() {
    document.getElementById('bookingModal').style.display = 'none';
    document.getElementById('modal-body').innerHTML = "";
}
</script>
</head>

<body>
<h1 class="main-title">Book Your Appointment</h1>

<div class="container">
    <h2 class="sub-title">Available Specialists</h2>

    <div class="cards">
        <?php 
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){ ?>
                <div class="card">
                    <img src="imgs/doctor_<?php echo $row['DOCTOR_ID']; ?>.jpg" 
                         onerror="this.src='imgs/default.jpg'" 
                         class="doc-img">

                    <h3 class="doc-name">Dr. <?php echo $row['FIRST_NAME']; ?></h3>
                    <p class="doc-type"><?php echo $row['SPECIALISATION_NAME']; ?></p>

                    <button class="btn" onclick="openBookingPopup(<?php echo $row['DOCTOR_ID']; ?>)">
                        Book Now
                    </button>
                    <a class="btn" href="profile.php?id=<?php echo $row['DOCTOR_ID']; ?>">View Profile</a>
                </div>
        <?php }} else { ?>
            <p class="no-data">No doctors found.</p>
        <?php } ?>
    </div>
</div>

<!-- FULL SCREEN MODAL -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-body"></div>
    </div>
</div>

</body>
</html>