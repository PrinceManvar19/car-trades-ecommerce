<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT Car_Id, Company, Model, PhotoURL1, Final_Price, expected_price, Mileage, Location FROM table_car_listing WHERE Status = 'Approved' AND (Status != 'Sold' OR Status IS NULL)");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

$cars = array();
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade - Buy Used Cars</title>
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
            background-color:rgba(25, 29, 26, 0);
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #f1f1f1}

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .listings {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .listings h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }

        .filter-section {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 8px;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .car-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .car-details {
            padding: 1rem;
        }

        .car-details h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }

        .car-details p {
            margin-bottom: 0.3rem;
            color: #666;
        }

        .price {
            color: #e74c3c;
            font-size: 1.25rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .view-details-btn {
            display: block;
            width: 100%;
            padding: 0.8rem;
            background: #1a1a1a;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 1rem;
        }

        .view-details-btn:hover {
            background: #333;
        }

        .no-cars {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
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

    <section class="listings" id="listings">
        <h2>Available Cars</h2>
        
        <div class="filter-section">
            <p>Filter options coming soon...</p>
        </div>
        
        <div class="cars-grid">
            <?php if (count($cars) > 0): ?>
                <?php foreach ($cars as $car): ?>
                    <?php 
                    $price = !empty($car['Final_Price']) ? $car['Final_Price'] : $car['expected_price'];
                    $photo = !empty($car['PhotoURL1']) ? $car['PhotoURL1'] : 'https://via.placeholder.com/300x200?text=No+Image';
                    ?>
                    <div class="car-card">
                        <img src="<?php echo htmlspecialchars($photo); ?>" class="car-image" alt="Car Image">
                        <div class="car-details">
                            <h3><?php echo htmlspecialchars($car['Company'] . ' ' . $car['Model']); ?></h3>
                            <p>Price: ₹<?php echo number_format($price); ?></p>
                            <p>Mileage: <?php echo htmlspecialchars($car['Mileage']); ?> KM</p>
                            <p>Location: <?php echo htmlspecialchars($car['Location']); ?></p>
                            <a href="car_details.php?id=<?php echo $car['Car_Id']; ?>" class="view-details-btn">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-cars">
                    <p>No cars available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
