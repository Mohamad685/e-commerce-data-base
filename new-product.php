<?php
header('Access-Control-Allow-Origin:*');
include("connection.php");
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

$headers = getallheaders();
if (!isset($headers['Authorization']) || empty($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

$authorizationHeader = $headers['Authorization'];
$token = null;

$token = trim(str_replace("Bearer", '', $authorizationHeader));
if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}
try {
    $key = "your_secret";
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    if ($decoded->is_seller == 1) {
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        $seller_id = $decoded->user_id;  

        $query = $mysqli->prepare('INSERT INTO products (product_name, description, price, stock_quantity, seller_id) VALUES (?, ?, ?, ?, ?)');
        $query->bind_param('ssdii', $product_name, $description, $price, $stock_quantity, $seller_id);

        if ($query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Product created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create product']);
        }
    } else {
        $response = ["permissions" => false];
        echo json_encode($response);
    }
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
$query->close();
$mysqli->close();
?>
