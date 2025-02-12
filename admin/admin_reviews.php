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

// Fetch all pending reviews
$sql = "SELECT * FROM reviews WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Pending Reviews</title>
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background-image: url('trial/frontend/uploads/pc-wallpapers-v0-6q5bm9ch16xc1.webp');
    background-size: cover; /* Makes the image cover the entire area */
    background-position: center; /* Centers the image */
    background-repeat: no-repeat; /* Prevents tiling */
    margin: 0;
    padding: 0;
    color: #333;
}

.header {
    background-color: #8ac926;
    color: white;
    padding: 20px;
    text-align: center;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.header h1 {
    margin: 0;
    font-size: 2.5em;
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

.reviews-container {
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.review-card {
    border-bottom: 1px solid #ddd;
    padding: 20px 0;
    transition: background-color 0.3s ease-in-out;
}

.review-card:hover {
    background-color: #f9fafb;
}

.review-card h3 {
    margin: 0;
    font-size: 1.3rem;
    color: #333;
}

.review-card p {
    color: #555;
    margin: 10px 0;
}

.review-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s, transform 0.2s;
}

.btn:hover {
    transform: translateY(-2px);
}

.approve {
    background-color: #28a745;
}

.approve:hover {
    background-color: #218838;
}

.reject {
    background-color: #dc3545;
}

.reject:hover {
    background-color: #c82333;
}

.message {
    padding: 15px;
    margin-bottom: 20px;
    color: white;
    background-color: #8ac926;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.no-reviews {
    text-align: center;
    color: #666;
    margin: 20px 0;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .header h1 {
        font-size: 2rem;
    }

  
    .reviews-container {
        padding: 15px;
    }

    .btn {
        padding: 10px 15px;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .header h1 {
        font-size: 1.8rem;
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

<div class="reviews-container">

    <?php
    // Display feedback message if available
    if (isset($_SESSION['message'])) {
        echo "<div class='message'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);  // Clear the message after displaying it
    }

    // Display pending reviews
    if ($result->num_rows > 0) {
        while ($review = $result->fetch_assoc()) {
            echo "<div class='review-card'>";
            echo "<h3>" . htmlspecialchars($review['name']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($review['review'])) . "</p>";
            echo "<p><strong>Status:</strong> " . htmlspecialchars($review['status']) . "</p>";
            echo "<p><strong>Submitted on:</strong> " . htmlspecialchars($review['created_at']) . "</p>";

            // Approve/Reject buttons
            echo "<div class='review-actions'>";
            echo "<a class='btn approve' href='approve_review.php?id=" . $review['id'] . "'>Approve</a>";
            echo "<a class='btn reject' href='reject_review.php?id=" . $review['id'] . "'>Reject</a>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-reviews'>No pending reviews found.</p>";
    }

    $conn->close();
    ?>

</div>

</body>
</html>
