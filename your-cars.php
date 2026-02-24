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

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE Username = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $username);
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cars = array();
    while($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
} else {
    $cars = null;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade - Your Car Listings</title>
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
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: rgba(66, 66, 66, 0);
    }

    .container {
        margin-top: 50px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    h1, h2 {
        color: #333;
    }

    a {
        color: #007BFF;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .col-md-6 {
        flex-basis: 50%;
        padding: 20px;
    }

    .car-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }

    .car-details {
        padding: 20px;
    }

    .car-details h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .car-details p {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .status-approved {
        color: green;
        font-weight: bold;
    }

    .status-pending {
        color: orange;
        font-weight: bold;
    }

    .status-rejected {
        color: red;
        font-weight: bold;
    }

    .no-cars {
        text-align: center;
        padding: 40px;
        color: #666;
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
                    <button class="dropbtn"><?php echo isset($_SESSION['username']) ? 'Hello, ' . $_SESSION['username'] . ' :)' : 'Account'; ?></button>
                    <div class="dropdown-content">
                        <?php if (isset($_SESSION['username'])) { ?>
                            <a href="your-cars.php">Your Cars</a>
                            <a href="settings.php">Settings</a>
                            <a href="logout.php">Logout</a>
                        <?php } else { ?>
                            <a href="login.php">Login</a>
                            <a href="registration.php">Signup</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Your Listed Cars</h2>
        
        <?php if ($cars !== null && count($cars) > 0) { ?>
            <table>
                <tr>
                    <th>Car Company</th>
                    <th>Car Model</th>
                    <th>Car Year</th>
                    <th>Expected Price</th>
                    <th>Description</th>
                    <th>Mileage</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php foreach ($cars as $car) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($car['Company']); ?></td>
                    <td><?php echo htmlspecialchars($car['Model']); ?></td>
                    <td><?php echo htmlspecialchars($car['Year']); ?></td>
                    <td><?php echo htmlspecialchars($car['expected_price']); ?></td>
                    <td><?php echo htmlspecialchars($car['Description']); ?></td>
                    <td><?php echo htmlspecialchars($car['Mileage']); ?></td>
                    <td><?php echo htmlspecialchars($car['Location']); ?></td>
                    <td>
                        <?php 
                        $status = isset($car['status']) ? $car['status'] : 'Pending';
                        $statusClass = '';
                        if ($status == 'Approved') $statusClass = 'status-approved';
                        elseif ($status == 'Rejected') $statusClass = 'status-rejected';
                        else $statusClass = 'status-pending';
                        ?>
                        <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                    </td>
                    <td><a href='edit.php?Car_Id=<?php echo $car['Car_Id']; ?>'>Edit</a></td>
                    <td><a href='delete.php?carID=<?php echo $car['Car_Id']; ?>' onclick="return confirm('Are you sure you want to delete this car?');">Delete</a></td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <div class="no-cars">
                <p>You haven't listed any cars yet.</p>
                <p><a href="sell-car.php">Click here to sell your first car</a></p>
            </div>
        <?php } ?>
    </div>
</body>
</html>
