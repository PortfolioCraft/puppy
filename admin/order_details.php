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

// Fetch the order details
$order_id = $_GET['id'];
$order_query = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$order_query->bind_param("i", $order_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows == 0) {
    echo "Order not found.";
    exit();
}

$order = $order_result->fetch_assoc();

// Handle order status update (change status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['order_status'];
        
        // Update the order status
        $update_order_query = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $update_order_query->bind_param("si", $new_status, $order_id);

        if ($update_order_query->execute()) {
            // Reduce stock for the product if the order is completed
            if ($new_status == 'completed') {
                $product_id = $order['product_id'];
                $quantity = $order['quantity'];
                
                $product_query = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $product_query->bind_param("ii", $quantity, $product_id);
                $product_query->execute();
            }
            header("Location: orders.php"); // Redirect back to orders page
            exit();
        } else {
            echo "Failed to update order status.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f7;
            margin: 0;
            padding: 0;
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

        h2 {
            text-align: center;
            color: #333;
            margin-top: 30px;
        }

        .order-details {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 8px;
        }

        .order-details p {
            font-size: 1rem;
            color: #555;
        }

        .order-details strong {
            color: #333;
        }

        form button {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #218838;
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            color: #28a745;
            text-decoration: none;
            font-size: 1rem;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }

        select {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
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

<h2>Order Details: Order #<?php echo $order['id']; ?></h2>

<div class="order-details">
    <p><strong>Customer Name:</strong> <?php echo $order['customer_name']; ?></p>
    <p><strong>Delivery Address:</strong> <?php echo $order['delivery_address']; ?></p>
    <p><strong>Phone Number:</strong> <?php echo $order['phone_number']; ?></p>
    <p><strong>County:</strong> <?php echo $order['county']; ?></p>
    <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
    <p><strong>Price:</strong> Ksh <?php echo number_format($order['price'], 2); ?></p>
    <p><strong>Order Status:</strong> <?php echo ucfirst($order['order_status']); ?></p>
</div>

<!-- Order Status Update Form -->
<form method="POST">
    <label for="order_status">Update Order Status</label>
    <select name="order_status" id="order_status">
        <option value="pending" <?php echo ($order['order_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="completed" <?php echo ($order['order_status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="cancelled" <?php echo ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
    </select>

    <button type="submit" name="update_status">Update Status</button>
</form>

<div class="back-btn">
    <a href="orders.php">Back to Orders List</a>
</div>

<?php $conn->close(); ?>

</body>
</html>
