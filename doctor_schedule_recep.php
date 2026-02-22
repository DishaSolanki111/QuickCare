<?php
session_start();


include 'config.php';

// Handle schedule operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_schedule'])) {
        $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
        $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
        $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
        $available_day = mysqli_real_escape_string($conn, $_POST['available_day']);
        
        $insert_query = "INSERT INTO doctor_schedule_tbl (DOCTOR_ID, START_TIME, END_TIME, AVAILABLE_DAY) 
                        VALUES ('$doctor_id', '$start_time', '$end_time', '$available_day')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Schedule created successfully!";
        } else {
            $error_message = "Error creating schedule: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update_schedule'])) {
        $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
        $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
        $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
        $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
        $available_day = mysqli_real_escape_string($conn, $_POST['available_day']);
        
        $update_query = "UPDATE doctor_schedule_tbl 
                        SET DOCTOR_ID = '$doctor_id', START_TIME = '$start_time', 
                            END_TIME = '$end_time', AVAILABLE_DAY = '$available_day' 
                        WHERE SCHEDULE_ID = '$schedule_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Schedule updated successfully!";
        } else {
            $error_message = "Error updating schedule: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['delete_schedule'])) {
        $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
        
        $delete_query = "DELETE FROM doctor_schedule_tbl WHERE SCHEDULE_ID = '$schedule_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            $success_message = "Schedule deleted successfully!";
        } else {
            $error_message = "Error deleting schedule: " . mysqli_error($conn);
        }
    }
}

// Get search parameters with sanitization
 $doctor_name = isset($_GET['doctor_name']) ? trim(mysqli_real_escape_string($conn, $_GET['doctor_name'])) : '';
 $specialization_id = isset($_GET['specialization']) ? mysqli_real_escape_string($conn, $_GET['specialization']) : '';
 $schedule_date = isset($_GET['schedule_date']) ? mysqli_real_escape_string($conn, $_GET['schedule_date']) : '';

// Build the base query
 $query = "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE 1=1
";

// Add search conditions
 $conditions = [];
if (!empty($doctor_name)) {
    $conditions[] = "(d.FIRST_NAME LIKE '%$doctor_name%' OR d.LAST_NAME LIKE '%$doctor_name%')";
}
if (!empty($specialization_id)) {
    $conditions[] = "d.SPECIALISATION_ID = '$specialization_id'";
}
if (!empty($schedule_date)) {
    // Convert date to day of week
    $day_of_week = date('D', strtotime($schedule_date));
    $conditions[] = "EXISTS (
        SELECT 1 FROM doctor_schedule_tbl sch 
        WHERE sch.DOCTOR_ID = d.DOCTOR_ID 
        AND sch.AVAILABLE_DAY = '$day_of_week'
    )";
}

// Apply conditions to query
if (!empty($conditions)) {
    $query .= " AND " . implode(' AND ', $conditions);
}

 $query .= " ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME";

 $doctors_query = mysqli_query($conn, $query);
 $doctors_with_schedules = [];

if ($doctors_query && mysqli_num_rows($doctors_query) > 0) {
    while ($doctor = mysqli_fetch_assoc($doctors_query)) {
        // Get schedules for this doctor
        $schedule_query = "SELECT * FROM doctor_schedule_tbl WHERE DOCTOR_ID = '" . $doctor['DOCTOR_ID'] . "' ORDER BY FIELD(AVAILABLE_DAY, 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN')";
        $schedule_result = mysqli_query($conn, $schedule_query);
        
        $schedules = [];
        if ($schedule_result && mysqli_num_rows($schedule_result) > 0) {
            while ($schedule = mysqli_fetch_assoc($schedule_result)) {
                $schedules[] = $schedule;
            }
        }
        
        $doctors_with_schedules[] = [
            'doctor' => $doctor,
            'schedules' => $schedules
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctor Schedule - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--dark-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: var(--light-blue);
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: var(--gray-blue);
            text-decoration: none;
            font-size: 16px;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar a:hover, .sidebar a.active {
            background: var(--mid-blue);
            border-left: 4px solid var(--light-blue);
            color: var(--white);
        }
        
        .logout-btn:hover{
            background-color: var(--light-blue);
        }
        .logout-btn {
            display: block;
            width: 80%;
            margin: 20px auto 0 auto;
            padding: 10px;
            background-color: var(--soft-blue);
            color: var(--white);    
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        
        .page-header {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--dark-blue);
            margin: 0;
        }
        .search-bar-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }
    
    .search-header {
        margin-bottom: 25px;
    }
    
    .search-title {
        font-size: 24px;
        font-weight: 700;
        color: #072D44;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .search-title i {
        color: #3498db;
        font-size: 28px;
    }
    
    .search-form {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    
    .search-field {
        flex: 1;
        min-width: 220px;
    }
    
    .field-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .field-label i {
        color: #3498db;
        font-size: 16px;
    }
    
    .search-input, .search-select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e8ed;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .search-input:focus, .search-select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        transform: translateY(-2px);
    }
    
    .search-input::placeholder {
        color: #95a5a6;
    }
    
    .search-actions {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }
    
    .btn-search {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }
    
    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }
    
    .btn-clear {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
    }
    
    .btn-clear:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
        background: linear-gradient(135deg, #7f8c8d 0%, #95a5a6 100%);
    }
    
    .active-filters {
        margin-top: 20px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #e8f4f8 0%, #d1e7f0 100%);
        border-radius: 12px;
        border-left: 4px solid #3498db;
    }
    
    .filters-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .filters-info i {
        color: #3498db;
        font-size: 18px;
    }
    
    .filters-info span:first-of-type {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .filter-tag {
        background: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        color: #34495e;
        border: 1px solid #bdc3c7;
    }
    
    .filter-tag strong {
        color: #2980b9;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .search-bar-container {
            padding: 20px;
        }
        
        .search-form {
            flex-direction: column;
        }
        
        .search-field {
            width: 100%;
        }
        
        .search-actions {
            width: 100%;
            justify-content: stretch;
        }
        
        .btn-search, .btn-clear {
            flex: 1;
            justify-content: center;
        }
        
        .filters-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
    
    /* Animation for search fields */
    .search-field {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }
    
    .search-field:nth-child(1) { animation-delay: 0.1s; }
    .search-field:nth-child(2) { animation-delay: 0.2s; }
    .search-field:nth-child(3) { animation-delay: 0.3s; }
    .search-actions { animation-delay: 0.4s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
        /* White background container for schedule cards */
        .schedule-cards-section {
            background: var(--white);
            border-radius: 15px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .schedule-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(480px, 1fr));
            gap: 28px;
            align-items: stretch;
            justify-items: stretch;
        }
        
        .doctor-schedule-card {
            background: var(--white);
            border-radius: 16px;
            padding: 0;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            min-height: 380px;
            display: flex;
            flex-direction: column;
        }
        
        .doctor-schedule-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        /* Doctor Header: soft light background */
        .doctor-header {
            background: #f8fafc;
            color: var(--primary-color);
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            border-radius: 16px 16px 0 0;
        }
        
        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid var(--light-blue);
            object-fit: cover;
        }
        
        .doctor-info h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .doctor-specialization {
            display: inline-block;
            background: rgba(72, 41, 112, 0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 15px;
            margin-top: 10px;
        }
        
        .schedule-content {
            padding: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .schedule-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
            flex: 1;
        }
        
        /* Schedule row: horizontal rounded card, light grey, blue left accent */
        .day-schedule {
            background: #f6f9fb;
            border-radius: 14px;
            padding: 20px 26px;
            border-left: 4px solid var(--secondary-color);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
        }
        
        .day-schedule:hover {
            background: #eef4f8;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .day-name {
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .day-name i {
            color: var(--secondary-color);
        }
        
        .time-range {
            color: #4a5568;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .time-range i {
            color: var(--accent-color);
            font-size: 20px;
        }
        
        .schedule-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .schedule-actions form {
            display: inline-flex;
        }
        
        .btn-edit, .btn-delete {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
        }
        
        .btn-edit {
            background: #f39c12;
        }
        
        .btn-edit:hover {
            background: #e67e22;
        }
        
        .btn-delete {
            background: #e74c3c;
        }
        
        .btn-delete:hover {
            background: #c0392b;
        }
        
        .no-schedule {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 24px 28px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            font-size: 16px;
        }
        
        .no-schedule i {
            font-size: 28px;
            margin-bottom: 12px;
            display: block;
        }
        
        .add-schedule-btn {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-schedule-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: var(--white);
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--accent-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--danger-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #6c757d;
        }
        
        /* Responsive: Tablet - 2 cards per row */
        @media (min-width: 769px) and (max-width: 1199px) {
            .schedule-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Responsive: Desktop - wider cards across full width */
        @media (min-width: 1200px) {
            .schedule-cards-grid {
                grid-template-columns: repeat(auto-fill, minmax(460px, 1fr));
            }
        }
        
        /* Responsive: Schedule row - stack on mobile */
        @media (max-width: 576px) {
            .day-schedule {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
                padding: 18px 20px;
            }
            
            .day-name {
                font-size: 19px;
            }
            
            .time-range {
                font-size: 17px;
            }
            
            .btn-edit, .btn-delete {
                padding: 6px 12px;
                font-size: 17px;
            }
            
            .schedule-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
        
        /* Responsive: Mobile - 1 card per row */
        @media (max-width: 768px) {
            .schedule-cards-section {
                padding: 24px;
            }
            
            .schedule-cards-grid {
                grid-template-columns: 1fr;
                gap: 22px;
            }
            
            .doctor-schedule-card {
                min-height: 340px;
            }
            
            .doctor-header {
                padding: 20px 24px;
                gap: 20px;
                flex-direction: column;
                text-align: center;
            }
            
            .doctor-avatar {
                width: 72px;
                height: 72px;
            }
            
            .doctor-info h3 {
                font-size: 22px;
            }
            
            .schedule-content {
                padding: 24px;
            }
            
            .schedule-grid {
                gap: 14px;
            }
            
            .day-schedule {
                padding: 18px 20px;
                gap: 20px;
            }
            
            .day-name {
                font-size: 20px;
            }
            
            .time-range {
                font-size: 18px;
            }
            
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'recept_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php include 'receptionist_header.php'; ?>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-calendar-week"></i> Doctor Schedules
            </h1>
            <button class="add-schedule-btn" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                <i class="bi bi-plus-circle"></i> Create New Schedule
            </button>
        </div>
        <div class="search-bar-container">
    
    <form method="GET" action="doctor_schedule_recep.php" class="search-form">
        <!-- Doctor Name Search -->
        <div class="search-field">
            <label for="doctor_name" class="field-label">
                <i class="bi bi-person"></i> Doctor Name
            </label>
            <input type="text" 
                   class="search-input" 
                   id="doctor_name" 
                   name="doctor_name" 
                   placeholder="Enter doctor name..."
                   value="<?php echo isset($_GET['doctor_name']) ? htmlspecialchars($_GET['doctor_name']) : ''; ?>">
        </div>
        
        <!-- Specialization Dropdown -->
        <div class="search-field">
            <label for="specialization" class="field-label">
                <i class="bi bi-award"></i> Specialization
            </label>
            <select class="search-select" id="specialization" name="specialization">
                <option value="">All Specializations</option>
                <?php
                // Fetch specializations from database
                $spec_query = mysqli_query($conn, "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
                if (mysqli_num_rows($spec_query) > 0) {
                    while ($spec = mysqli_fetch_assoc($spec_query)) {
                        $selected = (isset($_GET['specialization']) && $_GET['specialization'] == $spec['SPECIALISATION_ID']) ? 'selected' : '';
                        echo '<option value="' . $spec['SPECIALISATION_ID'] . '" ' . $selected . '>' . 
                             htmlspecialchars($spec['SPECIALISATION_NAME']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        
        <!-- Date Picker -->
        <div class="search-field">
            <label for="schedule_date" class="field-label">
                <i class="bi bi-calendar3"></i> Date
            </label>
            <input type="date" 
                   class="search-input" 
                   id="schedule_date" 
                   name="schedule_date"
                   value="<?php echo isset($_GET['schedule_date']) ? htmlspecialchars($_GET['schedule_date']) : ''; ?>">
        </div>
        
        <!-- Action Buttons -->
        <div class="search-actions">
            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Search
            </button>
            <a href="doctor_schedule_recep.php" class="btn-clear">
                <i class="bi bi-arrow-clockwise"></i> Clear
            </a>
        </div>
    </form>
    
    
</div>
        <!-- Doctor Schedules -->
        <?php if (!empty($doctors_with_schedules)): ?>
        <div class="schedule-cards-section">
            <div class="schedule-cards-grid">
            <?php foreach ($doctors_with_schedules as $doctor_data): ?>
                <div class="doctor-schedule-card">
                    <div class="doctor-header">
                        <img src="<?php echo !empty($doctor_data['doctor']['PROFILE_IMAGE']) ? $doctor_data['doctor']['PROFILE_IMAGE'] : 'https://picsum.photos/seed/doctor' . $doctor_data['doctor']['DOCTOR_ID'] . '/80/80.jpg'; ?>" 
                             alt="Doctor" class="doctor-avatar">
                        <div class="doctor-info">
                            <h3>Dr. <?php echo htmlspecialchars($doctor_data['doctor']['FIRST_NAME'] . ' ' . $doctor_data['doctor']['LAST_NAME']); ?></h3>
                            <span class="doctor-specialization">
                                <i class="bi bi-award"></i> 
                                <?php echo htmlspecialchars($doctor_data['doctor']['SPECIALISATION_NAME']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="schedule-content">
                        <?php if (!empty($doctor_data['schedules'])): ?>
                            <div class="schedule-grid">
                                <?php foreach ($doctor_data['schedules'] as $schedule): ?>
                                    <div class="day-schedule">
                                        <div class="day-name">
                                            <?php 
                                            $day_icons = [
                                                'MON' => 'bi-calendar-week',
                                                'TUE' => 'bi-calendar2-week',
                                                'WED' => 'bi-calendar3',
                                                'THUR' => 'bi-calendar4',
                                                'FRI' => 'bi-calendar5',
                                                'SAT' => 'bi-calendar6',
                                                'SUN' => 'bi-calendar'
                                            ];
                                            $day_names = [
                                                'MON' => 'Monday',
                                                'TUE' => 'Tuesday',
                                                'WED' => 'Wednesday',
                                                'THUR' => 'Thursday',
                                                'FRI' => 'Friday',
                                                'SAT' => 'Saturday',
                                                'SUN' => 'Sunday'
                                            ];
                                            ?>
                                            <i class="bi <?php echo $day_icons[$schedule['AVAILABLE_DAY']]; ?>"></i>
                                            <?php echo $day_names[$schedule['AVAILABLE_DAY']]; ?>
                                        </div>
                                        <div class="time-range">
                                            <i class="bi bi-clock-fill"></i>
                                            <?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - 
                                            <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?>
                                        </div>
                                        <div class="schedule-actions">
                                            <button class="btn-edit" onclick="editSchedule(<?php echo $schedule['SCHEDULE_ID']; ?>, '<?php echo $schedule['DOCTOR_ID']; ?>', '<?php echo $schedule['START_TIME']; ?>', '<?php echo $schedule['END_TIME']; ?>', '<?php echo $schedule['AVAILABLE_DAY']; ?>')">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['SCHEDULE_ID']; ?>">
                                                <button type="submit" name="delete_schedule" class="btn-delete" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-schedule">
                                <i class="bi bi-calendar-x"></i>
                                No schedules assigned to this doctor yet
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h4>No Doctors Found</h4>
                <p>There are no doctors in the system yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Create Schedule Modal -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createScheduleModalLabel">
                        <i class="bi bi-plus-circle"></i> Create New Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="doctor_schedule_recep.php">
                        <input type="hidden" name="create_schedule" value="1">
                        
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label">Select Doctor</label>
                            <select class="form-select" id="doctor_id" name="doctor_id" required>
                                <option value="">Choose a doctor...</option>
                                <?php
                                $doctors_dropdown_query = mysqli_query($conn, "
                                    SELECT d.*, s.SPECIALISATION_NAME 
                                    FROM doctor_tbl d
                                    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                                    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
                                ");
                                if (mysqli_num_rows($doctors_dropdown_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_dropdown_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="available_day" class="form-label">Available Day</label>
                            <select class="form-select" id="available_day" name="available_day" required>
                                <option value="">Select day...</option>
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THUR">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                                <option value="SUN">Sunday</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Create Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">
                        <i class="bi bi-pencil-square"></i> Edit Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="doctor_schedule_recep.php">
                        <input type="hidden" id="edit_schedule_id" name="schedule_id">
                        <input type="hidden" name="update_schedule" value="1">
                        
                        <div class="mb-3">
                            <label for="edit_doctor_id" class="form-label">Select Doctor</label>
                            <select class="form-select" id="edit_doctor_id" name="doctor_id" required>
                                <option value="">Choose a doctor...</option>
                                <?php
                                $doctors_dropdown_query = mysqli_query($conn, "
                                    SELECT d.*, s.SPECIALISATION_NAME 
                                    FROM doctor_tbl d
                                    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                                    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
                                ");
                                if (mysqli_num_rows($doctors_dropdown_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_dropdown_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_available_day" class="form-label">Available Day</label>
                            <select class="form-select" id="edit_available_day" name="available_day" required>
                                <option value="">Select day...</option>
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THUR">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                                <option value="SUN">Sunday</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Update Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ocument.addEventListener('DOMContentLoaded', function() {
    // Auto-format date input
    const dateInput = document.getElementById('schedule_date');
    if (dateInput) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
        
        // Optional: Set today's date as default if empty
        if (!dateInput.value) {
            // Uncomment the line below if you want today's date as default
            // dateInput.value = today;
        }
    }
    
    // Real-time search suggestion (optional enhancement)
    const doctorNameInput = document.getElementById('doctor_name');
    if (doctorNameInput) {
        let searchTimeout;
        doctorNameInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // You can implement AJAX live search here
                console.log('Searching for:', e.target.value);
            }, 300);
        });
    }
    
    // Auto-submit on specialization change (optional)
    const specializationSelect = document.getElementById('specialization');
    if (specializationSelect) {
        // Uncomment to auto-submit when specialization changes
        // specializationSelect.addEventListener('change', function() {
        //     this.form.submit();
        // });
    }
    
    // Clear all filters function
    function clearAllFilters() {
        document.querySelectorAll('.search-input, .search-select').forEach(input => {
            input.value = '';
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            doctorNameInput.focus();
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            clearAllFilters();
        }
    });
});
        // Function to edit schedule
        function editSchedule(scheduleId, doctorId, startTime, endTime, availableDay) {
            document.getElementById('edit_schedule_id').value = scheduleId;
            document.getElementById('edit_doctor_id').value = doctorId;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_end_time').value = endTime;
            document.getElementById('edit_available_day').value = availableDay;
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            editModal.show();
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>