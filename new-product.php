<?php
header('Access-Control-Allow-Origin: *');
include("connection.php");

$product_name = $_POST['product_name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];
$seller_id = $_POST['seller_id'];  

$query = $mysqli->prepare('INSERT INTO products (product_name, description, price, stock_quantity, seller_id) VALUES (?, ?, ?, ?, ?)');
$query->bind_param('ssdii', $product_name, $description, $price, $stock_quantity, $seller_id);

if ($query->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Product created successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create product']);
}

$query->close();
$mysqli->close();
?>
