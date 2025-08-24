<?php
@include 'config.php';
// Fetch all products from the database
$sql = "SELECT * FROM `products`";
$stmt = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your custom styles -->
</head>
<body>

<h1>Product List</h1>
<div class="product-container">
    <?php
    // Loop through the fetched products and display them
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Ensure the image file exists before displaying it
        $image_path = 'project images/' . $row['image'];
        if (file_exists($image_path)) {
            echo '<div class="product">';
            echo '<img src="' . $image_path . '" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p>Price: $' . htmlspecialchars($row['price']) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="product">';
            echo '<p>Image not found for ' . htmlspecialchars($row['name']) . '</p>';
            echo '</div>';
        }
    }
    ?>
</div>

</body>
</html>
