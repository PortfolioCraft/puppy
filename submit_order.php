<?php
$conn = new mysqli('localhost', 'root', '', 'shop');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $county = $_POST['county'];
    $quantity = 1; // Assuming you want to default to 1 for now
    
    // Fetch product price from the products table
    $product_result = $conn->query("SELECT price, discounted_price FROM products WHERE id = $product_id");
    $product = $product_result->fetch_assoc();
    
    // Use the discounted price if available, otherwise use the original price
    $price = $product['discounted_price'] ? $product['discounted_price'] : $product['price'];

    // Insert order into database
    $query = "INSERT INTO orders (product_id, customer_name, delivery_address, phone_number, county, quantity, price) 
              VALUES ('$product_id', '$name', '$address', '$phone', '$county', '$quantity', '$price')";
    
    if ($conn->query($query) === TRUE) {
        echo "Order placed successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}
?>
