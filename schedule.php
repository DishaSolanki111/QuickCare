<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule - Hospital Patient Appointment Booking System</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
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

        /* Doctor Schedule Section */
        .doctor-schedule-section {
            padding: 40px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title {
            color: #2c6ecb;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .section-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* Filters */
        .filters-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .filter-button {
            background-color: #2c6ecb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 24px;
        }

        .filter-button:hover {
            background-color: #1e4d8f;
        }

        /* Doctor Cards Grid */
        .doctor-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Doctor Card */
        .doctor-card {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .doctor-card.fade-in {
            animation: fadeIn 0.5s ease-out forwards;
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

        /* Doctor Info */
        .doctor-info {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .doctor-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e6f0ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            overflow: hidden;
        }

        .doctor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doctor-image svg {
            width: 40px;
            height: 40px;
            fill: #2c6ecb;
        }

        .doctor-details h3 {
            color: #2c6ecb;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .doctor-specialization {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Schedule Section */
        .schedule-section {
            padding: 20px;
        }

        .days-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            overflow-x: auto;
        }

        .day {
            text-align: center;
            min-width: 40px;
            font-weight: 600;
            color: #333;
        }

        .time-slots-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .time-slot {
            min-width: 40px;
            height: 40px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .time-slot.available {
            background-color: #d4edda;
            color: #155724;
        }

        .time-slot.available:hover {
            background-color: #c3e6cb;
            transform: scale(1.05);
        }

        .time-slot.fully-booked {
            background-color: #fff3cd;
            color: #856404;
            cursor: not-allowed;
        }

        .time-slot.not-available {
            background-color: #e2e3e5;
            color: #6c757d;
            cursor: not-allowed;
        }

        .time-slot.selected {
            border: 2px solid #2c6ecb;
            box-shadow: 0 0 5px rgba(44, 110, 203, 0.3);
        }

        /* Tooltip */
        .tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 10;
        }

        .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .time-slot:hover .tooltip {
            opacity: 1;
        }

        /* Book Button */
        .book-button-container {
            padding: 0 20px 20px;
        }

        .book-button {
            width: 100%;
            background-color: #2c6ecb;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .book-button:hover {
            background-color: #1e4d8f;
        }

        .book-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .empty-state svg {
            width: 60px;
            height: 60px;
            fill: #6c757d;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .spinner {
            border: 4px solid rgba(44, 110, 203, 0.1);
            border-radius: 50%;
            border-top: 4px solid #2c6ecb;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .doctor-cards-container {
                grid-template-columns: 1fr;
            }

            .filters-container {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .days-container {
                padding-bottom: 10px;
            }

            .time-slots-container {
                padding-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Quick Care Hospital</h1>
            <p class="subtitle">Your Health, Our Priority</p>
        </div>
    </header>

    <main class="container">
        <section class="doctor-schedule-section">
            <div class="section-header">
                <h2 class="section-title">Doctor Availability & Schedule</h2>
                <p class="section-subtitle">View doctor schedules and book appointments based on availability.</p>
            </div>

            <!-- Filters -->
            <div class="filters-container">
                <div class="filter-group">
                    <label class="filter-label" for="specialization-filter">Specialization</label>
                    <select id="specialization-filter" class="filter-input">
                        <option value="">All Specializations</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label" for="date-filter">Date</label>
                    <input type="date" id="date-filter" class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label" for="availability-filter">Availability</label>
                    <select id="availability-filter" class="filter-input">
                        <option value="">All</option>
                        <option value="available">Available</option>
                        <option value="fully-booked">Fully Booked</option>
                        <option value="not-available">Not Available</option>
                    </select>
                </div>
                <button id="apply-filters" class="filter-button">Apply Filters</button>
            </div>

            <!-- Doctor Cards Container -->
            <div id="doctor-cards-container" class="doctor-cards-container">
                <!-- Loading State -->
                <div id="loading-state" class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading doctor schedules...</p>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the application
            init();
        });

        // Initialize function
        function init() {
            // Set today's date as default in date filter
            const dateFilter = document.getElementById('date-filter');
            const today = new Date().toISOString().split('T')[0];
            dateFilter.value = today;

            // Load specializations
            loadSpecializations();

            // Load doctor schedules
            loadDoctorSchedules();

            // Add event listeners
            document.getElementById('apply-filters').addEventListener('click', applyFilters);
        }

        // Load specializations from database
        function loadSpecializations() {
            // This would typically be an API call to your backend
            // For demonstration, we'll use the data from your database schema
            const specializations = [
                { id: 1, name: 'Pediatrician' },
                { id: 2, name: 'Cardiologist' },
                { id: 3, name: 'Orthopedics' },
                { id: 4, name: 'Neurologist' }
            ];

            const specializationFilter = document.getElementById('specialization-filter');
            specializations.forEach(spec => {
                const option = document.createElement('option');
                option.value = spec.id;
                option.textContent = spec.name;
                specializationFilter.appendChild(option);
            });
        }

        // Load doctor schedules from database
        function loadDoctorSchedules() {
            // Show loading state
            const container = document.getElementById('doctor-cards-container');
            container.innerHTML = `
                <div id="loading-state" class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading doctor schedules...</p>
                </div>
            `;

            // Simulate API call to get doctor data
            // In a real implementation, this would be a fetch/axios call to your backend
            setTimeout(() => {
                // Mock data based on your database schema
                const doctors = [
                    {
                        id: 1,
                        name: 'Dr. Rajesh Kumar',
                        specialization: 'Pediatrician',
                        image: 'doctor1.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '9-11', status: 'available' },
                            { day: 'TUE', timeRange: '', status: 'not-available' },
                            { day: 'WED', timeRange: '9-11', status: 'available' },
                            { day: 'THU', timeRange: '', status: 'not-available' },
                            { day: 'FRI', timeRange: '9-11', status: 'fully-booked' },
                            { day: 'SAT', timeRange: '', status: 'not-available' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 2,
                        name: 'Dr. Priya Sharma',
                        specialization: 'Pediatrician',
                        image: 'doctor2.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '', status: 'not-available' },
                            { day: 'TUE', timeRange: '10-12', status: 'available' },
                            { day: 'WED', timeRange: '', status: 'not-available' },
                            { day: 'THU', timeRange: '10-12', status: 'available' },
                            { day: 'FRI', timeRange: '', status: 'not-available' },
                            { day: 'SAT', timeRange: '10-12', status: 'fully-booked' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 3,
                        name: 'Dr. Amit Patel',
                        specialization: 'Pediatrician',
                        image: 'doctor3.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '8-10', status: 'available' },
                            { day: 'TUE', timeRange: '', status: 'not-available' },
                            { day: 'WED', timeRange: '8-10', status: 'available' },
                            { day: 'THU', timeRange: '', status: 'not-available' },
                            { day: 'FRI', timeRange: '8-10', status: 'available' },
                            { day: 'SAT', timeRange: '', status: 'not-available' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 4,
                        name: 'Dr. Sunita Reddy',
                        specialization: 'Cardiologist',
                        image: 'doctor4.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '', status: 'not-available' },
                            { day: 'TUE', timeRange: '9-11', status: 'available' },
                            { day: 'WED', timeRange: '', status: 'not-available' },
                            { day: 'THU', timeRange: '9-11', status: 'available' },
                            { day: 'FRI', timeRange: '', status: 'not-available' },
                            { day: 'SAT', timeRange: '9-11', status: 'fully-booked' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 5,
                        name: 'Dr. Vikram Singh',
                        specialization: 'Cardiologist',
                        image: 'doctor5.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '10-12', status: 'available' },
                            { day: 'TUE', timeRange: '', status: 'not-available' },
                            { day: 'WED', timeRange: '10-12', status: 'available' },
                            { day: 'THU', timeRange: '', status: 'not-available' },
                            { day: 'FRI', timeRange: '10-12', status: 'available' },
                            { day: 'SAT', timeRange: '', status: 'not-available' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 6,
                        name: 'Dr. Anjali Gupta',
                        specialization: 'Cardiologist',
                        image: 'doctor6.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '', status: 'not-available' },
                            { day: 'TUE', timeRange: '8-10', status: 'available' },
                            { day: 'WED', timeRange: '', status: 'not-available' },
                            { day: 'THU', timeRange: '8-10', status: 'available' },
                            { day: 'FRI', timeRange: '', status: 'not-available' },
                            { day: 'SAT', timeRange: '8-10', status: 'fully-booked' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 7,
                        name: 'Dr. Rahul Verma',
                        specialization: 'Orthopedics',
                        image: 'doctor7.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '9-11', status: 'available' },
                            { day: 'TUE', timeRange: '', status: 'not-available' },
                            { day: 'WED', timeRange: '9-11', status: 'available' },
                            { day: 'THU', timeRange: '', status: 'not-available' },
                            { day: 'FRI', timeRange: '9-11', status: 'available' },
                            { day: 'SAT', timeRange: '', status: 'not-available' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 8,
                        name: 'Dr. Meera Joshi',
                        specialization: 'Orthopedics',
                        image: 'doctor8.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '', status: 'not-available' },
                            { day: 'TUE', timeRange: '10-12', status: 'available' },
                            { day: 'WED', timeRange: '', status: 'not-available' },
                            { day: 'THU', timeRange: '10-12', status: 'available' },
                            { day: 'FRI', timeRange: '', status: 'not-available' },
                            { day: 'SAT', timeRange: '10-12', status: 'fully-booked' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 9,
                        name: 'Dr. Sanjay Malhotra',
                        specialization: 'Neurologist',
                        image: 'doctor9.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '8-10', status: 'available' },
                            { day: 'TUE', timeRange: '', status: 'not-available' },
                            { day: 'WED', timeRange: '8-10', status: 'available' },
                            { day: 'THU', timeRange: '', status: 'not-available' },
                            { day: 'FRI', timeRange: '8-10', status: 'available' },
                            { day: 'SAT', timeRange: '', status: 'not-available' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    },
                    {
                        id: 10,
                        name: 'Dr. Kavita Nair',
                        specialization: 'Neurologist',
                        image: 'doctor10.jpg',
                        schedule: [
                            { day: 'MON', timeRange: '', status: 'not-available' },
                            { day: 'TUE', timeRange: '9-11', status: 'available' },
                            { day: 'WED', timeRange: '', status: 'not-available' },
                            { day: 'THU', timeRange: '9-11', status: 'available' },
                            { day: 'FRI', timeRange: '', status: 'not-available' },
                            { day: 'SAT', timeRange: '9-11', status: 'fully-booked' },
                            { day: 'SUN', timeRange: '', status: 'not-available' }
                        ]
                    }
                ];

                // Render doctor cards
                renderDoctorCards(doctors);
            }, 1000);
        }

        // Render doctor cards
        function renderDoctorCards(doctors) {
            const container = document.getElementById('doctor-cards-container');
            
            // Clear container
            container.innerHTML = '';

            // Check if there are doctors
            if (doctors.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <h3>No doctors available for selected date.</h3>
                        <p>Please try a different date or filter.</p>
                    </div>
                `;
                return;
            }

            // Create and append doctor cards
            doctors.forEach((doctor, index) => {
                const card = createDoctorCard(doctor);
                container.appendChild(card);

                // Add fade-in animation with delay
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });
        }

        // Create a doctor card element
        function createDoctorCard(doctor) {
            const card = document.createElement('div');
            card.className = 'doctor-card';
            card.dataset.doctorId = doctor.id;
            card.dataset.specialization = doctor.specialization;

            // Create doctor info section
            const doctorInfo = document.createElement('div');
            doctorInfo.className = 'doctor-info';

            // Create doctor image
            const doctorImage = document.createElement('div');
            doctorImage.className = 'doctor-image';
            
            // If image is available, use it, otherwise use a placeholder
            if (doctor.image) {
                const img = document.createElement('img');
                img.src = `images/${doctor.image}`;
                img.alt = doctor.name;
                doctorImage.appendChild(img);
            } else {
                // Create SVG placeholder
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('width', '40');
                svg.setAttribute('height', '40');
                
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z');
                
                svg.appendChild(path);
                doctorImage.appendChild(svg);
            }

            // Create doctor details
            const doctorDetails = document.createElement('div');
            doctorDetails.className = 'doctor-details';
            
            const doctorName = document.createElement('h3');
            doctorName.textContent = doctor.name;
            
            const doctorSpecialization = document.createElement('div');
            doctorSpecialization.className = 'doctor-specialization';
            doctorSpecialization.textContent = doctor.specialization;
            
            doctorDetails.appendChild(doctorName);
            doctorDetails.appendChild(doctorSpecialization);

            doctorInfo.appendChild(doctorImage);
            doctorInfo.appendChild(doctorDetails);

            // Create schedule section
            const scheduleSection = document.createElement('div');
            scheduleSection.className = 'schedule-section';

            // Create days container
            const daysContainer = document.createElement('div');
            daysContainer.className = 'days-container';

            // Create time slots container
            const timeSlotsContainer = document.createElement('div');
            timeSlotsContainer.className = 'time-slots-container';

            // Add days and time slots
            doctor.schedule.forEach(daySchedule => {
                // Add day
                const day = document.createElement('div');
                day.className = 'day';
                day.textContent = daySchedule.day;
                daysContainer.appendChild(day);

                // Add time slot
                const timeSlot = document.createElement('div');
                timeSlot.className = `time-slot ${daySchedule.status}`;
                timeSlot.dataset.day = daySchedule.day;
                timeSlot.dataset.status = daySchedule.status;
                
                // Add time range if available
                if (daySchedule.timeRange) {
                    timeSlot.textContent = daySchedule.timeRange;
                    
                    // Add tooltip with exact time
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = `Available: ${daySchedule.timeRange}`;
                    timeSlot.appendChild(tooltip);
                } else {
                    timeSlot.textContent = '❌';
                }

                // Add click event for available slots
                if (daySchedule.status === 'available') {
                    timeSlot.addEventListener('click', function() {
                        selectTimeSlot(this, doctor);
                    });
                }

                timeSlotsContainer.appendChild(timeSlot);
            });

            scheduleSection.appendChild(daysContainer);
            scheduleSection.appendChild(timeSlotsContainer);

            // Create book button
            const bookButtonContainer = document.createElement('div');
            bookButtonContainer.className = 'book-button-container';

            const bookButton = document.createElement('button');
            bookButton.className = 'book-button';
            bookButton.textContent = 'Book Appointment';
            bookButton.disabled = true;
            bookButton.dataset.doctorId = doctor.id;

            bookButton.addEventListener('click', function() {
                bookAppointment(doctor);
            });

            bookButtonContainer.appendChild(bookButton);

            // Assemble the card
            card.appendChild(doctorInfo);
            card.appendChild(scheduleSection);
            card.appendChild(bookButtonContainer);

            return card;
        }

        // Select a time slot
        function selectTimeSlot(slot, doctor) {
            // Remove selected class from all slots in this card
            const card = slot.closest('.doctor-card');
            const allSlots = card.querySelectorAll('.time-slot');
            allSlots.forEach(s => s.classList.remove('selected'));

            // Add selected class to clicked slot
            slot.classList.add('selected');

            // Enable book button
            const bookButton = card.querySelector('.book-button');
            bookButton.disabled = false;

            // Store selected slot data
            bookButton.dataset.day = slot.dataset.day;
            bookButton.dataset.time = slot.textContent;
        }

        // Book appointment
        function bookAppointment(doctor) {
            // In a real implementation, this would navigate to the appointment booking page
            // with the selected doctor and time slot information
            
            // For demonstration, we'll show an alert
            const bookButton = document.querySelector(`.book-button[data-doctor-id="${doctor.id}"]`);
            const day = bookButton.dataset.day;
            const time = bookButton.dataset.time;
            
            alert(`Booking appointment with ${doctor.name} on ${day} at ${time}. In a real application, this would redirect to the appointment booking page.`);
        }

        // Apply filters
        function applyFilters() {
            const specializationFilter = document.getElementById('specialization-filter').value;
            const dateFilter = document.getElementById('date-filter').value;
            const availabilityFilter = document.getElementById('availability-filter').value;

            // In a real implementation, this would make an API call with filter parameters
            // For demonstration, we'll just reload the data
            loadDoctorSchedules();
        }
    </script>
</body>
</html>