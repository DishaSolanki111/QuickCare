<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
       
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            margin-top:-15px;
        }
        
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark-color);
            margin-right: 20px;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        /* Content Card Styles */
        .content-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
       
        
      
      
        
        /* Feedback Card Styles */
        .feedback-card {
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .patient-info {
            display: flex;
            align-items: center;
        }
        
        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #007BFF;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            margin-right: 15px;
        }
        
        .patient-name {
            font-weight: bold;
        }
        
        .rating i {
            color: #FFD700;
        }
        
        /* Footer Styles */
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
        
        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'doctor_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <?php include 'doctor_header.php'; ?>
            
            <!-- Content Card -->
            <div class="content-card">
                <div class="feedback-section" style="margin-top: 30px;">
                    <h3 style="margin-bottom: 20px;">Patient Feedback</h3>
                    <div class="feedback-card">
                        <div class="feedback-header">
                            <div class="patient-info">
                                <div class="patient-avatar">MN</div>
                                <div>
                                    <div class="patient-name">Meera Nair</div>
                                    <div style="color: #777; font-size: 14px;">July 15, 2024</div>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        
                        <p style="margin-top: 15px;">Dr. Sharma is very empathetic and provided a clear plan of action. She took time to explain my condition and treatment options in detail. Highly recommended!</p>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-header">
                            <div class="patient-info">
                                <div class="patient-avatar">SK</div>
                                <div>
                                    <div class="patient-name">Sunil Kapoor</div>
                                    <div style="color: #777; font-size: 14px;">June 28, 2024</div>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        
                        <p style="margin-top: 15px;">Dr. Kumar is thorough. The consultation was good but the waiting time was a bit long. However, once I was in the consultation room, he gave me his full attention and answered all my questions.</p>
                    </div>
                    
                    <div class="feedback-card">
                        <div class="feedback-header">
                            <div class="patient-info">
                                <div class="patient-avatar">RV</div>
                                <div>
                                    <div class="patient-name">Rohan Verma</div>
                                    <div style="color: #777; font-size: 14px;">May 10, 2024</div>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        
                        <p style="margin-top: 15px;">Dr. Kumar's detailed explanation put my mind at ease. He was very patient and made sure I understood everything about my condition and treatment. Highly recommend!</p>
                    </div>
                </div>
            </div>
            
        
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>