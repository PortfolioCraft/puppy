<?php
// Include database connection
include '../frontend/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if review_id and action are set
    if (isset($_POST['review_id']) && isset($_POST['action'])) {
        $review_id = intval($_POST['review_id']);
        $action = $_POST['action'];

        // Prepare the SQL query based on the action
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE reviews SET status = 'rejected' WHERE id = ?");
        } else {
            echo "Invalid action.";
            exit;
        }

        // Bind the review ID and execute the query
        $stmt->bind_param("i", $review_id);
        if ($stmt->execute()) {
            echo "Review status updated successfully.";
        } else {
            echo "Error updating review status.";
        }
    } else {
        echo "Review ID or action not specified.";
    }
}
?>

<a href="admin_reviews.php?product_id=<?php echo htmlspecialchars($_POST['product_id']); ?>">Back to Reviews</a>
