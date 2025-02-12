<?php
$conn = new mysqli('localhost', 'root', '', 'shop');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $discounted_price = $_POST['discounted_price'];
    $description = $_POST['description']; // Get description from form
    $existing_images = json_decode($_POST['existing_images'], true); // Get existing images from hidden input

    // Handle image uploads (multiple files)
    $new_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image = basename($_FILES['images']['name'][$key]);
            $target_dir = "../uploads/";
            $target_file = $target_dir . preg_replace('/\s+/', '_', $image); // Normalize file name
            
            // Upload the image
            if (move_uploaded_file($tmp_name, $target_file)) {
                $new_images[] = "uploads/" . $image; // Store relative path
            }
        }
    }

    // Merge new images with existing ones, if they exist
    $all_images = array_merge($existing_images, $new_images);

    // Remove selected images
    if (!empty($_POST['remove_images'])) {
        $remove_images = $_POST['remove_images'];
        foreach ($remove_images as $img_to_remove) {
            // Remove from the all_images array
            if (($key = array_search($img_to_remove, $all_images)) !== false) {
                unset($all_images[$key]);
                // Optionally, delete the file from the server
                if (file_exists("../" . $img_to_remove)) {
                    unlink("../" . $img_to_remove);
                }
            }
        }
    }

    // Convert images array to JSON for storage in the database
    $images_json = json_encode(array_values($all_images));

    // Prepare the SQL statement to update product details
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, discounted_price=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $price, $discounted_price, $description, $images_json, $id);
    
    $stmt->execute();
    $stmt->close();

    header("Location: products.php"); // Redirect after update
}

// Fetch product for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    $product = $result->fetch_assoc();
    $existing_images = json_decode($product['image'], true); // Decode JSON to array
} else {
    die("Product ID not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .nav {
            background-color: #8ac926;
            text-align: center;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: bold;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        form {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        form input, form textarea, form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        form button {
            background-color: #28a745;
            color: white;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        form button:hover {
            background-color: #218838;
        }

        .current-images {
            margin-bottom: 20px;
        }

        .current-images img {
            max-width: 100px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .current-images label {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .current-images input {
            margin-right: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin: 20px auto;
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                padding: 15px;
            }

            .current-images img {
                max-width: 80px;
            }

            form input, form textarea, form button {
                font-size: 0.9rem;
            }

            form button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>



<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="products.php">Products</a>
    <a href="orders.php">Orders</a>
    <a href="admin_reviews.php">Manage Reviews</a>
</div>
<form action="edit_product.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="existing_images" value='<?php echo json_encode($existing_images); ?>'>

    <label for="name">Product Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

    <label for="price">Price:</label>
    <input type="text" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

    <label for="discounted_price">Discounted Price:</label>
    <input type="text" name="discounted_price" value="<?php echo htmlspecialchars($product['discounted_price']); ?>">

    <label for="description">Product Description:</label>
    <textarea name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>

    <label for="stock">Stock Quantity:</label>
    <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? 0); ?>" required>

    <label for="images">Add New Images:</label>
    <input type="file" name="images[]" multiple>

    <div class="current-images">
        <p>Current Images:</p>
        <?php foreach ($existing_images as $img) { ?>
            <div>
                <img src="../<?php echo $img; ?>" alt="Product Image">
                <label>
                    <input type="checkbox" name="remove_images[]" value="<?php echo $img; ?>"> Remove
                </label>
            </div>
        <?php } ?>
    </div>

    <button type="submit">Update Product</button>
</form>

<a class="back-link" href="products.php">Back to Product List</a>

</body>
</html>
