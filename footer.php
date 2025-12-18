<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>QuickCare Footer</title>

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
  </style>
</head>

<body>

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

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>

</body>
</html>
