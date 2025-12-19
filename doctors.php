<?php
include "config.php";
include "header.php";

 $spec_id = intval($_GET['spec_id']);

 $q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, PROFILE_IMAGE 
      FROM doctor_tbl 
      WHERE SPECIALISATION_ID = $spec_id";

 $res = mysqli_query($conn, $q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #004a99;
            --primary-light: #e6f0ff;
            --secondary: #0099ff;
            --accent: #0052cc;
            --light-blue: #f0f7ff;
            --medium-blue: #d4e6ff;
            --dark-blue: #003366;
            --text-dark: #1a3a5f;
            --text-light: #ffffff;
            --shadow: 0 4px 15px rgba(0, 102, 204, 0.1);
            --shadow-hover: 0 10px 25px rgba(0, 102, 204, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .doctors-section {
            padding: 4rem 0;
            flex-grow: 1;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            width: 100%;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .doctor-image-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .doctor-image {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 5px solid var(--light-blue);
            transition: all 0.3s ease;
        }

        .card:hover .doctor-image {
            border-color: var(--primary);
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .card-title {
            color: var(--primary);
            font-size: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .card-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: #ffc107;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            width: 100%;
            margin-top: auto;
        }

        .card-actions button, .card-actions a {
            flex: 1;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background: var(--light-blue);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--medium-blue);
            transform: translateY(-3px);
        }

        /* POPUP */
        #calendarModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        #calendarBox {
            background: white;
            width: 90%;
            max-width: 500px;
            height: 80vh;
            max-height: 600px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .calendar-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .calendar-header span {
            font-size: 1.5rem;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .calendar-header span:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .calendar-content {
            flex-grow: 1;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .grid-container {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .doctor-image {
                width: 120px;
                height: 120px;
            }
        }

        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }
        .card:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h1>Our Doctors</h1>
            <p>Meet our team of experienced medical professionals dedicated to your health</p>
        </div>
    </div>

    <section class="doctors-section">
        <div class="container">
            <div class="grid-container">
                <?php while($row = mysqli_fetch_assoc($res)){ 
                    $img = !empty($row['PROFILE_IMAGE']) 
                        ? $row['PROFILE_IMAGE'] 
                        : 'imgs/default.jpg';
                ?>
                    <div class="card">
                        <div class="doctor-image-container">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Doctor" class="doctor-image">
                        </div>
                        <h3><?php echo "Dr. ".$row['FIRST_NAME']." ".$row['LAST_NAME']; ?></h3>
                        <div class="card-title">Specialist</div>
                        <div class="card-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="card-actions">
                            <a href="doctor_profile.php?id=<?php echo $row['DOCTOR_ID']; ?>" class="btn-secondary">
                                <i class="fas fa-user"></i> View Profile
                            </a>
                            <button class="btn-primary" onclick="openCalendar(<?php echo $row['DOCTOR_ID']; ?>)">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- CALENDAR POPUP -->
    <div id="calendarModal">
        <div id="calendarBox">
            <div class="calendar-header">
                <h3>Select Appointment Date</h3>
                <span onclick="closeCalendar()">&times;</span>
            </div>
            <div class="calendar-content">
                <iframe id="calendarFrame"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openCalendar(id){
            document.getElementById("calendarFrame").src = "calendar.php?doctor_id=" + id;
            document.getElementById("calendarModal").style.display = "flex";
            document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        }
        
        function closeCalendar(){
            document.getElementById("calendarModal").style.display = "none";
            document.getElementById("calendarFrame").src = "";
            document.body.style.overflow = 'auto'; // Enable scrolling back
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("calendarModal");
            if (event.target == modal) {
                closeCalendar();
            }
        }
    </script>
</body>
</html>