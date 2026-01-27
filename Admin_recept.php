<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Receptionists - QuickCare</title>

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
    }

    .filter-container input {
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
    }

  
    .edit-btn { background: #f39c12; color: white; }
    .delete-btn { background: #e74c3c; color: white; }

    .add-btn {
        background: #2ecc71;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Manage Receptionists</h1>
        <p>Welcome, Admin</p>
    </div>

    <a href="recpt_regis.php" class="add-btn">+ Add New Receptionist</a>

    <!-- FILTER BY NAME ONLY -->
    <div class="filter-container">
        <form method="GET">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_GET['name_filter']) ? htmlspecialchars($_GET['name_filter']) : ''; ?>">
            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Date of Joining</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        include 'config.php';

        $query = "SELECT * FROM receptionist_tbl";

        if (isset($_GET['name_filter']) && $_GET['name_filter'] !== '') {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
            $query .= " WHERE CONCAT(FIRST_NAME,' ',LAST_NAME) LIKE '%$name%'";
        }

        $query .= " ORDER BY FIRST_NAME, LAST_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['RECEPTIONIST_ID']; ?></td>
                    <td><?php echo $row['FIRST_NAME']." ".$row['LAST_NAME']; ?></td>
                    <td><?php echo $row['DOB']; ?></td>
                    <td><?php echo $row['DOJ']; ?></td>
                    <td><?php echo $row['GENDER']; ?></td>
                    <td><?php echo $row['PHONE']; ?></td>
                    <td><?php echo $row['EMAIL']; ?></td>
                    <td>
                      
                        <button class="action-btn edit-btn"
                            onclick="editReceptionist(<?php echo $row['RECEPTIONIST_ID']; ?>)">Edit</button>
                        <button class="action-btn delete-btn"
                            onclick="deleteReceptionist(<?php echo $row['RECEPTIONIST_ID']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='8'>No receptionists found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>

</div>

<script>


function editReceptionist(id) {
    window.location.href = "edit_receptionist.php?id=" + id;
}

function deleteReceptionist(id) {
    if (confirm("Are you sure you want to delete this receptionist?")) {
        window.location.href = "delete_receptionist.php?id=" + id;
    }
}
</script>

</body>
</html>
