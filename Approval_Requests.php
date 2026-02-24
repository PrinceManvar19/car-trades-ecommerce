<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM test_drive WHERE Status = 'Pending'";
$result = $conn->query($query);

$test_drive_requests = array();
while ($row = $result->fetch_assoc()) {
    $test_drive_requests[] = $row;
}

$query = "SELECT * FROM table_document_verification WHERE Status = 'Pending'";
$result = $conn->query($query);

$document_verification_requests = array();
while ($row = $result->fetch_assoc()) {
    $document_verification_requests[] = $row;
}

$query = "SELECT * FROM table_car_listing WHERE Status = 'Pending'";
$result = $conn->query($query);

$car_listing_requests = array();
while ($row = $result->fetch_assoc()) {
    $car_listing_requests[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Requests</title>
    <style>
       body {
    font-family: Arial, sans-serif;
}

.container {
    width: 100%;
    height: 100%;
    display: flex;
}

.sidebar {
    width: 200px;
    height: 100%;
    background-color: #f0f0f0;
    padding: 20px;
    border-right: 1px solid #ccc;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar li {
    margin-bottom: 10px;
}

.sidebar a {
    text-decoration: none;
    color: #337ab7;
}

.sidebar a:hover {
    color: #23527c;
}

.content {
    width: 80%;
    height: 100%;
    padding: 20px;
}

.approval-requests {
    width: 100%;
    height: 100%;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.request-type {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.request-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.request-item {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.request-item:last-child {
    border-bottom: none;
}

.approve-button {
    width: 100%;
    height: 40px;
    background-color: #4CAF50;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.approve-button:hover {
    background-color: #3e8e41;
}

.reject-button {
    width: 100%;
    height: 40px;
    background-color: #e74c3c;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.reject-button:hover {
    background-color: #c0392b;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="adminpage1.php">Dashboard</a></li>
                <li><a href="adminpage2.php">Manage Users</a></li>
                <li><a href="adminpage3.php">Manage Cars</a></li>
                <li><a href="adminpage4.php">Manage Requests</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="content">
            <h2>Approval Requests</h2>
            <div class="approval-requests">
                <div class="request-type">Test Drive Requests</div>
                <ul class="request-list">
                    <?php foreach ($test_drive_requests as $request) { ?>
                        <li class="request-item">
                            <p>Car ID: <?php echo $request['Car_Id']; ?></p>
                            <p>Username: <?php echo $request['Username']; ?></p>
                            <p>Request Date: <?php echo $request['Request_Date']; ?></p>
                            <button class="approve-button">Approve</button>
                            <button class="reject-button">Reject</button>
                        </li>
                    <?php } ?>
                </ul>
                <div class="request-type">Document Verification Requests</div>
                <ul class="request-list">
                    <?php foreach ($document_verification_requests as $request) { ?>
                        <li class="request-item">
                            <p>Car ID: <?php echo $request['Car_Id']; ?></p>
                            <p>Username: <?php echo $request['Username']; ?></p>
                            <p>Request Date: <?php echo $request['Request_Date']; ?></p>
                            <button class="approve-button">Approve</button>
                            <button class="reject-button">Reject</button>
                        </li>
                    <?php } ?>
                </ul>
                <div class="request-type">Car Listing Requests</div>
                <ul class="request-list">
                    <?php foreach ($car_listing_requests as $request) { ?>
                        <li class="request-item">
                            <p>Car ID: <?php echo $request['Car_Id']; ?></p>
                            <p>Username: <?php echo $request['Username']; ?></p>
                            <p>Request Date: <?php echo $request['Request_Date']; ?></p>
                            <button class="approve-button">Approve</button>
                            <button class="reject-button">Reject</button>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>