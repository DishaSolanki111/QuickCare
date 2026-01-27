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
        margin: 0 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        display: inline-block;
        color: white;
    }

    .edit-btn { background-color: #f39c12; }
    .delete-btn { background-color: #e74c3c; }

    .add-btn {
        background: #2ecc71;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .doctor-info {
        display: flex;
        align-items: center;
    }

    .doctor-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
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

<?php include 'admin_sidebar.php'; ?>
<?php include 'config.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Manage Doctors</h1>
        <p>Welcome, Admin</p>
    </div>

    <a href="doctorform.php" class="add-btn">+ Add New Doctor</a>

    <!-- FILTER UI (UNCHANGED) -->
    <div class="filter-container">
        <form method="GET">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_GET['name_filter']) ? htmlspecialchars($_GET['name_filter']) : ''; ?>">

            <select name="specialization_filter">
                <option value="">All Specializations</option>
                <?php
                $spec_q = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
                while ($spec = mysqli_fetch_assoc($spec_q)) {
                    $selected = (isset($_GET['specialization_filter']) && $_GET['specialization_filter'] == $spec['SPECIALISATION_ID']) ? 'selected' : '';
                    echo "<option value='{$spec['SPECIALISATION_ID']}' $selected>{$spec['SPECIALISATION_NAME']}</option>";
                }
                ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

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
        $query = "
            SELECT d.DOCTOR_ID,
                   d.FIRST_NAME,
                   d.LAST_NAME,
                   d.PROFILE_IMAGE,
                   d.EDUCATION,
                   d.PHONE,
                   d.EMAIL,
                   s.SPECIALISATION_NAME
            FROM doctor_tbl d
            LEFT JOIN specialisation_tbl s
                ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE 1=1
        ";

        if (!empty($_GET['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
            $query .= " AND CONCAT(IFNULL(d.FIRST_NAME,''),' ',IFNULL(d.LAST_NAME,'')) LIKE '%$name%'";
        }

        if (!empty($_GET['specialization_filter'])) {
            $spec_id = (int)$_GET['specialization_filter'];
            $query .= " AND d.SPECIALISATION_ID = $spec_id";
        }

        $query .= " ORDER BY d.FIRST_NAME, d.LAST_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {

                $img = !empty($row['PROFILE_IMAGE'])
                    ? $row['PROFILE_IMAGE']
                    : 'uploads/default_doctor.png';

                echo "<tr>
                    <td>
                        <div class='doctor-info'>
                            <img src='{$img}' class='doctor-img'>
                            <div class='doctor-details'>
                                <strong>{$row['FIRST_NAME']} {$row['LAST_NAME']}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{$row['SPECIALISATION_NAME']}</td>
                    <td>{$row['EDUCATION']}</td>
                    <td>{$row['PHONE']}</td>
                    <td>{$row['EMAIL']}</td>
                    <td class='actions-td'>
                        <button class='action-btn edit-btn'
                            onclick=\"window.location.href='edit_doctor.php?id={$row['DOCTOR_ID']}'\">
                            Edit
                        </button>
                        <button class='action-btn delete-btn'
                            onclick=\"if(confirm('Are you sure?')) window.location.href='delete_doctor.php?id={$row['DOCTOR_ID']}'\">
                            Delete
                        </button>
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

</body>
</html>
