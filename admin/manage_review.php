<?php
// Include the database connection
include '../frontend/db.php';

// Get the review ID and action from the form
$review_id = $_POST['review_id'];
$action = $_POST['action'];

// Prepare the SQL statement based on the action (approve/reject)
if ($action === 'approve') {
    $sql = "UPDATE reviews SET status = 'approved' WHERE id = ?";
} elseif ($action === 'reject') {
    $sql = "UPDATE reviews SET status = 'rejected' WHERE id = ?";
}

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $review_id);

if ($stmt->execute()) {
    echo "Review has been " . htmlspecialchars($action) . "d successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Redirect back to the admin page after action
header("Location: admin_reviews.php");
exit();

// Close statement and connection
$stmt->close();
$conn->close();
?>
