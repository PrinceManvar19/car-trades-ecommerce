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

if (isset($_POST['approve'])) {
    $request_id = filter_var($_POST['request_id'], FILTER_SANITIZE_NUMBER_INT);
    $final_price = filter_var($_POST['final_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    $stmt = $conn->prepare("UPDATE table_car_listing SET Status = 'Approved', Final_Price = ? WHERE Car_Id = ?");
    $stmt->bind_param("di", $final_price, $request_id);
    if ($stmt->execute()) {
        echo '<script>alert("Request approved successfully!")</script>';
        echo '<script>window.location.href = "Car Listing Requests.php";</script>';
    }
    $stmt->close();
}

if (isset($_POST['reject'])) {
    $request_id = filter_var($_POST['request_id'], FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("UPDATE table_car_listing SET Status = 'Rejected' WHERE Car_Id = ?");
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        echo '<script>alert("Request rejected successfully!")</script>';
        echo '<script>window.location.href = "Car Listing Requests.php";</script>';
    }
    $stmt->close();
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
    <title>Car Listing Requests</title>
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

/* Main Content */
.main-content {
    flex: 1;
    padding: 2rem;
    background: #f5f6fa;
}

/* Form Styles */
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
        .car-details {
            width: 100%;
            height: 200px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
        }
        .car-details img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }
    </style>
</head>
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
    <div class="container">
        <h2>Car Listing Requests</h2>
        <ul class="request-list">
            <?php foreach ($car_listing_requests as $request) { ?>
                <?php
                $conn = new mysqli('localhost', 'root', '', 'carstreads');
                $query = "SELECT * FROM table_document_verification WHERE Car_Id = '$request[Car_Id]'";
                $result = $conn->query($query);
$document_verification_status = '';
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $document_verification_status = $row['status'];
                }
                $conn->close();
                ?>
                <li class="request-item">
                    <p>Car ID: <?php echo $request['Car_Id']; ?></p>
                    <p>Username: <?php echo $request['Username']; ?></p>
                    <p>Request Date: <?php echo isset($request['Request_Date']) ? $request['Request_Date'] : 'N/A'; ?></p>
                    <div class="car-details">
                        <img src="<?php echo $request['PhotoURL1']; ?>" alt="Car Image">
                    </div>
                    <p>Company: <?php echo $request['Company']; ?></p>
                    <p>Model: <?php echo $request['Model']; ?></p>
                    <p>Year: <?php echo $request['Year']; ?></p>
                    <p>Expected Price: <?php echo $request['expected_price']; ?></p>
                    <p>Mileage: <?php echo $request['Mileage']; ?></p>
                    <p>Location: <?php echo $request['Location']; ?></p>
                    <p>Description: <?php echo $request['Description']; ?></p>
                    <form method="post">
                        <input type="hidden" name="request_id" value="<?php echo $request['Car_Id']; ?>">
                        <label for="final_price">Final Price:</label>
                        <input type="number" name="final_price" id="final_price" required>
                        <button class="approve-button" name="approve">Approve</button>
                        <button class="reject-button" name="reject">Reject</button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    </div>
</body>
</html>