session_start();
session_regenerate_id(true); // For better security

<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Use appropriate filter for emails
    $pass = md5($_POST['pass']); // Keep using md5, but consider a stronger hashing algorithm like bcrypt

    try {
        $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email, $pass]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) { // Check if user exists
            if (isset($row['user_type'])) { // Ensure 'user_type' column exists
                $userType = strtolower($row['user_type']); // Case-insensitive comparison
                if ($userType === 'admin') {
                    $_SESSION['admin_id'] = $row['id'];
                    header('location:admin_page.php'); // Redirect to admin page
                    exit; // Stop further script execution
                } elseif ($userType === 'user') {
                    $_SESSION['user_id'] = $row['id'];
                    header('location:home.php'); // Redirect to user home page
                    exit; // Stop further script execution
                } else {
                    $message[] = 'User type not recognized!';
                }
            } else {
                $message[] = 'Invalid user data. Please contact support!';
            }
        } else {
            $message[] = 'Incorrect email or password!';
        }
    } catch (PDOException $e) {
        $message[] = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/components.css">
</head>
<body>
<?php
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
<section class="form-container">
    <form action="" method="POST">
        <h3>Login Now</h3>
        <input type="email" name="email" class="box" placeholder="Enter your email" required>
        <input type="password" name="pass" class="box" placeholder="Enter your password" required>
        <input type="submit" value="Login Now" class="btn" name="submit">
        <p>Don't have an account? <a href="register.php">Register now</a></p>
    </form>
</section>
</body>
</html>
