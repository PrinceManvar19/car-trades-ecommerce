<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Location: sell-car.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade - Buy & Sell Used Cars</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        *{
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

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=1200');
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            background-size: cover;
            background-position: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero button {
            background: #e74c3c;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .hero button:hover {
            background: #c0392b;
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

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .car-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

        .view-btn {
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

        .view-btn:hover {
            background: #333;
        }

        .no-cars {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        footer {
            background: #1a1a1a;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
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

    <section class="hero">
        <div class="hero-content">
            <h1>Buy & Sell Used Cars</h1>
            <p>Find your perfect car or sell your current one</p>
            <button onclick="document.getElementById('listings').scrollIntoView({behavior: 'smooth'})">View Cars</button>
        </div>
    </section>

    <section class="listings" id="listings">
        <h2>Recently Added Cars</h2>
        <div class="cars-grid">
            <?php
            $conn = new mysqli('localhost', 'root', '', 'carstreads');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get approved cars that are not sold
            $sql = "SELECT * FROM table_car_listing WHERE status = 'Approved' AND (Status != 'Sold' OR Status IS NULL) ORDER BY Car_Id DESC LIMIT 10";
            $res = mysqli_query($conn, $sql);

            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $price = isset($row['Final_Price']) && !empty($row['Final_Price']) ? $row['Final_Price'] : $row['expected_price'];
                    $photo = !empty($row['PhotoURL1']) ? $row['PhotoURL1'] : 'https://via.placeholder.com/300x200?text=No+Image';
                    ?>
                    <div class="car-card">
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="Car Image" class="car-image">
                        <div class="car-details">
                            <h3><?php echo htmlspecialchars($row['Company'] . ' ' . $row['Model']); ?></h3>
                            <p>Year: <?php echo htmlspecialchars($row['Year']); ?></p>
                            <p>Mileage: <?php echo htmlspecialchars($row['Mileage']); ?> KM</p>
                            <p>Location: <?php echo htmlspecialchars($row['Location']); ?></p>
                            <p class="price">₹<?php echo number_format($price); ?></p>
                            <a href="car_details.php?id=<?php echo $row['Car_Id']; ?>" class="view-btn">View Details</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="no-cars"><p>No cars available at the moment. Check back later!</p></div>';
            }

            $conn->close();
            ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 CarTrade. All rights reserved.</p>
    </footer>
</body>
</html>
