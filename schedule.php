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

        .doctor-education {
            color: #888;
            font-size: 0.8rem;
            margin-top: 3px;
        }

        /* Schedule Section */
        .schedule-section {
            padding: 20px;
        }

        /* Date Selection */
        .date-selection {
            margin-bottom: 20px;
        }

        .date-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #333;
        }

        .date-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .date-option {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .date-option:hover {
            border-color: #2c6ecb;
            background-color: #f0f7ff;
        }

        .date-option.selected {
            border-color: #2c6ecb;
            background-color: #2c6ecb;
            color: white;
        }

        .date-option.disabled {
            cursor: not-allowed;
            opacity: 0.5;
            background-color: #f5f5f5;
        }

        /* Days Container */
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

        /* Time Slots Container */
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

        /* Appointment Confirmation Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: #ffffff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .modal-title {
            color: #2c6ecb;
            font-size: 1.5rem;
        }

        .close-button {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close-button:hover {
            color: #333;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .appointment-details {
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            width: 120px;
            color: #333;
        }

        .detail-value {
            color: #555;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #2c6ecb;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1e4d8f;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Success Message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
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

            .modal-content {
                margin: 20% auto;
                width: 95%;
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

    <!-- Appointment Confirmation Modal -->
    <div id="appointment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Appointment</h3>
                <button class="close-button" id="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="success-message" id="success-message">
                    Your appointment has been booked successfully!
                </div>
                <div class="error-message" id="error-message">
                    Error booking appointment. Please try again.
                </div>
                
                <div class="appointment-details" id="appointment-details">
                    <div class="detail-row">
                        <div class="detail-label">Doctor:</div>
                        <div class="detail-value" id="modal-doctor-name"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Specialization:</div>
                        <div class="detail-value" id="modal-specialization"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Date:</div>
                        <div class="detail-value" id="modal-date"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Time:</div>
                        <div class="detail-value" id="modal-time"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="patient-name">Your Name *</label>
                    <input type="text" id="patient-name" class="form-input" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="patient-phone">Phone Number *</label>
                    <input type="tel" id="patient-phone" class="form-input" placeholder="Enter your phone number" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="patient-email">Email Address *</label>
                    <input type="email" id="patient-email" class="form-input" placeholder="Enter your email address" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="reason">Reason for Visit</label>
                    <textarea id="reason" class="form-textarea" placeholder="Describe your symptoms or reason for visit"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancel-appointment">Cancel</button>
                <button class="btn btn-primary" id="confirm-appointment">Confirm Booking</button>
            </div>
        </div>
    </div>

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
            
            // Modal event listeners
            document.getElementById('close-modal').addEventListener('click', closeModal);
            document.getElementById('cancel-appointment').addEventListener('click', closeModal);
            document.getElementById('confirm-appointment').addEventListener('click', confirmAppointment);
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('appointment-modal');
                if (event.target === modal) {
                    closeModal();
                }
            });
        }

        // Load specializations from database
        async function loadSpecializations() {
            try {
                const response = await fetch('get_specializations.php');
                const result = await response.json();
                
                if (result.success) {
                    const specializationFilter = document.getElementById('specialization-filter');
                    result.data.forEach(spec => {
                        const option = document.createElement('option');
                        option.value = spec.SPECIALISATION_ID;
                        option.textContent = spec.SPECIALISATION_NAME;
                        specializationFilter.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading specializations:', error);
            }
        }

        // Load doctor schedules from database
        async function loadDoctorSchedules() {
            // Show loading state
            const container = document.getElementById('doctor-cards-container');
            container.innerHTML = `
                <div id="loading-state" class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading doctor schedules...</p>
                </div>
            `;

            try {
                // Get filter values
                const specialization = document.getElementById('specialization-filter').value;
                const date = document.getElementById('date-filter').value;
                const availability = document.getElementById('availability-filter').value;

                // Build query string
                const params = new URLSearchParams();
                if (specialization) params.append('specialization', specialization);
                if (date) params.append('date', date);
                if (availability) params.append('availability', availability);

                // Fetch data from backend
                const response = await fetch(`get_doctors.php?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    renderDoctorCards(result.data);
                } else {
                    showError('Failed to load doctor schedules');
                }
            } catch (error) {
                console.error('Error loading doctor schedules:', error);
                showError('Error loading data. Please try again.');
            }
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
                        <h3>No doctors available for selected criteria.</h3>
                        <p>Please try different filters.</p>
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
                img.src = `uploads/${doctor.image}`;
                img.alt = doctor.name;
                img.onerror = function() {
                    this.style.display = 'none';
                    this.parentElement.innerHTML = createDoctorPlaceholder();
                };
                doctorImage.appendChild(img);
            } else {
                doctorImage.innerHTML = createDoctorPlaceholder();
            }

            // Create doctor details
            const doctorDetails = document.createElement('div');
            doctorDetails.className = 'doctor-details';
            
            const doctorName = document.createElement('h3');
            doctorName.textContent = doctor.name;
            
            const doctorSpecialization = document.createElement('div');
            doctorSpecialization.className = 'doctor-specialization';
            doctorSpecialization.textContent = doctor.specialization;
            
            const doctorEducation = document.createElement('div');
            doctorEducation.className = 'doctor-education';
            doctorEducation.textContent = doctor.education || '';
            
            doctorDetails.appendChild(doctorName);
            doctorDetails.appendChild(doctorSpecialization);
            if (doctor.education) {
                doctorDetails.appendChild(doctorEducation);
            }

            doctorInfo.appendChild(doctorImage);
            doctorInfo.appendChild(doctorDetails);

            // Create schedule section
            const scheduleSection = document.createElement('div');
            scheduleSection.className = 'schedule-section';

            // Create date selection
            const dateSelection = document.createElement('div');
            dateSelection.className = 'date-selection';
            
            const dateLabel = document.createElement('div');
            dateLabel.className = 'date-label';
            dateLabel.textContent = 'Select Available Date:';
            
            const dateContainer = document.createElement('div');
            dateContainer.className = 'date-container';
            
            // Add available dates
            if (doctor.availableDates && doctor.availableDates.length > 0) {
                doctor.availableDates.forEach(date => {
                    const dateOption = document.createElement('div');
                    dateOption.className = 'date-option';
                    dateOption.textContent = formatDate(date);
                    dateOption.dataset.date = date;
                    
                    // Check if date is in the past
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const selectedDate = new Date(date);
                    
                    if (selectedDate < today) {
                        dateOption.classList.add('disabled');
                    } else {
                        dateOption.addEventListener('click', function() {
                            selectDate(this, doctor);
                        });
                    }
                    
                    dateContainer.appendChild(dateOption);
                });
            } else {
                dateContainer.innerHTML = '<p style="color: #999;">No available dates</p>';
            }
            
            dateSelection.appendChild(dateLabel);
            dateSelection.appendChild(dateContainer);

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

            scheduleSection.appendChild(dateSelection);
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
                openAppointmentModal(doctor);
            });

            bookButtonContainer.appendChild(bookButton);

            // Assemble the card
            card.appendChild(doctorInfo);
            card.appendChild(scheduleSection);
            card.appendChild(bookButtonContainer);

            return card;
        }

        // Create doctor placeholder SVG
        function createDoctorPlaceholder() {
            return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40" height="40">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>`;
        }

        // Format date to a more readable format
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Select a date
        function selectDate(dateOption, doctor) {
            // Remove selected class from all date options in this card
            const card = dateOption.closest('.doctor-card');
            const allDateOptions = card.querySelectorAll('.date-option');
            allDateOptions.forEach(d => d.classList.remove('selected'));

            // Add selected class to clicked date
            dateOption.classList.add('selected');

            // Store selected date
            const bookButton = card.querySelector('.book-button');
            bookButton.dataset.date = dateOption.dataset.date;

            // Check if a time slot is already selected
            const selectedSlot = card.querySelector('.time-slot.selected');
            if (selectedSlot) {
                bookButton.disabled = false;
            }
        }

        // Select a time slot
        function selectTimeSlot(slot, doctor) {
            // Remove selected class from all slots in this card
            const card = slot.closest('.doctor-card');
            const allSlots = card.querySelectorAll('.time-slot');
            allSlots.forEach(s => s.classList.remove('selected'));

            // Add selected class to clicked slot
            slot.classList.add('selected');

            // Store selected slot data
            const bookButton = card.querySelector('.book-button');
            bookButton.dataset.day = slot.dataset.day;
            bookButton.dataset.time = slot.textContent;

            // Check if a date is already selected
            const selectedDate = card.querySelector('.date-option.selected');
            if (selectedDate) {
                bookButton.disabled = false;
            }
        }

        // Open appointment modal
        function openAppointmentModal(doctor) {
            const bookButton = document.querySelector(`.book-button[data-doctor-id="${doctor.id}"]`);
            const date = bookButton.dataset.date;
            const day = bookButton.dataset.day;
            const time = bookButton.dataset.time;
            
            // Set modal content
            document.getElementById('modal-doctor-name').textContent = doctor.name;
            document.getElementById('modal-specialization').textContent = doctor.specialization;
            document.getElementById('modal-date').textContent = formatDate(date);
            document.getElementById('modal-time').textContent = time;
            
            // Reset form
            document.getElementById('patient-name').value = '';
            document.getElementById('patient-phone').value = '';
            document.getElementById('patient-email').value = '';
            document.getElementById('reason').value = '';
            
            // Hide messages
            document.getElementById('success-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('appointment-details').style.display = 'block';
            document.querySelectorAll('.form-group').forEach(el => el.style.display = 'block');
            document.getElementById('modal-footer').style.display = 'flex';
            
            // Show modal
            document.getElementById('appointment-modal').style.display = 'block';
            
            // Store appointment data
            document.getElementById('confirm-appointment').dataset.doctorId = doctor.id;
            document.getElementById('confirm-appointment').dataset.date = date;
            document.getElementById('confirm-appointment').dataset.day = day;
            document.getElementById('confirm-appointment').dataset.time = time;
        }

        // Close modal
        function closeModal() {
            document.getElementById('appointment-modal').style.display = 'none';
        }

        // Confirm appointment
        async function confirmAppointment() {
            const doctorId = document.getElementById('confirm-appointment').dataset.doctorId;
            const date = document.getElementById('confirm-appointment').dataset.date;
            const day = document.getElementById('confirm-appointment').dataset.day;
            const time = document.getElementById('confirm-appointment').dataset.time;
            
            const patientName = document.getElementById('patient-name').value;
            const patientPhone = document.getElementById('patient-phone').value;
            const patientEmail = document.getElementById('patient-email').value;
            const reason = document.getElementById('reason').value;
            
            // Validate form
            if (!patientName || !patientPhone || !patientEmail) {
                showError('Please fill in all required fields.');
                return;
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(patientEmail)) {
                showError('Please enter a valid email address.');
                return;
            }
            
            // Validate phone
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(patientPhone.replace(/\D/g, ''))) {
                showError('Please enter a valid 10-digit phone number.');
                return;
            }
            
            try {
                // Create form data
                const formData = new FormData();
                formData.append('doctor_id', doctorId);
                formData.append('date', date);
                formData.append('day', day);
                formData.append('time', time);
                formData.append('patient_name', patientName);
                formData.append('patient_phone', patientPhone);
                formData.append('patient_email', patientEmail);
                formData.append('reason', reason);
                
                // Send booking request
                const response = await fetch('book_appointment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    document.getElementById('success-message').style.display = 'block';
                    document.getElementById('error-message').style.display = 'none';
                    
                    // Hide form
                    document.getElementById('appointment-details').style.display = 'none';
                    document.querySelectorAll('.form-group').forEach(el => el.style.display = 'none');
                    document.getElementById('modal-footer').style.display = 'none';
                    
                    // Close modal after 3 seconds
                    setTimeout(() => {
                        closeModal();
                        // Reset modal for next use
                        document.getElementById('appointment-details').style.display = 'block';
                        document.querySelectorAll('.form-group').forEach(el => el.style.display = 'block');
                        document.getElementById('modal-footer').style.display = 'flex';
                        
                        // Reload doctor schedules to update availability
                        loadDoctorSchedules();
                    }, 3000);
                } else {
                    showError(result.message || 'Failed to book appointment');
                }
            } catch (error) {
                console.error('Error booking appointment:', error);
                showError('Error booking appointment. Please try again.');
            }
        }

        // Show error message
        function showError(message) {
            const errorElement = document.getElementById('error-message');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Hide after 5 seconds
            setTimeout(() => {
                errorElement.style.display = 'none';
            }, 5000);
        }

        // Apply filters
        function applyFilters() {
            loadDoctorSchedules();
        }
    </script>
</body>
</html>