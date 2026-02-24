<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['Car_Id'])) {
    header("Location: buy_car.php");
    exit();
}

$Car_Id = filter_var($_GET['Car_Id'], FILTER_SANITIZE_NUMBER_INT);

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE Car_Id = ?");
$stmt->bind_param("i", $Car_Id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $_SESSION['car_id'] = $Car_Id;
} else {
    header("Location: buy_car.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Process</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    nav {
        background: #1a1a1a;
        padding: 1rem;
        color: white;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 2rem;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: rgba(25, 29, 26, 0);
        color: white;
        padding: 16px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    .dropbtn:hover {
        background-color: rgba(45, 45, 45, 0);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: rgba(66, 66, 66, 0);
    }

    .payment-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    }

    .payment-header {
        margin-bottom: 1rem;
    }

    .payment-form {
        margin-bottom: 2rem;
    }

    .payment-button {
        background: #1a1a1a;
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .payment-button:hover {
        background: #333;
    }
</style>
<body>
    <nav>
        <div class="nav-container">
            <div class="logo">CarTrade</div>
            <div class="nav-links">
                <a href="homepage.php">Home</a>
                <a href="buy_car.php">Buy Car</a>
                <a href="sell-car.php">Sell Car</a>

                <div class="dropdown">
                    <button class="dropbtn"><?php echo $_SESSION['username']; ?></button>
                    <div class="dropdown-content">
                        <a href="your-cars.php">Your Cars</a>
                        <a href="settings.php">Settings</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="payment-container">
        <h2 class="payment-header">Payment Process</h2>

        <form class="payment-form" action="verify_payment.php" method="post">
            <input type="hidden" name="Car_Id" value="<?php echo $Car_Id; ?>">
            <input type="hidden" name="Price" value="<?php echo $row['expected_price']; ?>">
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="card-number" required>
            <label for="expiration-date">Expiration Date:</label>
            <input type="date" id="expiration-date" name="expiration-date" required>
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" required>
            <button class="payment-button" type="submit">Make Payment</button>
        </form>
    </div>
</body>
</html>