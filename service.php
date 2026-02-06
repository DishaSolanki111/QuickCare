<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuickCare Services</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --primary: #0066cc;
    --secondary: #00a8cc;
    --light: #f8fbff;
    --dark: #1a3a5f;
    --text: #444;
}

* {
    box-sizing: border-box;
    font-family: "Segoe UI", sans-serif;
}

body {
    margin: 0;
    background: var(--light);
    color: var(--text);
}

/* ===== PAGE HEADER ===== */
.page-header {
    text-align: center;
    padding: 100px;
}

.page-header h1 {
    font-size: 2.6rem;
    color: var(--dark);
}

.page-header p {
    max-width: 750px;
    margin: 10px auto;
    font-size: 1.1rem;
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

/* ===== RESPONSIVE ===== */
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
</style>
</head>

<body>

<div class="page-header">
    <h1>Our Healthcare Services</h1>
    <p>QuickCare provides smart digital healthcare solutions for patients, doctors and hospital staff.</p>
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

</body>
</html>
