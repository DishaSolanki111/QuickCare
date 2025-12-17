<?php
include "config.php";
include "header.php";

$q = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl";
$res = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html>
<head>
<title>Select Specialization</title>
<style>
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
body{font-family:Arial;background:#f5f8ff;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;width:80%;margin:40px auto;}
.card{background:white;padding:100px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,.1);text-align:center;}
button{margin-top:15px;padding:10px 16px;background:#2e6ad6;color:white;border:none;border-radius:8px;}
</style>
</head>
<h1 style="text-align:center; padding:50px;">Choose Specialization</h1>

<div class="grid">
<?php while($row = mysqli_fetch_assoc($res)){ ?>
    <div class="card">
        <h2><?php echo $row['SPECIALISATION_NAME']; ?></h2>
        <a href="doctors.php?spec_id=<?php echo $row['SPECIALISATION_ID']; ?>">
            <button>View Doctors</button>
        </a>
    </div>
<?php } ?>
</div>
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
    </html>


