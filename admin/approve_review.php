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

// Check if review ID is set
if (isset($_GET['id'])) {
    $review_id = intval($_GET['id']);

    // Update the review status to approved
    $sql = "UPDATE reviews SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $review_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Review approved successfully
        $_SESSION['message'] = "Review approved successfully.";
    } else {
        // Error message if no rows were updated
        $_SESSION['message'] = "Error approving review or review already approved.";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid review ID.";
}

$conn->close();

// Redirect back to the admin reviews page
header("Location: admin_reviews.php");
exit();
?>
