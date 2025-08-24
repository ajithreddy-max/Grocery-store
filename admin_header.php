<?php
// Start the session (must be the first line of the file)
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Start the session if it hasn't been started already
}

// Initialize $admin_id safely
$admin_id = $_SESSION['admin_id'] ?? null;

// Check and display messages if any
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
    <div class="flex">
        <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>

        <nav class="navbar">
            <a href="admin_page.php">home</a>
            <a href="admin_products.php">products</a>
            <a href="admin_orders.php">orders</a>
            <a href="admin_users.php">users</a>
            <a href="admin_contacts.php">messages</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="profile">
            <?php
            if ($admin_id) {
                // Prepare the SQL query to fetch admin profile details
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$admin_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

                if ($fetch_profile) {
                    // Display profile details safely
                    $image = htmlspecialchars($fetch_profile['image'] ?? 'default.png'); // Default image if not set
                    $name = htmlspecialchars($fetch_profile['name'] ?? 'Admin'); // Default name if not set
                    echo '<img src="uploaded_img/' . $image . '" alt="Profile Image">';
                    echo '<p>' . $name . '</p>';
                    echo '<a href="admin_update_profile.php" class="btn">update profile</a>';
                    echo '<a href="logout.php" class="delete-btn">logout</a>';
                } else {
                    echo '<p>Admin profile not found.</p>';
                }
            } else {
                // If admin_id is not set, prompt the user to log in
                echo '<p>Please <a href="login.php" class="option-btn">log in</a> as admin.</p>';
            }
            ?>
            <?php if (!$admin_id): ?>
                <div class="flex-btn">
                    <a href="login.php" class="option-btn">login</a>
                    <a href="register.php" class="option-btn">register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
