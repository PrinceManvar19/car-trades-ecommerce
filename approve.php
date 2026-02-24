<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    die('Unauthorized access - Admin only');
}

if (!isset($_GET['Car_Id'])) {
    header('Location: Adminpage2.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Car_Id = filter_var($_GET['Car_Id'], FILTER_SANITIZE_NUMBER_INT);

$stmt = $conn->prepare("UPDATE table_car_listing SET Status = 'Approved' WHERE Car_Id = ?");
$stmt->bind_param("i", $Car_Id);
$stmt->execute();
$stmt->close();

$conn->close();

header('Location: Adminpage2.php');
exit;
?>