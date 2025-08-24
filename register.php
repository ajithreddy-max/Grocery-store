<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Use appropriate filter for emails
    $pass = md5($_POST['pass']);
    $cpass = md5($_POST['cpass']);
    $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    try {
        // Check if email already exists
        $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select->execute([$email]);

        if ($select->rowCount() > 0) {
            $message[] = 'User email already exists!';
        } else {
            if ($pass != $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                // Insert data into users table
                $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image, user_type) VALUES(?,?,?,?,?)");
                $insert->execute([$name, $email, $pass, $image, 'user']); // Default user type is 'user'

                if ($insert) {
                    if ($image_size > 2000000) {
                        $message[] = 'Image size is too large!';
                    } else {
                        move_uploaded_file($image_tmp_name, $image_folder);
                        $message[] = 'Registered successfully!';
                        header('location:login.php');
                        exit;
                    }
                }
            }
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
    <title>Register</title>
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
    <form action="" enctype="multipart/form-data" method="POST">
        <h3>Register Now</h3>
        <input type="text" name="name" class="box" placeholder="Enter your name" required pattern="[a-zA-Z ]+" title="Name must contain only letters and spaces.">
        <input type="email" name="email" class="box" placeholder="Enter your email" required>
        <input type="password" name="pass" class="box" placeholder="Enter your password" required 
               pattern="(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}" 
               title="Password must be at least 8 characters long, include at least one uppercase letter and one number.">
        <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
        <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
        <input type="submit" value="Register Now" class="btn" name="submit">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
</section>

</body>
</html>
