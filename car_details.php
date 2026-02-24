<?php
session_start();

$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'carstreads';

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: buy_car.php");
    exit;
}

$carId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

$stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE Car_Id = ?");
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("i", $carId);

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: buy_car.php");
    exit;
}

$car = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT PhotoURL1, PhotoURL2, PhotoURL3, PhotoURL4, PhotoURL5 FROM table_car_listing WHERE Car_Id = ?");
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("i", $carId);

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();

$photos = $result->fetch_assoc();
$stmt->close();

$conn->close();

require_once 'config.php';

require 'vendor/autoload.php';
use Razorpay\Api\Api;
$api = new Api(RAZORPAY_KEY, RAZORPAY_SECRET);

$car_name = $car['Company'] . ' ' . $car['Model']; 
$car_price = $car['Final_Price']; 

if ($car_price * 100 < 100) {
    echo "Error: Amount is less than the minimum amount allowed by Razorpay.";
    exit;
}

if ($car_price * 100 > 100000000) { 
    echo "Error: Amount exceeds the maximum amount allowed by Razorpay.";
    exit;
}


$orderData = [
    'receipt'         => 'CAR_' . time(),
    'amount'          => ceil($car_price * 100 / 100) * 100, 
    'currency'        => 'INR',
    'payment_capture' => 1 
];

$razorpayOrder = $api->order->create($orderData);
$order_id = $razorpayOrder['id']; 

$_SESSION['car_id'] = $carId;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['Company'] . ' ' . $car['Model']; ?> - CarTrade</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
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

        .dropdown:hover .dropbtn {
            background-color:rgba(66, 66, 66, 0);
        }

        .car-details-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .breadcrumb {
            margin-bottom: 1rem;
        }

        .breadcrumb a {
            color: #333;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .car-photos {
            display: flex;
            flex-direction: column;
            margin-bottom: 2rem;
        }

        .main-photo {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .thumbnail-container {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail.active {
            border-color: #1a1a1a;
        }

        .car-info {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .car-details h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .car-details h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .spec-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
        }

        .spec-item h4 {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .spec-item p {
            font-weight: bold;
            color: #333;
        }

        .additional-info {
            margin-bottom: 2rem;
        }

        .car-description {
            line-height: 1.6;
        }

        .action-panel {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .action-panel h3 {
            margin-bottom: 1rem;
        }

        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            padding: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            transition: background 0.3s ease;
        }

        .btn-primary {
            background: #1a1a1a;
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #1a1a1a;
            border: 1px solid #1a1a1a;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary:hover {
            background: #f1f1f1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group input[type="date"] {
            cursor: pointer;
        }

        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
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
                <a href="homepage.php">Sell Car</a>

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

    <div class="car-details-container">
        <div class="breadcrumb">
            <a href="buy_car.php">Cars</a> > <?php echo $car['Company'] . ' ' . $car['Model']; ?>
        </div>

        <div class="car-photos">
            <img id="main-photo" src="<?php echo $photos['PhotoURL1']; ?>" alt="<?php echo $car['Company'] . ' ' . $car['Model']; ?>" class="main-photo">
            
            <div class="thumbnail-container">
                <?php if (!empty($photos['PhotoURL1'])): ?>
                    <img 
                        src="<?php echo $photos['PhotoURL1']; ?>" 
                        alt="<?php echo $car['Company'] . ' ' . $car['Model'] . ' view 1'; ?>" 
                        class="thumbnail active thumbnail"
                        onclick="changeMainPhoto('<?php echo $photos['PhotoURL1']; ?>', this)"
                    >
                <?php endif; ?>
                <?php if (!empty($photos['PhotoURL2'])): ?>
                    <img 
                        src="<?php echo $photos['PhotoURL2']; ?>" 
                        alt="<?php echo $car['Company'] . ' ' . $car['Model'] . ' view 2'; ?>" 
                        class="thumbnail thumbnail"
                        onclick="changeMainPhoto('<?php echo $photos['PhotoURL2']; ?>', this)"
                    >
                <?php endif; ?>
                <?php if (!empty($photos['PhotoURL3'])): ?>
                    <img 
                        src="<?php echo $photos['PhotoURL3']; ?>" 
                        alt="<?php echo $car['Company'] . ' ' . $car['Model'] . ' view 3'; ?>" 
                        class="thumbnail thumbnail"
                        onclick="changeMainPhoto('<?php echo $photos['PhotoURL3']; ?>', this)"
                    >
                <?php endif; ?>
                <?php if (!empty($photos['PhotoURL4'])): ?>
                    <img 
                        src="<?php echo $photos['PhotoURL4']; ?>" 
                        alt="<?php echo $car['Company'] . ' ' . $car['Model'] . ' view 4'; ?>" 
                        class="thumbnail thumbnail"
                        onclick="changeMainPhoto('<?php echo $photos['PhotoURL4']; ?>', this)"
                    >
                <?php endif; ?>
                <?php if (!empty($photos['PhotoURL5'])): ?>
                    <img 
                        src="<?php echo $photos['PhotoURL5']; ?>" 
                        alt="<?php echo $car['Company'] . ' ' . $car['Model'] . ' view 5'; ?>" 
                        class="thumbnail thumbnail"
                        onclick="changeMainPhoto('<?php echo $photos['PhotoURL5']; ?>', this)"
                    >
                <?php endif; ?>
            </div>
        </div>

        <div class="car-info">
            <div class="car-details">
                <h1><?php echo $car['Company'] . ' ' . $car['Model']; ?></h1>
                <div class="car-price">₹<?php echo number_format($car['Final_Price']); ?></div>
                
                <div class="specs-grid">
                    <div class="spec-item">
                        <h4>Mileage</h4>
                        <p><?php echo $car['Mileage']; ?> KM</p>
                    </div>
                    <div class="spec-item">
                        <h4>Location</h4>
                        <p><?php echo $car['Location']; ?></p>
                    </div>
                    <div class="spec-item">
                        <h4>Registration Year</h4>
                        <p><?php echo $car['Year']; ?></p>
                    </div>
                    <div class="spec-item">
                        <h4>Fuel Type</h4>
                        <p></p>
                    </div>
                    <div class="spec-item">
                        <h4>Transmission</h4>
                        <p></p>
                    </div>
                    <div class="spec-item">
                        <h4>Owner</h4>
                        <p></p>
                    </div>
                </div>

                <div class="additional-info">
                    <h2>Car Details</h2>
                    <div class="car-description">
                        <?php if (isset($car['Description']) && !empty($car['Description'])): ?>
                            <p><?php echo $car['Description']; ?></p>
                        <?php else: ?>
                            <p>This <?php echo $car['Company'] . ' ' . $car['Model']; ?> is in excellent condition with regular maintenance and service history.</p>
                            <p>The car has been well-maintained by its previous owner and runs smoothly.</p>
                            <p>It comes with all standard features and has passed all necessary inspections.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="action-panel">
                <h3>Interested in this car?</h3>
                <div class="action-btns">
                    <button id="rzp-button1">Pay Now</button>
                    <button class="btn btn-secondary" onclick="openTestDriveModal()">Schedule Test Drive</button>
                </div>
            
                <div>
                    <h4>Car Highlights</h4>
                    <ul style="padding-left: 1.5rem; margin-top: 0.5rem;">
                        <li>Fully inspected and certified</li>
                        <li>No accident history</li>
                        <li>Complete service records</li>
                        <li>Free 1-year warranty</li>
                        <li>7-day money back guarantee</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="testDriveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTestDriveModal()">&times;</span>
            <h2>Schedule a Test Drive</h2>
            <form method="post" action="test_drive.php">
                <input type="hidden" name="car_id" value="<?php echo $car['Car_Id']; ?>">
                <div class="form-group">
                    <label for="test_drive_date">Preferred Date:</label>
                    <input type="date" id="test_drive_date" name="test_drive_date" required>
                </div>
                <button type="submit" name="schedule_test_drive" class="btn btn-primary">Schedule Test Drive</button>
            </form>
        </div>
    </div>

    <script>
        function changeMainPhoto(photoUrl, clickedThumb) {
            document.getElementById('main-photo').src = photoUrl;

            var thumbnails = document.getElementsByClassName('thumbnail');
            for (var i = 0; i < thumbnails.length; i++) {
                thumbnails[i].classList.remove('active');
            }

            clickedThumb.classList.add('active');
        }

        function openTestDriveModal() {
            document.getElementById('testDriveModal').style.display = 'block';
        }

        function closeTestDriveModal() {
            document.getElementById('testDriveModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var testDriveModal = document.getElementById('testDriveModal');

            if (event.target == testDriveModal) {
                testDriveModal.style.display = 'none';
            }
        }
    </script>

    <script>
        var options = {
            "key": "<?php echo RAZORPAY_KEY; ?>", 
            "amount": "<?php echo ceil($car_price * 100 / 100) * 100; ?>", 
            "currency": "INR",
            "name": "CarTrade",
            "description": "Payment for <?php echo $car_name; ?>",
            "order_id": "<?php echo $order_id; ?>", 
            "handler": function (response){
                alert("Payment Successful! Payment ID: " + response.razorpay_payment_id);
                window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        var rzp1 = new Razorpay(options);
        document.getElementById('rzp-button1').onclick = function(e){
            rzp1.open();
            e.preventDefault();
        }
    </script>
</body>
</html>
