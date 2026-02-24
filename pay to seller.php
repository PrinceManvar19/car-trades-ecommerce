<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    die('Unauthorized access - Admin only');
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once 'config.php';

require 'vendor/autoload.php';

use Razorpay\Api\Api;

$api_key = RAZORPAY_KEY;
$api_secret = RAZORPAY_SECRET;
$api = new Api($api_key, $api_secret);

$stmt = $conn->prepare("SELECT id, Car_Id, Username, account_number, ifsc_code, bank_name, status FROM bank_details");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

if (isset($_POST['pay_seller'])) {
    $seller_id = filter_var($_POST['seller_id'], FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (empty($seller_id) || empty($amount) || $amount <= 0) {
        die('Invalid seller ID or amount');
    }

    $order = $api->order->create([
        'amount' => ceil($amount * 100), 
        'currency' => 'INR',
        'receipt' => 'seller_' . time(),
        'payment_capture' => 1,
    ]);


    $paymentLink = $api->paymentLink->create([
        'amount' => ceil($amount * 100), 
        'currency' => 'INR',
        'description' => 'Payment to seller for car ID: ' . $seller_id,
        'customer' => [
            'name' => 'Admin',
            'email' => 'admin@cartrade.local',
            'contact' => '9123456789',
        ],
        'notify' => [
            'sms' => false,
            'email' => true,
        ],
    ]);

    header('Location: ' . $paymentLink['short_url']);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
   * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.admin-container {
    display: flex;
    min-height: 100vh;
}


.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    padding: 1rem;
}

.admin-logo {
    font-size: 1.5rem;
    padding: 1rem;
    border-bottom: 1px solid #34495e;
}

.menu-item {
    padding: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.menu-item:hover {
    background: #34495e;
}

.menu-item.active {
    background: #3498db;
}


.main-content {
    flex: 1;
    padding: 2rem;
    background: #f5f6fa;
}


form {
    margin-top: 2rem;
    padding: 1rem;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
    display: block;
    margin-bottom: 1rem;
}

input[type="text"], select {
    width: 100%;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button[type="submit"] {
    background: #3498db;
    color: #fff;
    padding: 1rem 2rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background: #2c3e50;
}

    .table-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 0.5rem;
    }

    th {
        background-color: #333;
        color: #fff;
    }
</style>
<body>
   
<div class="sidebar">
            <div class="admin-logo">CarTrade Admin</div>
            <div class="menu-item active">Dashboard</div>
            <div class="menu-item"><a href="add_company-model.php" style="color: white; text-decoration: none;">Add Car Company and Model</a></div>
            <div class="menu-item"><a href="Test Drive Requests.php" style="color: white; text-decoration: none;">Test Drive Requests</a></div>
            <div class="menu-item"><a href="Document Verification Requests.php" style="color: white; text-decoration: none;">Document Verification Request</a></div>
            <div class="menu-item"><a href="Car Listing Requests.php" style="color: white; text-decoration: none;">Car Listing Requests</a></div>
            <div class="menu-item" ><a href="pay to seller.php" style="color: white; text-decoration: none;">Pay to seller </a></div>
            <div class="menu-item" onclick="logout()">Logout</div>
        </div>

    <div class="main-content">
        <h2>View Payments</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Car ID</th>
                    <th>Username</th>
                    <th>Account Number</th>
                    <th>IFSC Code</th>
                    <th>Bank Name</th>
                    <th>Status</th>
                    <th>Pay</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Car_Id'] . "</td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['account_number'] . "</td>";
                        echo "<td>" . $row['ifsc_code'] . "</td>";
                        echo "<td>" . $row['bank_name'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td><form action='' method='post'><input type='hidden' name='seller_id' value='" . $row['Car_Id'] . "'><input type='hidden' name='amount' value='1000'><button type='submit' name='pay_seller'>Pay</button></form></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No payment details found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
