<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="topbar-right">
        <a href="../index.php" class="topbar-btn" target="_blank" title="View Website">
            <i class="fas fa-globe"></i>
        </a>
        <div class="user-menu">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-dropdown">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>
