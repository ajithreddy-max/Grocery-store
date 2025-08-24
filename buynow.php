<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['buy_now'])) {
    $pid = $_POST['pid'];
    $pid = filter_var($pid, FILTER_SANITIZE_STRING);
    $p_name = $_POST['p_name'];
    $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
    $p_price = $_POST['p_price'];
    $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
    $p_image = $_POST['p_image'];
    $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);
    $p_qty = $_POST['p_qty'];
    $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);

    $total_price = $p_price * $p_qty;

    try {
        // Insert order into the `orders` table
        $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, pid, name, price, quantity, image, total_price, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert_order->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image, $total_price]);

        if ($insert_order) {
            $_SESSION['order_message'] = 'Purchase successful!';
            header('location:order_confirmation.php'); // Redirect to confirmation page
            exit;
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
    <title>Buy Now</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="products">

    <h1 class="title">Buy Now</h1>

    <div class="box-container">

        <?php
        $select_products = $conn->prepare("SELECT * FROM `products`");
        $select_products->execute();
        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="POST" class="box">
            <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="Product Image">
            <div class="name"><?= $fetch_products['name']; ?></div>
            <div class="price">Rs.<?= $fetch_products['price']; ?>/- <span style="font-size: 0.9rem;">per KG</span></div>

            <!-- Hidden fields for passing product data -->
            <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
            <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
            <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
            <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
            <input type="number" min="1" value="1" name="p_qty" class="qty">

            <input type="submit" value="Buy Now" class="btn" name="buy_now">
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">No products available.</p>';
        }
        ?>

    </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
