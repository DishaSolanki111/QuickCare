<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Medicine - QuickCare</title>
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
</style>
</head>

<body>

<div class="main">

    <div class="topbar">
        <h1>View Medicine</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- FILTER (NAME ONLY, FIXED) -->
    <div class="filter-container">
        <form method="POST" action="">
            <input type="text" name="name_filter"
                   placeholder="Filter by Medicine Name"
                   value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Medicine Table -->
    <table>
        <tr>
            <th>Medicine ID</th>
            <th>Medicine Name</th>
            <th>Description</th>
            <th>Added By</th>
        </tr>

        <?php
        include 'config.php';

        $query = "SELECT 
                    m.MEDICINE_ID,
                    m.MED_NAME,
                    m.DESCRIPTION,
                    CONCAT(r.FIRST_NAME, ' ', r.LAST_NAME) AS receptionist_name
                  FROM medicine_tbl m
                  LEFT JOIN receptionist_tbl r 
                    ON m.RECEPTIONIST_ID = r.RECEPTIONIST_ID
                  WHERE 1=1";

        if (!empty($_POST['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name_filter']);
            $query .= " AND m.MED_NAME LIKE '%$name%'";
        }

        $query .= " ORDER BY m.MED_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['MEDICINE_ID']}</td>
                    <td>{$row['MED_NAME']}</td>
                    <td>{$row['DESCRIPTION']}</td>
                    <td>{$row['receptionist_name']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No medicines found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>

</body>
</html>
