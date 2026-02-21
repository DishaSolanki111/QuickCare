<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// Get today's date and max date (1 month from today) in YYYY-MM-DD format
 $today = date('Y-m-d');
 $maxDate = date('Y-m-d', strtotime('+1 month'));
 $formattedToday = date('d-m-Y', strtotime($today));

// Fetch specializations for the dropdown
 $specializations = [];
 $sql = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl ORDER BY SPECIALISATION_NAME";
 $result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $specializations[] = $row;
    }
}
 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Care - Doctor Appointment Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6fdc;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #e9ecef;
            --dark-color: #343a40;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.1);
            /* Footer styles from doctors.php */
            --primary: #0066cc;
            --primary-dark: #004a99;
            --primary-light: #e6f0ff;
            --secondary: #0099ff;
            --accent: #0052cc;
            --light-blue: #f0f7ff;
            --medium-blue: #d4e6ff;
            --dark-blue: #003366;
            --text-dark: #1a3a5f;
            --text-light: #ffffff;
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            
            /* Using same color variables as appointment.php */
            --primary-blue: #1a73e8;
            --secondary-blue: #4285f4;
            --light-blue-bg: #e8f0fe;
            --medium-blue-icon: #8ab4f8;
            --dark-blue-header: #174ea6;
            --accent-blue: #0b57d0;
            --text-dark-header: #202124;
            --text-light-header: #ffffff;
            --shadow-header: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover-header: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            color: var(--text-dark-header);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 0;
            margin-top: 0;
        }

        header {
            margin: 0;
            padding: 0;
            background: transparent;
            box-shadow: none;
        }

        .page-header {
            height: 200px; /* Same as .hero in aboutus.php */
            background: var(--gradient-1);
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 20px;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-content {
            flex-grow: 1;
            background-color: white;
        }

        /* .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        } */

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo i {
            margin-right: 10px;
            font-size: 2rem;
        }

        .filters {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin: 2rem auto;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            max-width: 1200px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .filter-group input,
        .filter-group select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .date-display {
            background-color: var(--secondary-color);
            padding: 1rem;
            border-radius: 10px;
            margin: 2rem auto 1.5rem;
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.5rem;
            max-width: 1200px;
        }

        .doctors-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 2rem auto 2rem;
            max-width: 1200px;
            padding: 0 20px;
        }

        .doctor-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .doctor-header {
            display: flex;
            padding: 1.5rem;
            background-color: var(--secondary-color);
            border-bottom: 1px solid #eee;
        }

        .doctor-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .doctor-info h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .doctor-specialization {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .doctor-schedule {
            padding: 1.5rem;
        }

        .schedule-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.5rem;
            text-align: center;
            border-radius: 5px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .time-slot:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .available {
            background-color: var(--success-color);
            color: white;
        }

        .time-slot.available.selected {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 3px rgba(74, 111, 220, 0.5);
        }

        .booked {
            background-color: var(--danger-color);
            color: white;
            cursor: not-allowed;
        }

        .unavailable {
            background-color: var(--light-color);
            color: #6c757d;
            cursor: not-allowed;
        }

        .tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--dark-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .time-slot:hover .tooltip {
            opacity: 1;
        }

        .no-doctors {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin: 2rem 0;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--light-color);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-book {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            margin-top: 1rem;
            display: block;
            width: 100%;
        }

        .btn-book:hover {
            background-color: #3a5bc9;
        }

        .btn-book:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        /* Footer with Wave Effect - Color changed to match aboutus.php */
        footer {
            background: var(--gradient-1);
            color: white;
            padding: 3rem 5%;
            position: relative;
            margin-top: 3rem;
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
            background-color: var(--medium-blue);
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

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 992px) {
            .page-header h1 {
                font-size: 2.4rem;
            }
            
            .page-header p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .doctors-container {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .page-header {
                padding-top: calc(80px + 1.5rem);
                padding-bottom: 3rem;
            }
            
            .page-header h1 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header.php'; ?>
    </header>

    <div class="page-header">
        <div class="container">
            <h1>Our Doctor Schedule</h1>
            <p>View available time slots and book appointments with our trusted specialists</p>
        </div>
    </div>

    <main class="main-content">
        <div class="container">
        <div class="filters">
            <div class="filter-group">
                <label for="date-picker">Select Date</label>
                <input type="date" id="date-picker" value="<?php echo $today; ?>" min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>" title="You can book appointments only within the next 1 month">
            </div>
            <div class="filter-group">
                <label for="specialization">Specialization</label>
                <select id="specialization">
                    <option value="0">All Specializations</option>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?php echo $spec['SPECIALISATION_ID']; ?>"><?php echo $spec['SPECIALISATION_NAME']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>&nbsp;</label>
                <button id="filter-btn" class="btn-book">Search</button>
            </div>
        </div>

        <div class="date-display" id="date-display">
            Schedule for: <?php echo $formattedToday; ?>
        </div>

        <div id="doctors-container" class="doctors-container">
            <div class="loading">
                <div class="spinner"></div>
            </div>
        </div>
        </div>
    </main>

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
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load of doctor schedules
            loadDoctorSchedules();

            // Event listeners for filters
            document.getElementById('date-picker').addEventListener('change', function() {
                updateDateDisplay();
                loadDoctorSchedules();
            });

            document.getElementById('specialization').addEventListener('change', loadDoctorSchedules);
            document.getElementById('filter-btn').addEventListener('click', loadDoctorSchedules);

            // Function to update the date display
            function updateDateDisplay() {
                const datePicker = document.getElementById('date-picker');
                const dateDisplay = document.getElementById('date-display');
                const selectedDate = new Date(datePicker.value);
                const formattedDate = selectedDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                dateDisplay.textContent = `Schedule for: ${formattedDate}`;
            }

            // Function to load doctor schedules via AJAX
            function loadDoctorSchedules() {
                const date = document.getElementById('date-picker').value;
                const specialization = document.getElementById('specialization').value;
                const container = document.getElementById('doctors-container');
                
                // Show loading spinner
                container.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
                
                // Fetch data from the backend
                fetch('fetch_doctor_schedule.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `date=${date}&specialization=${specialization}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDoctors(data.doctors);
                    } else {
                        container.innerHTML = `<div class="no-doctors">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div class="no-doctors">Error loading doctor schedules. Please try again later.</div>';
                });
            }

            // Function to display doctors and their schedules
            function displayDoctors(doctors) {
                const container = document.getElementById('doctors-container');
                
                if (doctors.length === 0) {
                    container.innerHTML = '<div class="no-doctors">No doctors available for the selected date and specialization.</div>';
                    return;
                }
                
                let html = '';
                doctors.forEach(doctor => {
                    html += `
                        <div class="doctor-card">
                            <div class="doctor-header">
                                <img src="${doctor.profile_image ? doctor.profile_image : 'uploads/default_doctor.JPG'}" alt="${doctor.first_name} ${doctor.last_name}" class="doctor-image">
                                <div class="doctor-info">
                                    <h3>Dr. ${doctor.first_name} ${doctor.last_name}</h3>
                                    <div class="doctor-specialization">${doctor.specialization_name}</div>
                                </div>
                            </div>
                            <div class="doctor-schedule">
                                <div class="schedule-title">Available Time Slots</div>
                                <div class="time-slots">
                                    ${generateTimeSlots(doctor)}
                                </div>
                                <button class="btn-book" id="book-btn-${doctor.doctor_id}" disabled>Select a time slot to book</button>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
                
                // Add event listeners to time slots
                document.querySelectorAll('.time-slot.available').forEach(slot => {
                    slot.addEventListener('click', function() {
                        // Remove previous selection
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        
                        // Add selection to clicked slot
                        this.classList.add('selected');
                        
                        // Enable the book button
                        const doctorId = this.getAttribute('data-doctor-id');
                        const bookBtn = document.getElementById(`book-btn-${doctorId}`);
                        bookBtn.disabled = false;
                        bookBtn.textContent = 'Book Appointment';
                        
                        // Set up the book button click event
                        bookBtn.onclick = function() {
                            const time = slot.getAttribute('data-time');
                            const date = document.getElementById('date-picker').value;
                            // Submit POST form to booking page with parameters
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'book_appointment_login.php';
                            var doctorInput = document.createElement('input');
                            doctorInput.type = 'hidden';
                            doctorInput.name = 'doctor_id';
                            doctorInput.value = doctorId;
                            form.appendChild(doctorInput);
                            var dateInput = document.createElement('input');
                            dateInput.type = 'hidden';
                            dateInput.name = 'date';
                            dateInput.value = date;
                            form.appendChild(dateInput);
                            var timeInput = document.createElement('input');
                            timeInput.type = 'hidden';
                            timeInput.name = 'time';
                            timeInput.value = time;
                            form.appendChild(timeInput);
                            document.body.appendChild(form);
                            form.submit();
                        };
                    });
                });
            }

            // Function to generate time slots for a doctor
            function generateTimeSlots(doctor) {
                if (!doctor.schedule || doctor.schedule.length === 0) {
                    return '<div class="time-slot unavailable">No schedule</div>';
                }
                
                let slotsHtml = '';
                const schedule = doctor.schedule[0]; // Assuming one schedule per day for simplicity
                
                // Generate time slots based on start_time, end_time and slot_duration (default 30 minutes)
                const startTime = new Date(`2000-01-01T${schedule.start_time}`);
                const endTime = new Date(`2000-01-01T${schedule.end_time}`);
                const slotDuration = 30; // Default slot duration in minutes
                
                const currentTime = new Date(startTime);
                
                while (currentTime < endTime) {
                    const timeString = currentTime.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                    
                    // Check if this slot is booked
                    const isBooked = doctor.booked_slots && doctor.booked_slots.includes(timeString);
                    
                    // Determine slot class
                    let slotClass = 'available';
                    let tooltipText = 'Available';
                    
                    if (isBooked) {
                        slotClass = 'booked';
                        tooltipText = 'Booked';
                    } else if (!schedule.is_available) {
                        slotClass = 'unavailable';
                        tooltipText = 'Not Available';
                    }
                    
                    slotsHtml += `
                        <div class="time-slot ${slotClass}" 
                             data-doctor-id="${doctor.doctor_id}" 
                             data-time="${timeString}"
                             ${slotClass === 'available' ? '' : 'style="cursor: not-allowed;"'}>
                            ${timeString}
                            <div class="tooltip">${tooltipText}</div>
                        </div>
                    `;
                    
                    // Move to next slot
                    currentTime.setMinutes(currentTime.getMinutes() + slotDuration);
                }
                
                return slotsHtml;
            }
        });
    </script>
</body>
</html>