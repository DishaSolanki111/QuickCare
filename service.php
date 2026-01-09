<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Hospital Patient Appointment Booking System</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f9ff;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            padding: 40px 0;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        h1 {
            color: #2c6ecb;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* Services Section */
        .services-section {
            padding: 80px 0;
        }

        /* Service Cards with Split Layout */
        .service-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 60px;
            overflow: hidden;
            display: flex;
            min-height: 400px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        /* Left side - Image */
        .service-image {
            flex: 1;
            background-color: #e6f0ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .service-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(44, 110, 203, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .service-image svg {
            width: 120px;
            height: 120px;
            fill: #2c6ecb;
            position: relative;
            z-index: 1;
        }

        /* Right side - Content */
        .service-content {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .service-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #2c6ecb;
            font-weight: 600;
        }

        .service-description {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .service-features {
            list-style: none;
        }

        .service-features li {
            padding: 10px 0;
            color: #333;
            display: flex;
            align-items: center;
        }

        .service-features li::before {
            content: '✓';
            color: #2c6ecb;
            font-weight: bold;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Animation Classes */
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Alternate layout for even cards */
        .service-card.reverse {
            flex-direction: row-reverse;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .service-card {
                flex-direction: column !important;
                min-height: auto;
            }

            .service-image {
                padding: 30px;
            }

            .service-image svg {
                width: 80px;
                height: 80px;
            }

            .service-content {
                padding: 30px;
            }

            .service-title {
                font-size: 1.5rem;
            }

            .service-description {
                font-size: 1rem;
            }
        }

        /* Expanded Service View */
        .expanded-service {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            z-index: 1000;
            display: none;
            overflow-y: auto;
            padding: 40px 0;
        }

        .expanded-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .back-button {
            background-color: #2c6ecb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #1e4d8f;
        }

        .expanded-header {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .expanded-icon {
            width: 80px;
            height: 80px;
            background-color: #e6f0ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }

        .expanded-icon svg {
            width: 40px;
            height: 40px;
            fill: #2c6ecb;
        }

        .expanded-title {
            font-size: 2rem;
            color: #2c6ecb;
        }

        /* Vertical Timeline */
        .timeline {
            position: relative;
            margin: 40px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 30px;
            height: 100%;
            width: 2px;
            background-color: #e6f0ff;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 30px;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .timeline-item:hover {
            transform: translateX(5px);
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            background-color: #e6f0ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            transition: background-color 0.3s ease;
        }

        .timeline-item.active .timeline-icon {
            background-color: #2c6ecb;
        }

        .timeline-icon svg {
            width: 30px;
            height: 30px;
            fill: #2c6ecb;
        }

        .timeline-item.active .timeline-icon svg {
            fill: white;
        }

        .timeline-content {
            background-color: #f5f9ff;
            padding: 20px;
            border-radius: 8px;
            flex-grow: 1;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c6ecb;
        }

        .timeline-description {
            color: #6c757d;
        }

        .timeline-arrow {
            position: absolute;
            left: 30px;
            top: 60px;
            width: 2px;
            height: 30px;
            background-color: #e6f0ff;
        }

        /* Notification Flow */
        .notification-flow {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 40px 0;
        }

        .notification-item {
            background-color: #f5f9ff;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .notification-item:hover {
            transform: translateX(5px);
            background-color: #e6f0ff;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .notification-icon svg {
            width: 25px;
            height: 25px;
            fill: #2c6ecb;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c6ecb;
        }

        .notification-description {
            color: #6c757d;
        }

        .notification-arrow {
            color: #2c6ecb;
            font-size: 1.5rem;
        }

        /* Rating Flow */
        .rating-flow {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin: 40px 0;
        }

        .rating-item {
            background-color: #f5f9ff;
            border-radius: 8px;
            padding: 25px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .rating-item:hover {
            transform: scale(1.02);
            background-color: #e6f0ff;
        }

        .rating-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .rating-icon {
            width: 50px;
            height: 50px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .rating-icon svg {
            width: 25px;
            height: 25px;
            fill: #2c6ecb;
        }

        .rating-title {
            font-weight: 600;
            color: #2c6ecb;
            font-size: 1.1rem;
        }

        .rating-content {
            padding-left: 70px;
        }

        .star-rating {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .star {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .star.filled {
            color: #ffc107;
        }

        .comment-box {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
            font-family: inherit;
            margin-bottom: 10px;
        }

        .submit-button {
            background-color: #2c6ecb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #1e4d8f;
        }
    </style>
</head>
<body>
    <?php
    include 'header.php';?>
        <div class="container">
            <h1>Our Services</h1>
            <p class="subtitle">Comprehensive healthcare management at your fingertips</p>
        </div>
    

    <main class="container">
        <section class="services-section" id="servicesSection">
            <!-- Service Card 1: Appointment Booking & Management -->
            <article class="service-card" data-service="appointment">
                <div class="service-image">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                </div>
                <div class="service-content">
                    <h2 class="service-title">Appointment Booking & Management</h2>
                    <p class="service-description">Schedule, view, and manage your appointments with healthcare providers seamlessly through our intuitive platform.</p>
                    <ul class="service-features">
                        <li>Easy online booking system</li>
                        <li>Real-time availability check</li>
                        <li>Instant confirmation</li>
                        <li>Appointment history tracking</li>
                    </ul>
                </div>
            </article>

            <!-- Service Card 2: Digital Prescription & Medical Records -->
            <article class="service-card reverse" data-service="prescription">
                <div class="service-image">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                </div>
                <div class="service-content">
                    <h2 class="service-title">Digital Prescription & Medical Records</h2>
                    <p class="service-description">Access your prescriptions and medical history digitally anytime, anywhere with secure cloud storage.</p>
                    <ul class="service-features">
                        <li>Digital prescriptions</li>
                        <li>Complete medical history</li>
                        <li>Lab reports storage</li>
                        <li>Easy sharing with doctors</li>
                    </ul>
                </div>
            </article>

            <!-- Service Card 3: Medicine & Appointment Reminders -->
            <article class="service-card" data-service="reminders">
                <div class="service-image">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <div class="service-content">
                    <h2 class="service-title">Medicine & Appointment Reminders</h2>
                    <p class="service-description">Never miss your medication or appointments with timely notifications through multiple channels.</p>
                    <ul class="service-features">
                        <li>Smart reminders</li>
                        <li>SMS & email alerts</li>
                        <li>Mobile push notifications</li>
                        <li>Customizable schedules</li>
                    </ul>
                </div>
            </article>

            <!-- Service Card 4: Feedback & Support -->
            <article class="service-card reverse" data-service="feedback">
                <div class="service-image">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                    </svg>
                </div>
                <div class="service-content">
                    <h2 class="service-title">Feedback & Support</h2>
                    <p class="service-description">Share your experience and get assistance from our dedicated support team 24/7.</p>
                    <ul class="service-features">
                        <li>Rate your experience</li>
                        <li>Submit feedback</li>
                        <li>24/7 customer support</li>
                        <li>Quick resolution</li>
                    </ul>
                </div>
            </article>
        </section>
    </main>

    <!-- Expanded Service Views -->
    <section class="expanded-service" id="expandedService">
        <div class="expanded-content">
            <!-- Appointment Booking & Management Expanded View -->
            <div id="appointmentView" class="service-view" style="display: none;">
                <button class="back-button" onclick="closeExpandedView()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back to Services
                </button>
                
                <div class="expanded-header">
                    <div class="expanded-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                    </div>
                    <h2 class="expanded-title">Appointment Booking & Management</h2>
                </div>
                
                <div class="timeline">
                    <div class="timeline-item active" data-step="1">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Login</h3>
                            <p class="timeline-description">Access your account using your credentials to begin the booking process.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="2">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Select Doctor</h3>
                            <p class="timeline-description">Choose from our list of qualified healthcare professionals based on specialty and availability.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="3">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Choose Date & Time</h3>
                            <p class="timeline-description">Select a convenient date and time slot from the doctor's available schedule.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="4">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Confirm</h3>
                            <p class="timeline-description">Review your appointment details and confirm to finalize your booking.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="5">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Appointment Scheduled</h3>
                            <p class="timeline-description">Your appointment has been successfully scheduled. You'll receive a confirmation notification.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Digital Prescription & Medical Records Expanded View -->
            <div id="prescriptionView" class="service-view" style="display: none;">
                <button class="back-button" onclick="closeExpandedView()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back to Services
                </button>
                
                <div class="expanded-header">
                    <div class="expanded-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                    </div>
                    <h2 class="expanded-title">Digital Prescription & Medical Records</h2>
                </div>
                
                <div class="timeline">
                    <div class="timeline-item active" data-step="1">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Appointment Completed</h3>
                            <p class="timeline-description">Your consultation with the doctor has been successfully completed.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="2">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Doctor Adds Prescription</h3>
                            <p class="timeline-description">Your doctor has added a digital prescription to your medical records.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="3">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M10.5 13H8v-3h2.5V7.5h3V10H16v3h-2.5v2.5h-3V13zM12 2L4 7v1c0 2.55 1.92 4.63 4 5.24V15h2v2h2v-2h2v-1.76c2.08-.61 4-2.69 4-5.24V7l-8-5z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Medicines & Dosage</h3>
                            <p class="timeline-description">Detailed information about prescribed medicines with dosage instructions.</p>
                        </div>
                        <div class="timeline-arrow"></div>
                    </div>
                    
                    <div class="timeline-item" data-step="4">
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">Patient Views Prescription</h3>
                            <p class="timeline-description">Access your prescription anytime through your patient portal.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medicine & Appointment Reminders Expanded View -->
            <div id="remindersView" class="service-view" style="display: none;">
                <button class="back-button" onclick="closeExpandedView()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back to Services
                </button>
                
                <div class="expanded-header">
                    <div class="expanded-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                    </div>
                    <h2 class="expanded-title">Medicine & Appointment Reminders</h2>
                </div>
                
                <div class="notification-flow">
                    <div class="notification-item active" data-step="1">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3 class="notification-title">Appointment / Prescription</h3>
                            <p class="notification-description">Your appointment or prescription details are recorded in the system.</p>
                        </div>
                        <div class="notification-arrow">→</div>
                    </div>
                    
                    <div class="notification-item" data-step="2">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3 class="notification-title">Reminder Generated</h3>
                            <p class="notification-description">System automatically generates reminders based on your schedule.</p>
                        </div>
                        <div class="notification-arrow">→</div>
                    </div>
                    
                    <div class="notification-item" data-step="3">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3 class="notification-title">Notification Sent</h3>
                            <p class="notification-description">You receive timely notifications via SMS, email, or app alerts.</p>
                        </div>
                        <div class="notification-arrow">→</div>
                    </div>
                    
                    <div class="notification-item" data-step="4">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3 class="notification-title">Reminder Completed</h3>
                            <p class="notification-description">You've successfully taken your medicine or attended your appointment.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback & Support Expanded View -->
            <div id="feedbackView" class="service-view" style="display: none;">
                <button class="back-button" onclick="closeExpandedView()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back to Services
                </button>
                
                <div class="expanded-header">
                    <div class="expanded-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                        </svg>
                    </div>
                    <h2 class="expanded-title">Feedback & Support</h2>
                </div>
                
                <div class="rating-flow">
                    <div class="rating-item active" data-step="1">
                        <div class="rating-header">
                            <div class="rating-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                            </div>
                            <h3 class="rating-title">Completed Appointment</h3>
                        </div>
                        <p class="timeline-description">Your appointment has been completed successfully.</p>
                    </div>
                    
                    <div class="rating-item" data-step="2">
                        <div class="rating-header">
                            <div class="rating-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </div>
                            <h3 class="rating-title">Give Star Rating</h3>
                        </div>
                        <div class="rating-content">
                            <div class="star-rating">
                                <span class="star" data-rating="1">★</span>
                                <span class="star" data-rating="2">★</span>
                                <span class="star" data-rating="3">★</span>
                                <span class="star" data-rating="4">★</span>
                                <span class="star" data-rating="5">★</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rating-item" data-step="3">
                        <div class="rating-header">
                            <div class="rating-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                </svg>
                            </div>
                            <h3 class="rating-title">Write Comment</h3>
                        </div>
                        <div class="rating-content">
                            <textarea class="comment-box" rows="4" placeholder="Share your experience..."></textarea>
                        </div>
                    </div>
                    
                    <div class="rating-item" data-step="4">
                        <div class="rating-header">
                            <div class="rating-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                            </div>
                            <h3 class="rating-title">Submit Feedback</h3>
                        </div>
                        <div class="rating-content">
                            <button class="submit-button">Submit Your Feedback</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set up Intersection Observer for scroll animations
            setupScrollAnimations();
            
            // Add click event listeners to service cards
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach(card => {
                card.addEventListener('click', function() {
                    const serviceType = this.getAttribute('data-service');
                    openExpandedView(serviceType);
                });
            });
            
            // Add click event listeners to steps in different views
            addStepClickListeners();
            
            // Add star rating functionality
            addStarRatingFunctionality();
        });

        // Set up scroll animations using Intersection Observer
        function setupScrollAnimations() {
            const serviceCards = document.querySelectorAll('.service-card');
            
            const observerOptions = {
                threshold: 0.2,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        const card = entry.target;
                        const cardIndex = Array.from(serviceCards).indexOf(card);
                        
                        // Add animation class based on card index
                        setTimeout(() => {
                            if (cardIndex % 2 === 0) {
                                card.classList.add('slide-in-left');
                            } else {
                                card.classList.add('slide-in-right');
                            }
                        }, index * 100);
                        
                        // Stop observing this card once animated
                        observer.unobserve(card);
                    }
                });
            }, observerOptions);
            
            // Start observing each service card
            serviceCards.forEach(card => {
                observer.observe(card);
            });
        }

        // Open expanded view for a specific service
        function openExpandedView(serviceType) {
            // Show expanded service view
            const expandedService = document.getElementById('expandedService');
            expandedService.style.display = 'block';
            
            // Hide all service views
            const serviceViews = document.querySelectorAll('.service-view');
            serviceViews.forEach(view => {
                view.style.display = 'none';
            });
            
            // Show the specific service view
            const viewId = serviceType + 'View';
            const specificView = document.getElementById(viewId);
            if (specificView) {
                specificView.style.display = 'block';
                
                // Reset active steps
                resetActiveSteps(specificView);
                
                // Set first step as active
                const firstStep = specificView.querySelector('[data-step="1"]');
                if (firstStep) {
                    firstStep.classList.add('active');
                }
            }
        }

        // Close expanded view and return to services grid
        function closeExpandedView() {
            // Hide expanded service view
            const expandedService = document.getElementById('expandedService');
            expandedService.style.display = 'none';
        }

        // Reset active steps in a view
        function resetActiveSteps(view) {
            const steps = view.querySelectorAll('[data-step]');
            steps.forEach(step => {
                step.classList.remove('active');
            });
        }

        // Add click event listeners to steps in different views
        function addStepClickListeners() {
            // Timeline items
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    timelineItems.forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });
            
            // Notification items
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    notificationItems.forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });
            
            // Rating items
            const ratingItems = document.querySelectorAll('.rating-item');
            ratingItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    ratingItems.forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });
        }

        // Add star rating functionality
        function addStarRatingFunctionality() {
            const stars = document.querySelectorAll('.star');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    
                    // Remove filled class from all stars
                    stars.forEach(s => s.classList.remove('filled'));
                    
                    // Add filled class to stars up to the clicked rating
                    for (let i = 0; i < rating; i++) {
                        stars[i].classList.add('filled');
                    }
                });
                
                // Add hover effect
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    
                    // Remove filled class from all stars
                    stars.forEach(s => s.classList.remove('filled'));
                    
                    // Add filled class to stars up to the hovered rating
                    for (let i = 0; i < rating; i++) {
                        stars[i].classList.add('filled');
                    }
                });
            });
            
            // Reset stars when mouse leaves the star rating container
            const starRatingContainers = document.querySelectorAll('.star-rating');
            starRatingContainers.forEach(container => {
                container.addEventListener('mouseleave', function() {
                    // Keep the current rating (if any)
                    const filledStars = this.querySelectorAll('.star.filled');
                    if (filledStars.length === 0) {
                        // If no stars are filled, remove filled class from all
                        const stars = this.querySelectorAll('.star');
                        stars.forEach(s => s.classList.remove('filled'));
                    }
                });
            });
        }
    </script>
</body>
</html>