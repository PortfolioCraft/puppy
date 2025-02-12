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

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search logic
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch appointments
$sql = "SELECT * FROM appointments WHERE name LIKE '%$search_query%' LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Total appointments for pagination
$total_sql = "SELECT COUNT(*) AS total FROM appointments WHERE name LIKE '%$search_query%'";
$total_result = $conn->query($total_sql);
$total_appointments = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_appointments / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        h1 { text-align: center; color: #333; }
        .search-bar { margin-bottom: 20px; text-align: center; }
        .search-bar input { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 5px; }
        .search-bar button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .appointment-card { background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .appointment-card h3 { margin: 0; color: #007bff; }
        .toggle-btn { margin-top: 10px; padding: 5px 10px; background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a { padding: 10px; margin: 5px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .pagination a.active { background-color: #0056b3; }
        .details { display: none; margin-top: 10px; }
        .back-btn { margin-bottom: 20px; padding: 10px 15px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Appointments</h1>

    <!-- Back Button -->
    <div>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="GET" action="appointments.php">
            <input type="text" name="search" placeholder="Search by Name" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Appointment List -->
    <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="appointment-card">
            <p>Name: <?php echo htmlspecialchars($row['name']); ?></p>
            <p>Date: <?php echo htmlspecialchars($row['appointment_date']); ?></p>

            <!-- Button to toggle details -->
            <button class="toggle-btn" onclick="toggleDetails(this)">Show More..</button>
            
            <!-- Hidden appointment details -->
            <div class="details">
                <p>Time: <?php echo htmlspecialchars($row['appointment_time']); ?></p>
                <p>Service: <?php echo htmlspecialchars($row['service']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($row['phone']); ?></p>
                <p>Message: <?php echo isset($row['special_requests']) && !empty($row['special_requests']) ? 
                             htmlspecialchars($row['special_requests']) : 'No message provided'; ?></p>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No appointments found.</p>
<?php endif; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="appointments.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" 
               class="<?php echo $page == $i ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- JavaScript for Show/Hide -->
    <script>
        function toggleDetails(button) {
            const details = button.nextElementSibling;
            if (details.style.display === "none" || details.style.display === "") {
                details.style.display = "block";
                button.textContent = "Show Less..";
            } else {
                details.style.display = "none";
                button.textContent = "Show More..";
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
