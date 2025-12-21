<style>
    /* Sidebar Styles */
    :root {
        --sidebar-bg: #072D44;
        --sidebar-hover: #064469;
        --sidebar-active: #9CCDD8;
    }

    .sidebar {
        background-color: var(--sidebar-bg);
        min-height: 100vh;
        padding: 0;
        position: fixed;
        width: 250px;
        z-index: 100;
    }
    
.sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        color: #9CCDD8;
    }
    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header h3 {
        color: var(--white);
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .sidebar-header i {
        margin-right: 10px;
        font-size: 1.5rem;
    }
    
    .sidebar-menu {
        padding: 20px 0;
    }
    
    .menu-item {
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .menu-item:hover {
        color: var(--white);
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .menu-item.active {
        color: var(--white);
        background-color: var(--sidebar-hover);
        border-left: 4px solid var(--sidebar-active);
    }
    
    .menu-item i {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
    /* Responsive styles */
    @media (max-width: 992px) {
        .sidebar {
            width: 70px;
        }
        
        .sidebar-header h3 span {
            display: none;
        }
        
        .menu-item span {
            display: none;
        }
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
    }
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
    <div class="sidebar-menu">
        <a href="receptionist.html" class="menu-item">
             <span>Dashboard</span>
        </a>
        <a href="#" class="menu-item active">
            <span>My Profile</span>
        </a>
        <a href="#" class="menu-item">
            <span>Appointments</span>
        </a>
        <a href="#" class="menu-item">
             <span>Doctors</span>
        </a>
        <a href="#" class="menu-item">
    </i> <span>Patients</span>
        </a>
        <a href="#" class="menu-item">
          </i> <span>Medicines</span>
        </a>
        <a href="#" class="menu-item">
          <span>Reminders</span>
        </a>
        <a href="#" class="menu-item">
        <span>Payments</span>
        </a>
        <a href="#" class="menu-item">
          <span>Logout</span>
        </a>
    </div>
</div>