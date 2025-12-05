<?php
@include 'config.php';

 $doctor_id = $_GET['doctor_id'] ?? 0;

// Get doctor information
 $stmt = mysqli_prepare($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
 $doctor_result = mysqli_stmt_get_result($stmt);
 $doctor = mysqli_fetch_assoc($doctor_result);

// Get existing appointments for this doctor
 $stmt = mysqli_prepare($conn, "
    SELECT APPOINTMENT_DATE, APPOINTMENT_TIME 
    FROM appointment_tbl 
    WHERE DOCTOR_ID = ? AND status = 'confirmed'
    AND APPOINTMENT_DATE >= CURDATE()
");
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
 $appointments_result = mysqli_stmt_get_result($stmt);

 $booked_slots = [];
while ($row = mysqli_fetch_assoc($appointments_result)) {
    $date = $row['APPOINTMENT_DATE'];
    $time = $row['APPOINTMENT_TIME'];
    
    if (!isset($booked_slots[$date])) {
        $booked_slots[$date] = [];
    }
    $booked_slots[$date][] = $time;
}
?>

    
    

<script>
// Calendar functionality
const today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let selectedDate = null;
let selectedTime = null;

const doctorId = <?php echo $doctor_id; ?>;
const bookedSlots = <?php echo json_encode($booked_slots); ?>;

// Define available time slots
const availableTimeSlots = [
    "09:00", "09:30", "10:00", "10:30",
    "11:00", "11:30", "14:00", "14:30",
    "15:00", "15:30", "16:00", "16:30"
];

// Month names
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    
    // Event listeners for navigation
    document.getElementById('prevMonth').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    });
    
    // Confirm booking button
    document.getElementById('confirmBooking').addEventListener('click', function() {
        confirmAppointment();
    });
});

function renderCalendar() {
    // Update month display
    document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    // Clear calendar
    const calendar = document.getElementById('calendar');
    calendar.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendar.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    // Add previous month's trailing days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = document.createElement('div');
        day.className = 'calendar-day other-month';
        day.textContent = daysInPrevMonth - i;
        calendar.appendChild(day);
    }
    
    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = day;
        
        // Format date as YYYY-MM-DD for comparison
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        dayElement.dataset.date = dateStr;
        
        // Check if today
        const currentDate = new Date();
        if (currentYear === currentDate.getFullYear() && 
            currentMonth === currentDate.getMonth() && 
            day === currentDate.getDate()) {
            dayElement.classList.add('today');
        }
        
        // Check if past date
        const cellDate = new Date(currentYear, currentMonth, day);
        if (cellDate < currentDate && !isSameDay(cellDate, currentDate)) {
            dayElement.classList.add('unavailable');
        } 
        // Check if fully booked
        else if (bookedSlots[dateStr] && bookedSlots[dateStr].length >= availableTimeSlots.length) {
            dayElement.classList.add('unavailable');
        } 
        // Otherwise it's available
        else {
            dayElement.classList.add('available');
            dayElement.addEventListener('click', function() {
                selectDate(dateStr, this);
            });
        }
        
        calendar.appendChild(dayElement);
    }
    
    // Add next month's leading days
    const totalCells = calendar.children.length - 7; // Subtract header row
    const remainingCells = 35 - totalCells; // 5 weeks * 7 days
    for (let day = 1; day <= remainingCells; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day other-month';
        dayElement.textContent = day;
        calendar.appendChild(dayElement);
    }
}

function selectDate(dateStr, element) {
    // Remove previous selection
    document.querySelectorAll('.calendar-day.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked date
    element.classList.add('selected');
    selectedDate = dateStr;
    
    // Show time slots
    showTimeSlots(dateStr);
}

function showTimeSlots(dateStr) {
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const timeSlots = document.getElementById('timeSlots');
    const reasonContainer = document.getElementById('reasonContainer');
    
    // Clear previous time slots
    timeSlots.innerHTML = '';
    
    // Get booked times for this date
    const bookedTimes = bookedSlots[dateStr] || [];
    
    // Create time slot elements
    availableTimeSlots.forEach(time => {
        const timeSlot = document.createElement('div');
        timeSlot.className = 'time-slot';
        timeSlot.textContent = time;
        
        // Check if this time is booked
        if (bookedTimes.includes(time)) {
            timeSlot.classList.add('unavailable');
        } else {
            timeSlot.addEventListener('click', function() {
                selectTime(time, this);
            });
        }
        
        timeSlots.appendChild(timeSlot);
    });
    
    // Show time slots container
    timeSlotsContainer.style.display = 'block';
    reasonContainer.style.display = 'none';
}

function selectTime(time, element) {
    // Remove previous selection
    document.querySelectorAll('.time-slot.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked time slot
    element.classList.add('selected');
    selectedTime = time;
    
    // Show reason form
    document.getElementById('reasonContainer').style.display = 'block';
}

function confirmAppointment() {
    const patientId = document.getElementById('patientId').value.trim();
    const patientName = document.getElementById('patientName').value.trim();
    const patientEmail = document.getElementById('patientEmail').value.trim();
    const patientPhone = document.getElementById('patientPhone').value.trim();
    const visitReason = document.getElementById('visitReason').value.trim();
    const notification = document.getElementById('notification');
    
    // Validate form
    if (!patientId || !patientName || !patientEmail || !patientPhone || !selectedDate || !selectedTime) {
        notification.className = 'notification error';
        notification.textContent = 'Please fill in all required fields and select a date and time.';
        return;
    }
    
    // Send appointment data to server
    fetch('save_appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `doctor_id=${doctorId}&patient_id=${patientId}&appointment_date=${selectedDate}&appointment_time=${selectedTime}&patient_name=${encodeURIComponent(patientName)}&patient_email=${encodeURIComponent(patientEmail)}&patient_phone=${encodeURIComponent(patientPhone)}&visit_reason=${encodeURIComponent(visitReason)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notification.className = 'notification success';
            notification.textContent = 'Your appointment has been booked successfully!';
            
            // Update booked slots to refresh the calendar
            if (!bookedSlots[selectedDate]) {
                bookedSlots[selectedDate] = [];
            }
            bookedSlots[selectedDate].push(selectedTime);
            
            // Reset form after successful booking
            setTimeout(() => {
                document.getElementById('reasonContainer').style.display = 'none';
                document.getElementById('timeSlotsContainer').style.display = 'none';
                renderCalendar();
                
                // Clear form fields
                document.getElementById('visitReason').value = '';
                document.querySelectorAll('.calendar-day.selected').forEach(el => {
                    el.classList.remove('selected');
                });
                document.querySelectorAll('.time-slot.selected').forEach(el => {
                    el.classList.remove('selected');
                });
                selectedDate = null;
                selectedTime = null;
            }, 2000);
        } else {
            notification.className = 'notification error';
            notification.textContent = data.message || 'There was an error booking your appointment. Please try again.';
        }
    })
    .catch(error => {
        notification.className = 'notification error';
        notification.textContent = 'There was an error processing your request. Please try again.';
        console.error('Error:', error);
    });
}

function isSameDay(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}
</script>