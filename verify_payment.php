<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['Car_Id']) || !isset($_POST['Price'])) {
    header("Location: payment_process.php");
    exit();
}

$Car_Id = filter_var($_POST['Car_Id'], FILTER_SANITIZE_NUMBER_INT);
$Price = filter_var($_POST['Price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if (empty($Car_Id) || empty($Price)) {
    die('Invalid car ID or price');
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$_SESSION['car_id'] = $Car_Id;


$conn->close();

header("Location: car_details.php?id=$Car_Id");
exit();
?>