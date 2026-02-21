<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuickCare Services</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #0066cc;
    --secondary: #00a8cc;
    --primary-blue: #1a73e8;
    --secondary-blue: #4285f4;
    --medium-blue: #8ab4f8;
    --header-gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
    --light: #f8fbff;
    --dark: #1a3a5f;
    --text: #444;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
    color: var(--text);
    min-height: 100vh;
    padding-top: 80px;
}

/* ===== PAGE HEADER (same as appointment.php) ===== */
.page-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    color: white;
    height: 200px; /* Match aboutus.php hero height */
    padding: 20px; /* Match aboutus.php vertical spacing */
    text-align: center;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-header::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
    background-size: cover;
}

.page-header .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
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

/* ===== SERVICES ===== */
.services-container {
    max-width: 1100px;
    margin: auto;
    padding: 20px 20px 80px;
}

.service-card {
    display: flex;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    margin-bottom: 50px;
    overflow: hidden;
}

/* IMAGE */
.service-image {
    flex: 1;
    background: linear-gradient(135deg, #e6f0ff, #f4f9ff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

.service-image img {
    width: 100%;
    max-height: 1000px;
    
}

/* CONTENT */
.service-content {
    flex: 1;
    padding: 45px 40px;
}

.service-content h2 {
    color: var(--primary);
    margin-bottom: 15px;
}

.service-content p {
    margin-bottom: 20px;
}

/* FEATURES */
.service-features {
    list-style: none;
    padding: 0;
    margin-bottom: 25px;
}

.service-features li {
    padding: 6px 0;
}

.service-features li::before {
    content: "✔";
    color: var(--primary);
    margin-right: 8px;
}

/* ===== STATIC WORKFLOW ===== */
.workflow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 35px;
    position: relative;
}

.workflow::before {
    content: "";
    position: absolute;
    top: 18px;
    left: 8%;
    right: 8%;
    height: 3px;
    background: #d6e4ff;
}

.workflow-step {
    text-align: center;
    width: 25%;
    position: relative;
    z-index: 1;
}

.workflow-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.workflow-step p {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--dark);
}

/* ===== FOOTER (same as appointment.php) ===== */
footer {
    background: var(--header-gradient-1);
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

.footer-bottom {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.7);
}

/* ===== RESPONSIVE (same as appointment.php) ===== */
@media (max-width: 992px) {
    .page-header h1 {
        font-size: 2.4rem;
    }
    
    .page-header p {
        font-size: 1.1rem;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 3rem 0;
    }

    .page-header h1 {
        font-size: 2.2rem;
    }

    .page-header p {
        font-size: 1rem;
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

@media(max-width: 900px) {
    .service-card {
        flex-direction: column;
    }

    .workflow {
        flex-direction: column;
    }

    .workflow::before {
        display: none;
    }

    .workflow-step {
        width: 100%;
        margin-bottom: 20px;
    }
}

@media (max-width: 576px) {
    .footer-content {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="page-header">
    <div class="container">
        <h1>Our Healthcare Services</h1>
        <p>QuickCare provides smart digital healthcare solutions for patients, doctors and hospital staff.</p>
    </div>
</div>

<div class="services-container">

<!-- ================= SERVICE 1 ================= -->
<div class="service-card">
    <div class="service-image">
        <img src="uploads/appointment booking.png" alt="Appointment Booking">
    </div>
    <div class="service-content">
        <h2>Online Appointment Booking</h2>
        <p>Patients can book doctor appointments easily without waiting in queues.</p>

        <ul class="service-features">
            <li>Easy online booking system</li>
            <li>Real-time availability check</li>
            <li>Instant confirmation</li>
            <li>Appointment history tracking</li>
            <li>Doctor & specialization selection</li>
            <li>Date and time slot availability</li>
            <li>Instant confirmation</li>
        </ul>

        <div class="workflow">
            <div class="workflow-step">
                <div class="workflow-circle">1</div>
                <p>Login</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">2</div>
                <p>Select Doctor</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">3</div>
                <p>Choose date & time Slot</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">4</div>
                <p>Confirm</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= SERVICE 2 ================= -->
<div class="service-card">
    <div class="service-image">
        <img src="uploads/digital prescription.png" alt="Digital Prescription & Medical Records">
    </div>
    <div class="service-content">
        <h2>Digital Prescription & Medical Records</h2>
        <p>Access your prescriptions and medical history digitally anytime, anywhere in digital format.</p>

        <ul class="service-features">
            <li>Digital prescriptions</li>
            <li>Complete medical history</li>                
            <li>Easy sharing with doctors</li>
        </ul>

        <div class="workflow">
            <div class="workflow-step">
                <div class="workflow-circle">1</div>
                <p>Consultation</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">2</div>
                <p>Doctor Uploads</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">3</div>
                <p>Stored Securely</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">4</div>
                <p>Patient Views</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= SERVICE 3 ================= -->
<div class="service-card">
    <div class="service-image">
        <img src="uploads/medicine and appointment reminder.png" alt="Reminder service">
    </div>
    <div class="service-content">
        <h2>Medicine & Appointment Reminders</h2>
        <p>Set reminders for your appointments and medication schedules.</p>

        <ul class="service-features">
            <li>Appointment reminders</li>
            <li>Medicine schedule alerts</li>
            <li>Smart reminders</li>
            <li>SMS alerts</li>           
            <li>Customizable reminders</li>
        </ul>

        <div class="workflow">
            <div class="workflow-step">
                <div class="workflow-circle">1</div>
                <p>Appointment Scheduled</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">2</div>
                <p>Prescription Created</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">3</div>
                <p>Reminder Set</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">4</div>
                <p>Reminder Sent</p>
            </div>
            
        </div>
    </div>
</div>

<!-- ================= SERVICE 4 ================= -->
<div class="service-card">
    <div class="service-image">
        <img src="uploads/feedback and support.png" alt="Feedback & Support">
    </div>
    <div class="service-content">
        <h2>Feedback & Support</h2>
        <p>Get support and feedback on your healthcare experience.</p>

        <ul class="service-features">
            <li>Rate your experience</li>
            <li>Phone support</li>
            <li>Feedback tracking</li>
        </ul>

        <div class="workflow">
            <div class="workflow-step">
                <div class="workflow-circle">1</div>
                <p>Appointment Completed</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">2</div>
                <p>Give Star Rating</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">3</div>
                <p>Give Feedback</p>
            </div>
            <div class="workflow-step">
                <div class="workflow-circle">4</div>
                <p>Feedback Submitted</p>
            </div>
        </div>
    </div>
</div>

</div>

    <!-- Footer (same as appointment.php) -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="appointment.php">Find Doctors</a></li>
                    <li><a href="appointment.php">Book Appointment</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>