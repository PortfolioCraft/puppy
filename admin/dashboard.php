<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders and display them, including order_status
$orders = $conn->query("SELECT id, order_status FROM orders WHERE order_status != 'Completed'");

if (!$orders) {
    echo "Error: " . $conn->error; // Show error if query fails
}

// Fetch all products
$products = $conn->query("SELECT COUNT(*) AS product_count FROM products");

$appointments = $conn->query("SELECT COUNT(*) AS appointment_count FROM appointments");


// Fetch all reviews awaiting confirmation
$reviews = $conn->query("SELECT COUNT(*) AS review_count FROM reviews WHERE status = 'Pending'");

// Array of background images
$backgroundImages = [
    '/trial/frontend/uploads/1600w-KBBZLdpjLcM.webp',
    '/trial/frontend/uploads/1078546434321809412.jpg',
    '/trial/frontend/uploads/beautiful-nature-desktop-1920x1200-wallpaper-preview.jpg',
    '/trial/frontend/uploads/e59c829455bd9cdc12930cb72c7253f3.jpg',
    '/trial/frontend/uploads/pc-wallpapers-v0-6q5bm9ch16xc1.webp',
];

// Pick a random image
$randomBackground = $backgroundImages[array_rand($backgroundImages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="frontend/uploads/logo1.png">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('<?php echo $randomBackground; ?>') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
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


h1 {
    text-align: center;
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
}



        /* Main Dashboard Container */
        .dashboard-container {
            padding: 20px;
            max-width: 1000px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            margin: 20px auto;
            width: 90%;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .card p {
            margin: 0;
            font-size: 1rem;
            color: #555;
        }

        .counter {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        .card a {
            color: #8ac926;
            text-decoration: none;
            font-size: 1rem;
            margin-top: 10px;
            display: inline-block;
        }

        .card a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .card h3 {
                font-size: 1.1rem;
            }

            .dashboard-container {
                width: 95%;
                margin: 10px auto;
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

    <div class="dashboard-container" id="dashboard">
        <h1>Admin Dashboard</h1>

        <!-- Orders Section -->
        <div class="card">
            <h3>Orders</h3>
            <p>Total Orders (Not Delivered):<b> <?php echo mysqli_num_rows($orders); ?></b></p>
            <a href="orders.php">View Orders</a>
        </div>

        <!-- Products Section -->
        <div class="card">
            <h3>Products</h3>
            <p>Total Products Listed: <b><?php echo mysqli_fetch_assoc($products)['product_count']; ?></b></p>
            <a href="products.php">Manage Products</a>
        </div>

        <!-- Reviews Section -->
        <div class="card">
            <h3>Reviews</h3>
            <p>Total Reviews Awaiting Confirmation:<b> <?php echo mysqli_fetch_assoc($reviews)['review_count']; ?></b></p>
            <a href="admin_reviews.php">Manage Reviews</a>
        </div>

        <div class="card">
            <h3>Puppy Paradise Appointments</h3>
            <p>New Appointments:<b> <?php echo mysqli_fetch_assoc($appointments)['appointment_count']; ?></b></p>
            <a href="appointments.php">Manage Appointments</a>
        </div>
    </div>


    <script> 
        document.getElementById('menu-toggle').addEventListener('click', function() {
    document.querySelector('.navbar').classList.toggle('open');
});

    </script>
</body>
</html>
