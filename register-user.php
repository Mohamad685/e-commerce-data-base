<?php
header('Access-Controll-Allow-Origin:*');
include("connection.php");

$username=$_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$is_seller = $_POST['is_seller'];
$full_name = $_POST['full_name'];
$address=$_POST['address'];
$phone_number =$_POST['phone_number'];

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$query = $mysqli->prepare('Insert into users(username,email,password,full_name,address,phone_number,is_seller) 
values(?,?,?,?,?,?,?)');
$query->bind_param('ssssssi', $username, $email, $password_hash, $full_name, $address,$phone_number, $is_seller);
$query->execute();

$response = [];
$response["status"] = "true";
echo json_encode($response);