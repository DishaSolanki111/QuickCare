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
            padding: 6px 12px;
            margin: 0 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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

<?php
include 'admin_sidebar.php';
include 'config.php';
?>

<div class="main">

    <div class="topbar">
        <h1>Manage Doctors</h1>
        <p>Welcome, Admin</p>
    </div>

    <a href="/QuickCare/admin/doctorform.php" class="add-btn">+ Add New Doctor</a>

    <div class="filter-container">
        <form method="POST">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">

            <select name="specialization_filter">
                <option value="">All Specializations</option>
                <?php
                $spec_q = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
                while ($spec = mysqli_fetch_assoc($spec_q)) {
                    $selected = (isset($_POST['specialization_filter']) && $_POST['specialization_filter'] == $spec['SPECIALISATION_ID']) ? 'selected' : '';
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
            SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PROFILE_IMAGE,
                   d.EDUCATION, d.PHONE, d.EMAIL,
                   s.SPECIALISATION_NAME
            FROM doctor_tbl d
            LEFT JOIN specialisation_tbl s
            ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE 1=1
        ";

        if (!empty($_POST['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name_filter']);
            $query .= " AND CONCAT(IFNULL(d.FIRST_NAME,''),' ',IFNULL(d.LAST_NAME,'')) LIKE '%$name%'";
        }

        if (!empty($_POST['specialization_filter'])) {
            $spec_id = (int)$_POST['specialization_filter'];
            $query .= " AND d.SPECIALISATION_ID = $spec_id";
        }

        $query .= " ORDER BY d.FIRST_NAME, d.LAST_NAME";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {

                $img = !empty($row['PROFILE_IMAGE'])
                    ? $row['PROFILE_IMAGE']
                    : 'uploads/default_doctor.png';
        ?>
        <tr>
            <td>
                <div class="doctor-info">
                    <img src="<?php echo $img; ?>" class="doctor-img">
                    <div class="doctor-details">
                        <strong><?php echo $row['FIRST_NAME'].' '.$row['LAST_NAME']; ?></strong>
                    </div>
                </div>
            </td>
            <td><?php echo $row['SPECIALISATION_NAME']; ?></td>
            <td><?php echo $row['EDUCATION']; ?></td>
            <td><?php echo $row['PHONE']; ?></td>
            <td><?php echo $row['EMAIL']; ?></td>
            <td class="actions-td">
                <button type="button" class="action-btn edit-btn"
                    onclick="window.location.href='/QuickCare/admin/edit_doctor.php?id=<?php echo $row['DOCTOR_ID']; ?>'">
                    Edit
                </button>

                <button type="button" class="action-btn delete-btn"
                    onclick="confirmDelete(<?php echo $row['DOCTOR_ID']; ?>)">
                    Delete
                </button>
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='6'>No doctors found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this doctor?")) {
        window.location.href = "/QuickCare/admin/delete_doctor.php?id=" + id;
    }
}
</script>

</body>
</html>
