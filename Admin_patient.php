<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Patients - QuickCare</title>
<?php include 'admin_sidebar.php'; ?>
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
    }

    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

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

    .filter-container input,
    .filter-container select {
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

    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        color: white;
    }

    .edit-btn { background: #f39c12; }
    .delete-btn { background: #e74c3c; }

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

<div class="main">

    <div class="topbar">
        <h1>Manage Patients</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- FILTER (UI UNCHANGED, BUG FIXED) -->
    <div class="filter-container">
        <form method="GET" action="">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_GET['name_filter']) ? htmlspecialchars($_GET['name_filter']) : ''; ?>">

            <select name="blood_group_filter">
                <option value="">All Blood Groups</option>
                <?php
                $blood_groups = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                foreach ($blood_groups as $bg) {
                    $selected = (isset($_GET['blood_group_filter']) && $_GET['blood_group_filter'] === $bg) ? 'selected' : '';
                    echo "<option value='$bg' $selected>$bg</option>";
                }
                ?>
            </select>

            <select name="gender_filter">
                <option value="">All Genders</option>
                <option value="MALE" <?php if(isset($_GET['gender_filter']) && $_GET['gender_filter']=='MALE') echo 'selected'; ?>>Male</option>
                <option value="FEMALE" <?php if(isset($_GET['gender_filter']) && $_GET['gender_filter']=='FEMALE') echo 'selected'; ?>>Female</option>
                <option value="OTHER" <?php if(isset($_GET['gender_filter']) && $_GET['gender_filter']=='OTHER') echo 'selected'; ?>>Other</option>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

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
        include 'config.php';

        $query = "SELECT * FROM patient_tbl WHERE 1=1";

        if (!empty($_GET['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
            $query .= " AND CONCAT(FIRST_NAME,' ',LAST_NAME) LIKE '%$name%'";
        }

        if (!empty($_GET['blood_group_filter'])) {
            $bg = mysqli_real_escape_string($conn, $_GET['blood_group_filter']);
            $query .= " AND BLOOD_GROUP = '$bg'";
        }

        if (!empty($_GET['gender_filter'])) {
            $gender = mysqli_real_escape_string($conn, $_GET['gender_filter']);
            $query .= " AND GENDER = '$gender'";
        }

        $query .= " ORDER BY FIRST_NAME, LAST_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['PATIENT_ID']}</td>
                    <td>{$row['FIRST_NAME']} {$row['LAST_NAME']}</td>
                    <td>{$row['DOB']}</td>
                    <td>{$row['GENDER']}</td>
                    <td><span class='blood-group-badge'>{$row['BLOOD_GROUP']}</span></td>
                    <td>{$row['PHONE']}</td>
                    <td>{$row['EMAIL']}</td>
                    <td>
                        <button class='action-btn edit-btn'
                            onclick=\"window.location.href='edit_patient.php?id={$row['PATIENT_ID']}'\">Edit</button>
                        <button class='action-btn delete-btn'
                            onclick=\"if(confirm('Are you sure?')) window.location.href='delete_patient.php?id={$row['PATIENT_ID']}'\">Delete</button>
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

</body>
</html>
