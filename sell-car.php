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

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carCompany = $_POST['carCompany'];
    $carModel = $_POST['carModel'];
    $carYear = $_POST['carYear'];
    $carexpected_price = $_POST['carexpected_price'];
    $mileage = $_POST['mileage'];
    $location = $_POST['location'];
    $carDescription = $_POST['carDescription'];
    $account_number = $_POST['account_number'];
    $account_holder_name = $_POST['account_holder_name'];
    $bank_name = $_POST['bank_name'];
    $ifsc_code = $_POST['ifsc_code'];
    $branch_name = $_POST['branch_name'];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    function uploadFile($file, $prefix) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $file['tmp_name'];
            $name = $prefix . '_' . time() . '_' . basename($file['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($tmp_name, 'uploads/' . $name)) {
                    return 'uploads/' . $name;
                }
            }
        }
        return '';
    }

    $photoURL1 = uploadFile($_FILES['PhotoURL1'] ?? ['error' => 4], 'car1');
    $photoURL2 = uploadFile($_FILES['PhotoURL2'] ?? ['error' => 4], 'car2');
    $photoURL3 = uploadFile($_FILES['PhotoURL3'] ?? ['error' => 4], 'car3');
    $photoURL4 = uploadFile($_FILES['PhotoURL4'] ?? ['error' => 4], 'car4');
    $photoURL5 = uploadFile($_FILES['PhotoURL5'] ?? ['error' => 4], 'car5');
    $rcBookURL = uploadFile($_FILES['rc_book'] ?? ['error' => 4], 'rc');
    $insuranceURL = uploadFile($_FILES['Insurance'] ?? ['error' => 4], 'insurance');

    $stmt = $conn->prepare("INSERT INTO table_car_listing (Username, Company, Model, Year, expected_price, Mileage, Location, Description, PhotoURL1, PhotoURL2, PhotoURL3, PhotoURL4, PhotoURL5, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    if ($stmt === false) {
        $error = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("sssidssssssss", $_SESSION['username'], $carCompany, $carModel, $carYear, $carexpected_price, $mileage, $location, $carDescription, $photoURL1, $photoURL2, $photoURL3, $photoURL4, $photoURL5);
        
        if (!$stmt->execute()) {
            $error = "Error executing statement: " . $stmt->error;
        } else {
            $car_id = $conn->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO table_document_verification (Car_Id, Username, Request_Date, rc_book, Insurance) VALUES (?, ?, ?, ?, ?)");
            $request_date = date('Y-m-d');
            $stmt->bind_param("issss", $car_id, $_SESSION['username'], $request_date, $rcBookURL, $insuranceURL);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("SELECT * FROM bank_details WHERE Username = ?");
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE bank_details SET account_number = ?, account_holder_name = ?, bank_name = ?, ifsc_code = ?, branch_name = ? WHERE Username = ?");
                $stmt->bind_param("ssssss", $account_number, $account_holder_name, $bank_name, $ifsc_code, $branch_name, $_SESSION['username']);
            } else {
                $stmt = $conn->prepare("INSERT INTO bank_details (Car_Id, Username, account_number, account_holder_name, bank_name, ifsc_code, branch_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $car_id, $_SESSION['username'], $account_number, $account_holder_name, $bank_name, $ifsc_code, $branch_name);
            }
            $stmt->execute();
            $stmt->close();

            $success = "Car listed successfully! Please wait for approval from the admin.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Selling Page</title>
    <link rel="stylesheet" href="styles.css">
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

        .dropdown-content a:hover {background-color: #ddd;}

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }

        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background: #e74c3c;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }

        button:hover {
            background: #c0392b;
        }

        .error {
            color: red;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            background: #e6ffe6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .section-title {
            background: #f5f5f5;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-radius: 4px;
            font-weight: bold;
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

    <section id="sell" class="form-container">
        <h2>Sell Your Car</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = 'homepage.php';
                }, 2000);
            </script>
        <?php endif; ?>
        
        <form id="sellForm" action="sell-car.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Username">Username</label>
                <input type="text" id="Username" name="Username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" readonly>
            </div>
            
            <div class="section-title">Car Details</div>
            
            <div class="form-group">
                <label for="carCompany">Company</label>
                <select id="carCompany" name="carCompany" required>
                    <option value="">Select Company</option>
                    <?php
                    $sql = "SELECT * FROM c_company";
                    $res = mysqli_query($conn, $sql);
                    while ($row = $res->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['Company_Name']) . '">' . htmlspecialchars($row['Company_Name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="carModel">Model</label>
                <select id="carModel" name="carModel" required>
                    <option value="">Select Model</option>
                    <?php
                    $sql = "SELECT * FROM c_model";
                    $res = mysqli_query($conn, $sql);
                    while ($row = $res->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['Model_Name']) . '">' . htmlspecialchars($row['Model_Name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="carYear">Year</label>
                <input type="number" id="carYear" name="carYear" min="1990" max="2025" required>
            </div>
            
            <div class="form-group">
                <label for="carexpected_price">Expected Price (INR)</label>
                <input type="number" id="carexpected_price" name="carexpected_price" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="mileage">Mileage (KM)</label>
                <input type="number" id="mileage" name="mileage" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="carDescription">Description</label>
                <textarea id="carDescription" name="carDescription" rows="4" required></textarea>
            </div>

            <div class="section-title">Car Images</div>
            
            <div class="form-group">
                <label for="PhotoURL1">Car Image (Front)</label>
                <input type="file" id="PhotoURL1" name="PhotoURL1" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <label for="PhotoURL2">Car Image (Back)</label>
                <input type="file" id="PhotoURL2" name="PhotoURL2" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="PhotoURL3">Car Image (Left Side)</label>
                <input type="file" id="PhotoURL3" name="PhotoURL3" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="PhotoURL4">Car Image (Right Side)</label>
                <input type="file" id="PhotoURL4" name="PhotoURL4" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="PhotoURL5">Car Image (Dashboard)</label>
                <input type="file" id="PhotoURL5" name="PhotoURL5" accept="image/*">
            </div>

            <div class="section-title">Documents</div>
            
            <div class="form-group">
                <label for="rc_book">RC Book</label>
                <input type="file" id="rc_book" name="rc_book" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <label for="Insurance">Insurance</label>
                <input type="file" id="Insurance" name="Insurance" accept="image/*" required>
            </div>

            <div class="section-title">Bank Details (for payment)</div>
            
            <div class="form-group">
                <label for="account_number">Account Number</label>
                <input type="text" id="account_number" name="account_number" required>
            </div>
            
            <div class="form-group">
                <label for="account_holder_name">Account Holder Name</label>
                <input type="text" id="account_holder_name" name="account_holder_name" required>
            </div>
            
            <div class="form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" id="bank_name" name="bank_name" required>
            </div>
            
            <div class="form-group">
                <label for="ifsc_code">IFSC Code</label>
                <input type="text" id="ifsc_code" name="ifsc_code" required>
            </div>
            
            <div class="form-group">
                <label for="branch_name">Branch Name</label>
                <input type="text" id="branch_name" name="branch_name" required>
            </div>
            
            <button type="submit">Submit Listing</button>
        </form>
    </section>
</body>
</html>
