<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['car_id']) || !isset($_POST['payment_id'])) {
    die('Missing required fields');
}

$car_id = filter_var($_POST['car_id'], FILTER_SANITIZE_NUMBER_INT);
$payment_id = filter_var($_POST['payment_id'], FILTER_SANITIZE_STRING);

if (empty($car_id) || empty($payment_id)) {
    die('Invalid car ID or payment ID');
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE table_car_listing SET Status = 'Sold' WHERE Car_Id = ?");
$stmt->bind_param("i", $car_id);
if ($stmt->execute()) {
    echo "Car status updated successfully!";
} else {
    echo "Error updating car status: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>