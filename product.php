<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product_id is passed via GET request
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Fetch product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $viewers = rand(5, 50); // Random number of viewers between 5 and 50
    $message = " Only a few left! Currently, <strong>{$viewers} other shoppers</strong> are viewing this item! Don't miss out! ";

    // Fetch approved reviews for the product
    $stmt = $conn->prepare("SELECT name, review, created_at FROM reviews WHERE product_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $approved_reviews = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get the count of reviews for this product
    $stmt = $conn->prepare("SELECT COUNT(*) AS review_count FROM reviews WHERE product_id = ? AND status = 'approved'");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($review_count);
    $stmt->fetch();
    $stmt->close();

    // Fetch similar products
    $stmt = $conn->prepare("
        SELECT p.*, 
               COALESCE(r.review_count, 0) AS review_count 
        FROM products p
        LEFT JOIN (
            SELECT product_id, COUNT(*) AS review_count
            FROM reviews
            WHERE status = 'approved'
            GROUP BY product_id
        ) r ON p.id = r.product_id
        WHERE p.id != ? 
        LIMIT 3");
        
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $similar_products_result = $stmt->get_result();
    $similar_products = $similar_products_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Product ID is missing."]);
    exit;
}

// Handle form submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $delivery_address = htmlspecialchars(trim($_POST['delivery_address']));
    $county = htmlspecialchars(trim($_POST['county']));
    $customer_name = htmlspecialchars(trim($_POST['customer_name'])); 
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));

    // Calculate total price
    $stmt = $conn->prepare("SELECT discounted_price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $total_price = $product['discounted_price'] * $quantity;

    // Insert order into the database
    $stmt = $conn->prepare("INSERT INTO orders (product_id, customer_name, delivery_address, phone_number, county, quantity, price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssid", $product_id, $customer_name, $delivery_address, $phone_number, $county, $quantity, $total_price);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order placed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error placing order."]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Handle review submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $product_id = intval($_POST['product_id']);
    $name = htmlspecialchars(trim($_POST['name']));
    $review = htmlspecialchars(trim($_POST['review']));

    if (empty($name) || empty($review)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
        exit;
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, name, review) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $product_id, $name, $review);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Review submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> <!-- Add Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="frontend/uploads/logo1.png">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
   
    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '604397305348499');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=604397305348499&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #007BFF;">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand" href="index.php" style="color: white; font-size: 30px; font-weight: bold;">
      Toy Paradise
    </a>

    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" 
      style="background-color: white;">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php" style="color: white; font-size: 1.2rem; font-weight: bold; 
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="all_products.php" style="color: white; font-size: 1.2rem; font-weight: bold; 
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Shop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php" style="color: white; font-size: 1.2rem; font-weight: bold; 
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/trial/admin/login.php" style="color: white; font-size: 1.2rem; font-weight: bold; 
          text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Include Bootstrap 5 Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
        .tabs button { flex: 1; padding: 10px; border: none; background-color: #8ac926; cursor: pointer; }
        .tabs button.active { background-color: #5c8001; }
        .tab-content { display: none; margin-top: 20px; }
        .tab-content.active { display: block; }
        .similar-products { margin-top: 40px; }
        .similar-products h3 { margin-bottom: 20px; }
        .similar-product-card { border: 1px solid #ddd; padding: 10px; max-width: 250px; text-align: center; }
        .similar-product-card img { width: 100%; height: 150px; object-fit: cover; }
       
        .product-container {
        display: flex;
        justify-content: center; /* Center the flex items */
        align-items: center; /* Align items vertically */
        flex-wrap: wrap; /* Allow wrapping if needed */
        gap: 40px; /* Space between columns */
    }

    .image-gallery {
        flex: 1;
        display: flex;
        flex-direction: column; /* Align items in a column */
        align-items: center; /* Center align items */
        max-width: 100%; /* Ensure gallery does not exceed screen width */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); /* Shadow effect */
        padding: 10px; /* Padding for gallery */
        border-radius: 5px; /* Round corners */
        background-color: #fff; /* Background color for contrast */
    }

    .main-image {
        margin-bottom: 10px;
        display: flex;
        justify-content: center; /* Center main image */
    }

    .large-image {
        width: 100%; /* Make main image responsive */
        max-width: 90%; /* Ensure it occupies 90% of the container width */
        height: auto; /* Maintain aspect ratio */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); /* Shadow for main image */
        border-radius: 5px; /* Round corners */
    }

    .thumbnails {
        display: flex; /* Flex layout for thumbnails */
        justify-content: center; /* Center align thumbnails */
        flex-wrap: nowrap; /* Keep thumbnails in a single row */
        overflow-x: auto; /* Enable horizontal scrolling */
        margin-top: 10px; /* Space above thumbnails */
    }

    .thumbnail {
        width: 60px; /* Fixed width for thumbnails */
        height: 60px; /* Fixed height for thumbnails */
        object-fit: cover; /* Maintain aspect ratio */
        margin: 5px; /* Space between thumbnails */
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s; /* Smooth transition */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Shadow for thumbnails */
    }

    .thumbnail:hover {
        transform: scale(1.1); /* Enlarge thumbnail on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4); /* Stronger shadow on hover */
    }

    .thumbnail.selected {
        border: 2px solid blue; /* Highlight selected thumbnail */
        opacity: 0.8; /* Dim the selected image */
    }

    @media (max-width: 767.98px) {
        .product-container {
            flex-direction: column; /* Stack elements on small screens */
            align-items: center; /* Center align items */
        }

        .thumbnail {
            width: 60px; /* Smaller thumbnail size on small screens */
            height: 60px; /* Adjust height accordingly */
        }

        .large-image {
            max-width: 100%; /* Ensure main image fits within the container */
        }
    }

    @media (min-width: 768px) {
        .large-image {
            max-width: 100%; /* Set a larger size for larger screens */
            height: auto; /* Maintain aspect ratio */
        }
    }


    
    /* Navbar styling */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #f2e8cf;
      position: fixed;
      width: 100%;
      top: 0;
      left: 0;
      z-index: 10;
    }

    .navbar-brand img {
      width: 150px;
    }

    .navbar-links {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .navbar-links li {
      margin-left: 30px;
    }

    .navbar-links a {
      color: #FD7C09;
      text-decoration: none;
      font-size: 1.2rem;
      font-weight: bold;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
      transition: color 0.3s;
      white-space: nowrap; /* Prevent text from wrapping */
    }

    .navbar-links a:hover {
      color: #f14e00;
    }

    /* Styling the toggle (hamburger) button */
    .navbar-toggler {
      display: none;
      flex-direction: column;
      cursor: pointer;
      margin-left: auto;
    }

    .navbar-toggler span {
      width: 30px;
      height: 3px;
      background-color: #333;
      margin: 5px 0;
      transition: all 0.3s ease;
    }

    /* Media query for mobile view */
    @media (max-width: 768px) {
      .navbar-links {
        position: absolute;
        top: 60px;
        left: -100%;
        width: 100%;
        height: 100vh;
        background-color: #f2e8cf;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: left 0.3s ease;
      }

      .navbar-links.active {
        left: 0;
      }

      .navbar-links li {
        margin: 20px 0;
      }

      .navbar-toggler {
        display: flex;
      }

      /* Transition effect for the hamburger icon */
      .navbar-toggler.open span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
      }

      .navbar-toggler.open span:nth-child(2) {
        opacity: 0;
      }

      .navbar-toggler.open span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
      }
    }
    
    </style>

<br><br>






<style>

#description {
    text-align: center;
}

/* Center-align text in the reviews section */
#reviews {
    text-align: center;
}

#reviews p {
    margin: 0 auto;
}

/* Center-align and reduce the width of the review form */
#add-review form {
    max-width: 500px; /* Set a max width for the form */
    margin: 0 auto; /* Center the form */
    text-align: left; /* Align form text to the left */
}

#add-review h3 {
    text-align: center; /* Center the heading of the form */
}

#add-review .form-label, 
#add-review .form-control {
    text-align: left; /* Keep form labels and controls left-aligned */
}

/* Style for Similar Products Section */
.similar-products {
    margin-top: 40px;
    text-align: center;
}

.similar-products h2 {
    margin-bottom: 20px;
    font-size: 1.8rem;
}

/* Flexbox layout for product cards */
.product-container1 {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.product-card1 {
    border: 1px solid #ddd;
    padding: 10px;
    width: 220px; /* Smaller card width */
    text-align: center;
    background-color: #f9f9f9;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card1:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.product-image1 {
    width: 100%;
    height: 150px;
    object-fit: cover;
    margin-bottom: 10px;
}

.product-details h3 {
    font-size: 1.2rem;
    margin: 10px 0;
}

.original-price {
    font-size: 0.9rem;
    text-decoration: line-through;
    color: #a00;
}

.discounted-price {
    font-size: 1.1rem;
    color: green;
}

.view-product {
    display: inline-block;
    margin-top: 10px;
    color: white;
    background-color: #28a745;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}

.view-product:hover {
    background-color: #218838;
}

/* Responsive design for smaller devices */
@media (max-width: 767.98px) {
    .product-container1 {
        flex-direction: column;
        align-items: center;
    }

    .product-card1 {
        width: 80%; /* Make cards wider on small screens */
        max-width: 300px;
    }
}
.price-container {
        display: flex; /* Flexbox for prices */
        gap: 15px; /* Space between prices */
        text-align: left;
        margin-bottom: 15px; /* Margin below price section */
    }


    .reviews-section {
    margin-top: 40px;
    text-align: center;
}

.reviews-section h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 20px;
}


.reviews-list {
    max-width: 600px; /* Adjust to your desired width */
    margin: auto; /* Center the list */
    padding: 10px;
}

.review-bubble {
    background-color: #f1f1f1; /* Light grey background for bubbles */
    border-radius: 10px; /* Rounded corners */
    padding: 10px; /* Padding inside bubbles */
    margin-bottom: 15px; /* Space between bubbles */
    position: relative; /* For positioning the pseudo-element */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional shadow effect */
}

.review-bubble::before {
    content: "";
    position: absolute;
    bottom: 100%; /* Position above the bubble */
    left: 20px; /* Adjust as needed */
    border-width: 10px; /* Size of the triangle */
    border-style: solid;
    border-color: transparent transparent #f1f1f1 transparent; /* Triangle color matches bubble */
}

.review-content h4 {
    margin: 0; /* Remove default margin */
    font-weight: bold; /* Make name bold */
}

.review-content p {
    margin: 5px 0; /* Add spacing above/below review text */
}

.review-content small {
    color: #888; /* Lighter color for date */
    font-size: 0.9rem; /* Slightly smaller text */
}


</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add click event listeners to all thumbnail images
        const thumbnails = document.querySelectorAll('.thumbnails img');
        const mainImage = document.querySelector('.main-image img');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function () {
                // Update the src of the main image
                mainImage.src = this.src;
            });
        });
    });
</script>


<?php
function decodeAndValidateImages($imageJson, $defaultImage = 'uploads/default.png') {
    // Decode the JSON string into an array
    $images = json_decode($imageJson, true);

    // Ensure it is a valid array and filter out empty values
    if (is_array($images)) {
        $images = array_filter($images, fn($img) => !empty($img));
    } else {
        $images = [];
    }

    // If no valid images, return the default
    if (empty($images)) {
        $images[] = $defaultImage;
    }

    return $images;
}
?>



<div class="product-container row" style="padding-top: 50px;">
<div class="image-gallery col-12 col-md-6 order-md-1">

<div class="main-image">
<?php 
    $images = json_decode($product['image']);
    if (!empty($images) && isset($images[0])) {
        $mainImage = htmlspecialchars($images[0]);
        echo "<img src='$mainImage' alt='Product Image' class='product-image'>";
    } else {
        echo "<img src='uploads/default.png' alt='Default Image' class='product-image'>";
    }
    ?>
</div>

<div class="thumbnails">
<?php foreach ($images as $img): ?>
        <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail" class="thumbnail" 
             onclick="changeMainImage('<?php echo htmlspecialchars($img); ?>')">
    <?php endforeach; ?>
</div>



</div>

    <!-- Right Side: Product Information and Form -->
    <div class="product-info col-12 col-md-6 order-md-2">
        <h1 style="font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="price-container">
     <!--   <p class="original-price" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Ksh<?php echo number_format($product['price'], 2); ?></p> -->
        <p class="discounted-price" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Ksh<?php echo number_format($product['discounted_price'], 2); ?></p><p style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;"><?php echo $review_count; ?> reviews</p>
    </div>
    <p style="font-weight: bold; color: black; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;"><?= $message ?></p>
    <h3 style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; font-size: 20px; color: red;">Simply Enter Your Delivery Details Below, and We'll Bring Your Order to You!</h3>
        <!-- Order Form -->
        <div class="container">
        <form id="orderForm" action="product.php?id=<?php echo $product_id; ?>" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"> <!-- Ensuring product_id is passed -->
        <input type="hidden" name="action" value="place_order">

        <div class="form-group">
            <label for="quantity" class="form-label"  style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;" required>
        </div>

        <div class="form-group">
            <label for="customer_name" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" class="form-control"  style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;" required>
        </div>

        <div class="form-group">
            <label for="delivery" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Delivery Address</label>
            <input type="text" id="delivery" name="delivery_address"  style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="county" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">County:</label>
            <select name="county" class="form-select"  style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;" required>
                <option value=""  style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;">Select County</option>
                <option value="Nairobi">Nairobi</option>
    <option value="Mombasa">Mombasa</option>
    <option value="Kisumu">Kisumu</option>
    <option value="Nakuru">Nakuru</option>
    <option value="Eldoret">Uasin Gishu</option>
    <option value="Kakamega">Kakamega</option>
    <option value="Kericho">Kericho</option>
    <option value="Machakos">Machakos</option>
    <option value="Nyeri">Nyeri</option>
    <option value="Meru">Meru</option>
    <option value="Embu">Embu</option>
    <option value="Kitui">Kitui</option>
    <option value="Kilifi">Kilifi</option>
    <option value="Bomet">Bomet</option>
    <option value="Homa Bay">Homa Bay</option>
    <option value="Narok">Narok</option>
    <option value="Laikipia">Laikipia</option>
    <option value="Nyandarua">Nyandarua</option>
    <option value="Kajiado">Kajiado</option>
    <option value="Taita Taveta">Taita Taveta</option>
    <option value="Bungoma">Bungoma</option>
    <option value="Busia">Busia</option>
    <option value="Vihiga">Vihiga</option>
    <option value="West Pokot">West Pokot</option>
    <option value="Trans Nzoia">Trans Nzoia</option>
    <option value="Nandi">Nandi</option>
    <option value="Isiolo">Isiolo</option>
    <option value="Samburu">Samburu</option>
    <option value="Marsabit">Marsabit</option>
    <option value="Garissa">Garissa</option>
    <option value="Wajir">Wajir</option>
    <option value="Mandera">Mandera</option>
    <option value="Lamu">Lamu</option>
    <option value="Kilifi">Kilifi</option>
    <option value="Tana River">Tana River</option>
    <option value="Makueni">Makueni</option>
    <option value="Kakamega">Kakamega</option>
    <option value="Siaya">Siaya</option>
    <option value="Bomet">Bomet</option>
    <option value="Migori">Migori</option>
                <!-- Add other counties here -->
            </select>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" class="form-control"  style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal;" required>
        </div>

        <p style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Total Price: <strong id="total-price">Ksh <?php echo isset($product) ? number_format($product['discounted_price'], 2) : '0.00'; ?></strong></p>
        <button type="submit" class="btn btn-success"  
                style="background-color:  #007BFF; font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;"
                data-product-id="<?php echo $product_id; ?>"  
                onclick="trackOrder(this);">
                Order Now
            </button>
<script type="text/javascript">
       

        function trackOrder(button) {
    var productId = button.getAttribute('data-product-id');
    var orderValue = <?php echo $productPrice; ?>; // Dynamically set the value

    console.log('Tracking Order:', {
        productId: productId,
        value: orderValue
    });

    fbq('track', 'Purchase', {
        currency: 'USD',
        value: orderValue,
        content_type: 'product',
        content_ids: [productId]
    });
}

        </script>

       <br><br>
        <div id="notification" style="display:none; color: green; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;"></div>
        <div id="error" style="display:none; color: red; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;"></div>
    </form>

</div>

    </div>
</div>

<script>
  document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission (page reload)

    var formData = new FormData(this); // Get the form data

    // Send the form data using AJAX to the same PHP file
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('notification').textContent = data.message;
            document.getElementById('notification').style.display = 'block'; // Show success message
            document.getElementById('error').style.display = 'none'; // Hide error message
        } else {
            document.getElementById('error').textContent = data.message;
            document.getElementById('error').style.display = 'block'; // Show error message
            document.getElementById('notification').style.display = 'none'; // Hide success message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('error').textContent = 'An error occurred while placing the order.';
        document.getElementById('error').style.display = 'block'; // Show error message on failure
    });
});
function toggleMenu() {
    var navLinks = document.getElementById("navLinks");
    if (navLinks.style.display === "flex") {
        navLinks.style.display = "none";
    } else {
        navLinks.style.display = "flex";
    }
}
</script>

<!--
<script>
 $(document).ready(function () {
    var unitPrice = <?php echo isset($product) ? $product['discounted_price'] : 0; ?>;

    // Update total price based on quantity
    $('#quantity').on('input', function () {
        var quantity = $(this).val();
        var totalPrice = (unitPrice * quantity).toFixed(2);
        $('#total-price').text('Ksh ' + totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    });

    // Handle form submission with AJAX
    $('#order-form').submit(function (event) {
        event.preventDefault(); // Prevent the form from submitting traditionally

        // Gather form data
        var formData = $(this).serialize();

        // AJAX request to submit the order
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                $('#notification').show().css('color', jsonResponse.status === 'success' ? 'green' : 'red').text(jsonResponse.message); // Show success or error message
                if (jsonResponse.status === 'success') {
                    $('#order-form')[0].reset(); // Reset the form fields on success
                }
            },
            error: function () {
                $('#notification').show().css('color', 'red').text('Error placing order. Please try again.'); // Show error message
            }
        });
    });
});

</script>
-->

<hr>
<br><br>
<!-- Tabs for Product Description, Reviews, and Add Review -->
<div class="tabs">
    <button class="tab-button active" data-tab="description" style="color: white; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; ">Description</button>
    <button class="tab-button" data-tab="reviews" style="color: white; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Reviews</button>
    <button class="tab-button" data-tab="add-review" style="color: white; font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">Add Review</button>
</div>
<div id="description" class="tab-content active" style="margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <div style="font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal; line-height: 1.6; color: #333;">
        <?php echo $product['description']; ?>
    </div>
</div>

<div id="reviews" class="tab-content">
    <div class="reviews-section">
        <h2 style="font-family: 'DM Serif Text', serif; font-weight: 400;">Customer Reviews</h2>

        <?php if (empty($approved_reviews)): ?>
            <p style="font-family: 'DM Serif Text', serif;">No reviews yet. Be the first to review this product!</p>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($approved_reviews as $review): ?>
                    <div class="review-bubble" style="border-bottom: 1px solid #ccc; padding: 10px;">
                        <div class="review-content">
                            <h4 style="color: #FD7C09; font-size: 15px;"><?php echo htmlspecialchars($review['name']); ?></h4>
                            <p style="font-family: 'Fira Sans', sans-serif;"><?php echo htmlspecialchars($review['review']); ?></p>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div id="add-review" class="tab-content">
    <h3 style="font-family: 'Prompt', sans-serif; font-weight: 400;">Add a Review</h3>
    <form id="review-form" action="admin/submit_review.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        <div class="mb-3">
            <label for="review-name" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400;">Name</label>
            <input type="text" id="review-name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="review-text" class="form-label" style="font-family: 'Prompt', sans-serif; font-weight: 400;">Review</label>
            <textarea id="review-text" name="review" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="background-color:  #007BFF; font-family: 'DM Serif Text', serif; font-weight: 400;">Submit Review</button>
    </form>
    
    <!-- Notification Div -->
    <div id="review-notification" style="display:none; padding: 10px; margin-top: 10px; text-align: center;"></div>
</div>

<script>
$(document).ready(function() {
    $('#review-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        var formData = $(this).serialize();

        // AJAX request to submit the review
        $.ajax({
            type: 'POST',
            url: 'admin/submit_review.php', // Ensure the path is correct
            data: formData,
            success: function(response) {
                $('#review-notification').show().text(response).css('color', 'green'); // Show success message
                $('#review-form')[0].reset(); // Reset the form fields
            },
            error: function(xhr, status, error) {
                $('#review-notification').show().text('Error submitting review. Please try again.').css('color', 'red'); // Show error message
            }
        });
    });
});
</script>






<hr>
<br><br>
<!-- Similar Products Section -->
<div class="similar-products">
    <h2 style="font-family: 'DM Serif Text', serif; font-weight: 400; font-style: normal;">Similar Products</h2>
    <div class="product-container1">
        <?php foreach ($similar_products as $similar_product) { ?>
        <div class="product-card" style="background-color: #ade8f4;">
            <?php 
            // Decode the image JSON array stored in the database
            $images = json_decode($similar_product['image']);
            $imgPath = !empty($images) && isset($images[0]) ? htmlspecialchars($images[0]) : 'uploads/default.png';
            ?>
            <img src="<?php echo $imgPath; ?>" alt="Product Image" class="product-image">
            <div class="product-details">
                <h4 class="product-name"><?php echo htmlspecialchars($similar_product['name']); ?></h4>
                                <p style=" font-family: 'Fira Sans', sans-serif; font-weight: 400; font-style: normal; color: #007BFF;"><?php echo $similar_product['review_count']; ?> reviews</p>
                <p class="discounted-price">Ksh<?php echo number_format($similar_product['discounted_price'], 2); ?></p>
                <a class="add-to-cart" href="product.php?id=<?php echo $similar_product['id']; ?>" class="btn" style="background-color:  #007BFF;">View Product</a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script>
    // JavaScript for toggle functionality
    const navbarToggler = document.getElementById('navbarToggler');
    const navbarLinks = document.getElementById('navbarLinks');

    navbarToggler.addEventListener('click', () => {
      navbarLinks.classList.toggle('active');
    });
  </script>

<br><br>
<footer class="site-footer" style="background-color: #ade8f4;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h6 style="color: rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">About</h6>
                <p class="text-justify" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; color: black;">
                    Welcome to <strong>Toy Paradise</strong>, your one-stop shop for quality toys that bring smiles to kids and families! 
                    From educational toys to the latest fun gadgets, we ensure every product meets the highest standards of quality. 
                    <a href="https://www.thegreaterrandservices.co.ke/" style="color: red;">Enjoy fast and reliable delivery</a> in less than <strong>48 hours</strong> to make every moment special. 
                    Shop with confidence and let us deliver happiness to your doorstep!
                </p>
                <!-- Social Media Links -->
                <div class="social-icons" style="margin-top: 10px;">
                <a href="https://wa.me/254112809190" target="_blank" style="margin-right: 10px; color: rgb(91, 224, 57); font-size: 1.5rem;">
    <i class="fa-brands fa-whatsapp"></i>
</a>
                   <a href="https://www.instagram.com/toys_puppyparadise/profilecard/?igsh=MTduYzE5dHBiOXlhZg==" target="_blank" style="margin-right: 10px; color: #E1306C; font-size: 1.5rem;">
                        <i class="fa-brands fa-instagram"></i>
                    </a> 
                    <a href="https://www.tiktok.com/@toys_puppy_paradise?_t=ZM-8sI5b7NOELd&_r=1" target="_blank" style="margin-right: 10px; color:rgb(241, 9, 9); font-size: 1.5rem;">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    
                </div>
            </div>

            <div class="col-xs-6 col-md-3">
                <h6 style="color: rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Legal Informations</h6>
                <ul class="footer-links" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal; color: white;">
                    <li><a href="#">Privacy Policies</a></li>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Terms Of Service</a></li>
                    <li><a href="#">Contact Infomation</a></li>
                </ul>
            </div>

            <div class="col-xs-6 col-md-3">
                <h6 style="color:rgb(15, 75, 4); font-size: 1.2rem; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); transition: color 0.3s;">Quick Links</h6>
                <ul class="footer-links" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="all_products.php">Shop</a></li>
                    <li><a href="Contact.php">Contact Us</a></li>
                    <li><a href="pet.html">Puppy Paradise</a></li>
                </ul>
            </div>
        </div>
        <hr>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-6 col-xs-12" style="font-family: 'Prompt', sans-serif; font-weight: 400; font-style: normal;">
                <p class="copyright-text" style="text-align: center; color: black;">Copyright &copy; <span id="current-year">2024</span> All Rights Reserved by 
                    <a href="#">Toy Paradise</a>.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (for positioning the dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Bootstrap 4 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>


<!-- Your custom script -->
<script src="script.js"></script>


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

    function changeMainImage(img) {
        document.getElementById('mainImage').src = img; // Change the main image
    }
</script>
<script>
    // Calculate total price dynamically
    $('#quantity').on('input', function () {
        var quantity = $(this).val();
        var price = <?php echo $product['discounted_price']; ?>;
        $('#total-price').text('Ksh' + (quantity * price).toFixed(2));
    });

    // Handle tab switching
    $('.tab-button').on('click', function () {
        var tabId = $(this).data('tab');
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    function changeMainImage(img) {
    // Change the main image source
    document.getElementById('mainImage').src = img;

    // Remove the 'selected' class from all thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => thumb.classList.remove('selected'));

    // Add the 'selected' class to the currently clicked thumbnail
    // Match against the original image URL
    const selectedThumbnail = Array.from(thumbnails).find(thumb => thumb.src === img);
    if (selectedThumbnail) {
        selectedThumbnail.classList.add('selected');
    }
}

function toggleMenu() {
    var navLinks = document.getElementById("navLinks");
    if (navLinks.style.display === "flex") {
        navLinks.style.display = "none";
    } else {
        navLinks.style.display = "flex";
    }
}

</script>

</body>
</html>
