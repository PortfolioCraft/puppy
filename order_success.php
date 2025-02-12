<?php
// Product Details Page with Form, Dynamic Price Calculation, and Order Success Redirect
$conn = new mysqli('localhost', 'root', '', 'shop');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .product-container { display: flex; gap: 40px; }
        .image-gallery { flex: 1; }
        .product-info { flex: 1; }
        .image-gallery img { width: 100%; max-width: 300px; margin-bottom: 10px; }
        .original-price { text-decoration: line-through; color: red; }
        .discounted-price { color: green; font-size: 24px; }
        .form-container { margin-top: 20px; }
        .tabs { display: flex; gap: 10px; margin-top: 20px; }
        .tabs button { flex: 1; padding: 10px; border: none; background-color: lightgray; cursor: pointer; }
        .tabs button.active { background-color: darkgray; }
        .tab-content { display: none; margin-top: 20px; }
        .tab-content.active { display: block; }
        .similar-products { margin-top: 40px; }
        .similar-products h3 { margin-bottom: 20px; }
        .similar-product-card { border: 1px solid #ddd; padding: 10px; max-width: 250px; text-align: center; }
        .similar-product-card img { width: 100%; height: 150px; object-fit: cover; }
    </style>
</head>
<body>

<div class="product-container">
    <!-- Left Side: Image Gallery -->
    <div class="image-gallery">
        <?php 
        $images = json_decode($product['image']);
        if (!empty($images)) {
            foreach ($images as $img) {
                echo "<img src='uploads/" . htmlspecialchars($img) . "' alt='Product Image'>";
            }
        } else {
            echo "<img src='uploads/default.png' alt='Default Image'>";
        }
        ?>
    </div>

    <div class="success-container">
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your order. We will contact you shortly to confirm the delivery details.</p>

    <!-- Optional: Show order summary if needed -->
    <div class="order-summary">
        <h3>Your Order:</h3>
        <ul>
            <li>Product: <strong><?php echo htmlspecialchars($product['name']); ?></strong></li>
            <li>Quantity: <strong><?php echo htmlspecialchars($_POST['quantity']); ?></strong></li>
            <li>Total Price: <strong>Ksh <?php echo number_format($_POST['total_price'], 2); ?></strong></li>
            <li>Delivery Address: <strong><?php echo htmlspecialchars($_POST['delivery_address']); ?></strong></li>
        </ul>
    </div>
</div>

<a href="index.php" class="btn btn-primary">Back to Home</a>
</body>
</html>
