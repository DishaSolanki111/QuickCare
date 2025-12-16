<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuickCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f8fc;
        }

        /* ===== HEADER ===== */
        header {
            background: #ffffff;
            padding: 14px 60px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .navbar {
            display: flex;
            align-items: center;
        }

        /* ===== LOGO ===== */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: #e9f2ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #007bff;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 600;
            color: #007bff;
        }

        /* ===== RIGHT SECTION ===== */
        .right-nav {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 35px;
        }

        /* ===== NAV LINKS ===== */
        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-size: 15px;
            font-weight: 500;
            position: relative;
            padding-bottom: 4px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: #007bff;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* ===== BUTTONS ===== */
        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-login {
            padding: 8px 22px;
            border: 2px solid #007bff;
            background: transparent;
            color: #007bff;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #007bff;
            color: #ffffff;
        }

        .btn-register {
            padding: 8px 22px;
            border: none;
            background: #007bff;
            color: #ffffff;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

<header>
    <div class="navbar">

        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">⚕️</div>
            <div class="logo-text">QuickCare</div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right-nav">

            <!-- Navigation Links -->
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="doctors.php">Doctors</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
            </nav>

            <!-- Buttons -->
            <div class="nav-buttons">
                <a href="login.php"><button class="btn-login">Login</button></a>
                <a href="register.php"><button class="btn-register">Register</button></a>
            </div>

        </div>

    </div>
</header>

</body>
</html>
