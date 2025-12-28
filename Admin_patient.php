<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Patients - QuickCare</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #D0D7E1;
        display: flex;
    }
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

    /* Sidebar */
    .sidebar {
            width: 250px;
            background: #072D44;
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #9CCDD8;
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: #D0D7E1;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #064469;
            border-left: 4px solid #9CCDD8;
            color: white;
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
    /* Main content */
    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    /* Top bar */
    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .topbar h1 {
        margin: 0;
        color: #064469;
    }

    /* Table Section */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 14px;
        border-bottom: 1px solid #D0D7E1;
    }

    th {
        background: #5790AB;
        color: white;
        text-align: left;
    }

    tr:hover {
        background: #F2F9FB;
    }
    .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
    .filter-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .filter-container form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .filter-container input, .filter-container select {
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
    }
    .filter-container button {
        padding: 10px 15px;
        background: #5790AB;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .filter-container button:hover {
        background: #064469;
    }
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .view-btn {
        background-color: #3498db;
        color: white;
    }
    .edit-btn {
        background-color: #f39c12;
        color: white;
    }
    .delete-btn {
        background-color: #e74c3c;
        color: white;
    }
    .add-btn {
        background: #2ecc71;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px;
        text-decoration: none;
        display: inline-block;
    }
    .add-btn:hover {
        background: #27ae60;
    }
    .blood-group-badge {
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        background-color: #e74c3c;
        color: white;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>

    <a href="admin_dashboard.php">Dashboard</a>
    <a href="view_appointments.php">View Appointments</a>
    <a href="manage_doctors.php">Manage Doctors</a>
    <a href="manage_receptionist.php">Manage Receptionist</a>
    <a href="manage_patients.php" class="active">Manage Patients</a>
    <a href="view_medicine.php">View Medicine</a>
    <a href="view_payments.php">View Payments</a>
    <a href="view_feedback.php">View Feedback</a>
    <a href="reports.php">Reports</a>
    <button class="logout-btn">logout</button>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h1>Manage Patients</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Add New Patient Button -->
    <a href="add_patient.php" class="add-btn">+ Add New Patient</a>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="manage_patients.php">
            <input type="text" name="name_filter" placeholder="Filter by Name">
            <select name="blood_group_filter">
                <option value="">All Blood Groups</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
            <select name="gender_filter">
                <option value="">All Genders</option>
                <option value="MALE">Male</option>
                <option value="FEMALE">Female</option>
                <option value="OTHER">Other</option>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Patients Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Blood Group</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        // PHP code to fetch patients from database
        include 'db_connection.php';
        
        // Build query based on filters
        $query = "SELECT * FROM patient_tbl WHERE 1=1";
        
        // Apply filters if set
        if(isset($_GET['name_filter']) && !empty($_GET['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
            $query .= " AND (FIRST_NAME LIKE '%$name%' OR LAST_NAME LIKE '%$name%')";
        }
        
        if(isset($_GET['blood_group_filter']) && !empty($_GET['blood_group_filter'])) {
            $blood_group = mysqli_real_escape_string($conn, $_GET['blood_group_filter']);
            $query .= " AND BLOOD_GROUP = '$blood_group'";
        }
        
        if(isset($_GET['gender_filter']) && !empty($_GET['gender_filter'])) {
            $gender = mysqli_real_escape_string($conn, $_GET['gender_filter']);
            $query .= " AND GENDER = '$gender'";
        }
        
        $query .= " ORDER BY FIRST_NAME, LAST_NAME";
        
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>".$row['PATIENT_ID']."</td>
                    <td>".$row['FIRST_NAME']." ".$row['LAST_NAME']."</td>
                    <td>".$row['DOB']."</td>
                    <td>".$row['GENDER']."</td>
                    <td><span class='blood-group-badge'>".$row['BLOOD_GROUP']."</span></td>
                    <td>".$row['PHONE']."</td>
                    <td>".$row['EMAIL']."</td>
                    <td>
                        <button class='action-btn view-btn' onclick='viewPatient(".$row['PATIENT_ID'].")'>View</button>
                        <button class='action-btn edit-btn' onclick='editPatient(".$row['PATIENT_ID'].")'>Edit</button>
                        <button class='action-btn delete-btn' onclick='deletePatient(".$row['PATIENT_ID'].")'>Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No patients found</td></tr>";
        }
        
        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function viewPatient(id) {
    window.location.href = "view_patient_details.php?id=" + id;
}

function editPatient(id) {
    window.location.href = "edit_patient.php?id=" + id;
}

function deletePatient(id) {
    if(confirm("Are you sure you want to delete this patient?")) {
        window.location.href = "delete_patient.php?id=" + id;
    }
}
</script>

</body>
</html>