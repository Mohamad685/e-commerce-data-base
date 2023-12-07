<?php
header('Access-Control-Allow-Origin: *');
include("connection.php");
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];

$query = $mysqli->prepare('UPDATE products SET product_name=?, description=?, price=?, stock_quantity=? WHERE product_id=?');
$query->bind_param('ssdii', $product_name, $description, $price, $stock_quantity, $product_id);

if ($query->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
}

$query->close();
$mysqli->close();
?>
