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

// Pagination Logic
$limit = 10; // Limit orders per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit;

// Search and Filter Logic
$search_query = "";
$status_filter = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}
if (isset($_POST['status_filter'])) {
    $status_filter = $_POST['status_filter'];
}

// Fetch orders with search and filter logic
$sql = "SELECT * FROM orders WHERE 1";
if ($search_query != "") {
    $sql .= " AND (customer_name LIKE '%$search_query%' OR id LIKE '%$search_query%')";
}
if ($status_filter != "") {
    $sql .= " AND order_status = '$status_filter'";
}
$sql .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Fetch total number of orders for pagination
$count_sql = "SELECT COUNT(*) AS total FROM orders WHERE 1";
if ($search_query != "") {
    $count_sql .= " AND (customer_name LIKE '%$search_query%' OR id LIKE '%$search_query%')";
}
if ($status_filter != "") {
    $count_sql .= " AND order_status = '$status_filter'";
}
$count_result = $conn->query($count_sql);
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="styles.css">
    
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

        .header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 15px;
            margin-bottom: 0;
            font-size: 1.5rem;
        }

    

        h1 {
            text-align: center;
            margin-top: 30px;
            font-size: 2rem;
        }



        .order-list {
            max-width: 1200px;
            margin: 30px auto;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item:hover {
            background-color: #f1f1f1;
        }

        .order-item .order-details {
            flex: 1;
        }

        .order-item h4 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .order-item p {
            margin: 5px 0;
            color: #555;
        }

        .order-item a {
            text-decoration: none;
            color: #28a745;
        }

        .order-item a:hover {
            text-decoration: underline;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            color: #333;
            font-size: 1rem;
        }

        .pagination a:hover {
            background-color: #28a745;
            color: white;
        }

        .pagination .active {
            background-color: #28a745;
            color: white;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav a {
                margin: 0 10px;
                font-size: 0.9rem;
            }
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

<h1 style="text-align: center; font-size: 2rem; color: black; margin-bottom: 20px; 
    text-shadow: 
        2px 2px 0 rgba(255, 255, 255, 0.7), 
        -2px -2px 0 rgba(255, 255, 255, 0.7),  
        2px -2px 0 rgba(255, 255, 255, 0.7),  
        -2px 2px 0 rgba(255, 255, 255, 0.7);">
    Order List
</h1>







<!-- Search and Status Filter 
<div style="display: flex; justify-content: center; margin-top: 20px; flex-wrap: wrap;">
    <form method="POST" action="" style="display: flex; gap: 15px; width: 100%; max-width: 900px; align-items: center; justify-content: center;">
        <input type="text" name="search" placeholder="Search by Customer Name or Order ID"
               value="<?php echo htmlspecialchars($search_query); ?>"
               style="flex: 1; padding: 10px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 14px; transition: border-color 0.3s; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); max-width: 300px;">

        <select name="status_filter"
                style="padding: 10px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 14px; transition: border-color 0.3s; max-width: 180px;">
            <option value="">All Orders</option>
            <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Delivered" <?php echo ($status_filter == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
        </select>

        <button type="submit" style="padding: 10px 20px; border: none; border-radius: 25px; background-color: #FD7C09; color: white; font-size: 14px; cursor: pointer; transition: background-color 0.3s; max-width: 120px;">
            Apply
        </button>
    </form>
</div>
-->
<style>
    /* Additional styles for a modern look */
    input[type="text"],
    select {
        font-family: 'Arial', sans-serif; /* Modern font */
    }

    input[type="text"]:focus,
    select:focus {
        border-color: #007BFF; /* Theme color */
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Shadow on focus */
    }

    .apply-button {
        background-color: #FD7C09;
        transition: background-color 0.3s;
    }

    .apply-button:hover {
        background-color: #e86a00; /* Darken button on hover */
    }

    /* Ensuring alignment and responsiveness */
    @media (max-width: 768px) {
        form {
            flex-direction: column; /* Stack elements on small screens */
            gap: 10px;
            align-items: stretch; /* Align the form elements to stretch */
        }

        input[type="text"],
        select,
        .apply-button {
            width: 100%; /* Make inputs full-width on small screens */
            max-width: 100%; /* Remove max-width restrictions */
        }

        /* Adjust padding for smaller devices */
        input[type="text"],
        select,
        .apply-button {
            padding: 8px 15px; /* Slightly smaller padding */
            font-size: 13px; /* Smaller font size */
        }
    }

    @media (min-width: 769px) {
        form {
            gap: 15px; /* Larger gap on wider screens */
            justify-content: center; /* Center the form elements */
        }

        input[type="text"],
        select,
        .apply-button {
            padding: 10px 15px; /* Standard padding for larger screens */
            font-size: 14px; /* Standard font size */
        }
    }
</style>




<div class="order-list">
    <?php if ($result->num_rows > 0) { ?>
        <?php while ($order = $result->fetch_assoc()) { ?>
            <div class="order-item">
                <div class="order-details">
                    <h4>Order #<?php echo $order['id']; ?></h4>
                    <p>Customer Name: <?php echo $order['customer_name']; ?></p>
                    <p>Delivery Address: <?php echo $order['delivery_address']; ?></p>
                    <p>Status: <b><?php echo $order['order_status']; ?></b></p>
                </div>
                <a href="order_details.php?id=<?php echo $order['id']; ?>">View Details</a>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No orders found.</p>
    <?php } ?>
</div>

<!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <a href="orders.php?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php } ?>
</div>

<?php $conn->close(); ?>

</body>
</html>
