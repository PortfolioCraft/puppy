<?php
// Product Details Page with Form, Dynamic Price Calculation, and Order Success Redirect
$conn = new mysqli('localhost', 'root', '', 'shop');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details based on ID
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Product not found.";
    exit;
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

    <!-- Right Side: Product Information and Form -->
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="original-price">$<?php echo number_format($product['price'], 2); ?></p>
        <p class="discounted-price">$<?php echo number_format($product['discounted_price'], 2); ?></p>

        <!-- Order Form -->
        <form id="order-form" action="order_success.php" method="POST" class="form-container">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" required>
            </div>
            <div class="mb-3">
                <label for="delivery" class="form-label">Delivery Location</label>
                <input type="text" id="delivery" name="delivery_location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone_number" class="form-control" required>
            </div>

            <p>Total Price: <strong id="total-price">$<?php echo number_format($product['discounted_price'], 2); ?></strong></p>
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    </div>
</div>

<!-- Tabs for Product Description, Reviews, and Add Review -->
<div class="tabs">
    <button class="tab-button active" data-tab="description">Description</button>
    <button class="tab-button" data-tab="reviews">Reviews</button>
    <button class="tab-button" data-tab="add-review">Add Review</button>
</div>

<div id="description" class="tab-content active">
    <p><?php echo htmlspecialchars($product['description']); ?></p>
</div>
<div id="reviews" class="tab-content">
    <h3>Reviews</h3>
    <p>No reviews yet.</p> <!-- Replace with dynamic reviews -->
</div>
<div id="add-review" class="tab-content">
    <h3>Add a Review</h3>
    <form action="submit_review.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <div class="mb-3">
            <label for="review-name" class="form-label">Name</label>
            <input type="text" id="review-name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="review-text" class="form-label">Review</label>
            <textarea id="review-text" name="review" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<!-- Similar Products Section -->
<div class="similar-products">
    <h3>Similar Products</h3>
    <div class="row">
        <?php
        // Fetch 4 random products to display as similar products
        $similar_query = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
        while ($similar_product = $similar_query->fetch_assoc()) {
            echo "<div class='col-md-3'>";
            echo "<div class='similar-product-card'>";
            $similar_images = json_decode($similar_product['image']);
            echo "<img src='uploads/" . htmlspecialchars($similar_images[0]) . "' alt='Similar Product Image'>";
            echo "<h4>" . htmlspecialchars($similar_product['name']) . "</h4>";
            echo "<p class='original-price'>$" . number_format($similar_product['price'], 2) . "</p>";
            echo "<p class='discounted-price'>$" . number_format($similar_product['discounted_price'], 2) . "</p>";
            echo "<a href='product.php?id=" . $similar_product['id'] . "' class='btn btn-secondary'>View Product</a>";
            echo "</div></div>";
        }
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Calculate total price dynamically
    $('#quantity').on('input', function () {
        var quantity = $(this).val();
        var price = <?php echo $product['discounted_price']; ?>;
        $('#total-price').text('$' + (quantity * price).toFixed(2));
    });

    // Handle tab switching
    $('.tab-button').on('click', function () {
        var tabId = $(this).data('tab');
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });
</script>
</body>
</html>
