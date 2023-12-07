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
    if ($decoded-> is_seller == 1) {
        $product_id = $_POST['product_id'];

$query = $mysqli->prepare('DELETE FROM products WHERE product_id=?');
$query->bind_param('i', $product_id);

$query -> execute();
if ($mysqli-> affected_rows>0){
    $response['message']="Product deleted successfully.";
    
}else{
    $response['message']="Error deleting the product.";
}
echo json_encode($response);

    } else {

        $response = [];
        $response["permissions"] = false;
    }
    echo json_encode($response);
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
$mysqli ->close();
?>