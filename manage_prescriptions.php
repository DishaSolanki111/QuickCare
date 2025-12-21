<?php
// Include your existing config file
require_once 'config.php';
// Check if the connection variable $conn exists and is valid
if (!$conn) {
    die("Error: Database connection failed. Please check your config.php file.");
}

// --- NEW: Check for success message in URL ---
 $alert_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $alert_message = "Prescription added suc    cessfully!";
    } elseif ($_GET['status'] === 'deleted_success') {
        $alert_message = "Prescription deleted successfully!";
    }
}

// Fetch all patients from the database
 $sql = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, DOB, PHONE FROM patient_tbl ORDER BY LAST_NAME, FIRST_NAME";
 $result = $conn->query($sql);

 $patients = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
 $conn->close(); // Close the connection when done
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #0056b3; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        thead { background-color: #007bff; color: white; }
        tbody tr:hover { background-color: #f5f5f5; }
        .btn { display: inline-block; padding: 8px 15px; text-decoration: none; border-radius: 5px; color: #fff; background-color: #28a745; border: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background-color: #218838; }
        :root {
      --primary: #0066cc;
      --dark: #1a3a5f;
    }

    footer {
      background: var(--dark);
      color: white;
      padding: 3rem 5%;
      text-align: center;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .social-link {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .social-link:hover {
      background: var(--primary);
      transform: translateY(-3px);
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Patient Prescriptions</h1>
        <p>Select a patient to view, add, or delete their prescriptions.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Date of Birth</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($patients) > 0): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></td>
                            <td><?php echo htmlspecialchars($patient['DOB']); ?></td>
                            <td><?php echo htmlspecialchars($patient['PHONE']); ?></td>
                            <td>
                                <a href="prescription_form.php?patient_id=<?php echo $patient['PATIENT_ID']; ?>" class="btn">Manage Prescriptions</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No patients found in the database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- NEW: JavaScript to show the pop-up alert -->
    <?php if (!empty($alert_message)): ?>
        <script>
            // Wait for the page to fully load before showing the alert
            window.onload = function() {
                alert('<?php echo addslashes($alert_message); ?>');
            }
        </script>
    <?php endif; ?>
    <footer>
    <div class="footer-content">
      <p>&copy; <span id="year"></span> QuickCare — Revolutionizing Healthcare Access</p>
      <div class="social-links">
        <a href="#" class="social-link"><span>f</span></a>
        <a href="#" class="social-link"><span>𝕏</span></a>
        <a href="#" class="social-link"><span>in</span></a>
        <a href="#" class="social-link"><span>📷</span></a>
      </div>
    </div>
  </footer>


</body>
 <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>