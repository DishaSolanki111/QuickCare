<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Doctors - QuickCare</title>
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
        display: inline-block;
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
    .doctor-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .doctor-info {
        display: flex;
        align-items: center;
    }
    .doctor-details {
        margin-left: 10px;
    }
    .actions-td {
        white-space: nowrap;
    }
</style>
</head>
<body>

<!-- Include Sidebar -->
<?php include 'admin_sidebar.php'; ?>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h1>Manage Doctors</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Add New Doctor Button -->
    <a href="doctorform.php" class="add-btn">+ Add New Doctor</a>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="manage_doctors.php">
            <input type="text" name="name_filter" placeholder="Filter by Name">
            <select name="specialization_filter">
                <option value="">All Specializations</option>
                <?php
                // PHP code to populate specializations from database
                include 'config.php';
                $spec_query = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl ORDER BY SPECIALISATION_NAME";
                $spec_result = mysqli_query($conn, $spec_query);
                while($spec_row = mysqli_fetch_assoc($spec_result)) {
                    echo "<option value='".$spec_row['SPECIALISATION_ID']."'>".$spec_row['SPECIALISATION_NAME']."</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Doctors Table -->
    <table>
        <tr>
            <th>Doctor</th>
            <th>Specialization</th>
            <th>Education</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        // PHP code to fetch doctors from database
        include 'config.php';
        
        // Build query based on filters
        $query = "SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PROFILE_IMAGE, 
                  d.EDUCATION, d.PHONE, d.EMAIL, s.SPECIALISATION_NAME 
                  FROM doctor_tbl d
                  JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                  WHERE 1=1";
        
        // Apply filters if set
        if(isset($_GET['name_filter']) && !empty($_GET['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
            $query .= " AND (d.FIRST_NAME LIKE '%$name%' OR d.LAST_NAME LIKE '%$name%')";
        }
        
        if(isset($_GET['specialization_filter']) && !empty($_GET['specialization_filter'])) {
            $spec_id = mysqli_real_escape_string($conn, $_GET['specialization_filter']);
            $query .= " AND d.SPECIALISATION_ID = $spec_id";
        }
        
        $query .= " ORDER BY d.FIRST_NAME, d.LAST_NAME";
        
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $profile_img = !empty($row['PROFILE_IMAGE']) ? $row['PROFILE_IMAGE'] : 'uploads/default_doctor.png';
                
                echo "<tr>
                    <td>
                        <div class='doctor-info'>
                            <img src='".$profile_img."' alt='".$row['FIRST_NAME']."' class='doctor-img'>
                            <div class='doctor-details'>
                                <strong>".$row['FIRST_NAME']." ".$row['LAST_NAME']."</strong>
                            </div>
                        </div>
                    </td>
                    <td>".$row['SPECIALISATION_NAME']."</td>
                    <td>".$row['EDUCATION']."</td>
                    <td>".$row['PHONE']."</td>
                    <td>".$row['EMAIL']."</td>
                    <td class='actions-td'>
                        <button class='action-btn view-btn' onclick='viewDoctor(".$row['DOCTOR_ID'].")'>View</button>
                        <button class='action-btn edit-btn' onclick='editDoctor(".$row['DOCTOR_ID'].")'>Edit</button>
                        <button class='action-btn delete-btn' onclick='deleteDoctor(".$row['DOCTOR_ID'].")'>Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No doctors found</td></tr>";
        }
        
        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function viewDoctor(id) {
    window.location.href = "view_doctor_details.php?id=" + id;
}

function editDoctor(id) {
    window.location.href = "edit_doctor.php?id=" + id;
}

function deleteDoctor(id) {
    if(confirm("Are you sure you want to delete this doctor?")) {
        window.location.href = "delete_doctor.php?id=" + id;
    }
}
</script>

</body>
</html>