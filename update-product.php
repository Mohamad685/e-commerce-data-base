<?php
header('Access-Control-Allow-Origin:*');
include("connection.php");
require __DIR__ . '/vendor/autoload.php';

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
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        
        // Ensure the product being updated belongs to the seller
        $checkOwnership = $mysqli->prepare('SELECT seller_id FROM products WHERE product_id = ?');
        $checkOwnership->bind_param('i', $product_id);
        $checkOwnership->execute();
        $checkOwnershipResult = $checkOwnership->get_result()->fetch_assoc();

        if (!$checkOwnershipResult || $checkOwnershipResult['seller_id'] != $decoded->user_id) {
            // Unauthorized access: Seller doesn't own the product
            $response = ["status" => "error", "message" => "Unauthorized access"];
            echo json_encode($response);
            exit();
        }

        $updateQuery = $mysqli->prepare('UPDATE products SET product_name=?, description=?, price=?, stock_quantity=? WHERE product_id=?');
        $updateQuery->bind_param('ssdii', $product_name, $description, $price, $stock_quantity, $product_id);

        if ($updateQuery->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
        }
    } else {
        // Unauthorized access: User is not a seller
        $response = ["status" => "error", "message" => "Unauthorized access"];
        echo json_encode($response);
    }
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}

$mysqli->close();
$checkOwnership->close();
$updateQuery->close();
?>
