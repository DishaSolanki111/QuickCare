<?php

// Start session to store booking data
session_start();
include "config.php";
include "header.php";

// Get doctor ID from URL
 $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;

// Redirect if no doctor ID is provided
if ($doctor_id == 0) {
    header("Location: doctors.php");
    exit();
}

// Fetch the selected doctor's details
 $doctor_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = $doctor_id AND d.STATUS = 'approved'
");

if (mysqli_num_rows($doctor_query) == 0) {
    header("Location: doctors.php");
    exit();
}

 $doctor = mysqli_fetch_assoc($doctor_query);

 $_SESSION['booking_doctor_id'] = $doctor['DOCTOR_ID'];
 $_SESSION['booking_doctor_name'] = $doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME'];
 $_SESSION['booking_specialization'] = $doctor['SPECIALISATION_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Appointment Date - Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy all the CSS styles from book_appointment.php here */
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top:100px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 5rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

      

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .booking-section {
            padding: 4rem 0;
            flex-grow: 1;
        }

        .booking-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .doctor-summary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            flex-shrink: 0;
        }

        .doctor-info h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .doctor-info p {
            opacity: 0.9;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            width: 100%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 20px;
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
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background: var(--light-blue);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--medium-blue);
            transform: translateY(-3px);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: var(--secondary-color);
        }

        .step.completed {
            background-color: var(--accent-color);
        }

        /* Calendar Styles */
        .calendar-container {
            margin-top: 10px;
            position: relative;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calendar-nav {
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .calendar-nav:hover {
            background: var(--primary-color);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            padding: 5px;
            color: var(--dark-color);
        }

        .calendar-day {
            text-align: center;
            padding: 10px 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-day.available {
            background-color: rgba(46, 204, 113, 0.3); /* Increased green opacity */
            border-color: var(--accent-color);
            color: var(--accent-color);
            font-weight: 500;
        }

        .calendar-day.selected {
            background-color: var(--secondary-color);
            color: white;
            font-weight: bold;
        }

        .calendar-day.fully-booked {
            background-color: rgba(243, 156, 18, 0.35); /* yellow when all slots are booked */
            border: 1px solid var(--warning-color);
            color: #b36b00;
            font-weight: 500;
        }

        .calendar-day.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-day.other-month {
            color: #eee;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .loading i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Footer with Wave Effect */
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: white;
            padding: 3rem 5%;
            position: relative;
        }

        footer::before {
            content: "";
            position: absolute;
            top: -100px;
            left: 0;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,112C1248,107,1344,117,1392,122.7L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--soft-blue);
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-column ul li a:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .doctor-summary {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
  

    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <!-- Doctor Summary -->
                <div class="doctor-summary">
                    <div class="doctor-avatar">
                        <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                    </div>
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
                        <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                    </div>
                </div>

                <div class="modal-content">
                    <form method="POST" action="doctors.php" style="display:inline">
                    <input type="hidden" name="spec_id" value="<?php echo $doctor['SPECIALISATION_ID']; ?>">
                    <button type="submit" class="close" style="background:none;border:none;font-size:28px;cursor:pointer">&times;</button>
                </form>
                    <h2>Select Appointment Date</h2>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step1">1</div>
                        <div class="step" id="step2">2</div>
                        <div class="step" id="step3">3</div>
                        <div class="step" id="step4">4</div>
                        <div class="step" id="step5">5</div>
                    </div>
                    
                    <form method="POST" action="book_appointment_time.php" id="appointmentForm">
                        <div class="form-group">
                            <label>Select Appointment Date</label>
                            <div class="calendar-container">
                                <div class="calendar-header">
                                    <button type="button" class="calendar-nav" id="prevMonth">&lt;</button>
                                    <span id="currentMonth">Month Year</span>
                                    <button type="button" class="calendar-nav" id="nextMonth">&gt;</button>
                                </div>
                                <div class="calendar-grid" id="calendarGrid">
                                    <!-- Calendar will be generated here -->
                                </div>
                            </div>
                            <div style="margin-top:10px; font-size: 0.9rem; color:#555;">
                                <span style="display:inline-block;width:16px;height:12px;border-radius:3px;background-color:rgba(46, 204, 113, 0.3);border:1px solid #2ecc71;margin-right:6px;vertical-align:middle;"></span>
                                Green = Available date&nbsp;&nbsp;
                                <span style="display:inline-block;width:16px;height:12px;border-radius:3px;background-color:rgba(243, 156, 18, 0.35);border:1px solid #f39c12;margin:0 6px;vertical-align:middle;"></span>
                                Yellow = Fully booked date
                            </div>
                            <input type="hidden" id="selected_date" name="appointment_date" required>
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" onclick="proceedToTime()" id="nextToTime">
                                Next: Select Time <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer with Wave Effect -->
    <footer id="footer-section">
        <div class="footer-content">
            <div class="footer-column">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="doctors.php">Find Doctors</a></li>
                    <li><a href="appointment.php">Book Appointment</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Healthcare Ave, Medical City</a></li>
                    <li><a href="#"><i class="fas fa-phone"></i> 91+ 9632587418</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 QuickCare. All Rights Reserved. | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Privacy Policy</a> | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Terms of Service</a></p>
        </div>
    </footer>

    <script>
    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar
        initCalendar();
        
        // Load doctor's schedule
        loadDoctorSchedule(<?php echo $doctor['DOCTOR_ID']; ?>);
    });
    
    // Calendar functionality
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDoctorId = <?php echo $doctor['DOCTOR_ID']; ?>;
    let doctorSchedule = [];
    let fullyBookedDates = [];
    
    function initCalendar() {
        renderCalendar(currentMonth, currentYear);
        
        const today = new Date();
        const maxDate = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
        
        document.getElementById('prevMonth').addEventListener('click', () => {
            const currentView = new Date(currentYear, currentMonth);
            const minView = new Date(today.getFullYear(), today.getMonth());
            if (currentView <= minView) return;
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            loadFullyBookedDates(currentMonth, currentYear);
            renderCalendar(currentMonth, currentYear);
        });
        
        document.getElementById('nextMonth').addEventListener('click', () => {
            const currentView = new Date(currentYear, currentMonth);
            const maxView = new Date(maxDate.getFullYear(), maxDate.getMonth());
            if (currentView >= maxView) return;
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            loadFullyBookedDates(currentMonth, currentYear);
            renderCalendar(currentMonth, currentYear);
        });

        // Initial fully booked dates load
        loadFullyBookedDates(currentMonth, currentYear);
    }
    
    function renderCalendar(month, year) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
        
        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';
        
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            calendarGrid.appendChild(emptyDay);
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const maxDate = new Date();
        maxDate.setMonth(maxDate.getMonth() + 1);
        maxDate.setHours(23, 59, 59, 999);
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            const currentDate = new Date(year, month, day);
            currentDate.setHours(0, 0, 0, 0);
            if (currentDate < today || currentDate > maxDate) {
                dayElement.classList.add('disabled');
            }
            
            if (selectedDoctorId && !dayElement.classList.contains('disabled')) {
                const dayOfWeek = currentDate.getDay();
                const dayMap = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                
                if (doctorSchedule.includes(dayMap[dayOfWeek])) {
                    dayElement.classList.add('available');

                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    if (fullyBookedDates.includes(dateStr)) {
                        dayElement.classList.add('fully-booked');
                    }

                    dayElement.addEventListener('click', () => selectDate(year, month, day));
                } else {
                    dayElement.classList.add('disabled');
                }
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    function loadDoctorSchedule(doctorId) {
        selectedDoctorId = doctorId;
        
        fetch('get_doctor_schedule.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'doctor_id=' + encodeURIComponent(doctorId) })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    doctorSchedule = data.available_days;
                    renderCalendar(currentMonth, currentYear);
                } else {
                    document.getElementById('calendarGrid').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading doctor's schedule: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading doctor schedule:', error);
                document.getElementById('calendarGrid').innerHTML = `
                    <div class="alert alert-danger">
                        Error loading doctor's schedule. Please try again later.
                    </div>
                `;
            });
    }

    function loadFullyBookedDates(month, year) {
        // month in JS is 0-based; backend expects 1-12
        const backendMonth = month + 1;

        fetch('get_fully_booked_dates.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'doctor_id=' + encodeURIComponent(selectedDoctorId) +
                  '&month=' + encodeURIComponent(backendMonth) +
                  '&year=' + encodeURIComponent(year)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && Array.isArray(data.fully_booked_dates)) {
                fullyBookedDates = data.fully_booked_dates;
                renderCalendar(currentMonth, currentYear);
            }
        })
        .catch(err => {
            console.error('Error loading fully booked dates:', err);
        });
    }
    
    function selectDate(year, month, day) {
        const date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        document.getElementById('selected_date').value = date;
        
        document.querySelectorAll('.calendar-day').forEach(el => {
            el.classList.remove('selected');
        });
        event.target.classList.add('selected');
        
        document.getElementById('nextToTime').disabled = false;
    }
    
    function proceedToTime() {
        const selectedDate = document.getElementById('selected_date').value;
        if (!selectedDate) {
            alert('Please select a date');
            return;
        }
        
        // Store the selected date in session
        fetch('store_booking_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `step=date&appointment_date=${selectedDate}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Redirect to time selection page
                window.location.href = 'book_appointment_time.php';
            } else {
                alert('Error saving date: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving date:', error);
            alert('An error occurred. Please try again.');
        });
    }
    </script>
</body>
</html>