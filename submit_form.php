<?php
// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'shop';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed!']));
}

// Check if form data is sent via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $service = isset($_POST['service']) ? trim($_POST['service']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $time = isset($_POST['time']) ? trim($_POST['time']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Check required fields
    if ($name && $phone && $service && $date && $time) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO appointments (name, phone, service, appointment_date, appointment_time, special_requests) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $phone, $service, $date, $time, $message);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save data!']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Some required fields are missing!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

// Close the database connection
$conn->close();
?>
