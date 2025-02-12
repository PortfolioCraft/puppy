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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $review = $conn->real_escape_string(trim($_POST['review']));

    // Insert review into database (pending status)
    $sql = "INSERT INTO reviews (product_id, name, review, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $product_id, $name, $review);
    
    if ($stmt->execute()) {
        echo "Review submitted successfully! It will be visible once approved.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
