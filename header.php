<!-- HEADER START -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#0B5ED7;
  --accent:#00C2CB;
  --dark:#0f172a;
  --text:#334155;
  --bg:#f8fafc;
  --card:#ffffff;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Inter',sans-serif;
}

<head>
        /* ===== HEADER ===== */
        header {
            background: #ffffff;
            padding: 14px 60px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        
        }

/* NAVBAR */
nav{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 7%;
  background:#fff;
  position:sticky;
  top:0;
  z-index:100;
  border-bottom:1px solid #e5e7eb;
}


.logo{
  font-size:1.4rem;
  font-weight:800;
  color:var(--primary);
}
.logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
/* RIGHT SIDE NAV */
.nav-right{
  display:flex;
  align-items:center;
  gap:36px;
}

nav ul{
  display:flex;
  gap:28px;
  list-style:none;
}

nav ul li a{
  text-decoration:none;
  font-weight:600;
  color:#1e293b;
}

nav ul li a:hover{
  color:var(--primary);
}

.nav-btns{
  display:flex;
  gap:14px;
}

.nav-btns a{
  text-decoration:none;
  padding:10px 22px;
  border-radius:10px;
  font-weight:600;
}

.login{
  color:var(--primary);
  border:2px solid var(--primary);
}

.register{
  background:linear-gradient(135deg,var(--primary),var(--accent));
  color:#fff;
}
</style>

<nav>
  <div class="logo">
    <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
    QuickCare</div>

  <div class="nav-right">
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="appointment.php">Schedules</a></li>
      <li><a href="appointment.php">Doctors</a></li>
      <li><a href="aboutus.html">About</a></li>

      <li><a href="contactus.html">Contact</a></li>
    </ul>

    <div class="nav-btns">
      <a href="login_for_all.php" class="login">Login</a>
      <a href="patientform.php" class="register">Register</a>
    </div>
  </div>
</nav>
<!-- HEADER END -->
