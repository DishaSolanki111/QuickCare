<?php
// admin_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Static admin display (no DB needed)
$admin_full_name = 'Admin';
$initials = 'AD';
?>

<header class="topbar">
    <h2><?php echo htmlspecialchars($page_title ?? 'Welcome back'); ?></h2>

    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo htmlspecialchars($initials); ?>
            </div>

            <div class="user-details">
                <div class="name-row">
                    <span class="user-name">
                        <?php echo htmlspecialchars($admin_full_name); ?>
                    </span>
                </div>

                <span class="date">
                    <?php echo date("F d, Y"); ?>
                </span>
            </div>
        </div>
    </div>
</header>

<style>
.topbar {
    background: #ffffff;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    margin-bottom: 10px;
}

.topbar-right {
    display: flex;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1a3a5f;
    color: #ffffff;
    font-weight: 600;
    font-size: 16px;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.name-row {
    display: flex;
    align-items: center;
}

.user-name {
    font-weight: 600;
    color: #1a3a5f;
    font-size: 16px;
}

.date {
    color: #6b7280;
    font-size: 14px;
}
</style>

