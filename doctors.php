<?php
include "config.php";
include "header.php";

$spec_id = $_GET['spec_id'];

$q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE SPECIALISATION_ID = $spec_id";
$res = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html>
<head>
<title>Doctors</title>

<style>
    :root {
      --primary: #0066cc;
      --dark: #1a3a5f;
    }

    footer {
      background: var(--dark);
      color: white;
      padding: 5rem 5%;
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
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;
    width:85%;
    margin:40px auto;
}
.card{
    background:white;
    padding:70px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
    text-align:center;
}
img{width:100px;height:100px;border-radius:50%;}
button{
    margin:5px;
    padding:8px 14px;
    background:#2e6ad6;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
.secondary{background:#555;}

/* POPUP */
#calendarModal{
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,.6);
    justify-content:center;
    align-items:center;
    z-index:999;
}
#calendarBox{
    background:white;
    width:460px;
    height:520px;
    border-radius:12px;
    position:relative;
}
#calendarBox span{
    position:absolute;
    top:10px;
    right:15px;
    font-size:22px;
    cursor:pointer;
}
iframe{
    width:100%;
    height:100%;
    border:none;
    border-radius:12px;
}
</style>
</head>

<body>

<h1 style="text-align:center;">Doctors</h1>

<div class="grid">
<?php while($row = mysqli_fetch_assoc($res)){ ?>
    <div class="card">
        <img src="imgs/default.jpg">
        <h3><?php echo $row['FIRST_NAME']." ".$row['LAST_NAME']; ?></h3>

        <a href="doctor_profile.php?id=<?php echo $row['DOCTOR_ID']; ?>">
            <button class="secondary">View Profile</button>
        </a>

        <button onclick="openCalendar(<?php echo $row['DOCTOR_ID']; ?>)">
        Book Now
        </button>

    </div>

<?php } ?>
</div>

<!-- CALENDAR POPUP -->
<div id="calendarModal">
    <div id="calendarBox">
        <span onclick="closeCalendar()">×</span>
        <iframe id="calendarFrame"></iframe>
    </div>
</div>

<script>
function openCalendar(doctorId){
    document.getElementById("calendarFrame").src =
        "calendar.php?doctor_id=" + doctorId;
    document.getElementById("calendarModal").style.display = "flex";
}

function closeCalendar(){
    document.getElementById("calendarModal").style.display = "none";
    document.getElementById("calendarFrame").src = "";
    document.getElementById('year').textContent = new Date().getFullYear();
}
</script>
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

</body>
</html>
