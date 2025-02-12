<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'shop');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);

    // Delete images associated with the product
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($images_json);
    $stmt->fetch();
    $stmt->close();

    $images = json_decode($images_json);
    foreach ($images as $image) {
        $image_path = __DIR__ . '/../frontend/' . $image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Product deleted successfully!');</script>";
}

// Handle adding a product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $discounted_price = $_POST['discounted_price'];
    $description = $_POST['description'];
    $stock = intval($_POST['stock']);

    $images = [];
    $upload_dir = __DIR__ . '/../frontend/uploads/';

    if (!is_dir($upload_dir)) {
        echo "<script>alert('Upload directory does not exist.');</script>";
        exit;
    }

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $image_name = basename($_FILES['images']['name'][$key]);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($tmp_name, $image_path)) {
            $images[] = 'uploads/' . $image_name;
        }
    }

    $images_json = json_encode($images);
    $stmt = $conn->prepare("INSERT INTO products (name, price, discounted_price, description, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $name, $price, $discounted_price, $description, $stock, $images_json);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Product added successfully!');</script>";
}

// Fetch products
$result = $conn->query("SELECT * FROM products");
$products = $result->fetch_all(MYSQLI_ASSOC);

// Pagination variables
$products_per_page = 8;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $products_per_page;

// Fetch products with pagination
$total_products = $conn->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'];
$total_pages = ceil($total_products / $products_per_page);
$result = $conn->query("SELECT * FROM products LIMIT $offset, $products_per_page");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #8ac926;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-links a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .tab-buttons {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .tab-buttons button {
            padding: 12px 20px;
            margin: 0 5px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            background-color: #ccc;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .tab-buttons .active {
            background-color: #8ac926;
            color: white;
        }

        .tab-buttons button:hover:not(.active) {
            background-color: #d4d4d4;
        }

        .tab-content {
            display: none;
            animation: fade-in 0.5s ease-in-out;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            padding: 12px;
            margin: 10px -10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-container button {
            background-color: #8ac926;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #6ea71e;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .gallery-item {
            background: white;
            border-radius: 8px;
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.2);
        }

        .gallery-item img {
            max-width: 100%;
            border-radius: 8px 8px 0 0;
        }

        .pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 15px;
            background-color: #8ac926;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #6ea71e;
        }

        .pagination a[style*="font-weight:bold;"] {
            background-color: #6ea71e;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }

            .gallery {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .search-bar input {
                width: 90%;
            }
        }

        .search-bar {
            text-align: center;
            margin: 20px 0;
        }

        #search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px; /* Space between the input and button */
        }

        #search {
            width: 50%;
            padding: 10px 15px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s;
        }

        #search:focus {
            border-color: #8ac926;
            outline: none;
        }

        #search-form button {
            padding: 10px 15px;
            font-size: 1rem;
            color: white;
            background-color: #8ac926;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #search-form button:hover {
            background-color: #6ea71e;
        }

        @media (max-width: 768px) {
            #search {
                width: 80%; /* Adjust for smaller screens */
            }
        }
    </style>
</head>
<body>

<div class="navbar" style="background-color: #8ac926;">
    <div class="logo">Admin Panel</div>
    <div class="toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="nav-links" style="color: white;">
        <a href="dashboard.php" style="color: white;">Dashboard</a>
        <a href="orders.php" style="color: white;">Orders</a>
        <a href="products.php" style="color: white;">Products</a>
        <a href="admin_reviews.php" style="color: white;">Reviews</a>
    </div>
</div>

<div class="tab-buttons">
    <button class="active" onclick="showTab('add')">Add Product</button>
    <button onclick="showTab('view')">View Products</button>
</div>

<div id="add" class="tab-content active">
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Price" required>
            <input type="number" name="discounted_price" placeholder="Discounted Price" required>
            <textarea name="description" placeholder="Description" rows="5" required></textarea>
            <input type="number" name="stock" placeholder="Number in Stock" required>
            <input type="file" name="images[]" multiple required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

<div id="view" class="tab-content">
    <div class="search-bar">
        <input type="text" id="search" placeholder="Search products...">
    </div>
    <div class="gallery">
        <?php foreach ($products as $product): ?>
            <div class="gallery-item">
                <?php
                $images = json_decode($product['image']);
                $image_path = !empty($images) ? $images[0] : 'frontend/uploads/default.png';
                ?>
                <img src="<?php echo $image_path; ?>" alt="Product Image">
                <h3><?php echo $product['name']; ?></h3>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Delete product?');">Delete</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" <?php echo $i == $page ? 'style="font-weight:bold;"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
    function showTab(tabName) {
        const tabs = document.querySelectorAll('.tab-content');
        const buttons = document.querySelectorAll('.tab-buttons button');
        
        tabs.forEach(tab => tab.classList.remove('active'));
        buttons.forEach(button => button.classList.remove('active'));
        
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
</script>

</body>
</html>
