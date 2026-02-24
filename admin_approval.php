<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    die('Unauthorized access');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carstreads";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['approval_status']) && isset($_POST['car_id'])) {
    $car_id = filter_var($_POST['car_id'], FILTER_SANITIZE_NUMBER_INT);
    $approval_status = isset($_POST['approval_status']) ? $_POST['approval_status'] : '';
    
    if (empty($approval_status) || (!in_array($approval_status, ['approved', 'unapproved']))) {
        die('Invalid approval status');
    }

    $stmt = $conn->prepare("UPDATE table_car_listing SET Status = ? WHERE Car_Id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("si", $approval_status, $car_id);
    if ($stmt->execute()) {
        echo 'Approval status updated successfully.';
    } else {
        echo 'Error updating approval status: ' . $stmt->error;
    }
    $stmt->close();
} else {
    echo 'Error updating approval status.';
}
$conn->close();
?>

<html>
<head>
    <title>Admin Approval System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group input[type="radio"] {
            margin-right: 10px;
        }
        .form-group input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Approval System</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="car_id">Car ID:</label>
                <input type="text" name="car_id" required>
            </div>
            <div class="form-group">
                <label>Approval Status:</label>
                <input type="radio" name="approval_status" value="approved" required> Approve
                <input type="radio" name="approval_status" value="unapproved" required> Unapprove
            </div>
            <div class="form-group">
                <input type="submit" value="Submit Approval">
            </div>
        </form>
    </div>
</body>
</html>