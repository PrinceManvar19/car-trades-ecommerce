<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['schedule_test_drive'])) {
    if (!isset($_POST['car_id']) || !isset($_POST['test_drive_date'])) {
        die('Missing required fields');
    }

    $car_id = filter_var($_POST['car_id'], FILTER_SANITIZE_NUMBER_INT);
    $username = $_SESSION['username'];
    $request_date = $_POST['test_drive_date'];

    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'carstreads';

    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO test_drive (Car_Id, Username, Request_Date, Status) VALUES (?, ?, ?, 'Pending')");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("iss", $car_id, $username, $request_date);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();
    $conn->close();

    echo "<script>alert('Test drive request submitted! Please wait for confirmation from admin.'); window.location.href='buy_car.php';</script>";
    exit;
}
?>