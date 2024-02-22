<?php
// Establish a database connection (mysqli or PDO)
$db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$telecaller_id = $_POST['telecaller'];
$order_count = $_POST['orderCount'];
$order_date = date("Y-m-d"); // Get the current date

// Insert data into the orders table with the current date
$insert_query = "INSERT INTO orders (telecaller_id, order_date, order_count) VALUES (?, ?, ?)";
$stmt = $db->prepare($insert_query);
$stmt->bind_param("iss", $telecaller_id, $order_date, $order_count);

if ($stmt->execute()) {
    // Orders have been successfully submitted
    echo "Your orders have been submitted!";
} else {
    // Handle the case where the insertion fails
    echo "Error: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
