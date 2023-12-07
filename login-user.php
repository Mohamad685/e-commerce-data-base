<?php
header('Access-Controll-Allow-Origin:*');
include("connection.php");
require 'vendor\autoload.php';

use Firebase\JWT\JWT;

$email = $_POST['email'];
$password = $_POST['password'];
$query=$mysqli->prepare('select user_id,full_name,password,is_seller from users where email=?');
$query->bind_param('s',$email);
$query->execute();
$query->store_result();
$num_rows=$query->num_rows;
$query->bind_result($user_id,$full_name,$password_hash,$is_seller);
$query->fetch();


$response=[];
if($num_rows== 0){
    $response['status']= 'user not found';
    echo json_encode($response);
} else {
    if(password_verify($password,$password_hash)){
        $key = "your_secret";
        $payload_array = [];
        $payload_array["email"] = $email;
        $payload_array["user_id"] = $user_id;
        $payload_array["is_seller"] = $is_seller;
        $payload_array["exp"] = time() + 3600;
        $payload = $payload_array;
        $response['status'] = 'logged in';
        $jwt = JWT::encode($payload, $key, 'HS256');
        $response['jwt'] = $jwt;
        echo json_encode($response);
        
    } else {
        $response['status']= 'wrong credentials';
        echo json_encode($response);
    }
};