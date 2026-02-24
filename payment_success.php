<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';

if (empty($payment_id)) {
    die("Error: Payment ID is missing.");
}

$car_id = isset($_SESSION['car_id']) ? $_SESSION['car_id'] : '';

if (!empty($car_id)) {
    $stmt = $conn->prepare("INSERT INTO table_transactions (Username, Car_Id, Payment_Id, Transaction_Type, Transaction_Date) VALUES (?, ?, ?, 'Buyer', CURDATE())");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("sis", $_SESSION['username'], $car_id, $payment_id);
    
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE table_car_listing SET Status = 'Sold' WHERE Car_Id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("i", $car_id);
    
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();
    
    $sellerQuery = "SELECT Username FROM table_car_listing WHERE Car_Id = ?";
    $sellerStmt = $conn->prepare($sellerQuery);
    $sellerStmt->bind_param("i", $car_id);
    $sellerStmt->execute();
    $sellerResult = $sellerStmt->get_result();
    $sellerRow = $sellerResult->fetch_assoc();
    $sellerUsername = $sellerRow['Username'];
    $sellerStmt->close();

    if ($sellerUsername) {
        $stmt = $conn->prepare("INSERT INTO table_transactions (Username, Car_Id, Payment_Id, Transaction_Type, Transaction_Date) VALUES (?, ?, ?, 'Seller', CURDATE())");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("sis", $sellerUsername, $car_id, $payment_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
        }

        button {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Successful!</h1>
        <p>Our team will connect with you within 24 hours for documentation.</p>
        <p>Thank you for choosing CarTrade!</p>
        <p>Payment ID: <?php echo $payment_id; ?></p>
        <button onclick="window.location.href='homepage.php'">OK</button>
    </div>
</body>
</html>
