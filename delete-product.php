<?php
header('Access-Controll-Allow-Origin:*');
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
        $queryCheckSeller = $mysqli->prepare('SELECT seller_id FROM products WHERE product_id=?');
        $queryCheckSeller->bind_param('i', $product_id);
        $queryCheckSeller->execute();
        $queryCheckSeller->bind_result($seller_id);
        $queryCheckSeller->fetch();
        $queryCheckSeller->close();

        if ($seller_id == $decoded->user_id) {
            $queryDelete = $mysqli->prepare('DELETE FROM products WHERE product_id=?');
            $queryDelete->bind_param('i', $product_id);

            if ($queryDelete->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
            }

            $queryDelete->close();
        } else {
            // Unauthorized access: Product does not belong to the authenticated seller
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        }
    } else {
        // Unauthorized access: User is not a seller
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    }
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
$mysqli->close();
?>
