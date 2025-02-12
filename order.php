<?php
$conn = new mysqli('localhost', 'root', '', 'shop');

// Fetch product
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    $product = $result->fetch_assoc();
} else {
    die("Product ID not provided.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title><?php echo $product['name']; ?></title>
</head>
<body>

<h1><?php echo $product['name']; ?></h1>
<img src="<?php echo $product['image']; ?>" alt="Product Image">
<p>Price: <?php echo $product['price']; ?></p>
<p>Discounted Price: <?php echo $product['discounted_price']; ?></p>

<!-- Delivery form -->
<form action="order.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
    <label for="delivery_address">Delivery Address:</label>
    <input type="text" name="delivery_address" required>
    <button type="submit">Place Order</button>
</form>

<a href="index.php">Back to Shop</a>

</body>
</html>
